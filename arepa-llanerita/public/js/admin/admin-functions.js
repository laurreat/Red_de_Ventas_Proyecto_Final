// Funciones para alertas y modales profesionales
class AdminAlerts {
    constructor() {
        this.deleteModal = null;
        this.statusModal = null;
        this.saveModal = null;
        this.currentDeleteForm = null;
        this.currentStatusForm = null;
        this.currentSaveForm = null;

        this.init();
    }

    init() {
        // Inicializar modales cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            this.initModals();
            this.initToasts();
        });
    }

    initModals() {
        // Inicializar modales
        const deleteModalEl = document.getElementById('deleteConfirmModal');
        const statusModalEl = document.getElementById('statusConfirmModal');
        const saveModalEl = document.getElementById('saveConfirmModal');

        if (deleteModalEl && typeof bootstrap !== 'undefined') {
            this.deleteModal = new bootstrap.Modal(deleteModalEl);
        }
        if (statusModalEl && typeof bootstrap !== 'undefined') {
            this.statusModal = new bootstrap.Modal(statusModalEl);
        }
        if (saveModalEl && typeof bootstrap !== 'undefined') {
            this.saveModal = new bootstrap.Modal(saveModalEl);
        }

        // Event listeners para confirmaciones
        this.setupModalEventListeners();
    }

    setupModalEventListeners() {
        // Confirmar eliminación
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => {
                if (this.currentDeleteForm) {
                    this.currentDeleteForm.submit();
                }
            });
        }

        // Confirmar cambio de estado
        const confirmStatusBtn = document.getElementById('confirmStatusBtn');
        if (confirmStatusBtn) {
            confirmStatusBtn.addEventListener('click', () => {
                if (this.currentStatusForm) {
                    this.currentStatusForm.submit();
                }
            });
        }

        // Confirmar guardado
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');
        if (confirmSaveBtn) {
            confirmSaveBtn.addEventListener('click', () => {
                if (this.currentSaveForm) {
                    this.currentSaveForm.submit();
                }
            });
        }
    }

    initToasts() {
        // Auto-hide alerts después de 5 segundos
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }
            }, 5000);
        });
    }

    // Mostrar modal de confirmación para eliminar
    confirmDelete(productId, productName = '', productCategory = '', productImage = '') {
        if (!this.deleteModal) return;

        // Actualizar información del producto en el modal
        const nameEl = document.getElementById('deleteProductName');
        const categoryEl = document.getElementById('deleteProductCategory');
        const imageEl = document.getElementById('deleteProductImage');

        if (nameEl) nameEl.textContent = productName;
        if (categoryEl) categoryEl.textContent = productCategory;
        if (imageEl) {
            imageEl.src = productImage || 'https://via.placeholder.com/50';
            imageEl.alt = productName;
        }

        // Establecer formulario actual
        this.currentDeleteForm = document.getElementById(`delete-form-${productId}`);

        // Mostrar modal
        this.deleteModal.show();
    }

    // Mostrar modal de confirmación para cambio de estado
    confirmStatusToggle(productId, isActive, productName = '') {
        if (!this.statusModal) return;

        const statusHeader = document.getElementById('statusModalHeader');
        const statusIcon = document.getElementById('statusIcon');
        const statusIconContainer = document.getElementById('statusIconContainer');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const statusBtn = document.getElementById('confirmStatusBtn');
        const statusBtnText = document.getElementById('statusBtnText');
        const statusBtnIcon = document.getElementById('statusBtnIcon');

        if (isActive) {
            // Desactivar producto
            statusHeader.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
            statusIconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
            statusIcon.className = 'bi bi-pause-fill text-warning fs-1';
            statusTitle.textContent = '¿Deseas desactivar este producto?';
            statusMessage.textContent = 'El producto no será visible en el catálogo y no estará disponible para venta.';
            statusBtn.className = 'btn btn-warning';
            statusBtnIcon.className = 'bi bi-pause me-1';
            statusBtnText.textContent = 'Desactivar Producto';
        } else {
            // Activar producto
            statusHeader.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
            statusIconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
            statusIcon.className = 'bi bi-play-fill text-success fs-1';
            statusTitle.textContent = '¿Deseas activar este producto?';
            statusMessage.textContent = 'El producto será visible en el catálogo y estará disponible para venta.';
            statusBtn.className = 'btn btn-success';
            statusBtnIcon.className = 'bi bi-play me-1';
            statusBtnText.textContent = 'Activar Producto';
        }

        // Establecer formulario actual
        this.currentStatusForm = document.getElementById(`toggle-form-${productId}`);

        // Mostrar modal
        this.statusModal.show();
    }

    // Mostrar modal de confirmación para guardar
    confirmSave(formId, message = 'Los cambios realizados se guardarán en el sistema.') {
        if (!this.saveModal) return;

        const saveMessage = document.getElementById('saveMessage');
        if (saveMessage) {
            saveMessage.textContent = message;
        }

        // Establecer formulario actual
        this.currentSaveForm = document.getElementById(formId) || document.querySelector(`form[data-form-id="${formId}"]`);

        // Mostrar modal
        this.saveModal.show();
    }

    // Mostrar alerta de éxito
    showSuccess(title, message) {
        this.showAlert('success', title, message);
    }

    // Mostrar alerta de error
    showError(title, message) {
        this.showAlert('error', title, message);
    }

    // Mostrar alerta de advertencia
    showWarning(title, message) {
        this.showAlert('warning', title, message);
    }

    // Mostrar alerta de información
    showInfo(title, message) {
        this.showAlert('info', title, message);
    }

    // Función general para mostrar alertas
    showAlert(type, title, message) {
        // Remover TODAS las alertas existentes en toda la página (incluidas las del DOM y las dinámicas)
        const existingAlerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        existingAlerts.forEach(alert => {
            // Cerrar con Bootstrap si es posible
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                try {
                    const bsAlert = bootstrap.Alert.getInstance(alert);
                    if (bsAlert) bsAlert.dispose();
                } catch (e) {}
            }
            // Eliminar del DOM
            alert.remove();
        });

        // Limpiar el messagesArea completamente si existe
        const existingMessagesArea = document.getElementById('messagesArea');
        if (existingMessagesArea) {
            existingMessagesArea.innerHTML = '';
        }

        let alertClass, iconClass, borderColor;
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                iconClass = 'bi-check-circle-fill text-success';
                borderColor = '#28a745';
                break;
            case 'error':
                alertClass = 'alert-danger';
                iconClass = 'bi-exclamation-triangle-fill text-danger';
                borderColor = '#dc3545';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                iconClass = 'bi-exclamation-circle-fill text-warning';
                borderColor = '#ffc107';
                break;
            case 'info':
                alertClass = 'alert-info';
                iconClass = 'bi-info-circle-fill text-info';
                borderColor = '#17a2b8';
                break;
            default:
                alertClass = 'alert-info';
                iconClass = 'bi-info-circle-fill text-info';
                borderColor = '#17a2b8';
        }

        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid ${borderColor} !important;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi ${iconClass} fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1 fw-bold">${title}</h6>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Buscar área de mensajes o crearla
        let messagesArea = document.getElementById('messagesArea');
        if (!messagesArea) {
            messagesArea = document.createElement('div');
            messagesArea.id = 'messagesArea';
            messagesArea.className = 'mb-4';

            // Insertar al inicio del contenido principal
            const mainContent = document.querySelector('.admin-main .container-fluid') ||
                              document.querySelector('.admin-main') ||
                              document.querySelector('main');

            if (mainContent) {
                mainContent.insertBefore(messagesArea, mainContent.firstChild);
            }
        }

        messagesArea.innerHTML = alertHTML;

        // Auto-hide después de 5 segundos
        setTimeout(() => {
            const alert = messagesArea.querySelector('.alert');
            if (alert && typeof bootstrap !== 'undefined') {
                const bsAlert = new bootstrap.Alert(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            }
        }, 5000);

        // Scroll hacia arriba para mostrar la alerta
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Mostrar toast
    showToast(type, title, message, duration = 4000) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        let bgClass, iconClass;
        switch(type) {
            case 'success':
                bgClass = 'bg-success';
                iconClass = 'bi-check-circle-fill';
                break;
            case 'error':
                bgClass = 'bg-danger';
                iconClass = 'bi-exclamation-triangle-fill';
                break;
            case 'warning':
                bgClass = 'bg-warning';
                iconClass = 'bi-exclamation-circle-fill';
                break;
            case 'info':
                bgClass = 'bg-info';
                iconClass = 'bi-info-circle-fill';
                break;
            default:
                bgClass = 'bg-info';
                iconClass = 'bi-info-circle-fill';
        }

        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgClass} text-white border-0">
                    <i class="bi ${iconClass} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        if (toastElement && typeof bootstrap !== 'undefined') {
            const toast = new bootstrap.Toast(toastElement, {
                delay: duration
            });
            toast.show();

            // Remover elemento después de que se oculte
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    }
}

// Inicializar el sistema de alertas
const adminAlerts = new AdminAlerts();

// Funciones globales para mantener compatibilidad
function confirmDelete(productId) {
    // Obtener información del producto desde la fila de la tabla
    const productRow = document.querySelector(`[data-product-id="${productId}"]`);
    let productName = '';
    let productCategory = '';
    let productImage = '';

    if (productRow) {
        const nameElement = productRow.querySelector('.producto-nombre');
        const categoryElement = productRow.querySelector('.categoria-badge');
        const imageElement = productRow.querySelector('img');

        productName = nameElement ? nameElement.textContent.trim() : '';
        productCategory = categoryElement ? categoryElement.textContent.trim() : '';
        productImage = imageElement ? imageElement.src : '';
    }

    adminAlerts.confirmDelete(productId, productName, productCategory, productImage);
}

function toggleStatus(productId) {
    // Obtener información del producto desde la fila de la tabla
    const productRow = document.querySelector(`[data-product-id="${productId}"]`);
    let isActive = false;
    let productName = '';

    if (productRow) {
        const statusBadge = productRow.querySelector('td:nth-child(5) .badge');
        const nameElement = productRow.querySelector('.producto-nombre');

        isActive = statusBadge && statusBadge.textContent.trim() === 'Activo';
        productName = nameElement ? nameElement.textContent.trim() : '';
    }

    adminAlerts.confirmStatusToggle(productId, isActive, productName);
}

function confirmSave(formId, message) {
    adminAlerts.confirmSave(formId, message);
}

// Funciones para mostrar alertas directamente
function showSuccess(title, message) {
    adminAlerts.showSuccess(title, message);
}

function showError(title, message) {
    adminAlerts.showError(title, message);
}

function showWarning(title, message) {
    adminAlerts.showWarning(title, message);
}

function showInfo(title, message) {
    adminAlerts.showInfo(title, message);
}

// Funciones para toasts
function showToast(type, title, message, duration = 4000) {
    adminAlerts.showToast(type, title, message, duration);
}

// Event listeners para formularios que requieren confirmación
document.addEventListener('DOMContentLoaded', function() {
    // Interceptar formularios con clase 'needs-confirmation'
    const formsNeedingConfirmation = document.querySelectorAll('form.needs-confirmation');

    formsNeedingConfirmation.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = this.dataset.confirmMessage || 'Los cambios realizados se guardarán en el sistema.';
            const formId = this.id || 'form-' + Date.now();

            if (!this.id) {
                this.id = formId;
            }

            adminAlerts.confirmSave(formId, message);
        });
    });
});

// Exponer funciones al scope global para usar desde Blade templates
window.confirmDelete = confirmDelete;
window.toggleStatus = toggleStatus;
window.confirmSave = confirmSave;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.showToast = showToast;
window.adminAlerts = adminAlerts;