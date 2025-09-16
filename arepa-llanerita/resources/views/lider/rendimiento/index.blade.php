@extends('layouts.lider')

@section('title', '- Rendimiento del Equipo')
@section('page-title', 'Rendimiento del Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/lider-dashboard.css') }}">
<style>
.rendimiento-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.rendimiento-card.excelente {
    border-left-color: #198754;
}

.rendimiento-card.bueno {
    border-left-color: #0dcaf0;
}

.rendimiento-card.regular {
    border-left-color: #ffc107;
}

.rendimiento-card.bajo {
    border-left-color: #dc3545;
}

.rendimiento-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(114, 47, 55, 0.15);
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.ranking-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    background: rgba(114, 47, 55, 0.05);
    transition: background-color 0.2s ease;
}

.ranking-item:hover {
    background: rgba(114, 47, 55, 0.1);
}

.ranking-position {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 0.75rem;
}

.ranking-position.gold {
    background: #ffd700;
    color: #000;
}

.ranking-position.silver {
    background: #c0c0c0;
    color: #000;
}

.ranking-position.bronze {
    background: #cd7f32;
    color: white;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filtros de período -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros de Período
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('lider.rendimiento.index') }}" class="d-flex gap-3 align-items-center">
                        <div>
                            <label class="form-label small">Período</label>
                            <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                                <option value="trimestre" {{ $periodo == 'trimestre' ? 'selected' : '' }}>Este Trimestre</option>
                                <option value="año" {{ $periodo == 'año' ? 'selected' : '' }}>Este Año</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-currency-dollar fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format($stats['total_ventas'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Total Ventas</p>
                    <small class="text-info">{{ $stats['cantidad_ventas'] }} ventas</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-graph-up fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['porcentaje_cumplimiento'], 1) }}%</h3>
                    <p class="text-muted mb-0 small">Cumplimiento Meta</p>
                    <small class="text-muted">${{ number_format($stats['meta_equipo'], 0) }} meta total</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['miembros_activos'] }}/{{ $stats['total_miembros'] }}</h3>
                    <p class="text-muted mb-0 small">Miembros Activos</p>
                    <small class="text-info">{{ number_format(($stats['miembros_activos']/$stats['total_miembros'])*100, 1) }}% activos</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-calculator fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format($stats['promedio_venta'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Promedio por Venta</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rendimiento Individual -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Rendimiento Individual
                    </h5>
                    <small class="text-muted">Período: {{ ucfirst($periodo) }}</small>
                </div>
                <div class="card-body">
                    @if(count($rendimientoIndividual) > 0)
                        <div class="row">
                            @foreach($rendimientoIndividual as $rendimiento)
                            <div class="col-lg-6 mb-3">
                                <div class="card rendimiento-card {{ $rendimiento['estado'] }} h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $rendimiento['miembro']->name }}</div>
                                                <small class="text-muted">{{ ucfirst($rendimiento['miembro']->rol) }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                @php
                                                    $badgeClass = '';
                                                    $badgeText = '';
                                                    switch($rendimiento['estado']) {
                                                        case 'excelente':
                                                            $badgeClass = 'success';
                                                            $badgeText = 'Excelente';
                                                            break;
                                                        case 'bueno':
                                                            $badgeClass = 'info';
                                                            $badgeText = 'Bueno';
                                                            break;
                                                        case 'regular':
                                                            $badgeClass = 'warning';
                                                            $badgeText = 'Regular';
                                                            break;
                                                        default:
                                                            $badgeClass = 'danger';
                                                            $badgeText = 'Bajo';
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">{{ $badgeText }}</span>
                                            </div>
                                        </div>

                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold text-primary">${{ number_format($rendimiento['total_ventas'], 0) }}</div>
                                                <small class="text-muted">Ventas</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-info">{{ $rendimiento['cantidad_ventas'] }}</div>
                                                <small class="text-muted">Cantidad</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success">{{ number_format($rendimiento['porcentaje_cumplimiento'], 1) }}%</div>
                                                <small class="text-muted">Meta</small>
                                            </div>
                                        </div>

                                        @if($rendimiento['miembro']->meta_mensual > 0)
                                        <div class="progress-custom mt-3">
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ min($rendimiento['porcentaje_cumplimiento'], 100) }}%"></div>
                                            </div>
                                        </div>
                                        @endif
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

        <!-- Rankings -->
        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top Performers
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Top por Ventas -->
                    <div class="mb-4">
                        <h6 class="text-primary">🏆 Top Ventas</h6>
                        @if($rankings['top_ventas']->count() > 0)
                            @foreach($rankings['top_ventas'] as $index => $item)
                            <div class="ranking-item">
                                <div class="ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $item->vendedor->name }}</div>
                                    <small class="text-muted">${{ number_format($item->total_ventas, 0) }}</small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small">Sin datos disponibles</p>
                        @endif
                    </div>

                    <!-- Top por Cantidad -->
                    <div class="mb-4">
                        <h6 class="text-info">📊 Top Cantidad</h6>
                        @if($rankings['top_cantidad']->count() > 0)
                            @foreach($rankings['top_cantidad'] as $index => $item)
                            <div class="ranking-item">
                                <div class="ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $item->vendedor->name }}</div>
                                    <small class="text-muted">{{ $item->cantidad_ventas }} ventas</small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small">Sin datos disponibles</p>
                        @endif
                    </div>

                    <!-- Top por Comisiones -->
                    <div>
                        <h6 class="text-success">💰 Top Comisiones</h6>
                        @if($rankings['top_comisiones']->count() > 0)
                            @foreach($rankings['top_comisiones'] as $index => $item)
                            <div class="ranking-item">
                                <div class="ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $item->usuario->name }}</div>
                                    <small class="text-muted">${{ number_format($item->total_comisiones, 0) }}</small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small">Sin datos disponibles</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evolución de Métricas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Evolución de Ventas (Últimos 6 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="evolucionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de evolución
    const evolucionData = @json($evolucionMetricas);

    const ctx = document.getElementById('evolucionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolucionData.map(item => item.mes),
            datasets: [{
                label: 'Ventas ($)',
                data: evolucionData.map(item => item.ventas),
                borderColor: '#722F37',
                backgroundColor: 'rgba(114, 47, 55, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Cantidad de Ventas',
                data: evolucionData.map(item => item.cantidad),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 2,
                fill: false,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Ventas ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Cantidad'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush