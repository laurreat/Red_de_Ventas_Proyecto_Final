@extends('layouts.vendedor')

@section('title', 'Nuevo Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-create-enhanced.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-create-enhanced.css')) }}">
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
<style>
/* Forzar estilos personalizados */
.glass-input-group .form-control,
.glass-input-group .form-select {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border: 2px solid rgba(114, 47, 55, 0.1) !important;
    border-radius: 12px !important;
}

.cliente-info-enhanced {
    display: block !important;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%) !important;
    animation: slideInDown 0.4s ease-out !important;
}

.product-selector-card {
    display: block !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
}

.producto-item-enhanced {
    display: block !important;
    animation: fadeInUp 0.4s ease-out !important;
}

.resumen-card-enhanced {
    display: block !important;
}

.btn-crear-pedido,
.btn-cancelar {
    display: flex !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    {{-- Header Hero --}}
    <div class="pedido-header scale-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="pedido-header-title">
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Pedido
                </h1>
                <p class="pedido-header-subtitle">Complete los datos para registrar un nuevo pedido</p>
            </div>
            <div class="pedido-header-actions">
                <a href="{{ route('vendedor.pedidos.index') }}" class="pedido-btn pedido-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver a Pedidos</span>
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('vendedor.pedidos.store') }}" method="POST" id="pedidoForm" autocomplete="off">
        @csrf

        <div class="row">
            {{-- Información del Pedido --}}
            <div class="col-lg-8">
                {{-- Búsqueda de Cliente --}}
                <div class="pedido-detail-card fade-in-up">
                    <div class="pedido-detail-header">
                        <i class="bi bi-search"></i>
                        <h3 class="pedido-detail-title">Información del Cliente</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="row">
                            {{-- Búsqueda de Cliente por Cédula --}}
                            <div class="col-md-12 mb-4 glass-input-group">
                                <label for="cliente_cedula" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill text-wine"></i> Cédula del Cliente *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-credit-card"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control @error('cliente_id') is-invalid @enderror"
                                           id="cliente_cedula"
                                           placeholder="Ingrese cédula del cliente"
                                           autocomplete="off">
                                    <button class="btn btn-primary" type="button" id="btn-buscar-cliente">
                                        <i class="bi bi-search"></i>
                                        <span class="ms-2">Buscar</span>
                                    </button>
                                </div>

                                {{-- Información del cliente encontrado --}}
                                <div id="cliente-info" style="display: none;">
                                    <div class="cliente-info-enhanced">
                                        <div class="d-flex align-items-center">
                                            <div class="cliente-avatar-enhanced">
                                                <i class="bi bi-person-check-fill"></i>
                                            </div>
                                            <div style="flex:1;">
                                                <div style="font-weight:700;color:#059669;margin-bottom:.25rem;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.5px;">Cliente encontrado</div>
                                                <div style="font-weight:700;color:#111827;font-size:1.125rem;" id="cliente-nombre"></div>
                                                <div style="font-size:.875rem;color:#6b7280;margin-top:0.25rem;" id="cliente-email"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Campo oculto para el ID del cliente --}}
                                <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id') }}">

                                @error('cliente_id')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">
                                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Información de Entrega --}}
                            <div class="col-md-6 mb-3">
                                <label for="direccion_entrega" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt-fill text-info"></i> Dirección de Entrega
                                </label>
                                <input type="text"
                                       class="form-control @error('direccion_entrega') is-invalid @enderror"
                                       id="direccion_entrega"
                                       name="direccion_entrega"
                                       value="{{ old('direccion_entrega') }}"
                                       placeholder="Ej: Calle 123 #45-67"
                                       style="border-radius:10px;padding:.75rem;">
                                @error('direccion_entrega')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono_entrega" class="form-label fw-semibold">
                                    <i class="bi bi-telephone-fill text-info"></i> Teléfono de Contacto
                                </label>
                                <input type="text"
                                       class="form-control @error('telefono_entrega') is-invalid @enderror"
                                       id="telefono_entrega"
                                       name="telefono_entrega"
                                       value="{{ old('telefono_entrega') }}"
                                       placeholder="Ej: 300 123 4567"
                                       style="border-radius:10px;padding:.75rem;">
                                @error('telefono_entrega')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descuento --}}
                            <div class="col-md-6 mb-3">
                                <label for="descuento" class="form-label fw-semibold">
                                    <i class="bi bi-tag-fill text-success"></i> Descuento (COP)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius:10px 0 0 10px;">$</span>
                                    <input type="number"
                                           class="form-control @error('descuento') is-invalid @enderror"
                                           id="descuento"
                                           name="descuento"
                                           value="{{ old('descuento', 0) }}"
                                           min="0"
                                           step="100"
                                           onchange="calcularTotal()"
                                           style="border-radius:0 10px 10px 0;padding:.75rem;">
                                </div>
                                @error('descuento')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notas --}}
                            <div class="col-12 mb-3">
                                <label for="notas" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text-fill text-gray-600"></i> Notas u Observaciones
                                </label>
                                <textarea class="form-control @error('notas') is-invalid @enderror"
                                          id="notas"
                                          name="notas"
                                          rows="3"
                                          placeholder="Observaciones adicionales del pedido..."
                                          style="border-radius:10px;padding:.75rem;">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Productos del Pedido --}}
                <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header" style="justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-box-seam"></i>
                            <h3 class="pedido-detail-title">Productos del Pedido</h3>
                        </div>
                        <button type="button" onclick="vaciarCarrito()" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                            <i class="bi bi-trash"></i>
                            Vaciar Carrito
                        </button>
                    </div>
                    <div class="pedido-detail-body">
                        {{-- Selector de Productos --}}
                        <div class="product-selector-card">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-search"></i> Buscar Producto
                                    </label>
                                    <select class="form-select glass-input-group" id="producto_selector">
                                        <option value="">Seleccione un producto...</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->_id }}"
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
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <input type="number" class="form-control glass-input-group" id="cantidad_input" min="1" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-add-product w-100" onclick="agregarProducto()">
                                        <i class="bi bi-plus-circle-fill"></i>
                                        <span class="ms-1">Agregar</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Lista de Productos Agregados --}}
                        <div id="productos-container">
                            <div class="empty-state-enhanced" id="sin-productos">
                                <div class="icon">
                                    <i class="bi bi-basket"></i>
                                </div>
                                <h4>No hay productos agregados</h4>
                                <p>Busca y agrega productos usando el selector de arriba</p>
                            </div>
                        </div>

                        @error('productos')
                            <div style="color:var(--danger);font-size:.875rem;margin-top:1rem;">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Resumen y Acciones --}}
            <div class="col-lg-4">
                {{-- Resumen del Pedido --}}
                <div class="pedido-detail-card fade-in-up animate-delay-2">
                    <div class="pedido-detail-header">
                        <i class="bi bi-calculator"></i>
                        <h3 class="pedido-detail-title">Resumen del Pedido</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="pedido-info-grid" style="grid-template-columns:1fr;">
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-box-seam"></i> Productos
                                </div>
                                <div class="pedido-info-value" id="cantidad-productos">0</div>
                            </div>
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-cash"></i> Subtotal
                                </div>
                                <div class="pedido-info-value" id="subtotal-display">$0</div>
                            </div>
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-tag"></i> Descuento
                                </div>
                                <div class="pedido-info-value" id="descuento-display" style="color:var(--success);">$0</div>
                            </div>
                            <div class="pedido-info-item" style="background:linear-gradient(135deg,var(--wine),var(--wine-dark));border:none;">
                                <div class="pedido-info-label" style="color:rgba(255,255,255,0.9);">
                                    <i class="bi bi-cash-stack"></i> Total Final
                                </div>
                                <div class="pedido-info-value" id="total-final" style="color:#fff;font-size:1.75rem;">$0</div>
                            </div>
                        </div>

                        <!-- Campos ocultos para enviar con el formulario -->
                        <input type="hidden" name="subtotal" id="subtotal-hidden">
                        <input type="hidden" name="iva" id="iva-hidden">
                        <input type="hidden" name="total_final" id="total-hidden">
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-gear"></i>
                        <h3 class="pedido-detail-title">Acciones</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <button type="submit" class="pedido-btn pedido-btn-primary" id="btn-crear" disabled style="width:100%;margin-bottom:.75rem;justify-content:center;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Crear Pedido</span>
                        </button>
                        <a href="{{ route('vendedor.pedidos.index') }}" class="pedido-btn pedido-btn-outline" style="width:100%;justify-content:center;">
                            <i class="bi bi-x-circle"></i>
                            <span>Cancelar</span>
                        </a>
                    </div>
                </div>

                {{-- Ayuda --}}
                <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;background:rgba(59,130,246,0.05);border-color:var(--info);">
                    <div class="pedido-detail-body" style="padding:1.25rem;">
                        <div style="display:flex;gap:1rem;">
                            <div style="font-size:2rem;color:var(--info);">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <div style="flex:1;">
                                <h6 style="font-weight:700;color:var(--info);margin-bottom:.75rem;">Instrucciones</h6>
                                <ul style="font-size:.875rem;color:var(--gray-700);margin:0;padding-left:1.25rem;">
                                    <li style="margin-bottom:.5rem;">Selecciona el cliente del listado</li>
                                    <li style="margin-bottom:.5rem;">Agrega productos al pedido</li>
                                    <li style="margin-bottom:.5rem;">Aplica descuento si es necesario</li>
                                    <li style="margin-bottom:.5rem;">Verifica el resumen antes de crear</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Módulo JavaScript principal --}}
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

{{-- Funcionalidad específica de Create --}}
<script>
// Variables globales
let productosAgregados = [];
let subtotal = 0;

// Cargar productos del carrito al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarProductosDelCarrito();
});

// Función para cargar productos del localStorage
function cargarProductosDelCarrito() {
    const cart = JSON.parse(localStorage.getItem('pedido_carrito') || '[]');
    
    if (cart.length > 0) {
        productosAgregados = cart;
        renderizarProductos();
        calcularTotal();
        
        if (window.pedidosManager) {
            window.pedidosManager.showToast(`${cart.length} producto(s) cargado(s) del carrito`, 'success', 3000);
        }
    }
}

// Búsqueda de cliente por cédula
document.getElementById('btn-buscar-cliente').addEventListener('click', function() {
    const cedula = document.getElementById('cliente_cedula').value.trim();

    if (!cedula) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('Ingrese una cédula', 'warning');
        }
        return;
    }

    // Mostrar estado de carga
    const btnBuscar = this;
    const originalHTML = btnBuscar.innerHTML;
    btnBuscar.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btnBuscar.disabled = true;

    // Realizar búsqueda AJAX
    fetch('{{ route("vendedor.pedidos.search-cliente") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ cedula: cedula })
    })
    .then(response => response.json())
    .then(data => {
        btnBuscar.innerHTML = originalHTML;
        btnBuscar.disabled = false;

        if (data.success) {
            // Mostrar información del cliente
            document.getElementById('cliente_id').value = data.user.id;
            document.getElementById('cliente-nombre').textContent = data.user.name;
            document.getElementById('cliente-email').textContent = data.user.email;
            document.getElementById('cliente-info').style.display = 'block';

            if (window.pedidosManager) {
                window.pedidosManager.showToast(data.message, 'success');
            }

            validarFormulario();
        } else {
            // Cliente no encontrado
            document.getElementById('cliente_id').value = '';
            document.getElementById('cliente-info').style.display = 'none';

            if (window.pedidosManager) {
                window.pedidosManager.showToast(data.message, 'error');
            }

            validarFormulario();
        }
    })
    .catch(error => {
        btnBuscar.innerHTML = originalHTML;
        btnBuscar.disabled = false;

        if (window.pedidosManager) {
            window.pedidosManager.showToast('Error al buscar el cliente', 'error');
        }
        console.error('Error:', error);
    });
});

// Permitir buscar con Enter
document.getElementById('cliente_cedula').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('btn-buscar-cliente').click();
    }
});

// Función para agregar producto
function agregarProducto() {
    const select = document.getElementById('producto_selector');
    const cantidad = parseInt(document.getElementById('cantidad_input').value);

    if (!select.value) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('Seleccione un producto', 'warning');
        }
        return;
    }

    if (cantidad < 1) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('La cantidad debe ser al menos 1', 'warning');
        }
        return;
    }

    const option = select.options[select.selectedIndex];
    const productoId = select.value;
    const nombre = option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);
    const stock = parseInt(option.dataset.stock);

    // Verificar stock
    if (cantidad > stock) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('Stock insuficiente. Disponible: ' + stock, 'error');
        }
        return;
    }

    // Verificar si ya existe
    const existe = productosAgregados.find(p => p.id === productoId);
    if (existe) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('Este producto ya fue agregado', 'warning');
        }
        return;
    }

    // Agregar producto
    productosAgregados.push({
        id: productoId,
        nombre: nombre,
        precio: precio,
        cantidad: cantidad,
        subtotal: precio * cantidad
    });

    renderizarProductos();
    calcularTotal();

    // Reset
    select.value = '';
    document.getElementById('cantidad_input').value = 1;

    if (window.pedidosManager) {
        window.pedidosManager.showToast('Producto agregado', 'success');
    }
}

// Función para renderizar productos
function renderizarProductos() {
    const container = document.getElementById('productos-container');
    const sinProductos = document.getElementById('sin-productos');
    
    // Validar que los elementos existan
    if (!container) {
        console.error('No se encontró el contenedor de productos');
        return;
    }

    if (productosAgregados.length === 0) {
        if (sinProductos) {
            sinProductos.style.display = 'block';
        }
        container.innerHTML = '';
        if (sinProductos) {
            container.appendChild(sinProductos);
        } else {
            // Crear mensaje si no existe
            const mensaje = document.createElement('div');
            mensaje.id = 'sin-productos';
            mensaje.className = 'sin-productos-mensaje';
            mensaje.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                    <p>No hay productos agregados al pedido</p>
                    <small>Busca y agrega productos usando el selector de arriba</small>
                </div>
            `;
            container.appendChild(mensaje);
        }
        validarFormulario();
        return;
    }

    if (sinProductos) {
        sinProductos.style.display = 'none';
    }
    container.innerHTML = '';

    productosAgregados.forEach((producto, index) => {
        const div = document.createElement('div');
        div.className = 'producto-item-enhanced';
        div.innerHTML = `
            <div style="display:flex;align-items:center;gap:1rem;">
                <div style="flex:1;">
                    <div class="producto-nombre">${producto.nombre}</div>
                    <div class="producto-detalles">
                        Cantidad: <strong>${producto.cantidad}</strong> × $${producto.precio.toLocaleString()}
                    </div>
                </div>
                <div class="producto-precio">
                    $${producto.subtotal.toLocaleString()}
                </div>
                <button type="button" class="btn-remove" onclick="eliminarProducto(${index})" title="Eliminar producto">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <input type="hidden" name="productos[${index}][id]" value="${producto.id}">
            <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
            <input type="hidden" name="productos[${index}][precio]" value="${producto.precio}">
        `;
        container.appendChild(div);
    });

    validarFormulario();
}

// Función para eliminar producto
function eliminarProducto(index) {
    productosAgregados.splice(index, 1);
    
    // Actualizar localStorage
    localStorage.setItem('pedido_carrito', JSON.stringify(productosAgregados));
    
    renderizarProductos();
    calcularTotal();

    if (window.pedidosManager) {
        window.pedidosManager.showToast('Producto eliminado', 'info');
    }
}

// Función para vaciar carrito
function vaciarCarrito() {
    if (productosAgregados.length === 0) {
        if (window.pedidosManager) {
            window.pedidosManager.showToast('El carrito ya está vacío', 'info');
        }
        return;
    }
    
    if (confirm('¿Estás seguro de vaciar el carrito? Se eliminarán todos los productos.')) {
        productosAgregados = [];
        localStorage.removeItem('pedido_carrito');
        
        // Actualizar vista
        renderizarProductos();
        calcularTotal();
        
        // Actualizar contador en show.blade.php si existe
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
        
        if (window.pedidosManager) {
            window.pedidosManager.showToast('Carrito vaciado correctamente', 'success');
        }
    }
}

// Limpiar carrito al enviar el formulario exitosamente
document.getElementById('pedidoForm').addEventListener('submit', function() {
    // El carrito se limpiará después de enviar el formulario
    setTimeout(() => {
        localStorage.removeItem('pedido_carrito');
    }, 500);
});

// Función para calcular total
function calcularTotal() {
    subtotal = productosAgregados.reduce((sum, p) => sum + p.subtotal, 0);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const total = subtotal - descuento;

    document.getElementById('cantidad-productos').textContent = productosAgregados.length;
    document.getElementById('subtotal-display').textContent = '$' + subtotal.toLocaleString();
    document.getElementById('descuento-display').textContent = '-$' + descuento.toLocaleString();
    document.getElementById('total-final').textContent = '$' + total.toLocaleString();

    // Actualizar campos ocultos
    document.getElementById('subtotal-hidden').value = subtotal;
    document.getElementById('total-hidden').value = total;
}

// Validar formulario
function validarFormulario() {
    const clienteSeleccionado = document.getElementById('cliente_id').value;
    const hayProductos = productosAgregados.length > 0;
    const btnCrear = document.getElementById('btn-crear');

    if (clienteSeleccionado && hayProductos) {
        btnCrear.disabled = false;
    } else {
        btnCrear.disabled = true;
    }
}

// Event listener para descuento
document.getElementById('descuento').addEventListener('input', calcularTotal);

// Permitir agregar producto con Enter
document.getElementById('cantidad_input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        agregarProducto();
    }
});
</script>

{{-- Mostrar mensajes flash --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("success") }}', 'success');
    }
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("error") }}', 'error');
    }
});
</script>
@endif

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ $errors->first() }}', 'error');
    }
});
</script>
@endif
@endpush
