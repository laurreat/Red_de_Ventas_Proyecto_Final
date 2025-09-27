@extends('layouts.app')

@section('title', '- Mi Cuenta')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/cliente-dashboard.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Bienvenida -->
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
                                <div class="fw-bold">{{ auth()->user()->codigo_referido }}</div>
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
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check text-success fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_pedidos']) }}</div>
                    <div class="metric-label">Pedidos Realizados</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-primary fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['total_gastado'], 0) }}</div>
                    <div class="metric-label">Total Comprado</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill text-warning fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format(4) }}</div>
                    <div class="metric-label">Productos Favoritos</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
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
        <!-- Acciones Rápidas -->
        <div class="col-xl-8 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-fill me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="#" class="quick-action" onclick="showComingSoon('Realizar Pedido')">
                                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Hacer Pedido</div>
                                <small>Ordenar arepas</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="#" class="quick-action" onclick="showComingSoon('Ver Menú')">
                                <i class="bi bi-book fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Ver Menú</div>
                                <small>Productos disponibles</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="#" class="quick-action" onclick="showComingSoon('Mis Pedidos')">
                                <i class="bi bi-clock-history fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Mis Pedidos</div>
                                <small>Historial completo</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catálogo de Productos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                        Nuestro Catálogo
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filtros y búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar productos..." onkeyup="filtrarProductos()">
                            </div>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-0">
                            <select class="form-select" id="filtroCategoria" onchange="filtrarProductos()">
                                <option value="">Todas las categorías</option>
                                <option value="arepas">Arepas</option>
                                <option value="bebidas">Bebidas</option>
                                <option value="acompanantes">Acompañantes</option>
                                <option value="postres">Postres</option>
                            </select>
                        </div>
                    </div>

                    <!-- Grid de productos -->
                    <div class="row" id="gridProductos">
                        <!-- Arepas -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa reina pepiada">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-primary text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(1)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Reina Pepiada</h6>
                                    <p class="text-muted small mb-2">Pollo desmenuzado, aguacate, mayonesa y cilantro</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$8,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(1)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa pelua">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-warning text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(2)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Pelua</h6>
                                    <p class="text-muted small mb-2">Carne mechada y queso amarillo derretido</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$9,000</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(2)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa catira">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-success text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(3)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Catira</h6>
                                    <p class="text-muted small mb-2">Pollo desmenuzado con queso amarillo</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$8,000</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(3)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Bebidas -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="bebidas" data-nombre="chicha de arroz">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-info text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-cup-straw fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(4)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Chicha de Arroz</h6>
                                    <p class="text-muted small mb-2">Bebida tradicional cremosa y refrescante</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$3,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(4)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Acompañantes -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="acompanantes" data-nombre="tequeños">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-danger text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-moisture fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(5)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Tequeños (6 unidades)</h6>
                                    <p class="text-muted small mb-2">Deditos de queso envueltos en masa crujiente</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$6,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(5)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Postres -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="postres" data-nombre="quesillo">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-secondary text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-cake2 fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(6)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Quesillo</h6>
                                    <p class="text-muted small mb-2">Postre tradicional de leche condensada</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$4,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(6)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="noResultados" class="text-center py-4" style="display: none;">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <h6 class="text-muted">No se encontraron productos</h6>
                        <p class="text-muted mb-3">Prueba con otros términos de búsqueda o categorías</p>
                    </div>
                </div>
            </div>

            <!-- Últimos Pedidos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bag-check me-2"></i>
                        Mis Últimos Pedidos
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Historial Completo')">
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
                                                <div class="fw-bold">Pedido {{ $pedido->numero_pedido }}</div>
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
                                                <div class="fw-bold text-success fs-5">${{ format_currency($pedido->total_final) }}</div>
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
                            <h6 class="text-muted">No tienes pedidos aún</h6>
                            <p class="text-muted mb-3">¡Haz tu primer pedido y disfruta de nuestras deliciosas arepas!</p>
                            <button class="btn btn-primary" onclick="showComingSoon('Realizar Pedido')">
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
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Mi Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Nombre completo</small>
                        <div class="fw-medium">{{ auth()->user()->name }} {{ auth()->user()->apellidos }}</div>
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
            <div class="card mb-3">
                <div class="card-body text-center" style="background: linear-gradient(135deg, var(--arepa-cream), #f5f0f0); border-radius: 12px; border: 2px solid var(--arepa-light-burgundy);">
                    <i class="bi bi-gift text-primary fs-1 mb-2"></i>
                    <h6 class="fw-bold mb-2">¡Refiere y Gana!</h6>
                    <p class="small mb-3">Comparte tu código con amigos y recibe beneficios especiales</p>
                    
                    <div class="bg-white p-2 rounded mb-3">
                        <div class="fw-bold text-primary">{{ auth()->user()->codigo_referido }}</div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="fw-bold text-success">{{ $stats['referidos_realizados'] }}</div>
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
            <div class="card">
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
                            <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Agregar al Carrito')">
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
                            <button class="btn btn-sm btn-primary" onclick="showComingSoon('Ver Productos')">
                                Explorar productos
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function shareReferralCode() {
        const code = '{{ auth()->user()->codigo_referido ?? "" }}';
        const text = `¡Prueba las deliciosas arepas de Arepa la Llanerita! Usa mi código de referido: ${code} y obtén beneficios especiales.`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Arepa la Llanerita - ¡Únete con mi código!',
                text: text,
            });
        } else {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Texto copiado al portapapeles', 'success');
            }).catch(function() {
                showToast('No se pudo copiar el código', 'error');
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animaciones de entrada
        document.querySelectorAll('.metric-card, .order-card').forEach(function(card, index) {
            setTimeout(function() {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                setTimeout(function() {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            }, 0);
        });
    });
</script>
@endpush