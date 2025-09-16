@extends('layouts.admin')

@section('title', '- Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra todos los usuarios del sistema</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>
                        Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0 small">Total Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-shield-check fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['administradores'] }}</h3>
                    <p class="text-muted mb-0 small">Administradores</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 202, 240, 0.1);">
                        <i class="bi bi-person-badge fs-2 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-info">{{ $stats['lideres'] }}</h3>
                    <p class="text-muted mb-0 small">Líderes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-person-workspace fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['vendedores'] }}</h3>
                    <p class="text-muted mb-0 small">Vendedores</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(108, 117, 125, 0.1);">
                        <i class="bi bi-person fs-2 text-secondary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-secondary">{{ $stats['clientes'] }}</h3>
                    <p class="text-muted mb-0 small">Clientes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['activos'] }}</h3>
                    <p class="text-muted mb-0 small">Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Buscar</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nombre, email, cédula..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="administrador" {{ request('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                    <option value="lider" {{ request('rol') == 'lider' ? 'selected' : '' }}>Líder</option>
                                    <option value="vendedor" {{ request('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                    <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select name="activo" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Buscar
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-people me-2"></i>
                        Lista de Usuarios ({{ $usuarios->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($usuarios->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Contacto</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Referidos</th>
                                        <th>Registro</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <div class="avatar-title rounded-circle d-flex align-items-center justify-content-center"
                                                             style="background: var(--primary-color); color: white;">
                                                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $usuario->name }} {{ $usuario->apellidos }}</h6>
                                                        <small class="text-muted">{{ $usuario->cedula }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $usuario->email }}</div>
                                                    <small class="text-muted">{{ $usuario->telefono }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $roleColors = [
                                                        'administrador' => 'success',
                                                        'lider' => 'info',
                                                        'vendedor' => 'warning',
                                                        'cliente' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $roleColors[$usuario->rol] ?? 'secondary' }}">
                                                    {{ ucfirst($usuario->rol) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $usuario->activo ? 'success' : 'danger' }}">
                                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $usuario->total_referidos ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $usuario->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.users.show', $usuario) }}"
                                                       class="btn btn-outline-info" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $usuario) }}"
                                                       class="btn btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('admin.users.toggle-active', $usuario) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-outline-{{ $usuario->activo ? 'warning' : 'success' }}"
                                                                title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                                            <i class="bi bi-{{ $usuario->activo ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if($usuarios->hasPages())
                            <div class="d-flex justify-content-center mt-4 mb-3">
                                {{ $usuarios->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay usuarios</h4>
                            <p class="text-muted">No se encontraron usuarios con los criterios especificados.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="bi bi-person-plus me-1"></i>
                                Crear primer usuario
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-title {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}
</style>
@endsection