<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Arepa la Llanerita') }} @yield('title')</title>
    <meta name="description" content="Sistema de ventas y gestión para Arepa la Llanerita - La mejor arepa de los llanos">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- App Theme -->
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-arepa"></div>
            <div class="mt-3 text-muted">Cargando...</div>
        </div>
    </div>

    <div id="app">
        @guest
            @yield('content')
        @else
            <!-- Navigation -->
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <i class="bi bi-shop me-2 fs-4"></i>
                        <span class="fw-bold">{{ config('app.name', 'Arepa la Llanerita') }}</span>
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                            @auth
                                @if(Auth::user()->puedeVender())
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard') }}">
                                            <i class="bi bi-speedometer2 me-1"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" onclick="showComingSoon('Inventario')">
                                            <i class="bi bi-boxes me-1"></i>
                                            Inventario
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" onclick="showComingSoon('Pedidos')">
                                            <i class="bi bi-cart3 me-1"></i>
                                            Pedidos
                                        </a>
                                    </li>
                                @endif
                                
                                @if(Auth::user()->esAdmin() || Auth::user()->esLider())
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-graph-up me-1"></i>
                                            Reportes
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="showComingSoon('Ventas')">Ventas</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="showComingSoon('Comisiones')">Comisiones</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="showComingSoon('Referidos')">Referidos</a></li>
                                        </ul>
                                    </li>
                                @endif
                            @endauth
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            @auth
                                <!-- Notifications -->
                                <li class="nav-item dropdown me-3">
                                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-bell fs-5"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                                            3
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
                                        <div class="p-3 border-bottom">
                                            <h6 class="mb-0">Notificaciones</h6>
                                        </div>
                                        <div class="p-3 text-center text-muted">
                                            <i class="bi bi-bell-slash fs-4"></i>
                                            <p class="mb-0">No hay notificaciones</p>
                                        </div>
                                    </div>
                                </li>
                                
                                <!-- User Menu -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                        <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <h6 class="dropdown-header">
                                                {{ Auth::user()->nombreCompleto() }}
                                                <small class="text-muted d-block">{{ ucfirst(Auth::user()->rol) }}</small>
                                            </h6>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showComingSoon('Perfil')">
                                                <i class="bi bi-person me-2"></i>
                                                Mi Perfil
                                            </a>
                                        </li>
                                        @if(Auth::user()->tieneReferidos())
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="showComingSoon('Mis Referidos')">
                                                    <i class="bi bi-people me-2"></i>
                                                    Mis Referidos
                                                    <span class="badge bg-primary ms-2">{{ Auth::user()->total_referidos }}</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if(Auth::user()->puedeVender())
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="showComingSoon('Comisiones')">
                                                    <i class="bi bi-cash-coin me-2"></i>
                                                    Comisiones
                                                    @if(Auth::user()->comisiones_disponibles > 0)
                                                        <span class="badge bg-success ms-2">
                                                            ${{ number_format(Auth::user()->comisiones_disponibles, 0) }}
                                                        </span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endif
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
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="py-4">
                @yield('content')
            </main>
        @endguest
    </div>

    @livewireScripts
    
    <!-- Livewire Toast Notifications -->
    @livewire('toast-notifications')

    <script>
        // Loading overlay functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
        
        // Toast functions (compatibility with old system)
        function showSuccessToast(message) {
            showToast(message, 'success');
        }
        
        function showErrorToast(message) {
            showToast(message, 'error');
        }
        
        // Coming soon modal
        function showComingSoon(feature) {
            alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
        }
        
        // Livewire loading states
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.sent', () => {
                showLoading();
            });
            
            Livewire.hook('message.processed', () => {
                hideLoading();
            });
        });
        
        // Global error handling
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Error no manejado:', event.reason);
            showErrorToast('Ha ocurrido un error inesperado');
        });
        
        // Coming soon modal
        function showComingSoon(feature) {
            alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
