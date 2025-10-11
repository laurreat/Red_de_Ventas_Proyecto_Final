@extends('layouts.lider')

@section('title', ' - Ventas del Equipo')
@section('page-title', 'Ventas del Equipo')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Monitorea las ventas y rendimiento de tu equipo</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="exportarVentas()">
                <i class="bi bi-download me-1"></i>
                Exportar Datos
            </button>
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
                                Ventas del Periodo
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['ventas_periodo'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-cart-check text-primary fs-1 opacity-75"></i>
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
                                Pedidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $stats['pedidos_periodo'] }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-bag-check text-success fs-1 opacity-75"></i>
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
                                Ticket Promedio
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($stats['ticket_promedio'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-calculator text-info fs-1 opacity-75"></i>
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
                                Crecimiento
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $stats['crecimiento'] > 0 ? '+' : '' }}{{ $stats['crecimiento'] }}%
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-graph-{{ $stats['crecimiento'] >= 0 ? 'up' : 'down' }} text-{{ $stats['crecimiento'] >= 0 ? 'success' : 'danger' }} fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Evolución de Ventas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Evolución de Ventas</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="evolucionVentasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas por Día de la Semana -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas por Día</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ventasDiaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar Ventas</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lider.ventas.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="periodo">Periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                            <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Semana Actual</option>
                            <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                            <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                            <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre</option>
                            <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="vendedor">Vendedor</label>
                        <select name="vendedor" id="vendedor" class="form-control">
                            <option value="">Todos</option>
                            @foreach($vendedoresEquipo as $vendedorEquipo)
                                <option value="{{ $vendedorEquipo->id }}" {{ $vendedor == $vendedorEquipo->id ? 'selected' : '' }}>
                                    {{ $vendedorEquipo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmado" {{ $estado == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="completado" {{ $estado == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ $estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="cliente">Cliente</label>
                        <input type="text" name="cliente" id="cliente" class="form-control" placeholder="Buscar cliente" value="{{ $cliente }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ventas del Equipo</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Pedido</th>
                            <th>Vendedor</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="font-weight-bold text-primary">
                                        #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($venta->vendedor->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $venta->vendedor->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $venta->cliente->name }}</div>
                                    <div class="small text-muted">{{ $venta->cliente->email }}</div>
                                </td>
                                <td class="font-weight-bold text-success">
                                    ${{ number_format($venta->total_final, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $venta->estado == 'completado' ? 'success' : ($venta->estado == 'pendiente' ? 'warning' : ($venta->estado == 'cancelado' ? 'danger' : 'info')) }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('lider.ventas.show', $venta->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                    No se encontraron ventas para los filtros seleccionados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $ventas->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Ranking de Vendedores -->
    @if($rankingVendedores->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ranking de Vendedores ({{ ucfirst(str_replace('_', ' ', $periodo)) }})</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($rankingVendedores as $vendedorRanking)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-left-{{ $vendedorRanking->posicion <= 3 ? 'success' : 'info' }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-{{ $vendedorRanking->posicion <= 3 ? 'success' : 'info' }} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                #{{ $vendedorRanking->posicion }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold">{{ $vendedorRanking->name }}</div>
                                            <div class="text-success font-weight-bold">
                                                ${{ number_format($vendedorRanking->total_ventas, 0, ',', '.') }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $vendedorRanking->total_pedidos }} pedidos •
                                                Promedio: ${{ number_format($vendedorRanking->ticket_promedio, 0, ',', '.') }}
                                            </div>
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

    <!-- Top Productos -->
    @if($topProductos->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Productos Más Vendidos</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Ingresos Totales</th>
                                <th>Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProductos as $producto)
                                <tr>
                                    <td class="font-weight-bold">{{ $producto->nombre }}</td>
                                    <td>{{ $producto->total_vendido }}</td>
                                    <td class="text-success font-weight-bold">
                                        ${{ number_format($producto->total_ingresos, 0, ',', '.') }}
                                    </td>
                                    <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
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
// Gráfico de Evolución de Ventas
const evolucionCtx = document.getElementById('evolucionVentasChart').getContext('2d');
new Chart(evolucionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucionVentas->pluck('mes')) !!},
        datasets: [{
            label: 'Ventas',
            data: {!! json_encode($evolucionVentas->pluck('ventas')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
            fill: true
        }, {
            label: 'Pedidos',
            data: {!! json_encode($evolucionVentas->pluck('pedidos')) !!},
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
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
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Gráfico de Ventas por Día
const ventasDiaCtx = document.getElementById('ventasDiaChart').getContext('2d');
const ventasPorDiaData = @json($ventasPorDia);
const diasLabels = ventasPorDiaData.map(item => item.dia);
const ventasData = ventasPorDiaData.map(item => item.ventas);

new Chart(ventasDiaCtx, {
    type: 'doughnut',
    data: {
        labels: diasLabels,
        datasets: [{
            data: ventasData,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#717384', '#484856']
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

// Función para exportar
function exportarVentas() {
    const periodo = document.getElementById('periodo').value;
    const vendedor = document.getElementById('vendedor').value;
    const estado = document.getElementById('estado').value;

    const params = new URLSearchParams({
        tipo: 'ventas',
        formato: 'excel',
        periodo: periodo,
        vendedor: vendedor,
        estado: estado
    });

    window.location.href = '{{ route("lider.reportes.exportar") }}?' + params.toString();
}
</script>
@endpush
@endsection