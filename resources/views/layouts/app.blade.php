<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Maternidade+') }} - @yield('title', 'Sistema de Gestão Pré-Natal')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Sidebar -->
        <nav class="sidebar d-flex flex-column p-0 bg-dark" style="width: 250px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000;">
            <div class="sidebar-brand p-3 text-center">
                <i class="fas fa-baby text-white fs-2"></i>
                <h4 class="text-white mt-2 mb-0">Maternidade<span class="text-warning">+</span></h4>
                <small class="text-light">Sistema Pré-Natal</small>
            </div>
            
            <ul class="nav nav-pills flex-column px-2">
                <li class="nav-item mb-1">
                    <a href="{{ route('dashboard') }}" class="nav-link text-light {{ request()->routeIs('dashboard') ? 'active bg-primary' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('patients.index') }}" class="nav-link text-light {{ request()->routeIs('patients.*') ? 'active bg-primary' : '' }}">
                        <i class="fas fa-female me-2"></i> Gestantes
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('consultations.index') }}" class="nav-link text-light {{ request()->routeIs('consultations.*') ? 'active bg-primary' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i> Consultas
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('exams.index') }}" class="nav-link text-light {{ request()->routeIs('exams.*') ? 'active bg-primary' : '' }}">
                        <i class="fas fa-flask me-2"></i> Exames
                    </a>
                </li>
            </ul>
            
            <div class="mt-auto p-3">
                <div class="d-flex align-items-center text-light">
                    <i class="fas fa-user-md me-2"></i>
                    <div>
                        <small>{{ auth()->user()->name }}</small><br>
                        <small class="text-muted">{{ auth()->user()->getRoleNames()->first() }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content" style="margin-left: 250px;">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="navbar-brand mb-0 h1">
                        @yield('page-title', 'Dashboard')
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="me-3">
                            <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small><br>
                            <small class="text-muted">Maputo, Moçambique</small>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-cog me-2"></i>Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>
