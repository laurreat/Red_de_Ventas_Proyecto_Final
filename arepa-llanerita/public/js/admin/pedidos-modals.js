// Modal simple para eliminación de pedidos
console.log('🚀 Modal de eliminación de pedidos cargado...');

// Función simple para confirmar eliminación de pedido
function confirmDeletePedido(pedidoId, numeroPedido, cliente, total, estado) {
    console.log('🗑️ confirmDeletePedido llamada con:', { pedidoId, numeroPedido, cliente, total, estado });

    // Buscar el modal de eliminación
    const deleteModal = document.getElementById('deletePedidoConfirmModal');

    if (!deleteModal) {
        console.error('❌ Modal de eliminación no encontrado');
        return;
    }

    // Actualizar información del pedido en el modal
    const numeroEl = document.getElementById('deletePedidoNumero');
    const clienteEl = document.getElementById('deletePedidoCliente');
    const totalEl = document.getElementById('deletePedidoTotal');
    const estadoEl = document.getElementById('deletePedidoEstado');

    if (numeroEl) numeroEl.textContent = numeroPedido || '-';
    if (clienteEl) clienteEl.textContent = cliente || '-';
    if (totalEl) totalEl.textContent = total || '-';
    if (estadoEl) {
        estadoEl.textContent = estado || '-';
        estadoEl.className = 'badge ' + getEstadoBadgeClass(estado ? estado.toLowerCase() : 'pendiente');
    }

    // Configurar el botón de confirmación
    const confirmBtn = document.getElementById('confirmDeletePedidoBtn');
    if (confirmBtn) {
        // Remover eventos anteriores clonando el botón
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Agregar el evento de click
        newConfirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🗑️ Ejecutando eliminación del pedido:', pedidoId);

            // Buscar el formulario y enviarlo
            const form = document.getElementById(`delete-form-${pedidoId}`);
            if (form) {
                form.submit();
            } else {
                console.error('❌ Formulario de eliminación no encontrado');
            }

            // Cerrar el modal
            hideDeleteModal();
        });
    }

    // Mostrar el modal usando Bootstrap
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(deleteModal);
            modal.show();
        } else {
            // Fallback manual si Bootstrap no está disponible
            deleteModal.style.display = 'block';
            deleteModal.classList.add('show');
            document.body.classList.add('modal-open');
        }
        console.log('✅ Modal de eliminación mostrado');
    } catch (error) {
        console.error('❌ Error mostrando modal:', error);
    }
}

// Función para ocultar el modal
function hideDeleteModal() {
    const deleteModal = document.getElementById('deletePedidoConfirmModal');
    if (deleteModal) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(deleteModal);
                if (modal) {
                    modal.hide();
                } else {
                    // Ocultar manualmente
                    deleteModal.style.display = 'none';
                    deleteModal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            } else {
                // Ocultar manualmente
                deleteModal.style.display = 'none';
                deleteModal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        } catch (error) {
            console.error('❌ Error ocultando modal:', error);
            // Forzar ocultado manual
            deleteModal.style.display = 'none';
            deleteModal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }
}

// Función auxiliar para obtener la clase del badge según el estado
function getEstadoBadgeClass(estado) {
    const classes = {
        'pendiente': 'bg-warning',
        'confirmado': 'bg-info',
        'en_preparacion': 'bg-primary',
        'listo': 'bg-secondary',
        'en_camino': 'bg-primary',
        'entregado': 'bg-success',
        'cancelado': 'bg-danger'
    };
    return classes[estado] || 'bg-secondary';
}

// Exponer funciones al scope global
window.confirmDeletePedido = confirmDeletePedido;
window.hideDeleteModal = hideDeleteModal;

console.log('✅ Funciones de eliminación de pedidos disponibles:', {
    confirmDeletePedido: typeof window.confirmDeletePedido,
    hideDeleteModal: typeof window.hideDeleteModal
});

// ==================== MODAL DE CAMBIO DE ESTADO ====================

// Función para mostrar selector de estados
function showStatusSelector(pedidoId, numeroPedido, cliente, currentStatus, estados) {
    console.log('🔄 showStatusSelector llamada con:', { pedidoId, numeroPedido, cliente, currentStatus, estados });

    const selectorModal = document.getElementById('statusSelectorPedidoModal');
    if (!selectorModal) {
        console.error('❌ Modal selector no encontrado');
        return;
    }

    // Actualizar información del pedido en el modal
    const numeroEl = document.getElementById('statusSelectorPedidoNumero');
    const clienteEl = document.getElementById('statusSelectorPedidoCliente');
    const currentStatusEl = document.getElementById('statusSelectorPedidoCurrentStatus');

    if (numeroEl) numeroEl.textContent = numeroPedido || '-';
    if (clienteEl) clienteEl.textContent = cliente || '-';
    if (currentStatusEl) {
        currentStatusEl.textContent = currentStatus || '-';
        currentStatusEl.className = 'badge ' + getEstadoBadgeClass(currentStatus ? currentStatus.toLowerCase() : 'pendiente');
    }

    // Generar opciones de estado dinámicamente
    const optionsContainer = document.getElementById('statusSelectorOptions');
    if (optionsContainer && estados) {
        optionsContainer.innerHTML = '';

        Object.entries(estados).forEach(([valor, nombre]) => {
            if (valor !== currentStatus.toLowerCase()) {
                const statusConfig = getStatusConfig(valor);

                const optionCol = document.createElement('div');
                optionCol.className = 'col-md-6 col-12 mb-3';

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-outline-light w-100 text-start d-flex align-items-center p-3 h-100 border-2 status-option-btn';
                button.style.cssText = `
                    background: linear-gradient(135deg, ${statusConfig.bgColor.replace('0.1', '0.05')}, white);
                    border-color: ${getBorderColor(statusConfig.badgeClass)};
                    border-radius: 12px;
                    transition: all 0.3s ease;
                    color: #333;
                    min-height: 80px;
                    cursor: pointer;
                `;

                button.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                         style="width: 55px; height: 55px; background: ${getBorderColor(statusConfig.badgeClass)}; color: white;">
                        <i class="${statusConfig.icon.split(' ').slice(0, 2).join(' ')} fs-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-1 fs-6">${nombre}</div>
                        <small class="text-muted opacity-75">${statusConfig.message}</small>
                    </div>
                    <i class="bi bi-arrow-right-circle text-primary fs-5"></i>
                `;

                // Evento click para cada botón de estado
                button.addEventListener('click', function(e) {
                    console.log('🎯 Botón de estado clickeado:', valor, nombre);
                    e.preventDefault();

                    // Cerrar modal actual
                    hideStatusSelector();

                    // Abrir modal de confirmación después de un breve delay
                    setTimeout(() => {
                        confirmStatusChangePedido(pedidoId, valor, numeroPedido, cliente, currentStatus);
                    }, 200);
                });

                optionCol.appendChild(button);
                optionsContainer.appendChild(optionCol);
            }
        });
    }

    // Mostrar el modal
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(selectorModal);
            modal.show();
        } else {
            selectorModal.style.display = 'block';
            selectorModal.classList.add('show');
            document.body.classList.add('modal-open');
        }
        console.log('✅ Modal selector mostrado');
    } catch (error) {
        console.error('❌ Error mostrando modal selector:', error);
    }
}

// Función para ocultar el modal selector
function hideStatusSelector() {
    const selectorModal = document.getElementById('statusSelectorPedidoModal');
    if (selectorModal) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(selectorModal);
                if (modal) {
                    modal.hide();
                } else {
                    selectorModal.style.display = 'none';
                    selectorModal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            } else {
                selectorModal.style.display = 'none';
                selectorModal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        } catch (error) {
            console.error('❌ Error ocultando modal selector:', error);
            selectorModal.style.display = 'none';
            selectorModal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }
}

// Función para confirmar cambio de estado
function confirmStatusChangePedido(pedidoId, newStatus, numeroPedido, cliente, currentStatus) {
    console.log('🔄 confirmStatusChangePedido llamada:', { pedidoId, newStatus, numeroPedido, cliente, currentStatus });

    const statusModal = document.getElementById('statusPedidoConfirmModal');
    if (!statusModal) {
        console.error('❌ Modal de confirmación de estado no encontrado');
        return;
    }

    try {
        // Configurar modal según el nuevo estado
        const statusConfig = getStatusConfig(newStatus);

        // Actualizar header del modal
        const header = document.getElementById('statusPedidoModalHeader');
        const iconContainer = document.getElementById('statusPedidoIconContainer');
        const statusIcon = document.getElementById('statusPedidoIcon');
        const title = document.getElementById('statusPedidoTitle');
        const message = document.getElementById('statusPedidoMessage');
        const btn = document.getElementById('confirmStatusPedidoBtn');
        const btnIcon = document.getElementById('statusPedidoBtnIcon');
        const btnText = document.getElementById('statusPedidoBtnText');

        if (header) header.style.background = statusConfig.gradient;
        if (iconContainer) iconContainer.style.backgroundColor = statusConfig.bgColor;
        if (statusIcon) statusIcon.className = statusConfig.icon;
        if (title) title.textContent = statusConfig.title;
        if (message) message.textContent = statusConfig.message;
        if (btn) btn.className = statusConfig.btnClass;
        if (btnIcon) btnIcon.className = statusConfig.btnIcon;
        if (btnText) btnText.textContent = statusConfig.btnText;

        // Actualizar información del pedido
        const numeroEl = document.getElementById('statusPedidoNumero');
        const clienteEl = document.getElementById('statusPedidoCliente');
        const currentStatusEl = document.getElementById('statusPedidoCurrentStatus');
        const newStatusEl = document.getElementById('statusPedidoNewStatus');

        if (numeroEl) numeroEl.textContent = numeroPedido || '-';
        if (clienteEl) clienteEl.textContent = cliente || '-';
        if (currentStatusEl) {
            currentStatusEl.textContent = currentStatus || '-';
            currentStatusEl.className = 'badge ' + getEstadoBadgeClass(currentStatus ? currentStatus.toLowerCase() : 'pendiente');
        }
        if (newStatusEl) {
            newStatusEl.textContent = statusConfig.displayName;
            newStatusEl.className = statusConfig.badgeClass;
        }

        // Configurar botón de confirmación
        const confirmBtn = document.getElementById('confirmStatusPedidoBtn');
        if (confirmBtn) {
            // Remover eventos anteriores clonando el botón
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

            // Agregar nuevo listener
            newBtn.addEventListener('click', function() {
                console.log('🔄 Ejecutando cambio de estado:', pedidoId, 'a', newStatus);
                const estadoInput = document.getElementById(`estado-${pedidoId}`);
                const form = document.getElementById(`status-form-${pedidoId}`);
                if (estadoInput && form) {
                    estadoInput.value = newStatus;
                    form.submit();
                }

                // Cerrar modal
                hideStatusConfirmModal();
            });
        }

        // Mostrar modal
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(statusModal);
            modal.show();
        } else {
            statusModal.style.display = 'block';
            statusModal.classList.add('show');
            document.body.classList.add('modal-open');
        }
        console.log('✅ Modal de confirmación de estado mostrado');

    } catch (error) {
        console.error('❌ Error creando modal de estado:', error);
    }
}

// Función para ocultar modal de confirmación de estado
function hideStatusConfirmModal() {
    const statusModal = document.getElementById('statusPedidoConfirmModal');
    if (statusModal) {
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(statusModal);
                if (modal) {
                    modal.hide();
                } else {
                    statusModal.style.display = 'none';
                    statusModal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            } else {
                statusModal.style.display = 'none';
                statusModal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        } catch (error) {
            console.error('❌ Error ocultando modal de estado:', error);
            statusModal.style.display = 'none';
            statusModal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }
}

// Configuración de estados
function getStatusConfig(status) {
    const configs = {
        'pendiente': {
            gradient: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
            bgColor: 'rgba(255, 193, 7, 0.1)',
            icon: 'bi bi-clock-history text-warning fs-2',
            title: '¿Marcar como pendiente?',
            message: 'El pedido está esperando confirmación y procesamiento.',
            btnClass: 'btn btn-warning',
            btnIcon: 'bi bi-clock-history me-1',
            btnText: 'Marcar Pendiente',
            badgeClass: 'badge bg-warning',
            displayName: 'Pendiente'
        },
        'confirmado': {
            gradient: 'linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%)',
            bgColor: 'rgba(13, 202, 240, 0.1)',
            icon: 'bi bi-check-circle-fill text-info fs-2',
            title: '¿Confirmar pedido?',
            message: 'El pedido será confirmado y listo para preparar.',
            btnClass: 'btn btn-info',
            btnIcon: 'bi bi-check-circle-fill me-1',
            btnText: 'Confirmar Pedido',
            badgeClass: 'badge bg-info',
            displayName: 'Confirmado'
        },
        'en_preparacion': {
            gradient: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
            bgColor: 'rgba(13, 110, 253, 0.1)',
            icon: 'bi bi-person-gear text-primary fs-2',
            title: '¿Marcar como en preparación?',
            message: 'El pedido está siendo preparado en cocina.',
            btnClass: 'btn btn-primary',
            btnIcon: 'bi bi-person-gear me-1',
            btnText: 'Marcar En Preparación',
            badgeClass: 'badge bg-primary',
            displayName: 'En Preparación'
        },
        'listo': {
            gradient: 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)',
            bgColor: 'rgba(108, 117, 125, 0.1)',
            icon: 'bi bi-bag-check-fill text-secondary fs-2',
            title: '¿Marcar como listo?',
            message: 'El pedido está listo para ser entregado.',
            btnClass: 'btn btn-secondary',
            btnIcon: 'bi bi-bag-check-fill me-1',
            btnText: 'Marcar Listo',
            badgeClass: 'badge bg-secondary',
            displayName: 'Listo'
        },
        'en_camino': {
            gradient: 'linear-gradient(135deg, #722f37 0%, #5c252b 100%)',
            bgColor: 'rgba(114, 47, 55, 0.1)',
            icon: 'bi bi-truck text-primary fs-2',
            title: '¿Marcar como en camino?',
            message: 'El pedido está siendo transportado al cliente.',
            btnClass: 'btn btn-primary',
            btnIcon: 'bi bi-truck me-1',
            btnText: 'Marcar En Camino',
            badgeClass: 'badge bg-primary',
            displayName: 'En Camino'
        },
        'entregado': {
            gradient: 'linear-gradient(135deg, #198754 0%, #146c43 100%)',
            bgColor: 'rgba(25, 135, 84, 0.1)',
            icon: 'bi bi-check2-all text-success fs-2',
            title: '¿Marcar como entregado?',
            message: 'El pedido ha sido entregado exitosamente.',
            btnClass: 'btn btn-success',
            btnIcon: 'bi bi-check2-all me-1',
            btnText: 'Marcar Entregado',
            badgeClass: 'badge bg-success',
            displayName: 'Entregado'
        },
        'cancelado': {
            gradient: 'linear-gradient(135deg, #dc3545 0%, #b02a37 100%)',
            bgColor: 'rgba(220, 53, 69, 0.1)',
            icon: 'bi bi-x-circle-fill text-danger fs-2',
            title: '¿Cancelar pedido?',
            message: 'El pedido será cancelado y no se procesará.',
            btnClass: 'btn btn-danger',
            btnIcon: 'bi bi-x-circle-fill me-1',
            btnText: 'Cancelar Pedido',
            badgeClass: 'badge bg-danger',
            displayName: 'Cancelado'
        }
    };

    return configs[status] || configs['pendiente'];
}

// Función auxiliar para obtener el color de borde
function getBorderColor(badgeClass) {
    if (badgeClass.includes('warning')) return '#ffc107';
    if (badgeClass.includes('info')) return '#0dcaf0';
    if (badgeClass.includes('primary')) return '#0d6efd';
    if (badgeClass.includes('secondary')) return '#6c757d';
    if (badgeClass.includes('success')) return '#198754';
    return '#dc3545';
}

// Exponer funciones de estado al scope global
window.showStatusSelector = showStatusSelector;
window.hideStatusSelector = hideStatusSelector;
window.confirmStatusChangePedido = confirmStatusChangePedido;
window.hideStatusConfirmModal = hideStatusConfirmModal;

// Event listener para cerrar modal con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const deleteModal = document.getElementById('deletePedidoConfirmModal');
        const statusModal = document.getElementById('statusSelectorPedidoModal');
        const confirmModal = document.getElementById('statusPedidoConfirmModal');

        if (deleteModal && deleteModal.classList.contains('show')) {
            hideDeleteModal();
        } else if (statusModal && statusModal.classList.contains('show')) {
            hideStatusSelector();
        } else if (confirmModal && confirmModal.classList.contains('show')) {
            hideStatusConfirmModal();
        }
    }
});