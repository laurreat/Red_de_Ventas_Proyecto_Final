@extends('layouts.lider')

@section('title', ' - Reportes de Ventas')
@section('page-title', 'Reportes de Ventas')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Análisis detallado de las ventas de tu equipo</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="exportarReporte()">
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
            <form method="GET" action="{{ route('lider.reportes.ventas') }}" id="reporteForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-2 mb-3">
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
                        <label for="producto">Producto</label>
                        <select name="producto" id="producto" class="form-control">
                            <option value="">Todos</option>
                            @foreach($productos as $productoItem)
                                <option value="{{ $productoItem->id }}" {{ $producto == $productoItem->id ? 'selected' : '' }}>
                                    {{ $productoItem->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="agrupacion">Agrupar por</label>
                        <select name="agrupacion" id="agrupacion" class="form-control">
                            <option value="dia" {{ $agrupacion == 'dia' ? 'selected' : '' }}>Día</option>
                            <option value="semana" {{ $agrupacion == 'semana' ? 'selected' : '' }}>Semana</option>
                            <option value="mes" {{ $agrupacion == 'mes' ? 'selected' : '' }}>Mes</option>
                            <option value="vendedor" {{ $agrupacion == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Generar Reporte
                        </button>
                        <a href="{{ route('lider.reportes.ventas') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ventas
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                ${{ number_format($resumenEjecutivo['total_ventas'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-currency-dollar text-primary fs-1 opacity-75"></i>
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
                                Total Pedidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $resumenEjecutivo['total_pedidos'] }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-cart-check text-success fs-1 opacity-75"></i>
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
                                ${{ number_format($resumenEjecutivo['ticket_promedio'], 0, ',', '.') }}
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
                                Vendedores Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $resumenEjecutivo['vendedores_activos'] }}
                            </div>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-people text-warning fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Análisis de Tendencias -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Análisis de Tendencias</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="tendenciasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparación con Periodo Anterior -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Comparación de Periodos</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h4 class="text-primary">Periodo Actual</h4>
                        <h2 class="text-success">${{ number_format($comparacionPeriodos['ventas_actual'], 0, ',', '.') }}</h2>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-muted">Periodo Anterior</h4>
                        <h3 class="text-muted">${{ number_format($comparacionPeriodos['ventas_anterior'], 0, ',', '.') }}</h3>
                    </div>
                    <div>
                        <h4>Crecimiento</h4>
                        <h2 class="text-{{ $comparacionPeriodos['crecimiento'] >= 0 ? 'success' : 'danger' }}">
                            {{ $comparacionPeriodos['crecimiento'] > 0 ? '+' : '' }}{{ $comparacionPeriodos['crecimiento'] }}%
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas Agrupadas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ventas Agrupadas por {{ ucfirst($agrupacion) }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ ucfirst($agrupacion) }}</th>
                            <th>Total Ventas</th>
                            <th>Cantidad Pedidos</th>
                            <th>Ticket Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reporteVentas['ventas_agrupadas'] as $grupo => $datos)
                            <tr>
                                <td class="font-weight-bold">
                                    @if($agrupacion == 'vendedor')
                                        {{ $datos['vendedor'] ?? $grupo }}
                                    @else
                                        {{ $grupo }}
                                    @endif
                                </td>
                                <td class="text-success font-weight-bold">
                                    ${{ number_format($datos['total'], 0, ',', '.') }}
                                </td>
                                <td>{{ $datos['cantidad'] }}</td>
                                <td>${{ number_format($datos['promedio'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Producto Más Vendido</h6>
                </div>
                <div class="card-body">
                    @if($resumenEjecutivo['producto_mas_vendido'])
                        <div class="text-center">
                            <h4 class="text-primary">{{ $resumenEjecutivo['producto_mas_vendido']->nombre }}</h4>
                            <h3 class="text-success">{{ $resumenEjecutivo['producto_mas_vendido']->total_vendido }} unidades</h3>
                            <p class="text-muted">Producto con mayor volumen de ventas en el periodo</p>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay datos de productos para el periodo seleccionado</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cliente Más Valioso</h6>
                </div>
                <div class="card-body">
                    @if($resumenEjecutivo['cliente_mas_valioso'])
                        <div class="text-center">
                            <h4 class="text-primary">{{ $resumenEjecutivo['cliente_mas_valioso']->name }}</h4>
                            <h3 class="text-success">${{ number_format($resumenEjecutivo['cliente_mas_valioso']->total_compras, 0, ',', '.') }}</h3>
                            <p class="text-muted">Cliente con mayor valor de compras en el periodo</p>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay datos de clientes para el periodo seleccionado</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    @if($reporteVentas['ventas_detalladas']->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detalle de Ventas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Pedido</th>
                                <th>Vendedor</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reporteVentas['ventas_detalladas']->take(50) as $venta)
                                <tr>
                                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="font-weight-bold text-primary">
                                        #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>{{ $venta->vendedor->name }}</td>
                                    <td>{{ $venta->cliente->name }}</td>
                                    <td class="text-success font-weight-bold">
                                        ${{ number_format($venta->total_final, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $venta->estado == 'completado' ? 'success' : ($venta->estado == 'pendiente' ? 'warning' : 'info') }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($reporteVentas['ventas_detalladas']->count() > 50)
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Mostrando las primeras 50 ventas de {{ $reporteVentas['ventas_detalladas']->count() }} registros encontrados.
                        Use la función de exportar para obtener el reporte completo.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Tendencias
const tendenciasCtx = document.getElementById('tendenciasChart').getContext('2d');
const tendenciasData = @json($analisisTendencias);

new Chart(tendenciasCtx, {
    type: 'line',
    data: {
        labels: tendenciasData.map(item => {
            if (item.fecha) return item.fecha;
            if (item.semana) return 'Semana ' + item.semana;
            if (item.mes) return item.mes;
            return item.periodo || 'N/A';
        }),
        datasets: [{
            label: 'Ventas',
            data: tendenciasData.map(item => item.ventas),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
            fill: true
        }, {
            label: 'Pedidos',
            data: tendenciasData.map(item => item.pedidos),
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

// Función para exportar reporte
function exportarReporte() {
    const form = document.getElementById('reporteForm');
    const formData = new FormData(form);
    formData.append('tipo', 'ventas');
    formData.append('formato', 'excel');

    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("lider.reportes.exportar") }}?' + params.toString();
}
</script>
@endpush
@endsection