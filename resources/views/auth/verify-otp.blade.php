<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Validar Código OTP — Maternidade+ (MISAU Moçambique)</title>

    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f766e">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-surface-900 selection:bg-brand-500 selection:text-white relative bg-surface-950 overflow-x-hidden">

    {{-- FULLSCREEN HERO BACKGROUND --}}
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('img/gestante-mocambique-real.jpg') }}" alt="Gestante Moçambicana com Capulana" class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-900/85 to-brand-950/90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-transparent to-brand-950/70"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-12">
        <div class="w-full max-w-xl">
            <div class="bg-white/95 backdrop-blur-2xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-white/40 space-y-6 relative overflow-hidden">
                
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-gold-400 to-crimson-600"></div>

                <div class="space-y-1 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl mx-auto mb-3 shadow-md">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-2xl font-black text-surface-900 tracking-tight">Validar Código OTP</h3>
                    <p class="text-xs text-surface-500">
                        Introduza o código de 6 dígitos enviado por SMS para o seu telemóvel
                    </p>
                </div>

                @if (session('success'))
                    <div class="p-3.5 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900 flex items-center gap-2.5">
                        <i class="fas fa-comment-sms text-brand-600 text-base shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.verify-otp.store') }}" class="space-y-5">
                    @csrf

                    {{-- Código OTP --}}
                    <div>
                        <label for="otp_code" class="label-tw text-center block">Código OTP de 6 Dígitos <span class="text-crimson-500">*</span></label>
                        <div class="relative max-w-xs mx-auto">
                            <input id="otp_code" 
                                   type="text" 
                                   name="otp_code" 
                                   maxlength="6"
                                   required 
                                   autofocus 
                                   placeholder="123456"
                                   class="input-tw text-center font-mono font-black text-2xl tracking-[0.4em] py-3 bg-surface-50 border-2 border-brand-500/30 focus:border-brand-500 @error('otp_code') input-error-tw @enderror">
                        </div>
                        @error('otp_code')
                            <p class="text-2xs text-crimson-600 mt-1.5 font-medium text-center flex items-center justify-center gap-1">
                                <i class="fas fa-triangle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div class="border-t border-surface-200 pt-4 space-y-4">
                        <h4 class="text-xs font-bold text-surface-900">Definir Nova Palavra-passe</h4>

                        {{-- Nova Palavra-passe --}}
                        <div>
                            <label for="password" class="label-tw">Nova Palavra-passe</label>
                            <div class="relative" x-data="{ showPass: false }">
                                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                <input id="password" 
                                       :type="showPass ? 'text' : 'password'" 
                                       name="password" 
                                       required 
                                       placeholder="Mínimo 8 caracteres"
                                       class="input-tw pl-10 pr-10 text-xs py-2.5 @error('password') input-error-tw @enderror">
                                <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs">
                                    <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmação --}}
                        <div>
                            <label for="password_confirmation" class="label-tw">Confirmar Nova Palavra-passe</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                <input id="password_confirmation" 
                                       type="password" 
                                       name="password_confirmation" 
                                       required 
                                       placeholder="Repita a palavra-passe"
                                       class="input-tw pl-10 text-xs py-2.5">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-tw w-full justify-center py-3 text-sm font-bold shadow-lg shadow-brand-600/30">
                        <i class="fas fa-check-circle text-xs"></i>
                        <span>Validar OTP & Redefinir Palavra-passe</span>
                    </button>
                </form>

                <div class="pt-4 border-t border-surface-100 flex items-center justify-between text-xs">
                    <a href="{{ route('password.request') }}" class="text-surface-500 hover:text-surface-800 text-2xs">
                        Não recebeu o SMS? Solicitar outro código
                    </a>
                    <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:underline text-2xs">
                        Voltar para o Login
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
