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
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-0 bg-primary"
        style="width: 280px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1);">
        <div class="sidebar-brand p-4 text-center bg-primary-dark" style="background-color: rgba(0,0,0,0.1);">
            <div class="position-relative d-inline-block">
                <i class="fas fa-baby-carriage text-white fs-1"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    +
                </span>
            </div>
            <h4 class="text-white mt-3 mb-0 fw-bold">Maternidade<span class="text-warning">+</span></h4>
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
                {!! menu_item('consultations.index', 'fa-calendar-check', 'Consultas') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('exams.index', 'fa-microscope', 'Exames') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('laboratory.index', 'fa-flask-vial', 'Laboratório') !!}
            </li>
            <li class="nav-item mb-2">
                {!! menu_item('reports.index', 'fa-chart-pie', 'Relatórios') !!}
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

        <div class="user-area p-3 bg-primary-dark" style="background-color: rgba(0,0,0,0.1);">
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
                        <input class="form-control form-control-sm" type="search" placeholder="Pesquisar..."
                            aria-label="Search">
                        <button class="btn btn-outline-secondary btn-sm ms-2" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div class="me-4 text-end d-none d-md-block">
                        <div class="text-muted small">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>
                        <div class="text-primary small fw-semibold">
                            <i class="fas fa-map-marker-alt me-1"></i> Quelimane, Moçambique
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light position-relative rounded-circle p-2" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-bell text-muted"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle  badge rounded-pill py-0.5 px-2 bg-danger" style="top:-4 px; ">
                                3
                                <span class="visually-hidden">notificações não lidas</span>
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" style="width: 300px;">
                            <li class="dropdown-header fw-bold text-primary">Notificações</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-success">
                                        <i class="fas fa-calendar-check fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Nova consulta agendada</div>
                                        <small class="text-muted">Há 15 minutos</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-warning">
                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Resultado de exame pendente</div>
                                        <small class="text-muted">Há 2 horas</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex py-2" href="#">
                                    <div class="me-3 text-info">
                                        <i class="fas fa-user-plus fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Nova gestante registada</div>
                                        <small class="text-muted">Hoje às 09:30</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-center text-primary small fw-semibold" href="#">
                                    Ver todas as notificações
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
                                Olá, {{ explode(' ', auth()->user()->name)[0] }}
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
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-moon me-2 text-muted"></i>Modo Escuro
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2 text-muted"></i>Sair
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
                <ol class="breadcrumb bg-transparent px-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i
                                class="fas fa-home"></i></a></li>
                    {!! View::getSection('breadcrumbs') ?? '<li class="breadcrumb-item active">Dashboard</li>' !!}
                </ol>
            </nav>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2 fa-lg"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('d-none');
            document.querySelector('.sidebar').classList.toggle('d-flex');
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 8000);

        // Dark mode toggle
        document.querySelector('[data-bs-theme-value]')?.addEventListener('click', function() {
            const theme = this.getAttribute('data-bs-theme-value');
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
        });

        // Initialize theme from localStorage
        if (localStorage.getItem('theme')) {
            document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('theme'));
        }
    </script>

    @stack('scripts')
</body>

</html>
