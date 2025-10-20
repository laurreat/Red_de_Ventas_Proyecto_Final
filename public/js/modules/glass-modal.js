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
            
            // Asegurar que el backdrop esté debajo del modal
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '1040';
                }
                modal.style.zIndex = '1055';
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
            <div class="modal fade" id="glassConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered" style="z-index: 1056;">
                    <div class="modal-content glass-modal" style="background: rgba(255, 255, 255, 0.98) !important; border-radius: 20px !important; overflow: visible !important; z-index: 1057 !important;">
                        <div class="modal-glass-bg" style="pointer-events: none; z-index: 0;"></div>
                        <div class="modal-body text-center" style="padding: 3rem 2rem; position: relative; z-index: 10;">
                            <div class="confirm-icon-wrapper mx-auto mb-3" 
                                 style="width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid; background: ${iconBg}; border-color: ${iconColor}40; position: relative; z-index: 10;">
                                <i class="${icon}" style="font-size: 3rem; color: ${iconColor};"></i>
                            </div>
                            <h3 class="confirm-title mb-3" style="font-weight: 700; color: #2c2c2c; font-size: 1.75rem; position: relative; z-index: 10;">${title}</h3>
                            <p class="confirm-message mb-4" style="color: #6c757d; font-size: 1.1rem; line-height: 1.6; position: relative; z-index: 10;">${message}</p>
                            <div class="d-flex gap-2 justify-content-center" style="position: relative; z-index: 10;">
                                ${showCancel ? `
                                    <button type="button" class="btn-glass btn-cancel btn-glass-secondary" style="position: relative; z-index: 10; pointer-events: auto;">
                                        <i class="bi bi-x-circle me-1"></i>
                                        ${cancelText}
                                    </button>
                                ` : ''}
                                <button type="button" class="btn-glass btn-confirm ${confirmClass}" style="position: relative; z-index: 10; pointer-events: auto;">
                                    <i class="bi bi-check-circle me-1"></i>
                                    ${confirmText}
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
