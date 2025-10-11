@extends('layouts.admin')

@section('title', '- Crear Usuario')
@section('page-title', 'Crear Nuevo Usuario')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modules/users.css') }}?v={{ filemtime(public_path('css/modules/users.css')) }}">
<style>
.form-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.form-section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.form-section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(114, 47, 55, 0.1);
    color: #722F37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.form-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #722F37;
    box-shadow: 0 0 0 3px rgba(114, 47, 55, 0.1);
    outline: none;
}

.input-group-text {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    color: #6b7280;
}

.password-toggle {
    cursor: pointer;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-left: none;
    color: #6b7280;
    transition: all 0.15s ease-in-out;
}

.password-toggle:hover {
    background: #f3f4f6;
    color: #374151;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .form-section {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column-reverse;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="users-header fade-in-up">
        <div class="users-header-content">
            <div>
                <h1 class="users-header-title">Crear Nuevo Usuario</h1>
                <p class="users-header-subtitle">Completa el formulario para agregar un nuevo usuario al sistema</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn-user-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Volver a la lista
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
        @csrf

        <div class="row">
            {{-- Columna Principal --}}
            <div class="col-lg-8">
                {{-- Información Personal --}}
                <div class="form-section fade-in-up">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <h3 class="form-section-title">Información Personal</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="Ej: Juan Carlos">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('apellidos') is-invalid @enderror"
                                   id="apellidos"
                                   name="apellidos"
                                   value="{{ old('apellidos') }}"
                                   required
                                   placeholder="Ej: Pérez García">
                            @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('cedula') is-invalid @enderror"
                                   id="cedula"
                                   name="cedula"
                                   value="{{ old('cedula') }}"
                                   required
                                   placeholder="Ej: 12345678">
                            @error('cedula')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   id="fecha_nacimiento"
                                   name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento') }}"
                                   required>
                            @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Información de Contacto --}}
                <div class="form-section fade-in-up animate-delay-1">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h3 class="form-section-title">Información de Contacto</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="ejemplo@correo.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono') }}"
                                   required
                                   placeholder="+57 300 123 4567">
                            @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('ciudad') is-invalid @enderror"
                                   id="ciudad"
                                   name="ciudad"
                                   value="{{ old('ciudad') }}"
                                   required
                                   placeholder="Ej: Bogotá">
                            @error('ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('departamento') is-invalid @enderror"
                                   id="departamento"
                                   name="departamento"
                                   value="{{ old('departamento') }}"
                                   required
                                   placeholder="Ej: Cundinamarca">
                            @error('departamento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror"
                                      id="direccion"
                                      name="direccion"
                                      rows="3"
                                      placeholder="Calle, número, barrio...">{{ old('direccion') }}</textarea>
                            @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Seguridad --}}
                <div class="form-section fade-in-up animate-delay-2">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h3 class="form-section-title">Seguridad</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required>
                                <button class="btn password-toggle" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 8 caracteres con mayúsculas, minúsculas y números</small>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required>
                                <button class="btn password-toggle" type="button" id="togglePasswordConfirmation">
                                    <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral --}}
            <div class="col-lg-4">
                {{-- Configuración del Sistema --}}
                <div class="form-section fade-in-up animate-delay-3">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3 class="form-section-title">Configuración</h3>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select @error('rol') is-invalid @enderror"
                                id="rol"
                                name="rol"
                                required>
                            <option value="">Seleccionar rol...</option>
                            <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="lider" {{ old('rol') == 'lider' ? 'selected' : '' }}>Líder</option>
                            <option value="vendedor" {{ old('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="cliente" {{ old('rol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </select>
                        @error('rol')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="referido_por" class="form-label">Referido por</label>
                        <select class="form-select @error('referido_por') is-invalid @enderror"
                                id="referido_por"
                                name="referido_por">
                            <option value="">Sin referidor</option>
                            @php
                            $posibles_referidores = \App\Models\User::whereIn('rol', ['administrador', 'lider', 'vendedor'])->get();
                            @endphp
                            @foreach($posibles_referidores as $referidor)
                            <option value="{{ $referidor->_id }}" {{ old('referido_por') == $referidor->_id ? 'selected' : '' }}>
                                {{ $referidor->name }} {{ $referidor->apellidos ?? '' }} ({{ ucfirst($referidor->rol) }})
                            </option>
                            @endforeach
                        </select>
                        @error('referido_por')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opcional: Selecciona quién refirió a este usuario</small>
                    </div>

                    <div class="mb-3">
                        <label for="meta_mensual" class="form-label">Meta Mensual</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   class="form-control @error('meta_mensual') is-invalid @enderror"
                                   id="meta_mensual"
                                   name="meta_mensual"
                                   value="{{ old('meta_mensual', 0) }}"
                                   step="0.01"
                                   min="0">
                        </div>
                        @error('meta_mensual')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Solo para vendedores y líderes</small>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="activo"
                               name="activo"
                               value="1"
                               {{ old('activo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">
                            Usuario activo
                        </label>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="form-actions">
                    <button type="submit" class="btn-user-primary" style="width: 100%;">
                        <i class="bi bi-check-circle"></i>
                        Crear Usuario
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-user-secondary" style="width: 100%;">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/users-management.js') }}?v={{ filemtime(public_path('js/modules/users-management.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const setupPasswordToggle = (buttonId, inputId, iconId) => {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (button && input && icon) {
            button.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                if (type === 'text') {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        }
    };

    setupPasswordToggle('togglePassword', 'password', 'togglePasswordIcon');
    setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation', 'togglePasswordConfirmationIcon');

    // Performance monitoring
    console.log('✅ Formulario de creación de usuario cargado');
});
</script>
@endpush
