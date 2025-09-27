/**
 * Funciones JavaScript para el panel de administracion
 * Sistema simplificado y directo
 */

// Funciones globales simplificadas
window.toggleStatus = function(itemId) {
    if (confirm('Esta seguro de que quiere cambiar el estado de este elemento?')) {
        const form = document.getElementById('toggle-form-' + itemId);
        if (form) {
            form.submit();
        } else {
            alert('Error: No se encontro el formulario. Recargue la pagina.');
        }
    }
};

window.confirmDelete = function(itemId) {
    if (confirm('Esta seguro de que quiere eliminar este elemento? Esta accion no se puede deshacer.')) {
        const form = document.getElementById('delete-form-' + itemId);
        if (form) {
            form.submit();
        } else {
            alert('Error: No se encontro el formulario. Recargue la pagina.');
        }
    }
};

// Funciones especificas para diferentes modulos
window.toggleActiveUser = function(userId) {
    if (confirm('Esta seguro de que quiere cambiar el estado de este usuario?')) {
        const form = document.getElementById('toggle-active-form-' + userId);
        if (form) {
            form.submit();
        } else {
            alert('Error: No se encontro el formulario. Recargue la pagina.');
        }
    }
};

window.confirmDeleteUser = function(userId) {
    if (confirm('Esta seguro de que quiere eliminar este usuario? Esta accion no se puede deshacer.')) {
        const form = document.getElementById('delete-form-' + userId);
        if (form) {
            form.submit();
        } else {
            alert('Error: No se encontro el formulario. Recargue la pagina.');
        }
    }
};

window.updatePedidoStatus = function(pedidoId) {
    if (confirm('Esta seguro de que quiere cambiar el estado de este pedido?')) {
        const form = document.getElementById('status-form-' + pedidoId);
        if (form) {
            form.submit();
        } else {
            alert('Error: No se encontro el formulario. Recargue la pagina.');
        }
    }
};

// confirmDeletePedido function removed - now handled by pedidos-modals.js

// Inicializar cuando el DOM este listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin functions cargadas correctamente');

    // Verificar que las funciones esten disponibles
    if (typeof window.toggleStatus === 'function') {
        console.log('toggleStatus disponible');
    }

    if (typeof window.confirmDelete === 'function') {
        console.log('confirmDelete disponible');
    }
});