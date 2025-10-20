@extends('layouts.cliente')

@section('title', ' - Crear Nuevo Pedido')
@section('header-title', 'Crear Nuevo Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-glassmorphism.css') }}?v={{ time() }}">
<style>
/* Estilos específicos para crear pedido */
.create-pedido-actions {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.create-pedido-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: var(--transition-base);
    border: 2px solid;
    cursor: pointer;
}

.create-pedido-btn-primary {
    background: rgba(114, 47, 55, 0.1);
    border-color: rgba(114, 47, 55, 0.3);
    color: var(--wine);
}

.create-pedido-btn-primary:hover {
    background: linear-gradient(135deg, var(--wine), var(--wine-dark));
    border-color: var(--wine);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.create-pedido-btn-secondary {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
    color: var(--info);
}

.create-pedido-btn-secondary:hover {
    background: linear-gradient(135deg, var(--info), var(--info-light));
    border-color: var(--info);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Sección de formulario con glass */
.pedidos-form-section {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
    transition: var(--transition-base);
}

.pedidos-form-section:hover {
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.18);
    border-color: rgba(255, 255, 255, 0.5);
}

.pedidos-form-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
}

.pedidos-form-section-title i {
    font-size: 1.5rem;
    color: var(--wine);
}

/* Inputs y controles */
.pedidos-form-control {
    border: 2px solid var(--gray-300);
    border-radius: 12px;
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
    transition: var(--transition-base);
    background: white;
    font-weight: 500;
}

.pedidos-form-control:focus {
    border-color: var(--wine);
    box-shadow: 0 0 0 4px rgba(114, 47, 55, 0.1);
    outline: none;
}

.pedidos-form-label {
    font-weight: 700;
    font-size: 0.875rem;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Producto checkbox mejorado */
.pedidos-product-checkbox {
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid var(--gray-200);
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition-base);
    cursor: pointer;
    position: relative;
}

.pedidos-product-checkbox:hover {
    border-color: var(--wine);
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.15);
    transform: translateY(-2px);
}

.pedidos-product-checkbox.selected {
    border-color: var(--wine);
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.05), rgba(114, 47, 55, 0.02));
    box-shadow: 0 4px 16px rgba(114, 47, 55, 0.2);
}

.pedidos-product-checkbox input[type="checkbox"] {
    width: 24px;
    height: 24px;
    cursor: pointer;
    accent-color: var(--wine);
}

.pedidos-product-image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.pedidos-product-placeholder {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--gray-200), var(--gray-100));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--gray-400);
}

.pedidos-product-info {
    flex: 1;
}

.pedidos-product-name {
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
    text-transform: capitalize;
}

.pedidos-product-details {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.pedidos-product-price {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--wine);
}

.pedidos-product-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(114, 47, 55, 0.1);
    padding: 0.5rem;
    border-radius: 12px;
}

.pedidos-qty-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-base);
    color: var(--wine);
    font-weight: 700;
}

.pedidos-qty-btn:hover {
    background: var(--wine);
    color: white;
    transform: scale(1.1);
}

.cantidad-input {
    width: 50px;
    text-align: center;
    border: none;
    background: transparent;
    font-weight: 700;
    font-size: 1rem;
    color: var(--wine);
}

/* Resumen del carrito */
.pedidos-cart-summary {
    background: linear-gradient(135deg, var(--wine) 0%, var(--wine-dark) 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 8px 32px rgba(114, 47, 55, 0.3);
}

.pedidos-cart-summary-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.pedidos-cart-item {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: start;
}

.pedidos-cart-total {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid rgba(255, 255, 255, 0.2);
    display: flex;
    justify-content: space-between;
    font-size: 1.5rem;
    font-weight: 800;
}

.pedidos-submit-btn {
    width: 100%;
    padding: 1.25rem;
    background: white;
    color: var(--wine);
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.125rem;
    margin-top: 1.5rem;
    cursor: pointer;
    transition: var(--transition-base);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.pedidos-submit-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.pedidos-submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Categoría section */
.categoria-section h6 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--wine);
    margin-bottom: 1.5rem;
    padding: 0.75rem 1rem;
    background: rgba(114, 47, 55, 0.05);
    border-left: 4px solid var(--wine);
    border-radius: 8px;
}

/* Breadcrumb en modal/collapse */
.breadcrumb {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 0.875rem 1.25rem;
    margin-bottom: 1.5rem;
}

.breadcrumb-item a {
    color: var(--wine);
    text-decoration: none;
    transition: all 0.2s;
}

.breadcrumb-item a:hover {
    color: var(--wine-dark);
}

.breadcrumb-item.active {
    color: var(--gray-600);
}

/* Input group mejorado */
.input-group-text {
    border: 2px solid var(--gray-300);
    border-right: none;
    border-radius: 12px 0 0 12px;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 12px 12px 0;
}

.input-group-lg .input-group-text,
.input-group-lg .form-control {
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
}

/* Responsive */
@media (max-width: 991.98px) {
    .pedidos-cart-summary {
        position: static !important;
        margin-top: 2rem;
    }
    
    .pedidos-form-section {
        padding: 1.5rem;
    }
}

@media (max-width: 767.98px) {
    .create-pedido-actions {
        flex-direction: column;
    }
    
    .create-pedido-btn {
        width: 100%;
        justify-content: center;
    }
    
    .pedidos-product-checkbox {
        padding: 1rem;
    }
    
    .pedidos-product-image,
    .pedidos-product-placeholder {
        width: 60px;
        height: 60px;
    }
}

/* Glass Modal Styles */
.glass-modal {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
}

.modal-glass-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.05) 0%, rgba(114, 47, 55, 0.1) 100%);
    border-radius: 24px;
    z-index: -1;
}

.confirm-icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid;
    margin: 0 auto;
}

.confirm-icon {
    font-size: 2.5rem;
}

.confirm-title {
    font-weight: 700;
    color: var(--gray-900);
}

.confirm-message {
    color: var(--gray-600);
    font-size: 1rem;
}

.btn-glass {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    border: 2px solid;
    transition: all 0.3s ease;
    min-width: 120px;
}

.btn-glass-primary {
    background: rgba(114, 47, 55, 0.1);
    border-color: rgba(114, 47, 55, 0.3);
    color: var(--wine);
}

.btn-glass-primary:hover {
    background: var(--wine);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.btn-glass-secondary {
    background: rgba(108, 117, 125, 0.1);
    border-color: rgba(108, 117, 125, 0.3);
    color: #6c757d;
}

.btn-glass-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.btn-glass-danger {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
    color: #dc3545;
}

.btn-glass-danger:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-glass-info {
    background: rgba(23, 162, 184, 0.1);
    border-color: rgba(23, 162, 184, 0.3);
    color: #17a2b8;
}

.btn-glass-info:hover {
    background: #17a2b8;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb mejorado -->
    <div class="mb-3 fade-in-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('cliente.dashboard') }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('cliente.pedidos.index') }}">
                        <i class="bi bi-box-seam"></i> Mis Pedidos
                    </a>
                </li>
                <li class="breadcrumb-item active">Nuevo Pedido</li>
            </ol>
        </nav>
    </div>

    <!-- Botones de acción -->
    <div class="create-pedido-actions fade-in-up animate-delay-1">
        <button type="button" class="create-pedido-btn create-pedido-btn-primary" onclick="limpiarSeleccion()">
            <i class="bi bi-x-circle"></i>
            Limpiar Selección
        </button>
        <button type="button" class="create-pedido-btn create-pedido-btn-secondary" onclick="cargarDesdeCarrito()">
            <i class="bi bi-upload"></i>
            Cargar desde Carrito
        </button>
    </div>

    <form id="crearPedidoForm" action="{{ route('cliente.pedidos.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Columna Principal - Productos -->
            <div class="col-lg-8 mb-4">
                <!-- Selección de Productos -->
                <div class="pedidos-form-section fade-in-up animate-delay-2">
                    <h5 class="pedidos-form-section-title">
                        <i class="bi bi-box-seam-fill"></i>
                        Selecciona tus Productos
                    </h5>
                    
                    <!-- Buscador y Filtros de Productos -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           id="searchProductos" 
                                           class="form-control pedidos-form-control" 
                                           placeholder="Buscar productos por nombre..."
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda()" title="Limpiar búsqueda">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <select id="filtroCategoria" class="form-select pedidos-form-control form-select-lg">
                                    <option value="">📂 Todas las categorías</option>
                                    @foreach($productosPorCategoria as $categoria => $productos)
                                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Contador de Resultados -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="contadorProductos">Mostrando todos los productos</span>
                            </small>
                            <small>
                                <span id="productosSeleccionados" class="pedidos-badge pedidos-badge-pendiente">0 seleccionados</span>
                            </small>
                        </div>
                    </div>

                    <!-- Productos por Categoría -->
                    <div id="productosContainer">
                        @forelse($productosPorCategoria as $categoria => $productos)
                        <div class="categoria-section mb-4" data-categoria="{{ $categoria }}">
                            <h6>
                                <i class="bi bi-tag-fill me-2"></i>
                                {{ $categoria }}
                            </h6>
                            
                            <div class="row">
                                @foreach($productos as $producto)
                                <div class="col-md-6 mb-3 producto-item" 
                                     data-nombre="{{ strtolower($producto->nombre) }}"
                                     data-producto-id="{{ $producto->_id }}">
                                    <div class="pedidos-product-checkbox" 
                                         data-precio="{{ $producto->precio }}"
                                         data-stock="{{ $producto->stock }}">
                                        <input type="checkbox" 
                                               value="{{ $producto->_id }}"
                                               id="producto_{{ $producto->_id }}"
                                               class="producto-checkbox"
                                               data-producto-id="{{ $producto->_id }}"
                                               {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                        
                                        @if($producto->imagen_principal)
                                        <img src="{{ asset('storage/' . $producto->imagen_principal) }}" 
                                             alt="{{ $producto->nombre }}"
                                             class="pedidos-product-image"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'pedidos-product-placeholder\'><i class=\'bi bi-image\'></i></div>';">
                                        @elseif($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                             alt="{{ $producto->nombre }}"
                                             class="pedidos-product-image"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'pedidos-product-placeholder\'><i class=\'bi bi-image\'></i></div>';">
                                        @else
                                        <div class="pedidos-product-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        @endif
                                        
                                        <div class="pedidos-product-info flex-grow-1">
                                            <div class="pedidos-product-name">{{ $producto->nombre }}</div>
                                            <div class="pedidos-product-details">
                                                @if($producto->stock > 0)
                                                    <span class="text-success">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        Stock: {{ $producto->stock }}
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="bi bi-x-circle-fill me-1"></i>
                                                        Sin stock
                                                    </span>
                                                @endif
                                                
                                                @if($producto->descripcion)
                                                    <br><small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="pedidos-product-price">
                                            ${{ number_format($producto->precio, 0, ',', '.') }}
                                        </div>
                                        
                                        <div class="pedidos-product-quantity" style="display: none;">
                                            <button type="button" class="pedidos-qty-btn" onclick="decrementQty(this)">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $producto->stock }}"
                                                   class="cantidad-input"
                                                   data-producto-id="{{ $producto->_id }}"
                                                   readonly>
                                            <button type="button" class="pedidos-qty-btn" onclick="incrementQty(this)">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="pedidos-empty-state">
                            <i class="bi bi-inbox"></i>
                            <h4>No hay productos disponibles</h4>
                            <p>Por favor, vuelve más tarde para ver nuestros productos</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Información de Entrega -->
                <div class="pedidos-form-section fade-in-up animate-delay-3">
                    <h5 class="pedidos-form-section-title">
                        <i class="bi bi-truck-fill"></i>
                        Información de Entrega
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="direccion_entrega" class="pedidos-form-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Dirección de Entrega *
                            </label>
                            <textarea name="direccion_entrega" 
                                      id="direccion_entrega" 
                                      class="pedidos-form-control @error('direccion_entrega') is-invalid @enderror" 
                                      rows="3" 
                                      required 
                                      placeholder="Ingresa la dirección completa de entrega">{{ old('direccion_entrega', $user->direccion ?? '') }}</textarea>
                            @error('direccion_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono_entrega" class="pedidos-form-label">
                                <i class="bi bi-telephone-fill"></i>
                                Teléfono de Contacto *
                            </label>
                            <input type="tel" 
                                   name="telefono_entrega" 
                                   id="telefono_entrega" 
                                   class="pedidos-form-control @error('telefono_entrega') is-invalid @enderror" 
                                   value="{{ old('telefono_entrega', $user->telefono ?? '') }}"
                                   required
                                   placeholder="Ej: +57 300 123 4567">
                            @error('telefono_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="metodo_pago" class="pedidos-form-label">
                                <i class="bi bi-credit-card-fill"></i>
                                Método de Pago *
                            </label>
                            <select name="metodo_pago" 
                                    id="metodo_pago" 
                                    class="pedidos-form-control @error('metodo_pago') is-invalid @enderror" 
                                    required>
                                <option value="">Selecciona un método</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>
                                    💵 Efectivo
                                </option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>
                                    🏦 Transferencia Bancaria
                                </option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>
                                    💳 Tarjeta de Crédito/Débito
                                </option>
                            </select>
                            @error('metodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="notas" class="pedidos-form-label">
                                <i class="bi bi-chat-left-text-fill"></i>
                                Notas Adicionales (Opcional)
                            </label>
                            <textarea name="notas" 
                                      id="notas" 
                                      class="pedidos-form-control @error('notas') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Comentarios especiales, referencias de ubicación, etc.">{{ old('notas') }}</textarea>
                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Resumen del Carrito -->
            <div class="col-lg-4">
                <div class="pedidos-cart-summary fade-in-up animate-delay-4" style="position: sticky; top: 100px;">
                    <div class="pedidos-cart-summary-title">
                        <i class="bi bi-cart-check-fill"></i>
                        Resumen del Pedido
                    </div>
                    
                    <div id="cartItems">
                        <div class="text-center py-4" style="opacity: 0.7;">
                            <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
                            <p class="mb-0">No has seleccionado productos</p>
                        </div>
                    </div>
                    
                    <div class="pedidos-cart-total">
                        <span>Total:</span>
                        <span id="cartTotal">$0</span>
                    </div>
                    
                    <button type="submit" 
                            class="pedidos-submit-btn" 
                            id="submitBtn"
                            disabled>
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Confirmar Pedido
                    </button>
                    
                    <div class="mt-3 text-center" style="opacity: 0.8;">
                        <small>
                            <i class="bi bi-shield-check me-1"></i>
                            Pedido seguro y protegido
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading Overlay -->
<div class="pedidos-loading-overlay" id="loadingOverlay">
    <div class="pedidos-loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/glass-modal.js') }}?v={{ time() }}"></script>
<script>
// Variable global para el carrito
window.cart = new Map();

// Formatear número (función global)
function formatNumber(num) {
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num);
}

// Función para actualizar el carrito (definida globalmente antes de usarla)
window.updateCart = function() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!cartItems || !cartTotal || !submitBtn) return;
    
    if (window.cart.size === 0) {
        cartItems.innerHTML = `
            <div class="text-center py-4" style="opacity: 0.7;">
                <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
                <p class="mb-0">No has seleccionado productos</p>
            </div>
        `;
        cartTotal.textContent = '$0';
        submitBtn.disabled = true;
        
        const productosSeleccionados = document.getElementById('productosSeleccionados');
        if (productosSeleccionados) {
            productosSeleccionados.textContent = '0 seleccionados';
            productosSeleccionados.className = 'pedidos-badge pedidos-badge-pendiente';
        }
        return;
    }
    
    let total = 0;
    let html = '';
    
    window.cart.forEach((item, productoId) => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        
        // Obtener imagen del producto
        const productoItem = document.querySelector(`.producto-item[data-producto-id="${productoId}"]`);
        let imagenHtml = '';
        
        if (productoItem) {
            const imagen = productoItem.querySelector('.pedidos-product-image');
            if (imagen) {
                imagenHtml = `<img src="${imagen.src}" alt="${item.nombre}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">`;
            } else {
                imagenHtml = `<div class="me-2" style="width: 40px; height: 40px; background: rgba(114, 47, 55, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>`;
            }
        }
        
        html += `
            <div class="pedidos-cart-item">
                <div class="d-flex align-items-center flex-grow-1">
                    ${imagenHtml}
                    <div>
                        <div class="fw-semibold text-capitalize">${item.nombre}</div>
                        <small style="opacity: 0.75;">${item.cantidad} x $${formatNumber(item.precio)}</small>
                    </div>
                </div>
                <div class="fw-bold">$${formatNumber(subtotal)}</div>
            </div>
        `;
    });
    
    cartItems.innerHTML = html;
    cartTotal.textContent = '$' + formatNumber(total);
    submitBtn.disabled = false;
    
    const productosSeleccionados = document.getElementById('productosSeleccionados');
    if (productosSeleccionados) {
        productosSeleccionados.textContent = `${window.cart.size} seleccionados`;
        productosSeleccionados.className = 'pedidos-badge pedidos-badge-confirmado';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Pre-cargar productos desde localStorage (si vienen del dashboard)
    const carritoLocalStorage = JSON.parse(localStorage.getItem('carrito')) || [];
    if (carritoLocalStorage.length > 0) {
        carritoLocalStorage.forEach(item => {
            const checkbox = document.querySelector(`input[value="${item.id}"]`);
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = true;
                const container = checkbox.closest('.pedidos-product-checkbox');
                const qtyDiv = container.querySelector('.pedidos-product-quantity');
                const cantidadInput = qtyDiv.querySelector('.cantidad-input');
                
                container.classList.add('selected');
                qtyDiv.style.display = 'flex';
                cantidadInput.value = item.cantidad;
                
                const productoItem = container.closest('.producto-item');
                const nombre = productoItem ? productoItem.dataset.nombre : item.nombre || 'Producto';
                const precio = parseFloat(container.dataset.precio);
                
                window.cart.set(item.id, {
                    nombre: nombre,
                    precio: precio,
                    cantidad: item.cantidad
                });
            }
        });
        
        updateCart();
        localStorage.removeItem('carrito');
        showToast('success', 'Productos cargados desde tu carrito');
    }
    
    // Escuchar cambios en checkboxes
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const container = this.closest('.pedidos-product-checkbox');
            const qtyDiv = container.querySelector('.pedidos-product-quantity');
            const productoId = this.value;
            const productoItem = container.closest('.producto-item');
            const nombre = productoItem ? productoItem.dataset.nombre : 'Producto';
            const precio = parseFloat(container.dataset.precio);
            
            if (this.checked) {
                container.classList.add('selected');
                qtyDiv.style.display = 'flex';
                
                const cantidadInput = qtyDiv.querySelector('.cantidad-input');
                if (cantidadInput) {
                    cantidadInput.value = 1;
                }
                
                window.cart.set(productoId, {
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1
                });
            } else {
                container.classList.remove('selected');
                qtyDiv.style.display = 'none';
                window.cart.delete(productoId);
            }
            
            updateCart();
        });
    });
    
    // Búsqueda de productos
    const searchInput = document.getElementById('searchProductos');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            document.querySelectorAll('.producto-item').forEach(item => {
                const nombre = item.dataset.nombre;
                if (nombre.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            actualizarContador();
        });
    }
    
    // Validación del formulario
    const form = document.getElementById('crearPedidoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('submitting')) {
                return false;
            }
            
            if (window.cart.size === 0) {
                showToast('warning', 'Debes seleccionar al menos un producto');
                return false;
            }
            
            const direccion = document.getElementById('direccion_entrega');
            const telefono = document.getElementById('telefono_entrega');
            const metodoPago = document.getElementById('metodo_pago');
            
            if (!direccion || !direccion.value.trim()) {
                showToast('warning', 'La dirección de entrega es obligatoria');
                direccion?.focus();
                return false;
            }
            
            if (!telefono || !telefono.value.trim()) {
                showToast('warning', 'El teléfono de contacto es obligatorio');
                telefono?.focus();
                return false;
            }
            
            if (!metodoPago || !metodoPago.value) {
                showToast('warning', 'Debes seleccionar un método de pago');
                metodoPago?.focus();
                return false;
            }
            
            // Eliminar inputs anteriores
            form.querySelectorAll('input[name^="productos["]').forEach(input => {
                if (!input.classList.contains('cantidad-input') && !input.classList.contains('producto-checkbox')) {
                    input.remove();
                }
            });
            
            // Crear campos dinámicos
            let index = 0;
            window.cart.forEach((item, productoId) => {
                const inputProductoId = document.createElement('input');
                inputProductoId.type = 'hidden';
                inputProductoId.name = `productos[${index}][producto_id]`;
                inputProductoId.value = productoId;
                form.appendChild(inputProductoId);
                
                const inputCantidad = document.createElement('input');
                inputCantidad.type = 'hidden';
                inputCantidad.name = `productos[${index}][cantidad]`;
                inputCantidad.value = item.cantidad;
                form.appendChild(inputCantidad);
                
                index++;
            });
            
            this.classList.add('submitting');
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.classList.add('active');
            }
            
            this.submit();
        });
    }
    
    // Mostrar mensajes flash
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
    
    @if($errors->any())
        showToast('error', 'Por favor corrige los errores en el formulario');
    @endif
});

// Funciones de cantidad
function incrementQty(btn) {
    const input = btn.previousElementSibling;
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
        updateProductQty(input);
    }
}

function decrementQty(btn) {
    const input = btn.nextElementSibling;
    const current = parseInt(input.value);
    
    if (current > 1) {
        input.value = current - 1;
        updateProductQty(input);
    }
}

function updateProductQty(input) {
    const container = input.closest('.pedidos-product-checkbox');
    const checkbox = container.querySelector('.producto-checkbox');
    const productoId = checkbox.value;
    const nuevaCantidad = parseInt(input.value);
    
    if (window.cart && window.cart.has(productoId)) {
        const item = window.cart.get(productoId);
        item.cantidad = nuevaCantidad;
        window.cart.set(productoId, item);
        
        if (typeof window.updateCart === 'function') {
            window.updateCart();
        }
    }
}

// Limpiar selección
function limpiarSeleccion() {
    if (typeof GlassModal !== 'undefined') {
        GlassModal.show({
            title: '¿Limpiar selección?',
            message: 'Se eliminarán todos los productos seleccionados del pedido actual',
            icon: 'bi-trash',
            iconColor: '#dc3545',
            iconBg: 'rgba(220, 53, 69, 0.2)',
            confirmText: 'Sí, limpiar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-danger',
            onConfirm: () => {
                document.querySelectorAll('.producto-checkbox:checked').forEach(checkbox => {
                    checkbox.checked = false;
                    const event = new Event('change');
                    checkbox.dispatchEvent(event);
                });
                
                showToast('success', 'Selección de productos limpiada');
            }
        });
    } else {
        if (!confirm('¿Estás seguro de que deseas limpiar todos los productos seleccionados?')) {
            return;
        }
        
        document.querySelectorAll('.producto-checkbox:checked').forEach(checkbox => {
            checkbox.checked = false;
            const event = new Event('change');
            checkbox.dispatchEvent(event);
        });
        
        showToast('success', 'Selección de productos limpiada');
    }
}

// Cargar desde carrito
function cargarDesdeCarrito() {
    const carritoLS = JSON.parse(localStorage.getItem('carrito')) || [];
    
    if (carritoLS.length === 0) {
        if (typeof GlassModal !== 'undefined') {
            GlassModal.warning('Carrito vacío', 'No hay productos en tu carrito para cargar');
        } else {
            showToast('info', 'No hay productos en tu carrito');
        }
        return;
    }
    
    if (typeof GlassModal !== 'undefined') {
        GlassModal.show({
            title: `Cargar ${carritoLS.length} producto(s)`,
            message: '¿Deseas cargar los productos guardados en tu carrito?',
            icon: 'bi-upload',
            iconColor: '#3b82f6',
            iconBg: 'rgba(59, 130, 246, 0.2)',
            confirmText: 'Sí, cargar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-info',
            onConfirm: () => {
                ejecutarCargaCarrito(carritoLS);
            }
        });
    } else {
        if (confirm(`¿Deseas cargar ${carritoLS.length} producto(s) desde tu carrito?`)) {
            ejecutarCargaCarrito(carritoLS);
        }
    }
}

// Función auxiliar para ejecutar la carga
function ejecutarCargaCarrito(carritoLS) {
    let cargados = 0;
    
    carritoLS.forEach(item => {
        const checkbox = document.querySelector(`input[value="${item.id}"]`);
        if (checkbox && !checkbox.disabled && !checkbox.checked) {
            checkbox.checked = true;
            const container = checkbox.closest('.pedidos-product-checkbox');
            const qtyDiv = container.querySelector('.pedidos-product-quantity');
            const cantidadInput = qtyDiv.querySelector('.cantidad-input');
            
            container.classList.add('selected');
            qtyDiv.style.display = 'flex';
            cantidadInput.value = item.cantidad || 1;
            
            const productoItem = container.closest('.producto-item');
            const nombre = productoItem ? productoItem.dataset.nombre : item.nombre || 'Producto';
            const precio = parseFloat(container.dataset.precio);
            
            window.cart.set(item.id, {
                nombre: nombre,
                precio: precio,
                cantidad: item.cantidad || 1
            });
            
            cargados++;
        }
    });
    
    if (cargados > 0) {
        updateCart();
        showToast('success', `${cargados} producto(s) cargado(s) desde tu carrito`);
        localStorage.removeItem('carrito');
    } else {
        showToast('warning', 'No se pudieron cargar productos (sin stock, no disponibles o ya seleccionados)');
    }
}

// Limpiar búsqueda
function limpiarBusqueda() {
    const searchInput = document.getElementById('searchProductos');
    searchInput.value = '';
    
    document.querySelectorAll('.producto-item').forEach(item => {
        item.style.display = 'block';
    });
    
    actualizarContador();
}

// Actualizar contador
function actualizarContador() {
    const total = document.querySelectorAll('.producto-item').length;
    const visibles = document.querySelectorAll('.producto-item[style*="display: block"], .producto-item:not([style*="display"])').length;
    const seleccionados = document.querySelectorAll('.producto-checkbox:checked').length;
    
    document.getElementById('contadorProductos').textContent = 
        visibles === total ? 
        `Mostrando ${total} producto(s)` : 
        `Mostrando ${visibles} de ${total} producto(s)`;
    
    const badge = document.getElementById('productosSeleccionados');
    if (badge) {
        badge.textContent = `${seleccionados} seleccionados`;
        badge.className = seleccionados > 0 ? 'pedidos-badge pedidos-badge-confirmado' : 'pedidos-badge pedidos-badge-pendiente';
    }
}

// Filtro por categoría
document.getElementById('filtroCategoria')?.addEventListener('change', function() {
    const categoria = this.value;
    
    document.querySelectorAll('.categoria-section').forEach(section => {
        if (!categoria || section.dataset.categoria === categoria) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
    
    actualizarContador();
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', () => {
    actualizarContador();
    
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarContador);
    });
});

// Toast function
function showToast(type, message) {
    if (typeof showSuccessToast !== 'undefined' && type === 'success') {
        showSuccessToast(message);
    } else if (typeof showErrorToast !== 'undefined' && type === 'error') {
        showErrorToast(message);
    } else if (typeof showWarningToast !== 'undefined' && type === 'warning') {
        showWarningToast(message);
    } else if (typeof showInfoToast !== 'undefined' && type === 'info') {
        showInfoToast(message);
    } else {
        alert(message);
    }
}
</script>
@endpush

