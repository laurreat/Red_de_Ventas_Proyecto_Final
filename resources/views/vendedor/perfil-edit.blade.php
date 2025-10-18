@extends('layouts.vendedor')

@section('title', 'Editar Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/perfil-modern.css') }}?v={{ filemtime(public_path('css/vendedor/perfil-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    
    {{-- Header --}}
    <div class="perfil-header fade-in-up" style="margin-bottom: 2rem;">
        <div class="perfil-header-content" style="justify-content: space-between;">
            <div>
                <h1 style="margin-bottom: 0.5rem;"><i class="bi bi-pencil-square"></i> Editar Mi Perfil</h1>
                <p style="opacity: 0.9; margin: 0;">Actualiza tu información personal y profesional</p>
            </div>
            <a href="{{ route('vendedor.perfil.index') }}" class="perfil-action-btn perfil-action-btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Perfil
            </a>
        </div>
    </div>
    
    {{-- Formulario de Edición --}}
    <div class="perfil-form-container scale-in">
        <form action="{{ route('vendedor.perfil.update') }}" method="POST" enctype="multipart/form-data" class="perfil-form" id="editPerfilForm">
            @csrf
            @method('PUT')
            
            {{-- Avatar Upload --}}
            <div class="perfil-avatar-upload">
                <div class="perfil-avatar-preview">
                    @if($vendedor->avatar)
                        <img src="{{ Storage::url($vendedor->avatar) }}" alt="{{ $vendedor->name }}" id="avatarPreview">
                    @else
                        <div class="avatar-placeholder" id="avatarPreview">
                            {{ strtoupper(substr($vendedor->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <div class="perfil-avatar-actions">
                    <h4>Foto de Perfil</h4>
                    <p>JPG, PNG o GIF. Máximo 2MB.</p>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                    <div class="perfil-avatar-btns">
                        <button type="button" class="perfil-btn-upload" id="uploadAvatarBtn">
                            <i class="bi bi-cloud-upload"></i> Subir Foto
                        </button>
                        @if($vendedor->avatar)
                        <form action="{{ route('vendedor.perfil.eliminar-avatar') }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="perfil-btn-remove" onclick="return confirm('¿Estás seguro de eliminar tu avatar?')">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Información Personal --}}
            <div class="perfil-form-section">
                <h3><i class="bi bi-person-badge"></i> Información Personal</h3>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group">
                        <label class="perfil-form-label required">Nombre</label>
                        <input type="text" name="name" class="perfil-form-control" value="{{ old('name', $vendedor->name) }}" required>
                        @error('name')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="perfil-form-control" value="{{ old('apellidos', $vendedor->apellidos) }}">
                        @error('apellidos')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Cédula</label>
                        <input type="text" name="cedula" class="perfil-form-control" value="{{ old('cedula', $vendedor->cedula) }}">
                        @error('cedula')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="perfil-form-control" value="{{ old('fecha_nacimiento', $vendedor->fecha_nacimiento ? \Carbon\Carbon::parse($vendedor->fecha_nacimiento)->format('Y-m-d') : '') }}">
                        @error('fecha_nacimiento')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group" style="grid-column: 1 / -1;">
                        <label class="perfil-form-label">Biografía</label>
                        <textarea name="biografia" class="perfil-form-control" rows="3" maxlength="1000">{{ old('biografia', $vendedor->biografia ?? $vendedor->bio) }}</textarea>
                        <span class="perfil-form-help">Máximo 1000 caracteres</span>
                        @error('biografia')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            {{-- Información de Contacto --}}
            <div class="perfil-form-section">
                <h3><i class="bi bi-telephone"></i> Información de Contacto</h3>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group">
                        <label class="perfil-form-label required">Email</label>
                        <input type="email" name="email" class="perfil-form-control" value="{{ old('email', $vendedor->email) }}" required>
                        <span class="perfil-form-help">Este será tu email de inicio de sesión</span>
                        @error('email')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Teléfono</label>
                        <input type="tel" name="telefono" class="perfil-form-control" value="{{ old('telefono', $vendedor->telefono) }}" placeholder="+57 300 123 4567">
                        @error('telefono')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group" style="grid-column: 1 / -1;">
                        <label class="perfil-form-label">Dirección</label>
                        <input type="text" name="direccion" class="perfil-form-control" value="{{ old('direccion', $vendedor->direccion) }}" placeholder="Calle 123 #45-67">
                        @error('direccion')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="perfil-form-row">
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="perfil-form-control" value="{{ old('ciudad', $vendedor->ciudad) }}" placeholder="Bogotá">
                        @error('ciudad')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="perfil-form-group">
                        <label class="perfil-form-label">Departamento</label>
                        <input type="text" name="departamento" class="perfil-form-control" value="{{ old('departamento', $vendedor->departamento) }}" placeholder="Cundinamarca">
                        @error('departamento')
                            <div class="perfil-form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            {{-- Botones de Acción --}}
            <div class="perfil-form-actions">
                <a href="{{ route('vendedor.perfil.index') }}" class="perfil-btn perfil-btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="perfil-btn perfil-btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    
    {{-- Información Adicional --}}
    <div class="perfil-info-grid" style="margin-top: 2rem;">
        <div class="perfil-info-section scale-in animate-delay-1">
            <h3><i class="bi bi-shield-check"></i> Seguridad</h3>
            <p style="color: var(--gray-600); font-size: 0.9rem; margin-bottom: 1rem;">
                Mantén tu cuenta segura actualizando tu contraseña regularmente.
            </p>
            <button onclick="perfilManager.showModal('change-password-modal')" class="perfil-btn perfil-btn-secondary" style="width: 100%;">
                <i class="bi bi-key"></i> Cambiar Contraseña
            </button>
        </div>
        
        <div class="perfil-info-section scale-in animate-delay-2">
            <h3><i class="bi bi-info-circle"></i> Información</h3>
            <div class="perfil-info-item">
                <span class="perfil-info-label">Cuenta Creada</span>
                <span class="perfil-info-value">{{ $vendedor->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="perfil-info-item">
                <span class="perfil-info-label">Último Acceso</span>
                <span class="perfil-info-value">{{ $vendedor->ultimo_acceso ? \Carbon\Carbon::parse($vendedor->ultimo_acceso)->diffForHumans() : 'N/A' }}</span>
            </div>
            <div class="perfil-info-item">
                <span class="perfil-info-label">Estado</span>
                <span class="perfil-info-value">
                    @if($vendedor->activo)
                        <span class="perfil-badge perfil-badge-success">Activo</span>
                    @else
                        <span class="perfil-badge perfil-badge-danger">Inactivo</span>
                    @endif
                </span>
            </div>
        </div>
        
        <div class="perfil-info-section scale-in animate-delay-3">
            <h3><i class="bi bi-lightbulb"></i> Consejos</h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: var(--gray-600); font-size: 0.9rem;">
                <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>Mantén tu información actualizada</span>
                </li>
                <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>Usa una foto de perfil profesional</span>
                </li>
                <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>Completa todos los campos</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="perfil-modal-backdrop" id="change-password-modal">
    <div class="perfil-modal">
        <div class="perfil-modal-header">
            <h3><i class="bi bi-shield-lock"></i> Cambiar Contraseña</h3>
            <button class="perfil-modal-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <form action="{{ route('vendedor.perfil.update-password') }}" method="POST" id="passwordForm" class="perfil-form">
            @csrf
            @method('PUT')
            <div class="perfil-modal-body">
                <div class="perfil-form-group">
                    <label class="perfil-form-label required">Contraseña Actual</label>
                    <input type="password" name="current_password" class="perfil-form-control" required>
                </div>
                
                <div class="perfil-form-group" style="margin-top: 1rem;">
                    <label class="perfil-form-label required">Nueva Contraseña</label>
                    <input type="password" name="password" id="password" class="perfil-form-control" required>
                    <span class="perfil-form-help">Mínimo 8 caracteres</span>
                </div>
                
                <div class="perfil-form-group" style="margin-top: 1rem;">
                    <label class="perfil-form-label required">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="perfil-form-control" required>
                </div>
            </div>
            <div class="perfil-modal-footer">
                <button type="button" class="perfil-btn perfil-btn-secondary" onclick="perfilManager.closeModal(document.getElementById('change-password-modal'))">
                    Cancelar
                </button>
                <button type="submit" class="perfil-btn perfil-btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Container --}}
<div class="perfil-toast-container"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('error') }}', 'danger');
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('Por favor corrige los errores en el formulario', 'warning');
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/perfil-modern.js') }}?v={{ filemtime(public_path('js/vendedor/perfil-modern.js')) }}"></script>
@endpush
