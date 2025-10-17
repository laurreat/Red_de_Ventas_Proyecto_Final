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

    <!-- Header Dropdowns CSS -->
    <link rel="stylesheet" href="{{ asset('css/lider/header-dropdowns.css') }}?v={{ filemtime(public_path('css/lider/header-dropdowns.css')) }}">

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

        .notification-badge-animated {
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
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-badge 2s ease-in-out infinite;
        }

        @keyframes pulse-badge {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
            }
        }

        /* Badge en sidebar */
        .nav-link .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            font-weight: 600;
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

        /* Dropdown del sidebar - sin fondo */
        .lider-sidebar .dropdown-menu {
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
            margin: 0;
            position: static;
        }

        /* Bootstrap dropdown fix */
        .dropdown-toggle::after {
            margin-left: 0.5rem;
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

            <!-- Configuración -->
            <div class="nav-section">Configuración</div>

            <div class="nav-item">
                <a href="{{ route('lider.perfil.index') }}" class="nav-link {{ request()->routeIs('lider.perfil.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    Mi Perfil
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('lider.notificaciones.index') }}" class="nav-link {{ request()->routeIs('lider.notificaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i>
                    Notificaciones
                    <span class="badge bg-danger ms-auto" id="sidebarNotifBadge" style="display: none;">0</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('lider.ayuda.index') }}" class="nav-link {{ request()->routeIs('lider.ayuda.*') ? 'active' : '' }}">
                    <i class="bi bi-question-circle"></i>
                    Centro de Ayuda
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
                                <i class="bi bi-person-badge me-1"></i>
                                {{ ucfirst(Auth::user()->rol) }}
                            </span>
                        </div>

                        <!-- Stats Section -->
                        @php
                            // Obtener miembros del equipo directo (referidos directos)
                            $equipoDirecto = \App\Models\User::where('referido_por', Auth::user()->_id)->get();
                            $totalEquipo = $equipoDirecto->count();

                            // Obtener total de ventas del líder y su equipo
                            $idsEquipo = $equipoDirecto->pluck('_id')->toArray();
                            $idsEquipo[] = Auth::user()->_id; // Incluir al líder

                            $totalVentas = \App\Models\Pedido::whereIn('vendedor_id', $idsEquipo)
                                ->whereIn('estado', ['completado', 'entregado'])
                                ->count();

                            // Obtener comisiones reales del líder desde la colección de comisiones
                            // Suma de comisiones aprobadas y pendientes (que aún no han sido pagadas)
                            $comisionesDisponibles = \App\Models\Comision::where('user_id', Auth::user()->_id)
                                ->whereIn('estado', ['aprobada', 'pendiente'])
                                ->sum('monto');

                            $liderStats = [
                                'equipo' => $totalEquipo,
                                'ventas' => $totalVentas,
                                'comisiones' => $comisionesDisponibles
                            ];
                        @endphp
                        <div class="profile-stats">
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $liderStats['equipo'] }}</span>
                                <span class="profile-stat-label">Equipo</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">{{ $liderStats['ventas'] }}</span>
                                <span class="profile-stat-label">Ventas</span>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-value">${{ format_currency($liderStats['comisiones']) }}</span>
                                <span class="profile-stat-label">Comisiones</span>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="profile-menu-section">
                            <a href="{{ route('lider.dashboard') }}" class="profile-menu-item">
                                <i class="bi bi-speedometer2"></i>
                                <span class="menu-item-text">Dashboard</span>
                            </a>
                            <a href="{{ route('lider.perfil.index') }}" class="profile-menu-item">
                                <i class="bi bi-person"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                            <a href="{{ route('lider.equipo.index') }}" class="profile-menu-item">
                                <i class="bi bi-people"></i>
                                <span class="menu-item-text">Mi Equipo</span>
                            </a>
                            <a href="{{ route('lider.comisiones.index') }}" class="profile-menu-item">
                                <i class="bi bi-currency-dollar"></i>
                                <span class="menu-item-text">Comisiones</span>
                            </a>
                        </div>

                        <hr class="profile-menu-divider">

                        <div class="profile-menu-section">
                            <a href="{{ route('lider.configuracion.index') }}" class="profile-menu-item">
                                <i class="bi bi-gear"></i>
                                <span class="menu-item-text">Configuración</span>
                            </a>
                            <a href="{{ route('lider.ayuda.index') }}" class="profile-menu-item">
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
    <main class="lider-main" id="liderMain">
        @yield('content')
    </main>

    @livewireScripts
    @livewire('toast-notifications')

    <!-- Notificaciones en Tiempo Real - Temporalmente deshabilitado para evitar conflictos -->
    {{-- <script src="{{ asset('js/lider/notifications-realtime.js') }}?v={{ filemtime(public_path('js/lider/notifications-realtime.js')) }}" defer></script> --}}

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

        // Sistema de notificaciones con datos reales
        window.notificationsSystem = {
            init: function() {
                this.loadNotifications();
                // Actualizar cada 30 segundos para tiempo real
                setInterval(() => this.loadNotifications(), 30000);
            },

            loadNotifications: function() {
                fetch('{{ route("lider.notificaciones.dropdown") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.updateNotificationUI(data.notificaciones, data.total_no_leidas);
                        }
                    })
                    .catch(error => console.error('Error loading notifications:', error));
            },

            updateNotificationUI: function(notificaciones, total) {
                const badge = document.getElementById('notificationBadge');
                const sidebarBadge = document.getElementById('sidebarNotifBadge');
                const count = document.getElementById('notificationCount');
                const list = document.getElementById('notificationsList');

                // Actualizar badges
                if (total > 0) {
                    const badgeText = total > 99 ? '99+' : total;
                    badge.textContent = badgeText;
                    badge.style.display = 'flex';
                    if (sidebarBadge) {
                        sidebarBadge.textContent = badgeText;
                        sidebarBadge.style.display = 'inline-block';
                    }
                } else {
                    badge.style.display = 'none';
                    if (sidebarBadge) {
                        sidebarBadge.style.display = 'none';
                    }
                }

                // Actualizar contador
                count.textContent = total > 0 ? `${total} nueva${total > 1 ? 's' : ''}` : 'Sin nuevas';

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

                // Formatear fecha
                const fecha = notif.created_at ? this.formatearFecha(notif.created_at) : 'Hace un momento';

                div.innerHTML = `
                    <div class="notification-content">
                        <div class="notification-icon ${notif.tipo || 'sistema'}">
                            ${this.getNotificationIcon(notif.tipo || 'sistema')}
                        </div>
                        <div class="notification-body">
                            <div class="notification-title">${this.escapeHtml(notif.titulo || 'Notificación')}</div>
                            <div class="notification-message">${this.escapeHtml(notif.mensaje || '')}</div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i>
                                ${fecha}
                            </div>
                            ${!notif.leida ? `
                            <div class="notification-actions">
                                <button class="btn btn-notification-action btn-mark-read" onclick="marcarLeidaDropdown('${notif._id || notif.id}')">
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
                    'meta': '<i class="bi bi-target"></i>',
                    'equipo': '<i class="bi bi-people"></i>',
                    'sistema': '<i class="bi bi-gear"></i>'
                };
                return icons[tipo] || '<i class="bi bi-bell"></i>';
            },

            formatearFecha: function(fecha) {
                if (!fecha) return 'Hace un momento';
                const now = new Date();
                const notifDate = new Date(fecha);
                const diff = Math.floor((now - notifDate) / 1000); // diferencia en segundos

                if (diff < 60) return 'Hace un momento';
                if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
                if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
                if (diff < 604800) return `Hace ${Math.floor(diff / 86400)} días`;

                return notifDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
            },

            escapeHtml: function(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };

        // Funciones globales para notificaciones
        window.verTodasLasNotificaciones = function() {
            window.location.href = '{{ route("lider.notificaciones.index") }}';
        };

        window.marcarLeidaDropdown = function(id) {
            fetch(`{{ route('lider.notificaciones.marcar-leida', ':id') }}`.replace(':id', id), {
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
            })
            .catch(error => console.error('Error:', error));
        };

        window.marcarTodasLeidasDropdown = function() {
            fetch('{{ route("lider.notificaciones.marcar-todas-leidas") }}', {
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
            })
            .catch(error => console.error('Error:', error));
        };

        // Inicializar notificaciones cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            notificationsSystem.init();
        });
    </script>

    @stack('scripts')
</body>
</html>