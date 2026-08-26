<!DOCTYPE html>
<html lang="pt" class="h-full bg-surface-50">
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
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-surface-100 via-brand-50/30 to-surface-100 font-sans selection:bg-brand-500 selection:text-white">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-surface-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[600px]">
        
        {{-- COLUNA 1: PAINEL VISUAL DE SAÚDE MATERNA MOÇAMBIQUE --}}
        <div class="lg:col-span-6 bg-gradient-to-br from-brand-800 via-brand-700 to-ocean-800 p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            
            {{-- Padrão de Fundo Estilizado --}}
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

            {{-- Topo Branding --}}
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 text-gold-300 text-2xl shadow-lg">
                        <i class="fas fa-person-pregnant"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white">Maternidade<span class="text-gold-400">+</span></h1>
                        <span class="text-2xs font-semibold uppercase tracking-widest text-gold-200">MISAU · Moçambique</span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full border border-white/20 text-2xs text-gold-300 font-semibold backdrop-blur-xs">
                    <i class="fas fa-shield-heart text-xs"></i>
                    <span>Meta MISAU: 8 Contactos Pré-Natais</span>
                </div>
            </div>

            {{-- Ilustração SVG de Saúde Materna --}}
            <div class="relative z-10 my-6 flex justify-center">
                <svg class="w-full max-w-[280px] h-auto drop-shadow-xl" viewBox="0 0 300 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="150" cy="120" r="100" fill="white" fill-opacity="0.08"/>
                    <circle cx="150" cy="120" r="75" fill="white" fill-opacity="0.12"/>
                    
                    {{-- Ícone Mãe & Bebê --}}
                    <path d="M150 65C138.954 65 130 73.9543 130 85C130 96.0457 138.954 105 150 105C161.046 105 170 96.0457 170 85C170 73.9543 161.046 65 150 65Z" fill="#FDE68A"/>
                    <path d="M150 115C118 115 95 140 95 180C95 185 99 190 105 190H195C201 190 205 185 205 180C205 140 182 115 150 115Z" fill="#FFFFFF"/>
                    <path d="M150 135C135 135 120 150 120 175C120 180 124 185 130 185H170C176 185 180 180 180 175C180 150 165 135 150 135Z" fill="#F59E0B"/>
                    <path d="M150 78L153 84L160 85L155 90L156 97L150 93L144 97L145 90L140 85L147 84L150 78Z" fill="#0F766E"/>
                </svg>
            </div>

            {{-- Rodapé Informativo --}}
            <div class="relative z-10 space-y-2 border-t border-white/10 pt-4 text-xs text-white/80">
                <p class="font-medium">Sistema Integrado de Acompanhamento Pré-Natal e Puerpério para Unidades Sanitárias de Moçambique.</p>
                <div class="flex items-center justify-between text-3xs text-white/50 pt-1 font-mono">
                    <span>Versão 2.0.0 (SaaS Release)</span>
                    <span>Quelimane · Zambézia</span>
                </div>
            </div>
        </div>

        {{-- COLUNA 2: FORMULÁRIO DE LOGIN --}}
        <div class="lg:col-span-6 p-8 sm:p-12 flex flex-col justify-between bg-white">
            
            <div>
                <div class="mb-8">
                    <h2 class="text-xl sm:text-2xl font-bold text-surface-900">Acesso ao Sistema</h2>
                    <p class="text-xs text-surface-500 mt-1">Introduza as suas credenciais de utilizador para continuar</p>
                </div>

                {{-- Status Session --}}
                @if (session('status'))
                    <div class="mb-4 p-3 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900 flex items-center gap-2">
                        <i class="fas fa-circle-check text-brand-600"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="label-tw">Endereço de Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="nome@maternidade.mz"
                                   class="input-tw pl-9 text-xs @error('email') input-error-tw @enderror">
                        </div>
                        @error('email')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
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
                                   class="input-tw pl-9 pr-9 text-xs @error('password') input-error-tw @enderror">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600 text-xs">
                                <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lembrar-me --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-xs text-surface-600">Manter-me conectado</span>
                        </label>
                    </div>

                    {{-- Botão Submeter --}}
                    <div class="pt-2">
                        <button type="submit" class="btn-primary-tw w-full justify-center py-2.5 text-sm font-semibold shadow-md">
                            <i class="fas fa-right-to-bracket text-xs"></i>
                            <span>Entrar no Sistema</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="pt-6 border-t border-surface-100 text-center text-3xs text-surface-400">
                <span>© {{ date('Y') }} Maternidade+ · Ministério da Saúde (MISAU Moçambique)</span>
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