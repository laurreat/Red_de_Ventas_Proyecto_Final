{{-- Modales de confirmación profesionales para gestión de pedidos --}}

{{-- Modal de confirmación para eliminar pedido --}}
<div class="modal fade" id="deletePedidoConfirmModal" tabindex="-1" aria-labelledby="deletePedidoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="deletePedidoConfirmModalLabel">Confirmar Eliminación de Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-basket-fill text-danger fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de eliminar este pedido?</h6>
                    <p class="text-muted mb-0 small">Esta acción no se puede deshacer. El pedido se eliminará permanentemente del sistema junto con todos sus detalles.</p>
                </div>

                <div id="deletePedidoInfo" class="bg-light rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Número:</small>
                            <p class="mb-1 fw-bold" id="deletePedidoNumero">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cliente:</small>
                            <p class="mb-1" id="deletePedidoCliente">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total:</small>
                            <p class="mb-1 fw-bold text-success" id="deletePedidoTotal">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Estado:</small>
                            <span class="badge bg-secondary" id="deletePedidoEstado">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-end">
                <button type="button" class="btn btn-danger" id="confirmDeletePedidoBtn">
                    <i class="bi bi-trash3 me-1"></i>
                    Eliminar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para cambiar estado de pedido --}}
<div class="modal fade" id="statusPedidoConfirmModal" tabindex="-1" aria-labelledby="statusPedidoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="statusPedidoModalHeader">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-arrow-repeat me-2 fs-4" id="statusPedidoModalIcon"></i>
                    <h5 class="modal-title mb-0" id="statusPedidoConfirmModalLabel">Cambiar Estado del Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px;" id="statusPedidoIconContainer">
                        <i class="bi bi-arrow-clockwise fs-1" id="statusPedidoIcon"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="statusPedidoTitle">¿Cambiar estado del pedido?</h6>
                    <p class="text-muted mb-0 small" id="statusPedidoMessage"></p>
                </div>

                <div id="statusPedidoInfo" class="bg-light rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Número:</small>
                            <p class="mb-0 fw-bold" id="statusPedidoNumero"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cliente:</small>
                            <p class="mb-0" id="statusPedidoCliente"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Estado actual:</small>
                            <span class="badge" id="statusPedidoCurrentStatus"></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Nuevo estado:</small>
                            <span class="badge" id="statusPedidoNewStatus"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="statusPedidoMotivo" class="form-label">Motivo del cambio (opcional)</label>
                    <textarea class="form-control" id="statusPedidoMotivo" rows="3"
                              placeholder="Describe el motivo del cambio de estado..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn" id="confirmStatusPedidoBtn">
                    <i class="bi bi-check-circle me-1" id="statusPedidoBtnIcon"></i>
                    <span id="statusPedidoBtnText">Cambiar Estado</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para guardar pedido --}}
<div class="modal fade" id="savePedidoConfirmModal" tabindex="-1" aria-labelledby="savePedidoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="savePedidoConfirmModalLabel">Confirmar Guardado</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-basket-check-fill text-success fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de guardar los cambios?</h6>
                    <p class="text-muted mb-0 small" id="savePedidoMessage">Los cambios del pedido se guardarán en el sistema.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmSavePedidoBtn">
                    <i class="bi bi-save me-1"></i>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para ver pedido --}}
<div class="modal fade" id="viewPedidoConfirmModal" tabindex="-1" aria-labelledby="viewPedidoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-eye-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="viewPedidoConfirmModalLabel">Ver Detalles del Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(23, 162, 184, 0.1);">
                        <i class="bi bi-basket-fill text-info fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas ver los detalles de este pedido?</h6>
                    <p class="text-muted mb-0 small">Se abrirá la página con toda la información detallada del pedido.</p>
                </div>

                <div id="viewPedidoInfo" class="bg-light rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Número:</small>
                            <p class="mb-1 fw-bold" id="viewPedidoNumero">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cliente:</small>
                            <p class="mb-1" id="viewPedidoCliente">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total:</small>
                            <p class="mb-1 fw-bold text-success" id="viewPedidoTotal">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Estado:</small>
                            <span class="badge bg-secondary" id="viewPedidoEstado">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-info" id="confirmViewPedidoBtn">
                    <i class="bi bi-eye me-1"></i>
                    Ver Detalles
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para editar pedido --}}
<div class="modal fade" id="editPedidoConfirmModal" tabindex="-1" aria-labelledby="editPedidoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-pencil-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="editPedidoConfirmModalLabel">Editar Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(253, 126, 20, 0.1);">
                        <i class="bi bi-basket-fill text-warning fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas editar este pedido?</h6>
                    <p class="text-muted mb-0 small">Se abrirá el formulario de edición donde podrás modificar los detalles del pedido.</p>
                </div>

                <div id="editPedidoInfo" class="bg-light rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Número:</small>
                            <p class="mb-1 fw-bold" id="editPedidoNumero">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cliente:</small>
                            <p class="mb-1" id="editPedidoCliente">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total:</small>
                            <p class="mb-1 fw-bold text-success" id="editPedidoTotal">-</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Estado:</small>
                            <span class="badge bg-secondary" id="editPedidoEstado">-</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <small>Solo se pueden editar pedidos que no estén entregados o cancelados.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmEditPedidoBtn">
                    <i class="bi bi-pencil me-1"></i>
                    Editar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal selector de estado para pedido - Estilo profesional como usuarios --}}
<div class="modal fade" id="statusSelectorPedidoModal" tabindex="-1" aria-labelledby="statusSelectorPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-elegant">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                <div class="d-flex align-items-center text-white">
                    <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                         style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="statusSelectorPedidoModalLabel">Cambiar Estado del Pedido</h5>
                        <small class="opacity-75">Selecciona el nuevo estado para el pedido</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5" style="background-color: #f8f9fa;">
                <!-- Información del pedido -->
                <div id="statusSelectorPedidoInfo" class="bg-white rounded-3 p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-basket-fill text-white fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">Pedido #<span id="statusSelectorPedidoNumero">-</span></h6>
                            <p class="mb-1 text-muted">Cliente: <span id="statusSelectorPedidoCliente">-</span></p>
                            <div>
                                <small class="text-muted">Estado actual: </small>
                                <span class="badge bg-secondary" id="statusSelectorPedidoCurrentStatus">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selector de estados -->
                <div class="mb-3">
                    <h6 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-list-ul me-2 text-primary"></i>
                        Selecciona el nuevo estado:
                    </h6>
                    <div class="row g-4" id="statusSelectorOptions">
                        <!-- Los estados se cargarán dinámicamente aquí -->
                    </div>
                </div>
            </div>
            {{-- Footer removido - solo usar botón X para cerrar --}}
        </div>
    </div>
</div>

{{-- Estilos personalizados para modales profesionales --}}
<style>
/* MODAL FORCE OVERLAY - Sobreponer completamente a TODO (incluido header) */
#statusSelectorPedidoModal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 99999 !important; /* Incrementado para sobreponer header */
    background-color: rgba(0, 0, 0, 0.6) !important;
    display: none !important;
}

#statusSelectorPedidoModal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

#statusSelectorPedidoModal .modal-dialog {
    position: relative !important;
    margin: 0 !important;
    max-width: 90vw !important;
    width: 800px !important;
    z-index: 100000 !important; /* Incrementado para sobreponer header */
}

#statusSelectorPedidoModal .modal-content {
    position: relative !important;
    background-color: #fff !important;
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3) !important;
    z-index: 100001 !important; /* Incrementado para sobreponer header */
    pointer-events: auto !important;
}

/* Asegurar que todos los elementos sean interactivos */
#statusSelectorPedidoModal *,
#statusSelectorPedidoModal button,
#statusSelectorPedidoModal .btn,
#statusSelectorPedidoModal input,
#statusSelectorPedidoModal select {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10002 !important;
}

/* Botón de cerrar funcional */
#statusSelectorPedidoModal .btn-close {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10003 !important;
    opacity: 1 !important;
}

/* Animaciones para botones de estado */
.status-option-btn {
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 10002 !important;
}

.status-option-btn:hover {
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
}

.status-option-btn:active {
    transform: translateY(0) scale(0.98) !important;
}

/* Remover backdrop de Bootstrap para usar nuestro propio overlay */
#statusSelectorPedidoModal + .modal-backdrop,
.modal-backdrop {
    display: none !important;
}

/* Asegurar que ningún otro elemento interfiera */
body.modal-open {
    overflow: hidden !important;
}

/* MODAL DE ELIMINACIÓN - Sobreponer completamente a TODO (incluido header) */
#deletePedidoConfirmModal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 99998 !important; /* Incrementado para sobreponer header */
    background-color: rgba(0, 0, 0, 0.6) !important;
    display: none !important;
}

#deletePedidoConfirmModal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

#deletePedidoConfirmModal .modal-dialog {
    position: relative !important;
    margin: 0 !important;
    max-width: 90vw !important;
    width: 500px !important;
    z-index: 99999 !important; /* Incrementado para sobreponer header */
}

#deletePedidoConfirmModal .modal-content {
    position: relative !important;
    background-color: #fff !important;
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3) !important;
    z-index: 100000 !important; /* Incrementado para sobreponer header */
    pointer-events: auto !important;
}

/* Asegurar que todos los elementos del modal de eliminación sean interactivos */
#deletePedidoConfirmModal *,
#deletePedidoConfirmModal button,
#deletePedidoConfirmModal .btn,
#deletePedidoConfirmModal input,
#deletePedidoConfirmModal select {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10001 !important;
}

/* Botón de cerrar funcional para modal de eliminación */
#deletePedidoConfirmModal .btn-close {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10002 !important;
    opacity: 1 !important;
}

/* Estilos para otros modales que no sean el selector */
.modal:not(#statusSelectorPedidoModal):not(#deletePedidoConfirmModal) {
    z-index: 1050 !important;
}

/* Forzar visibilidad del contenido del modal */
#statusSelectorPedidoModal .modal-header,
#statusSelectorPedidoModal .modal-body,
#statusSelectorPedidoModal .modal-footer {
    pointer-events: auto !important;
    z-index: 10002 !important;
}

/* Asegurar que las opciones de estado sean clickeables */
#statusSelectorOptions button {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10003 !important;
    position: relative !important;
}

/* Efecto visual mejorado */
.shadow-elegant {
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
}
</style>

{{-- Container para toasts de pedidos --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="pedidosToastContainer"></div>