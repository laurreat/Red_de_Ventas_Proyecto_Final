<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #722F37;
            --primary-light: rgba(114, 47, 55, 0.1);
            --primary-dark: #5a252a;
            --secondary-color: #ffffff;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --border-color: #dee2e6;
            --text-muted: #6c757d;
            --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --border-radius: 0.5rem;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }

        .vendedor-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .vendedor-sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .sidebar-brand:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: rgba(255, 255, 255, 0.5);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        /* Dropdown Styles */
        .dropdown-toggle {
            position: relative;
        }

        .dropdown-toggle::after {
            content: '\F285';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            transition: var(--transition);
        }

        .dropdown-toggle.collapsed::after {
            content: '\F286';
        }

        .dropdown-menu-vendedor {
            background: rgba(0, 0, 0, 0.2);
            border: none;
            padding: 0.5rem 0;
        }

        .dropdown-item-vendedor {
            padding: 0.5rem 1rem 0.5rem 3rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            transition: var(--transition);
        }

        .dropdown-item-vendedor:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Main Content */
        .vendedor-main {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .vendedor-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-btn:hover {
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }

        .user-menu:hover {
            background: var(--light-color);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .vendedor-content {
            flex: 1;
            padding: 1.5rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .metric-card {
            transition: var(--transition);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(114, 47, 55, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .vendedor-sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .vendedor-sidebar.show {
                transform: translateX(0);
            }

            .vendedor-main {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--primary-color);
            cursor: pointer;
        }

        /* Utility Classes */
        .text-vinotinto {
            color: var(--primary-color) !important;
        }

        .bg-vinotinto {
            background-color: var(--primary-color) !important;
        }

        .border-vinotinto {
            border-color: var(--primary-color) !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="vendedor-wrapper">
        <!-- Sidebar -->
        <nav class="vendedor-sidebar" id="vendedorSidebar">
            <div class="sidebar-header">
                <a href="{{ route('vendedor.dashboard') }}" class="sidebar-brand">
                    <i class="bi bi-shop me-2"></i>
                    Arepa la Llanerita
                </a>
            </div>

            <div class="sidebar-nav">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <a href="{{ route('vendedor.dashboard') }}" class="nav-link {{ request()->routeIs('vendedor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </div>

                <!-- Ventas -->
                <div class="nav-section">
                    <div class="nav-section-title">Ventas</div>

                    <a href="#ventasSubmenu" class="nav-link dropdown-toggle" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('vendedor.pedidos.*') || request()->routeIs('vendedor.ventas.*') ? 'true' : 'false' }}">
                        <i class="bi bi-cart-check"></i>
                        Mis Ventas
                    </a>
                    <div class="collapse {{ request()->routeIs('vendedor.pedidos.*') || request()->routeIs('vendedor.ventas.*') ? 'show' : '' }}" id="ventasSubmenu">
                        <div class="dropdown-menu-vendedor">
                            <a href="{{ route('vendedor.pedidos.index') }}" class="dropdown-item-vendedor">Gestión de Pedidos</a>
                            <a href="{{ route('vendedor.ventas.crear') }}" class="dropdown-item-vendedor">Nueva Venta</a>
                            <a href="{{ route('vendedor.ventas.historial') }}" class="dropdown-item-vendedor">Historial de Ventas</a>
                        </div>
                    </div>
                </div>

                <!-- Clientes -->
                <div class="nav-section">
                    <div class="nav-section-title">Clientes</div>

                    <a href="#clientesSubmenu" class="nav-link dropdown-toggle" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('vendedor.clientes.*') ? 'true' : 'false' }}">
                        <i class="bi bi-people"></i>
                        Mis Clientes
                    </a>
                    <div class="collapse {{ request()->routeIs('vendedor.clientes.*') ? 'show' : '' }}" id="clientesSubmenu">
                        <div class="dropdown-menu-vendedor">
                            <a href="{{ route('vendedor.clientes.index') }}" class="dropdown-item-vendedor">Lista de Clientes</a>
                            <a href="{{ route('vendedor.clientes.crear') }}" class="dropdown-item-vendedor">Nuevo Cliente</a>
                            <a href="{{ route('vendedor.clientes.seguimiento') }}" class="dropdown-item-vendedor">Seguimiento</a>
                        </div>
                    </div>
                </div>

                <!-- Comisiones -->
                <div class="nav-section">
                    <div class="nav-section-title">Ganancias</div>

                    <a href="#comisionesSubmenu" class="nav-link dropdown-toggle" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('vendedor.comisiones.*') ? 'true' : 'false' }}">
                        <i class="bi bi-cash-coin"></i>
                        Comisiones
                    </a>
                    <div class="collapse {{ request()->routeIs('vendedor.comisiones.*') ? 'show' : '' }}" id="comisionesSubmenu">
                        <div class="dropdown-menu-vendedor">
                            <a href="{{ route('vendedor.comisiones.index') }}" class="dropdown-item-vendedor">Mis Comisiones</a>
                            <a href="{{ route('vendedor.comisiones.solicitar') }}" class="dropdown-item-vendedor">Solicitar Retiro</a>
                            <a href="{{ route('vendedor.comisiones.historial') }}" class="dropdown-item-vendedor">Historial de Pagos</a>
                        </div>
                    </div>
                </div>

                <!-- Referidos -->
                <div class="nav-section">
                    <div class="nav-section-title">Red</div>

                    <a href="#referidosSubmenu" class="nav-link dropdown-toggle" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('vendedor.referidos.*') ? 'true' : 'false' }}">
                        <i class="bi bi-diagram-3"></i>
                        Mis Referidos
                    </a>
                    <div class="collapse {{ request()->routeIs('vendedor.referidos.*') ? 'show' : '' }}" id="referidosSubmenu">
                        <div class="dropdown-menu-vendedor">
                            <a href="{{ route('vendedor.referidos.index') }}" class="dropdown-item-vendedor">Mi Red</a>
                            <a href="{{ route('vendedor.referidos.invitar') }}" class="dropdown-item-vendedor">Invitar Nuevos</a>
                            <a href="{{ route('vendedor.referidos.ganancias') }}" class="dropdown-item-vendedor">Ganancias por Referidos</a>
                        </div>
                    </div>
                </div>

                <!-- Metas -->
                <div class="nav-section">
                    <div class="nav-section-title">Objetivos</div>
                    <a href="{{ route('vendedor.metas.index') }}" class="nav-link {{ request()->routeIs('vendedor.metas.*') ? 'active' : '' }}">
                        <i class="bi bi-target"></i>
                        Mis Metas
                    </a>
                </div>

                <!-- Reportes -->
                <div class="nav-section">
                    <div class="nav-section-title">Análisis</div>

                    <a href="#reportesSubmenu" class="nav-link dropdown-toggle" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->routeIs('vendedor.reportes.*') ? 'true' : 'false' }}">
                        <i class="bi bi-graph-up"></i>
                        Mis Reportes
                    </a>
                    <div class="collapse {{ request()->routeIs('vendedor.reportes.*') ? 'show' : '' }}" id="reportesSubmenu">
                        <div class="dropdown-menu-vendedor">
                            <a href="{{ route('vendedor.reportes.ventas') }}" class="dropdown-item-vendedor">Reporte de Ventas</a>
                            <a href="{{ route('vendedor.reportes.rendimiento') }}" class="dropdown-item-vendedor">Mi Rendimiento</a>
                            <a href="{{ route('vendedor.reportes.comisiones') }}" class="dropdown-item-vendedor">Reporte de Comisiones</a>
                        </div>
                    </div>
                </div>

                <!-- Perfil -->
                <div class="nav-section">
                    <div class="nav-section-title">Cuenta</div>
                    <a href="{{ route('vendedor.perfil.index') }}" class="nav-link {{ request()->routeIs('vendedor.perfil.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        Mi Perfil
                    </a>
                    <a href="{{ route('vendedor.configuracion.index') }}" class="nav-link {{ request()->routeIs('vendedor.configuracion.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        Configuración
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="vendedor-main">
            <!-- Header -->
            <header class="vendedor-header">
                <button class="mobile-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>

                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>

                <div class="header-actions">
                    <!-- Notifications -->
                    <button class="notification-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <li><a class="dropdown-item" href="#">Nueva comisión disponible</a></li>
                        <li><a class="dropdown-item" href="#">Meta mensual al 80%</a></li>
                        <li><a class="dropdown-item" href="#">Nuevo referido registrado</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>

                    <!-- User Menu -->
                    <div class="dropdown">
                        <div class="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-medium">{{ auth()->user()->name }}</div>
                                <small class="text-muted">Vendedor</small>
                            </div>
                            <i class="bi bi-chevron-down ms-1"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('vendedor.perfil.index') }}">
                                <i class="bi bi-person me-2"></i>Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('vendedor.configuracion.index') }}">
                                <i class="bi bi-gear me-2"></i>Configuración
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="vendedor-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('vendedorSidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('vendedorSidebar');
            const toggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            // Simple alert for now, can be enhanced with toast library
            const alertClass = type === 'success' ? 'alert-success' :
                             type === 'error' ? 'alert-danger' : 'alert-info';

            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        // Coming soon function
        function showComingSoon(feature) {
            showToast(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`, 'info');
        }
    </script>

    @stack('scripts')
</body>
</html>