{{-- Modales específicos para Mi Perfil --}}

{{-- Modal de confirmación para actualizar información personal --}}
<div class="modal fade" id="profileInfoConfirmModal" tabindex="-1" aria-labelledby="profileInfoConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-person-gear me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="profileInfoConfirmModalLabel">Actualizar Información Personal</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(23, 162, 184, 0.1);">
                        <i class="bi bi-person-lines-fill text-info fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de actualizar tu información personal?</h6>
                    <p class="text-muted mb-0 small" id="profileInfoMessage">Los cambios se aplicarán a tu perfil y serán visibles en el sistema.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-info" id="confirmProfileInfoBtn">
                    <i class="bi bi-person-check me-1"></i>
                    Actualizar Información
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para cambiar contraseña --}}
<div class="modal fade" id="passwordChangeConfirmModal" tabindex="-1" aria-labelledby="passwordChangeConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                <div class="d-flex align-items-center text-dark">
                    <i class="bi bi-shield-lock me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="passwordChangeConfirmModalLabel">Cambiar Contraseña</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-key-fill text-warning fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de cambiar tu contraseña?</h6>
                    <p class="text-muted mb-0 small" id="passwordChangeMessage">Esta acción es irreversible. Asegúrate de recordar tu nueva contraseña.</p>
                </div>

                <div class="alert alert-warning border-0" style="background-color: rgba(255, 193, 7, 0.1);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        <small><strong>Importante:</strong> Después del cambio deberás iniciar sesión nuevamente.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmPasswordChangeBtn">
                    <i class="bi bi-shield-check me-1"></i>
                    Cambiar Contraseña
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para actualizar notificaciones --}}
<div class="modal fade" id="notificationsConfirmModal" tabindex="-1" aria-labelledby="notificationsConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-bell-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="notificationsConfirmModalLabel">Actualizar Notificaciones</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-gear-fill text-success fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Estás seguro de actualizar tus notificaciones?</h6>
                    <p class="text-muted mb-0 small" id="notificationsMessage">Se aplicarán las nuevas preferencias de notificación.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmNotificationsBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Actualizar Preferencias
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Container para toasts específicos de perfil --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;" id="profileToastContainer"></div>