@extends('layouts.vendedor')

@section('title', '- Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.metric-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.metric-card.ventas {
    border-left-color: #28a745;
}

.metric-card.comisiones {
    border-left-color: #17a2b8;
}

.metric-card.referidos {
    border-left-color: #6f42c1;
}

.metric-card.pedidos {
    border-left-color: #ffc107;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(114, 47, 55, 0.15);
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dasharray 0.35s;
    transform-origin: 50% 50%;
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    border-radius: 0.5rem;
    background: white;
    border: 2px solid #e9ecef;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.quick-action:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(114, 47, 55, 0.1);
}

.quick-action i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    background: rgba(114, 47, 55, 0.02);
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: rgba(114, 47, 55, 0.05);
    border-left-color: var(--primary-color);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    color: white;
    font-size: 1.1rem;
}

.activity-icon.success { background-color: #28a745; }
.activity-icon.info { background-color: #17a2b8; }
.activity-icon.warning { background-color: #ffc107; }
.activity-icon.primary { background-color: var(--primary-color); }

.codigo-referido {
    background: linear-gradient(135deg, var(--primary-color) 0%, #8e3a42 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.codigo-referido::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

.codigo-valor {
    font-size: 1.5rem;
    font-weight: bold;
    letter-spacing: 2px;
    margin: 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Saludo y información básica -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">¡Hola, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-muted mb-0">Aquí tienes tu resumen de actividad del día</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-primary fs-6">
                        <i class="bi bi-calendar-event me-1"></i>
                        {{ now()->format('d/m/Y') }}
                    </div>
                    @if(auth()->user()->meta_mensual > 0)
                    <div class="badge bg-info fs-6">
                        <i class="bi bi-target me-1"></i>
                        Meta: ${{ number_format(auth()->user()->meta_mensual, 0) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card ventas h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format($stats['ventas_mes'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ventas del Mes</p>
                    @if($stats['meta_mensual'] > 0)
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success"
                             style="width: {{ min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format(min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100), 1) }}% de la meta</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card comisiones h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(23, 162, 184, 0.1);">
                        <i class="bi bi-cash-coin fs-2 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-info">${{ number_format($stats['comisiones_ganadas'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Comisiones Ganadas</p>
                    @if($stats['comisiones_disponibles'] > 0)
                    <small class="text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        ${{ number_format($stats['comisiones_disponibles'], 0) }} disponibles
                    </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card referidos h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['total_referidos']) }}</h3>
                    <p class="text-muted mb-0 small">Mis Referidos</p>
                    @if($stats['nuevos_referidos_mes'] > 0)
                    <small class="text-success">
                        +{{ $stats['nuevos_referidos_mes'] }} este mes
                    </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card pedidos h-100">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-cart-check fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ number_format($stats['pedidos_mes']) }}</h3>
                    <p class="text-muted mb-0 small">Pedidos del Mes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenido principal -->
        <div class="col-xl-8 mb-4">
            <!-- Acciones rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('vendedor.pedidos.create') }}" class="quick-action">
                                <i class="bi bi-plus-circle"></i>
                                <div class="fw-medium">Nuevo Pedido</div>
                                <small class="text-muted">Registrar venta</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('vendedor.clientes.index') }}" class="quick-action">
                                <i class="bi bi-people"></i>
                                <div class="fw-medium">Mis Clientes</div>
                                <small class="text-muted">Gestionar</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('vendedor.comisiones.solicitar') }}" class="quick-action">
                                <i class="bi bi-cash-stack"></i>
                                <div class="fw-medium">Solicitar Retiro</div>
                                <small class="text-muted">Comisiones</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('vendedor.referidos.invitar') }}" class="quick-action">
                                <i class="bi bi-share"></i>
                                <div class="fw-medium">Invitar</div>
                                <small class="text-muted">Referir amigos</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de evolución -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Evolución de Ventas (Últimos 6 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar derecho -->
        <div class="col-xl-4 mb-4">
            <!-- Código de referido -->
            @if(auth()->user()->codigo_referido)
            <div class="card mb-3">
                <div class="card-body p-0">
                    <div class="codigo-referido">
                        <div class="mb-2">
                            <i class="bi bi-share fs-3"></i>
                        </div>
                        <div class="fw-medium mb-1">Tu Código de Referido</div>
                        <div class="codigo-valor">{{ auth()->user()->codigo_referido }}</div>
                        <button class="btn btn-light btn-sm mt-2" onclick="copyReferralCode()">
                            <i class="bi bi-clipboard me-1"></i>
                            Copiar código
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Progreso de meta -->
            @if($progresoMetas['meta_mensual'] > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-target me-2"></i>
                        Progreso de Meta
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span>Meta: ${{ number_format($progresoMetas['meta_mensual'], 0) }}</span>
                                <strong>${{ number_format($progresoMetas['ventas_actuales'], 0) }}</strong>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar"
                                     style="width: {{ min($progresoMetas['porcentaje_cumplimiento'], 100) }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span>{{ $progresoMetas['dias_restantes'] }} días restantes</span>
                                <span>{{ number_format($progresoMetas['porcentaje_cumplimiento'], 1) }}%</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fs-1">
                                @if($progresoMetas['porcentaje_cumplimiento'] >= 100)
                                    🎉
                                @elseif($progresoMetas['porcentaje_cumplimiento'] >= 80)
                                    🔥
                                @elseif($progresoMetas['porcentaje_cumplimiento'] >= 60)
                                    📈
                                @else
                                    💪
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Pedidos recientes -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Pedidos Recientes
                    </h6>
                    <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver todos
                    </a>
                </div>
                <div class="card-body">
                    @if($pedidos_recientes->count() > 0)
                        @foreach($pedidos_recientes as $pedido)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <div class="fw-medium">{{ $pedido->numero_pedido }}</div>
                                <small class="text-muted">{{ $pedido->cliente->name }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">${{ number_format($pedido->total_final, 0) }}</div>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m') }}</small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-cart-x fs-3 text-muted"></i>
                            <p class="text-muted mb-0">No hay pedidos recientes</p>
                            <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-primary btn-sm mt-2">
                                Crear primer pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-activity me-2"></i>
                        Actividad Reciente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">Nueva venta registrada</div>
                            <small class="text-muted">hace 2 horas</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon info">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">Comisión recibida</div>
                            <small class="text-muted">hace 1 día</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">Nuevo referido registrado</div>
                            <small class="text-muted">hace 3 días</small>
                        </div>
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
    const evolucionData = @json($evolucionVentas);

    const ctx = document.getElementById('ventasChart').getContext('2d');
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
                tension: 0.4,
                pointBackgroundColor: '#722F37',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
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
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});

function copyReferralCode() {
    const code = '{{ auth()->user()->codigo_referido ?? "" }}';
    navigator.clipboard.writeText(code).then(function() {
        showToast('Código copiado al portapapeles', 'success');
    });
}

// Animar métricas al cargar
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1s ease-in-out';
            bar.style.width = width;
        }, 500);
    });
});
</script>
@endpush