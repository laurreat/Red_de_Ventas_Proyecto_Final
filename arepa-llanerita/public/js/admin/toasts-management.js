/**
 * Toasts Management
 * Gestión de notificaciones toast
 */

class ToastManager {
    constructor() {
        this.init();
    }

    init() {
        // Inicializar todos los toasts existentes
        const toastElements = document.querySelectorAll('.toast');
        toastElements.forEach((toastEl) => {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    }

    /**
     * Mostrar toast programáticamente
     * @param {string} type - Tipo de toast (success, error, warning, info)
     * @param {string} title - Título del toast
     * @param {string} message - Mensaje del toast
     * @param {number} delay - Tiempo en ms antes de ocultar (default: 5000)
     */
    show(type, title, message, delay = 5000) {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            console.error('Toast container not found');
            return;
        }

        const toastId = 'toast-' + Date.now();

        let headerClass, iconClass;
        switch (type) {
            case 'success':
                headerClass = 'bg-success text-white';
                iconClass = 'bi-check-circle-fill';
                break;
            case 'error':
                headerClass = 'bg-danger text-white';
                iconClass = 'bi-exclamation-triangle-fill';
                break;
            case 'warning':
                headerClass = 'bg-warning text-dark';
                iconClass = 'bi-exclamation-circle-fill';
                break;
            case 'info':
                headerClass = 'bg-info text-white';
                iconClass = 'bi-info-circle-fill';
                break;
            default:
                headerClass = 'bg-primary text-white';
                iconClass = 'bi-info-circle-fill';
        }

        const toastHTML = `
            <div class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="${delay}" id="${toastId}">
                <div class="toast-header ${headerClass} border-0">
                    <i class="bi ${iconClass} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'}" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const newToast = document.getElementById(toastId);
        const toast = new bootstrap.Toast(newToast);
        toast.show();

        // Remover el elemento del DOM cuando se oculte
        newToast.addEventListener('hidden.bs.toast', function () {
            newToast.remove();
        });
    }
}

// Inicialización automática
document.addEventListener('DOMContentLoaded', function () {
    window.toastManager = new ToastManager();
});

// Funciones de utilidad globales
window.showSuccessToast = function (message, title = '¡Éxito!') {
    if (window.toastManager) {
        window.toastManager.show('success', title, message);
    }
};

window.showErrorToast = function (message, title = 'Error') {
    if (window.toastManager) {
        window.toastManager.show('error', title, message);
    }
};

window.showWarningToast = function (message, title = 'Advertencia') {
    if (window.toastManager) {
        window.toastManager.show('warning', title, message);
    }
};

window.showInfoToast = function (message, title = 'Información') {
    if (window.toastManager) {
        window.toastManager.show('info', title, message);
    }
};
