/**
 * SISTEMA DE MODALES DE CONFIRMACIÓN PROFESIONALES
 * Con diseño glassmorphism moderno
 */

(function() {
    // Evitar redeclaración si ya existe
    if (typeof window.GlassModal !== 'undefined') {
        return;
    }

    class GlassModal {
        static show(options = {}) {
            const {
                title = 'Confirmación',
                message = '¿Estás seguro?',
                icon = 'bi-question-circle-fill',
                iconColor = '#ffc107',
                iconBg = 'rgba(255, 193, 7, 0.2)',
                confirmText = 'Confirmar',
                cancelText = 'Cancelar',
                confirmClass = 'btn-glass-primary',
                type = 'confirm',
                onConfirm = () => {},
                onCancel = () => {}
            } = options;

            // Remover modal y backdrops existentes
            const existingModal = document.getElementById('glassConfirmModal');
            if (existingModal) {
                const bsModal = bootstrap.Modal.getInstance(existingModal);
                if (bsModal) bsModal.dispose();
                existingModal.remove();
            }

            // Limpiar backdrops huérfanos
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });

            // Crear el modal
            const modal = this.createModal({
                title,
                message,
                icon,
                iconColor,
                iconBg,
                confirmText,
                cancelText,
                confirmClass,
                type
            });
            
            document.body.appendChild(modal);

            // Crear instancia del modal de Bootstrap
            const bsModal = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: true
            });

            // Event handlers
            const confirmBtn = modal.querySelector('.btn-confirm');
            const cancelBtn = modal.querySelector('.btn-cancel');

            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    onConfirm();
                    bsModal.hide();
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    onCancel();
                    bsModal.hide();
                });
            }

            // Limpiar al cerrar
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
                // Limpiar backdrops huérfanos después de cerrar
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }, 100);
            });

            // Mostrar modal
            bsModal.show();
            
            // Asegurar z-index máximo para el modal y backdrop
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop.show');
                if (backdrop) {
                    backdrop.style.zIndex = '10099';
                }
                modal.style.zIndex = '10100';
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.zIndex = '10101';
                }
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.zIndex = '10102';
                }
            }, 50);
        }

    static createModal(options) {
        const {
            title,
            message,
            icon,
            iconColor,
            iconBg,
            confirmText,
            cancelText,
            confirmClass,
            type
        } = options;

        const showCancel = type === 'confirm';

        const modalHTML = `
            <div class="modal fade" id="glassConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true" style="z-index: 10100 !important;">
                <div class="modal-dialog modal-dialog-centered" style="z-index: 10101 !important;">
                    <div class="modal-content glass-modal-enhanced" style="z-index: 10102 !important;">
                        <!-- Fondos decorativos glassmorphism -->
                        <div class="glass-bg-layer-1"></div>
                        <div class="glass-bg-layer-2"></div>
                        <div class="glass-bg-gradient"></div>
                        
                        <!-- Contenido del modal -->
                        <div class="modal-body text-center glass-modal-body-enhanced">
                            <!-- Icono animado con anillos -->
                            <div class="glass-icon-container">
                                <div class="glass-icon-ring-outer" style="border-color: ${iconColor}30;"></div>
                                <div class="glass-icon-ring-middle" style="border-color: ${iconColor}20;"></div>
                                <div class="glass-icon-wrapper" style="background: ${iconBg}; border-color: ${iconColor}40; box-shadow: 0 8px 32px ${iconColor}25;">
                                    <i class="${icon}" style="color: ${iconColor};"></i>
                                </div>
                            </div>
                            
                            <!-- Título y mensaje -->
                            <div class="glass-content-wrapper">
                                <h3 class="glass-modal-title">${title}</h3>
                                <p class="glass-modal-message">${message}</p>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="glass-actions-wrapper">
                                ${showCancel ? `
                                    <button type="button" class="btn-glass-enhanced btn-cancel btn-glass-secondary-enhanced">
                                        <i class="bi bi-x-circle"></i>
                                        <span>${cancelText}</span>
                                    </button>
                                ` : ''}
                                <button type="button" class="btn-glass-enhanced btn-confirm ${confirmClass}-enhanced">
                                    <i class="bi ${type === 'confirm' ? 'bi-check-circle' : 'bi-check2'}"></i>
                                    <span>${confirmText}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const temp = document.createElement('div');
        temp.innerHTML = modalHTML.trim();
        return temp.firstElementChild;
    }

    static success(title, message, callback = null) {
        this.show({
            title,
            message,
            icon: 'bi-check-circle-fill',
            iconColor: '#10b981',
            iconBg: 'rgba(16, 185, 129, 0.2)',
            confirmText: 'Entendido',
            confirmClass: 'btn-glass-success',
            type: 'alert',
            onConfirm: callback || (() => {})
        });
    }

    static error(title, message, callback = null) {
        this.show({
            title,
            message,
            icon: 'bi-x-circle-fill',
            iconColor: '#ef4444',
            iconBg: 'rgba(239, 68, 68, 0.2)',
            confirmText: 'Entendido',
            confirmClass: 'btn-glass-danger',
            type: 'alert',
            onConfirm: callback || (() => {})
        });
    }

    static warning(title, message, callback = null) {
        this.show({
            title,
            message,
            icon: 'bi-exclamation-triangle-fill',
            iconColor: '#f59e0b',
            iconBg: 'rgba(245, 158, 11, 0.2)',
            confirmText: 'Entendido',
            confirmClass: 'btn-glass-warning',
            type: 'alert',
            onConfirm: callback || (() => {})
        });
    }

    static info(title, message, callback = null) {
        this.show({
            title,
            message,
            icon: 'bi-info-circle-fill',
            iconColor: '#3b82f6',
            iconBg: 'rgba(59, 130, 246, 0.2)',
            confirmText: 'Entendido',
            confirmClass: 'btn-glass-info',
            type: 'alert',
            onConfirm: callback || (() => {})
        });
    }

    static confirm(options = {}) {
        const {
            title = 'Confirmación',
            message = '¿Estás seguro de realizar esta acción?',
            icon = 'bi-question-circle-fill',
            iconColor = '#ffc107',
            iconBg = 'rgba(255, 193, 7, 0.2)',
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            confirmClass = 'btn-glass-primary',
            onConfirm = () => {},
            onCancel = () => {}
        } = options;

        return this.show({
            title,
            message,
            icon,
            iconColor,
            iconBg,
            confirmText,
            cancelText,
            confirmClass,
            type: 'confirm',
            onConfirm,
            onCancel
        });
    }
}

    // Hacer disponible globalmente
    window.GlassModal = GlassModal;
})();
