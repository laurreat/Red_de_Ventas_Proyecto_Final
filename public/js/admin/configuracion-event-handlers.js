/**
 * Event handlers para configuración del sistema
 * Manejadores de eventos para botones de confirmación y AJAX
 */

/**
 * Inicializar todos los event handlers cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeBackupHandlers();
    initializeCacheHandlers();
    initializeLogsHandlers();

    // Configurar manejadores de cierre de modales
    if (window.setupModalCloseHandlers) {
        window.setupModalCloseHandlers();
    }
});

/**
 * Inicializar manejadores para backup
 */
function initializeBackupHandlers() {
    const confirmBackupBtn = document.getElementById('confirmBackupBtn');
    console.log('confirmBackupBtn encontrado:', confirmBackupBtn);

    if (confirmBackupBtn) {
        confirmBackupBtn.addEventListener('click', function() {
            // Cerrar modal de confirmación
            closeConfirmModal('backupConfirmModal');

            // Mostrar indicador de carga en el botón
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Creando backup...';
            button.disabled = true;

            const backupRoute = window.configuracionRoutes ? window.configuracionRoutes.backup : '#';
            const csrfToken = window.configuracionCSRF || '';

            fetch(backupRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if (window.showResultModal) {
                        window.showResultModal(
                            'success',
                            'Backup Creado Exitosamente',
                            'El backup del sistema se ha creado correctamente.',
                            [
                                `Archivo: ${data.filename || 'backup.zip'}`,
                                `Tamaño: ${data.size || 'N/A'}`,
                                `Colecciones: ${data.collections || 'N/A'}`,
                                `Ubicación: ${data.path || 'storage/backups'}`
                            ]
                        );
                    }
                } else {
                    if (window.showResultModal) {
                        window.showResultModal('error', 'Error al Crear Backup', data.message);
                    }
                }
            })
            .catch(error => {
                if (window.showResultModal) {
                    window.showResultModal('error', 'Error al Crear Backup', error.message);
                }
            })
            .finally(() => {
                // Restaurar botón
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    } else {
        console.error('confirmBackupBtn no encontrado');
    }
}

/**
 * Inicializar manejadores para cache
 */
function initializeCacheHandlers() {
    const confirmCacheBtn = document.getElementById('confirmCacheBtn');

    if (confirmCacheBtn) {
        confirmCacheBtn.addEventListener('click', function() {
            // Cerrar modal de confirmación
            closeConfirmModal('cacheConfirmModal');

            // Mostrar indicador de carga
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Limpiando...';
            button.disabled = true;

            const cacheRoute = window.configuracionRoutes ? window.configuracionRoutes.limpiarCache : '#';
            const csrfToken = window.configuracionCSRF || '';

            fetch(cacheRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if (window.showResultModal) {
                        window.showResultModal(
                            'success',
                            'Cache Limpiado Exitosamente',
                            'El cache del sistema se ha limpiado correctamente. La página se recargará automáticamente.',
                            (data.cleared || []).map(c => c)
                        );
                    }

                    // Recargar después de mostrar el modal
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    if (window.showResultModal) {
                        window.showResultModal('error', 'Error al Limpiar Cache', data.message);
                    }
                }
            })
            .catch(error => {
                if (window.showResultModal) {
                    window.showResultModal('error', 'Error al Limpiar Cache', error.message);
                }
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    }
}

/**
 * Inicializar manejadores para logs
 */
function initializeLogsHandlers() {
    const confirmLogsBtn = document.getElementById('confirmLogsBtn');

    if (confirmLogsBtn) {
        confirmLogsBtn.addEventListener('click', function() {
            // Cerrar modal de confirmación
            closeConfirmModal('logsConfirmModal');

            // Mostrar indicador de carga
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Limpiando logs...';
            button.disabled = true;

            const logsRoute = window.configuracionRoutes ? window.configuracionRoutes.limpiarLogs : '#';
            const csrfToken = window.configuracionCSRF || '';

            fetch(logsRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if (window.showResultModal) {
                        window.showResultModal(
                            'success',
                            'Logs Limpiados Exitosamente',
                            data.message
                        );
                    }
                } else {
                    if (window.showResultModal) {
                        window.showResultModal('error', 'Error al Limpiar Logs', data.message);
                    }
                }
            })
            .catch(error => {
                if (window.showResultModal) {
                    window.showResultModal('error', 'Error al Limpiar Logs', error.message);
                }
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    }
}

/**
 * Función helper para cerrar modales de confirmación
 */
function closeConfirmModal(modalId) {
    if (typeof $ !== 'undefined') {
        $('#' + modalId).modal('hide');
    } else {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.style.backgroundColor = '';
            modal.style.alignItems = '';
            modal.style.justifyContent = '';
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');

            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '';
                modalDialog.style.zIndex = '';
                modalDialog.style.position = '';
            }
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    }
}