/**
 * Módulo para gestión de respaldos
 * Separado para mejor organización del código
 */

// Variables globales
let backupFilenameToDelete = '';
let backupFilenameToRestore = '';
let notificationCounter = 0;

/**
 * Funciones para modales profesionales
 */
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    ocultarTodosLosModales();
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
    }, 300);

    // También mostrar notificación persistente
    showPersistentNotification(message, 'success');
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    ocultarTodosLosModales();
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
    }, 300);

    // También mostrar notificación persistente
    showPersistentNotification(message, 'error');
}

/**
 * Funciones simples para modales
 */
function ocultarTodosLosModales() {
    console.log('Ocultando todos los modales...');
    const modales = document.querySelectorAll('.modal');
    modales.forEach(function(modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    });

    // Remover todas las capas de fondo oscuro (backdrop)
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });

    // Restaurar scroll del body
    document.body.style.overflow = '';
    document.body.classList.remove('modal-open');
}

function mostrarModal(modalId) {
    console.log('Mostrando modal:', modalId);

    // Primero ocultar todos los modales y limpiar cualquier resto
    ocultarTodosLosModales();

    // Esperar un momento para que se complete la limpieza
    setTimeout(function() {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Configurar el modal
            modal.removeAttribute('aria-hidden');
            modal.style.display = 'block';
            modal.classList.add('show');

            // Crear backdrop limpio
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '1050';
            document.body.appendChild(backdrop);

            // Configurar body para modal
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            console.log('✅ Modal ' + modalId + ' mostrado correctamente');

            // Enfocar el primer elemento interactivo del modal
            setTimeout(function() {
                const firstInput = modal.querySelector('input, select, button');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 300);
        }
    }, 100);
}

function ocultarModal(modalId) {
    console.log('Ocultando modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        // Remover backdrop asociado
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });

        // Restaurar body
        document.body.style.overflow = '';
        document.body.classList.remove('modal-open');

        // Limpiar formularios
        const forms = modal.querySelectorAll('form');
        forms.forEach(function(form) {
            form.reset();
        });
    }
}

/**
 * Función para restaurar backup con modal profesional
 */
function restaurarBackup(filename) {
    console.log('Iniciando proceso de restauración para:', filename);

    // Guardar el filename globalmente
    backupFilenameToRestore = filename;

    // Mostrar el nombre del archivo en el modal
    document.getElementById('restoreBackupName').textContent = filename;

    // Resetear el checkbox
    const checkbox = document.getElementById('confirmRestore');
    checkbox.checked = false;

    // Deshabilitar el botón de confirmación
    document.getElementById('confirmRestoreBtn').disabled = true;

    // Mostrar el modal
    mostrarModal('restoreBackupModal');
}

/**
 * Función para ejecutar la restauración
 */
function ejecutarRestauracion() {
    console.log('Ejecutando restauración para:', backupFilenameToRestore);

    // Verificar que el checkbox esté marcado
    const checkbox = document.getElementById('confirmRestore');
    if (!checkbox.checked) {
        showErrorModal('Debes confirmar que entiendes las consecuencias de esta acción.');
        return;
    }

    // Deshabilitar el botón para evitar dobles clics
    const confirmBtn = document.getElementById('confirmRestoreBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Restaurando...';

    const restoreRoute = window.respaldosRoutes ?
        window.respaldosRoutes.restore.replace(':filename', backupFilenameToRestore) : '#';
    const csrfToken = window.respaldosCSRF || '';

    // Realizar la restauración
    fetch(restoreRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Respaldo restaurado exitosamente. La página se recargará.');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showErrorModal(data.message || 'Error al restaurar el respaldo');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error de conexión al restaurar el respaldo');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    });
}

/**
 * Función para crear notificaciones persistentes
 */
function showPersistentNotification(message, type = 'info', persistent = true, duration = 0) {
    const container = document.getElementById('persistentNotifications');
    if (!container) return;

    notificationCounter++;
    const notificationId = `notification-${notificationCounter}`;

    let borderColor = 'border-info';
    let iconClass = 'bi-info-circle';

    switch (type) {
        case 'success':
            borderColor = 'border-success';
            iconClass = 'bi-check-circle';
            break;
        case 'error':
            borderColor = 'border-danger';
            iconClass = 'bi-exclamation-triangle';
            break;
        case 'warning':
            borderColor = 'border-warning';
            iconClass = 'bi-exclamation-triangle';
            break;
    }

    // Crear la notificación
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `persistent-notification ${borderColor}`;
    notification.innerHTML = `
        <div class="d-flex align-items-start">
            <i class="bi ${iconClass} me-2 mt-1"></i>
            <div class="flex-grow-1">
                <div class="notification-message">${message}</div>
                <small class="text-muted">${new Date().toLocaleTimeString()}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                    onclick="closePersistentNotification('${notificationId}')">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Auto cerrar si no es persistente
    if (!persistent && duration > 0) {
        setTimeout(() => {
            closePersistentNotification(notificationId);
        }, duration);
    }
}

/**
 * Función para cerrar notificaciones persistentes
 */
function closePersistentNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.classList.add('hide');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

/**
 * Función para asegurar que la información importante siempre esté visible
 */
function ensureImportantInfoVisible() {
    const importantInfo = document.getElementById('restoreImportantInfo');
    if (importantInfo) {
        importantInfo.style.display = 'block';
        importantInfo.style.visibility = 'visible';
        importantInfo.style.opacity = '1';
    }
}

/**
 * Función para inicializar todos los event handlers
 */
function initializeRespaldosHandlers() {
    console.log('🔧 Script de respaldos cargando...');
    console.log('✅ Bootstrap disponible:', typeof bootstrap !== 'undefined');
    console.log('✅ Document estado:', document.readyState);

    // Asegurar que todos los modales estén ocultos al inicio
    ocultarTodosLosModales();

    // Configurar botones para abrir modales
    const botonesAbrir = document.querySelectorAll('[data-bs-toggle="modal"]');
    console.log('Botones encontrados:', botonesAbrir.length);

    botonesAbrir.forEach(function(boton, index) {
        const targetModal = boton.getAttribute('data-bs-target');
        console.log('✅ Configurando botón', index + 1, 'para modal:', targetModal);

        boton.onclick = function(e) {
            e.preventDefault();
            console.log('🔄 CLIC EN BOTÓN DETECTADO!');

            const modalId = targetModal.replace('#', '');
            console.log('🎯 Modal objetivo:', modalId);

            // Verificar que el modal existe antes de intentar mostrarlo
            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error('❌ Modal no encontrado:', modalId);
                return;
            }

            // Configurar datos específicos para modales de eliminación
            if (this.hasAttribute('data-backup-filename')) {
                const filename = this.getAttribute('data-backup-filename');
                const backupName = this.getAttribute('data-backup-name');
                console.log('  - data-backup-filename:', filename);
                console.log('  - data-backup-name:', backupName);

                backupFilenameToDelete = filename;
                const nameElement = document.getElementById('backupNameToDelete');
                if (nameElement) {
                    nameElement.textContent = backupName;
                }
            }

            mostrarModal(modalId);
        };
    });

    // Configurar botones para cerrar modales
    const botonesCerrar = document.querySelectorAll('[data-bs-dismiss="modal"]');
    console.log('✅ Botones cerrar encontrados:', botonesCerrar.length);

    botonesCerrar.forEach(function(boton, index) {
        console.log('⚙️ Configurando botón cerrar', index + 1);
        boton.onclick = function(e) {
            e.preventDefault();
            console.log('❌ CLIC EN CERRAR DETECTADO!');
            const modal = this.closest('.modal');
            if (modal) {
                ocultarModal(modal.id);
            } else {
                ocultarTodosLosModales();
            }
        };
    });

    // Cerrar modal al hacer clic en el backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            console.log('🔄 Clic en backdrop detectado, cerrando todos los modales');
            ocultarTodosLosModales();
        }
    });

    // Event delegation para botones de acción
    setupActionHandlers();

    // Configurar checkbox de confirmación de restauración
    setupRestoreConfirmation();

    // Protección de información importante
    setupImportantInfoProtection();
}

/**
 * Configurar manejadores de acciones principales
 */
function setupActionHandlers() {
    console.log('🔧 Configurando botones de acción con event delegation...');

    document.addEventListener('click', function(e) {
        const clickedElement = e.target;

        // Crear respaldo
        if (clickedElement.id === 'btnCreateBackup') {
            console.log('📝 CREAR RESPALDO - BOTÓN DETECTADO!');
            handleCreateBackup();
        }

        // Limpiar respaldos
        if (clickedElement.id === 'btnConfirmCleanup') {
            console.log('🧹 LIMPIAR RESPALDOS - BOTÓN DETECTADO!');
            handleCleanupBackups();
        }

        // Eliminar respaldo
        if (clickedElement.id === 'btnConfirmDelete') {
            console.log('🗑️ ELIMINAR RESPALDO - BOTÓN DETECTADO!');
            handleDeleteBackup();
        }

        // Confirmar restauración
        if (clickedElement.id === 'confirmRestoreBtn') {
            console.log('⚡ CONFIRMAR RESTAURACIÓN - BOTÓN DETECTADO!');
            ejecutarRestauracion();
        }
    });
}

/**
 * Manejar creación de respaldo
 */
function handleCreateBackup() {
    const btn = document.getElementById('btnCreateBackup');
    const form = document.getElementById('createBackupForm');

    if (!form) {
        console.error('❌ Formulario no encontrado');
        return;
    }

    btn.innerHTML = 'Creando...';
    btn.disabled = true;

    const formData = new FormData(form);
    const createRoute = window.respaldosRoutes ? window.respaldosRoutes.create : '#';
    const csrfToken = window.respaldosCSRF || '';

    console.log('🚀 Enviando petición para crear respaldo...');

    fetch(createRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Respaldo creado exitosamente');
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal(data.message || 'Error al crear respaldo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error de conexión');
    })
    .finally(() => {
        btn.innerHTML = 'Crear Respaldo';
        btn.disabled = false;
    });
}

/**
 * Manejar limpieza de respaldos
 */
function handleCleanupBackups() {
    const btn = document.getElementById('btnConfirmCleanup');
    btn.innerHTML = 'Limpiando...';
    btn.disabled = true;

    const cleanupRoute = window.respaldosRoutes ? window.respaldosRoutes.cleanup : '#';
    const csrfToken = window.respaldosCSRF || '';

    fetch(cleanupRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Respaldos limpiados exitosamente');
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal(data.message || 'Error al limpiar respaldos');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error de conexión');
    })
    .finally(() => {
        btn.innerHTML = 'Confirmar Limpieza';
        btn.disabled = false;
    });
}

/**
 * Manejar eliminación de respaldo
 */
function handleDeleteBackup() {
    if (!backupFilenameToDelete) {
        console.error('❌ No hay archivo para eliminar');
        return;
    }

    const btn = document.getElementById('btnConfirmDelete');
    btn.innerHTML = 'Eliminando...';
    btn.disabled = true;

    const deleteRoute = window.respaldosRoutes ?
        window.respaldosRoutes.delete.replace(':filename', backupFilenameToDelete) : '#';
    const csrfToken = window.respaldosCSRF || '';

    fetch(deleteRoute, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessModal('Respaldo eliminado exitosamente');
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal(data.message || 'Error al eliminar respaldo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('Error de conexión');
    })
    .finally(() => {
        btn.innerHTML = 'Confirmar Eliminación';
        btn.disabled = false;
    });
}

/**
 * Configurar confirmación de restauración
 */
function setupRestoreConfirmation() {
    const checkbox = document.getElementById('confirmRestore');
    const confirmBtn = document.getElementById('confirmRestoreBtn');

    if (checkbox && confirmBtn) {
        checkbox.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
            if (this.checked) {
                confirmBtn.classList.remove('btn-outline-warning');
                confirmBtn.classList.add('btn-warning');
            } else {
                confirmBtn.classList.remove('btn-warning');
                confirmBtn.classList.add('btn-outline-warning');
            }
        });
    }
}

/**
 * Configurar protección de información importante
 */
function setupImportantInfoProtection() {
    // Interceptar la apertura del modal para asegurar visibilidad
    document.addEventListener('click', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('onclick') &&
            e.target.getAttribute('onclick').includes('restaurarBackup')) {
            setTimeout(() => {
                ensureImportantInfoVisible();
            }, 500);
        }
    });

    // Monitorear cambios en el DOM para mantener la información visible
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' || mutation.type === 'childList') {
                    const modal = document.getElementById('restoreBackupModal');
                    if (modal && modal.classList.contains('show')) {
                        ensureImportantInfoVisible();
                    }
                }
            });
        });

        const modal = document.getElementById('restoreBackupModal');
        if (modal) {
            observer.observe(modal, {
                attributes: true,
                childList: true,
                subtree: true
            });
        }
    }
}

/**
 * Inicializar el módulo cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeRespaldosHandlers();
    console.log('✅ Módulo de gestión de respaldos inicializado');
});

// También ejecutar en window.load para mayor compatibilidad
window.addEventListener('load', function() {
    // Verificar que los elementos críticos estén presentes
    const buttons = [
        'btnCreateBackup',
        'btnConfirmCleanup',
        'btnConfirmDelete'
    ];

    buttons.forEach(function(buttonId) {
        const btn = document.getElementById(buttonId);
        console.log(`${buttonId}:`, btn ? '✅ Existe' : '❌ No encontrado');
    });
});

// Exponer funciones globalmente
window.restaurarBackup = restaurarBackup;
window.ejecutarRestauracion = ejecutarRestauracion;
window.showSuccessModal = showSuccessModal;
window.showErrorModal = showErrorModal;
window.closePersistentNotification = closePersistentNotification;