@extends('layouts.lider')

@section('title', ' - Mis Comisiones')
@section('page-title', 'Mis Comisiones')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Gestiona tus comisiones y solicita pagos</p>
        </div>
        <div>
            @if(auth()->user()->comisiones_disponibles > 0)
                <a href="{{ route('lider.comisiones.solicitar') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Solicitar Pago
                </a>
            @endif
        </div>
    </div>

    <!-- Cards de estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ganado
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['total_ganado'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-trophy text-primary fs-1 opacity-75"></i>
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
                                Disponible
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['disponible'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-wallet2 text-success fs-1 opacity-75"></i>
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
                                Mes Actual
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['mes_actual'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-calendar3 text-info fs-1 opacity-75"></i>
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
                                Promedio Mensual
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['promedio_mensual'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-graph-up text-warning fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Evolución -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Evolución de Comisiones</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="evolucionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown por Tipo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Comisiones por Tipo</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="tipoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar Comisiones</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lider.comisiones.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="periodo">Periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                            <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                            <option value="ultimo_trimestre" {{ $periodo == 'ultimo_trimestre' ? 'selected' : '' }}>Último Trimestre</option>
                            <option value="ultimo_ano" {{ $periodo == 'ultimo_ano' ? 'selected' : '' }}>Último Año</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tipo">Tipo de Comisión</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="todas" {{ $tipo == 'todas' ? 'selected' : '' }}>Todas</option>
                            <option value="venta_directa" {{ $tipo == 'venta_directa' ? 'selected' : '' }}>Venta Directa</option>
                            <option value="referido" {{ $tipo == 'referido' ? 'selected' : '' }}>Referido</option>
                            <option value="liderazgo" {{ $tipo == 'liderazgo' ? 'selected' : '' }}>Liderazgo</option>
                            <option value="bono" {{ $tipo == 'bono' ? 'selected' : '' }}>Bono</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('lider.comisiones.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Comisiones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Historial de Comisiones</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Referido</th>
                            <th>Pedido</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comisiones as $comision)
                            <tr>
                                <td>{{ $comision->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $comision->tipo == 'venta_directa' ? 'primary' : ($comision->tipo == 'referido' ? 'success' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $comision->tipo)) }}
                                    </span>
                                </td>
                                <td class="font-weight-bold text-success">
                                    ${{ number_format($comision->monto, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($comision->referido)
                                        {{ $comision->referido->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($comision->pedido)
                                        <a href="#" class="text-primary">
                                            #{{ str_pad($comision->pedido->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $comision->estado == 'pagado' ? 'success' : 'warning' }}">
                                        {{ ucfirst($comision->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No se encontraron comisiones para el periodo seleccionado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $comisiones->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Top Generadores -->
    @if($topGeneradores->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Generadores de Comisiones (Este Mes)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($topGeneradores as $index => $generador)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="me-3">
                                    <div class="text-primary font-weight-bold">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ $generador->referido->name }}</div>
                                    <div class="text-success font-weight-bold">
                                        ${{ number_format($generador->total_generado, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Solicitudes Pendientes -->
    @if($solicitudesPendientes->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Solicitudes de Pago Pendientes</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha Solicitud</th>
                                <th>Monto</th>
                                <th>Método de Pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="font-weight-bold text-primary">
                                        ${{ number_format($solicitud->monto, 0, ',', '.') }}
                                    </td>
                                    <td>{{ ucfirst($solicitud->metodo_pago) }}</td>
                                    <td>
                                        <span class="badge badge-warning">{{ ucfirst($solicitud->estado) }}</span>
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
// Gráfico de Evolución
const evolucionCtx = document.getElementById('evolucionChart').getContext('2d');
new Chart(evolucionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucionComisiones->pluck('mes')) !!},
        datasets: [{
            label: 'Comisiones',
            data: {!! json_encode($evolucionComisiones->pluck('total')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Gráfico de Tipos
const tipoCtx = document.getElementById('tipoChart').getContext('2d');
const breakdownData = @json($breakdownTipo);
const labels = Object.keys(breakdownData);
const data = Object.values(breakdownData).map(item => item.total);

new Chart(tipoCtx, {
    type: 'doughnut',
    data: {
        labels: labels.map(label => label.replace('_', ' ').toUpperCase()),
        datasets: [{
            data: data,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
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
</script>
@endpush
@endsection