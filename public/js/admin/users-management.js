/**
 * Funciones específicas para gestión de usuarios
 * Archivo separado para mantener organización del código
 */

// Definir todas las funciones globalmente de inmediato (sin esperar DOMContentLoaded)

// Función para eliminar usuario
window.deleteUser = function(userId, userName) {
    console.log('deleteUser ejecutada para usuario:', userId);

    // Obtener información del usuario desde la fila de la tabla
    const userRow = document.querySelector(`[data-user-id="${userId}"]`);
    let userEmail = '';
    let userRole = '';

    if (userRow) {
        const emailElement = userRow.querySelector('.user-email');
        const roleElement = userRow.querySelector('.user-role-badge');

        userEmail = emailElement ? emailElement.textContent.trim() : '';
        userRole = roleElement ? roleElement.textContent.trim() : '';
    }

    // Configurar modal dinámicamente
    const deleteHeader = document.getElementById('userDeleteModalHeader');
    const deleteIcon = document.getElementById('userDeleteIcon');
    const deleteIconContainer = document.getElementById('userDeleteIconContainer');
    const deleteTitle = document.getElementById('userDeleteTitle');
    const deleteMessage = document.getElementById('userDeleteMessage');
    const deleteBtn = document.getElementById('confirmUserDeleteBtn');
    const deleteBtnText = document.getElementById('userDeleteBtnText');
    const deleteBtnIcon = document.getElementById('userDeleteBtnIcon');

    // Actualizar información del usuario en el modal
    const userNameEl = document.getElementById('userDeleteName');
    const userEmailEl = document.getElementById('userDeleteEmail');
    const userRoleEl = document.getElementById('userDeleteRole');
    const userAvatar = document.getElementById('userDeleteAvatar');

    if (userNameEl) userNameEl.textContent = userName;
    if (userEmailEl) userEmailEl.textContent = userEmail;
    if (userRoleEl) userRoleEl.textContent = userRole;
    if (userAvatar) userAvatar.textContent = userName.charAt(0).toUpperCase();

    // Configurar colores de advertencia
    if (deleteHeader) deleteHeader.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
    if (deleteIconContainer) deleteIconContainer.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
    if (deleteIcon) deleteIcon.className = 'bi bi-exclamation-triangle-fill text-danger fs-1';
    if (deleteTitle) deleteTitle.textContent = '¿Estás seguro de eliminar este usuario?';
    if (deleteMessage) deleteMessage.innerHTML = 'Esta acción <strong>no se puede deshacer</strong>. El usuario será eliminado permanentemente del sistema.';
    if (deleteBtn) deleteBtn.className = 'btn btn-danger';
    if (deleteBtnIcon) deleteBtnIcon.className = 'bi bi-trash me-1';
    if (deleteBtnText) deleteBtnText.textContent = 'Eliminar Usuario';

    // Configurar botón de confirmación
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            const form = document.getElementById(`user-delete-form-${userId}`);
            if (form) {
                form.submit();
            } else {
                console.error('Formulario de eliminación no encontrado para usuario:', userId);
            }
        };
    }

    // Mostrar modal usando Bootstrap nativo
    const modalElement = document.getElementById('userDeleteConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de eliminación para usuario');

        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            console.error('❌ Bootstrap no está cargado');
            return;
        }

        // Usar Bootstrap Modal API
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        modal.show();
    }
};

// Función para cambiar estado de usuario
window.toggleUserStatus = function(userId) {
    console.log('toggleUserStatus ejecutada para usuario:', userId);

    // Obtener información del usuario desde la fila de la tabla
    const userRow = document.querySelector(`[data-user-id="${userId}"]`);
    let isActive = false;
    let userName = 'Usuario';
    let userEmail = '';
    let userRole = '';

    if (userRow) {
        const statusBadge = userRow.querySelector('.user-status-badge');
        const nameElement = userRow.querySelector('.user-name');
        const emailElement = userRow.querySelector('.user-email');
        const roleElement = userRow.querySelector('.user-role-badge');

        isActive = statusBadge && statusBadge.textContent.trim() === 'Activo';
        userName = nameElement ? nameElement.textContent.trim() : 'Usuario';
        userEmail = emailElement ? emailElement.textContent.trim() : '';
        userRole = roleElement ? roleElement.textContent.trim() : '';
    }

    // Configurar modal dinámicamente
    const statusHeader = document.getElementById('userStatusModalHeader');
    const statusIcon = document.getElementById('userStatusIcon');
    const statusIconContainer = document.getElementById('userStatusIconContainer');
    const statusTitle = document.getElementById('userStatusTitle');
    const statusMessage = document.getElementById('userStatusMessage');
    const statusBtn = document.getElementById('confirmUserStatusBtn');
    const statusBtnText = document.getElementById('userStatusBtnText');
    const statusBtnIcon = document.getElementById('userStatusBtnIcon');

    // Actualizar información del usuario en el modal
    const userNameEl = document.getElementById('userStatusName');
    const userEmailEl = document.getElementById('userStatusEmail');
    const userRoleEl = document.getElementById('userStatusRole');
    const userAvatar = document.getElementById('userAvatar');

    if (userNameEl) userNameEl.textContent = userName;
    if (userEmailEl) userEmailEl.textContent = userEmail;
    if (userRoleEl) userRoleEl.textContent = userRole;
    if (userAvatar) userAvatar.textContent = userName.charAt(0).toUpperCase();

    if (isActive) {
        // Desactivar usuario
        statusHeader.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
        statusIconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
        statusIcon.className = 'bi bi-person-x-fill text-warning fs-1';
        statusTitle.textContent = '¿Deseas desactivar este usuario?';
        statusMessage.textContent = 'El usuario no podrá acceder al sistema y se suspenderán sus permisos.';
        statusBtn.className = 'btn btn-warning';
        statusBtnIcon.className = 'bi bi-person-x me-1';
        statusBtnText.textContent = 'Desactivar Usuario';
    } else {
        // Activar usuario
        statusHeader.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        statusIconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        statusIcon.className = 'bi bi-person-check-fill text-success fs-1';
        statusTitle.textContent = '¿Deseas activar este usuario?';
        statusMessage.textContent = 'El usuario podrá acceder al sistema y tendrá todos sus permisos habilitados.';
        statusBtn.className = 'btn btn-success';
        statusBtnIcon.className = 'bi bi-person-check me-1';
        statusBtnText.textContent = 'Activar Usuario';
    }

    // Configurar botón de confirmación
    statusBtn.onclick = function() {
        document.getElementById(`user-toggle-form-${userId}`).submit();
    };

    // Mostrar modal usando Bootstrap nativo
    const modalElement = document.getElementById('userStatusConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de estado para usuario');

        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            console.error('❌ Bootstrap no está cargado');
            return;
        }

        // Usar Bootstrap Modal API
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        modal.show();
    }
};

// Función para confirmar guardado de usuario
window.confirmUserSave = function(formId, title = 'Guardar Usuario', message = 'Los datos del usuario se guardarán en el sistema.') {
    console.log('confirmUserSave ejecutada para:', formId);

    // Actualizar contenido del modal
    const titleEl = document.getElementById('userSaveTitle');
    const messageEl = document.getElementById('userSaveMessage');

    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;

    // Configurar botón de confirmación
    const confirmBtn = document.getElementById('confirmUserSaveBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            const form = document.getElementById(formId) || document.querySelector(`form[data-form-id="${formId}"]`);
            if (form) {
                form.submit();
            }
        };
    }

    // Mostrar modal usando Bootstrap nativo
    const modalElement = document.getElementById('userSaveConfirmModal');
    if (modalElement) {
        console.log('Mostrando modal de guardado para usuario');

        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            console.error('❌ Bootstrap no está cargado');
            return;
        }

        // Usar Bootstrap Modal API
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        modal.show();
    }
};

// Función para cerrar modales usando Bootstrap nativo
window.closeUserModal = function(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    }
};

// Event listeners que necesitan esperar al DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando event listeners para usuarios...');

    // Event listeners para cerrar modales
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) closeUserModal(modal.id);
        });
    });

    // Cerrar con backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const openModal = document.querySelector('.modal.show');
            if (openModal) closeUserModal(openModal.id);
        }
    });

    console.log('Funciones de usuarios inicializadas correctamente');
});