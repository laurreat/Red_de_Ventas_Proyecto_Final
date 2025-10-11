/**
 * Módulo para gestión de logs
 * Separado para mejor organización del código
 */

/**
 * Función para forzar modal por encima de todo
 */
function forceModalOnTop(modalElement) {
    if (modalElement && modalElement.classList.contains('show')) {
        modalElement.style.position = 'fixed';
        modalElement.style.top = '0';
        modalElement.style.left = '0';
        modalElement.style.width = '100%';
        modalElement.style.height = '100%';
        modalElement.style.zIndex = '999999';
        modalElement.style.background = 'rgba(0, 0, 0, 0.5)';
        modalElement.style.display = 'flex';
        modalElement.style.alignItems = 'center';
        modalElement.style.justifyContent = 'center';

        // Configurar diálogo
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.position = 'relative';
            modalDialog.style.zIndex = '1000000';
            modalDialog.style.margin = '0';
        }

        // Ocultar cualquier backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }
}

/**
 * Funciones para mostrar modales
 */
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    setTimeout(() => forceModalOnTop(document.getElementById('successModal')), 10);
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
    setTimeout(() => forceModalOnTop(document.getElementById('errorModal')), 10);
}

/**
 * Limpiar log principal - función de confirmación
 */
function confirmarLimpiarLogs() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmClearModal'));
    modal.hide();

    const clearRoute = window.logsRoutes ? window.logsRoutes.clear : '#';
    const csrfToken = window.logsCSRF || '';

    fetch(clearRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Logs limpiados exitosamente');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showErrorModal(data.message || 'Error al limpiar logs');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error de conexión: ' + error.message);
    });
}

/**
 * Mostrar mensaje completo
 */
function mostrarMensajeCompleto(mensaje) {
    document.getElementById('messageContent').textContent = mensaje;
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}

/**
 * Obtener estadísticas actualizadas
 */
function obtenerEstadisticas() {
    const statsRoute = window.logsRoutes ? window.logsRoutes.stats : '#';

    fetch(statsRoute)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar las estadísticas en la interfaz
                updateStatsDisplay(data.stats);
            }
        })
        .catch(error => {
            console.error('Error al obtener estadísticas:', error);
        });
}

/**
 * Actualizar display de estadísticas
 */
function updateStatsDisplay(stats) {
    // Actualizar contadores si existen en la página
    const totalLogsElement = document.getElementById('total-logs');
    const errorLogsElement = document.getElementById('error-logs');
    const warningLogsElement = document.getElementById('warning-logs');

    if (totalLogsElement && stats.total !== undefined) {
        totalLogsElement.textContent = stats.total;
    }
    if (errorLogsElement && stats.errors !== undefined) {
        errorLogsElement.textContent = stats.errors;
    }
    if (warningLogsElement && stats.warnings !== undefined) {
        warningLogsElement.textContent = stats.warnings;
    }
}

/**
 * Configurar formulario de limpieza
 */
function setupCleanupForm() {
    const cleanupForm = document.getElementById('cleanupForm');
    if (cleanupForm) {
        cleanupForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const cleanupRoute = window.logsRoutes ? window.logsRoutes.cleanup : '#';
            const csrfToken = window.logsCSRF || '';

            fetch(cleanupRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).hide();

                if (data.success) {
                    showSuccessModal(data.message || 'Limpieza completada exitosamente');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showErrorModal(data.message || 'Error durante la limpieza');
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).hide();
                showErrorModal('Error de conexión: ' + error.message);
            });
        });
    }
}

/**
 * Configurar formulario de exportación
 */
function setupExportForm() {
    const exportForm = document.getElementById('exportForm');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const exportRoute = window.logsRoutes ? window.logsRoutes.export : '#';
            const csrfToken = window.logsCSRF || '';

            // Crear un enlace temporal para descargar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = exportRoute;
            form.target = '_blank';

            // Agregar token CSRF
            const csrfTokenInput = document.createElement('input');
            csrfTokenInput.type = 'hidden';
            csrfTokenInput.name = '_token';
            csrfTokenInput.value = csrfToken;
            form.appendChild(csrfTokenInput);

            // Agregar datos del formulario
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        });
    }
}

/**
 * Configurar auto-refresh si estamos viendo logs del día actual
 */
function setupAutoRefresh() {
    const isCurrentDate = window.logsCurrentDate === window.logsToday;

    if (isCurrentDate) {
        setInterval(function() {
            // Solo auto-refresh si no hay modales abiertos
            if (!document.querySelector('.modal.show')) {
                obtenerEstadisticas();
            }
        }, 30000); // Cada 30 segundos
    }
}

/**
 * Inicializar el módulo de logs
 */
function initializeLogsModule() {
    // Configurar formularios
    setupCleanupForm();
    setupExportForm();

    // Configurar auto-refresh
    setupAutoRefresh();

    // Interceptar todos los modales para forzarlos por encima
    document.addEventListener('shown.bs.modal', function (event) {
        forceModalOnTop(event.target);
    });

    // También interceptar cuando se muestran para forzar inmediatamente
    document.addEventListener('show.bs.modal', function (event) {
        setTimeout(() => forceModalOnTop(event.target), 10);
    });

    console.log('✅ Módulo de gestión de logs inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeLogsModule);

// Exponer funciones globalmente
window.confirmarLimpiarLogs = confirmarLimpiarLogs;
window.mostrarMensajeCompleto = mostrarMensajeCompleto;
window.obtenerEstadisticas = obtenerEstadisticas;
window.showSuccessModal = showSuccessModal;
window.showErrorModal = showErrorModal;