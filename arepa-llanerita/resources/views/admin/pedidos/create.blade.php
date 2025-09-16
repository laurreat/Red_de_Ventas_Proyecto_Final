@extends('layouts.admin')

@section('title', '- Crear Pedido')
@section('page-title', 'Crear Nuevo Pedido')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Crear un nuevo pedido para un cliente</p>
                </div>
                <div>
                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver a Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.pedidos.store') }}" method="POST" id="pedidoForm">
        @csrf

        <div class="row">
            <!-- Información del Pedido -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-info-circle me-2"></i>
                            Información del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">Cliente *</label>
                                <select class="form-select @error('cliente_id') is-invalid @enderror"
                                        id="cliente_id"
                                        name="cliente_id"
                                        required>
                                    <option value="">Seleccionar cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                                {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->name }} - {{ $cliente->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vendedor_id" class="form-label">Vendedor</label>
                                <select class="form-select @error('vendedor_id') is-invalid @enderror"
                                        id="vendedor_id"
                                        name="vendedor_id">
                                    <option value="">Sin vendedor asignado</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}"
                                                {{ old('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                            {{ $vendedor->name }} - {{ $vendedor->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendedor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="descuento" class="form-label">Descuento (COP)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('descuento') is-invalid @enderror"
                                           id="descuento"
                                           name="descuento"
                                           value="{{ old('descuento', 0) }}"
                                           min="0"
                                           step="100"
                                           onchange="calcularTotal()">
                                </div>
                                @error('descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Observaciones adicionales del pedido...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-box-seam me-2"></i>
                            Productos del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Selector de Productos -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Seleccionar Producto</label>
                                <select class="form-select" id="producto_selector">
                                    <option value="">Buscar producto...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}"
                                                data-nombre="{{ $producto->nombre }}"
                                                data-precio="{{ $producto->precio }}"
                                                data-stock="{{ $producto->stock }}"
                                                data-imagen="{{ $producto->imagen }}">
                                            {{ $producto->nombre }} - ${{ number_format($producto->precio, 0) }} (Stock: {{ $producto->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad_input" min="1" value="1">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary d-block w-100" onclick="agregarProducto()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Productos Agregados -->
                        <div id="productos-container">
                            <div class="text-center py-4 text-muted" id="sin-productos">
                                <i class="bi bi-box fs-1"></i>
                                <p class="mt-2">No hay productos agregados</p>
                            </div>
                        </div>

                        @error('productos')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Resumen y Acciones -->
            <div class="col-lg-4">
                <!-- Resumen del Pedido -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-calculator me-2"></i>
                            Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos:</span>
                            <span id="cantidad-productos">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-display">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <span id="descuento-display">$0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Final:</strong>
                            <strong id="total-final" style="color: var(--primary-color); font-size: 1.1em;">$0</strong>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-crear" disabled>
                                <i class="bi bi-check-circle me-1"></i>
                                Crear Pedido
                            </button>
                            <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let productosSeleccionados = [];
let contadorProductos = 0;

function agregarProducto() {
    const selector = document.getElementById('producto_selector');
    const cantidadInput = document.getElementById('cantidad_input');
    const cantidad = parseInt(cantidadInput.value);

    if (!selector.value || cantidad <= 0) {
        alert('Selecciona un producto y una cantidad válida');
        return;
    }

    const option = selector.options[selector.selectedIndex];
    const producto = {
        id: selector.value,
        nombre: option.dataset.nombre,
        precio: parseFloat(option.dataset.precio),
        stock: parseInt(option.dataset.stock),
        imagen: option.dataset.imagen,
        cantidad: cantidad
    };

    if (cantidad > producto.stock) {
        alert('La cantidad no puede ser mayor al stock disponible (' + producto.stock + ')');
        return;
    }

    // Verificar si el producto ya está agregado
    const existeIndex = productosSeleccionados.findIndex(p => p.id === producto.id);
    if (existeIndex !== -1) {
        productosSeleccionados[existeIndex].cantidad += cantidad;
        if (productosSeleccionados[existeIndex].cantidad > producto.stock) {
            alert('La cantidad total no puede ser mayor al stock disponible (' + producto.stock + ')');
            productosSeleccionados[existeIndex].cantidad -= cantidad;
            return;
        }
    } else {
        productosSeleccionados.push(producto);
    }

    // Limpiar selector
    selector.value = '';
    cantidadInput.value = 1;

    actualizarListaProductos();
    calcularTotal();
}

function eliminarProducto(index) {
    productosSeleccionados.splice(index, 1);
    actualizarListaProductos();
    calcularTotal();
}

function cambiarCantidad(index, nuevaCantidad) {
    if (nuevaCantidad <= 0) {
        eliminarProducto(index);
        return;
    }

    if (nuevaCantidad > productosSeleccionados[index].stock) {
        alert('La cantidad no puede ser mayor al stock disponible (' + productosSeleccionados[index].stock + ')');
        return;
    }

    productosSeleccionados[index].cantidad = nuevaCantidad;
    calcularTotal();
}

function actualizarListaProductos() {
    const container = document.getElementById('productos-container');
    const sinProductos = document.getElementById('sin-productos');

    if (productosSeleccionados.length === 0) {
        sinProductos.style.display = 'block';
        container.innerHTML = sinProductos.outerHTML;
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th><th>Acciones</th></tr></thead><tbody>';

    productosSeleccionados.forEach((producto, index) => {
        const total = producto.precio * producto.cantidad;
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            ${producto.imagen ?
                                `<img src="/storage/${producto.imagen}" alt="${producto.nombre}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">` :
                                `<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-image text-white"></i></div>`
                            }
                        </div>
                        <div>
                            <div class="fw-medium">${producto.nombre}</div>
                            <small class="text-muted">Stock: ${producto.stock}</small>
                        </div>
                    </div>
                </td>
                <td><strong>$${producto.precio.toLocaleString()}</strong></td>
                <td>
                    <input type="number" class="form-control form-control-sm" style="width: 80px;"
                           value="${producto.cantidad}" min="1" max="${producto.stock}"
                           onchange="cambiarCantidad(${index}, parseInt(this.value))">
                </td>
                <td><strong>$${total.toLocaleString()}</strong></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <input type="hidden" name="productos[${index}][id]" value="${producto.id}">
            <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function calcularTotal() {
    const descuentoInput = document.getElementById('descuento');
    const descuento = parseFloat(descuentoInput.value) || 0;

    let subtotal = 0;
    productosSeleccionados.forEach(producto => {
        subtotal += producto.precio * producto.cantidad;
    });

    const totalFinal = Math.max(0, subtotal - descuento);

    document.getElementById('cantidad-productos').textContent = productosSeleccionados.length;
    document.getElementById('subtotal-display').textContent = '$' + subtotal.toLocaleString();
    document.getElementById('descuento-display').textContent = '$' + descuento.toLocaleString();
    document.getElementById('total-final').textContent = '$' + totalFinal.toLocaleString();

    // Habilitar/deshabilitar botón crear
    const btnCrear = document.getElementById('btn-crear');
    btnCrear.disabled = productosSeleccionados.length === 0;
}

// Inicializar cálculos
document.addEventListener('DOMContentLoaded', function() {
    calcularTotal();
});
</script>
@endsection