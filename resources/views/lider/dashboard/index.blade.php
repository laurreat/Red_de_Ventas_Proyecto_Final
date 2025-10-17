@extends('layouts.lider')

@section('title', '- Dashboard Líder')
@section('page-title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/dashboard-modern.css') }}?v={{ filemtime(public_path('css/lider/dashboard-modern.css')) }}" media="all">
<link rel="preload" href="{{ asset('css/lider/dashboard-modern.css') }}?v={{ filemtime(public_path('css/lider/dashboard-modern.css')) }}" as="style">
<link rel="preload" href="https://cdn.jsdelivr.net/npm/chart.js" as="script" crossorigin="anonymous">
<noscript><link rel="stylesheet" href="{{ asset('css/lider/dashboard-modern.css') }}?v={{ filemtime(public_path('css/lider/dashboard-modern.css')) }}"></noscript>
@endpush

@section('content')
<div role="main" aria-label="Dashboard del Líder">
<div class="container-fluid">
    <!-- Header Hero con Gradiente -->
    <header class="dashboard-header" role="banner">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="dashboard-title" id="page-title">
                    <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>
                    <span>¡Hola, {{ auth()->user()->name }}!</span>
                </h1>
                <p class="dashboard-subtitle">
                    Bienvenido de vuelta. Aquí tienes un resumen del rendimiento de tu equipo en tiempo real
                    <span class="dashboard-realtime-indicator ms-2" role="status" aria-live="polite">
                        <span class="dashboard-realtime-dot" aria-hidden="true"></span>
                        <span>En vivo</span>
                    </span>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="dashboard-date" role="time" aria-label="Fecha actual">
                    <i class="bi bi-calendar-week me-2" aria-hidden="true"></i>
                    <time datetime="{{ now()->toIso8601String() }}">{{ now()->isoFormat('D [de] MMMM, YYYY') }}</time>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="dashboard-stat-card success">
                <div class="d-flex align-items-center">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                        <i class="bi bi-people text-white"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="dashboard-stat-value text-success" data-stat="equipo-total">{{ $stats['equipo_total'] }}</h3>
                        <p class="dashboard-stat-label mb-0">Miembros Totales</p>
                        <div class="dashboard-stat-meta text-success">
                            <i class="bi bi-arrow-up"></i>
                            <span>{{ $stats['equipo_directo'] }} directos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <article class="dashboard-stat-card info" role="article" aria-label="Ventas del mes">
                <div class="d-flex align-items-center">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);" aria-hidden="true">
                        <i class="bi bi-cart-check text-white"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="dashboard-stat-value text-info" data-stat="ventas-mes" aria-live="polite">${{ number_format($stats['ventas_mes_actual'], 0) }}</h3>
                        <p class="dashboard-stat-label mb-0">Ventas del Mes</p>
                        <div class="dashboard-stat-meta text-info">
                            <i class="bi bi-calendar-month" aria-hidden="true"></i>
                            <span>{{ now()->isoFormat('MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <article class="dashboard-stat-card warning" role="article" aria-label="Comisiones del mes">
                <div class="d-flex align-items-center">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, var(--warning), #d97706);" aria-hidden="true">
                        <i class="bi bi-currency-dollar text-white"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="dashboard-stat-value text-warning" data-stat="comisiones-mes" aria-live="polite">${{ number_format($stats['comisiones_mes'], 0) }}</h3>
                        <p class="dashboard-stat-label mb-0">Comisiones del Mes</p>
                        <div class="dashboard-stat-meta text-warning">
                            <i class="bi bi-gem" aria-hidden="true"></i>
                            <span>Ganadas</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <article class="dashboard-stat-card wine" role="article" aria-label="Progreso de meta">
                <div class="d-flex align-items-center">
                    <div class="dashboard-stat-icon" style="background: linear-gradient(135deg, var(--wine), var(--wine-light));" aria-hidden="true">
                        <i class="bi bi-target text-white"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="dashboard-stat-value text-wine" data-stat="progreso-meta" aria-live="polite">{{ number_format($stats['progreso_meta'], 1) }}%</h3>
                        <p class="dashboard-stat-label mb-0">Progreso Meta</p>
                        <div class="progress mt-2" role="progressbar" aria-valuenow="{{ $stats['progreso_meta'] }}" aria-valuemin="0" aria-valuemax="100" style="height: 6px; background: var(--gray-200);">
                            <div class="progress-bar" style="width: {{ $stats['progreso_meta'] }}%; background: linear-gradient(90deg, var(--wine), var(--wine-light));"></div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Charts & Top Performers -->
    <div class="row mb-4">
        <!-- Gráfico de Ventas -->
        <div class="col-lg-8 mb-4">
            <section class="dashboard-chart-card" aria-labelledby="chart-title">
                <div class="dashboard-chart-header">
                    <h2 class="dashboard-chart-title" id="chart-title">
                        <i class="bi bi-graph-up" aria-hidden="true"></i>
                        <span>Ventas del Equipo (Últimos 30 días)</span>
                    </h2>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Filtros de período">
                        <button type="button" class="btn btn-outline-primary active" data-period="30" aria-pressed="true">30 días</button>
                        <button type="button" class="btn btn-outline-primary" data-period="7" aria-pressed="false">7 días</button>
                        <button type="button" class="btn btn-outline-primary" data-action="refresh" aria-label="Actualizar gráfico">
                            <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="dashboard-chart-body">
                    <canvas id="ventasChart" role="img" aria-label="Gráfico de ventas del equipo" style="max-height: 300px;"></canvas>
                </div>
            </section>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-chart-card">
                <div class="dashboard-chart-header">
                    <h5 class="dashboard-chart-title">
                        <i class="bi bi-trophy"></i>
                        Top Performers del Mes
                    </h5>
                </div>
                <div class="p-0">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers as $index => $performer)
                            <div class="dashboard-performer-item">
                                <div class="dashboard-performer-badge {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : 'bronze') }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="dashboard-performer-content">
                                    <h6 class="dashboard-performer-name">{{ $performer['usuario']->name }}</h6>
                                    <p class="dashboard-performer-stats mb-0">
                                        ${{ number_format($performer['ventas_mes'], 0) }} •
                                        {{ $performer['pedidos_mes'] }} pedidos
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">{{ $performer['referidos_mes'] }} ref.</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-trophy fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay datos de rendimiento aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad & Productos -->
    <div class="row mb-4">
        <!-- Actividad Reciente -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-activity-card">
                <div class="dashboard-chart-header">
                    <h5 class="dashboard-chart-title">
                        <i class="bi bi-clock-history"></i>
                        Actividad Reciente del Equipo
                    </h5>
                    <span class="dashboard-realtime-indicator">
                        <span class="dashboard-realtime-dot"></span>
                        En vivo
                    </span>
                </div>
                <div class="dashboard-activity-list" style="max-height: 400px; overflow-y: auto;">
                    @if($actividadReciente->count() > 0)
                        @foreach($actividadReciente as $actividad)
                            <div class="dashboard-activity-item" data-activity-id="{{ $loop->index }}">
                                <div class="dashboard-activity-icon" style="background: linear-gradient(135deg, {{ $actividad['tipo'] === 'pedido' ? 'var(--wine), var(--wine-light)' : 'var(--success), #059669' }});">
                                    <i class="bi bi-{{ $actividad['tipo'] === 'pedido' ? 'cart-check' : 'person-plus' }} text-white"></i>
                                </div>
                                <div class="dashboard-activity-content">
                                    <p class="dashboard-activity-title">{{ $actividad['descripcion'] }}</p>
                                    <small class="dashboard-activity-meta">
                                        {{ $actividad['fecha']->diffForHumans() }}
                                        @if(isset($actividad['monto']))
                                            • ${{ number_format($actividad['monto'], 0) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-activity fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos Más Vendidos -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-activity-card">
                <div class="dashboard-chart-header">
                    <h5 class="dashboard-chart-title">
                        <i class="bi bi-box-seam"></i>
                        Productos Más Vendidos
                    </h5>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @if($productosMasVendidos->count() > 0)
                        @foreach($productosMasVendidos as $producto)
                            <div class="dashboard-product-item">
                                <div class="dashboard-product-icon">
                                    <i class="bi bi-box"></i>
                                </div>
                                <div class="dashboard-product-content">
                                    <h6 class="dashboard-product-name">{{ $producto['nombre'] }}</h6>
                                    <p class="dashboard-product-stats mb-0">
                                        {{ $producto['cantidad_vendida'] }} unidades •
                                        ${{ number_format($producto['total_ventas'], 0) }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">${{ number_format($producto['precio'], 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay ventas de productos aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Metas y Objetivos -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="dashboard-chart-header">
                    <h5 class="dashboard-chart-title">
                        <i class="bi bi-bullseye"></i>
                        Metas y Objetivos del Mes
                    </h5>
                </div>
                <div class="dashboard-chart-body">
                    <div class="row">
                        @foreach($metas as $tipo => $meta)
                            <div class="col-lg-4 mb-3">
                                <div class="dashboard-goal-card" data-goal="{{ $tipo }}">
                                    <div class="dashboard-goal-header">
                                        <h6 class="dashboard-goal-title">
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
                                        <span class="dashboard-goal-badge">{{ number_format($meta['progreso'], 1) }}%</span>
                                    </div>
                                    <div class="dashboard-goal-progress">
                                        <div class="dashboard-goal-progress-bar" style="width: {{ $meta['progreso'] }}%;"></div>
                                    </div>
                                    <div class="dashboard-goal-meta">
                                        <small>Actual: ${{ number_format($meta['actual'], 0) }}</small>
                                        <small>Meta: ${{ number_format($meta['objetivo'], 0) }}</small>
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

<!-- Loading Overlay -->
<div class="dashboard-loading-overlay" role="alert" aria-live="assertive" aria-label="Cargando datos">
    <div class="dashboard-loading-spinner" role="status" aria-label="Cargando..."></div>
    <p class="visually-hidden">Cargando datos del dashboard...</p>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/lider/dashboard-modern.js') }}?v={{ filemtime(public_path('js/lider/dashboard-modern.js')) }}" defer></script>
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
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: $' + context.parsed.y.toLocaleString('es-CO');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-CO');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
});
</script>
@endpush
