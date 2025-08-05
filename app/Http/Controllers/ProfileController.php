<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show user statistics
     */
    public function stats()
    {
        $user = auth()->user();
        
        $stats = [
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

        return view('profile.stats', compact('user', 'stats'));
    }
}