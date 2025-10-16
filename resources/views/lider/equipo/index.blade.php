@extends('layouts.lider')

@section('title', '- Gestión de Equipo')
@section('page-title', 'Gestión de Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/equipo-modern.css') }}?v={{ filemtime(public_path('css/lider/equipo-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <header class="equipo-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="equipo-title">
                    <i class="bi bi-people-fill me-3"></i>
                    Gestión de Mi Equipo
                </h1>
                <p class="equipo-subtitle">
                    Administra y supervisa el rendimiento de tu equipo de ventas en tiempo real
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <button type="button" class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                    <i class="bi bi-funnel-fill me-2"></i>
                    Filtrar
                </button>
                <button type="button" class="btn btn-light" data-action="export">
                    <i class="bi bi-download me-2"></i>
                    Exportar
                </button>
            </div>
        </div>
    </header>

    {{-- Stats Cards --}}
    <section class="row mb-4" role="region" aria-label="Estadísticas del equipo">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card success">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--success), var(--success-dark));">
                        <i class="bi bi-people-fill text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value text-success">{{ $statsEquipo['total_miembros'] }}</h3>
                    <p class="equipo-stat-label mb-0">Total Miembros</p>
                </div>
            </article>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card info animate-delay-1">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--info), var(--info-dark));">
                        <i class="bi bi-check-circle-fill text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value text-info">{{ $statsEquipo['activos'] }}</h3>
                    <p class="equipo-stat-label mb-0">Activos</p>
                </div>
            </article>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card wine animate-delay-2">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--wine), var(--wine-dark));">
                        <i class="bi bi-currency-dollar text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value" style="color: var(--wine);">${{ number_format($statsEquipo['ventas_totales'], 0) }}</h3>
                    <p class="equipo-stat-label mb-0">Ventas Mes</p>
                </div>
            </article>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card warning animate-delay-3">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--warning), var(--warning-dark));">
                        <i class="bi bi-graph-up-arrow text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value text-warning">${{ number_format($statsEquipo['promedio_ventas'], 0) }}</h3>
                    <p class="equipo-stat-label mb-0">Promedio</p>
                </div>
            </article>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card danger animate-delay-4">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--danger), var(--danger-dark));">
                        <i class="bi bi-trophy-fill text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value text-danger">{{ $statsEquipo['metas_cumplidas'] }}</h3>
                    <p class="equipo-stat-label mb-0">Metas Cumplidas</p>
                </div>
            </article>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <article class="equipo-stat-card info animate-delay-5" style="--info: var(--teal);">
                <div class="text-center">
                    <div class="equipo-stat-icon mx-auto" style="background: linear-gradient(135deg, var(--teal), #0d9488);">
                        <i class="bi bi-person-plus-fill text-white"></i>
                    </div>
                    <h3 class="equipo-stat-value" style="color: var(--teal);">{{ $statsEquipo['nuevos_mes'] }}</h3>
                    <p class="equipo-stat-label mb-0">Nuevos Mes</p>
                </div>
            </article>
        </div>
    </section>

    {{-- Active Filters --}}
    @if($search || $estado || $ordenPor != 'rendimiento')
        <div class="equipo-filters mb-4">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="fw-semibold text-muted">
                    <i class="bi bi-funnel me-1"></i>
                    Filtros activos:
                </span>
                @if($search)
                    <span class="equipo-filter-tag">
                        Búsqueda: {{ $search }}
                        <button type="button" onclick="window.location.href='{{ route('lider.equipo.index', array_diff_key(request()->query(), ['search' => ''])) }}'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </span>
                @endif
                @if($estado)
                    <span class="equipo-filter-tag">
                        Estado: {{ ucfirst($estado) }}
                        <button type="button" onclick="window.location.href='{{ route('lider.equipo.index', array_diff_key(request()->query(), ['estado' => ''])) }}'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </span>
                @endif
                @if($ordenPor != 'rendimiento')
                    <span class="equipo-filter-tag">
                        Orden: {{ ucfirst($ordenPor) }}
                        <button type="button" onclick="window.location.href='{{ route('lider.equipo.index', array_diff_key(request()->query(), ['orden_por' => ''])) }}'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </span>
                @endif
                <a href="{{ route('lider.equipo.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>
                    Limpiar filtros
                </a>
            </div>
        </div>
    @endif

    {{-- Team Table --}}
    <div class="equipo-table-container">
        <div class="equipo-table-header">
            <h2 class="equipo-table-title">
                <i class="bi bi-people"></i>
                <span>Mi Equipo ({{ $equipoConStats->count() }})</span>
            </h2>
            <div class="equipo-filter-pills" role="group" aria-label="Ordenar equipo">
                <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'rendimiento'])) }}"
                   class="equipo-filter-pill equipo-filter-pill-rendimiento {{ $ordenPor == 'rendimiento' ? 'active' : '' }}"
                   title="Ordenar por rendimiento">
                    <i class="bi bi-speedometer2"></i>
                    <span>Rendimiento</span>
                </a>
                <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'ventas'])) }}"
                   class="equipo-filter-pill equipo-filter-pill-ventas {{ $ordenPor == 'ventas' ? 'active' : '' }}"
                   title="Ordenar por ventas">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Ventas</span>
                </a>
                <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'referidos'])) }}"
                   class="equipo-filter-pill equipo-filter-pill-referidos {{ $ordenPor == 'referidos' ? 'active' : '' }}"
                   title="Ordenar por referidos">
                    <i class="bi bi-diagram-3"></i>
                    <span>Referidos</span>
                </a>
            </div>
        </div>

        @if($equipoConStats->count() > 0)
            <div class="table-responsive">
                <table class="equipo-table">
                    <thead>
                        <tr>
                            <th>Miembro</th>
                            <th>Rendimiento</th>
                            <th>Ventas Mes</th>
                            <th>Pedidos</th>
                            <th>Red</th>
                            <th>Meta</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipoConStats as $index => $miembroData)
                            @php
                                $miembro = $miembroData['miembro'];
                                $rendimiento = $miembroData['rendimiento'];
                                $progressColor = $rendimiento >= 80 ? 'success' : ($rendimiento >= 50 ? 'warning' : 'danger');
                            @endphp
                            <tr data-member-id="{{ $miembro->_id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="equipo-member-avatar me-3"
                                             style="background: linear-gradient(135deg, {{ $index < 3 ? 'var(--wine), var(--wine-dark)' : 'var(--gray-500), var(--gray-700)' }});">
                                            {{ strtoupper(substr($miembro->name, 0, 1)) }}
                                            @if($index < 3)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning"
                                                      style="font-size: 0.7rem;">
                                                    #{{ $index + 1 }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $miembro->name }} {{ $miembro->apellidos }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i>{{ $miembro->email }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="min-width: 120px;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="fw-bold">{{ number_format($rendimiento, 1) }}%</small>
                                        </div>
                                        <div class="equipo-progress">
                                            <div class="equipo-progress-bar {{ $progressColor }}"
                                                 style="width: {{ $rendimiento }}%"
                                                 role="progressbar"
                                                 aria-valuenow="{{ $rendimiento }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">${{ number_format($miembroData['ventas_mes'], 0) }}</div>
                                    <small class="text-muted">{{ now()->isoFormat('MMMM') }}</small>
                                </td>
                                <td>
                                    <span class="equipo-badge equipo-badge-info">
                                        <i class="bi bi-cart-check"></i>
                                        {{ $miembroData['pedidos_mes'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $miembroData['referidos_totales'] }}</div>
                                    @if($miembroData['referidos_mes'] > 0)
                                        <small class="text-success">
                                            <i class="bi bi-arrow-up-circle-fill"></i>
                                            +{{ $miembroData['referidos_mes'] }} este mes
                                        </small>
                                    @else
                                        <small class="text-muted">Sin nuevos</small>
                                    @endif
                                </td>
                                <td>
                                    @if($miembro->meta_mensual)
                                        <div style="min-width: 100px;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold">{{ number_format($miembroData['progreso_meta'], 1) }}%</small>
                                            </div>
                                            <div class="equipo-progress">
                                                <div class="equipo-progress-bar {{ $miembroData['progreso_meta'] >= 100 ? 'success' : '' }}"
                                                     style="width: {{ min($miembroData['progreso_meta'], 100) }}%"></div>
                                            </div>
                                            <small class="text-muted">${{ number_format($miembro->meta_mensual, 0) }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Sin meta asignada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="equipo-badge equipo-badge-{{ $miembro->activo ? 'success' : 'danger' }}">
                                        <i class="bi bi-{{ $miembro->activo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                        {{ $miembro->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('lider.equipo.show', $miembro->_id) }}"
                                           class="equipo-action-btn equipo-action-btn-view"
                                           title="Ver detalles">
                                            <i class="bi bi-eye-fill"></i>
                                            <span class="equipo-action-btn-text">Ver</span>
                                        </a>
                                        <button type="button"
                                                class="equipo-action-btn equipo-action-btn-meta"
                                                onclick="asignarMeta('{{ $miembro->_id }}')"
                                                title="Asignar meta mensual">
                                            <i class="bi bi-bullseye"></i>
                                            <span class="equipo-action-btn-text">Meta</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="equipo-empty-state">
                <i class="bi bi-people"></i>
                <h3>Tu equipo está vacío</h3>
                <p>Aún no tienes miembros en tu equipo. Comienza a referir nuevos vendedores.</p>
                <a href="{{ route('lider.referidos.index') }}" class="btn btn-wine">
                    <i class="bi bi-person-plus me-2"></i>
                    Invitar Vendedores
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Modal Filtros --}}
<div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filtrosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtrosModalLabel">
                    <i class="bi bi-funnel-fill me-2"></i>
                    Filtros de Búsqueda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="GET" action="{{ route('lider.equipo.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-search me-1"></i>
                            Buscar miembro
                        </label>
                        <input type="text" class="form-control" name="search"
                               placeholder="Nombre, email o cédula..." value="{{ $search }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-toggle-on me-1"></i>
                            Estado
                        </label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ $estado == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-sort-down me-1"></i>
                            Ordenar por
                        </label>
                        <select class="form-select" name="orden_por">
                            <option value="rendimiento" {{ $ordenPor == 'rendimiento' ? 'selected' : '' }}>Rendimiento</option>
                            <option value="ventas" {{ $ordenPor == 'ventas' ? 'selected' : '' }}>Ventas</option>
                            <option value="referidos" {{ $ordenPor == 'referidos' ? 'selected' : '' }}>Referidos</option>
                            <option value="meta" {{ $ordenPor == 'meta' ? 'selected' : '' }}>Progreso de Meta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-wine">
                        <i class="bi bi-check-lg me-1"></i>
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Asignar Meta - Estilo Admin con Glassmorphism --}}
<div class="equipo-modal-backdrop" id="metaModalBackdrop"></div>
<div class="equipo-modal-container" id="metaModal" role="dialog" aria-labelledby="metaModalTitle" aria-hidden="true">
    <div class="equipo-modal-glass">
        <div class="equipo-modal-header-admin">
            <div class="d-flex align-items-center gap-3">
                <div class="equipo-modal-icon-admin">
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="equipo-modal-title-admin" id="metaModalTitle">Asignar Meta Mensual</h4>
                    <p class="equipo-modal-subtitle-admin">Define objetivos de ventas para este miembro</p>
                </div>
            </div>
            <button type="button" class="equipo-modal-close-admin" onclick="cerrarModalMeta()" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="metaForm" method="POST">
            @csrf
            <div class="equipo-modal-body-admin">
                <!-- Meta Mensual -->
                <div class="equipo-form-field">
                    <label class="equipo-form-field-label">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Meta Mensual
                    </label>
                    <div class="equipo-input-group">
                        <span class="equipo-input-addon">$</span>
                        <input type="number"
                               class="equipo-form-control"
                               name="meta_mensual"
                               step="1000"
                               min="0"
                               placeholder="5000000"
                               required>
                        <span class="equipo-input-badge">COP</span>
                    </div>
                    <small class="equipo-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Ingresa el monto objetivo en pesos colombianos
                    </small>
                </div>

                <!-- Período -->
                <div class="equipo-form-field">
                    <label class="equipo-form-field-label">
                        <i class="bi bi-calendar-event me-2"></i>
                        Período
                    </label>
                    <input type="month"
                           class="equipo-form-control"
                           name="mes"
                           value="{{ now()->format('Y-m') }}"
                           required>
                    <small class="equipo-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Selecciona el mes para aplicar la meta
                    </small>
                </div>

                <!-- Info Card -->
                <div class="equipo-alert-info">
                    <div class="equipo-alert-icon">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <div class="equipo-alert-content">
                        <strong>Consejo:</strong>
                        Establece metas alcanzables basadas en el rendimiento histórico. Las metas motivantes suelen estar entre 10-20% por encima del promedio actual.
                    </div>
                </div>
            </div>

            <div class="equipo-modal-footer-admin">
                <button type="button" class="equipo-btn-secondary-admin" onclick="cerrarModalMeta()">
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>
                <button type="submit" class="equipo-btn-primary-admin">
                    <i class="bi bi-check-lg"></i>
                    Asignar Meta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/equipo-modern.js') }}?v={{ filemtime(public_path('js/lider/equipo-modern.js')) }}" defer></script>
<script>
function asignarMeta(miembroId) {
    const modal = document.getElementById('metaModal');
    const backdrop = document.getElementById('metaModalBackdrop');
    const form = document.getElementById('metaForm');

    form.action = `/lider/equipo/${miembroId}/asignar-meta`;

    backdrop.classList.add('active');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModalMeta() {
    const modal = document.getElementById('metaModal');
    const backdrop = document.getElementById('metaModalBackdrop');

    backdrop.classList.remove('active');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('metaModal');
        if (modal && modal.classList.contains('active')) {
            cerrarModalMeta();
        }
    }
});

// Cerrar al hacer click en el backdrop
document.getElementById('metaModalBackdrop')?.addEventListener('click', cerrarModalMeta);
</script>
@endpush
