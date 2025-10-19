{{-- Modales con Glassmorphism para Pedidos del Vendedor --}}

<style>
/* Glassmorphism Styles */
.glass-modal .modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 24px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
}

.glass-modal .modal-header {
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.95) 0%, rgba(92, 37, 43, 0.95) 100%);
    backdrop-filter: blur(10px);
    border-radius: 24px 24px 0 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem 2rem;
}

.glass-modal .modal-body {
    padding: 2rem;
}

.glass-modal .modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1.5rem 2rem;
    background: rgba(248, 249, 250, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 0 0 24px 24px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.glass-icon-container {
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.1) 0%, rgba(92, 37, 43, 0.1) 100%);
    backdrop-filter: blur(5px);
    border-radius: 50%;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    border: 2px solid rgba(114, 47, 55, 0.3);
}

.glass-status-btn {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 2px solid;
    border-radius: 16px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.glass-status-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 1);
}

.modal-backdrop.show {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.4);
}
</style>

{{-- Modal de Selección de Estado --}}
<div class="modal fade glass-modal" id="statusSelectorPedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center text-white w-100">
                    <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                         style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title mb-0 fw-bold">Cambiar Estado del Pedido</h5>
                        <small class="opacity-90">Selecciona el nuevo estado para el pedido</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                {{-- Información del pedido --}}
                <div class="glass-card mb-4">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #722f37 0%, #5c252b 100%);">
                            <i class="bi bi-basket-fill text-white fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">Pedido #<span id="statusSelectorPedidoNumero">-</span></h6>
                            <p class="mb-1 text-muted small">Cliente: <span id="statusSelectorPedidoCliente">-</span></p>
                            <div>
                                <small class="text-muted">Estado actual: </small>
                                <span class="badge" id="statusSelectorPedidoCurrentStatus">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Selector de estados --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-list-ul me-2 text-wine"></i>
                    Selecciona el nuevo estado:
                </h6>
                <div class="row g-3" id="statusSelectorOptions">
                    {{-- Los estados se cargarán dinámicamente aquí --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Cambio de Estado --}}
<div class="modal fade glass-modal" id="statusPedidoConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="statusPedidoModalHeader">
                <div class="d-flex align-items-center text-white w-100">
                    <i class="fs-4 me-2" id="statusPedidoModalIcon"></i>
                    <h5 class="modal-title mb-0 fw-bold">Cambiar Estado del Pedido</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="glass-icon-container" id="statusPedidoIconContainer">
                    <i class="fs-1" id="statusPedidoIcon"></i>
                </div>

                <h6 class="fw-bold mb-2" id="statusPedidoTitle">¿Cambiar estado del pedido?</h6>
                <p class="text-muted mb-4" id="statusPedidoMessage"></p>

                <div class="glass-card text-start">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Número:</small>
                            <strong class="d-block" id="statusPedidoNumero">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Cliente:</small>
                            <span class="d-block" id="statusPedidoCliente">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Estado actual:</small>
                            <span class="badge" id="statusPedidoCurrentStatus">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Nuevo estado:</small>
                            <span class="badge" id="statusPedidoNewStatus">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn" id="confirmStatusPedidoBtn">
                    <i class="me-1" id="statusPedidoBtnIcon"></i>
                    <span id="statusPedidoBtnText">Cambiar Estado</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Eliminación --}}
<div class="modal fade glass-modal" id="deletePedidoConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.95) 0%, rgba(176, 42, 55, 0.95) 100%);">
                <div class="d-flex align-items-center text-white w-100">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                    <h5 class="modal-title mb-0 fw-bold">Confirmar Eliminación</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="glass-icon-container" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(176, 42, 55, 0.1) 100%); border-color: rgba(220, 53, 69, 0.3);">
                    <i class="bi bi-basket-fill text-danger fs-1"></i>
                </div>

                <h6 class="fw-bold mb-2">¿Estás seguro de eliminar este pedido?</h6>
                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
                <p class="text-muted small">El pedido se eliminará permanentemente del sistema junto con todos sus detalles.</p>

                <div class="glass-card text-start mt-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Número:</small>
                            <strong class="d-block" id="deletePedidoNumero">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Cliente:</small>
                            <span class="d-block" id="deletePedidoCliente">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Total:</small>
                            <strong class="d-block text-success" id="deletePedidoTotal">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Estado:</small>
                            <span class="badge" id="deletePedidoEstado">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
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

{{-- Toast Container --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="pedidosToastContainer"></div>
