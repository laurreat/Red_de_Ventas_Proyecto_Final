{{-- Mensajes de éxito --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid #28a745 !important;">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 fw-bold">¡Éxito!</h6>
                <p class="mb-0">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensajes de error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid #dc3545 !important;">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 fw-bold">¡Error!</h6>
                <p class="mb-0">{{ session('error') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensajes de advertencia --}}
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid #ffc107 !important;">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 fw-bold">¡Advertencia!</h6>
                <p class="mb-0">{{ session('warning') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Mensajes informativos --}}
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid #17a2b8 !important;">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-info-circle-fill text-info fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 fw-bold">Información</h6>
                <p class="mb-0">{{ session('info') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-left: 4px solid #dc3545 !important;">
        <div class="d-flex align-items-start">
            <div class="me-3 mt-1">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2 fw-bold">¡Hay problemas en el formulario!</h6>
                <p class="mb-2 small">Por favor, revisa los siguientes errores:</p>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Modal de confirmación para eliminar --}}
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

                <div id="productInfo" class="bg-light rounded p-3 mb-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <img id="productImage" src="" alt="" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-1" id="productName"></h6>
                            <small class="text-muted" id="productCategory"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3 me-1"></i>
                        Eliminar Producto
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para cambiar estado --}}
<div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-labelledby="statusConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                <div class="d-flex align-items-center text-dark">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="statusConfirmModalLabel">Cambiar Estado</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-toggle-on text-warning fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas cambiar el estado del producto?</h6>
                    <p class="text-muted mb-0 small" id="statusMessage"></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <form id="statusForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-toggle-on me-1"></i>
                        Cambiar Estado
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Los estilos y scripts están en archivos externos:
     CSS: public/css/admin/messages.css
     JS: public/js/admin/messages-management.js
     Incluir en la vista principal con @push('styles') y @push('scripts') --}}