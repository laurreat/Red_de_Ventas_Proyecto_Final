<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Título -->
    <title>{{ config('app.name', 'Arepa la Llanerita') }} @yield('title')</title>

    <!-- Descripción -->
    <meta name="description" content="Sistema de ventas y gestión para Arepa la Llanerita">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Estilos de la app (compilados con Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire -->
    @livewireStyles
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Overlay de carga con JS nativo -->
    <div id="loadingOverlay"
         class="position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex flex-column justify-content-center align-items-center"
         style="z-index:2000; display:none;">
        <div class="spinner-border text-danger" role="status"></div>
        <p class="mt-3 text-secondary">Cargando...</p>
    </div>

    <div id="app" class="flex-grow-1">
        @guest
            @yield('content')
        @else
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center fw-bold text-danger" href="{{ url('/') }}">
                        <i class="bi bi-shop me-2"></i> {{ config('app.name', 'Arepa la Llanerita') }}
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarContent">
                        <!-- Links -->
                        <ul class="navbar-nav me-auto">
                            @if(Auth::user()->puedeVender())
                                <li class="nav-item">
                                    <a href="{{ route('dashboard') }}" class="nav-link">
                                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" onclick="showComingSoon('Inventario')">
                                        <i class="bi bi-box-seam me-1"></i> Inventario
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" onclick="showComingSoon('Pedidos')">
                                        <i class="bi bi-basket me-1"></i> Pedidos
                                    </a>
                                </li>
                            @endif
                        </ul>

                        <!-- Notificaciones -->
                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-header fw-bold">Notificaciones</li>
                                    <li><span class="dropdown-item-text text-muted">No hay notificaciones</span></li>
                                </ul>
                            </li>

                            <!-- Usuario -->
                            <li class="nav-item dropdown">
                                <a id="userDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="px-3 py-2 text-muted small border-bottom">
                                        <strong>{{ Auth::user()->nombreCompleto() }}</strong><br>
                                        <span class="text-capitalize">{{ Auth::user()->rol }}</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="showComingSoon('Perfil')">
                                            <i class="bi bi-person-circle me-2"></i> Perfil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                        </a>
                                    </li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="py-4 container">
                @yield('content')
            </main>
        @endguest
    </div>

    <!-- Livewire -->
    @livewireScripts
    @livewire('toast-notifications')

    <!-- Bootstrap Bundle (con Popper incluido) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Overlay con JS nativo -->
    <script>
        function showComingSoon(feature) {
            alert(`${feature} estará disponible próximamente.`);
        }

        window.addEventListener("beforeunload", () => {
            document.getElementById("loadingOverlay").style.display = "flex";
        });

        window.addEventListener("load", () => {
            document.getElementById("loadingOverlay").style.display = "none";
        });
    </script>

    @stack('scripts')
</body>
</html>
