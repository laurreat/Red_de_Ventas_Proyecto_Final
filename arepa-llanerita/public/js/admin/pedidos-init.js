/**
 * Módulo de inicialización para la vista de pedidos
 * Separado para mejor organización del código
 */

/**
 * Función para inicializar los modales profesionales de pedidos
 */
function initializePedidosModals() {
    console.log('🚀 Pedidos Index con modales profesionales cargado...');

    // Test completo de modales y elementos
    setTimeout(function() {
        console.log('🔍 Test completo de modales:');
        console.log('- Bootstrap disponible:', typeof bootstrap !== 'undefined');
        console.log('- Bootstrap.Modal disponible:', typeof bootstrap?.Modal !== 'undefined');
        console.log('- confirmDeletePedido disponible:', typeof confirmDeletePedido !== 'undefined');
        console.log('- showStatusSelector disponible:', typeof showStatusSelector !== 'undefined');

        // Verificar elementos HTML de modales
        const deleteModal = document.getElementById('deletePedidoConfirmModal');
        const statusModal = document.getElementById('statusPedidoConfirmModal');
        const saveModal = document.getElementById('savePedidoConfirmModal');
        const statusSelectorModal = document.getElementById('statusSelectorPedidoModal');

        console.log('- HTML deleteModal encontrado:', deleteModal !== null);
        console.log('- HTML statusModal encontrado:', statusModal !== null);
        console.log('- HTML saveModal encontrado:', saveModal !== null);
        console.log('- HTML statusSelectorModal encontrado:', statusSelectorModal !== null);

        if (typeof confirmDeletePedido !== 'undefined' && typeof showStatusSelector !== 'undefined') {
            console.log('✅ Funciones de modales disponibles');
        } else {
            console.error('❌ Funciones de modales no encontradas');
        }
    }, 500);
}

/**
 * Función para mostrar mensajes flash usando AdminAlerts
 */
function showFlashMessages() {
    const successMessage = window.pedidosFlashMessages?.success;
    const errorMessage = window.pedidosFlashMessages?.error;

    if (successMessage && window.adminAlerts) {
        window.adminAlerts.showSuccess('¡Éxito!', successMessage);
    }

    if (errorMessage && window.adminAlerts) {
        window.adminAlerts.showError('Error', errorMessage);
    }
}

/**
 * Inicializar el módulo de pedidos
 */
function initializePedidosModule() {
    initializePedidosModals();
    showFlashMessages();
    console.log('✅ Módulo de inicialización de pedidos cargado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializePedidosModule);