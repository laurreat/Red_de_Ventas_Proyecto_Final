@extends('layouts.app')

@section('title', '- Crear Nuevo Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-modern.css') }}?v={{ filemtime(public_path('css/pages/pedidos-cliente-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Hero con Navegación -->
    <div class="pedidos-header fade-in-up">
        <div class="row align-items-center mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('cliente.dashboard') }}" class="text-decoration-none">
                                <i class="bi bi-house-door"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('cliente.pedidos.index') }}" class="text-decoration-none">
                                Mis Pedidos
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Nuevo Pedido</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <button onclick="volverAtras()" class="btn btn-light btn-sm" title="Volver atrás">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h1 class="pedidos-title mb-0">
                        <i class="bi bi-cart-plus me-2"></i>
                        Crear Nuevo Pedido
                    </h1>
                </div>
                <p class="pedidos-subtitle mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Selecciona tus productos favoritos y completa tu pedido
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex gap-2 justify-content-md-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="limpiarSeleccion()">
                        <i class="bi bi-x-circle me-1"></i>
                        Limpiar
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="cargarDesdeCarrito()">
                        <i class="bi bi-upload me-1"></i>
                        Desde Carrito
                    </button>
                </div>
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
                    
                    <!-- Buscador y Filtros de Productos -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           id="searchProductos" 
                                           class="form-control border-start-0 ps-0" 
                                           placeholder="Buscar productos por nombre..."
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda()" title="Limpiar búsqueda">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select id="filtroCategoria" class="form-select form-select-lg">
                                    <option value="">📂 Todas las categorías</option>
                                    @foreach($productosPorCategoria as $categoria => $productos)
                                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="toggleFiltroPrecio()">
                                    <i class="bi bi-funnel"></i>
                                    <span class="d-none d-md-inline ms-1">Filtros</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtros Avanzados (Colapsable) -->
                        <div id="filtrosAvanzados" class="mt-3 collapse">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bi bi-sliders me-2"></i>Filtros Avanzados</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small">Rango de Precio</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <input type="number" class="form-control form-control-sm" id="precioMin" placeholder="Mín">
                                                </div>
                                                <div class="col-6">
                                                    <input type="number" class="form-control form-control-sm" id="precioMax" placeholder="Máx">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Ordenar por</label>
                                            <select id="ordenarPor" class="form-select form-select-sm">
                                                <option value="nombre">Nombre A-Z</option>
                                                <option value="precio-asc">Precio: Menor a Mayor</option>
                                                <option value="precio-desc">Precio: Mayor a Menor</option>
                                                <option value="stock">Mayor Stock</option>
                                            </select>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="resetearFiltros()">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Resetear
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="aplicarFiltros()">
                                                <i class="bi bi-check me-1"></i>Aplicar Filtros
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contador de Resultados -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="contadorProductos">Mostrando todos los productos</span>
                            </small>
                            <small class="text-muted">
                                <span id="productosSeleccionados" class="badge bg-primary">0 seleccionados</span>
                            </small>
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
                                               value="{{ $producto->_id }}"
                                               id="producto_{{ $producto->_id }}"
                                               class="producto-checkbox"
                                               data-producto-id="{{ $producto->_id }}"
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
// Variable global para el carrito
window.cart = new Map();

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el manager pero SIN el carrito interno
    // El carrito se maneja globalmente
    if (typeof PedidosClienteManager !== 'undefined') {
        window.pedidosManager = new PedidosClienteManager();
    }
    
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
                
                window.cart.set(item.id, {
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
        if (window.pedidosManager) {
            pedidosManager.showToast('success', 'Productos cargados', 'Productos cargados desde tu carrito');
        }
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
                
                // Resetear cantidad a 1 al seleccionar
                const cantidadInput = qtyDiv.querySelector('.cantidad-input');
                if (cantidadInput) {
                    cantidadInput.value = 1;
                }
                
                // Agregar al carrito
                window.cart.set(productoId, {
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1
                });
            } else {
                container.classList.remove('selected');
                qtyDiv.style.display = 'none';
                
                // Quitar del carrito
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
        });
    }
    
    // Función para actualizar el carrito
    window.updateCart = function() {
        if (window.cart.size === 0) {
            cartItems.innerHTML = `
                <div class="text-center py-4 text-white-50">
                    <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
                    <p>No has seleccionado productos</p>
                </div>
            `;
            cartTotal.textContent = '$0';
            submitBtn.disabled = true;
            
            const productosSeleccionados = document.getElementById('productosSeleccionados');
            if (productosSeleccionados) {
                productosSeleccionados.textContent = '0 seleccionados';
            }
            return;
        }
        
        let total = 0;
        let html = '';
        
        window.cart.forEach((item, productoId) => {
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
        
        const productosSeleccionados = document.getElementById('productosSeleccionados');
        if (productosSeleccionados) {
            productosSeleccionados.textContent = `${window.cart.size} seleccionados`;
        }
    }
    
    // Formatear número
    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }
    
    // Validación del formulario
    const form = document.getElementById('crearPedidoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Siempre prevenir el envío por defecto
            
            // Prevenir múltiples envíos
            if (this.classList.contains('submitting')) {
                return false;
            }
            
            if (window.cart.size === 0) {
                if (window.pedidosManager) {
                    pedidosManager.showToast('warning', 'Carrito vacío', 'Debes seleccionar al menos un producto');
                } else {
                    alert('Debes seleccionar al menos un producto');
                }
                return false;
            }
            
            // Validar campos requeridos
            const direccion = document.getElementById('direccion_entrega');
            const telefono = document.getElementById('telefono_entrega');
            const metodoPago = document.getElementById('metodo_pago');
            
            if (!direccion || !direccion.value.trim()) {
                if (window.pedidosManager) {
                    pedidosManager.showToast('warning', 'Campo requerido', 'La dirección de entrega es obligatoria');
                } else {
                    alert('La dirección de entrega es obligatoria');
                }
                direccion?.focus();
                return false;
            }
            
            if (!telefono || !telefono.value.trim()) {
                if (window.pedidosManager) {
                    pedidosManager.showToast('warning', 'Campo requerido', 'El teléfono de contacto es obligatorio');
                } else {
                    alert('El teléfono de contacto es obligatorio');
                }
                telefono?.focus();
                return false;
            }
            
            if (!metodoPago || !metodoPago.value) {
                if (window.pedidosManager) {
                    pedidosManager.showToast('warning', 'Campo requerido', 'Debes seleccionar un método de pago');
                } else {
                    alert('Debes seleccionar un método de pago');
                }
                metodoPago?.focus();
                return false;
            }
            
            // Eliminar inputs anteriores de productos para evitar duplicados
            form.querySelectorAll('input[name^="productos["]').forEach(input => {
                if (!input.classList.contains('cantidad-input') && !input.classList.contains('producto-checkbox')) {
                    input.remove();
                }
            });
            
            // Crear campos de formulario dinámicamente desde el carrito
            let index = 0;
            window.cart.forEach((item, productoId) => {
                // Campo para producto_id
                const inputProductoId = document.createElement('input');
                inputProductoId.type = 'hidden';
                inputProductoId.name = `productos[${index}][producto_id]`;
                inputProductoId.value = productoId;
                form.appendChild(inputProductoId);
                
                // Campo para cantidad
                const inputCantidad = document.createElement('input');
                inputCantidad.type = 'hidden';
                inputCantidad.name = `productos[${index}][cantidad]`;
                inputCantidad.value = item.cantidad;
                form.appendChild(inputCantidad);
                
                index++;
            });
            
            // Marcar como enviando
            this.classList.add('submitting');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Procesando...';
            
            if (window.pedidosManager) {
                pedidosManager.showLoading('Procesando tu pedido...');
            }
            
            // Ahora sí enviar el formulario
            this.submit();
        });
    }
    
    // Mostrar mensajes flash
    @if(session('success'))
        if (window.pedidosManager) {
            pedidosManager.showToast('success', 'Éxito', '{{ session('success') }}');
        }
    @endif
    
    @if(session('error'))
        if (window.pedidosManager) {
            pedidosManager.showToast('error', 'Error', '{{ session('error') }}');
        }
    @endif
    
    @if($errors->any())
        if (window.pedidosManager) {
            pedidosManager.showToast('error', 'Error', 'Por favor corrige los errores en el formulario');
        }
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
    
    // Actualizar en el carrito global
    if (window.cart && window.cart.has(productoId)) {
        const item = window.cart.get(productoId);
        item.cantidad = nuevaCantidad;
        window.cart.set(productoId, item);
        
        // Actualizar vista del carrito
        if (typeof window.updateCart === 'function') {
            window.updateCart();
        }
    }
}

// ===========================================
// FUNCIONES DE NAVEGACIÓN Y UI
// ===========================================

/**
 * Volver a la página anterior
 */
function volverAtras() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route("cliente.dashboard") }}';
    }
}

/**
 * Limpiar selección de productos
 */
function limpiarSeleccion() {
    if (!confirm('¿Estás seguro de que deseas limpiar todos los productos seleccionados?')) {
        return;
    }
    
    document.querySelectorAll('.producto-checkbox:checked').forEach(checkbox => {
        checkbox.checked = false;
        const event = new Event('change');
        checkbox.dispatchEvent(event);
    });
    
    window.pedidosManager.showToast('success', 'Limpiado', 'Selección de productos limpiada');
}

/**
 * Cargar productos desde el carrito de localStorage
 */
function cargarDesdeCarrito() {
    const carritoLS = JSON.parse(localStorage.getItem('carrito')) || [];
    
    if (carritoLS.length === 0) {
        window.pedidosManager.showToast('info', 'Carrito vacío', 'No hay productos en tu carrito');
        return;
    }
    
    let cargados = 0;
    
    carritoLS.forEach(item => {
        const checkbox = document.querySelector(`input[value="${item.id}"]`);
        if (checkbox && !checkbox.disabled) {
            checkbox.checked = true;
            const container = checkbox.closest('.pedidos-product-checkbox');
            const qtyDiv = container.querySelector('.pedidos-product-quantity');
            const cantidadInput = qtyDiv.querySelector('.cantidad-input');
            
            container.classList.add('selected');
            qtyDiv.style.display = 'flex';
            cantidadInput.value = item.cantidad || 1;
            
            const event = new Event('change');
            checkbox.dispatchEvent(event);
            
            cargados++;
        }
    });
    
    if (cargados > 0) {
        window.pedidosManager.showToast('success', '¡Cargado!', `${cargados} producto(s) cargado(s) desde tu carrito`);
        localStorage.removeItem('carrito'); // Limpiar localStorage
    } else {
        window.pedidosManager.showToast('warning', 'Aviso', 'No se pudieron cargar productos (sin stock o no disponibles)');
    }
}

/**
 * Limpiar búsqueda
 */
function limpiarBusqueda() {
    const searchInput = document.getElementById('searchProductos');
    searchInput.value = '';
    
    // Mostrar todos los productos
    document.querySelectorAll('.producto-item').forEach(item => {
        item.style.display = 'block';
    });
    
    actualizarContador();
}

/**
 * Toggle panel de filtros avanzados
 */
function toggleFiltroPrecio() {
    const panel = document.getElementById('filtrosAvanzados');
    const btn = event.currentTarget;
    
    if (panel.classList.contains('show')) {
        panel.classList.remove('show');
        btn.querySelector('i').classList.remove('bi-funnel-fill');
        btn.querySelector('i').classList.add('bi-funnel');
    } else {
        panel.classList.add('show');
        btn.querySelector('i').classList.remove('bi-funnel');
        btn.querySelector('i').classList.add('bi-funnel-fill');
    }
}

/**
 * Aplicar filtros avanzados
 */
function aplicarFiltros() {
    const precioMin = parseFloat(document.getElementById('precioMin').value) || 0;
    const precioMax = parseFloat(document.getElementById('precioMax').value) || Infinity;
    const ordenar = document.getElementById('ordenarPor').value;
    const categoriaFiltro = document.getElementById('filtroCategoria').value;
    const busqueda = document.getElementById('searchProductos').value.toLowerCase();
    
    let productosVisibles = [];
    
    // Filtrar productos
    document.querySelectorAll('.producto-item').forEach(item => {
        const precio = parseFloat(item.closest('.categoria-section')?.querySelector(`[data-producto-id="${item.dataset.productoId}"] .pedidos-product-checkbox`).dataset.precio) || 0;
        const nombre = item.dataset.nombre;
        const categoria = item.closest('.categoria-section')?.dataset.categoria || '';
        
        let mostrar = true;
        
        // Filtro de precio
        if (precio < precioMin || precio > precioMax) {
            mostrar = false;
        }
        
        // Filtro de categoría
        if (categoriaFiltro && categoria !== categoriaFiltro) {
            mostrar = false;
        }
        
        // Filtro de búsqueda
        if (busqueda && !nombre.includes(busqueda)) {
            mostrar = false;
        }
        
        item.style.display = mostrar ? 'block' : 'none';
        
        if (mostrar) {
            productosVisibles.push({
                element: item,
                precio: precio,
                nombre: nombre
            });
        }
    });
    
    // Ordenar
    if (ordenar && productosVisibles.length > 0) {
        const container = document.getElementById('productosContainer');
        
        productosVisibles.sort((a, b) => {
            switch(ordenar) {
                case 'precio-asc':
                    return a.precio - b.precio;
                case 'precio-desc':
                    return b.precio - a.precio;
                case 'nombre':
                    return a.nombre.localeCompare(b.nombre);
                case 'stock':
                    const stockA = parseInt(a.element.querySelector('.pedidos-product-checkbox').dataset.stock) || 0;
                    const stockB = parseInt(b.element.querySelector('.pedidos-product-checkbox').dataset.stock) || 0;
                    return stockB - stockA;
                default:
                    return 0;
            }
        });
        
        // Reordenar en el DOM
        productosVisibles.forEach(item => {
            const categoriaSection = item.element.closest('.categoria-section');
            const row = categoriaSection.querySelector('.row');
            row.appendChild(item.element);
        });
    }
    
    actualizarContador();
    
    window.pedidosManager.showToast('success', 'Filtros', 'Filtros aplicados correctamente');
}

/**
 * Resetear filtros
 */
function resetearFiltros() {
    document.getElementById('precioMin').value = '';
    document.getElementById('precioMax').value = '';
    document.getElementById('ordenarPor').value = 'nombre';
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('searchProductos').value = '';
    
    // Mostrar todos los productos
    document.querySelectorAll('.producto-item').forEach(item => {
        item.style.display = 'block';
    });
    
    actualizarContador();
    
    window.pedidosManager.showToast('info', 'Resetear', 'Filtros reseteados');
}

/**
 * Actualizar contador de productos visibles
 */
function actualizarContador() {
    const total = document.querySelectorAll('.producto-item').length;
    const visibles = document.querySelectorAll('.producto-item[style*="display: block"], .producto-item:not([style*="display"])').length;
    const seleccionados = document.querySelectorAll('.producto-checkbox:checked').length;
    
    document.getElementById('contadorProductos').textContent = 
        visibles === total ? 
        `Mostrando ${total} producto(s)` : 
        `Mostrando ${visibles} de ${total} producto(s)`;
    
    document.getElementById('productosSeleccionados').textContent = `${seleccionados} seleccionados`;
}

// Escuchar cambios en filtros
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

// Actualizar contador al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    actualizarContador();
    
    // Escuchar cambios en checkboxes para actualizar contador
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarContador);
    });
});
</script>
@endpush
