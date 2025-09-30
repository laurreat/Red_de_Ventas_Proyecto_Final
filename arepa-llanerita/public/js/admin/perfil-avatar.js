/**
 * Módulo para gestión de avatar del perfil
 * Separado para mejor organización del código
 */

/**
 * Función para eliminar avatar
 */
function eliminarAvatar() {
    const btn = document.getElementById('eliminar-avatar-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    }

    const deleteRoute = window.perfilRoutes ? window.perfilRoutes.deleteAvatar : '#';
    const csrfToken = window.perfilCSRF || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(deleteRoute, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la interfaz
            const avatarContainer = document.getElementById('avatar-container');
            const userName = window.perfilUserInitial || 'U';

            avatarContainer.innerHTML = `
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-3 border-light shadow"
                     style="width: 150px; height: 150px; font-size: 3rem;" id="user-avatar-placeholder">
                    ${userName}
                </div>
            `;

            // Mostrar mensaje de éxito
            showSuccessMessage(data.message);

        } else {
            // Mostrar error
            alert('Error: ' + (data.message || 'Error al eliminar la foto'));

            // Restaurar botón
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash3"></i>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al eliminar la foto');

        // Restaurar botón
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash3"></i>';
        }
    });
}

/**
 * Función para mostrar vista previa de la imagen seleccionada
 */
function setupImagePreview() {
    const avatarInput = document.querySelector('input[name="avatar"]');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo es muy grande. Máximo 2MB permitido.');
                    e.target.value = '';
                    return;
                }

                // Validar tipo
                if (!file.type.match('image.*')) {
                    alert('Por favor selecciona un archivo de imagen válido.');
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarContainer = document.getElementById('avatar-container');
                    const currentAvatar = document.getElementById('user-avatar') || document.getElementById('user-avatar-placeholder');

                    if (currentAvatar) {
                        // Si era un placeholder DIV, crear nueva imagen
                        if (currentAvatar.tagName === 'DIV') {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'rounded-circle border border-3 border-light shadow';
                            img.style.width = '150px';
                            img.style.height = '150px';
                            img.style.objectFit = 'cover';
                            img.alt = 'Avatar Preview';
                            img.id = 'user-avatar';

                            currentAvatar.replaceWith(img);

                            // Remover botón de eliminar si existe
                            const deleteBtn = document.getElementById('eliminar-avatar-btn');
                            if (deleteBtn) {
                                deleteBtn.remove();
                            }
                        } else {
                            // Si ya era imagen, solo cambiar src
                            currentAvatar.src = e.target.result;
                            currentAvatar.alt = 'Avatar Preview';
                        }

                        // Mostrar mensaje de preview
                        const previewMsg = document.createElement('small');
                        previewMsg.className = 'text-info d-block mt-2';
                        previewMsg.id = 'preview-message';
                        previewMsg.innerHTML = '<i class="bi bi-info-circle me-1"></i>Vista previa - Guarda los cambios para confirmar';

                        // Remover mensaje anterior si existe
                        const existingMsg = document.getElementById('preview-message');
                        if (existingMsg) {
                            existingMsg.remove();
                        }

                        avatarContainer.parentNode.appendChild(previewMsg);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Función helper para mostrar mensajes de éxito
 */
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <i class="bi bi-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container-fluid .row').querySelector('.col-12');
    container.appendChild(alertDiv);

    // Auto-remover el mensaje después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

/**
 * Inicializar el módulo de avatar
 */
function initializeAvatarModule() {
    // Configurar botón de eliminar avatar
    const eliminarAvatarBtn = document.getElementById('eliminar-avatar-btn');
    if (eliminarAvatarBtn) {
        eliminarAvatarBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?')) {
                eliminarAvatar();
            }
        });
    }

    // Configurar preview de imagen
    setupImagePreview();

    console.log('✅ Módulo de avatar inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeAvatarModule);

// Exponer funciones globalmente
window.eliminarAvatar = eliminarAvatar;