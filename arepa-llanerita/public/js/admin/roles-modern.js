/**
 * Gestión de Roles y Permisos - JavaScript Interactivo
 * Modales profesionales, animaciones y efectos modernos
 * Versión: 2.0
 */

class RolesManager {
    constructor() {
        this.currentModal = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupTableAnimations();
        console.log('✅ Roles Manager initialized');
    }

    /* ========================================
       EVENT LISTENERS
    ======================================== */

    setupEventListeners() {
        // Toggle status buttons
        document.querySelectorAll('[data-action="toggle-status"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const roleId = btn.dataset.roleId;
                const isActive = btn.dataset.active === 'true';
                this.showToggleModal(roleId, isActive);
            });
        });

        // Delete buttons
        document.querySelectorAll('[data-action="delete-role"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const roleId = btn.dataset.roleId;
                const roleName = btn.dataset.roleName;
                this.showDeleteModal(roleId, roleName);
            });
        });

        // Initialize role button
        const initBtn = document.querySelector('[data-action="initialize-roles"]');
        if (initBtn) {
            initBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showInitializeModal();
            });
        }

        // View permissions buttons
        document.querySelectorAll('[data-action="view-permissions"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const roleId = btn.dataset.roleId;
                this.showPermissionsDetail(roleId);
            });
        });

        // ESC to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.currentModal) {
                this.closeModal();
            }
        });

        // Stats cards hover
        document.querySelectorAll('.role-stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });
    }

    setupTableAnimations() {
        // Add stagger animation to table rows
        const rows = document.querySelectorAll('.roles-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.animation = `fadeInUp 0.3s ease-out ${index * 0.05}s forwards`;
        });
    }

    /* ========================================
       TOGGLE STATUS
    ======================================== */

    showToggleModal(roleId, isActive) {
        const action = isActive ? 'desactivar' : 'activar';
        const actionCap = isActive ? 'Desactivar' : 'Activar';
        const type = isActive ? 'warning' : 'success';
        const icon = isActive ? 'toggle-off' : 'toggle-on';

        const modal = this.createModal({
            title: `${actionCap} Rol`,
            icon: icon,
            type: type,
            message: `¿Estás seguro que deseas ${action} este rol?<br><small class="text-muted">Los usuarios con este rol ${isActive ? 'perderán acceso al sistema' : 'podrán acceder al sistema'}.</small>`,
            confirmText: actionCap,
            cancelText: 'Cancelar',
            onConfirm: () => {
                this.toggleStatus(roleId);
            }
        });

        this.showModal(modal);
    }

    toggleStatus(roleId) {
        this.showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/roles/${roleId}/toggle`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="PATCH">
        `;

        document.body.appendChild(form);
        form.submit();
    }

    /* ========================================
       DELETE ROLE
    ======================================== */

    showDeleteModal(roleId, roleName) {
        const modal = this.createModal({
            title: 'Eliminar Rol',
            icon: 'trash',
            type: 'danger',
            message: `¿Estás seguro que deseas eliminar el rol <strong>${roleName}</strong>?<br><br><small class="text-muted">Esta acción no se puede deshacer. Los usuarios con este rol deberán ser reasignados a otro rol.</small>`,
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: () => {
                this.deleteRole(roleId);
            }
        });

        this.showModal(modal);
    }

    deleteRole(roleId) {
        this.showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/roles/${roleId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }

    /* ========================================
       INITIALIZE ROLES
    ======================================== */

    showInitializeModal() {
        const modal = this.createModal({
            title: 'Inicializar Roles del Sistema',
            icon: 'arrow-repeat',
            type: 'warning',
            message: `¿Estás seguro de inicializar los roles del sistema?<br><br>
                      <div class="alert alert-warning mt-3 mb-0" style="text-align: left;">
                          <strong>Esta acción:</strong><br>
                          <ul class="mb-0 mt-2">
                              <li>Recreará los roles predeterminados (Administrador, Líder, Vendedor, Cliente)</li>
                              <li>Restaurará los permisos de cada rol</li>
                              <li>No afectará los usuarios existentes</li>
                          </ul>
                      </div>`,
            confirmText: 'Sí, inicializar',
            cancelText: 'Cancelar',
            onConfirm: () => {
                this.initializeRoles();
            }
        });

        this.showModal(modal);
    }

    initializeRoles() {
        this.showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/roles/initialize';
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
        `;

        document.body.appendChild(form);
        form.submit();
    }

    /* ========================================
       VIEW PERMISSIONS DETAIL
    ======================================== */

    showPermissionsDetail(roleId) {
        // This would typically fetch role details via AJAX
        // For now, redirect to show page
        window.location.href = `/admin/roles/${roleId}`;
    }

    /* ========================================
       MODAL SYSTEM
    ======================================== */

    createModal(options) {
        const {
            title = 'Confirmación',
            icon = 'question-circle',
            type = 'primary',
            message = '¿Estás seguro?',
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            onConfirm = () => {},
            onCancel = () => {}
        } = options;

        const colors = {
            primary: { bg: 'rgba(114, 47, 55, 0.1)', color: '#722F37' },
            success: { bg: 'rgba(16, 185, 129, 0.1)', color: '#10b981' },
            warning: { bg: 'rgba(245, 158, 11, 0.1)', color: '#f59e0b' },
            danger: { bg: 'rgba(239, 68, 68, 0.1)', color: '#ef4444' },
            info: { bg: 'rgba(59, 130, 246, 0.1)', color: '#3b82f6' }
        };

        const color = colors[type] || colors.primary;

        const modalHTML = `
            <div class="role-modal-backdrop"></div>
            <div class="role-modal">
                <div class="role-modal-content">
                    <div class="role-modal-header">
                        <div class="role-modal-title">
                            <div class="role-modal-icon" style="background: ${color.bg}; color: ${color.color};">
                                <i class="bi bi-${icon}"></i>
                            </div>
                            <span>${title}</span>
                        </div>
                        <button class="role-modal-close" data-action="cancel">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="role-modal-body">
                        ${message}
                    </div>
                    <div class="role-modal-footer">
                        <button class="role-modal-btn role-modal-btn-secondary" data-action="cancel">
                            ${cancelText}
                        </button>
                        <button class="role-modal-btn role-modal-btn-${type}" data-action="confirm">
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

        container.querySelector('.role-modal-backdrop').addEventListener('click', () => {
            onCancel();
            this.closeModal();
        });

        // ESC key
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                onCancel();
                this.closeModal();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);

        return container;
    }

    showModal(modalContainer) {
        this.closeModal();

        document.body.appendChild(modalContainer);
        this.currentModal = modalContainer;

        requestAnimationFrame(() => {
            modalContainer.querySelector('.role-modal-backdrop').classList.add('active');
            modalContainer.querySelector('.role-modal').classList.add('active');
        });
    }

    closeModal() {
        if (!this.currentModal) return;

        const backdrop = this.currentModal.querySelector('.role-modal-backdrop');
        const modal = this.currentModal.querySelector('.role-modal');

        if (backdrop) backdrop.classList.remove('active');
        if (modal) modal.classList.remove('active');

        setTimeout(() => {
            if (this.currentModal) {
                this.currentModal.remove();
                this.currentModal = null;
            }
        }, 300);
    }

    /* ========================================
       TOAST NOTIFICATIONS
    ======================================== */

    showToast(message, type = 'info') {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };

        const toast = document.createElement('div');
        toast.className = `role-toast ${type}`;
        toast.innerHTML = `
            <i class="bi bi-${icons[type]}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /* ========================================
       LOADING OVERLAY
    ======================================== */

    showLoading() {
        let overlay = document.querySelector('.role-loading-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'role-loading-overlay';
            overlay.innerHTML = '<div class="role-loading-spinner"></div>';
            document.body.appendChild(overlay);
        }

        overlay.classList.add('active');
    }

    hideLoading() {
        const overlay = document.querySelector('.role-loading-overlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
}

/* ========================================
   GLOBAL FUNCTIONS (for inline onclick)
======================================== */

function toggleStatus(roleId) {
    if (window.rolesManager) {
        const btn = document.querySelector(`[data-action="toggle-status"][data-role-id="${roleId}"]`);
        const isActive = btn ? btn.dataset.active === 'true' : false;
        window.rolesManager.showToggleModal(roleId, isActive);
    }
}

function deleteRole(roleId) {
    if (window.rolesManager) {
        const btn = document.querySelector(`[data-action="delete-role"][data-role-id="${roleId}"]`);
        const roleName = btn ? btn.dataset.roleName : 'este rol';
        window.rolesManager.showDeleteModal(roleId, roleName);
    }
}

/* ========================================
   INITIALIZE
======================================== */

document.addEventListener('DOMContentLoaded', () => {
    window.rolesManager = new RolesManager();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);
