/**
 * Vista Detalles Usuario - JavaScript Interactivo
 * Modales modernos, animaciones y efectos
 * Versión: 2.0
 */

class UserShowManager {
    constructor() {
        this.userId = null;
        this.currentModal = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.addHoverEffects();
        console.log('✅ User Show Manager initialized');
    }

    setupEventListeners() {
        // Toggle status button
        const toggleBtn = document.querySelector('[data-action="toggle-status"]');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = toggleBtn.dataset.userId;
                const isActive = toggleBtn.dataset.active === 'true';
                this.showToggleModal(userId, isActive);
            });
        }

        // View referidos button
        document.querySelectorAll('[data-action="view-referido"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = btn.dataset.userId;
                this.viewReferido(userId);
            });
        });

        // Stats cards click
        document.querySelectorAll('.user-stat-card').forEach(card => {
            card.addEventListener('click', () => {
                const type = card.dataset.statType;
                this.showStatDetail(type);
            });
        });

        // Copy buttons
        document.querySelectorAll('[data-action="copy"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const text = btn.dataset.copyText;
                this.copyToClipboard(text);
            });
        });

        // ESC to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.currentModal) {
                this.closeModal();
            }
        });
    }

    showToggleModal(userId, isActive) {
        const action = isActive ? 'desactivar' : 'activar';
        const actionCap = isActive ? 'Desactivar' : 'Activar';
        const type = isActive ? 'warning' : 'success';
        const icon = isActive ? 'pause-circle' : 'play-circle';

        const modal = this.createModal({
            title: `${actionCap} Usuario`,
            icon: icon,
            type: type,
            message: `¿Estás seguro que deseas ${action} este usuario?`,
            confirmText: actionCap,
            onConfirm: () => {
                const form = document.querySelector(`[data-user-id="${userId}"]`);
                const formClone = form.cloneNode(true);
                document.body.appendChild(formClone);
                formClone.submit();
            }
        });

        this.showModal(modal);
    }

    showStatDetail(type) {
        const titles = {
            'pedidos-cliente': 'Pedidos como Cliente',
            'pedidos-vendedor': 'Pedidos como Vendedor',
            'total-vendido': 'Total Vendido',
            'comisiones': 'Comisiones Totales'
        };

        this.showInfo(`Detalle: ${titles[type] || type}`, 'Esta funcionalidad mostrará detalles específicos.');
    }

    viewReferido(userId) {
        window.location.href = `/admin/users/${userId}`;
    }

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showToast('Copiado al portapapeles', 'success');
        }).catch(() => {
            this.showToast('Error al copiar', 'error');
        });
    }

    createModal(options) {
        const {
            title = 'Confirmación',
            icon = 'question-circle',
            type = 'info',
            message = '¿Estás seguro?',
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            onConfirm = () => {},
            onCancel = () => {}
        } = options;

        const colors = {
            success: { bg: 'rgba(16, 185, 129, 0.1)', color: '#10b981' },
            warning: { bg: 'rgba(245, 158, 11, 0.1)', color: '#f59e0b' },
            danger: { bg: 'rgba(239, 68, 68, 0.1)', color: '#ef4444' },
            info: { bg: 'rgba(59, 130, 246, 0.1)', color: '#3b82f6' }
        };

        const color = colors[type] || colors.info;

        const modalHTML = `
            <div class="user-modal-backdrop"></div>
            <div class="user-modal">
                <div class="user-modal-content">
                    <div class="user-modal-header">
                        <div class="user-modal-title">
                            <div class="user-stat-icon" style="background: ${color.bg}; color: ${color.color}; width: 48px; height: 48px;">
                                <i class="bi bi-${icon}"></i>
                            </div>
                            <span>${title}</span>
                        </div>
                        <button class="user-modal-close" data-action="cancel">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="user-modal-body">
                        ${message}
                    </div>
                    <div class="user-modal-footer">
                        <button class="user-action-btn user-action-btn-secondary" data-action="cancel" style="width: auto; margin: 0;">
                            ${cancelText}
                        </button>
                        <button class="user-action-btn user-action-btn-${type}" data-action="confirm" style="width: auto; margin: 0;">
                            <i class="bi bi-${icon}"></i>
                            ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = modalHTML;

        // Event listeners
        container.querySelectorAll('[data-action="confirm"]').forEach(btn => {
            btn.addEventListener('click', () => {
                onConfirm();
                this.closeModal();
            });
        });

        container.querySelectorAll('[data-action="cancel"]').forEach(btn => {
            btn.addEventListener('click', () => {
                onCancel();
                this.closeModal();
            });
        });

        container.querySelector('.user-modal-backdrop').addEventListener('click', () => {
            onCancel();
            this.closeModal();
        });

        return container;
    }

    showModal(modalContainer) {
        this.closeModal();

        document.body.appendChild(modalContainer);
        this.currentModal = modalContainer;

        requestAnimationFrame(() => {
            modalContainer.querySelector('.user-modal-backdrop').classList.add('active');
            modalContainer.querySelector('.user-modal').classList.add('active');
        });
    }

    closeModal() {
        if (!this.currentModal) return;

        const backdrop = this.currentModal.querySelector('.user-modal-backdrop');
        const modal = this.currentModal.querySelector('.user-modal');

        if (backdrop) backdrop.classList.remove('active');
        if (modal) modal.classList.remove('active');

        setTimeout(() => {
            if (this.currentModal) {
                this.currentModal.remove();
                this.currentModal = null;
            }
        }, 300);
    }

    showInfo(title, message) {
        const modal = this.createModal({
            title: title,
            icon: 'info-circle',
            type: 'info',
            message: message,
            confirmText: 'Entendido',
            onConfirm: () => {}
        });

        // Remove cancel button for info modals
        const cancelBtn = modal.querySelector('[data-action="cancel"]');
        if (cancelBtn) cancelBtn.remove();

        this.showModal(modal);
    }

    showToast(message, type = 'info') {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${colors[type]};
            color: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            font-weight: 500;
        `;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    addHoverEffects() {
        // Add hover effects to stats cards
        document.querySelectorAll('.user-stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.userShowManager = new UserShowManager();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
