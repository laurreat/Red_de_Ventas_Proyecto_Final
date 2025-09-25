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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/catalogo-publico.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Sidebar (Estilo Admin) -->
    <nav class="public-sidebar" id="publicSidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="{{ url('/') }}" class="sidebar-brand">
                <i class="bi bi-shop"></i>
                <span class="brand-text">{{ config('app.name') }}</span>
            </a>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="nav-section">Navegación</div>

            <div class="nav-item">
                <a href="{{ url('/') }}"
                   class="nav-link {{ !request('categoria') ? 'active' : '' }}"
                   data-category="all">
                    <i class="bi bi-grid"></i>
                    <span class="nav-text">Todos los Productos</span>
                </a>
            </div>

            <!-- Categories Dropdown -->
            @if(isset($categorias) && $categorias->count() > 0)
                <div class="nav-item">
                    <a href="#categoriesDropdown"
                       class="nav-link dropdown-toggle has-dropdown"
                       id="categoriesToggle"
                       data-tooltip="Categorías ({{ $categorias->count() }})"
                       aria-expanded="false">
                        <i class="bi bi-tags"></i>
                        <span class="nav-text">Categorías</span>
                        <span class="total-categories-badge nav-text">{{ $categorias->count() }}</span>
                    </a>

                    <div class="dropdown-menu" id="categoriesDropdown">
                        @foreach($categorias as $categoria)
                            <a href="{{ url('/?categoria=' . $categoria->id) }}"
                               class="dropdown-item {{ request('categoria') == $categoria->id ? 'active' : '' }}"
                               data-category="{{ $categoria->id }}">
                                <i class="bi bi-tag"></i>
                                <span class="nav-text">{{ $categoria->nombre }}</span>
                                @if($categoria->productos_count > 0)
                                    <span class="category-counter">{{ $categoria->productos_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="nav-section">Cuenta</div>

            <div class="nav-item">
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="nav-text">Iniciar Sesión</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('register') }}" class="nav-link">
                    <i class="bi bi-person-plus"></i>
                    <span class="nav-text">Registrarse</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle" id="sidebarToggle" title="Contraer/Expandir sidebar">
            <i class="bi bi-chevron-left" id="toggleIcon"></i>
        </button>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container-fluid">
                <!-- Mobile Sidebar Toggle -->
                <button class="btn btn-outline-primary d-lg-none me-3"
                        id="mobileSidebarToggle"
                        title="Abrir menú">
                    <i class="bi bi-list"></i>
                </button>

                <!-- Page Title -->
                <span class="navbar-brand mb-0 h1 d-none d-md-block">
                    @yield('page-title', 'Catálogo de Productos')
                </span>

                <!-- Mobile Title -->
                <span class="navbar-brand mb-0 h6 d-md-none">
                    @yield('page-title-mobile', 'Catálogo')
                </span>

                <!-- Authentication Buttons -->
                <div class="ms-auto auth-buttons">
                    <a href="{{ url('/') }}"
                       class="btn btn-sm btn-outline-primary"
                       title="Ir al inicio">
                        <i class="bi bi-house me-1"></i>
                        <span class="d-none d-sm-inline">Inicio</span>
                    </a>
                    <a href="{{ route('login') }}"
                       class="btn btn-sm btn-outline-primary"
                       title="Iniciar sesión">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        <span class="d-none d-sm-inline">Iniciar Sesión</span>
                    </a>
                    <a href="{{ route('register') }}"
                       class="btn btn-sm btn-primary"
                       title="Crear cuenta nueva">
                        <i class="bi bi-person-plus me-1"></i>
                        <span class="d-none d-sm-inline">Registrarse</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer py-4" style="background-color: var(--primary-color); color: white; margin-top: auto;">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-shop me-3 fs-3"></i>
                        <div>
                            <h5 class="mb-1">{{ config('app.name') }}</h5>
                            <p class="mb-0 text-light">Tu tienda de confianza para productos de calidad</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="social-links mb-3">
                        <h6 class="mb-2">Síguenos</h6>
                        <a href="#" class="text-white me-3" title="Facebook"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3" title="Instagram"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white" title="Twitter"><i class="bi bi-twitter fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 small text-light">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-light">
                        <i class="bi bi-shield-check me-1"></i>
                        Compra segura garantizada
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('assets/js/catalogo-publico.js') }}"></script>

    @yield('scripts')
</body>
</html>