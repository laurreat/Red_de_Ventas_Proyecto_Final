@extends('layouts.admin')

@section('title', '- Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold text-white">{{ $user->name }} {{ $user->apellidos }}</h4>
                            <p class="text-white-50 mb-0">Modifica la información del usuario</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light">
                                <i class="bi bi-arrow-left me-1"></i>
                                Volver a la lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}"
          class="needs-user-confirmation"
          data-confirm-message="¿Estás seguro de actualizar la información de este usuario? Los cambios se aplicarán al sistema."
          id="editUserForm">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Información Personal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-person me-2"></i>
                            Información Personal
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('apellidos') is-invalid @enderror"
                                       id="apellidos" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" required>
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cedula') is-invalid @enderror"
                                       id="cedula" name="cedula" value="{{ old('cedula', $user->cedula) }}" required>
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       id="fecha_nacimiento" name="fecha_nacimiento"
                                       value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}" required>
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-envelope me-2"></i>
                            Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" required>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ciudad') is-invalid @enderror"
                                       id="ciudad" name="ciudad" value="{{ old('ciudad', $user->ciudad) }}" required>
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="departamento" class="form-label">Departamento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('departamento') is-invalid @enderror"
                                       id="departamento" name="departamento" value="{{ old('departamento', $user->departamento) }}" required>
                                @error('departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                          id="direccion" name="direccion" rows="3">{{ old('direccion', $user->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-shield-lock me-2"></i>
                            Cambiar Contraseña
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Deja estos campos vacíos si no deseas cambiar la contraseña
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Mínimo 8 caracteres</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                        <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración del Sistema -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-gear me-2"></i>
                            Configuración del Sistema
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select @error('rol') is-invalid @enderror"
                                    id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="administrador" {{ old('rol', $user->rol) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                <option value="lider" {{ old('rol', $user->rol) == 'lider' ? 'selected' : '' }}>Líder</option>
                                <option value="vendedor" {{ old('rol', $user->rol) == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                <option value="cliente" {{ old('rol', $user->rol) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="referido_por" class="form-label">Referido por</label>
                            <select class="form-select @error('referido_por') is-invalid @enderror"
                                    id="referido_por" name="referido_por">
                                <option value="">Sin referidor</option>
                                @foreach($posibles_referidores as $referidor)
                                    <option value="{{ $referidor->id }}"
                                            {{ old('referido_por', $user->referido_por) == $referidor->id ? 'selected' : '' }}>
                                        {{ $referidor->name }} {{ $referidor->apellidos }} ({{ ucfirst($referidor->rol) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('referido_por')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meta_mensual" class="form-label">Meta Mensual</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('meta_mensual') is-invalid @enderror"
                                       id="meta_mensual" name="meta_mensual"
                                       value="{{ old('meta_mensual', $user->meta_mensual) }}"
                                       step="0.01" min="0">
                                @error('meta_mensual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                   {{ old('activo', $user->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas MLM -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-graph-up me-2"></i>
                            Estadísticas MLM
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="ventas_mes_actual" class="form-label">Ventas Mes Actual</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('ventas_mes_actual') is-invalid @enderror"
                                       id="ventas_mes_actual" name="ventas_mes_actual"
                                       value="{{ old('ventas_mes_actual', $user->ventas_mes_actual) }}"
                                       step="0.01" min="0">
                                @error('ventas_mes_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comisiones_ganadas" class="form-label">Comisiones Ganadas</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('comisiones_ganadas') is-invalid @enderror"
                                       id="comisiones_ganadas" name="comisiones_ganadas"
                                       value="{{ old('comisiones_ganadas', $user->comisiones_ganadas) }}"
                                       step="0.01" min="0">
                                @error('comisiones_ganadas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comisiones_disponibles" class="form-label">Comisiones Disponibles</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('comisiones_disponibles') is-invalid @enderror"
                                       id="comisiones_disponibles" name="comisiones_disponibles"
                                       value="{{ old('comisiones_disponibles', $user->comisiones_disponibles) }}"
                                       step="0.01" min="0">
                                @error('comisiones_disponibles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-muted small">
                            <div class="d-flex justify-content-between">
                                <span>Total Referidos:</span>
                                <strong>{{ $user->total_referidos ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Código Referido:</span>
                                <strong>{{ $user->codigo_referido }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Registro:</span>
                                <strong>{{ $user->created_at->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>
                                Ver Detalles
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Incluir modales de confirmación para usuarios --}}
@include('admin.partials.modals-users')

@push('scripts')
<script>
// Funciones específicas para editar usuario
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        console.log('Inicializando funciones para editar usuario...');

        // Interceptar formularios que necesitan confirmación
        const formsNeedingConfirmation = document.querySelectorAll('form.needs-user-confirmation');

        formsNeedingConfirmation.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const message = this.dataset.confirmMessage || 'Los datos del usuario se actualizarán en el sistema.';
                const formId = this.id || 'editUserForm';

                if (!this.id) {
                    this.id = formId;
                }

                confirmUserSave(formId, 'Actualizar Usuario', message);
            });
        });

        // Función para confirmar guardado de usuario
        window.confirmUserSave = function(formId, title = 'Actualizar Usuario', message = 'Los datos del usuario se actualizarán en el sistema.') {
            console.log('confirmUserSave ejecutada para:', formId);

            // Actualizar contenido del modal
            const titleEl = document.getElementById('userSaveTitle');
            const messageEl = document.getElementById('userSaveMessage');
            const saveBtnText = document.getElementById('userSaveBtnText');

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;
            if (saveBtnText) saveBtnText.textContent = 'Actualizar';

            // Configurar botón de confirmación
            const confirmBtn = document.getElementById('confirmUserSaveBtn');
            if (confirmBtn) {
                confirmBtn.onclick = function() {
                    const form = document.getElementById(formId) || document.querySelector(`form[data-form-id="${formId}"]`);
                    if (form) {
                        form.submit();
                    }
                };
            }

            // Mostrar modal
            const modalElement = document.getElementById('userSaveConfirmModal');
            if (modalElement) {
                console.log('Mostrando modal de actualización para usuario');
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');

                // Crear backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        };

        // Función para cerrar modales
        window.closeUserModal = function(modalId) {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');

                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        };

        // Event listeners para cerrar modales
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) closeUserModal(modal.id);
            });
        });

        // Cerrar con backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) closeUserModal(openModal.id);
            }
        });

        // Funcionalidad para mostrar/ocultar contraseña
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        const togglePasswordConfirmationBtn = document.getElementById('togglePasswordConfirmation');
        const passwordConfirmationField = document.getElementById('password_confirmation');
        const togglePasswordConfirmationIcon = document.getElementById('togglePasswordConfirmationIcon');

        if (togglePasswordBtn && passwordField && togglePasswordIcon) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Cambiar icono
                if (type === 'text') {
                    togglePasswordIcon.classList.remove('bi-eye');
                    togglePasswordIcon.classList.add('bi-eye-slash');
                } else {
                    togglePasswordIcon.classList.remove('bi-eye-slash');
                    togglePasswordIcon.classList.add('bi-eye');
                }
            });
        }

        if (togglePasswordConfirmationBtn && passwordConfirmationField && togglePasswordConfirmationIcon) {
            togglePasswordConfirmationBtn.addEventListener('click', function() {
                const type = passwordConfirmationField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmationField.setAttribute('type', type);

                // Cambiar icono
                if (type === 'text') {
                    togglePasswordConfirmationIcon.classList.remove('bi-eye');
                    togglePasswordConfirmationIcon.classList.add('bi-eye-slash');
                } else {
                    togglePasswordConfirmationIcon.classList.remove('bi-eye-slash');
                    togglePasswordConfirmationIcon.classList.add('bi-eye');
                }
            });
        }

        console.log('Funciones de editar usuario inicializadas correctamente');
    }, 1000);
});
</script>
@endpush