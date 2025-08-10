<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Maternidade+') }} - @yield('title', 'Sistema de Gestão Pré-Natal')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS Moçambicano -->
    <style>
        :root {
            --moz-green: #009639;
            --moz-yellow: #FFD700;
            --moz-red: #DC143C;
            --moz-black: #000000;
            --primary-gradient: linear-gradient(135deg, var(--moz-green) 0%, #00b894 100%);
            --accent-gradient: linear-gradient(135deg, var(--moz-yellow) 0%, #f39c12 100%);
            --danger-gradient: linear-gradient(135deg, var(--moz-red) 0%, #e74c3c 100%);
            --shadow-soft: 0 8px 25px rgba(0, 0, 0, 0.1);
            --shadow-strong: 0 15px 35px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            overflow-x: hidden;
        }

        /* Padrão de fundo inspirado nos tecidos moçambicanos */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="moz-pattern" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse"><circle cx="15" cy="15" r="1" fill="rgba(0,150,57,0.03)"/><path d="M15,5 L25,15 L15,25 L5,15 Z" fill="none" stroke="rgba(255,215,0,0.02)" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(%23moz-pattern)"/></svg>');
            z-index: -1;
            animation: movePattern 30s ease-in-out infinite;
        }

        @keyframes movePattern {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-5px, -5px);
            }
        }

        /* Sidebar Moçambicana */
        .sidebar {
            background: var(--primary-gradient) !important;
            box-shadow: var(--shadow-strong) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><circle cx="30" cy="30" r="2" fill="rgba(255,255,255,0.05)"/><path d="M30,10 L50,30 L30,50 L10,30 Z" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></svg>');
            animation: rotate 30s linear infinite;
            opacity: 0.3;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .sidebar-brand {
            background: rgba(0, 0, 0, 0.15) !important;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand .position-relative {
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .sidebar-brand h4 {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .sidebar-brand h4::after {
            content: '🇲🇿';
            margin-left: 8px;
            font-size: 0.8em;
            animation: wave 2s ease-in-out infinite;
        }

        @keyframes wave {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(-10deg);
            }

            75% {
                transform: rotate(10deg);
            }
        }

        .sidebar-brand small {
            opacity: 0.9;
            font-weight: 400;
        }

        /* Menu Items Melhorados */
        .nav-pills .nav-link {
            border-radius: 12px !important;
            margin: 4px 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-pills .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .nav-pills .nav-link:hover::before {
            left: 0;
        }

        .nav-pills .nav-link:hover {
            color: white !important;
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1) !important;
        }

        .nav-pills .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-pills .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }

        /* User Area Melhorada */
        .user-area {
            background: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .avatar {
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            transition: all 0.3s ease;
        }

        .user-area .btn-outline-light {
            background: rgba(220, 20, 60, 0.8) !important;
            border-color: rgba(220, 20, 60, 0.5) !important;
            transition: all 0.3s ease;
        }

        .user-area .btn-outline-light:hover {
            background: var(--moz-red) !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220, 20, 60, 0.3);
        }

        /* Top Navbar Melhorada */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: var(--shadow-soft);
        }

        .navbar-brand {
            color: #2d3436 !important;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            color: var(--moz-green);
            margin-right: 8px;
        }

        /* Search melhorado */
        .form-control:focus {
            border-color: var(--moz-green) !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 150, 57, 0.15) !important;
        }

        /* Localização */
        .text-primary {
            color: var(--moz-green) !important;
        }

        /* Notifications */
        .position-absolute.badge {
            background: var(--danger-gradient) !important;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* Dropdown melhorado */
        .dropdown-menu {
            border: none !important;
            box-shadow: var(--shadow-strong) !important;
            border-radius: var(--border-radius) !important;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .dropdown-header {
            color: var(--moz-green) !important;
            font-weight: 600;
        }

        .dropdown-item:hover {
            background: rgba(0, 150, 57, 0.1) !important;
            color: var(--moz-green) !important;
        }

        /* Breadcrumb Moçambicano */
        .breadcrumb {
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: var(--shadow-soft);
        }

        .breadcrumb-item a {
            color: var(--moz-green) !important;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--moz-red) !important;
        }

        .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 600;
        }

        /* Alerts Moçambicanos */
        .alert-success {
            background: linear-gradient(135deg, rgba(0, 150, 57, 0.1), rgba(0, 184, 148, 0.1)) !important;
            color: var(--moz-green) !important;
            border: 1px solid rgba(0, 150, 57, 0.2) !important;
            border-left: 4px solid var(--moz-green) !important;
            border-radius: var(--border-radius) !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.1), rgba(231, 76, 60, 0.1)) !important;
            color: var(--moz-red) !important;
            border: 1px solid rgba(220, 20, 60, 0.2) !important;
            border-left: 4px solid var(--moz-red) !important;
            border-radius: var(--border-radius) !important;
        }

        /* Card Principal */
        .card {
            border: none !important;
            border-radius: var(--border-radius) !important;
            box-shadow: var(--shadow-soft) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-strong) !important;
        }

        .card-body {
            position: relative;
            z-index: 1;
        }

        /* Botões Moçambicanos */
        .btn-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 150, 57, 0.3) !important;
        }

        .btn-warning {
            background: var(--accent-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
            color: white !important;
            font-weight: 500;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3) !important;
            color: white !important;
        }

        .btn-danger {
            background: var(--danger-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3) !important;
        }

        /* Responsividade Melhorada */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            body::before {
                animation: none;
            }
        }

        /* Animações de Entrada */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Melhoramentos específicos para notificações */
        .dropdown-menu[style*="width: 300px"] .dropdown-item {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dropdown-menu[style*="width: 300px"] .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Footer customizado */
        .main-content {
            position: relative;
            z-index: 1;
        }
    </style>

    <!-- Custom CSS adicional se necessário -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-0 bg-primary"
        style="width: 280px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000;">

        <div class="sidebar-brand p-4 text-center bg-primary-dark">
            <div class="position-relative d-inline-block">
                <i class="fas fa-baby-carriage text-white fs-1"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    +
                </span>
            </div>
            <h4 class="text-white mt-3 mb-0 fw-bold">
                Maternidade<span class="text-warning">+</span>
            </h4>
            <small class="text-light opacity-75">Cuidado Integral Pré-Natal</small>
        </div>

        <ul class="nav nav-pills flex-column px-3 py-3 flex-grow-1">
            <li class="nav-item mb-2">
                {!! menu_item('dashboard', 'fa-tachometer-alt', 'Dashboard') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('patients.index', 'fa-venus', 'Gestantes') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('consultations.index', 'fa-calendar-check', 'Consultas ANC') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('exams.index', 'fa-microscope', 'Exames') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('laboratory.index', 'fa-flask-vial', 'Laboratório') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('vaccines.index', 'fa-syringe', 'Vacinas & IPTp') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('reports.index', 'fa-chart-pie', 'Relatórios MISAU') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('home_visits.index', 'fa-home-heart', 'Visitas Domiciliárias') !!}
            </li>

            {{-- separador --}}
            <li class="my-3">
                <hr class="dropdown-divider border-light opacity-25">
            </li>

            <li class="nav-item mb-2">
                {!! menu_item('users.index', 'fa-users-gear', 'Usuários') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('profile.edit', 'fa-user-gear', 'Perfil', 'profile.*') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('settings.index', 'fa-sliders', 'Configurações') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('help', 'fa-circle-question', 'Ajuda') !!}
            </li>
        </ul>

        <div class="user-area p-3 bg-primary-dark">
            <div class="d-flex align-items-center text-white">
                <div class="position-relative me-3">
                    <div class="avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle">
                        <span class="visually-hidden">Online</span>
                    </span>
                </div>
                <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <small class="opacity-75">{{ auth()->user()->getRoleNames()->first() }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" style="margin-left: 280px; background-color: #f8f9fa;">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container-fluid px-4">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-brand mb-0 h1 fw-bold text-primary">
                    <i class="fas @yield('title-icon', 'fa-home') me-2"></i>
                    @yield('page-title', 'Dashboard')
                </div>

                <div class="ms-auto d-flex align-items-center">
                    {{-- search --}}
                    <form class="d-none d-md-flex me-3" role="search">
                        <input class="form-control form-control-sm" type="search" placeholder="Pesquisar gestantes..."
                            aria-label="Search">
                        <button class="btn btn-outline-secondary btn-sm ms-2" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <div class="me-4 text-end d-none d-md-block">
                        <div class="text-muted small">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>
                        <div class="text-primary small fw-semibold">
                            <i class="fas fa-map-marker-alt me-1"></i> Quelimane, Moçambique 🇲🇿
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light position-relative rounded-circle p-2" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-bell text-muted"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill py-0.5 px-2 bg-danger"
                                style="top:-4px;">
                                3
                                <span class="visually-hidden">notificações não lidas</span>
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" style="width: 300px;">
                            <li class="dropdown-header fw-bold text-primary">
                                <i class="fas fa-bell me-2"></i>Notificações MISAU
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-success">
                                        <i class="fas fa-calendar-check fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Consulta ANC agendada</div>
                                        <small class="text-muted">8º contacto - Maria Silva</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-warning">
                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Exame HIV pendente</div>
                                        <small class="text-muted">Resultado laboratório</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-info">
                                        <i class="fas fa-syringe fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">IPTp vencimento próximo</div>
                                        <small class="text-muted">Verificar doses malária</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-center text-primary small fw-semibold" href="#">
                                    <i class="fas fa-eye me-1"></i>Ver todas as notificações
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown ms-3">
                        <button class="btn btn-light rounded-circle p-0" type="button" data-bs-toggle="dropdown">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li class="dropdown-header text-primary fw-bold">
                                <i class="fas fa-user-md me-2"></i>Olá, {{ explode(' ', auth()->user()->name)[0] }}
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-cog me-2 text-muted"></i>Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                    <i class="fas fa-moon me-2 text-muted"></i>Modo Escuro
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2 text-muted"></i>Configurações
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2 text-muted"></i>Sair do Sistema
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid px-4 py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent px-3 py-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    {!! View::getSection('breadcrumbs') ?? '<li class="breadcrumb-item active">Dashboard</li>' !!}
                </ol>
            </nav>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center fade-in"
                    role="alert">
                    <i class="fas fa-check-circle me-2 fa-lg"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center fade-in"
                    role="alert">
                    <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="card border-0 shadow-sm fade-in">
                <div class="card-body p-4">
                    @yield('content')
                </div>
            </div>
            <!-- Footer Moçambicano -->
            <footer class="text-center py-4 mt-5"
                style="color: #6c757d; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); border-radius: var(--border-radius);">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-md-start">
                            <small>
                                © {{ date('Y') }} Maternidade+ | Sistema desenvolvido para o <strong>MISAU</strong>
                                🇲🇿
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <small>
                                Versão 1.0.0 | <a href="#" class="text-primary text-decoration-none">Termos de
                                    Uso</a> | <a href="#"
                                    class="text-primary text-decoration-none">Privacidade</a>
                            </small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS Moçambicano -->
    <script>
        // Sidebar toggle para mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');

            // Overlay para mobile
            if (sidebar.classList.contains('show')) {
                const overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 1049;
                `;
                overlay.onclick = () => {
                    sidebar.classList.remove('show');
                    overlay.remove();
                };
                document.body.appendChild(overlay);
            }
        });

        // Auto-hide alerts com animação melhorada
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                alert.style.transform = 'translateY(-20px)';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 500);
            });
        }, 8000);

        // Dark mode toggle melhorado
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Feedback visual
            const icon = document.querySelector('.fa-moon');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'fas fa-sun me-2 text-muted' : 'fas fa-moon me-2 text-muted';
            }

            // Animação suave
            document.body.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                document.body.style.transition = '';
            }, 300);
        }

        // Initialize theme from localStorage
        if (localStorage.getItem('theme')) {
            document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('theme'));
        }

        // Animação de entrada para elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Observar todos os elementos que precisam de animação
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.card, .alert').forEach(el => {
                observer.observe(el);
            });
        });

        // Melhoramentos na navegação
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Remove active de todos os links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                // Adiciona active ao link clicado
                this.classList.add('active');
            });
        });

        // Funcionalidade de pesquisa melhorada
        const searchInput = document.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value;
                if (query.length > 2) {
                    // Aqui você implementaria a lógica de pesquisa
                    console.log('Pesquisando por:', query);
                    // Exemplo: fazer requisição AJAX para buscar gestantes
                }
            });

            // Efeito visual no focus
            searchInput.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });

            searchInput.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        }

        // Notificações em tempo real (simulação)
        function updateNotifications() {
            const badge = document.querySelector('.notification-badge');
            if (badge && Math.random() > 0.9) { // 10% chance
                const current = parseInt(badge.textContent);
                badge.textContent = current + 1;

                // Animação de nova notificação
                badge.style.animation = 'none';
                badge.offsetHeight; // Trigger reflow
                badge.style.animation = 'bounce 1s ease-in-out';

                // Toast notification (opcional)
                showToast('Nova notificação do sistema MISAU', 'info');
            }
        }

        // Atualizar notificações a cada 30 segundos
        setInterval(updateNotifications, 30000);

        // Sistema de toast para feedback
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'info' ? 'primary' : type} border-0`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            `;

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'info' ? 'info-circle' : 'check-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove element after hide
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Melhorar experiência dos dropdowns
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(dropdown => {
            dropdown.addEventListener('show.bs.dropdown', function() {
                this.style.transform = 'scale(1.05)';
            });

            dropdown.addEventListener('hide.bs.dropdown', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Feedback tátil para botões
        document.querySelectorAll('button, .btn').forEach(button => {
            button.addEventListener('click', function(e) {
                // Efeito ripple
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;

                if (this.style.position !== 'absolute' && this.style.position !== 'relative') {
                    this.style.position = 'relative';
                }
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // CSS para ripple effect
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(rippleStyle);

        // Detectar conexão e mostrar status
        function checkConnection() {
            const isOnline = navigator.onLine;
            const status = document.createElement('div');

            if (!isOnline) {
                status.className = 'alert alert-warning position-fixed';
                status.style.cssText = 'top: 10px; left: 50%; transform: translateX(-50%); z-index: 9999;';
                status.innerHTML = `
                    <i class="fas fa-wifi me-2"></i>
                    <strong>Sem conexão!</strong> Dados podem não estar atualizados.
                `;
                document.body.appendChild(status);

                const reconnect = () => {
                    if (navigator.onLine) {
                        status.remove();
                        showToast('Conexão restaurada! ✅', 'success');
                        window.removeEventListener('online', reconnect);
                    }
                };
                window.addEventListener('online', reconnect);
            }
        }

        // Verificar conexão ao carregar
        window.addEventListener('load', checkConnection);
        window.addEventListener('offline', checkConnection);

        // Performance: lazy load para imagens (se houver)
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Keyboard shortcuts para acessibilidade
        document.addEventListener('keydown', function(e) {
            // Alt + M = Toggle menu mobile
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                document.getElementById('sidebarToggle')?.click();
            }

            // Alt + S = Focus search
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                document.querySelector('input[type="search"]')?.focus();
            }

            // Alt + N = Open notifications
            if (e.altKey && e.key === 'n') {
                e.preventDefault();
                document.querySelector('[data-bs-toggle="dropdown"] .fa-bell')?.parentElement?.click();
            }
        });

        // Log de inicialização
        console.log('🇲🇿 Maternidade+ Sistema carregado com sucesso!');
        console.log('Sistema de gestão pré-natal para Moçambique - MISAU');
        console.log('Versão: 1.0.0 | Ano: 2025');

        // Feedback para desenvolvedores
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('🔧 Modo de desenvolvimento ativo');
            console.log('💡 Atalhos disponíveis:');
            console.log('   Alt + M = Toggle menu');
            console.log('   Alt + S = Focus pesquisa');
            console.log('   Alt + N = Abrir notificações');
        }
    </script>

    @stack('scripts')
</body>

</html>
