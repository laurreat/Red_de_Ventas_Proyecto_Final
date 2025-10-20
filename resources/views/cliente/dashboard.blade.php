@extends('layouts.cliente')

@section('title', ' - Mi Dashboard')
@section('header-title', 'Mi Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/cliente-dashboard-optimized.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Welcome Hero Card - Mejorado -->
    <div class="welcome-card animate-fade-in">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="welcome-badge mb-3">
                        <i class="bi bi-stars"></i>
                        Cliente Preferente
                    </div>
                    <h1 class="welcome-title mb-3">
                        ¡Hola, {{ auth()->user()->name }}! 
                        <span class="wave">👋</span>
                    </h1>
                    <p class="welcome-subtitle mb-4">
                        Bienvenido a tu espacio personal de Arepa la Llanerita. Descubre nuestros productos y realiza tus pedidos fácilmente.
                    </p>
                    
                    @if(auth()->user()->codigo_referido)
                    <div class="referral-code-box">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="referral-label">Tu Código de Referido</div>
                                <div class="referral-code">{{ auth()->user()->codigo_referido }}</div>
                            </div>
                            <button class="btn btn-light btn-sm" onclick="copiarCodigo('{{ auth()->user()->codigo_referido }}')">
                                <i class="bi bi-clipboard"></i>
                                Copiar
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="welcome-illustration">
                        <div class="arepa-icon-wrapper" style="width: 250px; height: 250px; margin: 0 auto; position: relative;">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 100%; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15)); display: block;">
                                <defs>
                                    <linearGradient id="arepaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#FFE5B4;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#F4A460;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <!-- Arepa circular -->
                                <circle cx="100" cy="100" r="80" fill="url(#arepaGradient)" stroke="#D2691E" stroke-width="3"/>
                                <!-- Textura de arepa -->
                                <circle cx="70" cy="80" r="8" fill="#E6C79C" opacity="0.6"/>
                                <circle cx="130" cy="90" r="10" fill="#E6C79C" opacity="0.6"/>
                                <circle cx="90" cy="120" r="7" fill="#E6C79C" opacity="0.6"/>
                                <circle cx="120" cy="115" r="9" fill="#E6C79C" opacity="0.6"/>
                                <circle cx="100" cy="70" r="6" fill="#E6C79C" opacity="0.6"/>
                                <circle cx="110" cy="135" r="8" fill="#E6C79C" opacity="0.6"/>
                                <!-- Marca del centro -->
                                <circle cx="100" cy="100" r="5" fill="#D2691E" opacity="0.3"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas - Grid Mejorado -->
    <div class="stats-grid">
        <a href="{{ route('cliente.pedidos.index') }}" class="stat-card animate-fade-in" style="--delay: 0.1s">
            <div class="stat-icon stat-icon-success">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Mis Pedidos</div>
                <div class="stat-value">{{ $stats['total_pedidos'] ?? 0 }}</div>
            </div>
        </a>

        <div class="stat-card animate-fade-in" style="--delay: 0.2s">
            <div class="stat-icon stat-icon-primary">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Invertido</div>
                <div class="stat-value">${{ number_format($stats['total_gastado'] ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="stat-card animate-fade-in" style="--delay: 0.3s">
            <div class="stat-icon stat-icon-warning">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Favoritos</div>
                <div class="stat-value" id="contadorFavoritosMetric">{{ $productos_favoritos->count() }}</div>
            </div>
        </div>

        <div class="stat-card animate-fade-in" style="--delay: 0.4s">
            <div class="stat-icon stat-icon-info">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Referidos</div>
                <div class="stat-value">{{ $stats['total_referidos'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Catálogo de Productos - SECCIÓN PRINCIPAL -->
    <div class="content-card catalogo-section">
        <div class="content-card-header">
            <div>
                <h3 class="content-card-title">
                    <i class="bi bi-shop"></i>
                    Nuestro Catálogo
                </h3>
                <p class="content-card-subtitle">Selecciona tus productos favoritos y agrégalos al carrito</p>
            </div>
            <div class="categoria-filter">
                <button class="filter-btn active" data-categoria="all">
                    <i class="bi bi-grid-fill"></i> Todos
                </button>
                <button class="filter-btn" data-categoria="favoritos">
                    <i class="bi bi-heart-fill"></i> Favoritos
                </button>
                @if($categorias->count() > 0)
                @foreach($categorias as $categoria)
                <button class="filter-btn" data-categoria="{{ $categoria }}">
                    {{ $categoria }}
                </button>
                @endforeach
                @endif
            </div>
        </div>

        <div class="content-card-body">
            @if($productos_catalogo->count() > 0)
                <div class="productos-grid" id="productosGrid">
                    @foreach($productos_catalogo as $producto)
                    <div class="producto-card" data-categoria="{{ $producto->categoria_data['nombre'] ?? 'Sin Categoría' }}">
                        <div class="producto-image-wrapper">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                     alt="{{ $producto->nombre }}" 
                                     class="producto-image"
                                     loading="lazy">
                            @else
                                <div class="producto-image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                            @if($producto->destacado)
                                <span class="producto-badge badge-destacado">
                                    <i class="bi bi-star-fill"></i>
                                    Destacado
                                </span>
                            @endif
                            @if($producto->stock <= 5 && $producto->stock > 0)
                                <span class="producto-badge badge-stock">
                                    ¡Últimas {{ $producto->stock }} unidades!
                                </span>
                            @endif
                            @if($producto->stock <= 0)
                                <span class="producto-badge badge-agotado">
                                    Agotado
                                </span>
                            @endif
                            <button class="producto-favorite @if(in_array($producto->_id, $productos_favoritos->pluck('_id')->toArray())) active @endif" 
                                    onclick="toggleFavorito('{{ $producto->_id }}', this)"
                                    data-producto-id="{{ $producto->_id }}">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </div>
                        <div class="producto-info">
                            <h4 class="producto-name">{{ $producto->nombre }}</h4>
                            @if($producto->descripcion)
                            <p class="producto-description">{{ Str::limit($producto->descripcion, 80) }}</p>
                            @endif
                            <div class="producto-meta">
                                <span class="producto-categoria">
                                    <i class="bi bi-tag"></i>
                                    {{ $producto->categoria_data['nombre'] ?? 'General' }}
                                </span>
                                @if(isset($producto->veces_vendido) && $producto->veces_vendido > 0)
                                <span class="producto-vendidos">
                                    <i class="bi bi-fire"></i>
                                    {{ $producto->veces_vendido }} vendidos
                                </span>
                                @endif
                            </div>
                            <div class="producto-footer">
                                <div class="producto-price-box">
                                    <div class="producto-price">${{ number_format((float)$producto->precio, 0) }}</div>
                                    <small class="producto-stock">
                                        @if($producto->stock > 5)
                                            <i class="bi bi-check-circle-fill text-success"></i> Disponible
                                        @elseif($producto->stock > 0)
                                            <i class="bi bi-exclamation-circle-fill text-warning"></i> Pocas unidades
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger"></i> Sin stock
                                        @endif
                                    </small>
                                </div>
                                @if($producto->stock > 0)
                                <button class="btn-add-cart" 
                                        onclick="agregarAlCarrito('{{ $producto->_id }}', '{{ $producto->nombre }}', {{ (float)$producto->precio }}, '{{ $producto->imagen ?? '' }}', {{ $producto->stock }})">
                                    <i class="bi bi-cart-plus"></i>
                                    Agregar
                                </button>
                                @else
                                <button class="btn-add-cart" disabled>
                                    <i class="bi bi-x-circle"></i>
                                    Agotado
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h4 class="empty-title">No hay productos disponibles</h4>
                    <p class="empty-text">
                        Pronto tendremos nuevos productos para ti
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pedidos Recientes - Sección Compacta -->
    @if($pedidos_recientes->count() > 0)
    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h3 class="content-card-title">
                    <i class="bi bi-clock-history"></i>
                    Mis Últimos Pedidos
                </h3>
                <p class="content-card-subtitle">Historial de pedidos recientes</p>
            </div>
            <a href="{{ route('cliente.pedidos.index') }}" class="btn btn-outline-primary btn-sm">
                Ver todos
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="content-card-body">
            <div class="pedidos-list">
                @foreach($pedidos_recientes as $pedido)
                <div class="pedido-item">
                    <div class="pedido-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="pedido-details">
                        <div class="pedido-title">#{{ $pedido->numero_pedido }}</div>
                        <div class="pedido-meta">
                            <span><i class="bi bi-calendar3"></i> {{ $pedido->created_at->format('d/m/Y') }}</span>
                            <span class="separator">•</span>
                            <span><i class="bi bi-currency-dollar"></i> ${{ number_format((float)$pedido->total_final, 0) }}</span>
                        </div>
                    </div>
                    <span class="pedido-status pedido-status-{{ $pedido->estado }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Floating Shopping Cart Button -->
<button class="floating-cart-btn" 
        onclick="clienteDashboard.openCarrito()"
        id="btnCarritoFlotante"
        aria-label="Abrir carrito">
    <i class="bi bi-cart3-fill"></i>
    <span class="cart-badge" id="carritoCount" style="display: none;">0</span>
</button>

<!-- Carrito Lateral -->
<div class="carrito-sidebar" id="carritoLateral">
    <div class="carrito-header">
        <h3 class="carrito-title">
            <i class="bi bi-cart3"></i>
            Mi Carrito
        </h3>
        <button class="carrito-close" onclick="clienteDashboard.closeCarrito()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <div class="carrito-body" id="carritoItems">
        <div class="carrito-empty">
            <div class="carrito-empty-icon">
                <i class="bi bi-cart-x"></i>
            </div>
            <p class="carrito-empty-text">Tu carrito está vacío</p>
            <p class="carrito-empty-subtext">¡Agrega productos para comenzar!</p>
        </div>
    </div>
    
    <div class="carrito-footer">
        <div class="carrito-total">
            <div>
                <span class="carrito-total-label">Total:</span>
                <span class="carrito-total-value" id="carritoTotal">$0</span>
            </div>
            <button class="btn-clear-cart" onclick="clienteDashboard.vaciarCarrito()" id="btnVaciarCarrito" style="display:none;">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <button class="btn-checkout" onclick="clienteDashboard.confirmarPedido()" id="btnConfirmarPedido">
            <i class="bi bi-check-circle"></i>
            Confirmar Pedido
        </button>
        <button class="btn-continue" onclick="clienteDashboard.closeCarrito()">
            <i class="bi bi-arrow-left"></i>
            Seguir comprando
        </button>
    </div>
</div>

<!-- Overlay del carrito -->
<div class="carrito-overlay" id="carritoOverlay" onclick="clienteDashboard.closeCarrito()"></div>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/cliente-dashboard-optimized.js') }}?v={{ time() }}"></script>
<script>
// Instanciar manager del dashboard
const clienteDashboard = new ClienteDashboardManager();

// Función para copiar código de referido
function copiarCodigo(codigo) {
    navigator.clipboard.writeText(codigo).then(() => {
        clienteDashboard.showToast('Código copiado al portapapeles', 'success');
    }).catch(err => {
        console.error('Error al copiar:', err);
        clienteDashboard.showToast('No se pudo copiar el código', 'error');
    });
}

// Función para toggle favorito
async function toggleFavorito(productoId, btnElement) {
    try {
        const isFavorito = btnElement.classList.contains('active');
        const url = isFavorito ? '/cliente/favoritos/eliminar' : '/cliente/favoritos/agregar';
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ producto_id: productoId })
        });

        const data = await response.json();
        
        if (data.success) {
            btnElement.classList.toggle('active');
            document.getElementById('contadorFavoritosMetric').textContent = data.total_favoritos;
            clienteDashboard.showToast(data.message, 'success');
        } else {
            clienteDashboard.showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        clienteDashboard.showToast('Error al actualizar favoritos', 'error');
    }
}

// Función para agregar al carrito
function agregarAlCarrito(productoId, nombre, precio, imagen, stock) {
    clienteDashboard.agregarAlCarrito(productoId, nombre, precio, imagen, stock);
}

// Filtros de categoría
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productos = document.querySelectorAll('.producto-card');
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const categoria = this.dataset.categoria;
            
            // Actualizar botón activo
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar productos con animación
            productos.forEach((producto, index) => {
                const esFavorito = producto.querySelector('.producto-favorite.active') !== null;
                let mostrar = false;
                
                if (categoria === 'all') {
                    mostrar = true;
                } else if (categoria === 'favoritos') {
                    mostrar = esFavorito;
                } else {
                    mostrar = producto.dataset.categoria === categoria;
                }
                
                if (mostrar) {
                    producto.style.display = 'block';
                    producto.style.animation = 'none';
                    setTimeout(() => {
                        producto.style.animation = `fadeIn 0.4s ease-out ${index * 0.05}s backwards`;
                    }, 10);
                } else {
                    producto.style.display = 'none';
                }
            });
            
            // Mostrar mensaje si no hay favoritos
            if (categoria === 'favoritos') {
                const favoritosVisibles = Array.from(productos).filter(p => 
                    p.style.display !== 'none'
                ).length;
                
                if (favoritosVisibles === 0) {
                    clienteDashboard.showToast('No tienes productos favoritos aún. ¡Marca algunos con el corazón!', 'info');
                }
            }
        });
    });
});
</script>
@endpush
