@extends('layouts.admin')

@section('title', '- Detalles del Usuario')
@section('page-title', 'Detalles del Usuario')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/users-show.css') }}?v={{ filemtime(public_path('css/admin/users-show.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="user-detail-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="user-detail-avatar">
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="user-detail-info">
                    <h1 class="user-detail-name">{{ $user->name }} {{ $user->apellidos }}</h1>
                    <div class="user-detail-meta">
                        <span class="user-detail-badge">
                            <i class="bi bi-{{ $user->activo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span class="user-detail-badge">
                            <i class="bi bi-person-badge"></i>
                            {{ ucfirst($user->rol) }}
                        </span>
                        <span class="user-detail-badge">
                            <i class="bi bi-envelope"></i>
                            {{ $user->email }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="user-detail-actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="user-detail-btn user-detail-btn-white">
                    <i class="bi bi-pencil"></i>
                    Editar
                </a>
                <a href="{{ route('admin.users.index') }}" class="user-detail-btn user-detail-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="user-stat-card fade-in-up" data-stat-type="pedidos-cliente">
                <div class="user-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-bag"></i>
                </div>
                <div class="user-stat-value">{{ $stats['pedidos_como_cliente'] }}</div>
                <div class="user-stat-label">Pedidos como Cliente</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="user-stat-card fade-in-up animate-delay-1" data-stat-type="pedidos-vendedor">
                <div class="user-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="user-stat-value">{{ $stats['pedidos_como_vendedor'] }}</div>
                <div class="user-stat-label">Pedidos como Vendedor</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="user-stat-card fade-in-up animate-delay-2" data-stat-type="total-vendido">
                <div class="user-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="user-stat-value">${{ number_format(to_float($stats['total_vendido']), 0) }}</div>
                <div class="user-stat-label">Total Vendido</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="user-stat-card fade-in-up animate-delay-3" data-stat-type="comisiones">
                <div class="user-stat-icon" style="background: rgba(114, 47, 55, 0.1); color: #722F37;">
                    <i class="bi bi-gem"></i>
                </div>
                <div class="user-stat-value">${{ number_format(to_float($stats['comisiones_totales']), 0) }}</div>
                <div class="user-stat-label">Comisiones Totales</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Columna Principal --}}
        <div class="col-lg-8">
            {{-- Información Personal --}}
            <div class="user-info-card fade-in-up">
                <div class="user-info-card-header">
                    <i class="bi bi-person"></i>
                    <h3 class="user-info-card-title">Información Personal</h3>
                </div>
                <div class="user-info-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Nombres</label>
                                <div class="user-info-value">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Apellidos</label>
                                <div class="user-info-value">{{ $user->apellidos }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Cédula</label>
                                <div class="user-info-value">
                                    {{ $user->cedula }}
                                    <button class="btn btn-sm btn-outline-secondary ms-2" data-action="copy" data-copy-text="{{ $user->cedula }}" title="Copiar">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Fecha de Nacimiento</label>
                                <div class="user-info-value">
                                    {{ $user->fecha_nacimiento ? $user->fecha_nacimiento->format('d/m/Y') : 'No especificada' }}
                                    @if($user->fecha_nacimiento)
                                        <small class="text-muted">({{ $user->fecha_nacimiento->age }} años)</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Email</label>
                                <div class="user-info-value">
                                    {{ $user->email }}
                                    <button class="btn btn-sm btn-outline-secondary ms-2" data-action="copy" data-copy-text="{{ $user->email }}" title="Copiar">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Teléfono</label>
                                <div class="user-info-value">{{ $user->telefono }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Ciudad</label>
                                <div class="user-info-value">{{ $user->ciudad }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Departamento</label>
                                <div class="user-info-value">{{ $user->departamento }}</div>
                            </div>
                        </div>
                        @if($user->direccion)
                            <div class="col-12">
                                <div class="user-info-item">
                                    <label class="user-info-label">Dirección</label>
                                    <div class="user-info-value">{{ $user->direccion }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Red de Referidos --}}
            <div class="user-info-card fade-in-up animate-delay-1">
                <div class="user-info-card-header">
                    <i class="bi bi-diagram-3"></i>
                    <h3 class="user-info-card-title">Red de Marketing Multinivel</h3>
                </div>
                <div class="user-info-card-body">
                    <div class="row g-4">
                        @if($user->referidor)
                            <div class="col-md-6">
                                <div class="user-info-item">
                                    <label class="user-info-label">Referido por</label>
                                    <div class="referido-item" data-action="view-referido" data-user-id="{{ $user->referidor->_id }}" style="cursor: pointer;">
                                        <div class="referido-avatar">
                                            {{ strtoupper(substr($user->referidor->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="referido-name">{{ $user->referidor->name }} {{ $user->referidor->apellidos }}</div>
                                            <div class="referido-role">{{ ucfirst($user->referidor->rol) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Código de Referido</label>
                                <div class="user-info-value">
                                    <span class="user-detail-badge" style="background: rgba(114, 47, 55, 0.1); color: #722F37; font-size: 1rem; padding: 0.5rem 1rem;">
                                        {{ $user->codigo_referido }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" data-action="copy" data-copy-text="{{ $user->codigo_referido }}" title="Copiar">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Total de Referidos</label>
                                <div class="user-info-value">{{ $user->total_referidos ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <label class="user-info-label">Meta Mensual</label>
                                <div class="user-info-value">${{ number_format(to_float($user->meta_mensual ?? 0), 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($user->referidos->count() > 0)
                        <hr style="margin: 1.5rem 0; border-color: var(--gray-200);">
                        <div>
                            <h6 style="font-weight: 600; margin-bottom: 1rem; color: var(--gray-900);">
                                Referidos Directos ({{ $user->referidos->count() }})
                            </h6>
                            <div>
                                @foreach($user->referidos->take(6) as $referido)
                                    <div class="referido-item" data-action="view-referido" data-user-id="{{ $referido->_id }}">
                                        <div class="referido-avatar">
                                            {{ strtoupper(substr($referido->name, 0, 1)) }}
                                        </div>
                                        <div style="flex: 1;">
                                            <div class="referido-name">{{ $referido->name }} {{ $referido->apellidos }}</div>
                                            <div class="referido-role">{{ ucfirst($referido->rol) }}</div>
                                        </div>
                                        <i class="bi bi-chevron-right" style="color: var(--gray-400);"></i>
                                    </div>
                                @endforeach
                            </div>
                            @if($user->referidos->count() > 6)
                                <p style="text-align: center; color: var(--gray-500); font-size: 0.875rem; margin-top: 1rem;">
                                    Y {{ $user->referidos->count() - 6 }} más...
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral --}}
        <div class="col-lg-4">
            {{-- Configuración --}}
            <div class="user-info-card fade-in-up animate-delay-2">
                <div class="user-info-card-header">
                    <i class="bi bi-gear"></i>
                    <h3 class="user-info-card-title">Configuración del Sistema</h3>
                </div>
                <div class="user-info-card-body">
                    <div class="user-info-item">
                        <label class="user-info-label">Rol</label>
                        <div>
                            @php
                                $roleColors = [
                                    'administrador' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
                                    'lider' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6'],
                                    'vendedor' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'color' => '#f59e0b'],
                                    'cliente' => ['bg' => 'rgba(107, 114, 128, 0.1)', 'color' => '#6b7280']
                                ];
                                $color = $roleColors[$user->rol] ?? $roleColors['cliente'];
                            @endphp
                            <span class="user-detail-badge" style="background: {{ $color['bg'] }}; color: {{ $color['color'] }};">
                                <i class="bi bi-person-badge"></i>
                                {{ ucfirst($user->rol) }}
                            </span>
                        </div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Estado</label>
                        <div>
                            <span class="user-detail-badge" style="background: {{ $user->activo ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $user->activo ? '#10b981' : '#ef4444' }};">
                                <i class="bi bi-{{ $user->activo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Fecha de Registro</label>
                        <div class="user-info-value">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Última Actualización</label>
                        <div class="user-info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Estadísticas Financieras --}}
            <div class="user-info-card fade-in-up animate-delay-3">
                <div class="user-info-card-header">
                    <i class="bi bi-graph-up"></i>
                    <h3 class="user-info-card-title">Estadísticas Financieras</h3>
                </div>
                <div class="user-info-card-body">
                    <div class="user-info-item">
                        <label class="user-info-label">Ventas Mes Actual</label>
                        <div class="user-info-value">${{ number_format(to_float($user->ventas_mes_actual ?? 0), 2) }}</div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Comisiones Ganadas</label>
                        <div class="user-info-value" style="color: #10b981;">${{ number_format(to_float($user->comisiones_ganadas ?? 0), 2) }}</div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Comisiones Disponibles</label>
                        <div class="user-info-value" style="color: #3b82f6;">${{ number_format(to_float($user->comisiones_disponibles ?? 0), 2) }}</div>
                    </div>

                    <div class="user-info-item">
                        <label class="user-info-label">Total Gastado</label>
                        <div class="user-info-value">${{ number_format(to_float($stats['total_gastado']), 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="user-info-card fade-in-up animate-delay-3">
                <div class="user-info-card-body">
                    <a href="{{ route('admin.users.edit', $user) }}" class="user-action-btn user-action-btn-primary">
                        <i class="bi bi-pencil"></i>
                        Editar Usuario
                    </a>

                    <button type="button"
                            class="user-action-btn user-action-btn-{{ $user->activo ? 'warning' : 'success' }}"
                            data-action="toggle-status"
                            data-user-id="{{ $user->_id }}"
                            data-active="{{ $user->activo ? 'true' : 'false' }}">
                        <i class="bi bi-{{ $user->activo ? 'pause-circle' : 'play-circle' }}"></i>
                        {{ $user->activo ? 'Desactivar' : 'Activar' }}
                    </button>

                    {{-- Formulario oculto para toggle --}}
                    <form data-user-id="{{ $user->_id }}"
                          action="{{ route('admin.users.toggle-active', $user) }}"
                          method="POST"
                          style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>

                    <a href="{{ route('admin.users.index') }}" class="user-action-btn user-action-btn-secondary">
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
<script src="{{ asset('js/admin/users-show.js') }}?v={{ filemtime(public_path('js/admin/users-show.js')) }}"></script>
@endpush
