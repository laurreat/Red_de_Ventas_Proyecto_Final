/**
 * Módulo para la página de restablecimiento de contraseña
 * Separado para mejor organización del código
 */

/**
 * Requisitos de contraseña
 */
const passwordRequirements = {
    length: { element: null, regex: /.{8,}/, weight: 20 },
    lowercase: { element: null, regex: /[a-z]/, weight: 20 },
    uppercase: { element: null, regex: /[A-Z]/, weight: 20 },
    number: { element: null, regex: /[0-9]/, weight: 20 },
    special: { element: null, regex: /[^A-Za-z0-9]/, weight: 20 }
};

/**
 * Elementos del DOM
 */
let elements = {};

/**
 * Inicializar elementos del DOM
 */
function initializeElements() {
    elements = {
        passwordInput: document.getElementById('password'),
        confirmInput: document.getElementById('password-confirm'),
        submitBtn: document.getElementById('submitBtn'),
        form: document.getElementById('resetForm'),
        strengthContainer: document.getElementById('strengthContainer'),
        strengthMeter: document.getElementById('strengthMeter')
    };

    // Inicializar elementos de requisitos
    passwordRequirements.length.element = document.getElementById('length-req');
    passwordRequirements.lowercase.element = document.getElementById('lowercase-req');
    passwordRequirements.uppercase.element = document.getElementById('uppercase-req');
    passwordRequirements.number.element = document.getElementById('number-req');
    passwordRequirements.special.element = document.getElementById('special-req');
}

/**
 * Validar contraseña contra los requisitos
 */
function validatePassword(password) {
    let strengthScore = 0;
    let allValid = true;

    // Verificar cada requisito
    for (let [key, req] of Object.entries(passwordRequirements)) {
        const isValid = req.regex.test(password);

        if (isValid) {
            req.element.classList.add('valid');
            req.element.querySelector('i').className = 'bi bi-check';
            strengthScore += req.weight;
        } else {
            req.element.classList.remove('valid');
            req.element.querySelector('i').className = 'bi bi-x';
            allValid = false;
        }
    }

    // Actualizar barra de fortaleza
    elements.strengthMeter.style.width = strengthScore + '%';

    return allValid;
}

/**
 * Resetear requisitos de contraseña
 */
function resetRequirements() {
    for (let req of Object.values(passwordRequirements)) {
        req.element.classList.remove('valid');
        req.element.querySelector('i').className = 'bi bi-x';
    }
    elements.strengthMeter.style.width = '0%';
}

/**
 * Validar que las contraseñas coincidan
 */
function validatePasswordMatch() {
    const password = elements.passwordInput.value;
    const confirm = elements.confirmInput.value;
    const errorDiv = document.getElementById('password-match-error');

    if (confirm && password !== confirm) {
        elements.confirmInput.classList.add('is-invalid');
        elements.confirmInput.classList.remove('is-valid');
        errorDiv.style.display = 'flex';
        return false;
    } else if (confirm && password === confirm) {
        elements.confirmInput.classList.remove('is-invalid');
        elements.confirmInput.classList.add('is-valid');
        errorDiv.style.display = 'none';
        return true;
    } else {
        elements.confirmInput.classList.remove('is-invalid', 'is-valid');
        errorDiv.style.display = 'none';
        return false;
    }
}

/**
 * Actualizar estado del botón de submit
 */
function updateSubmitButton() {
    const password = elements.passwordInput.value;
    const confirm = elements.confirmInput.value;

    // Verificar que todos los requisitos se cumplan
    const allRequirementsMet = validatePassword(password);

    // Verificar que las contraseñas coincidan
    const passwordsMatch = password && confirm && password === confirm;

    // Habilitar botón si todo está correcto
    if (allRequirementsMet && passwordsMatch) {
        elements.submitBtn.disabled = false;
        elements.passwordInput.classList.add('is-valid');
    } else {
        elements.submitBtn.disabled = true;
        elements.passwordInput.classList.remove('is-valid');
    }
}

/**
 * Configurar event listeners para inputs
 */
function setupEventListeners() {
    // Mostrar/ocultar indicador de fortaleza
    elements.passwordInput.addEventListener('input', function() {
        const password = this.value;

        if (password.length > 0) {
            elements.strengthContainer.style.display = 'block';
            validatePassword(password);
        } else {
            elements.strengthContainer.style.display = 'none';
            resetRequirements();
        }

        updateSubmitButton();
    });

    // Validar coincidencia de contraseñas
    elements.confirmInput.addEventListener('input', function() {
        validatePasswordMatch();
        updateSubmitButton();
    });
}

/**
 * Configurar efectos de focus
 */
function setupFocusEffects() {
    const inputs = document.querySelectorAll('.form-input-modern');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
}

/**
 * Configurar validación del formulario
 */
function setupFormValidation() {
    elements.form.addEventListener('submit', function(e) {
        if (elements.submitBtn.disabled) {
            e.preventDefault();
            if (typeof showToast === 'function') {
                showToast('Por favor completa todos los requisitos de la contraseña', 'error');
            }
            return;
        }

        if (typeof showLoading === 'function') {
            showLoading();
        }
    });
}

/**
 * Configurar animaciones de entrada
 */
function setupAnimations() {
    // Animación de entrada escalonada
    const formGroups = document.querySelectorAll('.form-group-modern');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';

        setTimeout(() => {
            group.style.transition = 'all 0.4s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, (index + 1) * 100);
    });

    // Animación del botón
    setTimeout(() => {
        elements.submitBtn.style.opacity = '0';
        elements.submitBtn.style.transform = 'translateY(20px)';
        elements.submitBtn.style.transition = 'all 0.4s ease';

        setTimeout(() => {
            elements.submitBtn.style.opacity = '1';
            elements.submitBtn.style.transform = 'translateY(0)';
        }, 50);
    }, (formGroups.length + 1) * 100);
}

/**
 * Configurar efectos de hover
 */
function setupHoverEffects() {
    elements.submitBtn.addEventListener('mouseenter', function() {
        if (!this.disabled) {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        }
    });

    elements.submitBtn.addEventListener('mouseleave', function() {
        if (!this.disabled) {
            this.style.transform = 'translateY(0) scale(1)';
        }
    });
}

/**
 * Inicializar el módulo de reset password
 */
function initializeResetPasswordModule() {
    // Inicializar elementos del DOM
    initializeElements();

    // Verificar que los elementos existen
    if (!elements.passwordInput || !elements.confirmInput || !elements.submitBtn) {
        console.error('Elementos requeridos para reset password no encontrados');
        return;
    }

    // Configurar event listeners
    setupEventListeners();

    // Configurar efectos de focus
    setupFocusEffects();

    // Configurar validación del formulario
    setupFormValidation();

    // Configurar animaciones
    setupAnimations();

    // Configurar efectos de hover
    setupHoverEffects();

    console.log('✅ Módulo de reset password inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeResetPasswordModule);