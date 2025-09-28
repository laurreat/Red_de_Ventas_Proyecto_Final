<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Arepa la Llanerita') }} @yield('title')</title>
    <meta name="description" content="Sistema de ventas y gestión para Arepa la Llanerita - La mejor arepa de los llanos">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#722f37">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Arepa Llanerita">
    <meta name="msapplication-TileColor" content="#722f37">
    <meta name="msapplication-config" content="/browserconfig.xml">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-180x180.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/icons/icon-120x120.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/icons/icon-114x114.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/icons/icon-76x76.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/icons/icon-60x60.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/images/icons/icon-57x57.png">
    <link rel="apple-touch-icon" href="/images/icons/icon-180x180.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-16x16.png">
    
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
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo" style="height: 40px;" class="me-2">
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
        
        // Toast functions
        function showToast(message, type = 'success') {
            // Crear contenedor de toasts si no existe
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 99999;
                    max-width: 350px;
                `;
                document.body.appendChild(toastContainer);
            }

            // Crear toast
            const toast = document.createElement('div');
            const toastId = 'toast-' + Date.now();
            toast.id = toastId;

            const colors = {
                success: { bg: '#28a745', icon: 'bi-check-circle-fill' },
                error: { bg: '#dc3545', icon: 'bi-x-circle-fill' },
                warning: { bg: '#ffc107', icon: 'bi-exclamation-triangle-fill' },
                info: { bg: '#17a2b8', icon: 'bi-info-circle-fill' }
            };

            const color = colors[type] || colors.success;

            toast.style.cssText = `
                background: ${color.bg};
                color: white;
                padding: 15px 20px;
                margin-bottom: 10px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                font-size: 14px;
                font-weight: 500;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                cursor: pointer;
                position: relative;
                overflow: hidden;
            `;

            toast.innerHTML = `
                <i class="bi ${color.icon} me-2"></i>
                <span>${message}</span>
                <button onclick="hideToast('${toastId}')" style="
                    background: none;
                    border: none;
                    color: white;
                    margin-left: auto;
                    padding: 0 0 0 10px;
                    cursor: pointer;
                    font-size: 18px;
                    line-height: 1;
                ">&times;</button>
            `;

            toastContainer.appendChild(toast);

            // Animar entrada
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Auto hide después de 4 segundos
            setTimeout(() => {
                hideToast(toastId);
            }, 4000);

            // Hacer clickeable para cerrar
            toast.addEventListener('click', () => hideToast(toastId));
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }

        function showSuccessToast(message) {
            showToast(message, 'success');
        }

        function showErrorToast(message) {
            showToast(message, 'error');
        }

        function showWarningToast(message) {
            showToast(message, 'warning');
        }

        function showInfoToast(message) {
            showToast(message, 'info');
        }
        
        // Coming soon modal
        function showComingSoon(feature) {
            alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
        }

        // Livewire loading states
        document.addEventListener('livewire:navigating', showLoading);
        document.addEventListener('livewire:navigated', hideLoading);

        // Global error handling
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Error no manejado:', event.reason);
            showErrorToast('Ha ocurrido un error inesperado');
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('[PWA] Service Worker registrado exitosamente:', registration.scope);

                        // Verificar si hay una actualización esperando
                        if (registration.waiting) {
                            showUpdateAvailable(registration);
                        }

                        // Escuchar por nuevas actualizaciones
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    showUpdateAvailable(registration);
                                }
                            });
                        });
                    })
                    .catch(function(error) {
                        console.log('[PWA] Error al registrar Service Worker:', error);
                    });
            });
        }

        // PWA Install prompt
        let deferredPrompt;
        let installButton = null;

        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA] Prompt de instalación disponible');
            e.preventDefault();
            deferredPrompt = e;
            showInstallBanner();
        });

        window.addEventListener('appinstalled', (evt) => {
            console.log('[PWA] App instalada exitosamente');
            hideInstallBanner();
            showSuccessToast('¡App instalada exitosamente!');
        });

        function showInstallBanner() {
            // Crear banner de instalación si no existe
            if (!document.getElementById('install-banner')) {
                const banner = document.createElement('div');
                banner.id = 'install-banner';
                banner.className = 'alert alert-info alert-dismissible position-fixed bottom-0 start-0 m-3';
                banner.style.zIndex = '9999';
                banner.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi bi-download me-2"></i>
                        <div class="flex-grow-1">
                            <strong>¿Instalar la app?</strong><br>
                            <small>Instala Arepa la Llanerita para un acceso más rápido</small>
                        </div>
                        <div class="ms-3">
                            <button type="button" class="btn btn-sm btn-primary me-2" onclick="installPWA()">
                                <i class="bi bi-download me-1"></i>Instalar
                            </button>
                            <button type="button" class="btn-close" onclick="hideInstallBanner()"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(banner);
            }
        }

        function hideInstallBanner() {
            const banner = document.getElementById('install-banner');
            if (banner) {
                banner.remove();
            }
        }

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[PWA] Usuario aceptó instalar');
                    } else {
                        console.log('[PWA] Usuario rechazó instalar');
                    }
                    deferredPrompt = null;
                    hideInstallBanner();
                });
            }
        }

        function showUpdateAvailable(registration) {
            // Mostrar notificación de actualización disponible
            const updateBanner = document.createElement('div');
            updateBanner.id = 'update-banner';
            updateBanner.className = 'alert alert-warning alert-dismissible position-fixed top-0 start-50 translate-middle-x mt-3';
            updateBanner.style.zIndex = '9999';
            updateBanner.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    <div class="flex-grow-1">
                        <strong>¡Actualización disponible!</strong><br>
                        <small>Hay una nueva versión de la aplicación</small>
                    </div>
                    <div class="ms-3">
                        <button type="button" class="btn btn-sm btn-warning me-2" onclick="updatePWA()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                        </button>
                        <button type="button" class="btn-close" onclick="hideUpdateBanner()"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(updateBanner);

            window.updateRegistration = registration;
        }

        function hideUpdateBanner() {
            const banner = document.getElementById('update-banner');
            if (banner) {
                banner.remove();
            }
        }

        function updatePWA() {
            if (window.updateRegistration && window.updateRegistration.waiting) {
                window.updateRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
            }
        }

        // Detectar estado online/offline
        window.addEventListener('online', function() {
            showSuccessToast('¡Conexión restaurada!');
            console.log('[PWA] Volvió la conexión');
        });

        window.addEventListener('offline', function() {
            showErrorToast('Sin conexión - Modo offline activado');
            console.log('[PWA] Perdió la conexión');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
