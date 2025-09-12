@extends('layouts.app')

@section('title', '- Dashboard Vendedor')

@push('styles')
<style>
    .metric-card {
        border-left: 4px solid var(--arepa-primary);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--arepa-primary);
    }
    
    .metric-label {
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .progress-custom {
        height: 12px;
        border-radius: 10px;
        background-color: #f1f3f4;
    }
    
    .progress-custom .progress-bar {
        border-radius: 10px;
        background: linear-gradient(135deg, var(--arepa-primary) 0%, var(--arepa-accent) 100%);
    }
    
    .quick-action-btn {
        transition: all 0.3s ease;
        border: 2px solid var(--arepa-primary);
        background: transparent;
        color: var(--arepa-primary);
        padding: 1rem;
        border-radius: 12px;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .quick-action-btn:hover {
        background: var(--arepa-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .referral-code {
        background: linear-gradient(135deg, var(--arepa-primary), var(--arepa-accent));
        color: white;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
    }
    
    .level-badge {
        background: linear-gradient(135deg, var(--arepa-accent), var(--arepa-light-burgundy));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">Dashboard Vendedor</h1>
                    <p class="text-muted mb-0">¡Hola {{ auth()->user()->name }}! Aquí tienes tu resumen de ventas</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="level-badge">
                        <i class="bi bi-star-fill me-1"></i>
                        Nivel {{ auth()->user()->nivel_vendedor }}
                    </div>
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-calendar-check me-1"></i>
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-success fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['ventas_mes'], 0) }}</div>
                    <div class="metric-label">Ventas del Mes</div>
                    @if($stats['meta_mensual'] > 0)
                    <div class="progress-custom mt-2">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <small class="text-muted">{{ number_format(min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100), 1) }}% de tu meta</small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin text-info fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['comisiones_ganadas'], 0) }}</div>
                    <div class="metric-label">Comisiones Ganadas</div>
                    @if($stats['comisiones_disponibles'] > 0)
                    <small class="text-success">
                        <i class="bi bi-check-circle"></i>
                        ${{ number_format($stats['comisiones_disponibles'], 0) }} disponibles
                    </small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people text-primary fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_referidos']) }}</div>
                    <div class="metric-label">Mis Referidos</div>
                    @if($stats['nuevos_referidos_mes'] > 0)
                    <small class="text-success">
                        +{{ $stats['nuevos_referidos_mes'] }} este mes
                    </small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check text-warning fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['pedidos_mes']) }}</div>
                    <div class="metric-label">Pedidos del Mes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Acciones Rápidas y Metas -->
        <div class="col-xl-8 mb-4">
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
                            <a href="#" class="quick-action-btn" onclick="showComingSoon('Nuevo Pedido')">
                                <i class="bi bi-plus-circle fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Nuevo Pedido</div>
                                <small>Registrar venta</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="#" class="quick-action-btn" onclick="showComingSoon('Inventario')">
                                <i class="bi bi-boxes fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Inventario</div>
                                <small>Ver productos</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="#" class="quick-action-btn" onclick="showComingSoon('Clientes')">
                                <i class="bi bi-person-lines-fill fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Mis Clientes</div>
                                <small>Gestionar</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="#" class="quick-action-btn" onclick="showComingSoon('Reportes')">
                                <i class="bi bi-graph-up fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Reportes</div>
                                <small>Ver estadísticas</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($stats['meta_mensual'] > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-target me-2"></i>
                        Progreso de Meta Mensual
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Meta: ${{ number_format($stats['meta_mensual']) }}</span>
                                <strong>Vendido: ${{ number_format($stats['ventas_mes']) }}</strong>
                            </div>
                            <div class="progress-custom">
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span>Faltante: ${{ number_format(max($stats['meta_mensual'] - $stats['ventas_mes'], 0)) }}</span>
                                <span>{{ number_format(min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100), 1) }}%</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            @php
                                $progress = ($stats['ventas_mes'] / $stats['meta_mensual']) * 100;
                            @endphp
                            <div class="fs-1">
                                @if($progress >= 100)
                                    🎉
                                @elseif($progress >= 80)
                                    🔥
                                @elseif($progress >= 60)
                                    📈
                                @else
                                    💪
                                @endif
                            </div>
                            <div class="fw-bold text-primary">
                                @if($progress >= 100)
                                    ¡Meta Alcanzada!
                                @elseif($progress >= 80)
                                    ¡Casi ahí!
                                @elseif($progress >= 60)
                                    Buen progreso
                                @else
                                    ¡Vamos por más!
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 mb-4">
            <!-- Código de Referido -->
            @if(auth()->user()->codigo_referido)
            <div class="card mb-3">
                <div class="card-body p-0">
                    <div class="referral-code">
                        <div class="mb-2">
                            <i class="bi bi-share fs-3"></i>
                        </div>
                        <div class="fw-bold mb-1">Tu Código de Referido</div>
                        <div class="fs-3 fw-bold mb-2">{{ auth()->user()->codigo_referido }}</div>
                        <button class="btn btn-light btn-sm" onclick="copyReferralCode()">
                            <i class="bi bi-clipboard me-1"></i>
                            Copiar código
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Últimos Pedidos -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Últimos Pedidos
                    </h6>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Historial Completo')">
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
                                <div class="fw-bold text-success">${{ number_format($pedido->total, 0) }}</div>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m') }}</small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-cart-x fs-3 text-muted"></i>
                            <p class="text-muted mb-0">No hay pedidos recientes</p>
                            <button class="btn btn-primary btn-sm mt-2" onclick="showComingSoon('Nuevo Pedido')">
                                Crear primer pedido
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mis Referidos -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Red de Referidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="fs-1 text-primary">{{ $stats['total_referidos'] }}</div>
                        <div class="text-muted">Referidos Totales</div>
                    </div>
                    
                    @if($stats['total_referidos'] > 0)
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="fw-bold text-success">{{ $stats['referidos_activos'] }}</div>
                                <small class="text-muted">Activos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-info">{{ $stats['nuevos_referidos_mes'] }}</div>
                            <small class="text-muted">Nuevos</small>
                        </div>
                    </div>
                    @else
                    <div class="text-center">
                        <p class="text-muted mb-2">Comparte tu código y empieza a ganar comisiones</p>
                        <button class="btn btn-primary btn-sm" onclick="shareReferralCode()">
                            <i class="bi bi-share me-1"></i>
                            Compartir código
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyReferralCode() {
        const code = '{{ auth()->user()->codigo_referido ?? "" }}';
        navigator.clipboard.writeText(code).then(function() {
            showToast('Código copiado al portapapeles', 'success');
        });
    }
    
    function shareReferralCode() {
        const code = '{{ auth()->user()->codigo_referido ?? "" }}';
        const text = `¡Únete a Arepa la Llanerita con mi código de referido: ${code}!`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Código de Referido - Arepa la Llanerita',
                text: text,
            });
        } else {
            copyReferralCode();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animar barras de progreso
        setTimeout(function() {
            document.querySelectorAll('.progress-bar').forEach(function(bar) {
                bar.style.transition = 'width 1.5s ease-in-out';
            });
        }, 100);
    });
</script>
@endpush