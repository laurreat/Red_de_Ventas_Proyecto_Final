/**
 * Vista Editar Usuario - JavaScript Interactivo
 * Modales modernos, validaciones y efectos profesionales
 * Versión: 2.0
 */

class UserEditManager {
    constructor() {
        this.currentModal = null;
        this.formData = {};
        this.hasUnsavedChanges = false;
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupPasswordToggle();
        this.setupPasswordStrength();
        this.setupUnsavedChangesDetection();
        this.setupFormSubmit();
        this.setupInputAnimations();
        console.log('✅ User Edit Manager initialized');
    }

    /* ========================================
       FORM VALIDATION
    ======================================== */

    setupFormValidation() {
        const form = document.querySelector('#editUserForm');
        if (!form) return;

        // Real-time validation for required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', () => {
                this.validateField(field);
            });

            field.addEventListener('input', () => {
                if (field.classList.contains('is-invalid')) {
                    this.validateField(field);
                }
            });
        });

        // Email validation
        const emailField = form.querySelector('#email');
        if (emailField) {
            emailField.addEventListener('blur', () => {
                this.validateEmail(emailField);
            });
        }

        // Phone validation
        const phoneField = form.querySelector('#telefono');
        if (phoneField) {
            phoneField.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9+\-\s()]/g, '');
            });
        }

        // Cedula validation (only numbers)
        const cedulaField = form.querySelector('#cedula');
        if (cedulaField) {
            cedulaField.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }

        // Number fields validation
        const numberFields = form.querySelectorAll('input[type="number"]');
        numberFields.forEach(field => {
            field.addEventListener('input', (e) => {
                if (parseFloat(e.target.value) < 0) {
                    e.target.value = 0;
                }
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const isValid = value.length > 0;

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            return false;
        }
    }

    validateEmail(field) {
        const email = field.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(email);

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        } else if (email.length > 0) {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            return false;
        }
        return true;
    }

    /* ========================================
       PASSWORD FUNCTIONALITY
    ======================================== */

    setupPasswordToggle() {
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        const toggleIcon = document.querySelector('#togglePasswordIcon');

        if (togglePassword && passwordField && toggleIcon) {
            togglePassword.addEventListener('click', () => {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;

                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }

        // Toggle password confirmation visibility
        const toggleConfirmation = document.querySelector('#togglePasswordConfirmation');
        const confirmationField = document.querySelector('#password_confirmation');
        const toggleConfirmationIcon = document.querySelector('#togglePasswordConfirmationIcon');

        if (toggleConfirmation && confirmationField && toggleConfirmationIcon) {
            toggleConfirmation.addEventListener('click', () => {
                const type = confirmationField.type === 'password' ? 'text' : 'password';
                confirmationField.type = type;

                toggleConfirmationIcon.classList.toggle('bi-eye');
                toggleConfirmationIcon.classList.toggle('bi-eye-slash');
            });
        }
    }

    setupPasswordStrength() {
        const passwordField = document.querySelector('#password');
        if (!passwordField) return;

        // Create strength indicator if it doesn't exist
        let strengthIndicator = passwordField.parentElement.parentElement.querySelector('.password-strength');
        if (!strengthIndicator) {
            strengthIndicator = document.createElement('div');
            strengthIndicator.className = 'password-strength';
            strengthIndicator.innerHTML = `
                <div class="password-strength-bar">
                    <div class="password-strength-fill"></div>
                </div>
                <div class="password-strength-text"></div>
            `;
            passwordField.parentElement.parentElement.appendChild(strengthIndicator);
        }

        const strengthBar = strengthIndicator.querySelector('.password-strength-fill');
        const strengthText = strengthIndicator.querySelector('.password-strength-text');

        passwordField.addEventListener('input', (e) => {
            const password = e.target.value;

            if (password.length === 0) {
                strengthIndicator.classList.remove('active');
                return;
            }

            strengthIndicator.classList.add('active');

            const strength = this.calculatePasswordStrength(password);

            strengthBar.className = 'password-strength-fill';
            strengthText.className = 'password-strength-text';

            if (strength.score < 40) {
                strengthBar.classList.add('weak');
                strengthText.classList.add('weak');
                strengthText.textContent = 'Contraseña débil';
            } else if (strength.score < 70) {
                strengthBar.classList.add('medium');
                strengthText.classList.add('medium');
                strengthText.textContent = 'Contraseña media';
            } else {
                strengthBar.classList.add('strong');
                strengthText.classList.add('strong');
                strengthText.textContent = 'Contraseña fuerte';
            }
        });

        // Password confirmation match
        const confirmationField = document.querySelector('#password_confirmation');
        if (confirmationField) {
            const validateMatch = () => {
                if (confirmationField.value.length > 0) {
                    if (passwordField.value === confirmationField.value) {
                        confirmationField.classList.remove('is-invalid');
                        confirmationField.classList.add('is-valid');
                    } else {
                        confirmationField.classList.remove('is-valid');
                        confirmationField.classList.add('is-invalid');
                    }
                }
            };

            passwordField.addEventListener('input', validateMatch);
            confirmationField.addEventListener('input', validateMatch);
        }
    }

    calculatePasswordStrength(password) {
        let score = 0;

        // Length
        if (password.length >= 8) score += 25;
        if (password.length >= 12) score += 15;

        // Complexity
        if (/[a-z]/.test(password)) score += 15;
        if (/[A-Z]/.test(password)) score += 15;
        if (/[0-9]/.test(password)) score += 15;
        if (/[^a-zA-Z0-9]/.test(password)) score += 15;

        return { score: Math.min(score, 100) };
    }

    /* ========================================
       FORM SUBMISSION
    ======================================== */

    setupFormSubmit() {
        const form = document.querySelector('#editUserForm');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            // Validate all required fields
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            // Validate email
            const emailField = form.querySelector('#email');
            if (emailField && !this.validateEmail(emailField)) {
                isValid = false;
            }

            // Validate password match if password is being changed
            const passwordField = form.querySelector('#password');
            const confirmationField = form.querySelector('#password_confirmation');

            if (passwordField && passwordField.value.length > 0) {
                if (passwordField.value !== confirmationField.value) {
                    confirmationField.classList.add('is-invalid');
                    isValid = false;
                    this.showToast('Las contraseñas no coinciden', 'error');
                }

                if (passwordField.value.length < 8) {
                    passwordField.classList.add('is-invalid');
                    isValid = false;
                    this.showToast('La contraseña debe tener al menos 8 caracteres', 'error');
                }
            }

            if (!isValid) {
                this.showToast('Por favor, corrige los errores en el formulario', 'error');

                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return;
            }

            // Show confirmation modal
            this.showConfirmationModal(form);
        });
    }

    showConfirmationModal(form) {
        const userName = document.querySelector('#name').value + ' ' + document.querySelector('#apellidos').value;

        const modal = this.createModal({
            title: 'Actualizar Usuario',
            icon: 'pencil-square',
            type: 'primary',
            message: `¿Estás seguro que deseas actualizar la información de <strong>${userName}</strong>? Los cambios se aplicarán inmediatamente al sistema.`,
            confirmText: 'Sí, actualizar',
            cancelText: 'Cancelar',
            onConfirm: () => {
                this.submitForm(form);
            }
        });

        this.showModal(modal);
    }

    submitForm(form) {
        // Show loading overlay
        this.showLoading();

        // Mark as saved to prevent unsaved changes warning
        this.hasUnsavedChanges = false;

        // Submit the form
        form.submit();
    }

    /* ========================================
       UNSAVED CHANGES DETECTION
    ======================================== */

    setupUnsavedChangesDetection() {
        const form = document.querySelector('#editUserForm');
        if (!form) return;

        // Store initial form data
        const formData = new FormData(form);
        this.formData = {};
        for (let [key, value] of formData.entries()) {
            this.formData[key] = value;
        }

        // Detect changes
        form.addEventListener('input', () => {
            this.hasUnsavedChanges = true;
        });

        // Warn before leaving
        window.addEventListener('beforeunload', (e) => {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Warn before navigation
        const links = document.querySelectorAll('a:not([href^="#"])');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (this.hasUnsavedChanges && !link.hasAttribute('data-ignore-changes')) {
                    e.preventDefault();

                    const modal = this.createModal({
                        title: 'Cambios sin guardar',
                        icon: 'exclamation-triangle',
                        type: 'warning',
                        message: 'Tienes cambios sin guardar. ¿Estás seguro que deseas salir sin guardar?',
                        confirmText: 'Sí, salir',
                        cancelText: 'Continuar editando',
                        onConfirm: () => {
                            this.hasUnsavedChanges = false;
                            window.location.href = link.href;
                        }
                    });

                    this.showModal(modal);
                }
            });
        });
    }

    /* ========================================
       INPUT ANIMATIONS
    ======================================== */

    setupInputAnimations() {
        const inputs = document.querySelectorAll('.form-control, .form-select');

        inputs.forEach(input => {
            // Focus animations
            input.addEventListener('focus', (e) => {
                const card = e.target.closest('.user-edit-card');
                if (card) {
                    card.style.transform = 'scale(1.01)';
                }
            });

            input.addEventListener('blur', (e) => {
                const card = e.target.closest('.user-edit-card');
                if (card) {
                    card.style.transform = 'scale(1)';
                }
            });
        });
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
            <div class="user-modal-backdrop"></div>
            <div class="user-modal">
                <div class="user-modal-content">
                    <div class="user-modal-header">
                        <div class="user-modal-title">
                            <div class="user-modal-icon" style="background: ${color.bg}; color: ${color.color};">
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
                        <button class="user-modal-btn user-modal-btn-secondary" data-action="cancel">
                            ${cancelText}
                        </button>
                        <button class="user-modal-btn user-modal-btn-${type}" data-action="confirm">
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

    /* ========================================
       TOAST NOTIFICATIONS
    ======================================== */

    showToast(message, type = 'info') {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };

        const toast = document.createElement('div');
        toast.className = `user-toast ${type}`;
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
        let overlay = document.querySelector('.loading-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(overlay);
        }

        overlay.classList.add('active');
    }

    hideLoading() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.userEditManager = new UserEditManager();
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
