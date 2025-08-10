<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $request->user()->id,
            'especialidade' => 'nullable|string|max:255',
            'crm' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
        ]);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('success', 'Senha atualizada com sucesso!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = $request->user();

        // Verificar se o usuário tem consultas
        if ($user->consultations()->count() > 0) {
            return Redirect::route('profile.edit')
                ->with('error', 'Não é possível excluir conta com consultas registradas.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show user statistics and dashboard
     */
    public function stats()
    {
        $user = auth()->user();
        
        $baseStats = [
            'total_consultations' => $user->consultations()->count(),
            'consultations_this_month' => $user->consultations()
                ->whereMonth('data_consulta', now()->month)
                ->whereYear('data_consulta', now()->year)
                ->count(),
            'consultations_today' => $user->consultations()
                ->whereDate('data_consulta', today())
                ->count(),
            'patients_attended' => $user->consultations()
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        // Estatísticas específicas por role
        $roleStats = [];
        
        if ($user->hasRole('Laboratorista')) {
            $roleStats = [
                'exams_processed' => Exam::where('processed_by', $user->id)->count(),
                'exams_today' => Exam::where('processed_by', $user->id)
                    ->whereDate('data_realizacao', today())->count(),
                'pending_exams' => Exam::where('status', 'solicitado')->count(),
                'exam_types_processed' => Exam::where('processed_by', $user->id)
                    ->distinct('tipo_exame')->count()
            ];
        } else {
            $roleStats = [
                'exams_requested' => Exam::whereHas('consultation', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
                'exams_this_month' => Exam::whereHas('consultation', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereMonth('data_solicitacao', now()->month)->count(),
                'pending_results' => Exam::whereHas('consultation', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'solicitado')->count()
            ];
        }

        $stats = array_merge($baseStats, $roleStats);

        // Atividades recentes
        $recentActivities = $this->getRecentActivities($user);

        return view('profile.stats', compact('user', 'stats', 'recentActivities'));
    }

    /**
     * Show user profile with basic info
     */
    public function show()
    {
        $user = auth()->user();
        $user->load('roles');

        return view('profile.show', compact('user'));
    }

    /**
     * Get recent activities for the user
     */
    private function getRecentActivities($user, $limit = 10)
    {
        $activities = collect();

        // Consultas recentes
        $consultations = $user->consultations()
            ->with('patient')
            ->orderBy('data_consulta', 'desc')
            ->limit($limit)
            ->get();

        foreach ($consultations as $consultation) {
            $activities->push([
                'type' => 'consultation',
                'date' => $consultation->data_consulta,
                'title' => 'Consulta Realizada',
                'description' => "Consulta com {$consultation->patient->nome_completo}",
                'status' => $consultation->status,
                'icon' => 'fas fa-user-md',
                'color' => 'primary'
            ]);
        }

        if ($user->hasRole('Laboratorista')) {
            // Exames processados pelo laboratorista
            $exams = Exam::where('processed_by', $user->id)
                ->with('consultation.patient')
                ->orderBy('data_realizacao', 'desc')
                ->limit($limit)
                ->get();

            foreach ($exams as $exam) {
                $activities->push([
                    'type' => 'exam_processed',
                    'date' => $exam->data_realizacao,
                    'title' => 'Exame Processado',
                    'description' => "{$exam->tipo_exame_label} - {$exam->consultation->patient->nome_completo}",
                    'status' => $exam->status,
                    'icon' => 'fas fa-flask',
                    'color' => 'success'
                ]);
            }
        } else {
            // Exames solicitados pelo médico/enfermeiro
            $exams = Exam::whereHas('consultation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('consultation.patient')
            ->orderBy('data_solicitacao', 'desc')
            ->limit($limit)
            ->get();

            foreach ($exams as $exam) {
                $activities->push([
                    'type' => 'exam_requested',
                    'date' => $exam->data_solicitacao,
                    'title' => 'Exame Solicitado',
                    'description' => "{$exam->tipo_exame_label} - {$exam->consultation->patient->nome_completo}",
                    'status' => $exam->status,
                    'icon' => 'fas fa-vial',
                    'color' => 'info'
                ]);
            }
        }

        return $activities->sortByDesc('date')->take($limit);
    }

    /**
     * Get performance metrics for the user
     */
    public function performance()
    {
        $user = auth()->user();
        
        // Dados dos últimos 6 meses
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('M Y'),
                'consultations' => $user->consultations()
                    ->whereMonth('data_consulta', $date->month)
                    ->whereYear('data_consulta', $date->year)
                    ->count()
            ];

            if ($user->hasRole('Laboratorista')) {
                $monthData['exams_processed'] = Exam::where('processed_by', $user->id)
                    ->whereMonth('data_realizacao', $date->month)
                    ->whereYear('data_realizacao', $date->year)
                    ->count();
            } else {
                $monthData['exams_requested'] = Exam::whereHas('consultation', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereMonth('data_solicitacao', $date->month)
                ->whereYear('data_solicitacao', $date->year)
                ->count();
            }

            $months->push($monthData);
        }

        return view('profile.performance', compact('user', 'months'));
    }
}