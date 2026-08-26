<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Recuperar Palavra-passe — Maternidade+ (MISAU Moçambique)</title>

    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f766e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Maternidade+">

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

    {{-- CONTEÚDO PRINCIPAL --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-12">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            {{-- SEÇÃO ESQUERDA: INFORMAÇÕES --}}
            <div class="lg:col-span-6 space-y-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-xl flex items-center justify-center border border-white/20 text-gold-300 text-2xl shadow-2xl">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-1.5">
                            <span>Maternidade</span><span class="text-gold-400">+</span>
                        </h1>
                        <p class="text-2xs font-extrabold uppercase tracking-widest text-gold-300">Recuperação de Acesso Profissional</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="inline-block px-3 py-1 rounded-lg bg-gold-400/20 border border-gold-400/30 text-gold-300 text-xs font-extrabold uppercase tracking-wider">
                        Recuperação via SMS OTP (httpSMS)
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black text-white leading-tight">
                        Envio Instantâneo de Código de Verificação
                    </h2>
                    <p class="text-sm text-white/80 leading-relaxed font-normal">
                        Introduza o seu email ou número de telemóvel registado no sistema para receber o código OTP de 6 dígitos via mensagem SMS.
                    </p>
                </div>

                <div class="card-tw p-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl flex items-center gap-3 text-xs text-white">
                    <i class="fas fa-comment-sms text-gold-300 text-xl shrink-0"></i>
                    <p class="text-2xs text-white/90">O código OTP enviado por SMS é válido por 10 minutos para garantir a segurança dos dados clínicos.</p>
                </div>
            </div>

            {{-- SEÇÃO DIREITA: FORMULÁRIO --}}
            <div class="lg:col-span-6">
                <div class="bg-white/95 backdrop-blur-2xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-white/40 space-y-6 relative overflow-hidden">
                    
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-gold-400 to-crimson-600"></div>

                    <div class="space-y-1">
                        <h3 class="text-2xl font-extrabold text-surface-900 tracking-tight">Esqueceu a Palavra-passe?</h3>
                        <p class="text-xs text-surface-500">Introduza o seu email ou telemóvel para receber o código OTP</p>
                    </div>

                    @if (session('status'))
                        <div class="p-3.5 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900 flex items-center gap-2.5">
                            <i class="fas fa-circle-check text-brand-600 text-sm shrink-0"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="login_input" class="label-tw">Email ou Telemóvel Registado</label>
                            <div class="relative">
                                <i class="fas fa-user text-surface-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
                                <input id="login_input" 
                                       type="text" 
                                       name="login_input" 
                                       value="{{ old('login_input') }}" 
                                       required 
                                       autofocus 
                                       placeholder="ex: enfermeira@maternidade.mz ou 841234567"
                                       class="input-tw pl-10 text-xs py-2.5 @error('login_input') input-error-tw @enderror">
                            </div>
                            @error('login_input')
                                <p class="text-2xs text-crimson-600 mt-1.5 font-medium flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary-tw w-full justify-center py-3 text-sm font-bold shadow-lg shadow-brand-600/30">
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Enviar Código OTP via SMS</span>
                        </button>
                    </form>

                    <div class="pt-4 border-t border-surface-100 flex items-center justify-between text-xs">
                        <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:underline flex items-center gap-1.5">
                            <i class="fas fa-arrow-left text-2xs"></i>
                            <span>Voltar para o Login</span>
                        </a>
                        <span class="text-3xs text-surface-400">httpSMS Provider</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</body>
</html>
