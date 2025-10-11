/**
 * Módulo para confirmaciones de formularios del perfil
 * Separado para mejor organización del código
 */

/**
 * Función para confirmar actualización de información personal
 */
function confirmProfileInfoUpdate(formId, message = 'Los cambios se aplicarán a tu información personal.') {
    console.log('confirmProfileInfoUpdate ejecutada para:', formId);

    // Actualizar contenido del modal
    const messageEl = document.getElementById('profileInfoMessage');
    if (messageEl) messageEl.textContent = message;

    // Configurar botón de confirmación
    const confirmBtn = document.getElementById('confirmProfileInfoBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            const form = document.getElementById(formId);
            if (form) {
                form.submit();
            }
        };
    }

    // Mostrar modal
    const modalElement = document.getElementById('profileInfoConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de actualización de información personal');
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
 * Función para confirmar cambio de contraseña
 */
function confirmPasswordChange(formId, message = 'Esta acción es irreversible. Asegúrate de recordar tu nueva contraseña.') {
    console.log('confirmPasswordChange ejecutada para:', formId);

    // Actualizar contenido del modal
    const messageEl = document.getElementById('passwordChangeMessage');
    if (messageEl) messageEl.textContent = message;

    // Configurar botón de confirmación
    const confirmBtn = document.getElementById('confirmPasswordChangeBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            const form = document.getElementById(formId);
            if (form) {
                form.submit();
            }
        };
    }

    // Mostrar modal
    const modalElement = document.getElementById('passwordChangeConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de cambio de contraseña');
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
 * Función para confirmar actualización de notificaciones
 */
function confirmNotificationsUpdate(formId, message = 'Se aplicarán las nuevas preferencias de notificación.') {
    console.log('confirmNotificationsUpdate ejecutada para:', formId);

    // Actualizar contenido del modal
    const messageEl = document.getElementById('notificationsMessage');
    if (messageEl) messageEl.textContent = message;

    // Configurar botón de confirmación
    const confirmBtn = document.getElementById('confirmNotificationsBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            const form = document.getElementById(formId);
            if (form) {
                form.submit();
            }
        };
    }

    // Mostrar modal
    const modalElement = document.getElementById('notificationsConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de actualización de notificaciones');
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
function closeProfileModal(modalId) {
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
 * Configurar interceptores de formularios
 */
function setupFormInterceptors() {
    // Interceptar formularios que necesitan confirmación
    const formsNeedingConfirmation = document.querySelectorAll('form.needs-profile-confirmation');
    console.log('📋 Formularios encontrados:', formsNeedingConfirmation.length);

    formsNeedingConfirmation.forEach((form, index) => {
        console.log(`📝 Configurando formulario ${index + 1}:`, form.id);

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('🛑 Formulario interceptado:', this.id);

            const message = this.dataset.confirmMessage || 'Los cambios se aplicarán a tu perfil.';
            const formId = this.id;

            // Determinar qué modal mostrar según el formulario
            if (formId.includes('Password')) {
                console.log('🔑 Mostrando modal de contraseña');
                confirmPasswordChange(formId, message);
            } else if (formId.includes('Notifications')) {
                console.log('🔔 Mostrando modal de notificaciones');
                confirmNotificationsUpdate(formId, message);
            } else {
                console.log('👤 Mostrando modal de información personal');
                confirmProfileInfoUpdate(formId, message);
            }
        });
    });
}

/**
 * Configurar event listeners para cerrar modales
 */
function setupModalCloseHandlers() {
    // Event listeners para cerrar modales
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) closeProfileModal(modal.id);
        });
    });

    // Cerrar con backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const openModal = document.querySelector('.modal.show');
            if (openModal) closeProfileModal(openModal.id);
        }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) closeProfileModal(openModal.id);
        }
    });
}

/**
 * Inicializar el módulo de formularios con retardo
 * Para asegurar que se ejecute después de otros scripts
 */
function initializeFormsModule() {
    setTimeout(function() {
        console.log('🔧 Inicializando funciones para perfil con prioridad...');

        setupFormInterceptors();
        setupModalCloseHandlers();

        console.log('✅ Módulo de formularios de perfil inicializado');
    }, 1500); // Ejecutar después de app.js
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeFormsModule);

// Exponer funciones globalmente
window.confirmProfileInfoUpdate = confirmProfileInfoUpdate;
window.confirmPasswordChange = confirmPasswordChange;
window.confirmNotificationsUpdate = confirmNotificationsUpdate;
window.closeProfileModal = closeProfileModal;