// Variables globales
let productosSeleccionados = [];
let contadorProductos = 0;

// URLs para las búsquedas AJAX - se deben pasar desde el blade
const searchUrls = window.searchUrls || {};

// Funciones de búsqueda por cédula
async function buscarCliente() {
    const cedula = document.getElementById('cliente_cedula').value.trim();
    const btnBuscar = document.getElementById('btn-buscar-cliente');
    const clienteInfo = document.getElementById('cliente-info');

    if (!cedula) {
        mostrarToast('Por favor ingrese una cédula', 'warning');
        return;
    }

    // Mostrar loading
    btnBuscar.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btnBuscar.disabled = true;

    try {
        const response = await fetch(searchUrls.cliente, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ cedula: cedula })
        });

        const data = await response.json();

        if (data.success) {
            // Cliente encontrado
            document.getElementById('cliente_id').value = data.user.id;
            document.getElementById('cliente-nombre').textContent = data.user.name;
            document.getElementById('cliente-email').textContent = data.user.email;
            clienteInfo.style.display = 'block';
            mostrarToast('Cliente encontrado correctamente', 'success');
        } else {
            // Cliente no encontrado
            document.getElementById('cliente_id').value = '';
            clienteInfo.style.display = 'none';
            mostrarToast(data.message || 'Cliente no encontrado', 'error');
        }
    } catch (error) {
        console.error('Error al buscar cliente:', error);
        mostrarToast('Error al buscar cliente', 'error');
    } finally {
        btnBuscar.innerHTML = '<i class="bi bi-search"></i>';
        btnBuscar.disabled = false;
    }
}

async function buscarVendedor() {
    const cedula = document.getElementById('vendedor_cedula').value.trim();
    const btnBuscar = document.getElementById('btn-buscar-vendedor');
    const btnLimpiar = document.getElementById('btn-limpiar-vendedor');
    const vendedorInfo = document.getElementById('vendedor-info');

    if (!cedula) {
        mostrarToast('Por favor ingrese una cédula', 'warning');
        return;
    }

    // Mostrar loading
    btnBuscar.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btnBuscar.disabled = true;

    try {
        const response = await fetch(searchUrls.vendedor, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ cedula: cedula })
        });

        const data = await response.json();

        if (data.success) {
            // Vendedor encontrado
            document.getElementById('vendedor_id').value = data.user.id;
            document.getElementById('vendedor-nombre').textContent = data.user.name;
            document.getElementById('vendedor-email').textContent = data.user.email;
            vendedorInfo.style.display = 'block';
            btnLimpiar.style.display = 'inline-block';
            mostrarToast('Vendedor encontrado correctamente', 'success');
        } else {
            // Vendedor no encontrado
            limpiarVendedor();
            mostrarToast(data.message || 'Vendedor no encontrado', 'error');
        }
    } catch (error) {
        console.error('Error al buscar vendedor:', error);
        mostrarToast('Error al buscar vendedor', 'error');
    } finally {
        btnBuscar.innerHTML = '<i class="bi bi-search"></i>';
        btnBuscar.disabled = false;
    }
}

function limpiarVendedor() {
    document.getElementById('vendedor_cedula').value = '';
    document.getElementById('vendedor_id').value = '';
    document.getElementById('vendedor-info').style.display = 'none';
    document.getElementById('btn-limpiar-vendedor').style.display = 'none';
}

// Función para mostrar toasts
function mostrarToast(mensaje, tipo = 'info') {
    const toastId = 'toast_' + Date.now();
    const colorClass = {
        'success': 'text-bg-success',
        'error': 'text-bg-danger',
        'warning': 'text-bg-warning',
        'info': 'text-bg-info'
    }[tipo] || 'text-bg-info';

    const toastHtml = `
        <div class="toast ${colorClass}" role="alert" id="${toastId}" style="z-index: 9999;">
            <div class="toast-header">
                <i class="bi bi-info-circle me-2"></i>
                <strong class="me-auto">Pedidos</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${mensaje}</div>
        </div>
    `;

    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Event listeners para búsquedas
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda de cliente
    document.getElementById('btn-buscar-cliente').addEventListener('click', buscarCliente);
    document.getElementById('cliente_cedula').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarCliente();
        }
    });

    // Búsqueda de vendedor
    document.getElementById('btn-buscar-vendedor').addEventListener('click', buscarVendedor);
    document.getElementById('vendedor_cedula').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarVendedor();
        }
    });

    // Limpiar vendedor
    document.getElementById('btn-limpiar-vendedor').addEventListener('click', limpiarVendedor);

    // Inicializar cálculos
    calcularTotal();
});

// === FUNCIONES EXISTENTES DE PRODUCTOS ===

function agregarProducto() {
    const selector = document.getElementById('producto_selector');
    const cantidadInput = document.getElementById('cantidad_input');
    const cantidad = parseInt(cantidadInput.value);

    if (!selector.value || cantidad <= 0) {
        mostrarToast('Selecciona un producto y una cantidad válida', 'warning');
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
        mostrarToast('La cantidad no puede ser mayor al stock disponible (' + producto.stock + ')', 'error');
        return;
    }

    // Verificar si el producto ya está agregado
    const existeIndex = productosSeleccionados.findIndex(p => p.id === producto.id);
    if (existeIndex !== -1) {
        productosSeleccionados[existeIndex].cantidad += cantidad;
        if (productosSeleccionados[existeIndex].cantidad > producto.stock) {
            mostrarToast('La cantidad total no puede ser mayor al stock disponible (' + producto.stock + ')', 'error');
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
        mostrarToast('La cantidad no puede ser mayor al stock disponible (' + productosSeleccionados[index].stock + ')', 'error');
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
    btnCrear.disabled = productosSeleccionados.length === 0 || !document.getElementById('cliente_id').value;
}
