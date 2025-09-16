@extends('layouts.lider')

@section('title', '- Capacitación del Equipo')
@section('page-title', 'Capacitación del Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/lider-dashboard.css') }}">
<style>
.modulo-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.modulo-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(114, 47, 55, 0.15);
}

.modulo-card.completado {
    border-color: #198754;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.05), rgba(25, 135, 84, 0.02));
}

.nivel-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
}

.progreso-miembro {
    background: rgba(114, 47, 55, 0.05);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.progreso-barra {
    height: 8px;
    background: rgba(114, 47, 55, 0.1);
    border-radius: 1rem;
    overflow: hidden;
}

.progreso-barra .fill {
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    transition: width 0.6s ease;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-mortarboard me-2"></i>
                        Resumen de Capacitación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="bi bi-book-fill fs-2 text-primary"></i>
                            </div>
                            <h4 class="fw-bold text-primary">{{ count($modulos) }}</h4>
                            <p class="text-muted mb-0">Módulos Disponibles</p>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="bi bi-people-fill fs-2 text-info"></i>
                            </div>
                            <h4 class="fw-bold text-info">{{ count($equipo) }}</h4>
                            <p class="text-muted mb-0">Miembros del Equipo</p>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                            </div>
                            <h4 class="fw-bold text-success">{{ collect($progresoEquipo)->sum('modulos_completados') }}</h4>
                            <p class="text-muted mb-0">Módulos Completados</p>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <i class="bi bi-graph-up fs-2 text-warning"></i>
                            </div>
                            <h4 class="fw-bold text-warning">{{ number_format((collect($progresoEquipo)->sum('modulos_completados') / (count($equipo) * count($modulos))) * 100, 1) }}%</h4>
                            <p class="text-muted mb-0">Progreso General</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulos de Capacitación -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-collection me-2"></i>
                        Módulos de Capacitación
                    </h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#asignarModuloModal">
                        <i class="bi bi-plus-lg me-1"></i>
                        Asignar Módulo
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($modulos as $modulo)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card modulo-card h-100 {{ $modulo['completado'] ? 'completado' : '' }}">
                                <div class="card-body position-relative">
                                    @php
                                        $nivelColors = [
                                            'Básico' => 'success',
                                            'Intermedio' => 'warning',
                                            'Avanzado' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $nivelColors[$modulo['nivel']] }} nivel-badge">{{ $modulo['nivel'] }}</span>

                                    @if($modulo['completado'])
                                        <div class="position-absolute top-0 start-0 p-2">
                                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        </div>
                                    @endif

                                    <div class="mb-3 pt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi {{ $modulo['icono'] }} fs-2 text-primary me-3"></i>
                                            <h6 class="fw-bold mb-0">{{ $modulo['titulo'] }}</h6>
                                        </div>
                                    </div>

                                    <p class="text-muted small mb-3">{{ $modulo['descripcion'] }}</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-1 text-muted"></i>
                                            <small class="text-muted">{{ $modulo['duracion'] }}</small>
                                        </div>
                                        @if($modulo['completado'])
                                            <span class="badge bg-success">Completado</span>
                                        @else
                                            <span class="badge bg-light text-dark">Pendiente</span>
                                        @endif
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="verDetalle({{ $modulo['id'] }})">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso del Equipo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>
                        Progreso del Equipo
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($progresoEquipo) > 0)
                        @foreach($progresoEquipo as $progreso)
                        <div class="progreso-miembro">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $progreso['miembro']->name }}</div>
                                        <small class="text-muted">{{ ucfirst($progreso['miembro']->rol) }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ $progreso['modulos_completados'] }}/{{ $progreso['total_modulos'] }}</div>
                                    <small class="text-muted">Módulos</small>
                                </div>
                            </div>

                            <div class="progreso-barra mb-2">
                                <div class="fill" style="width: {{ ($progreso['modulos_completados'] / $progreso['total_modulos']) * 100 }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Último módulo: <strong>{{ $progreso['ultimo_modulo'] }}</strong></small>
                                </div>
                                <div>
                                    <small class="text-muted">{{ $progreso['fecha_ultimo']->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mb-0">No hay miembros en el equipo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Módulo -->
<div class="modal fade" id="asignarModuloModal" tabindex="-1" aria-labelledby="asignarModuloModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignarModuloModalLabel">Asignar Módulo al Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lider.capacitacion.asignar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modulo_id" class="form-label">Seleccionar Módulo</label>
                        <select class="form-select" id="modulo_id" name="modulo_id" required>
                            @foreach($modulos as $modulo)
                            <option value="{{ $modulo['id'] }}">{{ $modulo['titulo'] }} ({{ $modulo['duracion'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Asignar a:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="todos" onclick="toggleTodos()">
                            <label class="form-check-label fw-bold" for="todos">
                                Seleccionar Todos
                            </label>
                        </div>
                        <hr>
                        @foreach($equipo as $miembro)
                        <div class="form-check">
                            <input class="form-check-input miembro-check" type="checkbox" name="miembro_ids[]" value="{{ $miembro->id }}" id="miembro_{{ $miembro->id }}">
                            <label class="form-check-label" for="miembro_{{ $miembro->id }}">
                                {{ $miembro->name }} - <small class="text-muted">{{ ucfirst($miembro->rol) }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Módulo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTodos() {
    const todosCheck = document.getElementById('todos');
    const miembroChecks = document.querySelectorAll('.miembro-check');

    miembroChecks.forEach(check => {
        check.checked = todosCheck.checked;
    });
}

function verDetalle(moduloId) {
    // En una implementación real, esto abriría un modal con los detalles del módulo
    alert('Funcionalidad de detalles del módulo próximamente disponible');
}
</script>
@endpush