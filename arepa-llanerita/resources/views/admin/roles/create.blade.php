@extends('layouts.admin')

@section('title', '- Crear Nuevo Rol')
@section('page-title', 'Crear Nuevo Rol')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/roles-modern.css') }}?v={{ filemtime(public_path('css/admin/roles-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="roles-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="roles-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Nuevo Rol
                </h1>
                <p class="roles-subtitle">
                    Define un nuevo rol y asigna sus permisos
                </p>
            </div>
            <div class="roles-header-actions">
                <a href="{{ route('admin.roles.index') }}" class="roles-btn roles-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" id="createRoleForm" novalidate>
        @csrf

        <div class="row">
            {{-- Columna Principal --}}
            <div class="col-lg-8">
                {{-- Información Básica --}}
                <div class="role-info-card fade-in-up">
                    <div class="role-info-card-header">
                        <i class="bi bi-info-circle"></i>
                        <h3 class="role-info-card-title">Información Básica</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nombre Técnico <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="ej: gerente_ventas">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i>
                                    Sin espacios, solo letras minúsculas, números y guiones bajos
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="display_name" class="form-label">
                                    Nombre para Mostrar <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('display_name') is-invalid @enderror"
                                       id="display_name"
                                       name="display_name"
                                       value="{{ old('display_name') }}"
                                       required
                                       placeholder="ej: Gerente de Ventas">
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i>
                                    Nombre amigable que se mostrará en la interfaz
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="3"
                                          placeholder="Describe las responsabilidades y funciones de este rol...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permisos --}}
                <div class="role-info-card fade-in-up animate-delay-1">
                    <div class="role-info-card-header">
                        <i class="bi bi-key"></i>
                        <h3 class="role-info-card-title">
                            Permisos del Rol
                            <span class="role-badge role-badge-permissions ms-2" id="selectedCount">0 seleccionados</span>
                        </h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="alert alert-info mb-4" style="border-radius: 12px; border-left: 4px solid var(--info); background: rgba(59, 130, 246, 0.1);">
                            <i class="bi bi-info-circle me-2"></i>
                            Selecciona los permisos que tendrá este rol. Usa el checkbox del encabezado para seleccionar todos los permisos de una categoría.
                        </div>

                        <div class="row g-3">
                            @foreach($categories as $categoryKey => $categoryName)
                                @php
                                    $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey) {
                                        return $permission['category'] === $categoryKey;
                                    });
                                @endphp
                                @if(!empty($categoryPermissions))
                                <div class="col-md-6 mb-3">
                                    <div class="permission-category-card">
                                        <div class="permission-category-header" style="cursor: pointer; user-select: none;">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox"
                                                       class="form-check-input category-toggle me-2"
                                                       data-category="{{ $categoryKey }}"
                                                       id="category_{{ $categoryKey }}"
                                                       style="margin-top: 0;">
                                                <label for="category_{{ $categoryKey }}" class="permission-category-title mb-0" style="cursor: pointer;">
                                                    <i class="bi bi-folder2-open text-primary"></i>
                                                    {{ $categoryName }}
                                                    <span class="role-badge role-badge-permissions ms-2">
                                                        <span class="category-selected-count" data-category="{{ $categoryKey }}">0</span> / {{ count($categoryPermissions) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="permission-category-body">
                                            @foreach($categoryPermissions as $permission)
                                            <div class="form-check" style="padding-left: 1.5rem;">
                                                <input type="checkbox"
                                                       class="form-check-input permission-checkbox"
                                                       data-category="{{ $categoryKey }}"
                                                       id="permission_{{ $permission['name'] }}"
                                                       name="permissions[]"
                                                       value="{{ $permission['name'] }}"
                                                       {{ in_array($permission['name'], old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission['name'] }}" style="cursor: pointer;">
                                                    <strong>{{ $permission['display_name'] }}</strong>
                                                    @if($permission['description'])
                                                        <br><small class="text-muted">{{ $permission['description'] }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral --}}
            <div class="col-lg-4">
                {{-- Configuración --}}
                <div class="role-info-card fade-in-up animate-delay-2">
                    <div class="role-info-card-header">
                        <i class="bi bi-gear"></i>
                        <h3 class="role-info-card-title">Configuración</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="active"
                                   name="active"
                                   value="1"
                                   {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Rol Activo
                            </label>
                        </div>
                        <p class="text-muted small mt-2">
                            <i class="bi bi-info-circle"></i>
                            Un rol inactivo no puede ser asignado a nuevos usuarios
                        </p>
                    </div>
                </div>

                {{-- Resumen --}}
                <div class="role-info-card fade-in-up animate-delay-3">
                    <div class="role-info-card-header">
                        <i class="bi bi-list-check"></i>
                        <h3 class="role-info-card-title">Resumen</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="role-info-item">
                            <span class="role-info-label">Permisos Seleccionados:</span>
                            <span class="role-info-value" id="totalSelectedCount">0</span>
                        </div>

                        <div class="role-info-item">
                            <span class="role-info-label">Total Disponible:</span>
                            <span class="role-info-value">{{ count($permissions) }}</span>
                        </div>

                        <div class="role-info-item">
                            <span class="role-info-label">Categorías:</span>
                            <span class="role-info-value">{{ count($categories) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="role-info-card fade-in-up animate-delay-3">
                    <div class="role-info-card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i>
                            Crear Rol
                        </button>

                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/roles-forms.js') }}?v={{ filemtime(public_path('js/admin/roles-forms.js')) }}"></script>
@endpush
