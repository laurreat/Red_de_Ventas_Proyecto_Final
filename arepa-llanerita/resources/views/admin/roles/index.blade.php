@extends('layouts.admin')

@section('title', 'Gestión de Roles')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Gestión de Roles y Permisos</h3>
                        <div>
                                <a href="{{ route('admin.roles.permissions') }}" class="btn btn-info btn-sm" title="Ver todos los permisos disponibles en el sistema">
                                    <i class="fas fa-key"></i> Ver Permisos
                                </a>
                                <form method="POST" action="{{ route('admin.roles.initialize') }}" style="display: inline;"
                                    onsubmit="return confirm('¿Estás seguro de inicializar los roles del sistema? Esto recreará los roles predeterminados.')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" title="Recrear roles del sistema (administrador, líder, vendedor, cliente)">
                                        <i class="fas fa-sync"></i> Inicializar Roles
                                    </button>
                                </form>
                                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo Rol
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users-cog"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Roles</span>
                                    <span class="info-box-number">{{ $stats['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Activos</span>
                                    <span class="info-box-number">{{ $stats['active'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-cog"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sistema</span>
                                    <span class="info-box-number">{{ $stats['system'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-edit"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Personalizados</span>
                                    <span class="info-box-number">{{ $stats['custom'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Buscar roles..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="active" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Sistema</option>
                                    <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Personalizados</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de roles -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Nombre Display</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Permisos</th>
                                    <th>Usuarios</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td style="color: black;">
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->system_role)
                                            <span class="badge bg-info text-white ms-1">Sistema</span>
                                        @endif
                                    </td>
                                    <td style="color: black;">{{ $role->display_name }}</td>
                                    <td style="color: black;">
                                        <small>{{ Str::limit($role->description, 50) }}</small>
                                    </td>
                                    <td style="color: black;">
                                        @if($role->system_role)
                                            <span class="badge bg-warning text-white">Sistema</span>
                                        @else
                                            <span class="badge bg-info text-white">Personalizado</span>
                                        @endif
                                    </td>
                                    <td style="color: black;">
                                        <span class="badge bg-secondary text-white">
                                            {{ count($role->permissions ?? []) }} permisos
                                        </span>
                                    </td>
                                    <td style="color: black;">
                                        <span class="badge bg-primary text-white">
                                            {{ $role->getUsersCount() }} usuarios
                                        </span>
                                    </td>
                                    <td style="color: black;">
                                        @if($role->active)
                                            <span class="badge bg-success text-white">Activo</span>
                                        @else
                                            <span class="badge bg-danger text-white">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-{{ $role->active ? 'secondary' : 'success' }} btn-sm"
                                                    onclick="toggleStatus('{{ $role->_id }}')" title="{{ $role->active ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $role->active ? 'toggle-off' : 'toggle-on' }}"></i>
                                            </button>
                                            @if($role->canBeDeleted())
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole('{{ $role->_id }}')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No se encontraron roles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $roles->appends(request()->query())->links() }}
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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/roles.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/roles-management.js') }}"></script>
@endpush