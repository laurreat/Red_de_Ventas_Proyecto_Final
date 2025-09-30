/**
 * Módulo para la página de registro
 * Separado para mejor organización del código
 */

/**
 * Función para mostrar/ocultar contraseña
 */
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

/**
 * Validar un campo individual
 */
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Validaciones específicas
    switch(field.type) {
        case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Ingresa un correo electrónico válido';
            }
            break;

        case 'tel':
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(value.replace(/\s+/g, ''))) {
                isValid = false;
                errorMessage = 'Ingresa un teléfono válido (10 dígitos)';
            }
            break;

        case 'text':
            if (field.name === 'cedula') {
                const docRegex = /^[0-9]{6,12}$/;
                if (!docRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Ingresa un documento válido (6-12 dígitos)';
                }
            }
            break;

        case 'password':
            if (value.length < 8) {
                isValid = false;
                errorMessage = 'La contraseña debe tener al menos 8 caracteres';
            }
            break;
    }

    // Validar campos requeridos
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es requerido';
    }

    // Aplicar estilos de validación
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        hideFieldError(field);
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        showFieldError(field, errorMessage);
    }

    return isValid;
}

/**
 * Mostrar error en un campo
 */
function showFieldError(field, message) {
    let errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        field.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

/**
 * Ocultar error de un campo
 */
function hideFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv && !errorDiv.hasAttribute('data-server-error')) {
        errorDiv.remove();
    }
}

/**
 * Configurar validación en tiempo real
 */
function setupRealTimeValidation() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

/**
 * Configurar validación del formulario completo
 */
function setupFormValidation() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        // Validar que las contraseñas coincidan
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');

        if (password.value !== passwordConfirm.value) {
            passwordConfirm.classList.add('is-invalid');
            showFieldError(passwordConfirm, 'Las contraseñas no coinciden');
            isValid = false;
        }

        // Validar términos y condiciones
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            terms.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Usar función de toast disponible o alerta simple
            if (typeof showErrorToast === 'function') {
                showErrorToast('Por favor corrige los errores en el formulario');
            } else {
                alert('Por favor corrige los errores en el formulario');
            }
        }
    });
}

/**
 * Configurar formato de campos numéricos
 */
function setupFieldFormatting() {
    // Formatear teléfono
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substr(0, 10);
            }
            e.target.value = value;
        });
    }

    // Formatear documento
    const cedulaInput = document.getElementById('cedula');
    if (cedulaInput) {
        cedulaInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.substr(0, 12);
            }
            e.target.value = value;
        });
    }
}

/**
 * Función para confirmar registro
 */
function confirmRegister(formId, title = 'Crear Cuenta', message = 'Se creará tu cuenta en el sistema.') {
    console.log('confirmRegister ejecutada para:', formId);

    // Actualizar contenido del modal
    const titleEl = document.getElementById('userSaveTitle');
    const messageEl = document.getElementById('userSaveMessage');
    const saveBtnText = document.getElementById('userSaveBtnText');

    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;
    if (saveBtnText) saveBtnText.textContent = 'Crear Cuenta';

    // Configurar botón de confirmación
    const confirmBtn = document.getElementById('confirmUserSaveBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            const form = document.getElementById(formId);
            if (form) {
                form.submit();
            }
        };
    }

    // Mostrar modal
    const modalElement = document.getElementById('userSaveConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de registro');
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');

        // Crear backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
}

/**
 * Función para cerrar modales
 */
function closeRegisterModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.classList.remove('modal-open');

        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    }
}

/**
 * Configurar modales de confirmación
 */
function setupConfirmationModals() {
    console.log('Inicializando funciones para registro...');

    // Interceptar formularios que necesitan confirmación
    const formsNeedingConfirmation = document.querySelectorAll('form.needs-register-confirmation');

    formsNeedingConfirmation.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = this.dataset.confirmMessage || 'Se creará tu cuenta en el sistema.';
            const formId = this.id || 'registerForm';

            if (!this.id) {
                this.id = formId;
            }

            confirmRegister(formId, 'Crear Cuenta', message);
        });
    });

    // Event listeners para cerrar modales
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) closeRegisterModal(modal.id);
        });
    });

    // Cerrar con backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const openModal = document.querySelector('.modal.show');
            if (openModal) closeRegisterModal(openModal.id);
        }
    });

    console.log('Funciones de registro inicializadas correctamente');
}

/**
 * Inicializar el módulo de registro
 */
function initializeRegisterModule() {
    // Configurar validación en tiempo real
    setupRealTimeValidation();

    // Configurar validación del formulario
    setupFormValidation();

    // Configurar formato de campos
    setupFieldFormatting();

    // Configurar modales (con delay para asegurar que el DOM esté completo)
    setTimeout(setupConfirmationModals, 1000);

    console.log('✅ Módulo de registro inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeRegisterModule);

// Exponer funciones globalmente
window.togglePassword = togglePassword;
window.confirmRegister = confirmRegister;
window.closeRegisterModal = closeRegisterModal;