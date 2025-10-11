/**
 * Messages Management
 * Manejo de mensajes de alerta y modales de confirmación
 */

class MessagesManager {
    constructor() {
        this.initAutoHideAlerts();
    }

    /**
     * Auto-ocultar alertas después de 5 segundos
     */
    initAutoHideAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach((alert) => {
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    }
}

/**
 * Función global para eliminar productos (llamada desde los botones)
 * @param {string} productId - ID del producto
 */
function confirmDelete(productId) {
    // Buscar información del producto en la página
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    let productName = 'el producto';
    let productImage = '';
    let productCategory = '';

    if (productCard) {
        const nameElement = productCard.querySelector('.producto-nombre, .producto-titulo, h6');
        const imageElement = productCard.querySelector('img');
        const categoryElement = productCard.querySelector('.categoria-badge, .producto-categoria');

        productName = nameElement ? nameElement.textContent.trim() : 'el producto';
        productImage = imageElement ? imageElement.src : '';
        productCategory = categoryElement ? categoryElement.textContent.trim() : '';
    }

    // Actualizar información del producto en el modal
    document.getElementById('productName').textContent = productName;
    document.getElementById('productCategory').textContent = productCategory;

    if (productImage) {
        document.getElementById('productImage').src = productImage;
        document.getElementById('productInfo').style.display = 'block';
    } else {
        document.getElementById('productInfo').style.display = 'none';
    }

    // Actualizar action del formulario
    document.getElementById('deleteForm').action = `/admin/productos/${productId}`;

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

/**
 * Función global para cambiar estado (llamada desde los botones)
 * @param {string} productId - ID del producto
 */
function toggleStatus(productId) {
    // Buscar el botón para determinar el estado actual
    const toggleButton = document.querySelector(`button[onclick="toggleStatus('${productId}')"]`);
    const currentStatus = toggleButton ? toggleButton.classList.contains('btn-outline-warning') : false;

    const action = currentStatus ? 'desactivar' : 'activar';
    const message = currentStatus
        ? 'El producto se ocultará del catálogo y no estará disponible para venta.'
        : 'El producto será visible en el catálogo y estará disponible para venta.';

    // Actualizar contenido del modal
    document.getElementById('statusMessage').textContent = message;
    document.getElementById('statusForm').action = `/admin/productos/${productId}/toggle-status`;

    // Cambiar el texto del botón según la acción
    const confirmButton = document.querySelector('#statusForm button[type="submit"]');
    confirmButton.innerHTML = `<i class="bi bi-toggle-on me-1"></i>${action.charAt(0).toUpperCase() + action.slice(1)} Producto`;

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('statusConfirmModal'));
    modal.show();
}

// Inicialización automática
document.addEventListener('DOMContentLoaded', function() {
    window.messagesManager = new MessagesManager();
});
