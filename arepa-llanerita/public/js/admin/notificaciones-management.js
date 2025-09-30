/**
 * Módulo para gestión de notificaciones
 * Separado para mejor organización del código
 */

/**
 * Función para mostrar modal de confirmación
 */
function showConfirmModal(title, message, actionCallback) {
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalMessage').textContent = message;
    document.getElementById('confirmModalAction').onclick = actionCallback;
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

/**
 * Función para mostrar modal de resultado
 */
function showResultModal(title, message, isSuccess = true) {
    const header = document.getElementById('resultModalHeader');
    header.className = isSuccess ? 'modal-header bg-success text-white' : 'modal-header bg-danger text-white';
    document.getElementById('resultModalTitle').textContent = title;
    document.getElementById('resultModalMessage').textContent = message;

    const modal = new bootstrap.Modal(document.getElementById('resultModal'));
    modal.show();

    if (isSuccess) {
        setTimeout(() => {
            modal.hide();
            location.reload();
        }, 2000);
    }
}

/**
 * Marcar una notificación como leída
 */
function marcarLeida(id) {
    const marcarLeidaRoute = window.notificacionesRoutes ?
        window.notificacionesRoutes.marcarLeida.replace(':id', id) : '#';
    const csrfToken = window.notificacionesCSRF || '';

    fetch(marcarLeidaRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResultModal('Éxito', 'Notificación marcada como leída');
        } else {
            showResultModal('Error', data.message || 'Error al marcar como leída', false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResultModal('Error', 'Error de conexión', false);
    });
}

/**
 * Marcar todas las notificaciones como leídas
 */
function marcarTodasLeidas() {
    showConfirmModal(
        'Marcar Todas como Leídas',
        '¿Estás seguro de marcar todas las notificaciones como leídas?',
        function() {
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();

            const marcarTodasRoute = window.notificacionesRoutes ?
                window.notificacionesRoutes.marcarTodasLeidas : '#';
            const csrfToken = window.notificacionesCSRF || '';

            fetch(marcarTodasRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResultModal('Éxito', 'Todas las notificaciones marcadas como leídas');
                } else {
                    showResultModal('Error', data.message || 'Error al marcar como leídas', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showResultModal('Error', 'Error de conexión', false);
            });
        }
    );
}

/**
 * Eliminar una notificación
 */
function eliminarNotificacion(id) {
    showConfirmModal(
        'Eliminar Notificación',
        '¿Estás seguro de eliminar esta notificación?',
        function() {
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();

            const eliminarRoute = window.notificacionesRoutes ?
                window.notificacionesRoutes.eliminar.replace(':id', id) : '#';
            const csrfToken = window.notificacionesCSRF || '';

            fetch(eliminarRoute, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResultModal('Éxito', 'Notificación eliminada correctamente');
                } else {
                    showResultModal('Error', data.message || 'Error al eliminar notificación', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showResultModal('Error', 'Error de conexión', false);
            });
        }
    );
}

/**
 * Limpiar notificaciones leídas
 */
function limpiarLeidas() {
    showConfirmModal(
        'Limpiar Notificaciones Leídas',
        '¿Estás seguro de eliminar todas las notificaciones leídas?',
        function() {
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();

            const limpiarRoute = window.notificacionesRoutes ?
                window.notificacionesRoutes.limpiarLeidas : '#';
            const csrfToken = window.notificacionesCSRF || '';

            fetch(limpiarRoute, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResultModal('Éxito', 'Notificaciones leídas eliminadas correctamente');
                } else {
                    showResultModal('Error', data.message || 'Error al limpiar notificaciones', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showResultModal('Error', 'Error de conexión', false);
            });
        }
    );
}

/**
 * Crear notificaciones de prueba
 */
function crearNotificacionesPrueba() {
    showConfirmModal(
        'Crear Notificaciones de Prueba',
        '¿Deseas crear algunas notificaciones de prueba para probar el sistema?',
        function() {
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();

            const crearPruebasRoute = window.notificacionesRoutes ?
                window.notificacionesRoutes.crearPruebas : '#';
            const csrfToken = window.notificacionesCSRF || '';

            fetch(crearPruebasRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResultModal('Éxito', 'Notificaciones de prueba creadas correctamente');
                } else {
                    showResultModal('Error', data.message || 'Error al crear notificaciones', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showResultModal('Error', 'Error de conexión', false);
            });
        }
    );
}

/**
 * Inicializar el módulo de notificaciones
 */
function initializeNotificacionesModule() {
    console.log('✅ Módulo de gestión de notificaciones inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeNotificacionesModule);

// Exponer funciones globalmente
window.marcarLeida = marcarLeida;
window.marcarTodasLeidas = marcarTodasLeidas;
window.eliminarNotificacion = eliminarNotificacion;
window.limpiarLeidas = limpiarLeidas;
window.crearNotificacionesPrueba = crearNotificacionesPrueba;