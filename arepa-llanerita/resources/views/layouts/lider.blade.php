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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        :root {
            --primary-color: #722F37;
            --primary-dark: #5a252a;
            --primary-light: #8b3c44;
            --primary-lighter: #a44950;
            --secondary-color: #ffffff;
            --text-dark: #2c2c2c;
            --text-muted: #6c757d;
            --border-color: #e5e7eb;
            --hover-bg: #f8f9fa;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin: 0;
            min-height: 100vh;
        }

        /* Layout principal */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: var(--shadow-lg);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(-280px);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--secondary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand i {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-section {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1.5rem;
        }

        .nav-section:first-child {
            margin-top: 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--secondary-color);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: var(--secondary-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.125rem;
        }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Header */
        .main-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        /* Notifications */
        .notification-dropdown {
            position: relative;
        }

        .notification-btn {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .notification-btn:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            padding: 0.125rem 0.375rem;
            border-radius: 0.75rem;
            min-width: 1.25rem;
            text-align: center;
            line-height: 1;
        }

        /* Profile dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }

        .profile-btn:hover {
            background: var(--hover-bg);
        }

        .profile-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .profile-info {
            text-align: left;
        }

        .profile-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Dropdown menus */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            min-width: 200px;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        /* Content area */
        .content-area {
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(var(--primary-color), 0.02);
        }

        .card-body {
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-header {
                padding: 1rem;
            }

            .header-right .profile-info {
                display: none;
            }
        }

        /* Utilities */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i class="bi bi-person-badge"></i>
                    <span>Dashboard Líder</span>
                </a>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">Panel Principal</div>
                <div class="nav-item">
                    <a href="{{ route('lider.dashboard') }}" class="nav-link {{ request()->routeIs('lider.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-section">Mi Equipo</div>
                <div class="nav-item">
                    <a href="{{ route('lider.equipo.index') }}" class="nav-link {{ request()->routeIs('lider.equipo.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Gestión de Equipo</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('lider.referidos.index') }}" class="nav-link {{ request()->routeIs('lider.referidos.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>Red de Referidos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('lider.rendimiento.index') }}" class="nav-link {{ request()->routeIs('lider.rendimiento.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Rendimiento</span>
                    </a>
                </div>

                <div class="nav-section">Ventas & Comisiones</div>
                <div class="nav-item">
                    <a href="{{ route('lider.ventas.index') }}" class="nav-link {{ request()->routeIs('lider.ventas.*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i>
                        <span>Ventas del Equipo</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('lider.comisiones.index') }}" class="nav-link {{ request()->routeIs('lider.comisiones.*') ? 'active' : '' }}">
                        <i class="bi bi-currency-dollar"></i>
                        <span>Mis Comisiones</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('lider.metas.index') }}" class="nav-link {{ request()->routeIs('lider.metas.*') ? 'active' : '' }}">
                        <i class="bi bi-target"></i>
                        <span>Metas y Objetivos</span>
                    </a>
                </div>

                <div class="nav-section">Reportes</div>
                <div class="nav-item">
                    <a href="{{ route('lider.reportes.ventas') }}" class="nav-link {{ request()->routeIs('lider.reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i>
                        <span>Reportes</span>
                    </a>
                </div>

                <div class="nav-section">Configuración</div>
                <div class="nav-item">
                    <a href="{{ route('lider.perfil.index') }}" class="nav-link {{ request()->routeIs('lider.perfil.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Mi Perfil</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="header-right">
                    <!-- Notifications -->
                    <div class="notification-dropdown">
                        <button class="notification-btn" id="notificationToggle">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="dropdown-menu" id="notificationMenu">
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-info-circle text-primary"></i>
                                <div>
                                    <div class="fw-semibold">Nueva venta registrada</div>
                                    <small class="text-muted">Hace 5 minutos</small>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-person-plus text-success"></i>
                                <div>
                                    <div class="fw-semibold">Nuevo miembro en tu equipo</div>
                                    <small class="text-muted">Hace 1 hora</small>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-target text-warning"></i>
                                <div>
                                    <div class="fw-semibold">Meta mensual alcanzada</div>
                                    <small class="text-muted">Hace 2 horas</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="profile-dropdown">
                        <button class="profile-btn" id="profileToggle">
                            <div class="profile-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="profile-info">
                                <div class="profile-name">{{ auth()->user()->name }}</div>
                                <div class="profile-role">{{ ucfirst(auth()->user()->rol) }}</div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" id="profileMenu">
                            <a href="{{ route('lider.perfil.index') }}" class="dropdown-item">
                                <i class="bi bi-person"></i>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="{{ route('lider.configuracion.index') }}" class="dropdown-item">
                                <i class="bi bi-gear"></i>
                                <span>Configuración</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-question-circle"></i>
                                <span>Ayuda</span>
                            </a>
                            <hr class="my-2">
                            <a href="{{ route('logout') }}" class="dropdown-item"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Livewire Toast Notifications -->
    @livewire('toast-notifications')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            // Dropdown toggles
            function setupDropdown(toggleId, menuId) {
                const toggle = document.getElementById(toggleId);
                const menu = document.getElementById(menuId);

                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Close other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                        if (m !== menu) m.classList.remove('show');
                    });
                    menu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    menu.classList.remove('show');
                });
            }

            setupDropdown('notificationToggle', 'notificationMenu');
            setupDropdown('profileToggle', 'profileMenu');

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Handle mobile sidebar
            if (window.innerWidth <= 768) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>