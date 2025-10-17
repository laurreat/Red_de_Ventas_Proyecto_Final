@extends('layouts.app')

@section('title', '- Mi Cuenta')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/cliente-dashboard-modern.css') }}?v={{ filemtime(public_path('css/pages/cliente-dashboard-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Bienvenida Hero -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 fw-bold">¡Bienvenido, {{ auth()->user()->name }}! 🍯</h1>
                            <p class="mb-0 opacity-90">
                                Disfruta de nuestras deliciosas arepas y productos tradicionales. 
                                @if(auth()->user()->referido_por)
                                Referido por: <strong>
                                    @php
                                        $referidor = \App\Models\User::find(auth()->user()->referido_por);
                                    @endphp
                                    {{ $referidor->name ?? 'Usuario' }}
                                </strong>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="fs-1">🫓</div>
                            @if(auth()->user()->codigo_referido)
                            <div class="mt-2">
                                <small class="opacity-75">Tu código:</small>
                                <div class="fw-bold fs-5">{{ auth()->user()->codigo_referido }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Cliente -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-1">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check text-success fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_pedidos']) }}</div>
                    <div class="metric-label">Pedidos Realizados</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-2">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-primary fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['total_gastado'], 0) }}</div>
                    <div class="metric-label">Total Comprado</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-3">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill text-warning fs-1 mb-3"></i>
                    <div class="metric-value">{{ $productos_favoritos->count() }}</div>
                    <div class="metric-label">Productos Favoritos</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-4">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_referidos']) }}</div>
                    <div class="metric-label">Amigos Referidos</div>
                    @if($stats['total_referidos'] > 0)
                    <small class="text-success">¡Gracias por recomendarnos!</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-xl-8 mb-4">
            <!-- Acciones Rápidas -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-fill me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="clienteDashboard.toggleCarrito()">
                                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Ver Carrito</div>
                                <small>Productos seleccionados</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="showComingSoon('Ver Menú')">
                                <i class="bi bi-book fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Ver Menú</div>
                                <small>Productos disponibles</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="showComingSoon('Mis Pedidos')">
                                <i class="bi bi-clock-history fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Mis Pedidos</div>
                                <small>Historial completo</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catálogo de Productos DINÁMICO -->
<div class="card mb-4 animate-fade-in animate-delay-1">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>
            Nuestro Catálogo
        </h5>
        <button class="btn btn-sm btn-primary" onclick="clienteDashboard.toggleCarrito()">
            <i class="bi bi-cart3 me-1"></i>
            Carrito
            <span class="badge bg-danger ms-1 carrito-count" style="display:none;">0</span>
        </button>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar productos...">
                </div>
            </div>
            <div class="col-md-4 mt-2 mt-md-0">
                <select class="form-select" id="filtroCategoria">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ strtolower(str_replace(' ', '-', $categoria)) }}">{{ $categoria }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid de productos DINÁMICO -->
        <div class="row" id="gridProductos">
            @forelse($productos_catalogo as $producto)
                @php
                    $categoria = $producto->categoria_data['nombre'] ?? 'Sin Categoría';
                    $categoriaSlug = strtolower(str_replace(' ', '-', $categoria));
                    
                    // Colores por categoría
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

                            <button class="btn btn-primary btn-sm w-100" 
                                    onclick="agregarAlCarrito('{{ $producto->_id }}')"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}"
                                    data-imagen="{{ $producto->imagen_principal }}">
                                <i class="bi bi-cart-plus me-1"></i>
                                Agregar al carrito
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
                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Historial Completo')">
                        Ver todos
                    </a>
                </div>
                <div class="card-body">
                    @if($pedidos_recientes->count() > 0)
                        <div class="row">
                            @foreach($pedidos_recientes as $pedido)
                            <div class="col-md-6 mb-3">
                                <div class="order-card card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold">Pedido #{{ $pedido->numero_pedido }}</div>
                                                <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
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
                                            @endphp
                                            <span class="status-badge bg-{{ $statusColors[$pedido->estado] ?? 'secondary' }} text-white">
                                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div>
                                                <small class="text-muted">Total pagado</small>
                                                <div class="fw-bold text-success fs-5">${{ number_format($pedido->total_final, 0) }}</div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Detalles del Pedido')">
                                                Ver detalles
                                            </button>
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
                            <button class="btn btn-primary" onclick="clienteDashboard.toggleCarrito()">
                                <i class="bi bi-cart-plus me-2"></i>
                                Hacer mi primer pedido
                            </button>
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
                    
                    <button class="btn btn-outline-primary btn-sm w-100" onclick="showComingSoon('Actualizar Perfil')">
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
                            <div class="fw-bold text-success">{{ $stats['total_referidos'] }}</div>
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
                <div class="card-body">
                    @if($productos_favoritos->count() > 0)
                        @foreach($productos_favoritos->take(3) as $producto)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                <small class="text-muted">${{ number_format($producto->precio, 0) }}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="agregarAlCarrito({{ $producto->id }})">
                                <i class="bi bi-cart-plus"></i>
                            </button>
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
        <div class="d-flex justify-content-between mb-3">
            <strong>Total:</strong>
            <strong class="text-success fs-5" id="carritoTotal">$0</strong>
        </div>
        <button class="btn btn-success w-100 mb-2" onclick="clienteDashboard.confirmarPedido()">
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