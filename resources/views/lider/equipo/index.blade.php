@extends('layouts.lider')

@section('title', '- Gestión de Equipo')
@section('page-title', 'Gestión de Equipo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra y supervisa el rendimiento de tu equipo</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                        <i class="bi bi-funnel me-1"></i>
                        Filtros
                    </button>
                    <button type="button" class="btn btn-primary" onclick="exportarEquipo()">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del equipo -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #198754, #20c997);">
                        <i class="bi bi-people fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $statsEquipo['total_miembros'] }}</h3>
                    <p class="text-muted mb-0 small">Total Miembros</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #0d6efd, #6610f2);">
                        <i class="bi bi-check-circle fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">{{ $statsEquipo['activos'] }}</h3>
                    <p class="text-muted mb-0 small">Miembros Activos</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light));">
                        <i class="bi bi-currency-dollar fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format($statsEquipo['ventas_totales'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ventas del Mes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107, #fd7e14);">
                        <i class="bi bi-graph-up fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">${{ number_format($statsEquipo['promedio_ventas'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Promedio Ventas</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #dc3545, #e74c3c);">
                        <i class="bi bi-target fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ $statsEquipo['metas_cumplidas'] }}</h3>
                    <p class="text-muted mb-0 small">Metas Cumplidas</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #20c997, #17a2b8);">
                        <i class="bi bi-person-plus fs-2 text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-info">{{ $statsEquipo['nuevos_mes'] }}</h3>
                    <p class="text-muted mb-0 small">Nuevos del Mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros actuales -->
    @if($search || $estado || $ordenPor != 'rendimiento')
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small">Filtros activos:</span>
                    @if($search)
                        <span class="badge bg-primary">Búsqueda: {{ $search }}</span>
                    @endif
                    @if($estado)
                        <span class="badge bg-success">Estado: {{ ucfirst($estado) }}</span>
                    @endif
                    @if($ordenPor != 'rendimiento')
                        <span class="badge bg-info">Orden: {{ ucfirst($ordenPor) }}</span>
                    @endif
                    <a href="{{ route('lider.equipo.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Limpiar filtros
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista del equipo -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-people me-2"></i>
                            Mi Equipo ({{ $equipoConStats->count() }})
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'rendimiento'])) }}"
                               class="btn btn-outline-primary {{ $ordenPor == 'rendimiento' ? 'active' : '' }}">
                                Rendimiento
                            </a>
                            <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'ventas'])) }}"
                               class="btn btn-outline-primary {{ $ordenPor == 'ventas' ? 'active' : '' }}">
                                Ventas
                            </a>
                            <a href="{{ route('lider.equipo.index', array_merge(request()->query(), ['orden_por' => 'referidos'])) }}"
                               class="btn btn-outline-primary {{ $ordenPor == 'referidos' ? 'active' : '' }}">
                                Referidos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($equipoConStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Miembro</th>
                                        <th>Rendimiento</th>
                                        <th>Ventas del Mes</th>
                                        <th>Pedidos</th>
                                        <th>Red</th>
                                        <th>Meta</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipoConStats as $index => $miembroData)
                                        @php $miembro = $miembroData['miembro']; @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        @if($index < 3)
                                                            <div class="position-relative">
                                                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light));">
                                                                    <span class="text-white fw-bold">{{ strtoupper(substr($miembro->name, 0, 1)) }}</span>
                                                                </div>
                                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                                                    {{ $index + 1 }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #6c757d, #495057);">
                                                                <span class="text-white fw-bold">{{ strtoupper(substr($miembro->name, 0, 1)) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">{{ $miembro->name }} {{ $miembro->apellidos }}</h6>
                                                        <small class="text-muted">{{ $miembro->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <small class="fw-semibold">{{ number_format($miembroData['rendimiento'], 1) }}%</small>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-{{ $miembroData['rendimiento'] >= 80 ? 'success' : ($miembroData['rendimiento'] >= 50 ? 'warning' : 'danger') }}"
                                                                 style="width: {{ $miembroData['rendimiento'] }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">${{ number_format($miembroData['ventas_mes'], 0) }}</div>
                                                <small class="text-muted">Este mes</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $miembroData['pedidos_mes'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-semibold">{{ $miembroData['referidos_totales'] }}</span> total
                                                    @if($miembroData['referidos_mes'] > 0)
                                                        <br><small class="text-success">+{{ $miembroData['referidos_mes'] }} este mes</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($miembro->meta_mensual)
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <small class="fw-semibold">{{ number_format($miembroData['progreso_meta'], 1) }}%</small>
                                                            </div>
                                                            <div class="progress" style="height: 4px;">
                                                                <div class="progress-bar bg-{{ $miembroData['progreso_meta'] >= 100 ? 'success' : 'primary' }}"
                                                                     style="width: {{ min($miembroData['progreso_meta'], 100) }}%"></div>
                                                            </div>
                                                            <small class="text-muted">${{ number_format($miembro->meta_mensual, 0) }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <small class="text-muted">Sin meta</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $miembro->activo ? 'success' : 'danger' }}">
                                                    {{ $miembro->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('lider.equipo.show', $miembro->id) }}"
                                                       class="btn btn-outline-info" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-primary"
                                                            onclick="asignarMeta({{ $miembro->id }})" title="Asignar meta">
                                                        <i class="bi bi-target"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success"
                                                            onclick="enviarMensaje({{ $miembro->id }})" title="Enviar mensaje">
                                                        <i class="bi bi-chat"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Tu equipo está vacío</h4>
                            <p class="text-muted">Aún no tienes miembros en tu equipo. Comienza a referir nuevos miembros.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros de Búsqueda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('lider.equipo.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Buscar miembro</label>
                        <input type="text" class="form-control" name="search"
                               placeholder="Nombre, email o cédula..." value="{{ $search }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ $estado == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordenar por</label>
                        <select class="form-select" name="orden_por">
                            <option value="rendimiento" {{ $ordenPor == 'rendimiento' ? 'selected' : '' }}>Rendimiento</option>
                            <option value="ventas" {{ $ordenPor == 'ventas' ? 'selected' : '' }}>Ventas</option>
                            <option value="referidos" {{ $ordenPor == 'referidos' ? 'selected' : '' }}>Referidos</option>
                            <option value="meta" {{ $ordenPor == 'meta' ? 'selected' : '' }}>Progreso de Meta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Asignar Meta -->
<div class="modal fade" id="metaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Meta Mensual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="metaForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Mensual ($)</label>
                        <input type="number" class="form-control" name="meta_mensual"
                               step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mes</label>
                        <input type="month" class="form-control" name="mes"
                               value="{{ now()->format('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Meta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function asignarMeta(miembroId) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(document.getElementById('metaModal'));
        const form = document.getElementById('metaForm');
        form.action = `/lider/equipo/${miembroId}/asignar-meta`;
        modal.show();
    } else {
        // Fallback: mostrar el modal manualmente
        const modalElement = document.getElementById('metaModal');
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    }
}

function enviarMensaje(miembroId) {
    // Implementar funcionalidad de mensajería
    alert('Funcionalidad de mensajería próximamente');
}

function exportarEquipo() {
    // Implementar exportación
    alert('Exportando datos del equipo...');
}
</script>
@endsection