<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Arepa la Llanerita') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-color: #722F37;
            --primary-dark: #5a252a;
            --primary-light: #8b3c44;
            --secondary-color: #ffffff;
            --text-dark: #2c2c2c;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --hover-bg: #f8f9fa;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .lider-sidebar {
            width: 260px;
            height: 100vh;
            background: var(--secondary-color);
            border-right: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        .lider-sidebar.collapsed {
            transform: translateX(-260px);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--primary-color);
            color: var(--secondary-color);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--secondary-color);
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .nav-section {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1rem;
        }

        .nav-section:first-child {
            margin-top: 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 0;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .nav-link.active {
            background-color: rgba(114, 47, 55, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 500;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .dropdown-toggle::after {
            margin-left: auto;
            transition: transform 0.2s ease;
        }

        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
            margin: 0;
            position: static;
        }

        .dropdown-item {
            padding: 0.5rem 1rem 0.5rem 3rem;
            color: var(--text-muted);
            font-size: 0.875rem;
            border-left: 3px solid transparent;
        }

        .dropdown-item:hover {
            background-color: var(--hover-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        /* Header Styles */
        .lider-header {
            height: 70px;
            background: var(--secondary-color);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 1060;
            transition: left 0.3s ease;
        }

        .lider-header.expanded {
            left: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 1.5rem;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            margin-right: 1rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        .sidebar-toggle:hover {
            background-color: var(--hover-bg);
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-notifications {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        .header-notifications:hover {
            background-color: var(--hover-bg);
        }

        .notification-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background: #dc3545;
            color: white;
            font-size: 0.6rem;
            padding: 0.125rem 0.375rem;
            border-radius: 0.75rem;
            min-width: 1.25rem;
            text-align: center;
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        .header-profile:hover {
            background-color: var(--hover-bg);
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.2;
        }

        /* Main Content */
        .lider-main {
            margin-left: 260px;
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .lider-main.expanded {
            margin-left: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .lider-sidebar {
                transform: translateX(-260px);
            }

            .lider-sidebar.show {
                transform: translateX(0);
            }

            .lider-header {
                left: 0;
            }

            .lider-main {
                margin-left: 0;
                padding: 1rem;
            }

            .profile-info {
                display: none;
            }

            .header-content {
                padding: 0 1rem;
            }

            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        @media (max-width: 575.98px) {
            .lider-main {
                padding: 0.75rem;
            }

            .card-body {
                padding: 1rem !important;
            }

            .header-content {
                padding: 0 0.75rem;
            }

            .profile-name {
                display: none;
            }

            .notification-badge {
                font-size: 0.5rem;
                padding: 0.1rem 0.3rem;
            }
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 2;
        }

        .card-header {
            background: var(--secondary-color);
            border-bottom: 1px solid var(--border-color);
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Asegurar que las badges no interfieran con dropdowns */
        .badge {
            position: relative;
            z-index: 1 !important;
        }

        /* Badges específicas del header pueden tener prioridad intermedia */
        .header-notifications .badge,
        .notification-badge {
            z-index: 50 !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            z-index: 1070 !important;
            position: absolute !important;
        }

        /* Específico para dropdowns del header */
        .lider-header .dropdown-menu {
            z-index: 1080 !important;
        }

        /* Bootstrap dropdown fix */
        .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* Forzar z-index para dropdowns activos */
        .dropdown.show .dropdown-menu {
            z-index: 1090 !important;
        }

        .lider-header .dropdown.show .dropdown-menu {
            z-index: 1100 !important;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="lider-sidebar" id="liderSidebar">
        <div class="sidebar-header">
            <a href="{{ route('lider.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-person-badge me-2"></i>
                Arepa la Llanerita
            </a>
        </div>

        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">Dashboard</div>
            <div class="nav-item">
                <a href="{{ route('lider.dashboard') }}" class="nav-link {{ request()->routeIs('lider.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Panel Principal
                </a>
            </div>

            <!-- Mi Equipo -->
            <div class="nav-section">Mi Equipo</div>

            <div class="nav-item">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#equipoSubmenu" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    Gestión de Equipo
                </a>
                <div class="collapse" id="equipoSubmenu">
                    <a href="{{ route('lider.equipo.index') }}" class="dropdown-item">
                        <i class="bi bi-list"></i>
                        Lista del Equipo
                    </a>
                    <a href="{{ route('lider.rendimiento.index') }}" class="dropdown-item">
                        <i class="bi bi-graph-up"></i>
                        Rendimiento
                    </a>
                    <a href="{{ route('lider.capacitacion.index') }}" class="dropdown-item">
                        <i class="bi bi-book"></i>
                        Capacitación
                    </a>
                </div>
            </div>

            <div class="nav-item">
                <a href="{{ route('lider.referidos.index') }}" class="nav-link {{ request()->routeIs('lider.referidos.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    Red de Referidos
                </a>
            </div>

            <!-- Ventas y Comisiones -->
            <div class="nav-section">Ventas y Comisiones</div>

            <div class="nav-item">
                <a href="{{ route('lider.ventas.index') }}" class="nav-link {{ request()->routeIs('lider.ventas.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i>
                    Ventas del Equipo
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('lider.comisiones.index') }}" class="nav-link {{ request()->routeIs('lider.comisiones.*') ? 'active' : '' }}">
                    <i class="bi bi-currency-dollar"></i>
                    Mis Comisiones
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('lider.metas.index') }}" class="nav-link {{ request()->routeIs('lider.metas.*') ? 'active' : '' }}">
                    <i class="bi bi-target"></i>
                    Metas y Objetivos
                </a>
            </div>

            <!-- Reportes -->
            <div class="nav-section">Reportes</div>

            <div class="nav-item">
                <a href="{{ route('lider.reportes.ventas') }}" class="nav-link {{ request()->routeIs('lider.reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    Reportes de Ventas
                </a>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link" onclick="showComingSoon('Dashboard Analítico')">
                    <i class="bi bi-pie-chart"></i>
                    Dashboard Analítico
                </a>
            </div>

            <!-- Configuración -->
            <div class="nav-section">Configuración</div>

            <div class="nav-item">
                <a href="{{ route('lider.perfil.index') }}" class="nav-link {{ request()->routeIs('lider.perfil.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    Mi Perfil
                </a>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link" onclick="showComingSoon('Notificaciones')">
                    <i class="bi bi-bell-fill"></i>
                    Notificaciones
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="lider-header" id="liderHeader">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="header-right">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="header-notifications" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 320px; z-index: 1090 !important;">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="mb-0">Notificaciones</h6>
                            <small class="text-muted">5 nuevas</small>
                        </div>
                        <div class="p-3">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <div class="fw-medium small">Nueva venta registrada</div>
                                    <small class="text-muted">Tu equipo ha registrado una nueva venta - hace 5 min</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <div class="fw-medium small">Meta mensual alcanzada</div>
                                    <small class="text-muted">Has alcanzado el 85% de tu meta - hace 2 horas</small>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Centro de Notificaciones')">Ver todas</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="dropdown">
                    <div class="header-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name">{{ Auth::user()->name }}</div>
                            <div class="profile-role">{{ ucfirst(Auth::user()->rol) }}</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1090 !important;">
                        <li>
                            <h6 class="dropdown-header">
                                {{ Auth::user()->name }}
                                <small class="text-muted d-block">{{ Auth::user()->email }}</small>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('lider.perfil.index') }}">
                                <i class="bi bi-person me-2"></i>
                                Mi Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('lider.configuracion.index') }}">
                                <i class="bi bi-gear me-2"></i>
                                Configuración
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="showComingSoon('Ayuda')">
                                <i class="bi bi-question-circle me-2"></i>
                                Ayuda
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="lider-main" id="liderMain">
        @yield('content')
    </main>

    @livewireScripts
    @livewire('toast-notifications')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('liderSidebar');
            const header = document.getElementById('liderHeader');
            const main = document.getElementById('liderMain');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar
            toggle.addEventListener('click', function() {
                if (window.innerWidth <= 991.98) {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                } else {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                    header.classList.toggle('expanded');
                    main.classList.toggle('expanded');
                }
            });

            // Close sidebar when clicking overlay
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991.98) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });

            // Initialize dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                }
                return null;
            }).filter(Boolean);
        });

        // Coming soon function
        function showComingSoon(feature) {
            alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
        }
    </script>

    @stack('scripts')
</body>
</html>