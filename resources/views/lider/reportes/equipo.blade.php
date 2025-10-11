@extends('layouts.lider')

@section('title', ' - Reportes de Equipo')
@section('page-title', 'Reportes de Equipo')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Análisis del rendimiento de tu equipo de trabajo</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="exportarReporteEquipo()">
                <i class="bi bi-download me-1"></i>
                Exportar Reporte
            </button>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Configurar Reporte</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lider.reportes.equipo') }}" id="reporteEquipoForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="periodo">Periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                            <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre Actual</option>
                            <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="metrica">Métrica Principal</label>
                        <select name="metrica" id="metrica" class="form-control">
                            <option value="ventas" {{ $metrica == 'ventas' ? 'selected' : '' }}>Ventas</option>
                            <option value="referidos" {{ $metrica == 'referidos' ? 'selected' : '' }}>Referidos</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Generar Reporte
                            </button>
                            <a href="{{ route('lider.reportes.equipo') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Métricas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Activos en Ventas
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $analisisActividad['activos_ventas'] }}
                            </div>
                            <div class="small text-muted">
                                {{ $analisisActividad['tasa_actividad_ventas'] }}% del equipo
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-person-check text-primary fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Activos en Referidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $analisisActividad['activos_referidos'] }}
                            </div>
                            <div class="small text-muted">
                                {{ $analisisActividad['tasa_actividad_referidos'] }}% del equipo
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-people text-success fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Red
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $analisisActividad['total_red'] }}
                            </div>
                            <div class="small text-muted">
                                Miembros en total
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-diagram-3 text-info fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Distribución
                            </div>
                            <div class="h6 mb-0 font-weight-bold">
                                {{ $distribucionNiveles['nivel_1'] ?? 0 }} / {{ $distribucionNiveles['nivel_2'] ?? 0 }} / {{ $distribucionNiveles['nivel_3'] ?? 0 }}
                            </div>
                            <div class="small text-muted">
                                Niveles 1 / 2 / 3+
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-layers text-warning fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Crecimiento de la Red -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Crecimiento de la Red</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="crecimientoRedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución por Niveles -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Niveles</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="distribucionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rendimiento del Equipo -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rendimiento Individual del Equipo</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
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
                        @foreach($rendimientoEquipo as $miembro)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($miembro['usuario']->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $miembro['usuario']->name }}</div>
                                            <div class="small text-muted">{{ $miembro['usuario']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-success font-weight-bold">
                                    ${{ number_format($miembro['ventas'], 0, ',', '.') }}
                                </td>
                                <td>{{ $miembro['pedidos'] }}</td>
                                <td>${{ number_format($miembro['ticket_promedio'], 0, ',', '.') }}</td>
                                <td>
                                    @if($miembro['referidos_nuevos'] > 0)
                                        <span class="badge badge-success">{{ $miembro['referidos_nuevos'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $miembro['rendimiento'] >= 70 ? 'success' : ($miembro['rendimiento'] >= 40 ? 'warning' : 'danger') }}"
                                             style="width: {{ $miembro['rendimiento'] }}%"></div>
                                    </div>
                                    <div class="small text-center">{{ round($miembro['rendimiento']) }}%</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    @if($topPerformers->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performers ({{ ucfirst($metrica) }})</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($topPerformers->take(6) as $index => $performer)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-left-{{ $index < 3 ? 'success' : 'info' }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-{{ $index < 3 ? 'success' : 'info' }} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                #{{ $index + 1 }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold">{{ $performer->name }}</div>
                                            @if($metrica == 'ventas')
                                                <div class="text-success font-weight-bold">
                                                    ${{ number_format($performer->total_ventas, 0, ',', '.') }}
                                                </div>
                                            @else
                                                <div class="text-info font-weight-bold">
                                                    {{ $performer->nuevos_referidos }} nuevos referidos
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Metas vs Resultados -->
    @if($metasVsResultados->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Metas vs Resultados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                    <td>
                                        <div class="font-weight-bold">{{ $meta['usuario']->name }}</div>
                                    </td>
                                    <td class="font-weight-bold">
                                        ${{ number_format($meta['meta'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-success">
                                        ${{ number_format($meta['ventas'], 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="progress mb-1" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $meta['progreso'] >= 100 ? 'success' : ($meta['progreso'] >= 75 ? 'info' : ($meta['progreso'] >= 50 ? 'warning' : 'danger')) }}"
                                                 style="width: {{ min($meta['progreso'], 100) }}%">
                                                {{ round($meta['progreso']) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($meta['cumplida'])
                                            <span class="badge badge-success">
                                                <i class="bi bi-check-circle me-1"></i>Cumplida
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
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
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Crecimiento de la Red
const crecimientoCtx = document.getElementById('crecimientoRedChart').getContext('2d');
const crecimientoData = @json($crecimientoRed);

new Chart(crecimientoCtx, {
    type: 'line',
    data: {
        labels: crecimientoData.map(item => item.mes),
        datasets: [{
            label: 'Nuevos Miembros',
            data: crecimientoData.map(item => item.nuevos),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.3,
            fill: true
        }, {
            label: 'Total Red',
            data: crecimientoData.map(item => item.total),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
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
                beginAtZero: true
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Gráfico de Distribución por Niveles
const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
const distribucionData = @json($distribucionNiveles);
const niveles = Object.keys(distribucionData);
const valores = Object.values(distribucionData);

new Chart(distribucionCtx, {
    type: 'doughnut',
    data: {
        labels: niveles.map(nivel => nivel.replace('nivel_', 'Nivel ')),
        datasets: [{
            data: valores,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Función para exportar reporte
function exportarReporteEquipo() {
    const form = document.getElementById('reporteEquipoForm');
    const formData = new FormData(form);
    formData.append('tipo', 'equipo');
    formData.append('formato', 'excel');

    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("lider.reportes.exportar") }}?' + params.toString();
}
</script>
@endpush
@endsection