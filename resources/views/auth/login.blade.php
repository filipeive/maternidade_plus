<!DOCTYPE html>
<html lang="pt" class="h-full bg-surface-900">
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
<body class="h-full font-sans antialiased text-surface-900 selection:bg-brand-500 selection:text-white">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-white">
        
        {{-- COLUNA 1: HERO VISUAL COM GESTANTE AFRICANA REAL --}}
        <div class="lg:col-span-7 relative flex flex-col justify-between p-8 sm:p-12 lg:p-16 text-white min-h-[400px] lg:min-h-screen bg-brand-950 overflow-hidden">
            
            {{-- Imagem de Fundo com Gestante Africana Real --}}
            <img src="{{ asset('img/gestante-africana.jpg') }}" alt="Gestante Africana Moçambique" class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000">
            
            {{-- Overlays Gradientes Elegantes (Escuro com Verde Brand do MISAU) --}}
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-900/80 to-brand-950/70"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-brand-950/90 via-transparent to-brand-950/40"></div>

            {{-- Topo: Branding MISAU --}}
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 text-gold-300 text-2xl shadow-xl">
                        <i class="fas fa-person-pregnant"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Maternidade<span class="text-gold-400">+</span></h1>
                        <p class="text-2xs font-bold uppercase tracking-widest text-gold-300">República de Moçambique · MISAU</p>
                    </div>
                </div>

                {{-- Badges Moçambique --}}
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-white">
                        <i class="fas fa-shield-heart text-gold-400 text-xs"></i>
                        <span>Protocolo MISAU: 8 Consultas ANC</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-white">
                        <i class="fas fa-map-marker-alt text-emerald-400 text-xs"></i>
                        <span>Quelimane · Zambézia</span>
                    </span>
                </div>
            </div>

            {{-- Centro / Conteúdo Principal do Hero --}}
            <div class="relative z-10 my-12 lg:my-auto max-w-xl space-y-4">
                <div class="inline-block px-3 py-1 rounded-md bg-gold-400/20 border border-gold-400/40 text-gold-300 text-xs font-bold uppercase tracking-wider">
                    Saúde Materno-Infantil
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight tracking-tight drop-shadow-md">
                    Protegendo a Vida de Mães e Bebês em Moçambique
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed font-normal">
                    Plataforma SaaS integrada para acompanhamento pré-natal, rastreio clínico de alertas, vacinação IPTp-SP e apoio contínuo ao parto e puerpério.
                </p>
            </div>

            {{-- Rodapé do Hero --}}
            <div class="relative z-10 border-t border-white/15 pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-white/70">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('favicon.svg') }}" alt="Maternidade+" class="w-8 h-8 rounded-lg shadow">
                    <div>
                        <p class="font-bold text-white">Serviço de Saúde Materno-Infantil</p>
                        <p class="text-2xs text-white/60">Direcção Provincial de Saúde da Zambézia</p>
                    </div>
                </div>
                <div class="text-2xs font-mono text-white/50">
                    Versão 2.0.0 (SaaS Build)
                </div>
            </div>
        </div>

        {{-- COLUNA 2: FORMULÁRIO DE AUTENTICAÇÃO --}}
        <div class="lg:col-span-5 flex flex-col justify-between p-8 sm:p-12 lg:p-16 bg-white">
            
            <div class="my-auto max-w-md w-full mx-auto space-y-8">
                
                {{-- Header do Formulário --}}
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-lg lg:hidden mb-4">
                        <i class="fas fa-person-pregnant"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-surface-900 tracking-tight">Iniciar Sessão</h2>
                    <p class="text-xs sm:text-sm text-surface-500">Introduza as suas credenciais profissionais para aceder ao sistema</p>
                </div>

                {{-- Status Session --}}
                @if (session('status'))
                    <div class="p-4 bg-brand-50 border border-brand-200 rounded-2xl text-xs text-brand-900 flex items-center gap-3">
                        <i class="fas fa-circle-check text-brand-600 text-base shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                            <p class="text-2xs text-crimson-600 mt-1.5 font-medium flex items-center gap-1">
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
                            <p class="text-2xs text-crimson-600 mt-1.5 font-medium flex items-center gap-1">
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
                        <button type="submit" class="btn-primary-tw w-full justify-center py-3 text-sm font-bold shadow-lg shadow-brand-500/20">
                            <i class="fas fa-right-to-bracket text-xs"></i>
                            <span>Entrar no Sistema</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Footer do Formulário --}}
            <div class="pt-8 border-t border-surface-100 text-center text-3xs text-surface-400 space-y-1">
                <p>© {{ date('Y') }} Maternidade+ · Ministério da Saúde (MISAU Moçambique)</p>
                <p class="text-surface-300">Centro de Saúde de Quelimane Urbano · Província da Zambézia</p>
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