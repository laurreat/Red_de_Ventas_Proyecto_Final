// Funciones específicas para gestión de pedidos
class PedidosManager {
    constructor() {
        this.currentPedidoId = null;
        this.currentNewStatus = null;
        this.deleteModal = null;
        this.statusModal = null;
        this.saveModal = null;
        this.detailsModal = null;

        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initModals();
            this.initToasts();
            this.setupEventListeners();
            console.log('🚀 PedidosManager inicializado correctamente');
        });
    }

    initModals() {
        // Inicializar modales si Bootstrap está disponible
        if (typeof bootstrap !== 'undefined') {
            const deleteModalEl = document.getElementById('deletePedidoModal');
            const statusModalEl = document.getElementById('updateStatusPedidoModal');
            const saveModalEl = document.getElementById('savePedidoModal');
            const detailsModalEl = document.getElementById('detailsPedidoModal');

            if (deleteModalEl) this.deleteModal = new bootstrap.Modal(deleteModalEl);
            if (statusModalEl) this.statusModal = new bootstrap.Modal(statusModalEl);
            if (saveModalEl) this.saveModal = new bootstrap.Modal(saveModalEl);
            if (detailsModalEl) this.detailsModal = new bootstrap.Modal(detailsModalEl);
        }
    }

    setupEventListeners() {
        // Event listeners para botones de confirmación
        const confirmDeleteBtn = document.getElementById('confirmDeletePedidoBtn');
        const confirmStatusBtn = document.getElementById('confirmUpdateStatusPedidoBtn');
        const confirmSaveBtn = document.getElementById('confirmSavePedidoBtn');

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => this.executePedidoDelete());
        }

        if (confirmStatusBtn) {
            confirmStatusBtn.addEventListener('click', () => this.executePedidoStatusUpdate());
        }

        if (confirmSaveBtn) {
            confirmSaveBtn.addEventListener('click', () => this.executePedidoSave());
        }

        // Event listeners para cerrar modales
        this.setupModalCloseListeners();
    }

    setupModalCloseListeners() {
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) {
                    pedidosManager.closeModal(modal.id);
                }
            });
        });

        // Cerrar con backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    pedidosManager.closeModal(openModal.id);
                }
            }
        });
    }

    initToasts() {
        // Auto-hide alerts después de 5 segundos
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }
            }, 5000);
        });
    }

    // Función para confirmar eliminación de pedido
    confirmPedidoDelete(pedidoId) {
        console.log('🗑️ Confirmando eliminación de pedido:', pedidoId);

        // Obtener información del pedido desde la fila de la tabla
        const pedidoRow = document.querySelector(`[data-pedido-id="${pedidoId}"]`);
        let pedidoInfo = this.extractPedidoInfoFromRow(pedidoRow);

        // Actualizar información en el modal
        this.updateDeleteModalInfo(pedidoInfo);

        // Guardar ID para uso posterior
        this.currentPedidoId = pedidoId;

        // Mostrar modal
        this.showModal('deletePedidoModal');
    }

    // Función para cambiar estado del pedido
    updatePedidoStatus(pedidoId, newStatus) {
        console.log('🔄 Cambiando estado del pedido:', pedidoId, 'a:', newStatus);

        // Obtener información del pedido
        const pedidoRow = document.querySelector(`[data-pedido-id="${pedidoId}"]`);
        let pedidoInfo = this.extractPedidoInfoFromRow(pedidoRow);

        // Configurar modal según el nuevo estado
        this.configureStatusModal(pedidoInfo, newStatus);

        // Guardar datos para uso posterior
        this.currentPedidoId = pedidoId;
        this.currentNewStatus = newStatus;

        // Mostrar modal
        this.showModal('updateStatusPedidoModal');
    }

    // Función para confirmar guardado de pedido
    confirmPedidoSave(formId, title = 'Guardar Pedido', message = 'Los datos del pedido se guardarán en el sistema.') {
        console.log('💾 Confirmando guardado de pedido:', formId);

        // Actualizar contenido del modal
        const titleEl = document.getElementById('savePedidoTitle');
        const messageEl = document.getElementById('savePedidoMessage');
        const btnTextEl = document.getElementById('savePedidoBtnText');

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;
        if (btnTextEl) btnTextEl.textContent = title.includes('Crear') ? 'Crear Pedido' : 'Guardar Cambios';

        // Configurar botón de confirmación
        this.currentFormId = formId;

        // Mostrar modal
        this.showModal('savePedidoModal');
    }

    // Función para mostrar detalles del pedido
    showPedidoDetails(pedidoId) {
        console.log('👁️ Mostrando detalles del pedido:', pedidoId);

        // Mostrar modal con spinner
        this.showModal('detailsPedidoModal');

        // Cargar detalles vía AJAX
        this.loadPedidoDetails(pedidoId);
    }

    // Funciones auxiliares
    extractPedidoInfoFromRow(row) {
        if (!row) return {};

        return {
            numero: row.querySelector('.pedido-numero')?.textContent?.trim() || '',
            cliente: row.querySelector('.pedido-cliente')?.textContent?.trim() || '',
            total: row.querySelector('.pedido-total')?.textContent?.trim() || '',
            estado: row.querySelector('.pedido-estado')?.textContent?.trim() || '',
            estadoBadgeClass: row.querySelector('.pedido-estado')?.className || ''
        };
    }

    updateDeleteModalInfo(pedidoInfo) {
        const elementos = {
            numero: document.getElementById('deletePedidoNumero'),
            cliente: document.getElementById('deletePedidoCliente'),
            total: document.getElementById('deletePedidoTotal'),
            estado: document.getElementById('deletePedidoEstado')
        };

        if (elementos.numero) elementos.numero.textContent = pedidoInfo.numero;
        if (elementos.cliente) elementos.cliente.textContent = pedidoInfo.cliente;
        if (elementos.total) elementos.total.textContent = pedidoInfo.total;
        if (elementos.estado) {
            elementos.estado.textContent = pedidoInfo.estado;
            elementos.estado.className = pedidoInfo.estadoBadgeClass;
        }
    }

    configureStatusModal(pedidoInfo, newStatus) {
        // Configurar colores y textos según el nuevo estado
        const statusConfig = this.getStatusConfig(newStatus);

        // Actualizar header del modal
        const header = document.getElementById('statusPedidoModalHeader');
        const icon = document.getElementById('statusPedidoModalIcon');
        const iconContainer = document.getElementById('statusPedidoIconContainer');
        const statusIcon = document.getElementById('statusPedidoIcon');
        const title = document.getElementById('statusPedidoTitle');
        const message = document.getElementById('statusPedidoMessage');
        const btn = document.getElementById('confirmUpdateStatusPedidoBtn');
        const btnIcon = document.getElementById('statusPedidoBtnIcon');
        const btnText = document.getElementById('statusPedidoBtnText');

        if (header) header.style.background = statusConfig.gradient;
        if (icon) icon.className = statusConfig.headerIcon;
        if (iconContainer) iconContainer.style.backgroundColor = statusConfig.bgColor;
        if (statusIcon) statusIcon.className = statusConfig.icon;
        if (title) title.textContent = statusConfig.title;
        if (message) message.textContent = statusConfig.message;
        if (btn) btn.className = statusConfig.btnClass;
        if (btnIcon) btnIcon.className = statusConfig.btnIcon;
        if (btnText) btnText.textContent = statusConfig.btnText;

        // Actualizar información del pedido
        const elementos = {
            numero: document.getElementById('statusPedidoNumero'),
            cliente: document.getElementById('statusPedidoCliente'),
            estadoActual: document.getElementById('statusPedidoEstadoActual'),
            estadoNuevo: document.getElementById('statusPedidoEstadoNuevo')
        };

        if (elementos.numero) elementos.numero.textContent = pedidoInfo.numero;
        if (elementos.cliente) elementos.cliente.textContent = pedidoInfo.cliente;
        if (elementos.estadoActual) {
            elementos.estadoActual.textContent = pedidoInfo.estado;
            elementos.estadoActual.className = pedidoInfo.estadoBadgeClass;
        }
        if (elementos.estadoNuevo) {
            elementos.estadoNuevo.textContent = this.getStatusDisplayName(newStatus);
            elementos.estadoNuevo.className = statusConfig.badgeClass;
        }
    }

    getStatusConfig(status) {
        const configs = {
            'pendiente': {
                gradient: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
                headerIcon: 'bi bi-hourglass-split me-2 fs-4',
                bgColor: 'rgba(255, 193, 7, 0.1)',
                icon: 'bi bi-hourglass-split text-warning fs-1',
                title: '¿Marcar como pendiente?',
                message: 'El pedido será marcado como pendiente de procesamiento.',
                btnClass: 'btn btn-warning',
                btnIcon: 'bi bi-hourglass-split me-1',
                btnText: 'Marcar Pendiente',
                badgeClass: 'badge bg-warning'
            },
            'confirmado': {
                gradient: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
                headerIcon: 'bi bi-check-circle me-2 fs-4',
                bgColor: 'rgba(13, 110, 253, 0.1)',
                icon: 'bi bi-check-circle text-primary fs-1',
                title: '¿Confirmar pedido?',
                message: 'El pedido será confirmado y listo para preparar.',
                btnClass: 'btn btn-primary',
                btnIcon: 'bi bi-check-circle me-1',
                btnText: 'Confirmar Pedido',
                badgeClass: 'badge bg-primary'
            },
            'preparando': {
                gradient: 'linear-gradient(135deg, #fd7e14 0%, #dc6502 100%)',
                headerIcon: 'bi bi-tools me-2 fs-4',
                bgColor: 'rgba(253, 126, 20, 0.1)',
                icon: 'bi bi-tools text-orange fs-1',
                title: '¿Marcar como preparando?',
                message: 'El pedido está siendo preparado para entrega.',
                btnClass: 'btn btn-warning',
                btnIcon: 'bi bi-tools me-1',
                btnText: 'Marcar Preparando',
                badgeClass: 'badge bg-warning'
            },
            'enviado': {
                gradient: 'linear-gradient(135deg, #6f42c1 0%, #59359a 100%)',
                headerIcon: 'bi bi-truck me-2 fs-4',
                bgColor: 'rgba(111, 66, 193, 0.1)',
                icon: 'bi bi-truck text-purple fs-1',
                title: '¿Marcar como enviado?',
                message: 'El pedido ha sido enviado y está en camino.',
                btnClass: 'btn btn-purple',
                btnIcon: 'bi bi-truck me-1',
                btnText: 'Marcar Enviado',
                badgeClass: 'badge bg-purple'
            },
            'entregado': {
                gradient: 'linear-gradient(135deg, #198754 0%, #146c43 100%)',
                headerIcon: 'bi bi-check-all me-2 fs-4',
                bgColor: 'rgba(25, 135, 84, 0.1)',
                icon: 'bi bi-check-all text-success fs-1',
                title: '¿Marcar como entregado?',
                message: 'El pedido ha sido entregado exitosamente al cliente.',
                btnClass: 'btn btn-success',
                btnIcon: 'bi bi-check-all me-1',
                btnText: 'Marcar Entregado',
                badgeClass: 'badge bg-success'
            },
            'cancelado': {
                gradient: 'linear-gradient(135deg, #dc3545 0%, #b02a37 100%)',
                headerIcon: 'bi bi-x-circle me-2 fs-4',
                bgColor: 'rgba(220, 53, 69, 0.1)',
                icon: 'bi bi-x-circle text-danger fs-1',
                title: '¿Cancelar pedido?',
                message: 'El pedido será cancelado y no se procesará.',
                btnClass: 'btn btn-danger',
                btnIcon: 'bi bi-x-circle me-1',
                btnText: 'Cancelar Pedido',
                badgeClass: 'badge bg-danger'
            }
        };

        return configs[status] || configs['pendiente'];
    }

    getStatusDisplayName(status) {
        const names = {
            'pendiente': 'Pendiente',
            'confirmado': 'Confirmado',
            'preparando': 'Preparando',
            'enviado': 'Enviado',
            'entregado': 'Entregado',
            'cancelado': 'Cancelado'
        };
        return names[status] || status;
    }

    loadPedidoDetails(pedidoId) {
        // URL para cargar detalles del pedido
        const url = `/admin/pedidos/${pedidoId}/details`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.renderPedidoDetails(data.pedido);
                } else {
                    this.showDetailsError('Error al cargar los detalles del pedido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showDetailsError('Error de conexión al cargar los detalles');
            });
    }

    renderPedidoDetails(pedido) {
        const content = document.getElementById('detailsPedidoContent');
        const editBtn = document.getElementById('detailsPedidoEditBtn');

        if (content) {
            content.innerHTML = this.generateDetailsHTML(pedido);
        }

        if (editBtn) {
            editBtn.href = `/admin/pedidos/${pedido._id}/edit`;
        }
    }

    generateDetailsHTML(pedido) {
        return `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Información del Pedido</h6>
                    <div class="mb-2"><strong>Número:</strong> ${pedido.numero_pedido}</div>
                    <div class="mb-2"><strong>Estado:</strong> <span class="badge bg-primary">${pedido.estado}</span></div>
                    <div class="mb-2"><strong>Total:</strong> <span class="text-success fw-bold">$${pedido.total_final}</span></div>
                    <div class="mb-2"><strong>Fecha:</strong> ${new Date(pedido.created_at).toLocaleDateString()}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Cliente</h6>
                    <div class="mb-2"><strong>Nombre:</strong> ${pedido.cliente_data?.name || 'N/A'}</div>
                    <div class="mb-2"><strong>Email:</strong> ${pedido.cliente_data?.email || 'N/A'}</div>
                    <div class="mb-2"><strong>Teléfono:</strong> ${pedido.telefono_entrega || 'N/A'}</div>
                    <div class="mb-2"><strong>Dirección:</strong> ${pedido.direccion_entrega || 'N/A'}</div>
                </div>
            </div>
            <hr>
            <h6 class="fw-bold mb-3">Productos</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pedido.detalles?.map(detalle => `
                            <tr>
                                <td>${detalle.producto_data?.nombre || 'Producto'}</td>
                                <td>${detalle.cantidad}</td>
                                <td>$${detalle.precio_unitario}</td>
                                <td>$${detalle.subtotal}</td>
                            </tr>
                        `).join('') || '<tr><td colspan="4" class="text-center">No hay productos</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    }

    showDetailsError(message) {
        const content = document.getElementById('detailsPedidoContent');
        if (content) {
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <p class="text-muted">${message}</p>
                </div>
            `;
        }
    }

    // Funciones de ejecución
    executePedidoDelete() {
        if (!this.currentPedidoId) return;

        console.log('🗑️ Ejecutando eliminación del pedido:', this.currentPedidoId);

        // Crear y enviar formulario de eliminación
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/pedidos/${this.currentPedidoId}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.content;
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }

    executePedidoStatusUpdate() {
        if (!this.currentPedidoId || !this.currentNewStatus) return;

        console.log('🔄 Ejecutando cambio de estado:', this.currentPedidoId, 'a:', this.currentNewStatus);

        const motivo = document.getElementById('statusPedidoMotivo')?.value || '';

        // Crear y enviar formulario de actualización
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/pedidos/${this.currentPedidoId}/update-status`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.content;
            form.appendChild(csrfInput);
        }

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'estado';
        statusInput.value = this.currentNewStatus;
        form.appendChild(statusInput);

        const motivoInput = document.createElement('input');
        motivoInput.type = 'hidden';
        motivoInput.name = 'motivo';
        motivoInput.value = motivo;
        form.appendChild(motivoInput);

        document.body.appendChild(form);
        form.submit();
    }

    executePedidoSave() {
        if (!this.currentFormId) return;

        const form = document.getElementById(this.currentFormId);
        if (form) {
            form.submit();
        }
    }

    // Función para mostrar modal
    showModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                // Fallback manual
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');

                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    // Función para cerrar modal
    closeModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            } else {
                // Fallback manual
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');

                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
    }

    // Funciones para mostrar alertas
    showSuccess(title, message) {
        this.showAlert('success', title, message);
    }

    showError(title, message) {
        this.showAlert('error', title, message);
    }

    showAlert(type, title, message) {
        // Implementación similar a la de productos/usuarios
        console.log(`${type.toUpperCase()}: ${title} - ${message}`);
    }
}

// Inicializar el manager de pedidos
const pedidosManager = new PedidosManager();

// Funciones globales para mantener compatibilidad
function confirmDelete(pedidoId) {
    pedidosManager.confirmPedidoDelete(pedidoId);
}

function updateStatus(pedidoId, newStatus) {
    pedidosManager.updatePedidoStatus(pedidoId, newStatus);
}

function confirmSave(formId, title, message) {
    pedidosManager.confirmPedidoSave(formId, title, message);
}

function showPedidoDetails(pedidoId) {
    pedidosManager.showPedidoDetails(pedidoId);
}

// Exponer funciones al scope global
window.confirmDelete = confirmDelete;
window.updateStatus = updateStatus;
window.confirmSave = confirmSave;
window.showPedidoDetails = showPedidoDetails;
window.pedidosManager = pedidosManager;