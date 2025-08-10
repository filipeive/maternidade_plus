<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage_users');
    }

    public function index(Request $request)
    {
        $query = User::with('roles');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('crm', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'ativo') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }
        
        $users = $query->orderBy('name')->paginate(15);
        $roles = Role::all();
        
        // Estatísticas
        $stats = [
            'total' => User::count(),
            'admins' => User::role('Administrador')->count(),
            'medicos' => User::role('Médico')->count(),
            'enfermeiros' => User::role('Enfermeiro')->count(),
            //'laboratorio' => User::role('Laboratorista')->count(),
            'ativos' => User::whereNotNull('email_verified_at')->count()
        ];
        
        return view('users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'especialidade' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:255'],
            'crm' => ['nullable', 'string', 'max:255']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'especialidade' => $request->especialidade,
            'telefone' => $request->telefone,
            'crm' => $request->crm
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'consultations.patient']);
        
        // Estatísticas do usuário
        $stats = [
            'total_consultations' => $user->consultations()->count(),
            'this_month' => $user->consultations()->whereMonth('data_consulta', now()->month)->count(),
            'pending_consultations' => $user->consultations()->where('status', 'agendada')->count(),
            'exams_requested' => Exam::whereHas('consultation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'exams_processed' => $user->hasRole('Laboratorista') ? 
                Exam::where('processed_by', $user->id)->count() : 0
        ];
        
        // Últimas atividades
        $recentConsultations = $user->consultations()
            ->with('patient')
            ->orderBy('data_consulta', 'desc')
            ->limit(10)
            ->get();

        // Se for laboratorista, mostrar exames processados
        $recentExams = collect();
        if ($user->hasRole('Laboratorista')) {
            $recentExams = Exam::where('processed_by', $user->id)
                ->with('consultation.patient')
                ->orderBy('data_realizacao', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('users.show', compact('user', 'stats', 'recentConsultations', 'recentExams'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'especialidade' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:255'],
            'crm' => ['nullable', 'string', 'max:255'],
            'active' => ['boolean']
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'especialidade' => $request->especialidade,
            'telefone' => $request->telefone,
            'crm' => $request->crm
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->has('active')) {
            $updateData['email_verified_at'] = $request->active ? now() : null;
        }

        $user->update($updateData);

        // Atualizar role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Não permitir exclusão do próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        // Verificar se o usuário tem consultas
        if ($user->consultations()->count() > 0) {
            return redirect()->route('users.index')
                ->with('error', 'Não é possível excluir usuário com consultas registradas.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Você não pode desativar seu próprio usuário.'], 400);
        }

        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now()
        ]);

        return response()->json([
            'success' => true,
            'status' => $user->email_verified_at ? 'ativo' : 'inativo'
        ]);
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'temp' . rand(1000, 9999);
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', "Senha resetada! Nova senha temporária: {$newPassword}")
            ->with('temp_password', $newPassword);
    }

    public function activity(User $user)
    {
        $activities = collect();
        
        // Consultas realizadas
        $consultations = $user->consultations()
            ->with('patient')
            ->orderBy('data_consulta', 'desc')
            ->limit(50)
            ->get();
        
        foreach ($consultations as $consultation) {
            $activities->push([
                'type' => 'consultation',
                'date' => $consultation->data_consulta,
                'description' => "Consulta com {$consultation->patient->nome_completo}",
                'status' => $consultation->status,
                'link' => route('consultations.show', $consultation)
            ]);
        }
        
        // Exames solicitados (se for médico/enfermeiro)
        if (!$user->hasRole('Laboratorista')) {
            $exams = Exam::whereHas('consultation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('consultation.patient')
            ->orderBy('data_solicitacao', 'desc')
            ->limit(50)
            ->get();
            
            foreach ($exams as $exam) {
                $activities->push([
                    'type' => 'exam_request',
                    'date' => $exam->data_solicitacao,
                    'description' => "Solicitou {$exam->tipo_exame_label} para {$exam->consultation->patient->nome_completo}",
                    'status' => $exam->status,
                    'link' => route('exams.show', $exam)
                ]);
            }
        }
        
        // Exames processados (se for laboratorista)
        if ($user->hasRole('Laboratorista')) {
            $processedExams = Exam::where('processed_by', $user->id)
                ->with('consultation.patient')
                ->orderBy('data_realizacao', 'desc')
                ->limit(50)
                ->get();
                
            foreach ($processedExams as $exam) {
                $activities->push([
                    'type' => 'exam_process',
                    'date' => $exam->data_realizacao,
                    'description' => "Processou {$exam->tipo_exame_label} de {$exam->consultation->patient->nome_completo}",
                    'status' => $exam->status,
                    'link' => route('exams.show', $exam)
                ]);
            }
        }
        
        $activities = $activities->sortByDesc('date');
        
        return view('users.activity', compact('user', 'activities'));
    }

    /**
     * Dashboard específico para laboratoristas
     */
    public function labDashboard()
    {
        if (!auth()->user()->hasRole('Laboratorista')) {
            abort(403, 'Acesso negado');
        }

        $stats = [
            'pending_exams' => Exam::where('status', 'solicitado')->count(),
            'completed_today' => Exam::where('status', 'realizado')
                ->whereDate('data_realizacao', today())->count(),
            'completed_this_month' => Exam::where('status', 'realizado')
                ->whereMonth('data_realizacao', now()->month)->count(),
            'my_processed' => Exam::where('processed_by', auth()->id())->count()
        ];

        $pendingExams = Exam::where('status', 'solicitado')
            ->with('consultation.patient')
            ->orderBy('data_solicitacao')
            ->limit(10)
            ->get();

        return view('users.lab-dashboard', compact('stats', 'pendingExams'));
    }
}