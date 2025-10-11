{{-- Modales específicos para gestión de pedidos --}}

{{-- Modal de confirmación para eliminar pedido --}}
<div class="modal fade" id="deletePedidoModal" tabindex="-1" aria-labelledby="deletePedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="deletePedidoModalLabel">Eliminar Pedido</h5>
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
                    <p class="text-muted mb-0 small">Esta acción no se puede deshacer. El pedido se eliminará permanentemente del sistema.</p>
                </div>

                <div id="deletePedidoInfo" class="bg-light rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Número:</small>
                            <p class="mb-0 fw-bold" id="deletePedidoNumero"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cliente:</small>
                            <p class="mb-0" id="deletePedidoCliente"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total:</small>
                            <p class="mb-0 fw-bold text-success" id="deletePedidoTotal"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Estado:</small>
                            <span class="badge" id="deletePedidoEstado"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeletePedidoBtn">
                    <i class="bi bi-trash3 me-1"></i>
                    Eliminar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para cambiar estado de pedido --}}
<div class="modal fade" id="updateStatusPedidoModal" tabindex="-1" aria-labelledby="updateStatusPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="statusPedidoModalHeader">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-arrow-repeat me-2 fs-4" id="statusPedidoModalIcon"></i>
                    <h5 class="modal-title mb-0" id="updateStatusPedidoModalLabel">Cambiar Estado del Pedido</h5>
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
                            <span class="badge" id="statusPedidoEstadoActual"></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Nuevo estado:</small>
                            <span class="badge" id="statusPedidoEstadoNuevo"></span>
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
                <button type="button" class="btn" id="confirmUpdateStatusPedidoBtn">
                    <i class="bi bi-check-circle me-1" id="statusPedidoBtnIcon"></i>
                    <span id="statusPedidoBtnText">Cambiar Estado</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para guardar pedido --}}
<div class="modal fade" id="savePedidoModal" tabindex="-1" aria-labelledby="savePedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="savePedidoModalLabel">Confirmar Acción</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-basket-check-fill text-success fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="savePedidoTitle">¿Confirmar acción?</h6>
                    <p class="text-muted mb-0 small" id="savePedidoMessage">Los datos del pedido se guardarán en el sistema.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmSavePedidoBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    <span id="savePedidoBtnText">Confirmar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de detalles del pedido (vista rápida) --}}
<div class="modal fade" id="detailsPedidoModal" tabindex="-1" aria-labelledby="detailsPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-eye-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="detailsPedidoModalLabel">Detalles del Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailsPedidoContent">
                <!-- El contenido se cargará dinámicamente -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cerrar
                </button>
                <a href="#" class="btn btn-primary" id="detailsPedidoEditBtn">
                    <i class="bi bi-pencil me-1"></i>
                    Editar Pedido
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Container para toasts de pedidos --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="pedidosToastContainer"></div>