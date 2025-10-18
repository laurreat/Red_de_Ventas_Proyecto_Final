@extends('layouts.vendedor')

@section('title', 'Editar Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-professional.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-professional.css')) }}">
@endpush

@section('content')
<!-- Header -->
<div class="pedidos-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="pedidos-header-icon-badge">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h1 class="pedidos-header-title">
                Editar Pedido #{{ $pedido->numero_pedido }}
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-info-circle me-2"></i>
                Modifica los datos del pedido
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                <a href="{{ route('vendedor.pedidos.show', $pedido->_id) }}" class="pedidos-btn-secondary">
                    <i class="bi bi-x-circle"></i>
                    <span>Cancelar</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form action="{{ route('vendedor.pedidos.update', $pedido->_id) }}" method="POST" enctype="application/x-www-form-urlencoded">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-lg-8">
            <!-- Cliente (Solo lectura) -->
            <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-1">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-person-badge"></i>
                            Cliente del Pedido
                        </h3>
                    </div>
                    <span class="badge" style="background: var(--wine-primary); padding: 0.5rem 1rem; font-size: 0.875rem;">
                        <i class="bi bi-lock-fill"></i> No editable
                    </span>
                </div>
                <div class="p-4">
                    @php
                        $clienteNombre = $pedido->cliente_data['name'] ?? $pedido->cliente->name ?? 'Cliente no encontrado';
                        $clienteEmail = $pedido->cliente_data['email'] ?? $pedido->cliente->email ?? '';
                        $clienteTelefono = $pedido->cliente_data['phone'] ?? $pedido->cliente->phone ?? '';
                    @endphp
                    <div class="card" style="background: var(--gray-50); border: 2px solid var(--gray-200); border-radius: var(--radius-md);">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="pedidos-header-icon-badge" style="width: 60px; height: 60px;">
                                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold" style="color: var(--wine-primary);">{{ $clienteNombre }}</h5>
                                    @if($clienteEmail)
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-envelope-fill me-2"></i>{{ $clienteEmail }}
                                    </p>
                                    @endif
                                    @if($clienteTelefono)
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-telephone-fill me-2"></i>{{ $clienteTelefono }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="cliente_id" value="{{ $pedido->cliente_id }}">
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle"></i> El cliente no puede ser modificado una vez creado el pedido
                    </small>
                </div>
            </div>

            <!-- Productos -->
            <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-2">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-cart"></i>
                            Productos del Pedido
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div id="productos-container">
                        @php
                            $productosData = $pedido->productos ?? $pedido->detalles ?? [];
                            if (empty($productosData) || !is_array($productosData)) {
                                $productosData = []; // Array vacío por defecto
                            }
                        @endphp
                        
                        @if(count($productosData) > 0)
                            @foreach($productosData as $index => $producto)
                            @php
                                // Obtener ID del producto de diferentes estructuras posibles
                                $productoId = $producto['producto_id'] 
                                    ?? ($producto['producto']['_id'] ?? null)
                                    ?? ($producto['producto_data']['_id'] ?? null);
                                
                                // Obtener nombre para mostrar
                                $productoNombre = $producto['nombre'] 
                                    ?? ($producto['producto_data']['nombre'] ?? null)
                                    ?? ($producto['producto']['nombre'] ?? 'Producto sin nombre');
                                
                                // Obtener cantidad y precio
                                $cantidad = $producto['cantidad'] ?? 1;
                                $precio = $producto['precio'] 
                                    ?? $producto['precio_unitario'] 
                                    ?? ($producto['producto_data']['precio'] ?? 0);
                            @endphp
                            <div class="producto-item mb-3 p-3" style="background: var(--gray-50); border-radius: var(--radius-md); border: 2px solid var(--gray-200);" data-old-producto-id="{{ $productoId }}" data-old-cantidad="{{ $cantidad }}">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-box-seam"></i> Producto
                                        </label>
                                        <select name="productos[{{ $index }}][id]" class="pedidos-filter-select producto-select" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($productos as $prod)
                                            <option value="{{ $prod->_id }}" 
                                                    data-precio="{{ to_float($prod->precio) }}" 
                                                    data-stock="{{ $prod->stock }}"
                                                    data-nombre="{{ $prod->nombre }}"
                                                    {{ $productoId == $prod->_id ? 'selected' : '' }}>
                                                {{ $prod->nombre }} (Stock: {{ $prod->stock }})
                                            </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="bi bi-info-circle"></i> Seleccione un producto del inventario
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-hash"></i> Cantidad
                                        </label>
                                        <input type="number" 
                                               name="productos[{{ $index }}][cantidad]" 
                                               class="pedidos-filter-input cantidad-input" 
                                               min="1" 
                                               value="{{ $cantidad }}" 
                                               placeholder="1"
                                               required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-currency-dollar"></i> Precio Unit.
                                        </label>
                                        <input type="number" 
                                               name="productos[{{ $index }}][precio]" 
                                               class="pedidos-filter-input precio-input" 
                                               min="0" 
                                               step="0.01" 
                                               value="{{ to_float($precio) }}" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" 
                                                class="pedidos-action-btn pedidos-action-btn-delete remove-producto" 
                                                style="width: 100%;"
                                                title="Eliminar producto">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0" style="padding: 0.75rem; font-size: 0.875rem;">
                                            <i class="bi bi-calculator"></i> 
                                            <strong>Subtotal:</strong> 
                                            <span class="subtotal-display">${{ number_format(to_float($cantidad * $precio), 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- Si no hay productos, mostrar uno vacío -->
                            <div class="producto-item mb-3 p-3" style="background: var(--gray-50); border-radius: var(--radius-md); border: 2px solid var(--gray-200);">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-box-seam"></i> Producto
                                        </label>
                                        <select name="productos[0][id]" class="pedidos-filter-select producto-select" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($productos as $prod)
                                            <option value="{{ $prod->_id }}" 
                                                    data-precio="{{ to_float($prod->precio) }}" 
                                                    data-stock="{{ $prod->stock }}"
                                                    data-nombre="{{ $prod->nombre }}">
                                                {{ $prod->nombre }} (Stock: {{ $prod->stock }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-hash"></i> Cantidad
                                        </label>
                                        <input type="number" 
                                               name="productos[0][cantidad]" 
                                               class="pedidos-filter-input cantidad-input" 
                                               min="1" 
                                               value="1" 
                                               required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="pedidos-filter-label">
                                            <i class="bi bi-currency-dollar"></i> Precio Unit.
                                        </label>
                                        <input type="number" 
                                               name="productos[0][precio]" 
                                               class="pedidos-filter-input precio-input" 
                                               min="0" 
                                               step="0.01" 
                                               value="0" 
                                               required>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" 
                                                class="pedidos-action-btn pedidos-action-btn-delete remove-producto" 
                                                style="width: 100%;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0" style="padding: 0.75rem; font-size: 0.875rem;">
                                            <i class="bi bi-calculator"></i> 
                                            <strong>Subtotal:</strong> 
                                            <span class="subtotal-display">$0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-producto" class="pedidos-btn-filter-secondary mt-3">
                        <i class="bi bi-plus-circle"></i>
                        Agregar Otro Producto
                    </button>
                    
                    <div class="alert alert-success mt-3" style="font-size: 0.875rem;">
                        <i class="bi bi-lightbulb-fill"></i>
                        <strong>Tip:</strong> Puedes modificar los productos existentes o agregar nuevos. El precio se carga automáticamente al seleccionar el producto.
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="pedidos-table-wrapper fade-in-up animate-delay-3">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-info-circle"></i>
                            Información Adicional
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="pedidos-filter-label">
                                <i class="bi bi-geo-alt"></i>
                                Dirección de Entrega
                            </label>
                            <input type="text" name="direccion_entrega" class="pedidos-filter-input" 
                                   value="{{ $pedido->direccion_entrega ?? '' }}" placeholder="Calle 123 #45-67">
                        </div>
                        <div class="col-md-6">
                            <label class="pedidos-filter-label">
                                <i class="bi bi-telephone"></i>
                                Teléfono de Contacto
                            </label>
                            <input type="text" name="telefono_entrega" class="pedidos-filter-input" 
                                   value="{{ $pedido->telefono_entrega ?? '' }}" placeholder="300 123 4567">
                        </div>
                        <div class="col-12">
                            <label class="pedidos-filter-label">
                                <i class="bi bi-sticky"></i>
                                Notas
                            </label>
                            <textarea name="notas" class="pedidos-filter-input" rows="3" 
                                      placeholder="Observaciones o instrucciones especiales...">{{ $pedido->notas ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Resumen -->
            <div class="pedidos-table-wrapper fade-in-up animate-delay-1" style="position: sticky; top: 20px;">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-calculator"></i>
                            Resumen
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal:</span>
                        <strong id="subtotal-display">${{ number_format(to_float($pedido->subtotal ?? 0), 0) }}</strong>
                    </div>
                    <div class="pedidos-filter-item mb-3">
                        <label class="pedidos-filter-label">Descuento ($)</label>
                        <input type="number" name="descuento" id="descuento-input" class="pedidos-filter-input" 
                               min="0" step="0.01" value="{{ to_float($pedido->descuento ?? 0) }}">
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold" style="font-size: 1.125rem;">Total:</span>
                        <strong class="text-wine" style="font-size: 1.5rem;" id="total-display">
                            ${{ number_format(to_float($pedido->total_final ?? 0), 0) }}
                        </strong>
                    </div>
                    
                    <button type="submit" class="pedidos-btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle"></i>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('vendedor.pedidos.show', $pedido->_id) }}" class="pedidos-btn-secondary w-100">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="subtotal" id="subtotal-hidden">
    <input type="hidden" name="total_final" id="total-hidden">
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/pedidos-modern.js') }}?v={{ filemtime(public_path('js/vendedor/pedidos-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoIndex = {{ count($productosData) > 0 ? count($productosData) : 1 }};
    
    // Función para actualizar subtotal de un producto
    function actualizarSubtotalProducto(productoItem) {
        const cantidad = parseFloat(productoItem.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(productoItem.querySelector('.precio-input').value) || 0;
        const subtotal = cantidad * precio;
        
        const subtotalDisplay = productoItem.querySelector('.subtotal-display');
        if (subtotalDisplay) {
            subtotalDisplay.textContent = '$' + Math.round(subtotal).toLocaleString('es-CO');
        }
    }
    
    // Validación de stock deshabilitada
    function validarStock(productoItem) {
        return true;
    }
    
    // Calcular totales generales
    function calcularTotales() {
        let subtotal = 0;
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const cantidad = parseFloat(item.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(item.querySelector('.precio-input').value) || 0;
            subtotal += cantidad * precio;
            
            // Actualizar subtotal individual
            actualizarSubtotalProducto(item);
            
        });
        
        const descuento = parseFloat(document.getElementById('descuento-input').value) || 0;
        const total = subtotal - descuento;
        
        document.getElementById('subtotal-display').textContent = '$' + Math.round(subtotal).toLocaleString('es-CO');
        document.getElementById('total-display').textContent = '$' + Math.round(total).toLocaleString('es-CO');
        
        document.getElementById('subtotal-hidden').value = subtotal;
        document.getElementById('total-hidden').value = total;
    }
    
    // Event listeners para inputs
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input') || 
            e.target.classList.contains('precio-input') ||
            e.target.id === 'descuento-input') {
            calcularTotales();
        }
    });
    
    // Event listener para selección de producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const option = e.target.options[e.target.selectedIndex];
            const precio = option.dataset.precio || 0;
            const stock = option.dataset.stock || 0;
            const nombre = option.dataset.nombre || '';
            
            const item = e.target.closest('.producto-item');
            const precioInput = item.querySelector('.precio-input');
            
            precioInput.value = precio;
            
            // Mostrar notificación con información del producto
            if (option.value && typeof pedidosManager !== 'undefined') {
                pedidosManager.showToast(`${nombre} - Stock disponible: ${stock} unidades`, 'info');
            }
            
            calcularTotales();
        }
    });
    
    // Agregar nuevo producto
    document.getElementById('add-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const div = document.createElement('div');
        div.className = 'producto-item mb-3 p-3';
        div.style.cssText = 'background: var(--gray-50); border-radius: var(--radius-md); border: 2px solid var(--gray-200);';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="pedidos-filter-label">
                        <i class="bi bi-box-seam"></i> Producto
                    </label>
                    <select name="productos[${productoIndex}][id]" class="pedidos-filter-select producto-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($productos as $prod)
                        <option value="{{ $prod->_id }}" 
                                data-precio="{{ to_float($prod->precio) }}" 
                                data-stock="{{ $prod->stock }}"
                                data-nombre="{{ $prod->nombre }}">
                            {{ $prod->nombre }} (Stock: {{ $prod->stock }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-info-circle"></i> Seleccione un producto del inventario
                    </small>
                </div>
                <div class="col-md-3">
                    <label class="pedidos-filter-label">
                        <i class="bi bi-hash"></i> Cantidad
                    </label>
                    <input type="number" 
                           name="productos[${productoIndex}][cantidad]" 
                           class="pedidos-filter-input cantidad-input" 
                           min="1" 
                           value="1" 
                           placeholder="1"
                           required>
                </div>
                <div class="col-md-3">
                    <label class="pedidos-filter-label">
                        <i class="bi bi-currency-dollar"></i> Precio Unit.
                    </label>
                    <input type="number" 
                           name="productos[${productoIndex}][precio]" 
                           class="pedidos-filter-input precio-input" 
                           min="0" 
                           step="0.01" 
                           value="0" 
                           placeholder="0.00"
                           required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" 
                            class="pedidos-action-btn pedidos-action-btn-delete remove-producto" 
                            style="width: 100%;"
                            title="Eliminar producto">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="alert alert-info mb-0" style="padding: 0.75rem; font-size: 0.875rem;">
                        <i class="bi bi-calculator"></i> 
                        <strong>Subtotal:</strong> 
                        <span class="subtotal-display">$0</span>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        productoIndex++;
        
        // Animación de entrada
        div.style.opacity = '0';
        div.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            div.style.transition = 'all 0.3s ease';
            div.style.opacity = '1';
            div.style.transform = 'translateY(0)';
        }, 10);
        
        if (typeof pedidosManager !== 'undefined') {
            pedidosManager.showToast('Producto agregado. Seleccione el producto del inventario', 'success');
        }
    });
    
    // Remover producto
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-producto')) {
            const item = e.target.closest('.producto-item');
            const totalItems = document.querySelectorAll('.producto-item').length;
            
            if (totalItems > 1) {
                // Animación de salida
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    item.remove();
                    calcularTotales();
                    
                    if (typeof pedidosManager !== 'undefined') {
                        pedidosManager.showToast('Producto eliminado', 'info');
                    }
                }, 300);
            } else {
                if (typeof pedidosManager !== 'undefined') {
                    pedidosManager.showToast('Debe haber al menos un producto en el pedido', 'warning');
                }
            }
        }
    });
    
    // Validación al enviar deshabilitada (siempre permite guardar)
    document.querySelector('form').addEventListener('submit', function(e) {
        calcularTotales();
    });
    
    // Calcular totales al cargar
    calcularTotales();
});

// Estilos adicionales
const styleEdit = document.createElement('style');
styleEdit.textContent = `
    .text-wine {
        color: var(--wine-primary);
    }
    
    .producto-item {
        transition: all 0.3s ease;
    }
    
    .producto-item:hover {
        box-shadow: 0 4px 12px rgba(114, 47, 55, 0.1);
        transform: translateY(-2px);
    }
`;
document.head.appendChild(styleEdit);
</script>
@endpush
