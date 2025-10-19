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

    <!-- CSS personalizado para mensajes -->
    <link rel="stylesheet" href="{{ asset('css/admin/messages.css') }}">

    <!-- CSS personalizado para pedidos -->
    <link rel="stylesheet" href="{{ asset('css/admin/pedidos.css') }}">

    <!-- Mobile Optimizations -->
    <link rel="stylesheet" href="{{ asset('css/mobile-optimizations.css') }}">

    <!-- Header Dropdowns CSS -->
    <link rel="stylesheet" href="{{ asset('css/header-dropdowns.css') }}?v={{ filemtime(public_path('css/header-dropdowns.css')) }}">

    <!-- Modern Sidebar Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-sidebar-global.css') }}?v={{ time() }}">

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
        .admin-sidebar {
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

        .admin-sidebar.collapsed {
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

        .sidebar .dropdown-menu {
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
        .admin-header {
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

        .admin-header.expanded {
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
            overflow: hidden;
            position: relative;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
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
        .admin-main {
            margin-left: 260px;
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .admin-main.expanded {
            margin-left: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-260px);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-header {
                left: 0;
            }

            .admin-main {
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
            .admin-main {
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
        .admin-header .dropdown-menu {
            z-index: 1080 !important;
            background-color: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175) !important;
            position: absolute !important;
        }

        /* Asegurar fondo sólido para dropdowns del header */
        .admin-header .dropdown-menu::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ffffff;
            z-index: -1;
            border-radius: 0.375rem;
        }

        /* Bootstrap dropdown fix */
        .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* Forzar z-index para dropdowns activos */
        .dropdown.show .dropdown-menu {
            z-index: 1090 !important;
        }

        .admin-header .dropdown.show .dropdown-menu {
            z-index: 1100 !important;
        }

        /* Estilos para items del dropdown del header */
        .admin-header .dropdown-item {
            color: #212529 !important;
            background-color: transparent !important;
            padding: 0.5rem 1rem !important;
        }

        .admin-header .dropdown-item:hover,
        .admin-header .dropdown-item:focus {
            background-color: #f8f9fa !important;
            color: #0d6efd !important;
        }

        .admin-header .dropdown-header {
            color: #6c757d !important;
            background-color: transparent !important;
        }

        .admin-header .dropdown-divider {
            border-top: 1px solid #dee2e6 !important;
        }

        /* Específico para el dropdown de notificaciones */
        .admin-header .dropdown-menu[style*="width: 320px"] {
            background-color: #ffffff !important;
            min-width: 320px !important;
        }

        .admin-header .notification-item {
            background-color: #ffffff !important;
            color: #212529 !important;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem !important;
        }

        .admin-header .notification-item:hover {
            background-color: #f8f9fa !important;
        }

        .admin-header .notification-item:last-child {
            border-bottom: none;
        }

        /* Contenedor global para evitar stacking context issues */
        .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Asegurar que todo el contenido del dropdown tenga fondo sólido */
        .admin-header .dropdown-menu * {
            position: relative;
            z-index: 1;
        }

        /* Forzar estilo para elementos específicos del header */
        .admin-header .dropdown-menu .border-bottom {
            background-color: #ffffff !important;
        }

        /* Estilo para texto en dropdowns del header */
        .admin-header .dropdown-menu .text-muted {
            color: #6c757d !important;
        }

        .admin-header .dropdown-menu h6 {
            color: #212529 !important;
        }

        /* Todos los elementos del contenido principal deben estar por debajo del header */
        .admin-main * {
            position: relative;
            z-index: auto;
        }

        /* Las badges del contenido específicamente deben estar muy por debajo */
        .admin-main .badge {
            z-index: 1 !important;
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

    <!-- Sidebar Moderno -->
    <nav class="modern-sidebar admin-sidebar" id="adminSidebar">
        <!-- Header del Sidebar -->
        <div class="sidebar-header">
            <div class="sidebar-brand-wrapper">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">Arepa Llanerita</span>
                        <span class="brand-subtitle">Administrador</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Info de Usuario -->
        <div class="sidebar-user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos ?? Auth::user()->name, 0, 1)) }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role admin">
                    <i class="bi bi-shield-check"></i>
                    Administrador
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <i class="bi bi-grid-fill"></i>
                <span>Panel Principal</span>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                    <span class="nav-badge">Inicio</span>
                </a>
            </div>

            <!-- Gestión -->
            <div class="nav-section">
                <i class="bi bi-gear-fill"></i>
                <span>Gestión</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Usuarios</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Roles y Permisos</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.productos.index') }}" class="nav-link {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Productos</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.pedidos.index') }}" class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check-fill"></i>
                    <span>Pedidos</span>
                    <span class="nav-icon-end">
                        <i class="bi bi-arrow-right-short"></i>
                    </span>
                </a>
            </div>

            <!-- Ventas y Comisiones -->
            <div class="nav-section">
                <i class="bi bi-cash-stack"></i>
                <span>Finanzas</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.reportes.ventas') }}" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Reportes de Ventas</span>
                    <span class="nav-icon-end">
                        <i class="bi bi-arrow-right-short"></i>
                    </span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.comisiones.index') }}" class="nav-link {{ request()->routeIs('admin.comisiones.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Comisiones</span>
                    <span class="nav-icon-end">
                        <i class="bi bi-graph-up"></i>
                    </span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.referidos.index') }}" class="nav-link {{ request()->routeIs('admin.referidos.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Red de Referidos</span>
                </a>
            </div>

            <!-- Configuración -->
            <div class="nav-section">
                <i class="bi bi-sliders"></i>
                <span>Sistema</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.configuracion.index') }}" class="nav-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-wide-connected"></i>
                    <span>Configuración</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.logs.index') }}" class="nav-link">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Logs del Sistema</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.notificaciones.index') }}" class="nav-link">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notificaciones</span>
                    <span class="nav-badge badge-warning" id="sidebarNotificationBadge" style="display: none;">0</span>
                </a>
            </div>
        </div>

        <!-- Footer del Sidebar -->
        <div class="sidebar-footer">
            <div class="footer-stats">
                <div class="stat-item admin-stat">
                    <i class="bi bi-award-fill"></i>
                    <span>Administrador Master</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Header -->
    <header class="admin-header" id="adminHeader">
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
                    <button class="header-notifications" data-bs-toggle="dropdown" aria-expanded="false" id="notificationsDropdown">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge-animated" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end header-dropdown-menu notifications-dropdown" id="notificationsDropdownMenu">
                        <!-- Header -->
                        <div class="dropdown-header-modern">
                            <h6>
                                <i class="bi bi-bell me-2"></i>
                                Notificaciones
                            </h6>
                            <div class="dropdown-header-actions">
                                <span class="notification-count-badge" id="notificationCount">0 nuevas</span>
                                <button class="btn btn-view-all" onclick="verTodasLasNotificaciones()">
                                    Ver todas
                                </button>
                            </div>
                        </div>

                        <!-- Notifications List -->
                        <div class="notifications-list" id="notificationsList">
                            <div class="notifications-empty">
                                <i class="bi bi-bell-slash"></i>
                                <h6>Sin notificaciones</h6>
                                <p>No tienes notificaciones nuevas en este momento</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="dropdown-footer">
                            <button class="btn btn-mark-all-read" onclick="marcarTodasLeidasDropdown()">
                                <i class="bi bi-check-all"></i>
                                Marcar todas como leídas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="dropdown">
                    <div class="header-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}"
                                     alt="Avatar">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="profile-info">
                            <div class="profile-name">{{ Auth::user()->name }}</div>
                            <div class="profile-role">{{ ucfirst(Auth::user()->rol) }}</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end header-dropdown-menu profile-dropdown">
                        <!-- Profile Header -->
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-avatar">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}"
                                         alt="Avatar">
                                @else
                                    <div class="avatar-initial">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="profile-dropdown-name">{{ Auth::user()->name }}</div>
                            <div class="profile-dropdown-email">{{ Auth::user()->email }}</div>
                            <span class="profile-dropdown-role">
                                <i class="bi bi-shield-check me-1"></i>
                                {{ ucfirst(Auth::user()->rol) }}
                            </span>
                        </div>

                        <!-- Stats Section -->
                        @php
                            $stats = [
                                'pedidos' => \App\Models\Pedido::count(),
                                'usuarios' => \App\Models\User::count(),
                                'ventas' => \App\Models\Pedido::sum('total_final')
                            ];
                        @endphp
                        <div class="profile-stats">
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['pedidos'] }}</span>
                                <span class="profile-stat-label">Pedidos</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $stats['usuarios'] }}</span>
                                <span class="profile-stat-label">Usuarios</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">${{ format_currency($stats['ventas']) }}</span>
                                <span class="profile-stat-label">Ventas</span>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="profile-menu-section">
                            <a href="{{ route('admin.perfil.index') }}" class="profile-menu-item">
                                <i class="bi bi-person"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                            <a href="{{ route('admin.configuracion.index') }}" class="profile-menu-item">
                                <i class="bi bi-gear"></i>
                                <span class="menu-item-text">Configuración</span>
                            </a>
                            <a href="{{ route('admin.notificaciones.index') }}" class="profile-menu-item">
                                <i class="bi bi-bell"></i>
                                <span class="menu-item-text">Notificaciones</span>
                                @php
                                    $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())
                                        ->where('leida', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="menu-item-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </div>

                        <hr class="profile-menu-divider">

                        <div class="profile-menu-section">
                            <a href="{{ route('admin.perfil.index') }}#actividad" class="profile-menu-item">
                                <i class="bi bi-clock-history"></i>
                                <span class="menu-item-text">Actividad</span>
                            </a>
                            <a href="{{ route('admin.ayuda.index') }}" class="profile-menu-item">
                                <i class="bi bi-question-circle"></i>
                                <span class="menu-item-text">Ayuda</span>
                            </a>
                        </div>

                        <hr class="profile-menu-divider">

                        <div class="profile-menu-section">
                            <a href="{{ route('logout') }}" class="profile-menu-item danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span class="menu-item-text">Cerrar Sesión</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main" id="adminMain">
        {{-- @include('admin.partials.messages-simple') --}}
        @yield('content')
    </main>

    {{-- Incluir modales de confirmación --}}
    @include('admin.partials.modals')

    @livewireScripts

    {{-- Incluir componentes de toasts al final --}}
    @include('admin.partials.toasts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const header = document.getElementById('adminHeader');
            const main = document.getElementById('adminMain');
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

        // Sistema de notificaciones
        window.notificationsSystem = {
            updateInterval: null,

            init: function() {
                this.loadNotifications();
                this.startPolling();
                this.bindEvents();
            },

            loadNotifications: function() {
                fetch('{{ route("admin.notificaciones.dropdown") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.updateNotificationUI(data.notificaciones, data.total_no_leidas);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            },

            updateNotificationUI: function(notificaciones, totalNoLeidas) {
                const badge = document.getElementById('notificationBadge');
                const sidebarBadge = document.getElementById('sidebarNotificationBadge');
                const count = document.getElementById('notificationCount');
                const list = document.getElementById('notificationsList');

                // Actualizar badges
                if (totalNoLeidas > 0) {
                    badge.textContent = totalNoLeidas;
                    badge.style.display = 'flex';
                    sidebarBadge.textContent = totalNoLeidas;
                    sidebarBadge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                    sidebarBadge.style.display = 'none';
                }

                // Actualizar contador
                count.textContent = totalNoLeidas + ' nuevas';

                // Actualizar lista
                if (notificaciones.length > 0) {
                    list.innerHTML = '';
                    notificaciones.forEach(notif => {
                        const item = this.createNotificationItem(notif);
                        list.appendChild(item);
                    });
                } else {
                    list.innerHTML = `
                        <div class="notifications-empty">
                            <i class="bi bi-bell-slash"></i>
                            <h6>Sin notificaciones</h6>
                            <p>No tienes notificaciones nuevas en este momento</p>
                        </div>
                    `;
                }
            },

            createNotificationItem: function(notif) {
                const div = document.createElement('div');
                div.className = 'notification-item' + (!notif.leida ? ' unread' : '');
                div.innerHTML = `
                    <div class="notification-content">
                        <div class="notification-icon ${notif.tipo}">
                            ${this.getNotificationIcon(notif.tipo)}
                        </div>
                        <div class="notification-body">
                            <div class="notification-title">${notif.titulo}</div>
                            <div class="notification-message">${notif.mensaje}</div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i>
                                ${this.timeAgo(notif.created_at)}
                            </div>
                            ${!notif.leida ? `
                            <div class="notification-actions">
                                <button class="btn btn-notification-action btn-mark-read" onclick="marcarLeidaDropdown('${notif._id}')">
                                    <i class="bi bi-check"></i> Marcar como leída
                                </button>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                return div;
            },

            getNotificationIcon: function(tipo) {
                const icons = {
                    'pedido': '<i class="bi bi-cart"></i>',
                    'venta': '<i class="bi bi-currency-dollar"></i>',
                    'usuario': '<i class="bi bi-person"></i>',
                    'comision': '<i class="bi bi-wallet"></i>',
                    'sistema': '<i class="bi bi-gear"></i>'
                };
                return icons[tipo] || '<i class="bi bi-bell"></i>';
            },

            timeAgo: function(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);

                if (seconds < 60) return 'hace un momento';
                if (seconds < 3600) return `hace ${Math.floor(seconds / 60)} min`;
                if (seconds < 86400) return `hace ${Math.floor(seconds / 3600)} h`;
                return `hace ${Math.floor(seconds / 86400)} d`;
            },

            startPolling: function() {
                this.updateInterval = setInterval(() => {
                    this.loadNotifications();
                }, 30000); // Actualizar cada 30 segundos
            },

            stopPolling: function() {
                if (this.updateInterval) {
                    clearInterval(this.updateInterval);
                    this.updateInterval = null;
                }
            },

            bindEvents: function() {
                // Cargar notificaciones cuando se abre el dropdown
                document.getElementById('notificationsDropdown').addEventListener('click', () => {
                    this.loadNotifications();
                });
            }
        };

        // Funciones globales para notificaciones
        window.verTodasLasNotificaciones = function() {
            window.location.href = '{{ route("admin.notificaciones.index") }}';
        };

        window.marcarLeidaDropdown = function(id) {
            fetch(`{{ route('admin.notificaciones.marcar-leida', ':id') }}`.replace(':id', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationsSystem.loadNotifications();
                }
            });
        };

        window.marcarTodasLeidasDropdown = function() {
            fetch('{{ route("admin.notificaciones.marcar-todas-leidas") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationsSystem.loadNotifications();
                }
            });
        };

        // Inicializar sistema de notificaciones cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            notificationsSystem.init();
        });
    </script>

    {{-- Incluir funciones JavaScript para alertas --}}
    <script src="{{ asset('js/admin/admin-functions.js') }}"></script>

    {{-- Pasar mensajes flash a JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                if (window.adminAlerts) {
                    window.adminAlerts.showSuccess('¡Éxito!', '{{ session('success') }}');
                }
            @endif
            @if(session('error'))
                if (window.adminAlerts) {
                    window.adminAlerts.showError('Error', '{{ session('error') }}');
                }
            @endif
            @if(session('warning'))
                if (window.adminAlerts) {
                    window.adminAlerts.showWarning('Advertencia', '{{ session('warning') }}');
                }
            @endif
            @if(session('info'))
                if (window.adminAlerts) {
                    window.adminAlerts.showInfo('Información', '{{ session('info') }}');
                }
            @endif
        });
    </script>

    {{-- Incluir funciones JavaScript para pedidos --}}
    {{-- <script src="{{ asset('js/admin/pedidos-functions.js') }}"></script> --}}

    {{-- Sistema de notificaciones en tiempo real mejorado - Temporalmente deshabilitado para evitar conflictos --}}
    {{-- <script src="{{ asset('js/admin/notifications-realtime.js') }}?v={{ filemtime(public_path('js/admin/notifications-realtime.js')) }}"></script> --}}

    @stack('scripts')
</body>
</html>