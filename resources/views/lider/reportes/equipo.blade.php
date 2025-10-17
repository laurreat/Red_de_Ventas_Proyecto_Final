@extends('layouts.lider')

@section('title', ' - Reportes de Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/reportes-modern.css') }}?v={{ filemtime(public_path('css/lider/reportes-modern.css')) }}">
@endpush

@section('content')
<div class="reportes-container">
    <!-- Header Hero -->
    <div class="reportes-header fade-in-up">
        <div class="reportes-header-content">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="reportes-title">
                        <i class="bi bi-people"></i>
                        Reportes de Equipo
                    </h1>
                    <p class="reportes-subtitle">Análisis del rendimiento de tu equipo de trabajo</p>
                </div>
                <div class="reportes-actions">
                    <button class="reportes-action-btn reportes-action-btn-success" onclick="reportesManager.exportarReporte()">
                        <i class="bi bi-download"></i>
                        Exportar Reporte
                    </button>
                    <a href="{{ route('lider.ventas.index') }}" class="reportes-action-btn reportes-action-btn-info">
                        <i class="bi bi-graph-up-arrow"></i>
                        Ver Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="reportes-stats-grid scale-in animate-delay-1">
        <div class="reportes-stat-card">
            <div class="reportes-stat-icon info">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="reportes-stat-value">{{ $analisisActividad['activos_ventas'] }}</div>
            <div class="reportes-stat-label">Activos en Ventas</div>
            <div class="reportes-stat-change neutral">
                {{ $analisisActividad['tasa_actividad_ventas'] }}% del equipo
            </div>
        </div>

        <div class="reportes-stat-card">
            <div class="reportes-stat-icon success">
                <i class="bi bi-people"></i>
            </div>
            <div class="reportes-stat-value">{{ $analisisActividad['activos_referidos'] }}</div>
            <div class="reportes-stat-label">Activos en Referidos</div>
            <div class="reportes-stat-change neutral">
                {{ $analisisActividad['tasa_actividad_referidos'] }}% del equipo
            </div>
        </div>

        <div class="reportes-stat-card">
            <div class="reportes-stat-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="reportes-stat-value">{{ $analisisActividad['total_red'] }}</div>
            <div class="reportes-stat-label">Total Red</div>
            <div class="reportes-stat-change neutral">
                Miembros en total
            </div>
        </div>

        <div class="reportes-stat-card">
            <div class="reportes-stat-icon warning">
                <i class="bi bi-layers"></i>
            </div>
            <div class="reportes-stat-value">{{ $distribucionNiveles['nivel_1'] ?? 0 }} / {{ $distribucionNiveles['nivel_2'] ?? 0 }} / {{ $distribucionNiveles['nivel_3'] ?? 0 }}</div>
            <div class="reportes-stat-label">Distribución</div>
            <div class="reportes-stat-change neutral">
                Niveles 1 / 2 / 3+
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reportes-filter-card fade-in-up animate-delay-2">
        <h3 class="reportes-filter-title">
            <i class="bi bi-funnel"></i>
            Configurar Reporte
        </h3>
        <form method="GET" action="{{ route('lider.reportes.equipo') }}" id="reportesFilterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="reportes-form-label" for="periodo">
                        <i class="bi bi-calendar-range me-1"></i>
                        Periodo
                    </label>
                    <select name="periodo" id="periodo" class="reportes-form-control">
                        <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                        <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre Actual</option>
                        <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="reportes-form-label" for="metrica">
                        <i class="bi bi-speedometer2 me-1"></i>
                        Métrica Principal
                    </label>
                    <select name="metrica" id="metrica" class="reportes-form-control">
                        <option value="ventas" {{ $metrica == 'ventas' ? 'selected' : '' }}>Ventas</option>
                        <option value="referidos" {{ $metrica == 'referidos' ? 'selected' : '' }}>Referidos</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="reportes-form-label">&nbsp;</label>
                    <div class="reportes-btn-group">
                        <button type="submit" class="reportes-btn reportes-btn-primary">
                            <i class="bi bi-search"></i>
                            Generar Reporte
                        </button>
                        <a href="{{ route('lider.reportes.equipo') }}" class="reportes-btn reportes-btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Crecimiento de la Red -->
        <div class="col-xl-8 col-lg-7">
            <div class="reportes-chart-card fade-in-up animate-delay-3">
                <h3 class="reportes-chart-title">
                    <i class="bi bi-graph-up"></i>
                    Crecimiento de la Red
                </h3>
                <div class="reportes-chart-wrapper">
                    <canvas id="crecimientoRedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribución por Niveles -->
        <div class="col-xl-4 col-lg-5">
            <div class="reportes-chart-card fade-in-up animate-delay-3">
                <h3 class="reportes-chart-title">
                    <i class="bi bi-pie-chart"></i>
                    Distribución por Niveles
                </h3>
                <div class="reportes-chart-wrapper" style="min-height:350px">
                    <canvas id="distribucionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    @if($topPerformers->isNotEmpty())
        <div class="reportes-table-container fade-in-up animate-delay-4">
            <div class="reportes-table-header">
                <h3 class="reportes-table-title">
                    <i class="bi bi-trophy"></i>
                    Top Performers ({{ ucfirst($metrica) }})
                </h3>
            </div>
            <div class="container-fluid py-4">
                <div class="row">
                    @foreach($topPerformers->take(6) as $index => $performer)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="reportes-top-card {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                <div class="reportes-top-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="reportes-top-info">
                                    <div class="reportes-top-name">{{ $performer->name }}</div>
                                    @if($metrica == 'ventas')
                                        <div class="reportes-top-value">${{ number_format($performer->total_ventas, 0, ',', '.') }}</div>
                                        <div class="reportes-top-meta">Total en ventas</div>
                                    @else
                                        <div class="reportes-top-value">{{ $performer->nuevos_referidos }}</div>
                                        <div class="reportes-top-meta">Nuevos referidos</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Rendimiento del Equipo -->
    <div class="reportes-table-container fade-in-up animate-delay-5">
        <div class="reportes-table-header">
            <h3 class="reportes-table-title">
                <i class="bi bi-speedometer"></i>
                Rendimiento Individual del Equipo
            </h3>
        </div>
        <div class="table-responsive">
            <table class="reportes-table">
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Ventas {{ ucfirst(str_replace('_', ' ', $periodo)) }}</th>
                        <th>Pedidos</th>
                        <th>Ticket Promedio</th>
                        <th>Nuevos Referidos</th>
                        <th>Rendimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rendimientoEquipo as $miembro)
                        <tr>
                            <td data-label="Miembro">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="reportes-referido-avatar">
                                        {{ strtoupper(substr($miembro['usuario']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="reportes-referido-name">{{ $miembro['usuario']->name }}</div>
                                        <small class="text-muted">{{ $miembro['usuario']->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Ventas">
                                <span class="reportes-amount-display">${{ number_format($miembro['ventas'], 0, ',', '.') }}</span>
                            </td>
                            <td data-label="Pedidos">{{ $miembro['pedidos'] }}</td>
                            <td data-label="Ticket Promedio">${{ number_format($miembro['ticket_promedio'], 0, ',', '.') }}</td>
                            <td data-label="Referidos">
                                @if($miembro['referidos_nuevos'] > 0)
                                    <span class="reportes-badge reportes-badge-completado">{{ $miembro['referidos_nuevos'] }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td data-label="Rendimiento">
                                <div class="reportes-progress-bar">
                                    <div class="reportes-progress-fill" style="width: {{ $miembro['rendimiento'] }}%; background: {{ $miembro['rendimiento'] >= 70 ? 'linear-gradient(90deg, #10b981, #059669)' : ($miembro['rendimiento'] >= 40 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)') }}"></div>
                                </div>
                                <div class="small text-center mt-1">{{ round($miembro['rendimiento']) }}%</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="reportes-empty-state">
                                    <i class="bi bi-inbox reportes-empty-icon"></i>
                                    <div class="reportes-empty-text">No hay datos disponibles</div>
                                    <div class="reportes-empty-subtext">No hay miembros en tu equipo aún</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Metas vs Resultados -->
    @if($metasVsResultados->isNotEmpty())
        <div class="reportes-table-container fade-in-up">
            <div class="reportes-table-header">
                <h3 class="reportes-table-title">
                    <i class="bi bi-target"></i>
                    Metas vs Resultados
                </h3>
            </div>
            <div class="table-responsive">
                <table class="reportes-table">
                    <thead>
                        <tr>
                            <th>Miembro</th>
                            <th>Meta</th>
                            <th>Ventas Actuales</th>
                            <th>Progreso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metasVsResultados as $meta)
                            <tr>
                                <td data-label="Miembro">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="reportes-referido-avatar">
                                            {{ strtoupper(substr($meta['usuario']->name, 0, 1)) }}
                                        </div>
                                        <div class="reportes-referido-name">{{ $meta['usuario']->name }}</div>
                                    </div>
                                </td>
                                <td data-label="Meta">
                                    <strong>${{ number_format($meta['meta'], 0, ',', '.') }}</strong>
                                </td>
                                <td data-label="Ventas">
                                    <span class="reportes-amount-display">${{ number_format($meta['ventas'], 0, ',', '.') }}</span>
                                </td>
                                <td data-label="Progreso">
                                    <div class="reportes-progress-bar">
                                        <div class="reportes-progress-fill" style="width: {{ min($meta['progreso'], 100) }}%; background: {{ $meta['progreso'] >= 100 ? 'linear-gradient(90deg, #10b981, #059669)' : ($meta['progreso'] >= 75 ? 'linear-gradient(90deg, #3b82f6, #2563eb)' : ($meta['progreso'] >= 50 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)')) }}"></div>
                                    </div>
                                    <div class="small text-center mt-1">{{ round($meta['progreso']) }}%</div>
                                </td>
                                <td data-label="Estado">
                                    @if($meta['cumplida'])
                                        <span class="reportes-badge reportes-badge-completado">
                                            <i class="bi bi-check-circle me-1"></i>Cumplida
                                        </span>
                                    @else
                                        <span class="reportes-badge reportes-badge-pendiente">
                                            <i class="bi bi-clock me-1"></i>En Progreso
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/lider/reportes-modern.js') }}?v={{ filemtime(public_path('js/lider/reportes-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de crecimiento
    const crecimientoData = @json($crecimientoRed);

    // Gráfico de Crecimiento de la Red
    const crecimientoChartData = {
        labels: crecimientoData.map(item => item.mes),
        datasets: [{
            label: 'Nuevos Miembros',
            data: crecimientoData.map(item => item.nuevos),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }, {
            label: 'Total Red',
            data: crecimientoData.map(item => item.total),
            borderColor: '#722F37',
            backgroundColor: 'rgba(114, 47, 55, 0.1)',
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#722F37',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            yAxisID: 'y1'
        }]
    };

    const crecimientoOptions = {
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + ' nuevos';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false
                },
                ticks: {
                    callback: function(value) {
                        return value + ' total';
                    }
                }
            }
        }
    };

    reportesManager.initChart('crecimientoRedChart', 'line', crecimientoChartData, crecimientoOptions);

    // Datos de distribución
    const distribucionData = @json($distribucionNiveles);
    const niveles = Object.keys(distribucionData);
    const valores = Object.values(distribucionData);

    // Gráfico de Distribución por Niveles
    const distribucionChartData = {
        labels: niveles.map(nivel => nivel.replace('nivel_', 'Nivel ')),
        datasets: [{
            data: valores,
            backgroundColor: ['#722F37', '#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
            hoverBackgroundColor: ['#5a252d', '#059669', '#2563eb', '#d97706', '#dc2626'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    };

    const distribucionOptions = {
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        size: 13,
                        weight: '600'
                    }
                }
            }
        }
    };

    reportesManager.initChart('distribucionChart', 'doughnut', distribucionChartData, distribucionOptions);

    // Animar números de stats
    document.querySelectorAll('.reportes-stat-value').forEach(el => {
        const text = el.textContent.replace(/[^0-9]/g, '');
        const value = parseInt(text);
        if (!isNaN(value) && value > 0) {
            reportesManager.animateNumber(el, value);
        }
    });
});
</script>
@endpush
