{{-- Container para Toast Notifications --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

<style>
.toast {
    min-width: 300px;
    max-width: 400px;
}

.toast-header {
    font-weight: 600;
}

.toast-body {
    background-color: white;
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

@media (max-width: 576px) {
    .toast-container {
        position: fixed !important;
        top: 10px !important;
        left: 10px !important;
        right: 10px !important;
        transform: none !important;
    }

    .toast {
        width: 100% !important;
        min-width: auto !important;
        max-width: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos los toasts
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(function(toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    });
});

// Función para mostrar toast programáticamente
function showToast(type, title, message, delay = 5000) {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();

    let headerClass, iconClass;
    switch(type) {
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
    newToast.addEventListener('hidden.bs.toast', function() {
        newToast.remove();
    });
}

// Funciones de utilidad globales
window.showSuccessToast = function(message, title = '¡Éxito!') {
    showToast('success', title, message);
};

window.showErrorToast = function(message, title = 'Error') {
    showToast('error', title, message);
};

window.showWarningToast = function(message, title = 'Advertencia') {
    showToast('warning', title, message);
};

window.showInfoToast = function(message, title = 'Información') {
    showToast('info', title, message);
};
</script>