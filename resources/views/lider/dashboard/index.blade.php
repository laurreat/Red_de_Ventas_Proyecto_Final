@extends('layouts.lider')

@section('title', '- Dashboard Líder')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Saludo personalizado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="mb-2 fw-bold">¡Hola, {{ auth()->user()->name }}!</h2>
                            <p class="mb-0 opacity-90">
                                Bienvenido de vuelta. Aquí tienes un resumen del rendimiento de tu equipo.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-inline-flex align-items-center bg-white bg-opacity-20 rounded-pill px-3 py-2">
                                <i class="bi bi-calendar-week me-2"></i>
                                <span>{{ now()->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #198754, #20c997);">
                                <i class="bi bi-people fs-2 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold text-success">{{ $stats['equipo_total'] }}</h3>
                            <p class="text-muted mb-0 small">Miembros Totales</p>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i>
                                {{ $stats['equipo_directo'] }} directos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #0d6efd, #6610f2);">
                                <i class="bi bi-cart-check fs-2 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold text-primary">${{ number_format($stats['ventas_mes_actual'], 0) }}</h3>
                            <p class="text-muted mb-0 small">Ventas del Mes</p>
                            <small class="text-primary">
                                <i class="bi bi-calendar-month"></i>
                                {{ now()->format('M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107, #fd7e14);">
                                <i class="bi bi-currency-dollar fs-2 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold text-warning">${{ number_format($stats['comisiones_mes'], 0) }}</h3>
                            <p class="text-muted mb-0 small">Comisiones del Mes</p>
                            <small class="text-warning">
                                <i class="bi bi-gem"></i>
                                Ganadas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light));">
                                <i class="bi bi-target fs-2 text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold" style="color: var(--primary-color);">{{ number_format($stats['progreso_meta'], 1) }}%</h3>
                            <p class="text-muted mb-0 small">Progreso Meta</p>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar"
                                     style="width: {{ $stats['progreso_meta'] }}%; background: linear-gradient(90deg, var(--primary-color), var(--primary-light));">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de ventas -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-graph-up me-2"></i>
                            Ventas del Equipo (Últimos 30 días)
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active">30 días</button>
                            <button class="btn btn-outline-primary">7 días</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ventasChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top Performers del Mes
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers as $index => $performer)
                            <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                                         style="width: 40px; height: 40px; background: linear-gradient(135deg,
                                         {{ $index === 0 ? '#ffd700, #ffa500' : ($index === 1 ? '#c0c0c0, #808080' : '#cd7f32, #8b4513') }});">
                                        <span class="text-white fw-bold">{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold">{{ $performer['usuario']->name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        ${{ number_format($performer['ventas_mes'], 0) }} •
                                        {{ $performer['pedidos_mes'] }} pedidos
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">
                                        {{ $performer['referidos_mes'] }} ref.
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-trophy fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay datos de rendimiento aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actividad reciente -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-clock-history me-2"></i>
                        Actividad Reciente del Equipo
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    @if($actividadReciente->count() > 0)
                        @foreach($actividadReciente as $actividad)
                            <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                         style="width: 40px; height: 40px; background: linear-gradient(135deg,
                                         {{ $actividad['tipo'] === 'pedido' ? 'var(--primary-color), var(--primary-light)' : '#198754, #20c997' }});">
                                        <i class="bi bi-{{ $actividad['tipo'] === 'pedido' ? 'cart-check' : 'person-plus' }} text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 fw-semibold">{{ $actividad['descripcion'] }}</p>
                                    <small class="text-muted">
                                        {{ $actividad['fecha']->diffForHumans() }}
                                        @if(isset($actividad['monto']))
                                            • ${{ number_format($actividad['monto'], 0) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-activity fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-box-seam me-2"></i>
                        Productos Más Vendidos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($productosMasVendidos->count() > 0)
                        @foreach($productosMasVendidos as $producto)
                            <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                         style="width: 40px; height: 40px; background: linear-gradient(135deg, #6610f2, #0d6efd);">
                                        <i class="bi bi-box text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold">{{ $producto->nombre }}</h6>
                                    <p class="mb-0 text-muted small">
                                        {{ $producto->cantidad_vendida }} unidades •
                                        ${{ number_format($producto->total_ventas, 0) }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">
                                        ${{ number_format($producto->precio, 0) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay ventas de productos aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Metas y objetivos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bullseye me-2"></i>
                        Metas y Objetivos del Mes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($metas as $tipo => $meta)
                            <div class="col-lg-4 mb-3">
                                <div class="p-3 rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 fw-semibold">
                                            @switch($tipo)
                                                @case('ventas_mes')
                                                    <i class="bi bi-cart-check me-2"></i>Ventas del Mes
                                                    @break
                                                @case('equipo_mes')
                                                    <i class="bi bi-people me-2"></i>Crecimiento Equipo
                                                    @break
                                                @case('comisiones_mes')
                                                    <i class="bi bi-currency-dollar me-2"></i>Comisiones
                                                    @break
                                            @endswitch
                                        </h6>
                                        <span class="badge bg-primary">{{ number_format($meta['progreso'], 1) }}%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar"
                                             style="width: {{ $meta['progreso'] }}%; background: linear-gradient(90deg, var(--primary-color), var(--primary-light));">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            Actual: ${{ number_format($meta['actual'], 0) }}
                                        </small>
                                        <small class="text-muted">
                                            Meta: ${{ number_format($meta['objetivo'], 0) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de ventas
    const ventasData = @json($ventasPorDia);
    const fechas = [];
    const totales = [];

    // Generar datos para los últimos 30 días
    for (let i = 29; i >= 0; i--) {
        const fecha = new Date();
        fecha.setDate(fecha.getDate() - i);
        const fechaStr = fecha.toISOString().split('T')[0];

        fechas.push(fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }));
        totales.push(ventasData[fechaStr] ? parseFloat(ventasData[fechaStr].total) : 0);
    }

    // Configurar gráfico de ventas
    const ctx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: 'Ventas ($)',
                data: totales,
                borderColor: 'rgb(114, 47, 55)',
                backgroundColor: 'rgba(114, 47, 55, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(114, 47, 55)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection