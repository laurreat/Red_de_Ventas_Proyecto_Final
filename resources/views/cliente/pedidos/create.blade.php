@extends('layouts.app')

@section('title', '- Crear Nuevo Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-modern.css') }}?v={{ filemtime(public_path('css/pages/pedidos-cliente-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Hero -->
    <div class="pedidos-header fade-in-up">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <a href="{{ route('cliente.pedidos.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="pedidos-title mb-0">
                        <i class="bi bi-cart-plus me-2"></i>
                        Crear Nuevo Pedido
                    </h1>
                </div>
                <p class="pedidos-subtitle mb-0">
                    Selecciona tus productos favoritos y completa tu pedido
                </p>
            </div>
        </div>
    </div>

    <form id="crearPedidoForm" action="{{ route('cliente.pedidos.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Columna Principal - Productos -->
            <div class="col-lg-8 mb-4">
                <!-- Selección de Productos -->
                <div class="pedidos-form-section fade-in-up">
                    <h5 class="pedidos-form-section-title">
                        <i class="bi bi-box-seam"></i>
                        Selecciona tus Productos
                    </h5>
                    
                    <!-- Buscador de Productos -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   id="searchProductos" 
                                   class="form-control border-start-0" 
                                   placeholder="Buscar productos por nombre...">
                        </div>
                    </div>

                    <!-- Productos por Categoría -->
                    <div id="productosContainer">
                        @forelse($productosPorCategoria as $categoria => $productos)
                        <div class="categoria-section mb-4" data-categoria="{{ $categoria }}">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-tag me-2"></i>
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
                                               name="productos[{{ $loop->parent->index }}][producto_id]" 
                                               value="{{ $producto->_id }}"
                                               id="producto_{{ $producto->_id }}"
                                               class="producto-checkbox"
                                               {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                        
                                        @if($producto->imagen_principal)
                                        <img src="{{ asset('storage/' . $producto->imagen_principal) }}" 
                                             alt="{{ $producto->nombre }}"
                                             class="pedidos-product-image"
                                             onerror="this.onerror=null; this.src='{{ asset('images/producto-default.jpg') }}';">
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
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Stock: {{ $producto->stock }}
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="bi bi-x-circle me-1"></i>
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
                                                   name="productos[{{ $loop->parent->index }}][cantidad]" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $producto->stock }}"
                                                   class="cantidad-input"
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
                <div class="pedidos-form-section fade-in-up animate-delay-1">
                    <h5 class="pedidos-form-section-title">
                        <i class="bi bi-truck"></i>
                        Información de Entrega
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="direccion_entrega" class="pedidos-form-label">
                                <i class="bi bi-geo-alt me-1"></i>
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
                                <i class="bi bi-telephone me-1"></i>
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
                                <i class="bi bi-credit-card me-1"></i>
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
                                <i class="bi bi-chat-left-text me-1"></i>
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
                <div class="pedidos-cart-summary fade-in-up animate-delay-2" style="position: sticky; top: 100px;">
                    <div class="pedidos-cart-summary-title">
                        <i class="bi bi-cart-check"></i>
                        Resumen del Pedido
                    </div>
                    
                    <div id="cartItems">
                        <div class="text-center py-4 text-white-50">
                            <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
                            <p>No has seleccionado productos</p>
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
                        <i class="bi bi-check-circle me-2"></i>
                        Confirmar Pedido
                    </button>
                    
                    <div class="mt-3 text-center">
                        <small class="text-white-50">
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
<div class="pedidos-loading-overlay">
    <div class="pedidos-loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/pedidos-cliente-modern.js') }}?v={{ filemtime(public_path('js/pages/pedidos-cliente-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.pedidosManager = new PedidosClienteManager();
    
    // Carrito de compras
    const cart = new Map();
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const submitBtn = document.getElementById('submitBtn');
    
    // Pre-cargar productos desde localStorage (si vienen del dashboard)
    const carritoLocalStorage = JSON.parse(localStorage.getItem('carrito')) || [];
    if (carritoLocalStorage.length > 0) {
        carritoLocalStorage.forEach(item => {
            // Buscar el checkbox del producto
            const checkbox = document.querySelector(`input[value="${item.id}"]`);
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = true;
                const container = checkbox.closest('.pedidos-product-checkbox');
                const qtyDiv = container.querySelector('.pedidos-product-quantity');
                const cantidadInput = qtyDiv.querySelector('.cantidad-input');
                
                container.classList.add('selected');
                qtyDiv.style.display = 'flex';
                cantidadInput.value = item.cantidad;
                
                // Agregar al carrito local de la página
                const nombre = container.closest('.producto-item').dataset.nombre;
                const precio = parseFloat(container.dataset.precio);
                
                cart.set(item.id, {
                    nombre: nombre,
                    precio: precio,
                    cantidad: item.cantidad
                });
            }
        });
        
        // Actualizar vista del carrito
        updateCart();
        
        // Limpiar localStorage después de cargar
        localStorage.removeItem('carrito');
        
        // Mostrar mensaje
        pedidosManager.showToast('Productos cargados desde tu carrito', 'success');
    }
    
    // Escuchar cambios en checkboxes
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const container = this.closest('.pedidos-product-checkbox');
            const qtyDiv = container.querySelector('.pedidos-product-quantity');
            const productoId = this.value;
            const nombre = container.closest('.producto-item').dataset.nombre;
            const precio = parseFloat(container.dataset.precio);
            
            if (this.checked) {
                container.classList.add('selected');
                qtyDiv.style.display = 'flex';
                
                // Agregar al carrito
                cart.set(productoId, {
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1
                });
            } else {
                container.classList.remove('selected');
                qtyDiv.style.display = 'none';
                
                // Quitar del carrito
                cart.delete(productoId);
            }
            
            updateCart();
        });
    });
    
    // Búsqueda de productos
    document.getElementById('searchProductos').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const nombre = item.dataset.nombre;
            if (nombre.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Función para actualizar el carrito
    function updateCart() {
        if (cart.size === 0) {
            cartItems.innerHTML = `
                <div class="text-center py-4 text-white-50">
                    <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
                    <p>No has seleccionado productos</p>
                </div>
            `;
            cartTotal.textContent = '$0';
            submitBtn.disabled = true;
            return;
        }
        
        let total = 0;
        let html = '';
        
        cart.forEach((item, productoId) => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;
            
            html += `
                <div class="pedidos-cart-item">
                    <div>
                        <div class="fw-semibold text-capitalize">${item.nombre}</div>
                        <small class="opacity-75">${item.cantidad} x $${formatNumber(item.precio)}</small>
                    </div>
                    <div class="fw-bold">$${formatNumber(subtotal)}</div>
                </div>
            `;
        });
        
        cartItems.innerHTML = html;
        cartTotal.textContent = '$' + formatNumber(total);
        submitBtn.disabled = false;
    }
    
    // Formatear número
    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }
    
    // Validación del formulario
    document.getElementById('crearPedidoForm').addEventListener('submit', function(e) {
        if (cart.size === 0) {
            e.preventDefault();
            pedidosManager.showToast('Debes seleccionar al menos un producto', 'error');
            return;
        }
        
        pedidosManager.showLoading();
    });
    
    // Mostrar mensajes flash
    @if(session('success'))
        pedidosManager.showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        pedidosManager.showToast('{{ session('error') }}', 'error');
    @endif
    
    @if($errors->any())
        pedidosManager.showToast('Por favor corrige los errores en el formulario', 'error');
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
    
    // Actualizar en el carrito
    const event = new Event('change');
    checkbox.dispatchEvent(event);
}
</script>
@endpush
