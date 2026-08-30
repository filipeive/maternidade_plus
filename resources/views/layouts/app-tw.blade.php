<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Maternidade+') }} — @yield('title', 'Sistema de Gestão Pré-Natal')</title>

    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f766e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Maternidade+">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body class="h-full" x-data="appShell()" x-cloak>

    {{-- ============================================================
         TOAST CONTAINER
         ============================================================ --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-2"></div>

    {{-- ============================================================
         MOBILE OVERLAY
         ============================================================ --}}
    <div x-show="sidebarOpen && isMobile"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden">
    </div>

    {{-- ============================================================
         SIDEBAR
         ============================================================ --}}
    <aside
        :class="{
            'w-sidebar': !sidebarCollapsed || isMobile,
            'sidebar-collapsed': sidebarCollapsed && !isMobile,
            'translate-x-0': sidebarOpen || !isMobile,
            '-translate-x-full': !sidebarOpen && isMobile
        }"
        class="sidebar-tw"
        @keydown.escape.window="if(isMobile) sidebarOpen = false">

        {{-- Sidebar Header / Brand --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10">
            <div class="relative shrink-0">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-heart-pulse text-white text-lg"></i>
                </div>
            </div>
            <div class="sidebar-text overflow-hidden whitespace-nowrap">
                <h1 class="text-base font-bold text-white leading-none">
                    Maternidade<span class="text-gold-400">+</span>
                </h1>
                <p class="text-2xs text-white/50 mt-0.5">Cuidado Pré-Natal</p>
            </div>
        </div>

        {{-- Collapse Toggle (Desktop only) --}}
        <button @click="toggleSidebar()"
                class="hidden lg:flex absolute top-5 -right-3 w-6 h-6 items-center justify-center
                       bg-surface-100 hover:bg-surface-200 rounded-full shadow-md border border-surface-200
                       text-surface-900 hover:text-brand-600
                       transition-transform duration-200 z-50"
                :class="{'rotate-180': sidebarCollapsed}"
                title="Recolher menu">
            <i class="fas fa-chevron-left text-2xs"></i>
        </button>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- SECTION: Principal --}}
            <div class="sidebar-section-title sidebar-text">Principal</div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-home"></i></span>
                <span class="sidebar-text">Início</span>
            </a>

            <a href="{{ route('alertas.index') }}"
               class="sidebar-link {{ request()->routeIs('alertas.index') ? 'active' : '' }} relative">
                <span class="sidebar-link-icon"><i class="fas fa-triangle-exclamation"></i></span>
                <span class="sidebar-text flex-1">Alertas Precoces</span>
                @if(($alertasAltosCount ?? 0) > 0)
                    <span class="ml-auto bg-crimson-500 text-white text-2xs font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                        {{ $alertasAltosCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('alertas.metricas') }}"
               class="sidebar-link {{ request()->routeIs('alertas.metricas*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-chart-line"></i></span>
                <span class="sidebar-text">Métricas</span>
            </a>

            {{-- SECTION: Clínico --}}
            <div class="sidebar-divider"></div>
            <div class="sidebar-section-title sidebar-text">Clínico</div>

            <a href="{{ route('patients.index') }}"
               class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-person-pregnant"></i></span>
                <span class="sidebar-text">Gestantes</span>
            </a>

            <a href="{{ route('consultations.index') }}"
               class="sidebar-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-calendar-check"></i></span>
                <span class="sidebar-text">Consultas ANC</span>
            </a>

            <a href="{{ route('mod_sis_b01.index') }}"
               class="sidebar-link {{ request()->routeIs('mod_sis_b01.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-book-medical"></i></span>
                <span class="sidebar-text">Livro CPN (MOD-SIS-B01)</span>
            </a>

            <a href="{{ route('exams.index') }}"
               class="sidebar-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-microscope"></i></span>
                <span class="sidebar-text">Exames</span>
            </a>

            <a href="{{ route('laboratory.index') }}"
               class="sidebar-link {{ request()->routeIs('laboratory.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-flask-vial"></i></span>
                <span class="sidebar-text">Laboratório</span>
            </a>

            <a href="{{ route('vaccines.index') }}"
               class="sidebar-link {{ request()->routeIs('vaccines.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-syringe"></i></span>
                <span class="sidebar-text">Vacinas & IPTp</span>
            </a>

            {{-- SECTION: Acompanhamento --}}
            <div class="sidebar-divider"></div>
            <div class="sidebar-section-title sidebar-text">Acompanhamento</div>

            <a href="{{ route('home_visits.index') }}"
               class="sidebar-link {{ request()->routeIs('home_visits.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-house-medical"></i></span>
                <span class="sidebar-text">Visitas Domiciliárias</span>
            </a>

            <a href="{{ route('births.index') }}"
               class="sidebar-link {{ request()->routeIs('births.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-baby"></i></span>
                <span class="sidebar-text">Partos</span>
            </a>

            <a href="{{ route('notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('notifications.*') || request()->routeIs('sms.*') ? 'active' : '' }} relative">
                <span class="sidebar-link-icon"><i class="fas fa-bell"></i></span>
                <span class="sidebar-text flex-1">Notificações & SMS</span>
                @if(($unreadNotificationsCount ?? 0) > 0)
                    <span class="ml-auto bg-brand-500 text-white text-2xs font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                        {{ $unreadNotificationsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('scanner') }}"
               class="sidebar-link {{ request()->routeIs('scanner') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-qrcode"></i></span>
                <span class="sidebar-text">Leitor QR Code</span>
            </a>

            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sidebar-text">Relatórios MISAU</span>
            </a>

            {{-- SECTION: Sistema --}}
            @hasrole('Administrador')
                <div class="sidebar-divider"></div>
                <div class="sidebar-section-title sidebar-text">Sistema</div>

                <a href="{{ route('users.index') }}"
                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="sidebar-link-icon"><i class="fas fa-users-gear"></i></span>
                    <span class="sidebar-text">Utilizadores</span>
                </a>

                <a href="{{ route('settings.index') }}"
                   class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <span class="sidebar-link-icon"><i class="fas fa-sliders"></i></span>
                    <span class="sidebar-text">Configurações</span>
                </a>
            @endhasrole

            <a href="{{ route('help.index') }}"
               class="sidebar-link {{ request()->routeIs('help.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="fas fa-circle-question"></i></span>
                <span class="sidebar-text">Ajuda</span>
            </a>
        </nav>

        {{-- Sidebar Footer — User --}}
        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 transition-colors">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    {{-- Online Indicator — replaces intrusiv network monitor --}}
                    <span x-show="isOnline"
                          class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-brand-700"></span>
                    <span x-show="!isOnline"
                          class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-surface-400 ring-2 ring-brand-700 animate-pulse"></span>
                </div>
                {{-- User Info --}}
                <div class="sidebar-text overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Utilizador' }}</p>
                    <p class="text-2xs text-white/50 truncate">
                        {{ auth()->check() && method_exists(auth()->user(), 'getRoleNames') ? (auth()->user()->getRoleNames()->first() ?? 'Profissional') : 'Utilizador' }}
                    </p>
                </div>
                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="ml-auto sidebar-text">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white transition-colors p-1" title="Sair">
                        <i class="fas fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ============================================================
         MAIN CONTENT AREA
         ============================================================ --}}
    <div :class="{
             'lg:ml-sidebar': !sidebarCollapsed,
             'lg:content-collapsed': sidebarCollapsed
         }"
         class="min-h-screen transition-all duration-300 flex flex-col">

        {{-- ============================================================
             TOP HEADER
             ============================================================ --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-surface-200/60">
            <div class="flex items-center gap-4 px-4 lg:px-6 h-16">

                {{-- Mobile Menu Toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="btn-icon-tw lg:hidden"
                        aria-label="Toggle menu">
                    <i class="fas fa-bars text-lg"></i>
                </button>

                {{-- Page Title --}}
                <div class="flex items-center gap-2 min-w-0">
                    <i class="fas @yield('title-icon', 'fa-grid-2') text-brand-500"></i>
                    <h2 class="text-base font-semibold text-surface-900 truncate">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Search (Desktop) --}}
                <div class="hidden md:block relative w-64 xl:w-80" x-data="searchManager()">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                        <input type="search"
                               x-model="query"
                               @input.debounce.300ms="search()"
                               @focus="showResults = query.length >= 2"
                               @keydown.escape="showResults = false"
                               @keydown.arrow-down.prevent="navigateDown()"
                               @keydown.arrow-up.prevent="navigateUp()"
                               @keydown.enter.prevent="selectActive()"
                               placeholder="Pesquisar gestantes…"
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-lg
                                      bg-surface-100 border-0
                                      placeholder:text-surface-400
                                      focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300
                                      transition-all duration-200">
                    </div>

                    {{-- Search Results Dropdown --}}
                    <div x-show="showResults && results.length > 0"
                         @click.outside="showResults = false"
                         x-transition
                         class="dropdown-tw mt-1 max-h-80 overflow-y-auto py-2">
                        <template x-for="(patient, index) in results" :key="patient.id || index">
                            <a :href="patient.url || '#'"
                               :class="{'bg-brand-50': activeIndex === index}"
                               class="dropdown-item-tw"
                               @mouseenter="activeIndex = index">
                                <i class="fas fa-person-pregnant text-brand-400 w-5 text-center"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate" x-text="patient.nome_completo || 'N/A'"></p>
                                    <p class="text-2xs text-surface-400 truncate"
                                       x-text="'BI: ' + (patient.documento_bi || 'N/A') + ' · ' + (patient.semanas_gestacao || '?') + 'ª sem'">
                                    </p>
                                </div>
                            </a>
                        </template>
                    </div>

                    {{-- No Results --}}
                    <div x-show="showResults && results.length === 0 && !loading && query.length >= 2"
                         @click.outside="showResults = false"
                         x-transition
                         class="dropdown-tw py-6 text-center text-surface-400 text-sm">
                        <i class="fas fa-search-minus mb-1"></i>
                        <p>Nenhuma gestante encontrada</p>
                    </div>

                    {{-- Loading --}}
                    <div x-show="loading"
                         class="dropdown-tw py-4 text-center text-surface-400 text-sm">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Pesquisando…
                    </div>
                </div>

                {{-- Date/Location (xl+ only) --}}
                <div class="hidden xl:flex items-center gap-2 text-xs text-surface-500">
                    <i class="fas fa-map-marker-alt text-brand-500"></i>
                    <span>Quelimane, MZ</span>
                    <span class="text-surface-300">·</span>
                    <span x-text="currentDate"></span>
                </div>

                {{-- Action Icons --}}
                <div class="flex items-center gap-1.5">

                    {{-- QR Code Scanner --}}
                    <a href="{{ route('scanner') }}" class="btn-icon-tw relative text-brand-600 hover:bg-brand-50" title="Leitor de QR Code / Cartão da Gestante">
                        <i class="fas fa-qrcode text-base"></i>
                    </a>

                    {{-- Alertas Precoces --}}
                    <a href="{{ route('alertas.index') }}"
                       class="btn-icon-tw relative"
                       title="Alertas Precoces Clínicos">
                        <i class="fas fa-triangle-exclamation {{ ($alertasAltosCount ?? 0) > 0 ? 'text-crimson-500' : '' }}"></i>
                        @if(($alertasAltosCount ?? 0) > 0)
                            <span class="badge-count">{{ $alertasAltosCount }}</span>
                        @endif
                    </a>

                    {{-- Notificações --}}
                    <div class="relative" x-data="notificationManager()" @click.outside="open = false">
                        <button @click="toggle()" class="btn-icon-tw relative" title="Notificações">
                            <i class="fas fa-bell"></i>
                            <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount"
                                  class="badge-count" x-cloak></span>
                        </button>

                        {{-- Notifications Dropdown --}}
                        <div x-show="open" x-transition class="dropdown-tw w-80 sm:w-96 max-h-[30rem] right-0 shadow-2xl z-50">
                            <div class="px-4 py-3 flex items-center justify-between border-b border-surface-100 bg-surface-50/50">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-surface-900">Notificações</h3>
                                    <span x-show="unreadCount > 0" class="text-2xs font-bold px-1.5 py-0.5 rounded-full bg-brand-100 text-brand-700" x-text="unreadCount + ' novas'" x-cloak></span>
                                </div>
                                <button x-show="unreadCount > 0" @click="markAllRead()" class="text-2xs text-brand-600 hover:text-brand-700 font-medium" x-cloak>
                                    Marcar todas como lidas
                                </button>
                            </div>

                            <div class="overflow-y-auto max-h-72 divide-y divide-surface-100">
                                <template x-for="n in notifications" :key="n.id">
                                    <a :href="n.url" @click.prevent="handleClick(n)"
                                       class="flex items-start gap-3 px-4 py-3 hover:bg-surface-50 transition-colors cursor-pointer"
                                       :class="{'bg-brand-50/40': n.unread}">
                                        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-xs"
                                             :class="{
                                                 'bg-brand-100 text-brand-600': n.color === 'success',
                                                 'bg-ocean-100 text-ocean-600': n.color === 'info',
                                                 'bg-gold-100 text-gold-600': n.color === 'warning',
                                                 'bg-crimson-100 text-crimson-600': n.color === 'danger'
                                             }">
                                            <i :class="'fas fa-' + (n.icon || 'bell')"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-surface-900 truncate" x-text="n.title"></p>
                                            <p class="text-2xs text-surface-500 line-clamp-2 mt-0.5" x-text="n.message"></p>
                                            <p class="text-2xs text-surface-400 mt-1" x-text="n.time"></p>
                                        </div>
                                        <span x-show="n.unread"
                                              class="w-2 h-2 rounded-full bg-brand-500 shrink-0 mt-2"></span>
                                    </a>
                                </template>

                                <div x-show="notifications.length === 0 && !loading" class="py-8 text-center text-surface-400">
                                    <i class="fas fa-bell-slash text-2xl mb-2 text-surface-300"></i>
                                    <p class="text-xs">Sem notificações no momento</p>
                                </div>

                                <div x-show="loading" class="py-6 text-center text-surface-400">
                                    <i class="fas fa-spinner fa-spin text-sm"></i>
                                    <p class="text-2xs mt-1">Carregando…</p>
                                </div>
                            </div>

                            <div class="p-2.5 border-t border-surface-100 bg-surface-50/50 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 inline-flex items-center gap-1.5">
                                    <span>Ver todas as notificações</span>
                                    <i class="fas fa-arrow-right text-2xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- User Menu --}}
                    <div class="relative" x-data="{open: false}" @click.outside="open = false">
                        <button @click="open = !open"
                                class="flex items-center gap-2 p-1 rounded-lg hover:bg-surface-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-500 to-brand-700
                                        flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="hidden lg:block text-sm font-medium text-surface-700 max-w-[100px] truncate">
                                {{ explode(' ', auth()->user()->name ?? 'Utilizador')[0] }}
                            </span>
                            <i class="fas fa-chevron-down text-2xs text-surface-400 hidden lg:block"
                               :class="{'rotate-180': open}"
                               style="transition: transform 0.2s"></i>
                        </button>

                        <div x-show="open" x-transition class="dropdown-tw w-56 right-0">
                            <div class="px-4 py-3 border-b border-surface-100">
                                <p class="text-sm font-semibold text-surface-900">{{ auth()->user()->name ?? 'Utilizador' }}</p>
                                <p class="text-2xs text-surface-500">
                                    {{ auth()->check() && method_exists(auth()->user(), 'getRoleNames') ? (auth()->user()->getRoleNames()->first() ?? 'Profissional') : '' }}
                                </p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item-tw">
                                <i class="fas fa-user-pen w-5 text-center text-surface-400"></i>
                                <span>Perfil</span>
                            </a>
                            <button @click="toggleTheme()" class="dropdown-item-tw w-full text-left">
                                <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'" class="w-5 text-center text-surface-400"></i>
                                <span x-text="darkMode ? 'Modo Claro' : 'Modo Escuro'"></span>
                            </button>
                            <a href="{{ route('settings.index') }}" class="dropdown-item-tw">
                                <i class="fas fa-cog w-5 text-center text-surface-400"></i>
                                <span>Configurações</span>
                            </a>
                            <div class="dropdown-divider-tw"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item-tw w-full text-left text-crimson-600 hover:bg-crimson-50">
                                    <i class="fas fa-right-from-bracket w-5 text-center"></i>
                                    <span>Sair do Sistema</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- ============================================================
             OFFLINE BANNER (only shows when actually offline)
             ============================================================ --}}
        <div x-show="!isOnline"
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-full"
             class="bg-gold-50 border-b border-gold-200 px-4 py-2 text-center text-sm text-gold-800">
            <i class="fas fa-wifi-slash mr-1"></i>
            Sem conexão — as alterações serão sincronizadas quando a rede voltar
        </div>

        {{-- ============================================================
             PAGE CONTENT
             ============================================================ --}}
        <main class="flex-1 p-4 lg:p-6">
            {{-- Breadcrumbs --}}
            <nav class="breadcrumb-tw mb-5" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                </a>
                <span class="breadcrumb-separator">/</span>
                @yield('breadcrumbs')
                @sectionMissing('breadcrumbs')
                    <span class="active">Dashboard</span>
                @endif
            </nav>



            {{-- Page Content --}}
            @yield('content')
        </main>

        {{-- ============================================================
             FOOTER
             ============================================================ --}}
        <footer class="border-t border-surface-200/60 bg-white/50 backdrop-blur-sm px-4 lg:px-6 py-4 mt-auto">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-surface-400">
                <p>© {{ date('Y') }} Maternidade+ · <span class="font-medium text-surface-500">MISAU</span> · Moçambique</p>
                <div class="flex items-center gap-3">
                    <span>v2.0.0</span>
                    <span class="text-surface-300">·</span>
                    <a href="{{ route('help.index') }}" class="hover:text-brand-600 transition-colors">Suporte</a>
                    <span class="text-surface-300">·</span>
                    <a href="{{ route('help.manual') }}" class="hover:text-brand-600 transition-colors">Documentação</a>
                </div>
            </div>
        </footer>
    </div>

    {{-- ============================================================
         ALPINE.JS APPLICATION LOGIC
         ============================================================ --}}
    <script>
        // ===== APP CONFIG =====
        const APP_CONFIG = {
            routes: {
                patientsSearch: "{{ \Illuminate\Support\Facades\Route::has('patients.search') ? route('patients.search') : '/patients/search' }}",
            },
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        };

        // ===== APP SHELL =====
        function appShell() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                isMobile: window.innerWidth < 1024,
                isOnline: navigator.onLine,
                darkMode: localStorage.getItem('darkMode') === 'true',
                currentDate: '',

                init() {
                    // Responsive handler
                    const mediaQuery = window.matchMedia('(min-width: 1024px)');
                    mediaQuery.addEventListener('change', (e) => {
                        this.isMobile = !e.matches;
                        if (!this.isMobile) this.sidebarOpen = false;
                    });

                    // Network monitor — NO popup on load, only on transitions
                    window.addEventListener('online', () => { this.isOnline = true; });
                    window.addEventListener('offline', () => { this.isOnline = false; });

                    // Dark mode
                    if (this.darkMode) document.documentElement.classList.add('dark');

                    // Date update
                    this.updateDate();
                    setInterval(() => this.updateDate(), 60000);

                    // Keyboard shortcuts
                    document.addEventListener('keydown', (e) => {
                        if (e.altKey && e.key === 'm') { e.preventDefault(); this.toggleSidebar(); }
                        if (e.altKey && e.key === 's') { e.preventDefault(); document.querySelector('[type="search"]')?.focus(); }
                    });
                },

                toggleSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = !this.sidebarOpen;
                    } else {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                    }
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    document.documentElement.classList.toggle('dark', this.darkMode);
                },

                updateDate() {
                    this.currentDate = new Date().toLocaleDateString('pt-MZ', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    });
                }
            };
        }

        // ===== SEARCH MANAGER =====
        function searchManager() {
            return {
                query: '',
                results: [],
                loading: false,
                showResults: false,
                activeIndex: -1,

                async search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.showResults = false;
                        return;
                    }

                    this.loading = true;
                    this.showResults = true;

                    try {
                        const url = APP_CONFIG.routes.patientsSearch;
                        const response = await fetch(`${url}?q=${encodeURIComponent(this.query)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': APP_CONFIG.csrfToken
                            }
                        });

                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        this.results = await response.json();
                    } catch (err) {
                        console.error('Search error:', err);
                        this.results = [];
                    } finally {
                        this.loading = false;
                        this.activeIndex = -1;
                    }
                },

                navigateDown() {
                    if (this.activeIndex < this.results.length - 1) this.activeIndex++;
                },
                navigateUp() {
                    if (this.activeIndex > 0) this.activeIndex--;
                },
                selectActive() {
                    const item = this.results[this.activeIndex];
                    if (item?.url) window.location.href = item.url;
                }
            };
        }

        // ===== NOTIFICATION MANAGER =====
        function notificationManager() {
            return {
                open: false,
                notifications: [],
                unreadCount: {{ $unreadNotificationsCount ?? 0 }},
                loading: false,

                init() {
                    this.load();
                    setInterval(() => this.checkNew(), 30000);
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) this.load();
                },

                async load() {
                    this.loading = true;
                    try {
                        const response = await fetch('/notifications/api/list', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': APP_CONFIG.csrfToken
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unreadCount ?? 0;
                        }
                    } catch (e) {
                        console.warn('Erro ao carregar notificações:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async handleClick(n) {
                    if (n.unread) {
                        n.unread = false;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        try {
                            fetch(`/notifications/${n.id}/mark-read`, {
                                method: 'PATCH',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': APP_CONFIG.csrfToken,
                                    'Content-Type': 'application/json'
                                }
                            });
                        } catch (err) {
                            console.error(err);
                        }
                    }
                    this.open = false;
                    if (n.url) {
                        window.location.href = n.url;
                    }
                },

                async markRead(id) {
                    const n = this.notifications.find(n => n.id === id);
                    if (n && n.unread) {
                        n.unread = false;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        try {
                            await fetch(`/notifications/${id}/mark-read`, {
                                method: 'PATCH',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': APP_CONFIG.csrfToken
                                }
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    }
                },

                async markAllRead() {
                    this.notifications.forEach(n => n.unread = false);
                    this.unreadCount = 0;
                    try {
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': APP_CONFIG.csrfToken
                            }
                        });
                        if (typeof window.showToast === 'function') {
                            showToast('Notificações marcadas como lidas', 'success');
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async checkNew() {
                    try {
                        const response = await fetch('/notifications/api/count', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.unreadCount = data.count ?? 0;
                        }
                    } catch (e) {}
                }
            };
        }

        // ===== GLOBAL TOAST =====
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
            const colors = { success: 'text-brand-500', error: 'text-crimson-500', warning: 'text-gold-500', info: 'text-ocean-500' };
            const bgColors = { success: 'bg-brand-50', error: 'bg-crimson-50', warning: 'bg-gold-50', info: 'bg-ocean-50' };

            const id = 'toast-' + Date.now();
            const toast = document.createElement('div');
            toast.id = id;
            toast.className = `toast-tw ${bgColors[type] || bgColors.info}`;
            toast.innerHTML = `
                <i class="fas fa-${icons[type] || icons.info} ${colors[type] || colors.info}"></i>
                <span class="flex-1 text-sm text-surface-800">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        };

        // ===== SERVICE WORKER =====
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW failed:', err));
            });
        }
    </script>

    {{-- ============================================================
         FLOATING AI ASSISTANT WIDGET
         ============================================================ --}}
    <div x-data="floatingAiWidget()"
         class="fixed bottom-5 right-5 z-50">
        
        {{-- Floating Trigger Button --}}
        <button @click="openAiWidget = !openAiWidget; if(openAiWidget) scrollToBottom();"
                class="w-14 h-14 rounded-full bg-gradient-to-r from-brand-600 to-brand-700 text-white shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-105 group relative"
                title="Assistente IA Maternidade+">
            <i class="fas fa-robot text-xl text-gold-300 group-hover:rotate-12 transition-transform"></i>
            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-gold-400 border-2 border-white animate-pulse"></span>
        </button>

        {{-- Floating Drawer/Popup Window --}}
        <div x-show="openAiWidget"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             @click.outside="openAiWidget = false"
             class="absolute bottom-16 right-0 w-80 sm:w-96 h-[490px] bg-white rounded-2xl shadow-2xl border border-surface-200 flex flex-col overflow-hidden text-xs z-50">
            
            <div class="p-3 bg-gradient-to-r from-brand-600 to-brand-700 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-robot text-gold-300 text-base"></i>
                    <div>
                        <h4 class="font-bold text-white leading-tight">Guia IA Maternidade+</h4>
                        <span class="text-2xs text-white/70">MISAU Moçambique</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="clearChat()" class="text-white/70 hover:text-white text-xs p-1" title="Limpar conversa">
                        <i class="fas fa-trash-can"></i>
                    </button>
                    <button @click="openAiWidget = false" class="text-white/70 hover:text-white text-xs p-1" title="Fechar">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 p-3 overflow-y-auto space-y-3 bg-surface-50/50" id="floating-chat-box">
                <template x-for="(m, i) in aiMessages" :key="i">
                    <div class="flex gap-2" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                        <template x-if="m.role === 'assistant'">
                            <div class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center shrink-0 text-2xs shadow-xs">
                                <i class="fas fa-robot"></i>
                            </div>
                        </template>
                        <div class="max-w-[85%] rounded-xl p-2.5 leading-relaxed shadow-2xs"
                             :class="m.role === 'user' ? 'bg-brand-600 text-white rounded-tr-none' : 'bg-white border border-surface-200 text-surface-900 rounded-tl-none'">
                            <p x-html="formatMessage(m.content)" class="whitespace-pre-line"></p>
                        </div>
                    </div>
                </template>
                <div x-show="aiLoading" class="text-surface-400 italic text-2xs flex items-center gap-1.5 p-2">
                    <i class="fas fa-spinner fa-spin text-brand-600"></i> Assistente IA a pensar...
                </div>
            </div>

            <div class="p-2.5 bg-white border-t border-surface-200">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-1.5">
                    <input type="text" x-model="aiInput" placeholder="Pergunte ao assistente..." class="input-tw py-1.5 px-3 text-xs flex-1" :disabled="aiLoading">
                    <button type="submit" class="btn-primary-tw btn-sm-tw px-3 py-1.5" :disabled="aiLoading || !aiInput.trim()">
                        <i class="fas fa-paper-plane text-2xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function floatingAiWidget() {
            const STORAGE_KEY = 'maternidade_ai_chat_history';
            const defaultGreeting = {
                role: 'assistant',
                content: 'Olá! Sou o Assistente IA do Maternidade+. Como posso ajudar hoje?'
            };

            let saved = [];
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (raw) saved = JSON.parse(raw);
            } catch (e) {
                saved = [];
            }

            return {
                openAiWidget: false,
                aiInput: '',
                aiLoading: false,
                aiMessages: (Array.isArray(saved) && saved.length > 0) ? saved : [defaultGreeting],

                saveHistory() {
                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(this.aiMessages));
                    } catch (e) {}
                },

                async sendMessage() {
                    const txt = this.aiInput.trim();
                    if (!txt || this.aiLoading) return;

                    const historyContext = this.aiMessages.slice(-8);

                    this.aiMessages.push({ role: 'user', content: txt });
                    this.saveHistory();
                    this.aiInput = '';
                    this.aiLoading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ route('help.ai.ask') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt: txt,
                                history: historyContext
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.aiMessages.push({ role: 'assistant', content: data.response });
                        } else {
                            this.aiMessages.push({ role: 'assistant', content: '⚠️ ' + (data.message || 'Erro ao consultar IA') });
                        }
                    } catch (e) {
                        this.aiMessages.push({ role: 'assistant', content: '❌ Erro de ligação com o servidor de IA.' });
                    } finally {
                        this.aiLoading = false;
                        this.saveHistory();
                        this.scrollToBottom();
                    }
                },

                clearChat() {
                    this.aiMessages = [{
                        role: 'assistant',
                        content: 'Conversa reiniciada. Como posso ajudar?'
                    }];
                    this.saveHistory();
                    this.scrollToBottom();
                },

                formatMessage(text) {
                    if (!text) return '';
                    return text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/`(.*?)`/g, '<code class="px-1 py-0.5 bg-surface-100 rounded text-brand-700 font-mono text-2xs">$1</code>');
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const cb = document.getElementById('floating-chat-box');
                        if (cb) cb.scrollTop = cb.scrollHeight;
                    });
                }
            };
        }
    </script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Configuração Global de Toast SweetAlert2
        const SwalToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Interceptador de Mensagens Flash Laravel
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                SwalToast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: @json(session('error')),
                    confirmButtonColor: '#0f766e',
                    confirmButtonText: 'Compreendido'
                });
            @endif

            @if(session('warning'))
                SwalToast.fire({
                    icon: 'warning',
                    title: @json(session('warning'))
                });
            @endif

            @if(session('info'))
                SwalToast.fire({
                    icon: 'info',
                    title: @json(session('info'))
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Atenção aos Dados',
                    html: '<ul style="text-align: left; font-size: 13px; color: #334155; padding-left: 10px;">@foreach($errors->all() as $err)<li style="margin-bottom: 4px;">• {{ addslashes($err) }}</li>@endforeach</ul>',
                    confirmButtonColor: '#0f766e',
                    confirmButtonText: 'Corrigir Formulário'
                });
            @endif
        });

        // Função utilitária global para confirmação SweetAlert2
        function confirmSwal(formOrUrl, message = "Esta ação não poderá ser desfeita!", title = "Tem a certeza?") {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sim, confirmar!',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl font-sans'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof formOrUrl === 'string') {
                        window.location.href = formOrUrl;
                    } else if (formOrUrl && typeof formOrUrl.submit === 'function') {
                        formOrUrl.submit();
                    }
                }
            });
            return false;
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {});
            });
        }
    </script>
    @stack('scripts')
</body>

</html>
