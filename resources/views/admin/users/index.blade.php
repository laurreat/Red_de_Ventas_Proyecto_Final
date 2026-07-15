@extends('layouts.admin')

@section('title', '- Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modules/users.css') }}?v={{ filemtime(public_path('css/modules/users.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="users-header fade-in-up">
        <div class="users-header-content">
            <div>
                <h1 class="users-header-title">Gestión de Usuarios</h1>
                <p class="users-header-subtitle">Administra todos los usuarios del sistema de manera eficiente</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn-user-primary">
                    <i class="bi bi-person-plus"></i>
                    Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="users-stats">
        <div class="user-stat-card fade-in-up">
            <div class="user-stat-icon" style="background: rgba(114, 47, 55, 0.1); color: var(--user-wine);">
                <i class="bi bi-people"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['total']) }}</div>
            <div class="user-stat-label">Total Usuarios</div>
        </div>

        <div class="user-stat-card fade-in-up animate-delay-1">
            <div class="user-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--user-success);">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['administradores']) }}</div>
            <div class="user-stat-label">Administradores</div>
        </div>

        <div class="user-stat-card fade-in-up animate-delay-2">
            <div class="user-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--user-info);">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['lideres']) }}</div>
            <div class="user-stat-label">Líderes</div>
        </div>

        <div class="user-stat-card fade-in-up animate-delay-3">
            <div class="user-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--user-warning);">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['vendedores']) }}</div>
            <div class="user-stat-label">Vendedores</div>
        </div>

        <div class="user-stat-card fade-in-up animate-delay-1">
            <div class="user-stat-icon" style="background: rgba(107, 114, 128, 0.1); color: var(--user-gray-500);">
                <i class="bi bi-person"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['clientes']) }}</div>
            <div class="user-stat-label">Clientes</div>
        </div>

        <div class="user-stat-card fade-in-up animate-delay-2">
            <div class="user-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--user-success);">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="user-stat-value">{{ number_format($stats['activos']) }}</div>
            <div class="user-stat-label">Activos</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="users-filters fade-in-up">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="filter-group">
                        <label class="filter-label">Buscar</label>
                        <input type="text"
                               name="search"
                               class="form-control filter-input"
                               placeholder="Nombre, email, cédula..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="filter-group">
                        <label class="filter-label">Rol</label>
                        <select name="rol" class="form-select filter-input">
                            <option value="">Todos</option>
                            <option value="administrador" {{ request('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="lider" {{ request('rol') == 'lider' ? 'selected' : '' }}>Líder</option>
                            <option value="vendedor" {{ request('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="filter-group">
                        <label class="filter-label">Estado</label>
                        <select name="activo" class="form-select filter-input">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="filter-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-user-primary">
                            <i class="bi bi-search"></i>
                            Buscar
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-user-secondary">
                            <i class="bi bi-x-circle"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="users-table-card fade-in-up">
        <div class="users-table-header">
            <h3 class="users-table-title">
                <i class="bi bi-people"></i>
                Lista de Usuarios <span style="color: var(--user-gray-500); font-weight: 400;">({{ $usuarios->total() }})</span>
            </h3>
        </div>

        @if($usuarios->count() > 0)
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
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
                        <tr data-user-id="{{ $usuario->_id }}">
                            <td data-label="Usuario">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                    </div>
                                    <div class="user-details">
                                        <h6 class="user-name">{{ $usuario->name }} {{ $usuario->apellidos ?? '' }}</h6>
                                        <small>C.I: {{ $usuario->cedula }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Contacto">
                                <div class="user-contact">
                                    <span class="user-email">{{ $usuario->email }}</span>
                                    <small class="user-phone">{{ $usuario->telefono }}</small>
                                </div>
                            </td>
                            <td data-label="Rol">
                                @php
                                    $roleClasses = [
                                        'administrador' => 'role-admin',
                                        'lider' => 'role-leader',
                                        'vendedor' => 'role-seller',
                                        'cliente' => 'role-client'
                                    ];
                                @endphp
                                <span class="user-badge {{ $roleClasses[$usuario->rol] ?? 'role-client' }}">
                                    {{ ucfirst($usuario->rol) }}
                                </span>
                            </td>
                            <td data-label="Estado">
                                <span class="user-badge {{ $usuario->activo ? 'status-active' : 'status-inactive' }}">
                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td data-label="Referidos">
                                <span class="user-badge" style="background: rgba(114, 47, 55, 0.1); color: var(--user-wine);">
                                    {{ $usuario->total_referidos ?? 0 }}
                                </span>
                            </td>
                            <td data-label="Registro">
                                <small style="color: var(--user-gray-500);">
                                    {{ $usuario->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td data-label="Acciones">
                                <div class="user-actions">
                                    <a href="{{ route('admin.users.show', $usuario) }}"
                                       class="user-action-btn btn-view"
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $usuario) }}"
                                       class="user-action-btn btn-edit"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="user-action-btn btn-toggle"
                                            title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}"
                                            data-user-id="{{ $usuario->_id }}"
                                            data-action="toggle">
                                        <i class="bi bi-{{ $usuario->activo ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                    <button type="button"
                                            class="user-action-btn btn-delete"
                                            title="Eliminar"
                                            data-user-id="{{ $usuario->_id }}"
                                            data-user-name="{{ $usuario->name }}"
                                            data-action="delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    {{-- Forms ocultos --}}
                                    <form class="user-toggle-form"
                                          data-user-id="{{ $usuario->_id }}"
                                          action="{{ route('admin.users.toggle-active', $usuario) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    <form class="user-delete-form"
                                          data-user-id="{{ $usuario->_id }}"
                                          action="{{ route('admin.users.destroy', $usuario) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($usuarios->hasPages())
            <div class="p-4 border-top">
                <div class="d-flex justify-content-center">
                    {{ $usuarios->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif
        @else
            <div class="users-empty">
                <div class="users-empty-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h4 class="users-empty-title">No hay usuarios</h4>
                <p class="users-empty-text">No se encontraron usuarios con los criterios especificados.</p>
                <a href="{{ route('admin.users.create') }}" class="btn-user-primary">
                    <i class="bi bi-person-plus"></i>
                    Crear primer usuario
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/users-management.js') }}?v={{ filemtime(public_path('js/modules/users-management.js')) }}"></script>
<script>
    // Performance monitoring
    if (window.performance && window.performance.timing) {
        window.addEventListener('load', () => {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log(`⚡ Módulo Usuarios cargado en: ${(pageLoadTime / 1000).toFixed(2)}s`);

            if (pageLoadTime > 3000) {
                console.warn('⚠️ El módulo tardó más de 3 segundos en cargar');
            }
        });
    }

    // PWA Detection
    if ('serviceWorker' in navigator) {
        console.log('✅ PWA Compatible - Service Worker supported');
    }

    // Detectar modo standalone
    if (window.matchMedia('(display-mode: standalone)').matches) {
        console.log('✅ Running as PWA');
    }
</script>
@endpush
