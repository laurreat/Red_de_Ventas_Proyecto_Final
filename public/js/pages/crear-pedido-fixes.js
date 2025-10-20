/**
 * FIXES PARA CREAR PEDIDO
 * Agregar este código en resources/views/cliente/pedidos/create.blade.php
 * en la sección @push('scripts')
 */

// 1. Función updateCart - Actualiza el carrito desde el formulario
function updateCart() {
    const checkboxes = document.querySelectorAll('.producto-checkbox input[type="checkbox"]:checked');
    let total = 0;
    let items = [];
    
    checkboxes.forEach(checkbox => {
        const productCard = checkbox.closest('.pedidos-product-checkbox') || checkbox.closest('.producto-item');
        if (!productCard) return;
        
        const productoId = checkbox.value;
        const nombre = productCard.querySelector('.pedidos-product-name')?.textContent.trim() || '';
        const precioText = productCard.querySelector('.pedidos-product-price')?.textContent.replace(/[^0-9]/g, '') || '0';
        const precio = parseFloat(precioText);
        const cantidadInput = productCard.querySelector('.quantity-input') || productCard.querySelector('input[name="cantidad"]');
        const cantidad = parseInt(cantidadInput?.value || 1);
        
        if (precio && cantidad > 0) {
            const subtotal = precio * cantidad;
            total += subtotal;
            
            items.push({
                producto_id: productoId,
                nombre: nombre,
                precio: precio,
                cantidad: cantidad,
                subtotal: subtotal
            });
        }
    });
    
    // Actualizar UI del total
    const totalElement = document.getElementById('totalPedido') || document.querySelector('.total-value');
    if (totalElement) {
        totalElement.textContent = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(total);
    }
    
    // Actualizar contador de items
    const itemsCountElement = document.getElementById('itemsCount') || document.querySelector('.items-count');
    if (itemsCountElement) {
        itemsCountElement.textContent = items.length;
    }
    
    // Guardar en variable global si existe
    if (typeof window.carritoItems !== 'undefined') {
        window.carritoItems = items;
    }
    
    console.log('Carrito actualizado:', items, 'Total:', total);
    
    return { items, total };
}

// 2. Función cargarDesdeCarrito - Carga el carrito guardado en sessionStorage
function cargarDesdeCarrito() {
    try {
        const carritoTemp = sessionStorage.getItem('carritoTemp');
        
        if (!carritoTemp) {
            if (typeof showWarningToast !== 'undefined') {
                showWarningToast('No hay carrito guardado');
            } else {
                alert('No hay carrito guardado');
            }
            return false;
        }
        
        const items = JSON.parse(carritoTemp);
        
        if (!Array.isArray(items) || items.length === 0) {
            if (typeof showWarningToast !== 'undefined') {
                showWarningToast('El carrito está vacío');
            }
            return false;
        }
        
        // Desmarcar todos los checkboxes primero
        document.querySelectorAll('.producto-checkbox input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });
        
        // Marcar productos del carrito
        let itemsCargados = 0;
        items.forEach(item => {
            const checkbox = document.querySelector(`input[type="checkbox"][value="${item.producto_id}"]`);
            if (checkbox) {
                checkbox.checked = true;
                
                // Buscar el input de cantidad
                const productCard = checkbox.closest('.pedidos-product-checkbox') || checkbox.closest('.producto-item');
                if (productCard) {
                    const cantidadInput = productCard.querySelector('.quantity-input') || productCard.querySelector('input[name="cantidad"]');
                    if (cantidadInput) {
                        cantidadInput.value = item.cantidad || 1;
                    }
                    
                    // Marcar visualmente como seleccionado
                    productCard.classList.add('selected');
                }
                
                itemsCargados++;
            }
        });
        
        // Actualizar el carrito
        updateCart();
        
        if (typeof showSuccessToast !== 'undefined') {
            showSuccessToast(`Se cargaron ${itemsCargados} productos al pedido`);
        } else {
            alert(`Se cargaron ${itemsCargados} productos al pedido`);
        }
        
        // Limpiar sessionStorage después de cargar
        sessionStorage.removeItem('carritoTemp');
        
        return true;
    } catch (error) {
        console.error('Error al cargar carrito:', error);
        if (typeof showErrorToast !== 'undefined') {
            showErrorToast('Error al cargar el carrito guardado');
        }
        return false;
    }
}

// 3. Event Listeners para checkboxes y cantidades
document.addEventListener('DOMContentLoaded', function() {
    // Listener para checkboxes de productos
    document.querySelectorAll('.producto-checkbox input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const productCard = this.closest('.pedidos-product-checkbox') || this.closest('.producto-item');
            if (productCard) {
                if (this.checked) {
                    productCard.classList.add('selected');
                } else {
                    productCard.classList.remove('selected');
                }
            }
            updateCart();
        });
    });
    
    // Listener para inputs de cantidad
    document.querySelectorAll('.quantity-input, input[name="cantidad"]').forEach(input => {
        input.addEventListener('change', function() {
            // Validar que la cantidad sea mayor a 0
            if (this.value < 1) {
                this.value = 1;
            }
            updateCart();
        });
        
        input.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
            updateCart();
        });
    });
    
    // Si hay botón de cargar desde carrito, agregar listener
    const btnCargarCarrito = document.getElementById('btnCargarCarrito') || document.querySelector('[onclick*="cargarDesdeCarrito"]');
    if (btnCargarCarrito && !btnCargarCarrito.onclick) {
        btnCargarCarrito.addEventListener('click', function(e) {
            e.preventDefault();
            cargarDesdeCarrito();
        });
    }
    
    // Intentar cargar carrito automáticamente si existe
    setTimeout(() => {
        const carritoTemp = sessionStorage.getItem('carritoTemp');
        if (carritoTemp) {
            const confirmacion = confirm('Tienes productos guardados en el carrito. ¿Deseas cargarlos?');
            if (confirmacion) {
                cargarDesdeCarrito();
            } else {
                sessionStorage.removeItem('carritoTemp');
            }
        }
    }, 500);
});

// 4. Función para validar el formulario antes de enviar
function validarFormularioPedido() {
    const checkboxes = document.querySelectorAll('.producto-checkbox input[type="checkbox"]:checked');
    
    if (checkboxes.length === 0) {
        if (typeof showWarningToast !== 'undefined') {
            showWarningToast('Debe seleccionar al menos un producto');
        } else {
            alert('Debe seleccionar al menos un producto');
        }
        return false;
    }
    
    // Validar cantidades
    let valido = true;
    checkboxes.forEach(checkbox => {
        const productCard = checkbox.closest('.pedidos-product-checkbox') || checkbox.closest('.producto-item');
        const cantidadInput = productCard?.querySelector('.quantity-input') || productCard?.querySelector('input[name="cantidad"]');
        const cantidad = parseInt(cantidadInput?.value || 0);
        
        if (cantidad < 1) {
            valido = false;
            if (typeof showErrorToast !== 'undefined') {
                showErrorToast('Todas las cantidades deben ser mayores a 0');
            } else {
                alert('Todas las cantidades deben ser mayores a 0');
            }
            return false;
        }
    });
    
    return valido;
}

// 5. Exportar funciones para uso global
window.updateCart = updateCart;
window.cargarDesdeCarrito = cargarDesdeCarrito;
window.validarFormularioPedido = validarFormularioPedido;
