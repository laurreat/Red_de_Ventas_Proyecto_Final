@extends('layouts.lider')

@section('title', '- Metas y Objetivos')
@section('page-title', 'Metas y Objetivos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/lider-dashboard.css') }}">
<style>
.meta-card {
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-color);
}

.meta-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(114, 47, 55, 0.15);
}

.progreso-meta {
    position: relative;
    background: rgba(114, 47, 55, 0.1);
    border-radius: 1rem;
    overflow: hidden;
    height: 12px;
}

.progreso-meta .barra {
    height: 100%;
    border-radius: 1rem;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    transition: width 0.6s ease;
}

.estado-meta {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
}

.estado-meta.excelente {
    background: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.estado-meta.bueno {
    background: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
}

.estado-meta.regular {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.estado-meta.bajo {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.form-floating-custom {
    position: relative;
}

.form-floating-custom label {
    position: absolute;
    top: 0;
    left: 0.75rem;
    padding: 0.25rem;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 400;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    transform-origin: 0 0;
    background: white;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Resumen del Equipo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bullseye me-2"></i>
                        Resumen de Metas del Equipo - {{ now()->format('F Y') }}
                    </h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#asignarMetaEquipoModal">
                        <i class="bi bi-plus-lg me-1"></i>
                        Asignar Meta Equipo
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-target fs-1 text-primary"></i>
                            </div>
                            <h4 class="fw-bold text-primary">${{ number_format($metaEquipo, 0) }}</h4>
                            <p class="text-muted mb-0">Meta del Equipo</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-graph-up fs-1 text-success"></i>
                            </div>
                            <h4 class="fw-bold text-success">${{ number_format($ventasEquipo, 0) }}</h4>
                            <p class="text-muted mb-0">Ventas Actuales</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-percent fs-1 text-info"></i>
                            </div>
                            <h4 class="fw-bold text-info">{{ number_format($progresoEquipo, 1) }}%</h4>
                            <p class="text-muted mb-0">Progreso</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-currency-dollar fs-1 {{ $ventasEquipo - $metaEquipo >= 0 ? 'text-success' : 'text-danger' }}"></i>
                            </div>
                            <h4 class="fw-bold {{ $ventasEquipo - $metaEquipo >= 0 ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($ventasEquipo - $metaEquipo, 0) }}
                            </h4>
                            <p class="text-muted mb-0">Diferencia</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="progreso-meta">
                            <div class="barra" style="width: {{ min($progresoEquipo, 100) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">0%</small>
                            <small class="fw-bold">{{ number_format($progresoEquipo, 1) }}% completado</small>
                            <small class="text-muted">100%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metas Individuales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check me-2"></i>
                        Metas Individuales
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($progreso) > 0)
                        <div class="row">
                            @foreach($progreso as $item)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card meta-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $item['miembro']->name }}</div>
                                                <small class="text-muted">{{ ucfirst($item['miembro']->rol) }}</small>
                                            </div>
                                            <div>
                                                @php
                                                    $estado = 'bajo';
                                                    if ($item['porcentaje'] >= 90) $estado = 'excelente';
                                                    elseif ($item['porcentaje'] >= 70) $estado = 'bueno';
                                                    elseif ($item['porcentaje'] >= 50) $estado = 'regular';
                                                @endphp
                                                <span class="estado-meta {{ $estado }}">
                                                    @switch($estado)
                                                        @case('excelente') Excelente @break
                                                        @case('bueno') Bueno @break
                                                        @case('regular') Regular @break
                                                        @default Bajo
                                                    @endswitch
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="small text-muted">Meta Mensual</span>
                                                <span class="fw-bold text-primary">${{ number_format($item['meta_actual'], 0) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="small text-muted">Ventas Actuales</span>
                                                <span class="fw-bold">${{ number_format($item['ventas_mes'], 0) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="small text-muted">Diferencia</span>
                                                <span class="fw-bold {{ $item['diferencia'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($item['diferencia'], 0) }}
                                                </span>
                                            </div>

                                            <div class="progreso-meta mb-2">
                                                <div class="barra" style="width: {{ min($item['porcentaje'], 100) }}%"></div>
                                            </div>
                                            <div class="text-center">
                                                <span class="fw-bold">{{ number_format($item['porcentaje'], 1) }}%</span>
                                                <small class="text-muted"> completado</small>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editarMeta({{ $item['miembro']->id }}, '{{ $item['miembro']->name }}', {{ $item['meta_actual'] }})">
                                                <i class="bi bi-pencil me-1"></i>
                                                Editar Meta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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

    <!-- Historial de Metas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Evolución de Metas (Últimos 6 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="historialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Meta Equipo -->
<div class="modal fade" id="asignarMetaEquipoModal" tabindex="-1" aria-labelledby="asignarMetaEquipoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignarMetaEquipoModalLabel">Asignar Meta del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lider.metas.asignar-equipo') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating-custom">
                                <input type="number" class="form-control" id="meta_equipo" name="meta_equipo"
                                       value="{{ $metaEquipo }}" min="0" step="0.01" required>
                                <label for="meta_equipo">Meta del Equipo ($)</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating-custom">
                                <select class="form-select" id="distribucion" name="distribucion" required>
                                    <option value="equitativa">Distribución Equitativa</option>
                                    <option value="proporcional">Basada en Rendimiento Previo</option>
                                    <option value="manual">Asignación Manual</option>
                                </select>
                                <label for="distribucion">Tipo de Distribución</label>
                            </div>
                        </div>
                    </div>

                    <div id="distribucionManual" style="display: none;">
                        <hr>
                        <h6>Asignación Manual de Metas</h6>
                        @foreach($equipo as $miembro)
                        <div class="row align-items-center mb-2">
                            <div class="col-6">
                                <strong>{{ $miembro->name }}</strong>
                                <small class="text-muted d-block">{{ ucfirst($miembro->rol) }}</small>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control"
                                       name="metas_individuales[{{ $miembro->id }}]"
                                       value="{{ $miembro->meta_mensual ?? 0 }}"
                                       min="0" step="0.01" placeholder="Meta individual">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Tipos de Distribución:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Equitativa:</strong> Divide la meta total equitativamente entre todos los miembros</li>
                            <li><strong>Basada en Rendimiento:</strong> Asigna metas proporcionalmente según las ventas del mes anterior</li>
                            <li><strong>Manual:</strong> Te permite asignar metas específicas a cada miembro</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Metas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Meta Individual -->
<div class="modal fade" id="editarMetaModal" tabindex="-1" aria-labelledby="editarMetaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarMetaModalLabel">Editar Meta Individual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarMetaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="nombreMiembro"></label>
                    </div>

                    <div class="mb-3">
                        <div class="form-floating-custom">
                            <input type="number" class="form-control" id="meta_mensual_edit" name="meta_mensual"
                                   min="0" step="0.01" required>
                            <label for="meta_mensual_edit">Meta Mensual ($)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-floating-custom">
                            <textarea class="form-control" id="notas_edit" name="notas" rows="3" style="height: 80px;"></textarea>
                            <label for="notas_edit">Notas (Opcional)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Meta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de evolución de metas
    const historialData = @json($historialMetas);

    const ctx = document.getElementById('historialChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: historialData.map(item => item.mes),
            datasets: [{
                label: 'Meta ($)',
                data: historialData.map(item => item.meta),
                borderColor: '#722F37',
                backgroundColor: 'rgba(114, 47, 55, 0.1)',
                borderWidth: 3,
                fill: false
            }, {
                label: 'Ventas Reales ($)',
                data: historialData.map(item => item.ventas),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto ($)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Control de distribución manual
    document.getElementById('distribucion').addEventListener('change', function() {
        const distribucionManual = document.getElementById('distribucionManual');
        if (this.value === 'manual') {
            distribucionManual.style.display = 'block';
        } else {
            distribucionManual.style.display = 'none';
        }
    });
});

function editarMeta(id, nombre, metaActual) {
    document.getElementById('nombreMiembro').textContent = nombre;
    document.getElementById('meta_mensual_edit').value = metaActual;
    document.getElementById('editarMetaForm').action = `/lider/metas/${id}`;

    const modal = new bootstrap.Modal(document.getElementById('editarMetaModal'));
    modal.show();
}
</script>
@endpush