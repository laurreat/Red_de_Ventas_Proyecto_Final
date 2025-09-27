{{-- Modales de confirmación para usuarios --}}

{{-- Modal de confirmación para cambiar estado de usuario --}}
<div class="modal fade" id="userStatusConfirmModal" tabindex="-1" aria-labelledby="userStatusConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="userStatusModalHeader">
                <div class="d-flex align-items-center text-dark">
                    <i class="bi bi-question-circle-fill me-2 fs-4" id="userStatusModalIcon"></i>
                    <h5 class="modal-title mb-0" id="userStatusConfirmModalLabel">Cambiar Estado del Usuario</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px;" id="userStatusIconContainer">
                        <i class="bi bi-person-check-fill fs-1" id="userStatusIcon"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="userStatusTitle">¿Deseas cambiar el estado de este usuario?</h6>
                    <p class="text-muted mb-0 small" id="userStatusMessage"></p>
                </div>

                <div id="userStatusInfo" class="bg-light rounded p-3 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <div class="avatar-title rounded-circle d-flex align-items-center justify-content-center"
                                 style="background: var(--primary-color); color: white;" id="userAvatar">
                                U
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1" id="userStatusName">Usuario</h6>
                            <small class="text-muted" id="userStatusEmail">usuario@email.com</small><br>
                            <small class="text-muted">Rol: <span id="userStatusRole">Usuario</span></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn" id="confirmUserStatusBtn">
                    <i class="bi bi-person-check me-1" id="userStatusBtnIcon"></i>
                    <span id="userStatusBtnText">Cambiar Estado</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para guardar usuario --}}
<div class="modal fade" id="userSaveConfirmModal" tabindex="-1" aria-labelledby="userSaveConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-person-check-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="userSaveConfirmModalLabel">Confirmar Acción</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-person-plus-fill text-success fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="userSaveTitle">¿Estás seguro de realizar esta acción?</h6>
                    <p class="text-muted mb-0 small" id="userSaveMessage">Los datos del usuario se guardarán en el sistema.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmUserSaveBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    <span id="userSaveBtnText">Confirmar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para actualizar perfil --}}
<div class="modal fade" id="profileUpdateConfirmModal" tabindex="-1" aria-labelledby="profileUpdateConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-person-gear me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="profileUpdateConfirmModalLabel">Actualizar Perfil</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(23, 162, 184, 0.1);">
                        <i class="bi bi-person-lines-fill text-info fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de actualizar tu perfil?</h6>
                    <p class="text-muted mb-0 small" id="profileUpdateMessage">Los cambios se aplicarán a tu información personal.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-info" id="confirmProfileUpdateBtn">
                    <i class="bi bi-person-check me-1"></i>
                    Actualizar Perfil
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Container para toasts específicos de usuarios --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="userToastContainer"></div>