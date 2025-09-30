/**
 * Módulo para la página de login
 * Separado para mejor organización del código
 */

/**
 * Variable para controlar si hay errores
 */
let hasErrors = false;

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
 * Configurar animaciones de entrada
 */
function setupAnimations() {
    const loginContent = document.querySelector('.login-content');
    const brandContent = document.querySelector('.brand-content');

    // Animación de entrada
    if (loginContent && brandContent) {
        loginContent.style.opacity = '0';
        loginContent.style.transform = 'translateX(30px)';
        brandContent.style.opacity = '0';
        brandContent.style.transform = 'translateX(-30px)';

        setTimeout(() => {
            loginContent.style.transition = 'all 0.8s ease';
            brandContent.style.transition = 'all 0.8s ease';
            loginContent.style.opacity = '1';
            loginContent.style.transform = 'translateX(0)';
            brandContent.style.opacity = '1';
            brandContent.style.transform = 'translateX(0)';
        }, 100);
    }
}

/**
 * Configurar validación del formulario
 */
function setupFormValidation() {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (typeof showLoading === 'function') {
                showLoading();
            }
        });
    }
}

/**
 * Inicializar el módulo de login
 */
function initializeLoginModule() {
    // Ocultar loading si hay errores
    if (hasErrors && typeof hideLoading === 'function') {
        hideLoading();
    }

    // Configurar animaciones
    setupAnimations();

    // Configurar validación del formulario
    setupFormValidation();

    console.log('✅ Módulo de login inicializado');
}

/**
 * Configurar variables globales
 */
function setLoginErrors(errors) {
    hasErrors = errors;
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeLoginModule);

// Exponer funciones globalmente
window.togglePassword = togglePassword;
window.setLoginErrors = setLoginErrors;