@extends('layouts.cliente')

@section('title', ' - Mi Dashboard')
@section('header-title', 'Mi Dashboard')

@push('styles')
<style>
    /* Dashboard Moderno del Cliente */
    
    /* Hero Welcome Card con Glassmorphism */
    .welcome-hero {
        background: linear-gradient(135deg, #722F37 0%, #8b3c44 100%);
        border-radius: 24px;
        padding: 3rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(114, 47, 55, 0.3);
    }

    .welcome-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .welcome-hero::after {
        content: '🫓';
        position: absolute;
        bottom: -20px;
        right: 5%;
        font-size: 200px;
        opacity: 0.1;
        transform: rotate(-15deg);
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        opacity: 0.95;
        margin-bottom: 1.5rem;
    }

    .codigo-referido-box {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 1rem 2rem;
        border-radius: 16px;
        border: 2px dashed rgba(255,255,255,0.4);
        transition: all 0.3s ease;
    }

    .codigo-referido-box:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* Stats Cards Mejoradas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        border-color: var(--primary-color);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-detail {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    /* Quick Actions Cards */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .action-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .action-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .action-card:hover .action-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    .action-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }

    .action-description {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* Pedidos Recientes */
    .pedidos-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-badge {
        background: var(--primary-color);
        color: white;
        padding: 0.35rem 0.875rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .pedido-item {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .pedido-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateX(4px);
    }

    .pedido-numero {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        margin-right: 1.25rem;
    }

    .pedido-info {
        flex: 1;
    }

    .pedido-title {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }

    .pedido-meta {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .pedido-estado {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .estado-pendiente { background: #fef3c7; color: #92400e; }
    .estado-confirmado { background: #dbeafe; color: #1e40af; }
    .estado-en-preparacion { background: #e0e7ff; color: #4338ca; }
    .estado-enviado { background: #fce7f3; color: #9f1239; }
    .estado-entregado { background: #d1fae5; color: #065f46; }
    .estado-cancelado { background: #fee2e2; color: #991b1b; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 5rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .empty-text {
        color: var(--text-muted);
        margin-bottom: 2rem;
    }

    /* Productos Destacados */
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .producto-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .producto-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        border-color: var(--primary-color);
    }

    .producto-image {
        height: 200px;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        position: relative;
        overflow: hidden;
    }

    .producto-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .producto-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--primary-color);
        color: white;
        padding: 0.35rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .producto-content {
        padding: 1.5rem;
    }

    .producto-nombre {
        font-weight: 700;
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }

    .producto-precio {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .btn-agregar {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-agregar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(114, 47, 55, 0.3);
    }

    /* Animaciones */
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

    .animate-fade-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
    .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
    .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
    .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }

    /* Responsive */
    @media (max-width: 768px) {
        .welcome-hero {
            padding: 2rem 1.5rem;
        }

        .welcome-title {
            font-size: 1.75rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .productos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Hero -->
    <div class="welcome-hero animate-fade-in">
        <div class="welcome-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="welcome-title">¡Bienvenido, {{ auth()->user()->name }}! 👋</h1>
                    <p class="welcome-subtitle">
                        Disfruta de nuestras deliciosas arepas y productos tradicionales.
                        @if(auth()->user()->referido_por)
                            @php
                                $referidor = \App\Models\User::find(auth()->user()->referido_por);
                            @endphp
                            Fuiste referido por <strong>{{ $referidor->name ?? 'Usuario' }}</strong>
                        @endif
                    </p>
                    @if(auth()->user()->codigo_referido)
                    <div class="codigo-referido-box">
                        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Tu código de referido</div>
                        <div style="font-size: 1.5rem; font-weight: 800; letter-spacing: 2px;">{{ auth()->user()->codigo_referido }}</div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <div style="font-size: 8rem; opacity: 0.9;">🫓</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <a href="{{ route('cliente.pedidos.index') }}" style="text-decoration: none;">
            <div class="stat-card animate-fade-in animate-delay-1">
                <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total_pedidos'] ?? 0) }}</div>
                <div class="stat-label">Pedidos Realizados</div>
                <div class="stat-detail">
                    <i class="bi bi-arrow-right-circle me-1"></i>
                    Ver historial completo
                </div>
            </div>
        </a>

        <div class="stat-card animate-fade-in animate-delay-2">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #722F37, #8b3c44);">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-value">${{ number_format($stats['total_gastado'] ?? 0, 0) }}</div>
            <div class="stat-label">Total Invertido</div>
            <div class="stat-detail">
                Gracias por tu confianza
            </div>
        </div>

        <div class="stat-card animate-fade-in animate-delay-3">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-value" id="contadorFavoritosMetric">{{ $productos_favoritos->count() }}</div>
            <div class="stat-label">Productos Favoritos</div>
            <div class="stat-detail">
                <i class="bi bi-heart me-1"></i>
                Tus preferidos
            </div>
        </div>

        <div class="stat-card animate-fade-in animate-delay-4">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_referidos'] ?? 0) }}</div>
            <div class="stat-label">Amigos Referidos</div>
            <div class="stat-detail">
                @if(($stats['total_referidos'] ?? 0) > 0)
                    <i class="bi bi-trophy me-1"></i>¡Excelente!
                @else
                    Invita amigos
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="quick-actions">
        <a href="{{ route('cliente.pedidos.create') }}" class="action-card">
            <div class="action-icon">
                <i class="bi bi-plus-circle-fill"></i>
            </div>
            <div class="action-title">Nuevo Pedido</div>
            <div class="action-description">
                Crea un nuevo pedido y recíbelo en la puerta de tu casa
            </div>
        </a>

        <a href="{{ route('catalogo.index') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="bi bi-shop"></i>
            </div>
            <div class="action-title">Ver Catálogo</div>
            <div class="action-description">
                Explora todos nuestros productos disponibles
            </div>
        </a>

        <a href="{{ route('cliente.pedidos.index') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="action-title">Mis Pedidos</div>
            <div class="action-description">
                Revisa el estado de tus pedidos
            </div>
        </a>
    </div>

    <!-- Pedidos Recientes -->
    <div class="pedidos-section">
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-clock-history"></i>
                Pedidos Recientes
            </div>
            @if($pedidos_recientes->count() > 0)
            <a href="{{ route('cliente.pedidos.index') }}" class="section-badge">
                Ver todos
            </a>
            @endif
        </div>

        @if($pedidos_recientes->count() > 0)
            @foreach($pedidos_recientes as $pedido)
            <div class="pedido-item">
                <div class="pedido-numero">
                    #{{ $loop->iteration }}
                </div>
                <div class="pedido-info">
                    <div class="pedido-title">Pedido {{ $pedido->numero_pedido }}</div>
                    <div class="pedido-meta">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-currency-dollar me-1"></i>
                        ${{ number_format($pedido->total_final, 0) }}
                    </div>
                </div>
                <span class="pedido-estado estado-{{ $pedido->estado }}">
                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                </span>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div class="empty-title">Aún no has realizado pedidos</div>
                <div class="empty-text">
                    ¡Es hora de probar nuestras deliciosas arepas!
                </div>
                <a href="{{ route('cliente.pedidos.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Hacer mi Primer Pedido
                </a>
            </div>
        @endif
    </div>

    <!-- Productos Destacados -->
    @if($productos_catalogo->count() > 0)
    <div class="pedidos-section">
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-star-fill"></i>
                Productos Destacados
            </div>
            <a href="{{ route('catalogo.index') }}" class="section-badge">
                Ver catálogo completo
            </a>
        </div>

        <div class="productos-grid">
            @foreach($productos_catalogo->take(6) as $producto)
            <div class="producto-card">
                <div class="producto-image">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
                    @else
                        🫓
                    @endif
                    @if($producto->destacado)
                        <span class="producto-badge">Destacado</span>
                    @endif
                </div>
                <div class="producto-content">
                    <div class="producto-nombre">{{ $producto->nombre }}</div>
                    <div class="producto-precio">${{ number_format($producto->precio, 0) }}</div>
                    <button class="btn-agregar" onclick="agregarAlCarrito('{{ $producto->_id }}')">
                        <i class="bi bi-cart-plus"></i>
                        Agregar al Pedido
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Floating Shopping Cart Button -->
<button class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg" 
        style="width: 60px; height: 60px; z-index: 1000;" 
        onclick="clienteDashboard.openCarrito()"
        id="btnCarritoFlotante">
    <i class="bi bi-cart3 fs-4"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
          id="carritoCount" style="display: none;">
        0
    </span>
</button>

<!-- Carrito Lateral -->
<div class="carrito-lateral" id="carritoLateral">
    <div class="carrito-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="bi bi-cart3 me-2"></i>
                Mi Carrito
            </h5>
            <button class="btn btn-sm btn-light" onclick="clienteDashboard.closeCarrito()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    
    <div class="carrito-items" id="carritoItems">
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <p class="text-muted mt-3">Tu carrito está vacío</p>
        </div>
    </div>
    
    <div class="carrito-footer">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <small class="text-muted d-block">Total:</small>
                <strong class="text-success fs-4" id="carritoTotal">$0</strong>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="clienteDashboard.vaciarCarrito()" id="btnVaciarCarrito" style="display:none;">
                <i class="bi bi-trash"></i> Vaciar
            </button>
        </div>
        <button class="btn btn-success w-100 mb-2" onclick="clienteDashboard.confirmarPedido()" id="btnConfirmarPedido">
            <i class="bi bi-check-circle me-2"></i>
            Confirmar Pedido
        </button>
        <button class="btn btn-outline-secondary w-100" onclick="clienteDashboard.closeCarrito()">
            Seguir comprando
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/cliente-dashboard-modern.js') }}?v={{ filemtime(public_path('js/pages/cliente-dashboard-modern.js')) }}"></script>
@endpush
                    $colores = [
                        'arepas' => 'primary',
                        'arepas-dulces' => 'warning',
                        'bebidas' => 'info',
                        'acompañantes' => 'danger',
                        'postres' => 'secondary',
                        'empanadas' => 'success'
                    ];
                    $color = $colores[$categoriaSlug] ?? 'primary';
                    
                    // Icono por categoría
                    $iconos = [
                        'arepas' => 'egg-fried',
                        'arepas-dulces' => 'cake2',
                        'bebidas' => 'cup-straw',
                        'acompañantes' => 'moisture',
                        'postres' => 'cake2',
                        'empanadas' => 'egg-fried'
                    ];
                    $icono = $iconos[$categoriaSlug] ?? 'egg-fried';
                    
                    // Formatear precio
                    $precioFormateado = number_format($producto->precio, 0, ',', '.');
                    
                    // Rating
                    $rating = $producto->rating_promedio ?? 0;
                    $estrellas = floor($rating);
                    $mediaEstrella = ($rating - $estrellas) >= 0.5;
                @endphp

                <div class="col-lg-4 col-md-6 mb-4 producto-item" 
                     data-categoria="{{ $categoriaSlug }}" 
                     data-nombre="{{ strtolower($producto->nombre) }}"
                     data-producto-id="{{ $producto->_id }}">
                    <div class="card producto-card h-100 {{ $producto->destacado ? 'border-warning' : '' }}">
                        @if($producto->destacado)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Destacado
                            </span>
                        </div>
                        @endif

                        <div class="card-body p-3">
                            <div class="producto-imagen mb-3">
                                @if($producto->imagen_principal)
                                    <img src="{{ asset('storage/' . $producto->imagen_principal) }}" 
                                         alt="{{ $producto->nombre }}"
                                         class="img-fluid rounded" 
                                         style="height: 120px; width: 100%; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/producto-default.jpg') }}';">
                                @else
                                    <div class="bg-gradient-{{ $color }} text-white d-flex align-items-center justify-content-center" 
                                         style="height: 120px; border-radius: 12px;">
                                        <i class="bi bi-{{ $icono }} fs-1"></i>
                                    </div>
                                @endif
                                
                                <button class="btn-favorito" onclick="toggleFavorito('{{ $producto->_id }}')">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title fw-bold mb-0">{{ $producto->nombre }}</h6>
                                @if($producto->codigo)
                                    <span class="badge bg-light text-dark small">{{ $producto->codigo }}</span>
                                @endif
                            </div>

                            <p class="text-muted small mb-2">{{ Str::limit($producto->descripcion, 60) }}</p>

                            <!-- Información adicional -->
                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                @if($producto->tiempo_preparacion)
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $producto->tiempo_preparacion }} min
                                    </small>
                                @endif
                                @if($producto->calorias)
                                    <small class="text-muted">
                                        <i class="bi bi-fire"></i> {{ $producto->calorias }} cal
                                    </small>
                                @endif
                                <small class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                    {{ $categoria }}
                                </small>
                            </div>

                            <!-- Stock -->
                            @if($producto->stock <= $producto->stock_minimo)
                                <div class="alert alert-warning py-1 px-2 mb-2 small">
                                    <i class="bi bi-exclamation-triangle"></i> ¡Últimas unidades! ({{ $producto->stock }})
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="precio-producto">
                                    <span class="fw-bold text-success fs-5">${{ $precioFormateado }}</span>
                                    @if($producto->precio_mayorista && $producto->precio_mayorista < $producto->precio)
                                        <br><small class="text-muted text-decoration-line-through">${{ number_format($producto->precio_mayorista, 0, ',', '.') }}</small>
                                    @endif
                                </div>
                                <div class="rating text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $estrellas)
                                            <i class="bi bi-star-fill"></i>
                                        @elseif($i == ($estrellas + 1) && $mediaEstrella)
                                            <i class="bi bi-star-half"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    @if($producto->total_reviews > 0)
                                        <small class="text-muted ms-1">({{ $producto->total_reviews }})</small>
                                    @endif
                                </div>
                            </div>

                            <button class="btn btn-primary btn-sm w-100 {{ $producto->stock <= 0 ? 'disabled' : '' }}" 
                                    onclick="agregarAlCarrito('{{ $producto->_id }}')"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}"
                                    data-imagen="{{ $producto->imagen_principal }}"
                                    data-stock="{{ $producto->stock }}"
                                    {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus me-1"></i>
                                {{ $producto->stock <= 0 ? 'Agotado' : 'Agregar al carrito' }}
                            </button>

                            <!-- Tags -->
                            @if(!empty($producto->tags))
                                <div class="mt-2">
                                    @foreach(array_slice($producto->tags, 0, 3) as $tag)
                                        <span class="badge bg-light text-dark small me-1">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                        <h5>No hay productos disponibles</h5>
                        <p class="mb-0">Estamos trabajando para traerte los mejores productos pronto.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Mensaje cuando no hay resultados en la búsqueda -->
        <div id="noResultados" class="text-center py-4" style="display: none;">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h6 class="text-muted mt-3">No se encontraron productos</h6>
            <p class="text-muted mb-3">Prueba con otros términos de búsqueda o categorías</p>
        </div>
    </div>
</div>

            <!-- Últimos Pedidos -->
            <div class="card animate-fade-in animate-delay-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bag-check me-2"></i>
                        Mis Últimos Pedidos
                    </h5>
                    <a href="{{ route('cliente.pedidos.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i>
                        Ver todos
                    </a>
                </div>
                {{-- Debug temporal --}}
                @if(config('app.debug'))
                <div class="alert alert-info m-3">
                    <strong>Debug:</strong> Total de pedidos: {{ $pedidos_recientes->count() }}
                    @if($pedidos_recientes->count() > 0)
                        | Primer pedido: {{ $pedidos_recientes->first()->numero_pedido ?? 'N/A' }}
                    @endif
                </div>
                @endif
                <div class="card-body">
                    @if($pedidos_recientes->count() > 0)
                        <div class="row">
                            @foreach($pedidos_recientes as $pedido)
                            <div class="col-md-6 mb-3">
                                <div class="order-card card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Pedido #{{ $pedido->numero_pedido }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3"></i>
                                                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            @php
                                                $statusColors = [
                                                    'pendiente' => 'warning',
                                                    'confirmado' => 'info',
                                                    'en_preparacion' => 'primary',
                                                    'listo' => 'success',
                                                    'en_camino' => 'info',
                                                    'entregado' => 'success',
                                                    'cancelado' => 'danger'
                                                ];
                                                $statusIcons = [
                                                    'pendiente' => 'clock-history',
                                                    'confirmado' => 'check-circle',
                                                    'en_preparacion' => 'hourglass-split',
                                                    'listo' => 'check-circle-fill',
                                                    'en_camino' => 'truck',
                                                    'entregado' => 'check-all',
                                                    'cancelado' => 'x-circle'
                                                ];
                                                $statusColor = $statusColors[$pedido->estado] ?? 'secondary';
                                                $statusIcon = $statusIcons[$pedido->estado] ?? 'circle';
                                            @endphp
                                            <span class="status-badge bg-{{ $statusColor }} text-white">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Información adicional del pedido -->
                                        @php
                                            // Intentar obtener productos de diferentes campos posibles
                                            $productos_pedido = $pedido->productos ?? $pedido->items ?? $pedido->detalles ?? [];
                                            if (is_object($productos_pedido)) {
                                                $productos_pedido = (array) $productos_pedido;
                                            }
                                            $total_productos = is_array($productos_pedido) ? count($productos_pedido) : 0;
                                        @endphp
                                        
                                        @if($total_productos > 0)
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-box-seam"></i>
                                                {{ $total_productos }} {{ $total_productos == 1 ? 'producto' : 'productos' }}
                                            </small>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @foreach(array_slice($productos_pedido, 0, 3) as $producto)
                                                    @php
                                                        $nombreProducto = 'Producto';
                                                        if (is_array($producto)) {
                                                            $nombreProducto = $producto['nombre'] ?? $producto['producto_nombre'] ?? $producto['nombre_producto'] ?? 'Producto';
                                                        } elseif (is_object($producto)) {
                                                            $nombreProducto = $producto->nombre ?? $producto->producto_nombre ?? $producto->nombre_producto ?? 'Producto';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-light text-dark small">{{ $nombreProducto }}</span>
                                                @endforeach
                                                @if($total_productos > 3)
                                                    <span class="badge bg-light text-dark small">+{{ $total_productos - 3 }} más</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-end mt-3">
                                            <div>
                                                <small class="text-muted d-block">Total pagado</small>
                                                <div class="fw-bold text-success fs-5">${{ number_format($pedido->total_final, 0) }}</div>
                                            </div>
                                            <a href="{{ route('cliente.pedidos.show', $pedido->_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>
                                                Ver detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <h6 class="text-muted mt-3">No tienes pedidos aún</h6>
                            <p class="text-muted mb-3">¡Haz tu primer pedido y disfruta de nuestras deliciosas arepas!</p>
                            <a href="{{ route('cliente.pedidos.create') }}" class="btn btn-primary">
                                <i class="bi bi-cart-plus me-2"></i>
                                Hacer mi primer pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 mb-4">
            <!-- Información Personal -->
            <div class="card mb-3 animate-fade-in animate-delay-1">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Mi Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Nombre completo</small>
                        <div class="fw-medium">{{ auth()->user()->name }} {{ auth()->user()->apellidos ?? '' }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <div class="fw-medium">{{ auth()->user()->email }}</div>
                    </div>
                    
                    @if(auth()->user()->telefono)
                    <div class="mb-2">
                        <small class="text-muted">Teléfono</small>
                        <div class="fw-medium">{{ auth()->user()->telefono }}</div>
                    </div>
                    @endif
                    
                    @if(auth()->user()->direccion)
                    <div class="mb-3">
                        <small class="text-muted">Dirección</small>
                        <div class="fw-medium">{{ auth()->user()->direccion }}</div>
                        @if(auth()->user()->ciudad)
                        <small class="text-muted">{{ auth()->user()->ciudad }}</small>
                        @endif
                    </div>
                    @endif
                    
                    <button class="btn btn-outline-primary btn-sm w-100" onclick="mostrarEditarPerfil()">
                        <i class="bi bi-pencil me-1"></i>
                        Actualizar información
                    </button>
                </div>
            </div>

            <!-- Programa de Referidos -->
            @if(auth()->user()->codigo_referido)
            <div class="card mb-3 animate-fade-in animate-delay-2">
                <div class="card-body text-center" style="background: linear-gradient(135deg, #fef5f5, #f5e6d3); border-radius: 12px; border: 2px solid #722F37;">
                    <i class="bi bi-gift text-primary fs-1 mb-2"></i>
                    <h6 class="fw-bold mb-2">¡Refiere y Gana!</h6>
                    <p class="small mb-3">Comparte tu código con amigos y recibe beneficios especiales</p>
                    
                    <div class="bg-white p-2 rounded mb-3">
                        <div class="fw-bold text-primary">{{ auth()->user()->codigo_referido }}</div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="fw-bold text-success">{{ $stats['total_referidos'] ?? 0 }}</div>
                            <small class="text-muted">Referidos</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-info">$0</div>
                            <small class="text-muted">Ahorrado</small>
                        </div>
                    </div>
                    
                    <button class="btn btn-warning btn-sm" onclick="shareReferralCode()">
                        <i class="bi bi-share me-1"></i>
                        Compartir código
                    </button>
                </div>
            </div>
            @endif

            <!-- Productos Favoritos -->
            <div class="card animate-fade-in animate-delay-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-heart-fill me-2"></i>
                        Tus Favoritos
                    </h6>
                </div>
                {{-- Debug temporal --}}
                @if(config('app.debug'))
                <div class="alert alert-warning m-3">
                    <strong>Debug Favoritos:</strong> Total: {{ $productos_favoritos->count() }}
                    @if(auth()->user()->favoritos)
                        | IDs guardados: {{ count(auth()->user()->favoritos ?? []) }}
                        @if(count(auth()->user()->favoritos ?? []) > 0)
                            | Primer ID: {{ auth()->user()->favoritos[0] ?? 'N/A' }}
                        @endif
                    @else
                        | Campo favoritos no existe en usuario
                    @endif
                </div>
                @endif
                <div class="card-body">
                    @if($productos_favoritos->count() > 0)
                        @foreach($productos_favoritos->take(3) as $producto)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}" data-favorito-id="{{ $producto->_id }}">
                            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                <small class="text-muted">${{ number_format($producto->precio, 0) }}</small>
                                @if($producto->stock !== null)
                                    <small class="d-block {{ $producto->stock <= 0 ? 'text-danger' : ($producto->stock <= 5 ? 'text-warning' : 'text-muted') }}">
                                        <i class="bi bi-box-seam"></i> Stock: {{ $producto->stock }}
                                    </small>
                                @endif
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary {{ $producto->stock <= 0 ? 'disabled' : '' }}" 
                                        onclick="agregarAlCarritoFromFavorito('{{ $producto->_id }}')"
                                        data-producto-id="{{ $producto->_id }}"
                                        data-nombre="{{ $producto->nombre }}"
                                        data-precio="{{ $producto->precio }}"
                                        data-imagen="{{ $producto->imagen_principal ?? '' }}"
                                        data-stock="{{ $producto->stock ?? 0 }}"
                                        {{ $producto->stock <= 0 ? 'disabled' : '' }}
                                        title="{{ $producto->stock <= 0 ? 'Producto agotado' : 'Agregar al carrito' }}">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarFavorito('{{ $producto->_id }}')"
                                        title="Quitar de favoritos">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($productos_favoritos->count() > 3)
                        <div class="text-center mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Ver Todos los Favoritos')">
                                Ver todos ({{ $productos_favoritos->count() }})
                            </button>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-heart fs-3 text-muted"></i>
                            <p class="text-muted mb-2">No tienes productos favoritos</p>
                            <button class="btn btn-sm btn-primary" onclick="document.getElementById('buscarProducto').scrollIntoView({behavior:'smooth'})">
                                Explorar productos
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carrito Sidebar -->
<div id="carritoBackdrop" class="carrito-backdrop" onclick="clienteDashboard.closeCarrito()"></div>
<div id="carritoSidebar">
    <div class="carrito-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-cart3 me-2"></i>
                Mi Carrito
            </h5>
            <button class="btn btn-sm btn-light" onclick="clienteDashboard.closeCarrito()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    
    <div class="carrito-items" id="carritoItems">
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <p class="text-muted mt-3">Tu carrito está vacío</p>
        </div>
    </div>
    
    <div class="carrito-footer">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <small class="text-muted d-block">Total:</small>
                <strong class="text-success fs-4" id="carritoTotal">$0</strong>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="clienteDashboard.vaciarCarrito()" id="btnVaciarCarrito" style="display:none;">
                <i class="bi bi-trash"></i> Vaciar
            </button>
        </div>
        <button class="btn btn-success w-100 mb-2" onclick="clienteDashboard.confirmarPedido()" id="btnConfirmarPedido">
            <i class="bi bi-check-circle me-2"></i>
            Confirmar Pedido
        </button>
        <button class="btn btn-outline-secondary w-100" onclick="clienteDashboard.closeCarrito()">
            Seguir comprando
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/cliente-dashboard-modern.js') }}?v={{ filemtime(public_path('js/pages/cliente-dashboard-modern.js')) }}"></script>
@endpush