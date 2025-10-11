/**
 * ============================================
 * Módulo Gestión de Usuarios - JavaScript
 * Funcionalidad: CRUD, Modales, Alertas únicas, Validaciones
 * Optimizado: Performance, PWA, Seguridad
 * Versión: 2.0
 * ============================================
 */

class UsersManagement {
    constructor() {
        this.config = {
            confirmationDelay: 300,
            animationDuration: 300,
            searchDebounceTime: 500,
        };

        this.state = {
            currentModal: null,
            isProcessing: false,
            searchTimeout: null,
        };

        this.init();
    }

    /**
     * Inicializar módulo
     */
    init() {
        this.setupEventListeners();
        this.initSearchDebounce();
        this.initTableInteractions();
        this.checkPWA();
        console.log('✅ Users Management Module initialized');
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Event delegation para botones de acciones
        document.addEventListener('click', (e) => {
            const toggleBtn = e.target.closest('[data-action="toggle"]');
            const deleteBtn = e.target.closest('[data-action="delete"]');

            if (toggleBtn) {
                e.preventDefault();
                const userId = toggleBtn.dataset.userId;
                if (userId) {
                    this.toggleUserStatus(userId);
                }
            }

            if (deleteBtn) {
                e.preventDefault();
                const userId = deleteBtn.dataset.userId;
                const userName = deleteBtn.dataset.userName;
                if (userId && userName) {
                    this.deleteUser(userId, userName);
                }
            }

            // Click fuera del modal para cerrar
            if (e.target.classList.contains('user-modal-backdrop')) {
                this.closeModal();
            }
        });

        // Prevenir envíos duplicados de formularios
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (this.state.isProcessing) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Cerrar modales con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.state.currentModal) {
                this.closeModal();
            }
        });
    }

    /**
     * Search con debounce
     */
    initSearchDebounce() {
        const searchInput = document.querySelector('input[name="search"]');
        if (!searchInput) return;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(this.state.searchTimeout);

            this.state.searchTimeout = setTimeout(() => {
                // Auto-submit del form de búsqueda
                if (e.target.value.length >= 2 || e.target.value.length === 0) {
                    e.target.closest('form')?.submit();
                }
            }, this.config.searchDebounceTime);
        });
    }

    /**
     * Interacciones de tabla
     */
    initTableInteractions() {
        // Hover effects ya manejados por CSS
        // Aquí podríamos agregar más interacciones si es necesario
    }

    /**
     * Toggle user status - FUNCIÓN ÚNICA
     */
    async toggleUserStatus(userId) {
        if (this.state.isProcessing) return;

        try {
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (!userRow) {
                throw new Error('Usuario no encontrado');
            }

            const userName = userRow.querySelector('.user-name')?.textContent?.trim() || 'este usuario';
            const statusBadge = userRow.querySelector('.user-badge.status-active, .user-badge.status-inactive');
            const currentStatus = statusBadge?.classList.contains('status-active');

            const action = currentStatus ? 'desactivar' : 'activar';
            const actionCapital = currentStatus ? 'Desactivar' : 'Activar';

            // IMPORTANTE: Buscar formulario ANTES de mostrar loading
            // El showProcessing() reemplaza el innerHTML y elimina los forms
            const actionsCell = userRow.querySelector('.user-actions');
            let form = null;

            if (actionsCell) {
                form = actionsCell.querySelector(`.user-toggle-form[data-user-id="${userId}"]`);

                if (!form) {
                    form = actionsCell.querySelector('.user-toggle-form');
                }
            }

            if (!form) {
                form = userRow.querySelector(`.user-toggle-form[data-user-id="${userId}"]`);
            }

            if (!form) {
                form = userRow.querySelector('.user-toggle-form');
            }

            if (!form) {
                console.error('🔍 DEBUG TOGGLE FORM:');
                console.error('User ID:', userId);
                console.error('User Row:', userRow);
                console.error('Actions Cell:', actionsCell);
                console.error('Forms en row:', userRow.querySelectorAll('form'));
                console.error('Toggle forms en row:', userRow.querySelectorAll('.user-toggle-form'));
                console.error('HTML de actions cell:', actionsCell ? actionsCell.innerHTML : 'No actions cell');
                throw new Error('Formulario de toggle no encontrado');
            }

            console.log('✅ Formulario de toggle encontrado:', form);

            // Mostrar modal de confirmación
            const confirmed = await this.showConfirmModal({
                title: `${actionCapital} Usuario`,
                message: `¿Estás seguro que deseas ${action} a <strong>${userName}</strong>?`,
                type: 'warning',
                confirmText: actionCapital,
                confirmClass: currentStatus ? 'btn-user-danger' : 'btn-user-primary'
            });

            if (!confirmed) return;

            // Marcar como procesando
            this.state.isProcessing = true;

            // IMPORTANTE: Clonar el form y agregarlo al body antes de mostrar loading
            // Esto asegura que el form esté conectado al DOM cuando se haga submit
            const formClone = form.cloneNode(true);
            document.body.appendChild(formClone);

            // Mostrar loading
            this.showProcessing(userRow);

            // Submit del formulario clonado (está conectado al DOM)
            formClone.submit();

        } catch (error) {
            console.error('Error toggling user status:', error);
            this.showAlert('Error al cambiar estado del usuario', 'error');
            this.state.isProcessing = false;
        }
    }

    /**
     * Delete user - FUNCIÓN ÚNICA
     */
    async deleteUser(userId, userName) {
        if (this.state.isProcessing) return;

        try {
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (!userRow) {
                console.error('User ID buscado:', userId);
                console.error('No se encontró la fila del usuario');
                throw new Error('Fila de usuario no encontrada');
            }

            // IMPORTANTE: Buscar formulario ANTES de mostrar loading
            // El showProcessing() reemplaza el innerHTML y elimina los forms
            const actionsCell = userRow.querySelector('.user-actions');
            let form = null;

            if (actionsCell) {
                form = actionsCell.querySelector(`.user-delete-form[data-user-id="${userId}"]`);

                if (!form) {
                    form = actionsCell.querySelector('.user-delete-form');
                }
            }

            if (!form) {
                form = userRow.querySelector(`.user-delete-form[data-user-id="${userId}"]`);
            }

            if (!form) {
                form = userRow.querySelector('.user-delete-form');
            }

            if (!form) {
                console.error('🔍 DEBUG DELETE FORM:');
                console.error('User ID:', userId);
                console.error('User Row:', userRow);
                console.error('Actions Cell:', actionsCell);
                console.error('Forms en actions cell:', actionsCell ? actionsCell.querySelectorAll('form') : 'No actions cell');
                console.error('Forms en row:', userRow.querySelectorAll('form'));
                console.error('Delete forms en row:', userRow.querySelectorAll('.user-delete-form'));
                console.error('HTML de actions cell:', actionsCell ? actionsCell.innerHTML : 'No actions cell');
                throw new Error('Formulario de delete no encontrado');
            }

            console.log('✅ Formulario de delete encontrado:', form);

            // Mostrar modal de confirmación
            const confirmed = await this.showConfirmModal({
                title: 'Eliminar Usuario',
                message: `
                    <p>¿Estás seguro que deseas eliminar a <strong>${userName}</strong>?</p>
                    <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle"></i> Esta acción no se puede deshacer.</p>
                `,
                type: 'danger',
                confirmText: 'Eliminar',
                confirmClass: 'btn-user-danger'
            });

            if (!confirmed) return;

            // Marcar como procesando
            this.state.isProcessing = true;

            // IMPORTANTE: Clonar el form y agregarlo al body antes de mostrar loading
            // Esto asegura que el form esté conectado al DOM cuando se haga submit
            const formClone = form.cloneNode(true);
            document.body.appendChild(formClone);

            // Mostrar loading
            this.showProcessing(userRow);

            // Submit del formulario clonado (está conectado al DOM)
            formClone.submit();

        } catch (error) {
            console.error('Error deleting user:', error);
            this.showAlert('Error al eliminar usuario', 'error');
            this.state.isProcessing = false;
        }
    }

    /**
     * Mostrar modal de confirmación - SISTEMA ÚNICO
     */
    showConfirmModal(options) {
        return new Promise((resolve) => {
            // Eliminar modales existentes
            this.removeExistingModals();

            const {
                title = 'Confirmación',
                message = '¿Estás seguro?',
                type = 'warning',
                confirmText = 'Confirmar',
                cancelText = 'Cancelar',
                confirmClass = 'btn-user-primary'
            } = options;

            // Crear modal
            const modalHTML = `
                <div class="user-modal-backdrop active"></div>
                <div class="user-modal active ${type}">
                    <div class="user-modal-content">
                        <div class="user-modal-header">
                            <h5 class="user-modal-title">
                                <i class="bi bi-${type === 'danger' ? 'exclamation-triangle' : 'question-circle'}"></i>
                                ${title}
                            </h5>
                            <button type="button" class="user-modal-close" data-action="cancel">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="user-modal-body">
                            ${message}
                        </div>
                        <div class="user-modal-footer">
                            <button type="button" class="btn-user-secondary" data-action="cancel">
                                ${cancelText}
                            </button>
                            <button type="button" class="${confirmClass}" data-action="confirm">
                                ${confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Agregar al DOM
            const container = document.createElement('div');
            container.id = 'user-confirm-modal-container';
            container.innerHTML = modalHTML;
            document.body.appendChild(container);

            this.state.currentModal = container;

            // Event listeners
            const confirmBtn = container.querySelector('[data-action="confirm"]');
            const cancelBtn = container.querySelectorAll('[data-action="cancel"]');
            const backdrop = container.querySelector('.user-modal-backdrop');

            const cleanup = (result) => {
                this.closeModal();
                setTimeout(() => resolve(result), this.config.confirmationDelay);
            };

            confirmBtn.addEventListener('click', () => cleanup(true));
            cancelBtn.forEach(btn => btn.addEventListener('click', () => cleanup(false)));
            backdrop.addEventListener('click', () => cleanup(false));

            // Agregar clase al body
            document.body.classList.add('modal-open');
        });
    }

    /**
     * Cerrar modal actual
     */
    closeModal() {
        if (!this.state.currentModal) return;

        const backdrop = this.state.currentModal.querySelector('.user-modal-backdrop');
        const modal = this.state.currentModal.querySelector('.user-modal');

        if (backdrop) backdrop.classList.remove('active');
        if (modal) modal.classList.remove('active');

        setTimeout(() => {
            if (this.state.currentModal) {
                this.state.currentModal.remove();
                this.state.currentModal = null;
            }
            document.body.classList.remove('modal-open');
        }, this.config.animationDuration);
    }

    /**
     * Eliminar modales existentes
     */
    removeExistingModals() {
        // Remover modales del sistema de usuarios
        document.querySelectorAll('#user-confirm-modal-container').forEach(el => el.remove());

        // Remover modales de Bootstrap si existen
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.querySelectorAll('.modal.show').forEach(el => {
            el.classList.remove('show');
            el.style.display = 'none';
        });

        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    /**
     * Mostrar processing state en una fila
     */
    showProcessing(row) {
        const actionsCell = row.querySelector('.user-actions');
        if (!actionsCell) return;

        const originalContent = actionsCell.innerHTML;
        actionsCell.innerHTML = `
            <div class="user-loading"></div>
        `;
        actionsCell.dataset.originalContent = originalContent;
    }

    /**
     * Mostrar alerta - SISTEMA ÚNICO
     */
    showAlert(message, type = 'info') {
        // Usar el sistema de alertas global si existe
        if (window.adminAlerts) {
            const methods = {
                success: 'showSuccess',
                error: 'showError',
                warning: 'showWarning',
                info: 'showInfo'
            };

            const method = methods[type] || 'showInfo';
            window.adminAlerts[method]('', message);
        } else {
            // Fallback a console
            console.log(`[${type.toUpperCase()}] ${message}`);

            // Toast simple si no hay sistema de alertas
            this.showToast(message, type);
        }
    }

    /**
     * Toast simple fallback
     */
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
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            font-size: 0.875rem;
            animation: slideInRight 0.3s ease-out;
        `;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /**
     * Verificar PWA
     */
    checkPWA() {
        if ('serviceWorker' in navigator) {
            // PWA compatible
            console.log('✅ PWA Ready - Service Worker supported');
        }

        // Detectar si está instalado como PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('✅ Running as installed PWA');
        }
    }

    /**
     * Validar formulario de usuario
     */
    validateUserForm(form) {
        const errors = [];

        // Validar nombre
        const nombre = form.querySelector('[name="name"]');
        if (nombre && nombre.value.trim().length < 2) {
            errors.push('El nombre debe tener al menos 2 caracteres');
        }

        // Validar email
        const email = form.querySelector('[name="email"]');
        if (email && !this.isValidEmail(email.value)) {
            errors.push('El email no es válido');
        }

        // Validar cédula
        const cedula = form.querySelector('[name="cedula"]');
        if (cedula && !/^\d+$/.test(cedula.value)) {
            errors.push('La cédula solo debe contener números');
        }

        // Validar password si se está cambiando
        const password = form.querySelector('[name="password"]');
        if (password && password.value && password.value.length < 8) {
            errors.push('La contraseña debe tener al menos 8 caracteres');
        }

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Validar email
     */
    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Formatear datos para envío seguro
     */
    sanitizeFormData(formData) {
        const sanitized = {};

        for (const [key, value] of formData.entries()) {
            // Limpiar espacios
            let cleanValue = typeof value === 'string' ? value.trim() : value;

            // Escapar HTML básico
            if (typeof cleanValue === 'string') {
                cleanValue = cleanValue
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            sanitized[key] = cleanValue;
        }

        return sanitized;
    }

    /**
     * Performance monitoring
     */
    measurePerformance(label, callback) {
        const start = performance.now();
        const result = callback();
        const end = performance.now();

        console.log(`⚡ ${label}: ${(end - start).toFixed(2)}ms`);

        return result;
    }
}

/**
 * Funciones globales para compatibilidad con HTML inline (DEPRECADAS - usar data attributes)
 * Mantenidas para retrocompatibilidad
 */
window.toggleUserStatus = function(userId) {
    console.warn('toggleUserStatus() está deprecada. Usar data-action="toggle" en su lugar');
    if (window.usersManagement) {
        window.usersManagement.toggleUserStatus(userId);
    }
};

window.deleteUser = function(userId, userName) {
    console.warn('deleteUser() está deprecada. Usar data-action="delete" en su lugar');
    if (window.usersManagement) {
        window.usersManagement.deleteUser(userId, userName);
    }
};

/**
 * Inicializar cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', () => {
    window.usersManagement = new UsersManagement();
});

/**
 * Cleanup al salir de la página
 */
window.addEventListener('beforeunload', () => {
    if (window.usersManagement) {
        window.usersManagement.removeExistingModals();
    }
});
