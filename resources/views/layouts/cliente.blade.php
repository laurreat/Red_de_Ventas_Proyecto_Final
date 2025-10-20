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
    <link rel="stylesheet" href="{{ asset('css/header-dropdowns.css') }}?v={{ filemtime(public_path('css/header-dropdowns.css')) }}">

    <!-- Glass Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/modules/glass-modal.css') }}?v={{ time() }}">

    <!-- Mobile Optimizations -->
    <link rel="stylesheet" href="{{ asset('css/mobile-optimizations.css') }}">

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
            background-color: #f9fafb;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-delay-1 {
            animation-delay: 0.1s;
        }

        .animate-delay-2 {
            animation-delay: 0.2s;
        }

        .animate-delay-3 {
            animation-delay: 0.3s;
        }

        /* Sidebar Styles - Enhanced & Modern */
        .cliente-sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
            border-right: 1px solid #e5e7eb;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1040;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .cliente-sidebar.collapsed {
            transform: translateX(-280px);
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 2px solid #f3f4f6;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        }

        .sidebar-brand-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .sidebar-brand:hover {
            transform: scale(1.02);
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.125rem;
            color: var(--text-dark);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Sidebar User Info */
        .sidebar-user-info {
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(114, 47, 55, 0.05) 0%, rgba(114, 47, 55, 0.02) 100%);
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(114, 47, 55, 0.2);
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            color: #111827;
            font-size: 0.9375rem;
            margin-bottom: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-role {
            font-size: 0.75rem;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 600;
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(114, 47, 55, 0.3) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(114, 47, 55, 0.3);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background-color: rgba(114, 47, 55, 0.5);
        }

        /* Nav Section */
        .nav-section {
            padding: 1rem 1.5rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-section:first-child {
            margin-top: 0;
        }

        .nav-section i {
            font-size: 0.875rem;
        }

        /* Nav Item */
        .nav-item {
            margin: 0.25rem 0.75rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: #4b5563;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
            overflow: hidden;
            gap: 0.875rem;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
            transform: scaleY(0);
            transition: transform 0.25s ease;
            border-radius: 0 4px 4px 0;
        }

        .nav-link:hover {
            background: linear-gradient(90deg, rgba(114, 47, 55, 0.08) 0%, rgba(114, 47, 55, 0.04) 100%);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(114, 47, 55, 0.12) 0%, rgba(114, 47, 55, 0.06) 100%);
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i:first-child {
            width: 24px;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .nav-link span:first-of-type {
            flex: 1;
        }

        /* Nav Badge */
        .nav-badge {
            padding: 0.25rem 0.5rem;
            background: #f3f4f6;
            color: #6b7280;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .nav-badge.badge-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .nav-badge.badge-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .nav-icon-end {
            font-size: 1.25rem;
            opacity: 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .nav-link:hover .nav-icon-end {
            opacity: 1;
            transform: translateX(4px);
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 1.25rem 1.5rem;
            border-top: 2px solid #f3f4f6;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        }

        .logout-form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;
            padding: 0.875rem 1rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
        }

        .logout-btn i {
            font-size: 1.125rem;
        }

        /* Header */
        .cliente-header {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 75px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-bottom: 2px solid #e5e7eb;
            z-index: 1030;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .cliente-header.expanded {
            left: 0;
        }

        .header-content {
            height: 100%;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .sidebar-toggle {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.625rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-toggle:hover {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            transform: scale(1.05);
        }

        .header-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .header-notifications {
            position: relative;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.625rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-notifications:hover {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            transform: scale(1.05);
        }

        .notification-badge {
            position: absolute;
            top: 0.125rem;
            right: 0.125rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            font-size: 0.625rem;
            padding: 0.125rem 0.4rem;
            border-radius: 10px;
            min-width: 1.25rem;
            text-align: center;
            font-weight: 700;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            cursor: pointer;
            padding: 0.5rem 0.875rem;
            border-radius: 14px;
            transition: all 0.25s ease;
            border: 2px solid transparent;
        }

        .header-profile:hover {
            background: linear-gradient(135deg, rgba(114, 47, 55, 0.08) 0%, rgba(114, 47, 55, 0.04) 100%);
            border-color: rgba(114, 47, 55, 0.1);
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(114, 47, 55, 0.25);
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .profile-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.3;
            font-weight: 500;
        }

        /* Main Content */
        .cliente-main {
            margin-left: 280px;
            margin-top: 75px;
            padding: 2rem;
            min-height: calc(100vh - 75px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
            background: #f9fafb;
        }

        .cliente-main.expanded {
            margin-left: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .cliente-sidebar {
                transform: translateX(-280px);
            }

            .cliente-sidebar.show {
                transform: translateX(0);
            }

            .cliente-header {
                left: 0;
            }

            .cliente-main {
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
            .cliente-main {
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

            .header-title {
                font-size: 1.1rem;
            }

            .sidebar-toggle {
                margin-right: 0.75rem;
            }
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-bottom: 2px solid #f3f4f6;
            font-weight: 600;
            position: relative;
            z-index: 2;
            padding: 1.25rem 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(114, 47, 55, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(114, 47, 55, 0.35);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
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
    <nav class="cliente-sidebar" id="clienteSidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="sidebar-brand-wrapper">
                <a href="{{ route('cliente.dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">Arepa Llanerita</span>
                        <span class="brand-subtitle">Cliente</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Sidebar User Info -->
        <div class="sidebar-user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos ?? '', 0, 1)) }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">
                    <i class="bi bi-person-check"></i>
                    Cliente
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <i class="bi bi-grid-fill"></i>
                <span>Principal</span>
            </div>
            <div class="nav-item">
                <a href="{{ route('cliente.dashboard') }}" class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Pedidos -->
            <div class="nav-section">
                <i class="bi bi-cart-fill"></i>
                <span>Mis Pedidos</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('cliente.pedidos.index') }}" class="nav-link {{ request()->routeIs('cliente.pedidos.index') || request()->routeIs('cliente.pedidos.show') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Ver Pedidos</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('cliente.pedidos.create') }}" class="nav-link {{ request()->routeIs('cliente.pedidos.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nuevo Pedido</span>
                </a>
            </div>

            <!-- Notificaciones -->
            <div class="nav-section">
                <i class="bi bi-bell-fill"></i>
                <span>Notificaciones</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('cliente.notificaciones.modulo') }}" class="nav-link {{ request()->routeIs('cliente.notificaciones.modulo') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i>
                    <span>Mis Notificaciones</span>
                    @php
                        $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())->where('leida', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="nav-badge badge-warning">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Configuración -->
            <div class="nav-section">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('cliente.perfil.index') }}" class="nav-link {{ request()->routeIs('cliente.perfil.index') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>

            @if(Auth::user()->codigo_referido)
            <div class="nav-item">
                <a href="#" class="nav-link" onclick="showComingSoon('Mis Referidos')">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Mis Referidos</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
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
    <header class="cliente-header" id="clienteHeader">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="header-title">@yield('header-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                <!-- Notifications Dropdown -->
                <div class="dropdown header-dropdown">
                    <button class="header-notifications" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        @php
                            $unreadCount = \App\Models\Notificacion::where('user_id', Auth::id())->where('leida', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notifications-dropdown header-dropdown-menu" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-header-modern">
                            <h6 class="mb-0">Notificaciones</h6>
                            <div class="dropdown-header-actions">
                                <span class="notification-count-badge" id="notificationCountText">Cargando...</span>
                            </div>
                        </div>
                        <div class="notifications-list" id="notificationsList">
                            <!-- Las notificaciones se cargan dinámicamente -->
                            <div class="notifications-loading text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0 small text-muted">Cargando notificaciones...</p>
                            </div>
                        </div>
                        <div class="dropdown-footer">
                            <button class="btn-mark-all-read" id="btnMarkAllRead">
                                <i class="bi bi-check-all"></i>
                                Marcar todas como leídas
                            </button>
                            <a href="{{ route('cliente.notificaciones.modulo') }}" class="btn-view-all-notifications">
                                <i class="bi bi-arrow-right"></i>
                                Ver todas las notificaciones
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown header-dropdown">
                    <div class="header-profile" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <div class="profile-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="profile-info">
                            <div class="profile-name">{{ Auth::user()->name }}</div>
                            <div class="profile-role">Cliente</div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown header-dropdown-menu profile-dropdown" aria-labelledby="userDropdown">
                        <li class="profile-dropdown-header">
                            <div class="profile-dropdown-avatar">
                                <span class="avatar-initial">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos ?? '', 0, 1)) }}</span>
                            </div>
                            <div class="profile-dropdown-name">{{ Auth::user()->nombreCompleto() }}</div>
                            <div class="profile-dropdown-email">{{ Auth::user()->email }}</div>
                            <span class="profile-dropdown-role">Cliente</span>
                        </li>
                        <li><hr class="profile-menu-divider"></li>
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="{{ route('cliente.dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                <span class="menu-item-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="{{ route('cliente.pedidos.index') }}">
                                <i class="bi bi-box-seam"></i>
                                <span class="menu-item-text">Mis Pedidos</span>
                            </a>
                        </li>
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="{{ route('cliente.perfil.index') }}">
                                <i class="bi bi-person-circle"></i>
                                <span class="menu-item-text">Mi Perfil</span>
                            </a>
                        </li>
                        @if(Auth::user()->codigo_referido)
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="#" onclick="showComingSoon('Mis Referidos'); return false;">
                                <i class="bi bi-diagram-3"></i>
                                <span class="menu-item-text">Mis Referidos</span>
                            </a>
                        </li>
                        @endif
                        <li><hr class="profile-menu-divider"></li>
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="#" onclick="showComingSoon('Configuración'); return false;">
                                <i class="bi bi-gear"></i>
                                <span class="menu-item-text">Configuración</span>
                            </a>
                        </li>
                        <li class="profile-menu-section">
                            <a class="profile-menu-item" href="#" onclick="showComingSoon('Ayuda'); return false;">
                                <i class="bi bi-question-circle"></i>
                                <span class="menu-item-text">Ayuda</span>
                            </a>
                        </li>
                        <li><hr class="profile-menu-divider"></li>
                        <li class="profile-menu-section">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="profile-menu-item danger w-100 text-start border-0 bg-transparent">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span class="menu-item-text">Cerrar Sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="cliente-main" id="clienteMain">
        @yield('content')
    </main>

    @livewireScripts
    @livewire('toast-notifications')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('clienteSidebar');
            const header = document.getElementById('clienteHeader');
            const main = document.getElementById('clienteMain');
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

            // Initialize Bootstrap dropdowns with proper configuration
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl, {
                    autoClose: true,
                    boundary: 'viewport'
                });
            });
        });

        // Coming soon function
        function showComingSoon(feature) {
            if (typeof GlassModal !== 'undefined') {
                GlassModal.info(
                    `${feature} - Próximamente`,
                    `La funcionalidad de ${feature} estará disponible muy pronto. ¡Estamos trabajando en ello para brindarte la mejor experiencia!`
                );
            } else {
                alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
            }
        }
    </script>

    <!-- Sistema de Modales Glass -->
    <script src="{{ asset('js/modules/glass-modal.js') }}?v={{ time() }}"></script>

    <!-- Sistema de Notificaciones en Tiempo Real -->
    <script src="{{ asset('js/modules/notifications-realtime.js') }}?v={{ time() }}"></script>
    
    <script>
        // Ensure GlassModal is loaded
        if (typeof GlassModal === 'undefined') {
            console.error('GlassModal no está cargado');
        }
    </script>

    @stack('scripts')
</body>
</html>
