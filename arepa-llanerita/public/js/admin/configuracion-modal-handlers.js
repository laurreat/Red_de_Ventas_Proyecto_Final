/**
 * Manejadores de modales para configuración del sistema
 * Separado para mejor organización del código
 */

/**
 * Función para mostrar modal de resultados
 */
function showResultModal(type, title, message, details = null) {
    const modal = document.getElementById('resultModal');
    const header = document.getElementById('resultModalHeader');
    const headerContent = document.getElementById('resultModalHeaderContent');
    const modalIcon = document.getElementById('resultModalIcon');
    const close = document.getElementById('resultModalClose');
    const iconContainer = document.getElementById('resultIconContainer');
    const icon = document.getElementById('resultIcon');
    const modalTitle = document.getElementById('resultTitle');
    const modalMessage = document.getElementById('resultMessage');
    const detailsContainer = document.getElementById('resultDetails');
    const detailsList = document.getElementById('resultDetailsList');

    // Configurar colores y estilos según el tipo
    if (type === 'success') {
        header.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        headerContent.className = 'd-flex align-items-center text-white';
        modalIcon.className = 'bi bi-check-circle me-2 fs-4';
        close.className = 'btn-close btn-close-white';
        iconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        icon.className = 'bi bi-check-circle text-success fs-1';
    } else if (type === 'error') {
        header.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
        headerContent.className = 'd-flex align-items-center text-white';
        modalIcon.className = 'bi bi-exclamation-triangle me-2 fs-4';
        close.className = 'btn-close btn-close-white';
        iconContainer.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
        icon.className = 'bi bi-exclamation-triangle text-danger fs-1';
    } else if (type === 'warning') {
        header.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
        headerContent.className = 'd-flex align-items-center text-dark';
        modalIcon.className = 'bi bi-exclamation-triangle me-2 fs-4';
        close.className = 'btn-close';
        iconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
        icon.className = 'bi bi-exclamation-triangle text-warning fs-1';
    }

    // Establecer contenido
    modalTitle.textContent = title;
    modalMessage.textContent = message;

    // Manejar detalles
    if (details && details.length > 0) {
        detailsList.innerHTML = '';
        details.forEach(detail => {
            const li = document.createElement('li');
            li.textContent = detail;
            detailsList.appendChild(li);
        });
        detailsContainer.classList.remove('d-none');
    } else {
        detailsContainer.classList.add('d-none');
    }

    // Mostrar modal
    if (typeof $ !== 'undefined') {
        modal.removeAttribute('aria-hidden');
        $(modal).modal('show');
    } else {
        modal.classList.remove('fade');
        modal.classList.add('show');
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        modal.removeAttribute('aria-hidden');

        const modalDialog = modal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.margin = '0';
            modalDialog.style.zIndex = '1060';
            modalDialog.style.position = 'relative';
        }

        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    }

    // Gestión del foco para accesibilidad
    setTimeout(() => {
        const closeButton = modal.querySelector('[data-bs-dismiss="modal"]');
        if (closeButton) {
            closeButton.focus();
        }
    }, 200);

    // Solo agregar evento de clic para cerrar si no se está usando jQuery
    if (typeof $ === 'undefined') {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                const focusedElement = modal.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }

                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.style.backgroundColor = '';
                modal.style.alignItems = '';
                modal.style.justifyContent = '';
                modal.removeAttribute('aria-modal');
                modal.removeAttribute('role');

                setTimeout(() => {
                    modal.setAttribute('aria-hidden', 'true');
                }, 100);

                if (modalDialog) {
                    modalDialog.style.margin = '';
                    modalDialog.style.zIndex = '';
                    modalDialog.style.position = '';
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });
    }
}

/**
 * Configurar manejadores de cierre para todos los modales
 */
function setupModalCloseHandlers() {
    function closeModal(modalId, backdropId = null) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (typeof $ !== 'undefined') {
                $(modal).modal('hide');
            } else {
                const focusedElement = modal.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }

                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.style.backgroundColor = '';
                modal.style.alignItems = '';
                modal.style.justifyContent = '';
                modal.removeAttribute('aria-modal');
                modal.removeAttribute('role');

                setTimeout(() => {
                    modal.setAttribute('aria-hidden', 'true');
                }, 100);

                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.margin = '';
                    modalDialog.style.zIndex = '';
                    modalDialog.style.position = '';
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        }
    }

    // Event listeners para botones de cerrar
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                const modalId = modal.id;
                let backdropId;

                switch(modalId) {
                    case 'backupConfirmModal':
                        backdropId = 'backup-backdrop';
                        break;
                    case 'cacheConfirmModal':
                        backdropId = 'cache-backdrop';
                        break;
                    case 'logsConfirmModal':
                        backdropId = 'logs-backdrop';
                        break;
                    case 'infoSistemaModal':
                        backdropId = 'info-backdrop';
                        break;
                    case 'resultModal':
                        backdropId = 'result-backdrop';
                        break;
                }

                closeModal(modalId, backdropId);
            }
        });
    });

    // Cerrar modal con backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const modalId = e.target.id?.replace('-backdrop', '') + 'Modal';
            if (modalId) {
                closeModal(modalId.replace('Modal', 'Modal'), e.target.id);
            }
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modalId = openModal.id;
                let backdropId;

                switch(modalId) {
                    case 'backupConfirmModal':
                        backdropId = 'backup-backdrop';
                        break;
                    case 'cacheConfirmModal':
                        backdropId = 'cache-backdrop';
                        break;
                    case 'logsConfirmModal':
                        backdropId = 'logs-backdrop';
                        break;
                    case 'infoSistemaModal':
                        backdropId = 'info-backdrop';
                        break;
                    case 'resultModal':
                        backdropId = 'result-backdrop';
                        break;
                }

                closeModal(modalId, backdropId);
            }
        }
    });
}

// Exponer funciones globalmente
window.showResultModal = showResultModal;
window.setupModalCloseHandlers = setupModalCloseHandlers;