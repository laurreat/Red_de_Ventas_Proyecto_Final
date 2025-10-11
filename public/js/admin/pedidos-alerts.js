// Sistema de alertas y modales profesionales para gestión de pedidos
class PedidosAlerts {
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
        const deleteModalEl = document.getElementById('deletePedidoConfirmModal');
        const statusModalEl = document.getElementById('statusPedidoConfirmModal');
        const saveModalEl = document.getElementById('savePedidoConfirmModal');

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
        const confirmDeleteBtn = document.getElementById('confirmDeletePedidoBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => {
                if (this.currentDeleteForm) {
                    this.currentDeleteForm.submit();
                }
            });
        }

        // Confirmar cambio de estado
        const confirmStatusBtn = document.getElementById('confirmStatusPedidoBtn');
        if (confirmStatusBtn) {
            confirmStatusBtn.addEventListener('click', () => {
                if (this.currentStatusForm) {
                    this.currentStatusForm.submit();
                }
            });
        }

        // Confirmar guardado
        const confirmSaveBtn = document.getElementById('confirmSavePedidoBtn');
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

    // Mostrar modal de confirmación para eliminar pedido
    confirmDelete(pedidoId, numeroPedido = '', cliente = '', total = '', estado = '') {
        if (!this.deleteModal) return;

        // Actualizar información del pedido en el modal
        const numeroEl = document.getElementById('deletePedidoNumero');
        const clienteEl = document.getElementById('deletePedidoCliente');
        const totalEl = document.getElementById('deletePedidoTotal');
        const estadoEl = document.getElementById('deletePedidoEstado');

        if (numeroEl) numeroEl.textContent = numeroPedido;
        if (clienteEl) clienteEl.textContent = cliente;
        if (totalEl) totalEl.textContent = total;
        if (estadoEl) {
            estadoEl.textContent = estado;
            estadoEl.className = this.getEstadoBadgeClass(estado.toLowerCase());
        }

        // Establecer formulario actual
        this.currentDeleteForm = document.getElementById(`delete-form-${pedidoId}`);

        // Mostrar modal
        this.deleteModal.show();
    }

    // Mostrar modal de confirmación para cambio de estado
    confirmStatusChange(pedidoId, newStatus, numeroPedido = '', cliente = '', currentStatus = '') {
        if (!this.statusModal) return;

        const statusConfig = this.getStatusConfig(newStatus);

        // Actualizar elementos del modal
        const statusHeader = document.getElementById('statusPedidoModalHeader');
        const statusIcon = document.getElementById('statusPedidoIcon');
        const statusIconContainer = document.getElementById('statusPedidoIconContainer');
        const statusTitle = document.getElementById('statusPedidoTitle');
        const statusMessage = document.getElementById('statusPedidoMessage');
        const statusBtn = document.getElementById('confirmStatusPedidoBtn');
        const statusBtnText = document.getElementById('statusPedidoBtnText');
        const statusBtnIcon = document.getElementById('statusPedidoBtnIcon');

        // Información del pedido
        const numeroEl = document.getElementById('statusPedidoNumero');
        const clienteEl = document.getElementById('statusPedidoCliente');
        const currentStatusEl = document.getElementById('statusPedidoCurrentStatus');
        const newStatusEl = document.getElementById('statusPedidoNewStatus');

        if (statusHeader) statusHeader.style.background = statusConfig.gradient;
        if (statusIconContainer) statusIconContainer.style.backgroundColor = statusConfig.bgColor;
        if (statusIcon) statusIcon.className = statusConfig.icon;
        if (statusTitle) statusTitle.textContent = statusConfig.title;
        if (statusMessage) statusMessage.textContent = statusConfig.message;
        if (statusBtn) statusBtn.className = statusConfig.btnClass;
        if (statusBtnIcon) statusBtnIcon.className = statusConfig.btnIcon;
        if (statusBtnText) statusBtnText.textContent = statusConfig.btnText;

        if (numeroEl) numeroEl.textContent = numeroPedido;
        if (clienteEl) clienteEl.textContent = cliente;
        if (currentStatusEl) {
            currentStatusEl.textContent = currentStatus;
            currentStatusEl.className = this.getEstadoBadgeClass(currentStatus.toLowerCase());
        }
        if (newStatusEl) {
            newStatusEl.textContent = statusConfig.displayName;
            newStatusEl.className = statusConfig.badgeClass;
        }

        // Establecer formulario actual
        this.currentStatusForm = document.getElementById(`status-form-${pedidoId}`);

        // Establecer el valor del estado en el formulario
        const estadoInput = document.getElementById(`estado-${pedidoId}`);
        if (estadoInput) {
            estadoInput.value = newStatus;
        }

        // Mostrar modal
        this.statusModal.show();
    }

    // Mostrar modal de confirmación para guardar
    confirmSave(formId, message = 'Los cambios del pedido se guardarán en el sistema.') {
        if (!this.saveModal) return;

        const saveMessage = document.getElementById('savePedidoMessage');
        if (saveMessage) {
            saveMessage.textContent = message;
        }

        // Establecer formulario actual
        this.currentSaveForm = document.getElementById(formId) || document.querySelector(`form[data-form-id="${formId}"]`);

        // Mostrar modal
        this.saveModal.show();
    }

    // Configuración de estados para modales
    getStatusConfig(status) {
        const configs = {
            'pendiente': {
                gradient: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
                bgColor: 'rgba(255, 193, 7, 0.1)',
                icon: 'bi bi-hourglass-split text-warning fs-1',
                title: '¿Marcar como pendiente?',
                message: 'El pedido será marcado como pendiente de procesamiento.',
                btnClass: 'btn btn-warning',
                btnIcon: 'bi bi-hourglass-split me-1',
                btnText: 'Marcar Pendiente',
                badgeClass: 'badge bg-warning',
                displayName: 'Pendiente'
            },
            'confirmado': {
                gradient: 'linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%)',
                bgColor: 'rgba(13, 202, 240, 0.1)',
                icon: 'bi bi-check-circle text-info fs-1',
                title: '¿Confirmar pedido?',
                message: 'El pedido será confirmado y listo para preparar.',
                btnClass: 'btn btn-info',
                btnIcon: 'bi bi-check-circle me-1',
                btnText: 'Confirmar Pedido',
                badgeClass: 'badge bg-info',
                displayName: 'Confirmado'
            },
            'en_preparacion': {
                gradient: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
                bgColor: 'rgba(13, 110, 253, 0.1)',
                icon: 'bi bi-tools text-primary fs-1',
                title: '¿Marcar como en preparación?',
                message: 'El pedido está siendo preparado para entrega.',
                btnClass: 'btn btn-primary',
                btnIcon: 'bi bi-tools me-1',
                btnText: 'Marcar En Preparación',
                badgeClass: 'badge bg-primary',
                displayName: 'En Preparación'
            },
            'listo': {
                gradient: 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)',
                bgColor: 'rgba(108, 117, 125, 0.1)',
                icon: 'bi bi-check2-square text-secondary fs-1',
                title: '¿Marcar como listo?',
                message: 'El pedido está listo para ser enviado.',
                btnClass: 'btn btn-secondary',
                btnIcon: 'bi bi-check2-square me-1',
                btnText: 'Marcar Listo',
                badgeClass: 'badge bg-secondary',
                displayName: 'Listo'
            },
            'en_camino': {
                gradient: 'linear-gradient(135deg, #722f37 0%, #5c252b 100%)',
                bgColor: 'rgba(114, 47, 55, 0.1)',
                icon: 'bi bi-truck fs-1',
                title: '¿Marcar como en camino?',
                message: 'El pedido ha sido enviado y está en camino.',
                btnClass: 'btn',
                btnIcon: 'bi bi-truck me-1',
                btnText: 'Marcar En Camino',
                badgeClass: 'badge',
                displayName: 'En Camino'
            },
            'entregado': {
                gradient: 'linear-gradient(135deg, #198754 0%, #146c43 100%)',
                bgColor: 'rgba(25, 135, 84, 0.1)',
                icon: 'bi bi-check-all text-success fs-1',
                title: '¿Marcar como entregado?',
                message: 'El pedido ha sido entregado exitosamente al cliente.',
                btnClass: 'btn btn-success',
                btnIcon: 'bi bi-check-all me-1',
                btnText: 'Marcar Entregado',
                badgeClass: 'badge bg-success',
                displayName: 'Entregado'
            },
            'cancelado': {
                gradient: 'linear-gradient(135deg, #dc3545 0%, #b02a37 100%)',
                bgColor: 'rgba(220, 53, 69, 0.1)',
                icon: 'bi bi-x-circle text-danger fs-1',
                title: '¿Cancelar pedido?',
                message: 'El pedido será cancelado y no se procesará.',
                btnClass: 'btn btn-danger',
                btnIcon: 'bi bi-x-circle me-1',
                btnText: 'Cancelar Pedido',
                badgeClass: 'badge bg-danger',
                displayName: 'Cancelado'
            }
        };

        return configs[status] || configs['pendiente'];
    }

    getEstadoBadgeClass(estado) {
        const classes = {
            'pendiente': 'badge bg-warning',
            'confirmado': 'badge bg-info',
            'en_preparacion': 'badge bg-primary',
            'listo': 'badge bg-secondary',
            'en_camino': 'badge',
            'entregado': 'badge bg-success',
            'cancelado': 'badge bg-danger'
        };
        return classes[estado] || 'badge bg-secondary';
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
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('#messagesArea .alert');
        existingAlerts.forEach(alert => alert.remove());

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
                              document.querySelector('main') ||
                              document.querySelector('.container-fluid');

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
        const toastContainer = document.getElementById('pedidosToastContainer');
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

// Inicializar el sistema de alertas para pedidos
const pedidosAlerts = new PedidosAlerts();

// Funciones globales para mantener compatibilidad
function confirmDeletePedido(pedidoId, numeroPedido, cliente, total, estado) {
    pedidosAlerts.confirmDelete(pedidoId, numeroPedido, cliente, total, estado);
}

function confirmStatusChangePedido(pedidoId, newStatus, numeroPedido, cliente, currentStatus) {
    pedidosAlerts.confirmStatusChange(pedidoId, newStatus, numeroPedido, cliente, currentStatus);
}

function confirmSavePedido(formId, message) {
    pedidosAlerts.confirmSave(formId, message);
}

// Funciones para mostrar alertas directamente
function showPedidoSuccess(title, message) {
    pedidosAlerts.showSuccess(title, message);
}

function showPedidoError(title, message) {
    pedidosAlerts.showError(title, message);
}

function showPedidoWarning(title, message) {
    pedidosAlerts.showWarning(title, message);
}

function showPedidoInfo(title, message) {
    pedidosAlerts.showInfo(title, message);
}

// Funciones para toasts
function showPedidoToast(type, title, message, duration = 4000) {
    pedidosAlerts.showToast(type, title, message, duration);
}

// Exponer funciones al scope global para usar desde Blade templates
window.confirmDeletePedido = confirmDeletePedido;
window.confirmStatusChangePedido = confirmStatusChangePedido;
window.confirmSavePedido = confirmSavePedido;
window.showPedidoSuccess = showPedidoSuccess;
window.showPedidoError = showPedidoError;
window.showPedidoWarning = showPedidoWarning;
window.showPedidoInfo = showPedidoInfo;
window.showPedidoToast = showPedidoToast;
window.pedidosAlerts = pedidosAlerts;