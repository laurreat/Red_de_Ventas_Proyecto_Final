/**
 * Funciones del sistema para configuración
 * Módulo para backup, cache, logs e información del sistema
 */

// Declarar funciones en el ámbito global
console.log('Configuración: Cargando funciones JavaScript del sistema...');

// Verificar que Bootstrap esté disponible
console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
if (typeof bootstrap !== 'undefined') {
    console.log('Bootstrap Modal disponible');
}

/**
 * Función para crear backup del sistema
 */
function crearBackup() {
    console.log('crearBackup() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal');
        $('#backupConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de backup');
        const modal = document.getElementById('backupConfirmModal');
        if (modal) {
            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModalNative(modal, modalDialog);
                }
            });

            console.log('Modal de backup mostrado con métodos nativos');
        } else {
            console.error('Modal backupConfirmModal no encontrado');
            alert('Error: Modal no encontrado');
        }
    }
}

/**
 * Función para limpiar cache del sistema
 */
function limpiarCache() {
    console.log('limpiarCache() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal de cache');
        $('#cacheConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de cache');
        const modal = document.getElementById('cacheConfirmModal');
        if (modal) {
            console.log('Modal encontrado:', modal);

            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModalNative(modal, modalDialog);
                }
            });

            console.log('Modal de cache mostrado con métodos nativos');
        } else {
            console.error('Modal cacheConfirmModal no encontrado en el DOM');
            alert('Error: Modal no encontrado');
        }
    }
}

/**
 * Función para limpiar logs del sistema
 */
function limpiarLogs() {
    console.log('limpiarLogs() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal de logs');
        $('#logsConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de logs');
        const modal = document.getElementById('logsConfirmModal');
        if (modal) {
            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModalNative(modal, modalDialog);
                }
            });

            console.log('Modal de logs mostrado con métodos nativos');
        } else {
            console.error('Modal logsConfirmModal no encontrado');
            alert('Error: Modal no encontrado');
        }
    }
}

/**
 * Función para mostrar información del sistema
 */
function mostrarInfoSistema() {
    console.log('mostrarInfoSistema() llamada');

    // Primero mostrar el modal con loading
    const infoModal = document.getElementById('infoSistemaModal');
    console.log('infoModal encontrado:', infoModal);

    if (!infoModal) {
        console.error('Modal infoSistemaModal no encontrado');
        alert('Error: Modal no encontrado');
        return;
    }

    // Mostrar modal inmediatamente con contenido de carga
    if (typeof $ !== 'undefined') {
        $(infoModal).modal('show');
    } else {
        // Mostrar modal de forma simple y directa
        infoModal.classList.remove('fade');
        infoModal.classList.add('show');
        infoModal.style.display = 'flex';
        infoModal.style.alignItems = 'center';
        infoModal.style.justifyContent = 'center';
        infoModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        infoModal.setAttribute('aria-modal', 'true');
        infoModal.setAttribute('role', 'dialog');

        // Asegurar que el modal-dialog esté visible
        const modalDialog = infoModal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.margin = '0';
            modalDialog.style.zIndex = '1060';
            modalDialog.style.position = 'relative';
        }

        // Deshabilitar scroll del body
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';

        // Cerrar modal al hacer clic en el fondo
        infoModal.addEventListener('click', function(e) {
            if (e.target === infoModal) {
                closeModalNative(infoModal, modalDialog);
            }
        });
    }

    // Ahora hacer el fetch para cargar los datos
    const infoRoute = window.configuracionRoutes ? window.configuracionRoutes.infoSistema : '#';
    fetch(infoRoute)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let html = '';

                // Información del Sistema
                html += `<div class="mb-4">
                    <h6 class="text-primary mb-3"><i class="bi bi-cpu me-2"></i>Información del Sistema</h6>
                    <div class="row">`;
                Object.keys(data.data.sistema || {}).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.sistema[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                // Información de la Aplicación
                html += `<div class="mb-4">
                    <h6 class="text-success mb-3"><i class="bi bi-app me-2"></i>Información de la Aplicación</h6>
                    <div class="row">`;
                Object.keys(data.data.aplicacion || {}).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.aplicacion[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                // Estadísticas
                html += `<div class="mb-4">
                    <h6 class="text-warning mb-3"><i class="bi bi-graph-up me-2"></i>Estadísticas de Uso</h6>
                    <div class="row">`;
                Object.keys(data.data.estadisticas || {}).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.estadisticas[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                document.getElementById('infoSistemaContent').innerHTML = html;
            } else {
                document.getElementById('infoSistemaContent').innerHTML =
                    '<div class="alert alert-danger">❌ Error al cargar información: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            document.getElementById('infoSistemaContent').innerHTML =
                '<div class="alert alert-danger">❌ Error de conexión: ' + error.message + '</div>';
        });

    console.log('Modal de info sistema mostrado y datos cargados');
}

/**
 * Función helper para cerrar modales nativos
 */
function closeModalNative(modal, modalDialog) {
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.style.backgroundColor = '';
    modal.style.alignItems = '';
    modal.style.justifyContent = '';
    modal.removeAttribute('aria-modal');
    modal.removeAttribute('role');

    if (modalDialog) {
        modalDialog.style.margin = '';
        modalDialog.style.zIndex = '';
        modalDialog.style.position = '';
    }

    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
}

// Asegurar que las funciones estén disponibles globalmente
window.crearBackup = crearBackup;
window.limpiarCache = limpiarCache;
window.limpiarLogs = limpiarLogs;
window.mostrarInfoSistema = mostrarInfoSistema;

console.log('Configuración: Funciones del sistema cargadas:', {
    crearBackup: typeof crearBackup,
    limpiarCache: typeof limpiarCache,
    limpiarLogs: typeof limpiarLogs,
    mostrarInfoSistema: typeof mostrarInfoSistema
});