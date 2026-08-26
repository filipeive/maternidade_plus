<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Acesso ao Sistema — Maternidade+ (MISAU Moçambique)</title>

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

    {{-- ============================================================
         FULLSCREEN HERO BACKGROUND COM A IMAGEM REAL DA GESTANTE MOÇAMBICANA
         ============================================================ --}}
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('img/gestante-mocambique-real.jpg') }}" 
             alt="Gestante Moçambicana com Capulana" 
             class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000">
        
        {{-- Gradientes Ambientais Ricos --}}
        <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-900/85 to-brand-950/90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-transparent to-brand-950/70"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-gold-500/10 via-transparent to-transparent"></div>
    </div>

    {{-- ============================================================
         CONTEÚDO PRINCIPAL (SPLIT LAYOUT RESPONSIVO)
         ============================================================ --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-12">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            {{-- SEÇÃO ESQUERDA: HERO & HISTÓRIA MATERNA DE MOÇAMBIQUE --}}
            <div class="lg:col-span-7 space-y-8 text-white">
                
                {{-- Logo & Certificação MISAU --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-xl flex items-center justify-center border border-white/20 text-gold-300 text-3xl shadow-2xl">
                            <i class="fas fa-person-pregnant"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white flex items-center gap-1.5">
                                <span>Maternidade</span><span class="text-gold-400">+</span>
                            </h1>
                            <p class="text-2xs font-extrabold uppercase tracking-widest text-gold-300">
                                República de Moçambique · MISAU
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-white">
                            <i class="fas fa-shield-heart text-gold-400"></i>
                            <span>Protocolo MISAU: 8 Consultas ANC</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-white">
                            <i class="fas fa-location-dot text-emerald-400"></i>
                            <span>Quelimane · Zambézia</span>
                        </span>
                    </div>
                </div>

                {{-- Título Impactante --}}
                <div class="space-y-4 max-w-2xl">
                    <div class="inline-block px-3 py-1 rounded-lg bg-gold-400/20 border border-gold-400/30 text-gold-300 text-xs font-extrabold uppercase tracking-wider">
                        Saúde Materno-Infantil & Capulana
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight drop-shadow-xl">
                        Protegendo a Vida de Mães e Bebês em Cada Comunidade
                    </h2>
                    <p class="text-base sm:text-lg text-white/80 leading-relaxed font-normal">
                        Plataforma SaaS integrada para o acompanhamento pré-natal, rastreio precoce de complicações, vacinação IPTp-SP e apoio contínuo ao parto e puerpério.
                    </p>
                </div>

                {{-- Card Flutuante de Capulana & Cuidados Humanizados --}}
                <div class="card-tw p-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl flex items-start gap-4 max-w-lg shadow-2xl">
                    <div class="w-10 h-10 rounded-xl bg-gold-400/20 border border-gold-400/40 text-gold-300 flex items-center justify-center text-lg shrink-0 mt-0.5">
                        <i class="fas fa-heart-pulse"></i>
                    </div>
                    <div class="text-xs space-y-1 text-white">
                        <span class="font-bold text-gold-300 uppercase tracking-wider text-3xs block">Cuidados Humanizados MISAU</span>
                        <p class="text-white/90 leading-normal">
                            Garantindo a monitorização em tempo real desde a 1ª consulta pré-natal até ao acompanhamento pós-parto no Centro de Saúde.
                        </p>
                    </div>
                </div>

            </div>

            {{-- SEÇÃO DIREITA: CARTÃO DE AUTENTICAÇÃO GLASSMORPHISM --}}
            <div class="lg:col-span-5">
                <div class="bg-white/95 backdrop-blur-2xl rounded-3xl p-8 sm:p-10 shadow-2xl border border-white/40 space-y-6 relative overflow-hidden">
                    
                    {{-- Faixa Decorativa Moçambique --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-gold-400 to-crimson-600"></div>

                    {{-- Form Header --}}
                    <div class="space-y-1">
                        <h3 class="text-2xl font-extrabold text-surface-900 tracking-tight">Iniciar Sessão</h3>
                        <p class="text-xs text-surface-500">Introduza as credenciais profissionais para aceder ao sistema</p>
                    </div>

                    {{-- Status Session --}}
                    @if (session('status'))
                        <div class="p-3.5 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900 flex items-center gap-2.5">
                            <i class="fas fa-circle-check text-brand-600 text-sm shrink-0"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="label-tw">Endereço de Email Profissional</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="enfermeira@maternidade.mz"
                                       class="input-tw pl-10 text-xs py-2.5 @error('email') input-error-tw @enderror">
                            </div>
                            @error('email')
                                <p class="text-2xs text-crimson-600 mt-1 font-medium flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Palavra-passe --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="label-tw mb-0">Palavra-passe</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-2xs font-semibold text-brand-600 hover:text-brand-700">
                                        Esqueceu a palavra-passe?
                                    </a>
                                @endif
                            </div>
                            <div class="relative" x-data="{ showPass: false }">
                                <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                <input id="password" 
                                       :type="showPass ? 'text' : 'password'" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="input-tw pl-10 pr-10 text-xs py-2.5 @error('password') input-error-tw @enderror">
                                <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600 text-xs">
                                    <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-2xs text-crimson-600 mt-1 font-medium flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Lembrar-me --}}
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-xs text-surface-600">Manter a sessão iniciada</span>
                            </label>
                        </div>

                        {{-- Botão Submeter --}}
                        <div class="pt-2">
                            <button type="submit" class="btn-primary-tw w-full justify-center py-3 text-sm font-bold shadow-lg shadow-brand-600/30 hover:scale-[1.01] transition-transform">
                                <i class="fas fa-right-to-bracket text-xs"></i>
                                <span>Aceder à Maternidade+</span>
                            </button>
                        </div>
                    </form>

                    {{-- Rodapé Informativo --}}
                    <div class="pt-4 border-t border-surface-100 text-center text-3xs text-surface-400 space-y-1">
                        <p>© {{ date('Y') }} Maternidade+ · Ministério da Saúde (MISAU Moçambique)</p>
                        <p class="text-surface-400 font-mono">Centro de Saúde de Quelimane Urbano · Zambézia</p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {});
            });
        }
    </script>
</body>
</html>