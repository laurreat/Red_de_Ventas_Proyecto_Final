@extends('layouts.admin')

@section('title', '- Gestión de Roles')
@section('page-title', 'Gestión de Roles')

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
                    <i class="bi bi-shield-lock me-2"></i>
                    Gestión de Roles y Permisos
                </h1>
                <p class="roles-subtitle">
                    Administra los roles y permisos del sistema
                </p>
            </div>
            <div class="roles-header-actions">
                <a href="{{ route('admin.roles.permissions') }}" class="roles-btn roles-btn-outline" title="Ver todos los permisos disponibles">
                    <i class="bi bi-key"></i>
                    Ver Permisos
                </a>

                <button type="button"
                        class="roles-btn roles-btn-warning"
                        data-action="initialize-roles"
                        title="Recrear roles del sistema">
                    <i class="bi bi-arrow-repeat"></i>
                    Inicializar Roles
                </button>

                <a href="{{ route('admin.roles.create') }}" class="roles-btn roles-btn-white">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo Rol
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up">
                <div class="role-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="role-stat-value">{{ $stats['total'] }}</div>
                <div class="role-stat-label">Total de Roles</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-1">
                <div class="role-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="role-stat-value">{{ $stats['active'] }}</div>
                <div class="role-stat-label">Roles Activos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-2">
                <div class="role-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-gear"></i>
                </div>
                <div class="role-stat-value">{{ $stats['system'] }}</div>
                <div class="role-stat-label">Roles del Sistema</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="role-stat-card fade-in-up animate-delay-3">
                <div class="role-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="bi bi-star"></i>
                </div>
                <div class="role-stat-value">{{ $stats['custom'] }}</div>
                <div class="role-stat-label">Roles Personalizados</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="roles-filters fade-in-up">
        <form method="GET" action="{{ route('admin.roles.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por nombre o descripción..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="active" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Sistema</option>
                        <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Personalizados</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Roles Table --}}
    <div class="roles-table-container fade-in-up">
        <div class="table-responsive">
            <table class="roles-table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Permisos</th>
                        <th>Usuarios</th>
                        <th>Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <div class="role-name">{{ $role->display_name }}</div>
                            <small class="text-muted">{{ $role->name }}</small>
                        </td>

                        <td>
                            <small class="text-truncate" style="max-width: 200px; display: block;">
                                {{ $role->description ?: 'Sin descripción' }}
                            </small>
                        </td>

                        <td>
                            @if($role->system_role)
                                <span class="role-badge role-badge-system">
                                    <i class="bi bi-gear-fill"></i>
                                    Sistema
                                </span>
                            @else
                                <span class="role-badge role-badge-custom">
                                    <i class="bi bi-star-fill"></i>
                                    Personalizado
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="role-badge role-badge-permissions">
                                <i class="bi bi-key"></i>
                                {{ count($role->permissions ?? []) }}
                            </span>
                        </td>

                        <td>
                            <span class="role-badge role-badge-users">
                                <i class="bi bi-people"></i>
                                {{ $role->getUsersCount() }}
                            </span>
                        </td>

                        <td>
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
                        </td>

                        <td>
                            <div class="role-actions" style="justify-content: center;">
                                <a href="{{ route('admin.roles.show', $role) }}"
                                   class="role-action-btn role-action-btn-view"
                                   title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="role-action-btn role-action-btn-edit"
                                   title="Editar rol">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <button type="button"
                                        class="role-action-btn role-action-btn-toggle"
                                        data-action="toggle-status"
                                        data-role-id="{{ $role->_id }}"
                                        data-active="{{ $role->active ? 'true' : 'false' }}"
                                        title="{{ $role->active ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi bi-{{ $role->active ? 'toggle-off' : 'toggle-on' }}"></i>
                                </button>

                                @if($role->canBeDeleted())
                                <button type="button"
                                        class="role-action-btn role-action-btn-delete"
                                        data-action="delete-role"
                                        data-role-id="{{ $role->_id }}"
                                        data-role-name="{{ $role->display_name }}"
                                        title="Eliminar rol">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem;">
                            <div style="color: var(--gray-500);">
                                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                <p class="mb-0">No se encontraron roles con los filtros seleccionados</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($roles->hasPages())
        <div class="p-3 border-top">
            <div class="d-flex justify-content-center">
                {{ $roles->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/roles-modern.js') }}?v={{ filemtime(public_path('js/admin/roles-modern.js')) }}"></script>
@endpush
