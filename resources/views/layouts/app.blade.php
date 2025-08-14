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

    <style>
        :root {
            --moz-green: #009639;
            --moz-yellow: #FFD700;
            --moz-red: #DC143C;
            --moz-black: #000000;
            --moz-blue: #0080C7;
            --primary-gradient: linear-gradient(135deg, var(--moz-green) 0%, #00b894 100%);
            --accent-gradient: linear-gradient(135deg, var(--moz-yellow) 0%, #f39c12 100%);
            --danger-gradient: linear-gradient(135deg, var(--moz-red) 0%, #e74c3c 100%);
            --shadow-soft: 0 8px 25px rgba(0, 0, 0, 0.08);
            --shadow-strong: 0 15px 35px rgba(0, 0, 0, 0.12);
            --shadow-card: 0 4px 15px rgba(0, 0, 0, 0.06);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 280px;
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
            position: relative;
        }

        /* Emblema de Moçambique como fundo sutil */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="30" fill="none" stroke="rgba(0,150,57,0.02)" stroke-width="2"/><path d="M50,20 L80,50 L50,80 L20,50 Z" fill="rgba(0,150,57,0.01)"/></svg>');
            background-repeat: repeat;
            background-size: 200px 200px;
            z-index: -2;
            animation: subtleMove 60s ease-in-out infinite;
        }

        @keyframes subtleMove {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-10px, -10px);
            }
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

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes pulse-notification {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-gradient);
            box-shadow: var(--shadow-strong);
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* OVERLAY */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* MAIN CONTENT RESPONSIVO */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* MOBILE RESPONSIVO */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-search {
                display: block !important;
            }

            .desktop-search {
                display: none !important;
            }

            .navbar-brand {
                font-size: 1rem;
            }
        }

        @media (min-width: 992px) {
            .mobile-search {
                display: none !important;
            }

            .desktop-search {
                display: flex !important;
            }
        }

        /* Sidebar Brand */
        .sidebar-brand {
            background: rgba(0, 0, 0, 0.15);
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .logo-wrapper {
            position: relative;
            display: inline-block;
            animation: pulse 3s ease-in-out infinite;
        }

        .sidebar-brand h4 {
            color: white;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        /* Navigation Items */
        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            margin: 4px 0;
            padding: 12px 16px;
            transition: var(--transition);
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
            z-index: 0;
        }

        .nav-pills .nav-link:hover::before {
            left: 0;
        }

        .nav-pills .nav-link:hover {
            color: white;
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-pills .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-pills .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            position: relative;
            z-index: 1;
        }

        .nav-pills .nav-link span {
            position: relative;
            z-index: 1;
        }

        /* User Area */
        .user-area {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            margin-top: auto;
        }

        /* Top Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Search Container */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-container .form-control {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.9);
        }

        .search-container .form-control:focus {
            border-color: var(--moz-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 150, 57, 0.15);
            transform: scale(1.02);
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            box-shadow: var(--shadow-strong);
            z-index: 1050;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            margin-top: 5px;
        }

        .search-item {
            padding: 12px 20px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-item:hover,
        .search-item.active {
            background: rgba(0, 150, 57, 0.1);
            color: var(--moz-green);
        }

        /* Loading spinner */
        .search-loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--moz-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* MODAL */
        .modal {
            z-index: 1055;
        }

        .modal-backdrop {
            z-index: 1050;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1060;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-header .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--moz-green), var(--moz-yellow), var(--moz-red));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-strong);
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 150, 57, 0.25);
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-strong);
            backdrop-filter: blur(10px);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        /* Role-based menu styling */
        .nav-link[data-permission] {
            opacity: 0.6;
            pointer-events: none;
        }

        .nav-link[data-permission].allowed {
            opacity: 1;
            pointer-events: auto;
        }

        /* Dashboard */
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        .stats-card.success {
            border-left-color: var(--moz-green);
        }

        .stats-card.warning {
            border-left-color: var(--moz-yellow);
        }

        .stats-card.danger {
            border-left-color: var(--moz-red);
        }

        .stats-card.info {
            border-left-color: var(--moz-blue);
        }

        /* Notifications */
        .notification-badge {
            background: var(--moz-red);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            animation: pulse-notification 2s ease-in-out infinite;
        }

        .notifications-dropdown {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background: rgba(0, 150, 57, 0.05);
        }

        .notification-item.unread {
            background: rgba(0, 150, 57, 0.02);
            border-left: 3px solid var(--moz-green);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: var(--shadow-soft);
        }

        .breadcrumb-item a {
            color: var(--moz-green);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            color: var(--moz-red);
        }

        /* Alerts */
        .alert-success {
            background: linear-gradient(135deg, rgba(0, 150, 57, 0.1), rgba(0, 184, 148, 0.1));
            color: var(--moz-green);
            border: 1px solid rgba(0, 150, 57, 0.2);
            border-left: 4px solid var(--moz-green);
            border-radius: var(--border-radius);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.1), rgba(231, 76, 60, 0.1));
            color: var(--moz-red);
            border: 1px solid rgba(220, 20, 60, 0.2);
            border-left: 4px solid var(--moz-red);
            border-radius: var(--border-radius);
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--moz-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 150, 57, 0.15);
        }

        /* Table */
        .table-responsive {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
        }

        /* Status badges */
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 8px;
        }

        /* Connection status */
        .connection-status {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .connection-status.offline {
            background: var(--moz-red);
            color: white;
        }

        .connection-status.online {
            background: var(--moz-green);
            color: white;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Connection Status -->
    <div class="connection-status d-none" id="connection-status">
        <i class="fas fa-wifi me-1"></i>
        <span id="connection-text">Verificando conexão...</span>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-wrapper">
                <i class="fas fa-baby-carriage text-white fs-1"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">+</span>
            </div>
            <h4>Maternidade<span class="text-warning">+</span></h4>
            <small>Cuidado Integral Pré-Natal - MISAU 🇲🇿</small>
        </div>

        <div class="flex-grow-1 px-3 py-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" data-permission="view_dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}"
                        href="{{ route('patients.index') }}" data-permission="view_patients">
                        <i class="fas fa-venus"></i>
                        <span>Gestantes</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}"
                        href="{{ route('consultations.index') }}" data-permission="view_consultations">
                        <i class="fas fa-calendar-check"></i>
                        <span>Consultas ANC</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}"
                        href="{{ route('exams.index') }}" data-permission="view_exams">
                        <i class="fas fa-microscope"></i>
                        <span>Exames</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('laboratory.*') ? 'active' : '' }}"
                        href="{{ route('laboratory.index') }}" data-permission="view_exams">
                        <i class="fas fa-flask-vial"></i>
                        <span>Laboratório</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('vaccines.*') ? 'active' : '' }}"
                        href="{{ route('vaccines.index') }}" data-permission="view_patients">
                        <i class="fas fa-syringe"></i>
                        <span>Vacinas & IPTp</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        href="{{ route('reports.index') }}" data-permission="view_dashboard">
                        <i class="fas fa-chart-pie"></i>
                        <span>Relatórios MISAU</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('home_visits.*') ? 'active' : '' }}"
                        href="{{ route('home_visits.index') }}" data-permission="view_patients">
                        <i class="fas fa-home"></i>
                        <span>Visitas Domiciliárias</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('births.*') ? 'active' : '' }}"
                        href="{{ route('births.index') }}" data-permission="view_patients">
                        <i class="fas fa-baby"></i>
                        <span>Partos</span>
                    </a>
                </li>

                <li class="my-3">
                    <hr class="dropdown-divider border-light opacity-25">
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}" data-permission="manage_users">
                        <i class="fas fa-users-gear"></i>
                        <span>Usuários</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-gear"></i>
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}" data-permission="manage_users">
                        <i class="fas fa-sliders"></i>
                        <span>Configurações</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('help.*') ? 'active' : '' }}"
                        href="{{ route('help.index') }}">
                        <i class="fas fa-circle-question"></i>
                        <span>Ajuda</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-area">
            <div class="d-flex align-items-center text-white mb-3">
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
                    <small class="opacity-75">{{ auth()->user()->getRoleNames()->first() ?? 'Usuário' }}</small>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sign-out-alt me-2"></i> Sair do Sistema
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid px-4">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-brand mb-0 h1 fw-bold text-primary d-flex align-items-center">
                    <i class="fas @yield('title-icon', 'fa-home') me-2"></i>
                    @yield('page-title', 'Dashboard')
                </div>

                <div class="ms-auto d-flex align-items-center">
                    <!-- Search Desktop -->
                    <div class="desktop-search me-3">
                        <div class="search-container">
                            <input class="form-control form-control-sm" type="search" id="patient-search"
                                placeholder="Pesquisar gestantes..." aria-label="Search" autocomplete="off">
                            <div class="search-results" id="search-results"></div>
                        </div>
                    </div>

                    <!-- Data e Localização -->
                    <div class="me-4 text-end d-none d-md-block">
                        <div class="text-muted small" id="current-date"></div>
                        <div class="text-primary small fw-semibold">
                            <i class="fas fa-map-marker-alt me-1"></i> Quelimane, Moçambique 🇲🇿
                        </div>
                    </div>

                    <!-- Notificações -->
                    <div class="dropdown me-3">
                        <button class="btn btn-light position-relative rounded-circle p-2" type="button"
                            data-bs-toggle="dropdown" id="notifications-toggle">
                            <i class="fas fa-bell text-muted"></i>
                            <span class="notification-badge position-absolute top-0 start-100 translate-middle"
                                id="notification-count" style="display: none;">0</span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end notifications-dropdown">
                            <div
                                class="dropdown-header fw-bold text-primary d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-bell me-2"></i>Notificações</span>
                                <button class="btn btn-sm btn-link text-muted p-0" id="mark-all-read">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div id="notifications-list">
                                <div class="notification-item text-center py-3 text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Carregando notificações...
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="text-center p-2">
                                <a href="#" class="btn btn-sm btn-primary w-100" id="view-all-notifications">
                                    <i class="fas fa-eye me-1"></i>Ver Todas
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-0" type="button" data-bs-toggle="dropdown">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header text-primary fw-bold">
                                <i class="fas fa-user-md me-2"></i>{{ explode(' ', auth()->user()->name)[0] }}
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
                                    <i class="fas fa-moon me-2 text-muted" id="theme-icon"></i>
                                    <span id="theme-text">Modo Escuro</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
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

        <!-- Search Mobile -->
        <div class="mobile-search container-fluid px-4 py-2 bg-light border-bottom">
            <div class="search-container">
                <input class="form-control" type="search" id="patient-search-mobile"
                    placeholder="Pesquisar gestantes..." aria-label="Search Mobile">
                <div class="search-results" id="search-results-mobile"></div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid px-4 py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb px-3 py-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    @yield('breadcrumbs')
                    @if (!View::hasSection('breadcrumbs'))
                        <li class="breadcrumb-item active">Dashboard</li>
                    @endif
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
            @yield('content')

            <!-- Footer -->
            <footer class="text-center py-4 mt-5"
                style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: var(--border-radius);">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-md-start">
                            <small class="text-muted">
                                © {{ date('Y') }} Maternidade+ | Sistema para o <strong>MISAU</strong> 🇲🇿
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <small>
                                Versão 2.0.0 |
                                <a href="#" class="text-primary text-decoration-none">Suporte</a> |
                                <a href="#" class="text-primary text-decoration-none">Documentação</a>
                            </small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== CONFIGURAÇÃO GLOBAL =====
        const APP_CONFIG = {
            routes: {
                patientsSearch: "{{ route('patients.search') ?? '/patients/search' }}",
                notificationsIndex: "{{ route('notifications.index') ?? '/notifications' }}",
                notificationsUnreadCount: "{{ route('notifications.unread-count') ?? '/notifications/unread-count' }}",
                notificationsMarkAllRead: "{{ route('notifications.mark-all-read') ?? '/notifications/mark-all-read' }}"
            },
            user: {
                permissions: @json(auth()->user()->getAllPermissions()->pluck('name') ?? [])
            }
        };

        // ===== SIDEBAR MANAGER =====
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.mainContent = document.getElementById('main-content');
                this.overlay = document.getElementById('sidebar-overlay');
                this.toggle = document.getElementById('sidebar-toggle');
                this.isDesktop = window.innerWidth >= 992;
                this.isOpen = false;

                this.init();
            }

            init() {
                this.setupEventListeners();
                this.handleResize();
                window.addEventListener('resize', () => this.handleResize());
            }

            setupEventListeners() {
                // Toggle button
                this.toggle?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleSidebar();
                    this.addRippleEffect(e);
                });

                // Overlay click
                this.overlay?.addEventListener('click', () => {
                    this.closeSidebar();
                });

                // ESC key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isOpen && !this.isDesktop) {
                        this.closeSidebar();
                    }

                    // Atalhos de teclado (Alt + M para menu)
                    if (e.altKey && e.key === 'm') {
                        e.preventDefault();
                        this.toggleSidebar();
                    }
                });
            }

            addRippleEffect(e) {
                const rect = e.target.getBoundingClientRect();
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

                if (e.target.style.position !== 'absolute' && e.target.style.position !== 'relative') {
                    e.target.style.position = 'relative';
                }
                e.target.style.overflow = 'hidden';
                e.target.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            }

            toggleSidebar() {
                if (this.isOpen) {
                    this.closeSidebar();
                } else {
                    this.openSidebar();
                }
            }

            openSidebar() {
                if (!this.isDesktop) {
                    this.sidebar.classList.add('show');
                    this.overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    this.sidebar.classList.remove('collapsed');
                    this.mainContent.classList.remove('expanded');
                }
                this.isOpen = true;
            }

            closeSidebar() {
                if (!this.isDesktop) {
                    this.sidebar.classList.remove('show');
                    this.overlay.classList.remove('show');
                    document.body.style.overflow = '';
                } else {
                    this.sidebar.classList.add('collapsed');
                    this.mainContent.classList.add('expanded');
                }
                this.isOpen = false;
            }

            handleResize() {
                const wasDesktop = this.isDesktop;
                this.isDesktop = window.innerWidth >= 992;

                if (wasDesktop !== this.isDesktop) {
                    // Reset states when switching between mobile/desktop
                    this.sidebar.classList.remove('show', 'collapsed');
                    this.overlay.classList.remove('show');
                    this.mainContent.classList.remove('expanded');
                    document.body.style.overflow = '';
                    this.isOpen = false;
                }
            }
        }

        // ===== SEARCH MANAGER =====
        class SearchManager {
            constructor() {
                this.desktopInput = document.getElementById('patient-search');
                this.mobileInput = document.getElementById('patient-search-mobile');
                this.desktopResults = document.getElementById('search-results');
                this.mobileResults = document.getElementById('search-results-mobile');
                this.searchTimeout = null;
                this.activeInput = null;
                this.activeResults = null;

                this.init();
            }

            init() {
                this.setupSearchInputs();
                this.setupKeyboardShortcuts();
                this.setupClickOutside();
            }

            setupKeyboardShortcuts() {
                // Atalho Alt+S para focar na pesquisa
                document.addEventListener('keydown', (e) => {
                    if (e.altKey && e.key === 's') {
                        e.preventDefault();
                        const activeInput = window.innerWidth >= 992 ? this.desktopInput : this.mobileInput;
                        activeInput?.focus();
                    }
                });
            }

            setupClickOutside() {
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.search-container')) {
                        this.hideSearchResults();
                    }
                });
            }

            setupSearchInputs() {
                [this.desktopInput, this.mobileInput].forEach((input, index) => {
                    if (!input) return;

                    const results = index === 0 ? this.desktopResults : this.mobileResults;

                    input.addEventListener('input', (e) => {
                        this.activeInput = input;
                        this.activeResults = results;
                        this.handleSearch(e.target.value.trim());
                    });

                    input.addEventListener('keydown', (e) => {
                        this.activeResults = results;
                        this.handleKeyNavigation(e);
                    });

                    input.addEventListener('focus', () => {
                        this.activeInput = input;
                        this.activeResults = results;
                        input.parentElement.style.transform = 'scale(1.02)';
                        input.parentElement.style.transition = 'transform 0.2s ease';
                    });

                    input.addEventListener('blur', () => {
                        input.parentElement.style.transform = 'scale(1)';
                    });
                });
            }

            handleSearch(query) {
                clearTimeout(this.searchTimeout);

                if (query.length < 2) {
                    this.hideSearchResults();
                    return;
                }

                this.showSearchLoading();

                this.searchTimeout = setTimeout(() => {
                    this.performSearch(query);
                }, 300);
            }

            performSearch(query) {
                const url = APP_CONFIG.routes.patientsSearch;

                if (!url || url === '#') {
                    this.showSearchError('Pesquisa não configurada');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    this.showSearchError('Token CSRF não encontrado');
                    return;
                }

                fetch(`${url}?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        this.displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Erro na pesquisa:', error);
                        this.showSearchError('Erro ao pesquisar gestantes');
                    });
            }

            showSearchLoading() {
                if (!this.activeResults) return;

                this.activeResults.innerHTML = `
                    <div class="search-item text-center py-3">
                        <div class="search-loading me-2"></div>
                        Pesquisando gestantes...
                    </div>
                `;
                this.activeResults.style.display = 'block';
            }

            showSearchError(message) {
                if (!this.activeResults) return;

                this.activeResults.innerHTML = `
                    <div class="search-item text-center py-3 text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                `;
            }

            displaySearchResults(patients) {
                if (!this.activeResults) return;

                if (!Array.isArray(patients) || patients.length === 0) {
                    this.activeResults.innerHTML = `
            <div class="search-item text-center py-3 text-muted">
                <i class="fas fa-search me-2"></i>
                Nenhuma gestante encontrada
            </div>
        `;
                } else {
                    const html = patients.map(patient => {
                        // Validação adicional para o nome
                        const nome = patient.nome_completo ||
                            (patient.first_name && patient.last_name ?
                                `${patient.first_name} ${patient.last_name}` : 'Nome não disponível');

                        return `
                <div class="search-item" data-url="${patient.url || '#'}">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-venus text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">${nome}</div>
                            <small class="text-muted">
                                BI: ${patient.documento_bi || 'N/A'} | 
                                ${this.calculateAge(patient.data_nascimento)} anos | 
                                ${patient.contacto || 'N/A'}
                            </small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">${patient.semanas_gestacao || 'N/A'}ª sem</small>
                        </div>
                    </div>
                </div>
            `;
                    }).join('');

                    this.activeResults.innerHTML = html;
                    this.setupSearchItemEvents();
                }

                this.activeResults.style.display = 'block';
            }

            setupSearchItemEvents() {
                if (!this.activeResults) return;

                this.activeResults.querySelectorAll('.search-item[data-url]').forEach(item => {
                    item.addEventListener('click', () => {
                        const url = item.dataset.url;
                        if (url && url !== '#') {
                            window.location.href = url;
                        }
                    });

                    item.addEventListener('mouseenter', () => {
                        this.activeResults.querySelectorAll('.search-item').forEach(i => i.classList
                            .remove('active'));
                        item.classList.add('active');
                    });
                });
            }

            handleKeyNavigation(e) {
                if (!this.activeResults) return;

                const items = this.activeResults.querySelectorAll('.search-item[data-url]');
                const activeItem = this.activeResults.querySelector('.search-item.active');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.navigateResults(items, activeItem, 'down');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.navigateResults(items, activeItem, 'up');
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeItem?.dataset.url) {
                        window.location.href = activeItem.dataset.url;
                    }
                } else if (e.key === 'Escape') {
                    this.hideSearchResults();
                    this.activeInput?.blur();
                }
            }

            navigateResults(items, activeItem, direction) {
                if (items.length === 0) return;

                items.forEach(item => item.classList.remove('active'));

                let nextItem;
                if (!activeItem) {
                    nextItem = direction === 'down' ? items[0] : items[items.length - 1];
                } else {
                    const currentIndex = Array.from(items).indexOf(activeItem);
                    if (direction === 'down') {
                        nextItem = items[currentIndex + 1] || items[0];
                    } else {
                        nextItem = items[currentIndex - 1] || items[items.length - 1];
                    }
                }

                if (nextItem) {
                    nextItem.classList.add('active');
                    nextItem.scrollIntoView({
                        block: 'nearest'
                    });
                }
            }

            hideSearchResults() {
                [this.desktopResults, this.mobileResults].forEach(results => {
                    if (results) {
                        results.style.display = 'none';
                        results.querySelectorAll('.search-item').forEach(item => {
                            item.classList.remove('active');
                        });
                    }
                });
            }

            calculateAge(birthDate) {
                if (!birthDate) return 'N/A';
                const today = new Date();
                const birth = new Date(birthDate);
                const age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                return monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate()) ? age - 1 : age;
            }
        }

        // ===== NOTIFICATION MANAGER =====
        class NotificationManager {
            constructor() {
                this.container = document.getElementById('notifications-list');
                this.badge = document.getElementById('notification-count');
                this.markAllBtn = document.getElementById('mark-all-read');
                this.notifications = [];
                this.unreadCount = 0;

                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadNotifications();
                this.startPolling();
                this.setupKeyboardShortcuts();
            }

            setupKeyboardShortcuts() {
                // Atalho Alt+N para notificações
                document.addEventListener('keydown', (e) => {
                    if (e.altKey && e.key === 'n') {
                        e.preventDefault();
                        document.getElementById('notifications-toggle')?.click();
                    }
                });
            }

            setupEventListeners() {
                this.markAllBtn?.addEventListener('click', () => {
                    this.markAllAsRead();
                });

                // Auto-update when dropdown opens
                document.getElementById('notifications-toggle')?.addEventListener('click', () => {
                    this.loadNotifications();
                });
            }

            async loadNotifications() {
                try {
                    this.showLoading();

                    // Simular carregamento - substitua pela sua API real
                    setTimeout(() => {
                        this.simulateNotifications();
                    }, 1000);

                } catch (error) {
                    console.error('Erro ao carregar notificações:', error);
                    this.showError();
                }
            }

            simulateNotifications() {
                const mockNotifications = [{
                        id: 1,
                        type: 'consultation',
                        title: 'Consulta ANC Agendada',
                        message: 'Maria Silva - 8º contacto hoje às 14:30',
                        icon: 'calendar-check',
                        color: 'success',
                        time: '5 min atrás',
                        unread: true,
                        url: '/consultations/1'
                    },
                    {
                        id: 2,
                        type: 'exam',
                        title: 'Resultado HIV Disponível',
                        message: 'Ana Joaquim - Resultado negativo confirmado',
                        icon: 'flask',
                        color: 'info',
                        time: '15 min atrás',
                        unread: true,
                        url: '/exams/2'
                    },
                    {
                        id: 3,
                        type: 'vaccine',
                        title: 'IPTp Dose Vencida',
                        message: 'Verificar doses de malária - 3 gestantes',
                        icon: 'syringe',
                        color: 'warning',
                        time: '1 hora atrás',
                        unread: false,
                        url: '/vaccines'
                    }
                ];

                this.notifications = mockNotifications;
                this.unreadCount = mockNotifications.filter(n => n.unread).length;
                this.renderNotifications();
                this.updateBadge();
            }

            showLoading() {
                if (this.container) {
                    this.container.innerHTML = `
                        <div class="notification-item text-center py-3">
                            <div class="search-loading me-2"></div>
                            Carregando notificações...
                        </div>
                    `;
                }
            }

            showError() {
                if (this.container) {
                    this.container.innerHTML = `
                        <div class="notification-item text-center py-3 text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Erro ao carregar notificações
                        </div>
                    `;
                }
            }

            renderNotifications() {
                if (!this.container) return;

                if (this.notifications.length === 0) {
                    this.container.innerHTML = `
                        <div class="notification-item text-center py-4 text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <div>Nenhuma notificação</div>
                        </div>
                    `;
                    return;
                }

                const html = this.notifications.map(notification => `
                    <div class="notification-item ${notification.unread ? 'unread' : ''}" 
                         data-id="${notification.id}"
                         data-url="${notification.url}">
                        <div class="d-flex align-items-start">
                            <div class="me-3 text-${notification.color}">
                                <i class="fas fa-${notification.icon}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${notification.title}</div>
                                <div class="small text-muted">${notification.message}</div>
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-clock me-1"></i>${notification.time}
                                </div>
                            </div>
                            ${notification.unread ? '<div class="badge bg-primary">Nova</div>' : ''}
                        </div>
                    </div>
                `).join('');

                this.container.innerHTML = html;
                this.setupNotificationEvents();
            }

            setupNotificationEvents() {
                this.container.querySelectorAll('.notification-item[data-url]').forEach(item => {
                    item.addEventListener('click', () => {
                        const url = item.dataset.url;
                        const id = item.dataset.id;

                        if (item.classList.contains('unread')) {
                            this.markAsRead(id);
                        }

                        if (url && url !== '#') {
                            window.location.href = url;
                        }
                    });
                });
            }

            updateBadge() {
                if (this.badge) {
                    if (this.unreadCount > 0) {
                        this.badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                        this.badge.style.display = 'block';
                    } else {
                        this.badge.style.display = 'none';
                    }
                }
            }

            async markAsRead(notificationId) {
                try {
                    const notification = this.notifications.find(n => n.id == notificationId);
                    if (notification && notification.unread) {
                        notification.unread = false;
                        this.unreadCount--;
                        this.updateBadge();
                        this.renderNotifications();
                    }
                } catch (error) {
                    console.error('Erro ao marcar como lida:', error);
                }
            }

            async markAllAsRead() {
                try {
                    this.notifications.forEach(n => n.unread = false);
                    this.unreadCount = 0;
                    this.updateBadge();
                    this.renderNotifications();

                    showToast('Todas as notificações foram marcadas como lidas', 'success');
                } catch (error) {
                    console.error('Erro ao marcar todas como lidas:', error);
                    showToast('Erro ao marcar notificações como lidas', 'danger');
                }
            }

            startPolling() {
                setInterval(() => {
                    this.checkForNewNotifications();
                }, 30000);
            }

            async checkForNewNotifications() {
                try {
                    // Simulação ocasional de nova notificação
                    if (Math.random() > 0.95) {
                        this.addNewNotification({
                            id: Date.now(),
                            type: 'system',
                            title: 'Sistema Atualizado',
                            message: 'Nova funcionalidade disponível',
                            icon: 'info-circle',
                            color: 'info',
                            time: 'Agora',
                            unread: true,
                            url: '#'
                        });
                    }
                } catch (error) {
                    console.error('Erro ao verificar novas notificações:', error);
                }
            }

            addNewNotification(notification) {
                this.notifications.unshift(notification);
                this.unreadCount++;
                this.updateBadge();

                showToast(`Nova notificação: ${notification.title}`, 'info');

                if (this.notifications.length > 20) {
                    this.notifications = this.notifications.slice(0, 20);
                }
            }
        }

        // ===== PERMISSION MANAGER =====
        class PermissionManager {
            constructor() {
                this.userPermissions = APP_CONFIG.user.permissions;
                this.init();
            }

            init() {
                this.applyPermissions();
            }

            applyPermissions() {
                document.querySelectorAll('[data-permission]').forEach(element => {
                    const requiredPermission = element.getAttribute('data-permission');

                    if (this.userPermissions.includes(requiredPermission)) {
                        element.classList.add('allowed');
                    } else {
                        element.style.display = 'none';
                    }
                });
            }

            hasPermission(permission) {
                return this.userPermissions.includes(permission);
            }
        }

        // ===== NETWORK MONITOR =====
        class NetworkMonitor {
            constructor() {
                this.statusElement = document.getElementById('connection-status');
                this.textElement = document.getElementById('connection-text');
                this.isOnline = navigator.onLine;

                this.init();
            }

            init() {
                this.updateStatus();

                window.addEventListener('online', () => {
                    this.isOnline = true;
                    this.updateStatus();
                });

                window.addEventListener('offline', () => {
                    this.isOnline = false;
                    this.updateStatus();
                });
            }

            updateStatus() {
                if (!this.statusElement || !this.textElement) return;

                if (this.isOnline) {
                    this.statusElement.className = 'connection-status online';
                    this.textElement.textContent = 'Online - Sincronizado';
                    this.statusElement.innerHTML = '<i class="fas fa-wifi me-1"></i>' + this.textElement.outerHTML;

                    setTimeout(() => {
                        this.statusElement.classList.add('d-none');
                    }, 3000);
                } else {
                    this.statusElement.className = 'connection-status offline';
                    this.textElement.textContent = 'Offline - Modo local ativado';
                    this.statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>' + this.textElement
                        .outerHTML;
                    this.statusElement.classList.remove('d-none');
                }
            }
        }

        // ===== THEME TOGGLE =====
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            if (themeIcon) {
                themeIcon.className = newTheme === 'dark' ? 'fas fa-sun me-2 text-muted' : 'fas fa-moon me-2 text-muted';
            }

            if (themeText) {
                themeText.textContent = newTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }
        }

        // ===== DATE AND TIME =====
        function updateDateTime() {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = new Date().toLocaleDateString('pt-MZ', options);
            }
        }

        // ===== TOAST HELPER =====
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return;

            const toastId = 'toast-' + Date.now();
            const icon = type === 'success' ? 'check-circle' :
                type === 'error' || type === 'danger' ? 'exclamation-circle' :
                type === 'warning' ? 'exclamation-triangle' : 'info-circle';

            const colorClass = type === 'success' ? 'bg-success' :
                type === 'error' || type === 'danger' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toastHtml = `
                <div class="toast fade-in ${colorClass} text-white" role="alert" id="${toastId}">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${icon} me-2"></i>
                        <span class="flex-grow-1">${message}</span>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });

            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // ===== RIPPLE EFFECT =====
        function setupRippleEffect() {
            document.querySelectorAll('.btn, button').forEach(button => {
                button.addEventListener('click', function(e) {
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
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar tema salvo
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);

            // Atualizar ícone do tema
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            if (themeIcon) {
                themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun me-2 text-muted' :
                    'fas fa-moon me-2 text-muted';
            }

            if (themeText) {
                themeText.textContent = savedTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }

            // Inicializar componentes
            try {
                new SidebarManager();
                new SearchManager();
                new NotificationManager();
                new PermissionManager();
                new NetworkMonitor();
            } catch (error) {
                console.error('Erro ao inicializar componentes:', error);
            }

            // Configurar efeitos
            setupRippleEffect();

            // Atualizar data/hora
            updateDateTime();
            setInterval(updateDateTime, 60000);

            // Bootstrap components
            try {
                // Tooltips e Popovers
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            } catch (error) {
                console.warn('Erro ao inicializar tooltips/popovers:', error);
            }

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert.fade.show').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        setTimeout(() => bsAlert.close(), 5000);
                    }
                });
            }, 100);

            // Log de inicialização (apenas em desenvolvimento)
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                console.log('%c🇲🇿 Maternidade+ Sistema carregado com sucesso!',
                    'color: #009639; font-weight: bold; font-size: 16px;');
                console.log('%c💡 Atalhos disponíveis:', 'color: #0080C7; font-weight: bold;');
                console.log('   Alt + M = Toggle menu');
                console.log('   Alt + S = Focus pesquisa');
                console.log('   Alt + N = Abrir notificações');
                console.log('   ESC = Fechar sidebar/modals');
            }
        });

        // ===== ERROR HANDLING =====
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript:', e.error);

            // Mostrar toast apenas em desenvolvimento
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                showToast('Erro no JavaScript. Verifique o console.', 'error');
            }
        });

        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promise rejeitada:', e.reason);
        });

        // Expor funções globais
        window.toggleTheme = toggleTheme;
        window.showToast = showToast;
        window.updateDateTime = updateDateTime;

        // ===== PERFORMANCE MONITORING =====
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.timing;
                    const loadTime = perfData.loadEventEnd - perfData.navigationStart;

                    if (loadTime > 3000) {
                        console.warn(`⚠️ Página demorou ${loadTime}ms para carregar`);
                    } else {
                        console.log(`✅ Página carregada em ${loadTime}ms`);
                    }
                }, 0);
            });
        }

        // ===== SERVICE WORKER (OPCIONAL) =====
        if ('serviceWorker' in navigator && 'production' === 'production') {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registrado com sucesso:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('Falha ao registrar SW:', error);
                    });
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
