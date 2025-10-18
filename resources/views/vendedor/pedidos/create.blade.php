@extends('layouts.vendedor')

@section('title', 'Nuevo Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-professional.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-professional.css')) }}">
@endpush

@section('content')
<!-- Header -->
<div class="pedidos-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="pedidos-header-icon-badge">
                <i class="bi bi-plus-circle"></i>
            </div>
            <h1 class="pedidos-header-title">
                Crear Nuevo Pedido
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-info-circle me-2"></i>
                Complete los datos para registrar un nuevo pedido
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                <a href="{{ route('vendedor.pedidos.index') }}" class="pedidos-btn-secondary">
                    <i class="bi bi-x-circle"></i>
                    <span>Cancelar</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<form action="{{ route('vendedor.pedidos.store') }}" method="POST">
    @csrf
    
    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-lg-8">
            <!-- Cliente -->
            <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-1">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-person-badge"></i>
                            Cliente
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="pedidos-filter-item">
                        <label class="pedidos-filter-label">
                            <i class="bi bi-person"></i>
                            Seleccionar Cliente
                        </label>
                        <select name="cliente_id" class="pedidos-filter-select" required>
                            <option value="">Seleccione un cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->_id }}">
                                {{ $cliente->name }} - {{ $cliente->email }}
                            </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-2">
                <div class="pedidos-table-header">
                    <div class="pedidos-table-header-left">
                        <h3 class="pedidos-table-title">
                            <i class="bi bi-cart"></i>
                            Productos
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div id="productos-container">
                        <div class="producto-item mb-3 p-3" style="background: var(--gray-50); border-radius: var(--radius-md);">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="pedidos-filter-label">Producto</label>
                                    <select name="productos[0][id]" class="pedidos-filter-select producto-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($productos as $producto)
                                        <option value="{{ $producto->_id }}" 
                                                data-precio="{{ $producto->precio }}" 
                                                data-stock="{{ $producto->stock }}">
                                            {{ $producto->nombre }} (Stock: {{ $producto->stock }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="pedidos-filter-label">Cantidad</label>
                                    <input type="number" name="productos[0][cantidad]" 
                                           class="pedidos-filter-input cantidad-input" 
                                           min="1" value="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="pedidos-filter-label">Precio</label>
                                    <input type="number" name="productos[0][precio]" 
                                           class="pedidos-filter-input precio-input" 
                                           min="0" step="0.01" value="0" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="pedidos-action-btn pedidos-action-btn-delete remove-producto" style="width: 100%;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="add-producto" class="pedidos-btn-filter-secondary mt-3">
                        <i class="bi bi-plus-circle"></i>
                        Agregar Producto
                    </button>
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
                                   placeholder="Calle 123 #45-67">
                        </div>
                        <div class="col-md-6">
                            <label class="pedidos-filter-label">
                                <i class="bi bi-telephone"></i>
                                Teléfono de Contacto
                            </label>
                            <input type="text" name="telefono_entrega" class="pedidos-filter-input" 
                                   placeholder="300 123 4567">
                        </div>
                        <div class="col-12">
                            <label class="pedidos-filter-label">
                                <i class="bi bi-sticky"></i>
                                Notas
                            </label>
                            <textarea name="notas" class="pedidos-filter-input" rows="3" 
                                      placeholder="Observaciones o instrucciones especiales..."></textarea>
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
                        <strong id="subtotal-display">$0</strong>
                    </div>
                    <div class="pedidos-filter-item mb-3">
                        <label class="pedidos-filter-label">Descuento ($)</label>
                        <input type="number" name="descuento" id="descuento-input" class="pedidos-filter-input" 
                               min="0" step="0.01" value="0">
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">IVA (19%):</span>
                        <strong id="iva-display">$0</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold" style="font-size: 1.125rem;">Total:</span>
                        <strong class="text-wine" style="font-size: 1.5rem;" id="total-display">$0</strong>
                    </div>
                    
                    <button type="submit" class="pedidos-btn-primary w-100 mb-2">
                        <i class="bi bi-check-circle"></i>
                        Crear Pedido
                    </button>
                    <a href="{{ route('vendedor.pedidos.index') }}" class="pedidos-btn-secondary w-100">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="subtotal" id="subtotal-hidden">
    <input type="hidden" name="iva" id="iva-hidden">
    <input type="hidden" name="total_final" id="total-hidden">
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/pedidos-modern.js') }}?v={{ filemtime(public_path('js/vendedor/pedidos-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoIndex = 1;
    
    // Calcular totales
    function calcularTotales() {
        let subtotal = 0;
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const cantidad = parseFloat(item.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(item.querySelector('.precio-input').value) || 0;
            subtotal += cantidad * precio;
        });
        
        const descuento = parseFloat(document.getElementById('descuento-input').value) || 0;
        const iva = (subtotal - descuento) * 0.19;
        const total = subtotal - descuento + iva;
        
        document.getElementById('subtotal-display').textContent = '$' + Math.round(subtotal).toLocaleString();
        document.getElementById('iva-display').textContent = '$' + Math.round(iva).toLocaleString();
        document.getElementById('total-display').textContent = '$' + Math.round(total).toLocaleString();
        
        document.getElementById('subtotal-hidden').value = subtotal;
        document.getElementById('iva-hidden').value = iva;
        document.getElementById('total-hidden').value = total;
    }
    
    // Event listeners
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input') || 
            e.target.classList.contains('precio-input') ||
            e.target.id === 'descuento-input') {
            calcularTotales();
        }
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const option = e.target.options[e.target.selectedIndex];
            const precio = option.dataset.precio || 0;
            const item = e.target.closest('.producto-item');
            item.querySelector('.precio-input').value = precio;
            calcularTotales();
        }
    });
    
    // Agregar producto
    document.getElementById('add-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const div = document.createElement('div');
        div.className = 'producto-item mb-3 p-3';
        div.style.cssText = 'background: var(--gray-50); border-radius: var(--radius-md);';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="pedidos-filter-label">Producto</label>
                    <select name="productos[${productoIndex}][id]" class="pedidos-filter-select producto-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->_id }}" data-precio="{{ $producto->precio }}" data-stock="{{ $producto->stock }}">
                            {{ $producto->nombre }} (Stock: {{ $producto->stock }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="pedidos-filter-label">Cantidad</label>
                    <input type="number" name="productos[${productoIndex}][cantidad]" class="pedidos-filter-input cantidad-input" min="1" value="1" required>
                </div>
                <div class="col-md-3">
                    <label class="pedidos-filter-label">Precio</label>
                    <input type="number" name="productos[${productoIndex}][precio]" class="pedidos-filter-input precio-input" min="0" step="0.01" value="0" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="pedidos-action-btn pedidos-action-btn-delete remove-producto" style="width: 100%;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(div);
        productoIndex++;
    });
    
    // Remover producto
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-producto')) {
            const item = e.target.closest('.producto-item');
            if (document.querySelectorAll('.producto-item').length > 1) {
                item.remove();
                calcularTotales();
            } else {
                pedidosManager.showToast('Debe haber al menos un producto', 'warning');
            }
        }
    });
    
    // Validar antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        const productos = document.querySelectorAll('.producto-item').length;
        if (productos === 0) {
            e.preventDefault();
            pedidosManager.showToast('Debe agregar al menos un producto', 'error');
        }
    });
});

.text-wine {
    color: var(--wine-primary);
}
</script>
@endpush
