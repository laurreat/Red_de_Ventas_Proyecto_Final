


<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="deleteConfirmModalLabel">Confirmar Eliminación</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-trash3-fill text-danger fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de eliminar este producto?</h6>
                    <p class="text-muted mb-0 small">Esta acción no se puede deshacer. El producto se eliminará permanentemente del sistema.</p>
                </div>

                <div id="deleteProductInfo" class="bg-light rounded p-3 mb-3">
                    <div class="d-flex align-items-center">
                        <img id="deleteProductImage" src="" alt="Producto" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-1" id="deleteProductName"></h6>
                            <small class="text-muted" id="deleteProductCategory"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash3 me-1"></i>
                    Eliminar Producto
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-labelledby="statusConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="statusModalHeader">
                <div class="d-flex align-items-center text-dark">
                    <i class="bi bi-question-circle-fill me-2 fs-4" id="statusModalIcon"></i>
                    <h5 class="modal-title mb-0" id="statusConfirmModalLabel">Cambiar Estado</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px;" id="statusIconContainer">
                        <i class="bi bi-toggle-on fs-1" id="statusIcon"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="statusTitle">¿Deseas cambiar el estado del producto?</h6>
                    <p class="text-muted mb-0 small" id="statusMessage"></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn" id="confirmStatusBtn">
                    <i class="bi bi-toggle-on me-1" id="statusBtnIcon"></i>
                    <span id="statusBtnText">Cambiar Estado</span>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="saveConfirmModal" tabindex="-1" aria-labelledby="saveConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="saveConfirmModalLabel">Confirmar Guardado</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-save-fill text-success fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de guardar los cambios?</h6>
                    <p class="text-muted mb-0 small" id="saveMessage">Los cambios realizados se guardarán en el sistema.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmSaveBtn">
                    <i class="bi bi-save me-1"></i>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>


<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="toastContainer"></div><?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/admin/partials/modals.blade.php ENDPATH**/ ?>