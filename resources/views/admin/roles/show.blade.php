@extends('layouts.admin')

@section('title', '- Detalles del Rol')
@section('page-title', 'Detalles del Rol')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/roles-modern.css') }}?v={{ filemtime(public_path('css/admin/roles-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="role-detail-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="role-detail-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h1 class="role-detail-name">{{ $role->display_name }}</h1>
                    <p class="role-detail-description">
                        {{ $role->description ?: 'Sin descripción' }}
                    </p>
                    <div class="mt-2" style="display: flex; gap: 0.5rem;">
                        @if($role->system_role)
                            <span class="role-badge role-badge-system">
                                <i class="bi bi-gear-fill"></i>
                                Rol del Sistema
                            </span>
                        @else
                            <span class="role-badge role-badge-custom">
                                <i class="bi bi-star-fill"></i>
                                Rol Personalizado
                            </span>
                        @endif

                        @if($role->active)
                            <span class="role-badge role-badge-active">
                                <i class="bi bi-check-circle-fill"></i>
                                Activo
                            </span>
                        @else
                            <span class="role-badge role-badge-inactive">
                                <i class="bi bi-x-circle-fill"></i>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="roles-header-actions">
                <a href="{{ route('admin.roles.edit', $role) }}" class="roles-btn roles-btn-white">
                    <i class="bi bi-pencil"></i>
                    Editar
                </a>
                <a href="{{ route('admin.roles.index') }}" class="roles-btn roles-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="role-stat-card fade-in-up">
                <div class="role-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="bi bi-key"></i>
                </div>
                <div class="role-stat-value">{{ count($role->permissions ?? []) }}</div>
                <div class="role-stat-label">Permisos Asignados</div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-1">
                <div class="role-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-people"></i>
                </div>
                <div class="role-stat-value">{{ $role->getUsersCount() }}</div>
                <div class="role-stat-label">Usuarios con este Rol</div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-2">
                <div class="role-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="role-stat-value">{{ count($categories) }}</div>
                <div class="role-stat-label">Categorías de Permisos</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Columna Principal --}}
        <div class="col-lg-8">
            {{-- Permisos Asignados --}}
            <div class="role-info-card fade-in-up">
                <div class="role-info-card-header">
                    <i class="bi bi-key"></i>
                    <h3 class="role-info-card-title">Permisos Asignados</h3>
                </div>
                <div class="role-info-card-body">
                    @if(empty($role->permissions))
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Este rol no tiene permisos asignados.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($categories as $categoryKey => $categoryName)
                                @php
                                    $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey, $role) {
                                        return $permission['category'] === $categoryKey &&
                                               in_array($permission['name'], $role->permissions ?? []);
                                    });
                                @endphp
                                @if(!empty($categoryPermissions))
                                <div class="col-md-6 mb-3">
                                    <div class="permission-category-card">
                                        <div class="permission-category-header">
                                            <h6 class="permission-category-title">
                                                <i class="bi bi-folder2-open text-primary"></i>
                                                {{ $categoryName }}
                                                <span class="role-badge role-badge-permissions ms-2">
                                                    {{ count($categoryPermissions) }}
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="permission-category-body">
                                            @foreach($categoryPermissions as $permission)
                                            <div class="permission-item">
                                                <div class="permission-icon">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div class="permission-name">{{ $permission['display_name'] }}</div>
                                                    @if($permission['description'])
                                                        <div class="permission-description">{{ $permission['description'] }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral --}}
        <div class="col-lg-4">
            {{-- Información General --}}
            <div class="role-info-card fade-in-up animate-delay-1">
                <div class="role-info-card-header">
                    <i class="bi bi-info-circle"></i>
                    <h3 class="role-info-card-title">Información General</h3>
                </div>
                <div class="role-info-card-body">
                    <div class="role-info-item">
                        <span class="role-info-label">Nombre Técnico:</span>
                        <span class="role-info-value">{{ $role->name }}</span>
                    </div>

                    <div class="role-info-item">
                        <span class="role-info-label">Nombre Display:</span>
                        <span class="role-info-value">{{ $role->display_name }}</span>
                    </div>

                    <div class="role-info-item">
                        <span class="role-info-label">Tipo:</span>
                        <span class="role-info-value">
                            @if($role->system_role)
                                <span class="role-badge role-badge-system">Sistema</span>
                            @else
                                <span class="role-badge role-badge-custom">Personalizado</span>
                            @endif
                        </span>
                    </div>

                    <div class="role-info-item">
                        <span class="role-info-label">Estado:</span>
                        <span class="role-info-value">
                            @if($role->active)
                                <span class="role-badge role-badge-active">Activo</span>
                            @else
                                <span class="role-badge role-badge-inactive">Inactivo</span>
                            @endif
                        </span>
                    </div>

                    <div class="role-info-item">
                        <span class="role-info-label">Creado:</span>
                        <span class="role-info-value">{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>

                    <div class="role-info-item">
                        <span class="role-info-label">Actualizado:</span>
                        <span class="role-info-value">{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Usuarios Asignados --}}
            @if($role->getUsersCount() > 0)
            <div class="role-info-card fade-in-up animate-delay-2">
                <div class="role-info-card-header">
                    <i class="bi bi-people"></i>
                    <h3 class="role-info-card-title">Usuarios Asignados</h3>
                </div>
                <div class="role-info-card-body">
                    <div class="text-center mb-3">
                        <div style="font-size: 2.5rem; color: var(--info);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div style="font-size: 2rem; font-weight: 700; color: var(--gray-900); margin-top: 0.5rem;">
                            {{ $role->getUsersCount() }}
                        </div>
                        <div style="color: var(--gray-500); font-size: 0.875rem;">
                            {{ $role->getUsersCount() === 1 ? 'usuario tiene' : 'usuarios tienen' }} este rol
                        </div>
                    </div>

                    <a href="{{ route('admin.roles.assign-users', $role) }}"
                       class="btn btn-primary w-100">
                        <i class="bi bi-person-gear"></i>
                        Gestionar Usuarios
                    </a>
                </div>
            </div>
            @endif

            {{-- Acciones --}}
            <div class="role-info-card fade-in-up animate-delay-3">
                <div class="role-info-card-body">
                    <a href="{{ route('admin.roles.edit', $role) }}"
                       class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i>
                        Editar Rol
                    </a>

                    <button type="button"
                            class="btn btn-{{ $role->active ? 'secondary' : 'success' }} w-100 mb-2"
                            data-action="toggle-status"
                            data-role-id="{{ $role->_id }}"
                            data-active="{{ $role->active ? 'true' : 'false' }}">
                        <i class="bi bi-{{ $role->active ? 'toggle-off' : 'toggle-on' }}"></i>
                        {{ $role->active ? 'Desactivar Rol' : 'Activar Rol' }}
                    </button>

                    @if(!$role->system_role && $role->canBeDeleted())
                        <button type="button"
                                class="btn btn-danger w-100 mb-2"
                                data-action="delete-role"
                                data-role-id="{{ $role->_id }}"
                                data-role-name="{{ $role->display_name }}">
                            <i class="bi bi-trash"></i>
                            Eliminar Rol
                        </button>
                    @endif

                    <a href="{{ route('admin.roles.index') }}"
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i>
                        Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/roles-modern.js') }}?v={{ filemtime(public_path('js/admin/roles-modern.js')) }}"></script>
@endpush
