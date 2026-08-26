<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Exibir o formulário de pedido de redefinição de palavra-passe (SMS OTP / Email).
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Processar pedido de OTP via SMS ou Email.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login_input' => ['required', 'string'],
        ], [
            'login_input.required' => 'Introduza o seu email ou número de telemóvel registado.',
        ]);

        $input = trim($request->login_input);
        
        // Procurar utilizador por email ou telefone
        $user = User::where('email', $input)
            ->orWhere('telefone', $input)
            ->orWhere('telefone', 'like', "%{$input}%")
            ->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'login_input' => 'Nenhum profissional encontrado com esse email ou telefone.',
            ]);
        }

        // Determinar número de telefone para envio de SMS OTP
        $phone = $user->telefone;
        
        // Se a entrada for numérica/telefone
        if (preg_match('/^[\d\s+]{8,15}$/', $input)) {
            $phone = $input;
        }

        // Se não possuir telefone registado, fallback para telefone remetente ou default
        if (empty($phone)) {
            $phone = env('HTTPSMS_FROM', '+258862134230');
        }

        // Gerar Código OTP de 6 Dígitos
        $otp = rand(100000, 999999);

        // Guardar na sessão (válido por 10 minutos)
        session([
            'reset_otp_user_id' => $user->id,
            'reset_otp_code'    => (string) $otp,
            'reset_otp_expires' => now()->addMinutes(10)->timestamp,
            'reset_target_phone'=> $phone,
            'reset_user_email'  => $user->email,
        ]);

        // Mensagem SMS
        $msgSms = "Maternidade+: O seu codigo OTP de verificacao para redefinir a palavra-passe e: {$otp}. Valido por 10 minutos.";

        // Disparar SMS via httpSMS Driver
        [$success, $statusMsg] = SmsService::sendSmsAndLog($user->id, $phone, $msgSms);

        return redirect()->route('password.verify-otp')
            ->with('success', "Código OTP de verificação enviado via SMS para o telemóvel " . mask_phone($phone) . ". (httpSMS: {$statusMsg})");
    }

    /**
     * Formulário de introdução do código OTP e nova palavra-passe.
     */
    public function showOtpForm(): View|RedirectResponse
    {
        if (!session()->has('reset_otp_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['login_input' => 'Sessão de redefinição expirada. Por favor, solicite um novo código.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Validar código OTP e atualizar a palavra-passe do utilizador.
     */
    public function verifyOtpStore(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp_code.required' => 'Introduza o código OTP de 6 dígitos recebido via SMS.',
            'otp_code.size' => 'O código OTP deve possuir exatamente 6 dígitos.',
            'password.min' => 'A nova palavra-passe deve possuir no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da palavra-passe não coincide.',
        ]);

        $sessionOtp = session('reset_otp_code');
        $expiresAt  = session('reset_otp_expires');
        $userId     = session('reset_otp_user_id');

        if (!$sessionOtp || !$userId) {
            return redirect()->route('password.request')
                ->withErrors(['login_input' => 'Sessão de verificação inválida. Solicite um novo código.']);
        }

        if (now()->timestamp > $expiresAt) {
            return redirect()->route('password.request')
                ->withErrors(['login_input' => 'O código OTP expirou (limite de 10 min). Solicite um novo código.']);
        }

        if (trim($request->otp_code) !== (string) $sessionOtp) {
            return back()->withErrors(['otp_code' => 'Código OTP incorreto. Verifique a mensagem SMS recebida.']);
        }

        // Atualizar palavra-passe do utilizador
        $user = User::findOrFail($userId);
        $user->password = Hash::make($request->password);
        $user->save();

        // Limpar dados da sessão
        session()->forget(['reset_otp_user_id', 'reset_otp_code', 'reset_otp_expires', 'reset_target_phone', 'reset_user_email']);

        return redirect()->route('login')
            ->with('status', 'Palavra-passe redefinida com sucesso! Pode agora iniciar sessão com a nova credencial.');
    }
}

/**
 * Helper para mascarar número de telefone (ex: +258 86 *** *230)
 */
function mask_phone(string $phone): string
{
    $len = strlen($phone);
    if ($len <= 5) return $phone;
    return substr($phone, 0, 5) . ' **** ' . substr($phone, -3);
}
