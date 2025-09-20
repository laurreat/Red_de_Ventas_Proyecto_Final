@extends('layouts.admin')

@section('title', 'Detalles del Rol')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detalles del Rol: {{ $role->display_name }}</h3>
                    <div>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre Display:</strong></td>
                                    <td>{{ $role->display_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Descripción:</strong></td>
                                    <td>{{ $role->description ?: 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        @if($role->system_role)
                                            <span class="badge bg-warning">Rol del Sistema</span>
                                        @else
                                            <span class="badge bg-info">Rol Personalizado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($role->active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Usuarios Asignados:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $role->getUsersCount() }} usuarios</span>
                                        @if($role->getUsersCount() > 0)
                                            <a href="{{ route('admin.roles.assign-users', $role) }}" class="btn btn-sm btn-outline-primary ms-2">
                                                Ver Usuarios
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Creado:</strong></td>
                                    <td>{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'No disponible' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Actualizado:</strong></td>
                                    <td>{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'No disponible' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Estadísticas</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ count($role->permissions ?? []) }}</h4>
                                            <p class="mb-0">Permisos Asignados</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $role->getUsersCount() }}</h4>
                                            <p class="mb-0">Usuarios con este Rol</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>Permisos Asignados</h5>
                    @if(empty($role->permissions))
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Este rol no tiene permisos asignados.
                        </div>
                    @else
                        <div class="row">
                            @foreach($categories as $categoryKey => $categoryName)
                                @php
                                    $categoryPermissions = array_filter($permissions, function($permission) use ($categoryKey, $role) {
                                        return $permission['category'] === $categoryKey &&
                                               in_array($permission['name'], $role->permissions ?? []);
                                    });
                                @endphp
                                @if(!empty($categoryPermissions))
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="bi bi-folder2-open text-primary me-2"></i>
                                                {{ $categoryName }}
                                                <span class="badge bg-secondary ms-2">{{ count($categoryPermissions) }}</span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($categoryPermissions as $permission)
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                                <div>
                                                    <strong>{{ $permission['display_name'] }}</strong>
                                                    @if($permission['description'])
                                                        <br><small class="text-muted">{{ $permission['description'] }}</small>
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

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Editar Rol
                            </a>
                            @if($role->getUsersCount() > 0)
                                <a href="{{ route('admin.roles.assign-users', $role) }}" class="btn btn-info">
                                    <i class="bi bi-people"></i> Gestionar Usuarios
                                </a>
                            @endif
                        </div>

                        <div>
                            @if(!$role->system_role && $role->canBeDeleted())
                                <button type="button" class="btn btn-danger" onclick="deleteRole('{{ $role->_id }}')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            @endif

                            <button type="button" class="btn btn-{{ $role->active ? 'secondary' : 'success' }}"
                                    onclick="toggleStatus('{{ $role->_id }}')">
                                <i class="bi bi-{{ $role->active ? 'toggle-off' : 'toggle-on' }}"></i>
                                {{ $role->active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formularios ocultos para acciones -->
<form id="toggleForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
function toggleStatus(roleId) {
    if (confirm('¿Estás seguro de cambiar el estado de este rol?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/roles/${roleId}/toggle`;
        form.submit();
    }
}

function deleteRole(roleId) {
    if (confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/roles/${roleId}`;
        form.submit();
    }
}
</script>
@endsection