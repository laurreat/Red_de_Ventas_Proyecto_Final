@extends('layouts.vendedor')

@section('title', 'Detalles del Producto')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-professional.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-professional.css')) }}">
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
@endpush

@section('content')
<!-- Header Hero Mejorado -->
<div class="pedido-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="pedidos-header-icon-badge">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1 class="pedidos-header-title">
                {{ $producto->nombre }}
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-info-circle me-2"></i>
                Información detallada del producto
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                <a href="{{ route('vendedor.productos.index') }}" class="pedidos-btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="row g-4 fade-in-up animate-delay-1">
    <!-- Columna Izquierda - Imagen -->
    <div class="col-lg-5">
        <div class="pedido-detail-card">
            <div class="pedido-detail-body" style="padding: 0; overflow: hidden;">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                         alt="{{ $producto->nombre }}" 
                         class="producto-detail-image"
                         id="main-product-image"
                         onerror="this.parentElement.innerHTML='<div class=\'producto-detail-placeholder\'><i class=\'bi bi-box-seam\'></i><p>Sin imagen</p></div>';">
                @else
                    <div class="producto-detail-placeholder">
                        <i class="bi bi-box-seam"></i>
                        <p>Sin imagen disponible</p>
                    </div>
                @endif
                
                <!-- Badge de Estado -->
                <div class="producto-detail-badges">
                    @if($producto->stock == 0)
                        <span class="producto-status-badge badge-agotado">
                            <i class="bi bi-x-circle-fill"></i>
                            Agotado
                        </span>
                    @elseif($producto->stock <= ($producto->stock_minimo ?? 5))
                        <span class="producto-status-badge badge-bajo-stock">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Bajo Stock
                        </span>
                    @elseif($producto->activo)
                        <span class="producto-status-badge badge-disponible">
                            <i class="bi bi-check-circle-fill"></i>
                            Disponible
                        </span>
                    @else
                        <span class="producto-status-badge badge-inactivo">
                            <i class="bi bi-dash-circle-fill"></i>
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Galería de imágenes adicionales -->
            @if(!empty($producto->imagenes_adicionales) && is_array($producto->imagenes_adicionales))
                <div class="producto-gallery-thumbs">
                    @foreach($producto->imagenes_adicionales as $imagen)
                        <img src="{{ asset('storage/' . $imagen) }}" 
                             alt="{{ $producto->nombre }}" 
                             class="producto-thumb"
                             onclick="document.getElementById('main-product-image').src = this.src"
                             onerror="this.style.display='none';">
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <!-- Columna Derecha - Detalles -->
    <div class="col-lg-7">
        <!-- Card de Información Principal -->
        <div class="pedido-detail-card fade-in-up">
            <div class="pedido-detail-header">
                <i class="bi bi-info-circle"></i>
                <h3 class="pedido-detail-title">Información del Producto</h3>
            </div>
            <div class="pedido-detail-body">
                <!-- Precio Grande -->
                <!-- Precio Grande -->
                <div class="producto-precio-section">
                    <div class="producto-precio-label">Precio</div>
                    <div class="producto-precio-value">
                        ${{ number_format(to_float($producto->precio), 0) }}
                    </div>
                </div>
                
                <!-- Grid de Información -->
                <div class="pedido-info-grid">
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">
                            <i class="bi bi-folder"></i>
                            Categoría
                        </div>
                        <div class="pedido-info-value">
                            @if(isset($producto->categoria_data['nombre']))
                                {{ $producto->categoria_data['nombre'] }}
                            @else
                                Sin categoría
                            @endif
                        </div>
                    </div>
                    
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">
                            <i class="bi bi-box"></i>
                            Stock Disponible
                        </div>
                        <div class="pedido-info-value">
                            <strong>{{ $producto->stock }}</strong> unidades
                        </div>
                    </div>
                    
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">
                            <i class="bi bi-arrow-down-circle"></i>
                            Stock Mínimo
                        </div>
                        <div class="pedido-info-value">
                            {{ $producto->stock_minimo ?? 5 }} unidades
                        </div>
                    </div>
                    
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">
                            <i class="bi bi-toggle-on"></i>
                            Estado
                        </div>
                        <div class="pedido-info-value">
                            @if($producto->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Descripción -->
                @if($producto->descripcion)
                <div class="producto-descripcion-section">
                    <h4 class="producto-section-title">
                        <i class="bi bi-file-text"></i>
                        Descripción
                    </h4>
                    <p class="producto-descripcion-text">{{ $producto->descripcion }}</p>
                </div>
                @endif
                
                <!-- Especificaciones -->
                @if(!empty($producto->especificaciones) && is_array($producto->especificaciones))
                <div class="producto-specs-section">
                    <h4 class="producto-section-title">
                        <i class="bi bi-gear"></i>
                        Especificaciones
                    </h4>
                    <div class="specs-grid">
                        @foreach($producto->especificaciones as $key => $value)
                            <div class="spec-item">
                                <span class="spec-label">{{ is_string($key) ? $key : 'Especificación' }}</span>
                                <span class="spec-value">{{ is_array($value) ? json_encode($value) : $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Ingredientes -->
                @if(!empty($producto->ingredientes) && is_array($producto->ingredientes))
                <div class="producto-ingredientes-section">
                    <h4 class="producto-section-title">
                        <i class="bi bi-list-check"></i>
                        Ingredientes
                    </h4>
                    <div class="ingredientes-tags">
                        @foreach($producto->ingredientes as $ingrediente)
                            <span class="ingrediente-tag">{{ $ingrediente }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Tiempo de preparación -->
                @if($producto->tiempo_preparacion)
                <div class="producto-tiempo-section">
                    <i class="bi bi-clock"></i>
                    <strong>Tiempo de preparación:</strong> {{ $producto->tiempo_preparacion }} minutos
                </div>
                @endif
            </div>
        </div>
        
        <!-- Card de Agregar al Pedido -->
        <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top: 1.5rem;">
            <div class="pedido-detail-header" style="background: linear-gradient(135deg, #722F37 0%, #5a252c 100%); color: white;">
                <i class="bi bi-cart-plus"></i>
                <h3 class="pedido-detail-title" style="color: white;">Agregar al Pedido</h3>
            </div>
            <div class="pedido-detail-body">
                <form id="add-to-cart-form" onsubmit="return false;">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="cantidad" class="form-label fw-semibold">
                                <i class="bi bi-hash"></i> Cantidad
                            </label>
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn qty-minus" onclick="decreaseQuantity()">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" 
                                       id="cantidad" 
                                       class="qty-input" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $producto->stock }}"
                                       {{ $producto->stock == 0 ? 'disabled' : '' }}>
                                <button type="button" class="qty-btn qty-plus" onclick="increaseQuantity()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Disponible: {{ $producto->stock }} unidades
                            </small>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-cash"></i> Subtotal
                            </label>
                            <div class="subtotal-display" id="subtotal-display">
                                ${{ number_format(to_float($producto->precio), 0) }}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <button type="button" 
                                    class="pedido-btn pedido-btn-primary w-100"
                                    onclick="addToCart()"
                                    {{ $producto->stock == 0 || !$producto->activo ? 'disabled' : '' }}
                                    style="height: 48px;">
                                <i class="bi bi-cart-plus-fill"></i>
                                <span>Agregar</span>
                            </button>
                        </div>
                    </div>
                    
                    @if($producto->stock == 0)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Este producto está agotado y no se puede agregar al pedido.
                        </div>
                    @elseif(!$producto->activo)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-info-circle-fill"></i>
                            Este producto está inactivo y no se puede agregar al pedido.
                        </div>
                    @endif
                </form>
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid #e5e7eb;">
                    <a href="{{ route('vendedor.productos.index') }}" class="pedido-btn pedido-btn-outline">
                        <i class="bi bi-arrow-left"></i>
                        <span>Seguir Comprando</span>
                    </a>
                    <a href="{{ route('vendedor.pedidos.create') }}" class="pedido-btn pedido-btn-secondary">
                        <i class="bi bi-cart"></i>
                        <span>Ver Carrito (<span id="cart-count">0</span>)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

<!-- Estilos adicionales para vista de producto -->
<style>
/* Imagen del producto */
.producto-detail-image {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: cover;
    border-radius: 16px;
}

.producto-detail-placeholder {
    width: 100%;
    height: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    color: #9ca3af;
}

.producto-detail-placeholder i {
    font-size: 5rem;
    margin-bottom: 1rem;
}

.producto-detail-badges {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.producto-gallery-thumbs {
    display: flex;
    gap: 0.75rem;
    padding: 1rem;
    overflow-x: auto;
}

.producto-thumb {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.producto-thumb:hover {
    border-color: #722F37;
    transform: scale(1.05);
}

/* Precio grande */
.producto-precio-section {
    background: linear-gradient(135deg, #722F37 0%, #5a252c 100%);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.producto-precio-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.producto-precio-value {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}

/* Secciones */
.producto-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.producto-descripcion-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.producto-descripcion-text {
    color: #6b7280;
    line-height: 1.6;
    margin: 0;
}

/* Especificaciones */
.producto-specs-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.specs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.spec-item {
    background: #f9fafb;
    padding: 0.75rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.spec-label {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.spec-value {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
}

/* Ingredientes */
.producto-ingredientes-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.ingredientes-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ingrediente-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid #e5e7eb;
}

/* Tiempo */
.producto-tiempo-section {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #fef3c7;
    border-radius: 8px;
    border: 1px solid #fde68a;
    color: #92400e;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Selector de cantidad */
.quantity-selector {
    display: flex;
    align-items: center;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

.qty-btn {
    width: 40px;
    height: 48px;
    border: none;
    background: #f9fafb;
    color: #374151;
    font-size: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover {
    background: #722F37;
    color: white;
}

.qty-input {
    width: 80px;
    height: 48px;
    border: none;
    text-align: center;
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.qty-input:focus {
    outline: none;
}

/* Subtotal display */
.subtotal-display {
    font-size: 1.5rem;
    font-weight: 700;
    color: #722F37;
    padding: 0.75rem;
    background: #fef2f3;
    border-radius: 10px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .producto-precio-value {
        font-size: 2rem;
    }
    
    .specs-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Datos del producto
const producto = {
    id: '{{ $producto->_id }}',
    nombre: '{{ $producto->nombre }}',
    precio: {{ to_float($producto->precio) }},
    stock: {{ $producto->stock }},
    imagen: '{{ $producto->imagen ?? "" }}'
};

// Inicializar PedidosManager
window.pedidosManager = new PedidosManager();

// Actualizar contador del carrito al cargar
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    @if(session('success'))
    if (window.pedidosManager) {
        pedidosManager.showToast("{{ session('success') }}", 'success', 3000);
    }
    @endif

    @if(session('error'))
    if (window.pedidosManager) {
        pedidosManager.showToast("{{ session('error') }}", 'error', 5000);
    }
    @endif
});

// Aumentar cantidad
function increaseQuantity() {
    const input = document.getElementById('cantidad');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
        updateSubtotal();
    }
}

// Disminuir cantidad
function decreaseQuantity() {
    const input = document.getElementById('cantidad');
    const min = parseInt(input.min);
    const current = parseInt(input.value);
    
    if (current > min) {
        input.value = current - 1;
        updateSubtotal();
    }
}

// Actualizar subtotal
function updateSubtotal() {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const subtotal = producto.precio * cantidad;
    document.getElementById('subtotal-display').textContent = '$' + subtotal.toLocaleString('es-CO', {maximumFractionDigits: 0});
}

// Actualizar contador del carrito
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('pedido_carrito') || '[]');
    const totalItems = cart.reduce((sum, item) => sum + item.cantidad, 0);
    document.getElementById('cart-count').textContent = totalItems;
}

// Agregar al carrito
function addToCart() {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    
    if (cantidad < 1 || cantidad > producto.stock) {
        if (window.pedidosManager) {
            pedidosManager.showToast('Cantidad inválida', 'error');
        }
        return;
    }
    
    // Obtener carrito actual
    let cart = JSON.parse(localStorage.getItem('pedido_carrito') || '[]');
    
    // Buscar si el producto ya existe
    const existingIndex = cart.findIndex(item => item.id === producto.id);
    
    if (existingIndex !== -1) {
        // Actualizar cantidad
        const newCantidad = cart[existingIndex].cantidad + cantidad;
        
        if (newCantidad > producto.stock) {
            if (window.pedidosManager) {
                pedidosManager.showToast('No hay suficiente stock disponible', 'error');
            }
            return;
        }
        
        cart[existingIndex].cantidad = newCantidad;
        cart[existingIndex].subtotal = newCantidad * producto.precio;
    } else {
        // Agregar nuevo producto
        cart.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio,
            cantidad: cantidad,
            subtotal: cantidad * producto.precio,
            imagen: producto.imagen
        });
    }
    
    // Guardar en localStorage
    localStorage.setItem('pedido_carrito', JSON.stringify(cart));
    
    // Actualizar contador
    updateCartCount();
    
    // Mostrar mensaje
    if (window.pedidosManager) {
        pedidosManager.showToast(`${producto.nombre} agregado al pedido`, 'success', 3000);
    }
    
    // Resetear cantidad
    document.getElementById('cantidad').value = 1;
    updateSubtotal();
    
    // Animación del botón del carrito
    const cartBtn = document.querySelector('a[href="{{ route('vendedor.pedidos.create') }}"]');
    if (cartBtn) {
        cartBtn.classList.add('pulse');
        setTimeout(() => cartBtn.classList.remove('pulse'), 600);
    }
}

// Update subtotal on quantity change
document.getElementById('cantidad').addEventListener('input', updateSubtotal);

// Animación pulse
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .pulse {
        animation: pulse 0.6s ease;
    }
`;
document.head.appendChild(style);
</script>
@endpush
