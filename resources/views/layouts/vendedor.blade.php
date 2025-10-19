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

    <!-- Header Dropdowns CSS - Using Admin Unified Style -->
    <link rel="stylesheet" href="{{ asset('css/header-dropdowns.css') }}?v={{ filemtime(public_path('css/header-dropdowns.css')) }}">

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
        .vendedor-sidebar {
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

        .vendedor-sidebar.collapsed {
            transform: translateX(-280px);
        }

        /* Sidebar Header - Mejorado */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--secondary-color);
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 6s ease-in-out infinite;
        }

        .sidebar-brand-wrapper {
            position: relative;
            z-index: 2;
        }

        .sidebar-brand {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--secondary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .sidebar-brand:hover {
            color: var(--secondary-color);
            transform: translateX(4px);
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            opacity: 0.9;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Sidebar User Info - Nuevo */
        .sidebar-user-info {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
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

        /* Sidebar Navigation - Mejorado */
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

        /* Nav Section - Mejorado */
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

        /* Nav Item - Mejorado */
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

        /* Nav Badge - Nuevo */
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

        /* Sidebar Footer - Nuevo */
        .sidebar-footer {
            padding: 1.25rem 1.5rem;
            border-top: 2px solid #f3f4f6;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        }

        .footer-stats {
            margin-bottom: 1rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #92400e;
        }

        .stat-item i {
            font-size: 1.125rem;
        }

        .logout-form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .logout-btn i {
            font-size: 1.125rem;
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

        /* Header Styles - Enhanced */
        .vendedor-header {
            height: 75px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            position: fixed;
            top: 0;
            right: 0;
            left: 280px;
            z-index: 1060;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .vendedor-header.expanded {
            left: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 2rem;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            margin-right: 1.5rem;
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

        /* Main Content - Enhanced */
        .vendedor-main {
            margin-left: 280px;
            margin-top: 75px;
            padding: 2rem;
            min-height: calc(100vh - 75px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
            background: #f9fafb;
        }

        .vendedor-main.expanded {
            margin-left: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .vendedor-sidebar {
                transform: translateX(-280px);
            }

            .vendedor-sidebar.show {
                transform: translateX(0);
            }

            .vendedor-header {
                left: 0;
            }

            .vendedor-main {
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
            .vendedor-main {
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

        /* Card Styles - Enhanced */
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

        /* Buttons - Enhanced */
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

        /* Dropdown Menu */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            z-index: 1070 !important;
            position: absolute !important;
        }

        /* Específico para dropdowns del header */
        .vendedor-header .dropdown-menu {
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

        .vendedor-header .dropdown.show .dropdown-menu {
            z-index: 1100 !important;
        }

        /* Contenedor global para evitar stacking context issues */
        .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Todos los elementos del contenido principal deben estar por debajo del header */
        .vendedor-main * {
            position: relative;
            z-index: auto;
        }

        /* Las badges del contenido específicamente deben estar muy por debajo */
        .vendedor-main .badge {
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
    
    <!-- Sidebar Mejorado -->
    <nav class="vendedor-sidebar" id="vendedorSidebar">
        <!-- Sidebar Header Mejorado -->
        <div class="sidebar-header">
            <div class="sidebar-brand-wrapper">
                <a href="{{ route('vendedor.dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">Arepa Llanerita</span>
                        <span class="brand-subtitle">Vendedor</span>
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
                    <i class="bi bi-shield-check"></i>
                    Vendedor Activo
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
                <a href="{{ route('vendedor.dashboard') }}" class="nav-link {{ request()->routeIs('vendedor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                    <span class="nav-badge">Inicio</span>
                </a>
            </div>

            <!-- Gestión de Ventas -->
            <div class="nav-section">
                <i class="bi bi-cart-fill"></i>
                <span>Ventas</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.pedidos.index') }}" class="nav-link {{ request()->routeIs('vendedor.pedidos.index') || request()->routeIs('vendedor.pedidos.show') ? 'active' : '' }}">
                    <i class="bi bi-cart-check-fill"></i>
                    <span>Mis Pedidos</span>
                    @if(isset($pedidosPendientes) && $pedidosPendientes > 0)
                    <span class="nav-badge badge-warning">{{ $pedidosPendientes }}</span>
                    @endif
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.pedidos.create') }}" class="nav-link {{ request()->routeIs('vendedor.pedidos.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Crear Pedido</span>
                    <span class="nav-icon-end">
                        <i class="bi bi-arrow-right-short"></i>
                    </span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.productos.index') }}" class="nav-link {{ request()->routeIs('vendedor.productos.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>Productos</span>
                </a>
            </div>

            <!-- Ganancias -->
            <div class="nav-section">
                <i class="bi bi-cash-stack"></i>
                <span>Ganancias</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.comisiones.index') }}" class="nav-link {{ request()->routeIs('vendedor.comisiones.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Mis Comisiones</span>
                    <span class="nav-icon-end">
                        <i class="bi bi-graph-up"></i>
                    </span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.referidos.index') }}" class="nav-link {{ request()->routeIs('vendedor.referidos.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Mis Referidos</span>
                    @if(Auth::user()->referidos_count > 0)
                    <span class="nav-badge badge-success">{{ Auth::user()->referidos_count }}</span>
                    @endif
                </a>
            </div>

            <!-- Configuración -->
            <div class="nav-section">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.perfil.index') }}" class="nav-link {{ request()->routeIs('vendedor.perfil.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>

            <!-- Quick Actions -->
            <div class="nav-section">
                <i class="bi bi-lightning-fill"></i>
                <span>Acceso Rápido</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('vendedor.ayuda.index') }}" class="nav-link {{ request()->routeIs('vendedor.ayuda.*') ? 'active' : '' }}">
                    <i class="bi bi-question-circle-fill"></i>
                    <span>Ayuda</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="footer-stats">
                <div class="stat-item">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Top Vendedor</span>
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

    <!-- Header Unificado -->
    @include('admin.partials.unified-header', [
        'headerId' => 'vendedorHeader',
        'sidebarToggleId' => 'sidebarToggle'
    ])

    <!-- Main Content -->
    <main class="vendedor-main" id="vendedorMain">
        @yield('content')
    </main>

    @livewireScripts
    @livewire('toast-notifications')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('vendedorSidebar');
            const header = document.getElementById('vendedorHeader');
            const main = document.getElementById('vendedorMain');
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

    <!-- Header Modern Script -->
    <script src="{{ asset('js/vendedor/header-modern.js') }}?v={{ filemtime(public_path('js/vendedor/header-modern.js')) }}"></script>

    @stack('scripts')
</body>
</html>