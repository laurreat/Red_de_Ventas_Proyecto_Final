@extends('layouts.vendedor')

@section('title', '- Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/dashboard-modern.css') }}?v={{ filemtime(public_path('css/vendedor/dashboard-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Header Moderno -->
    <div class="vendedor-dashboard-header animate-fade-in-up">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="vendedor-dashboard-title">
                    ¡Hola, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="vendedor-dashboard-subtitle">
                    Aquí tienes tu resumen de actividad y rendimiento del día
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="vendedor-dashboard-date">
                    <i class="bi bi-calendar-event"></i>
                    <span>{{ now()->translatedFormat('d M Y') }}</span>
                </div>
                @if(auth()->user()->meta_mensual > 0)
                <div class="vendedor-dashboard-meta-badge">
                    <i class="bi bi-target"></i>
                    <span>Meta: ${{ number_format((float)auth()->user()->meta_mensual, 0) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards Modernas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="vendedor-stat-card success animate-delay-1">
                <div class="vendedor-stat-icon" style="background:linear-gradient(135deg,var(--success),var(--success-dark));color:#fff;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="vendedor-stat-label">Ventas del Mes</div>
                <div class="vendedor-stat-value text-success" data-stat="ventas_mes">
                    ${{ number_format(to_float($stats['ventas_mes']), 0) }}
                </div>
                @if($stats['meta_mensual'] > 0)
                <div class="vendedor-stat-meta text-success">
                    <i class="bi bi-arrow-up-right"></i>
                    {{ number_format(min((to_float($stats['ventas_mes']) / to_float($stats['meta_mensual'])) * 100, 100), 1) }}% de meta
                </div>
                <div class="vendedor-stat-progress">
                    <div class="vendedor-stat-progress-bar" 
                         style="color:var(--success);width:{{ min((to_float($stats['ventas_mes']) / to_float($stats['meta_mensual'])) * 100, 100) }}%"
                         data-width="{{ min((to_float($stats['ventas_mes']) / to_float($stats['meta_mensual'])) * 100, 100) }}%">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="vendedor-stat-card info animate-delay-2">
                <div class="vendedor-stat-icon" style="background:linear-gradient(135deg,var(--info),var(--info-dark));color:#fff;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="vendedor-stat-label">Comisiones Ganadas</div>
                <div class="vendedor-stat-value text-info" data-stat="comisiones_ganadas">
                    ${{ number_format(to_float($stats['comisiones_ganadas']), 0) }}
                </div>
                @if($stats['comisiones_disponibles'] > 0)
                <div class="vendedor-stat-meta text-success">
                    <i class="bi bi-check-circle"></i>
                    ${{ number_format(to_float($stats['comisiones_disponibles']), 0) }} disponibles
                </div>
                @endif
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="vendedor-stat-card wine animate-delay-3">
                <div class="vendedor-stat-icon" style="background:linear-gradient(135deg,var(--wine),var(--wine-dark));color:#fff;">
                    <i class="bi bi-people"></i>
                </div>
                <div class="vendedor-stat-label">Mis Referidos</div>
                <div class="vendedor-stat-value text-wine" data-stat="total_referidos">
                    {{ number_format($stats['total_referidos']) }}
                </div>
                @if($stats['nuevos_referidos_mes'] > 0)
                <div class="vendedor-stat-meta text-success">
                    <i class="bi bi-plus-circle"></i>
                    +{{ $stats['nuevos_referidos_mes'] }} este mes
                </div>
                @endif
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="vendedor-stat-card warning animate-delay-4">
                <div class="vendedor-stat-icon" style="background:linear-gradient(135deg,var(--warning),var(--warning-dark));color:#fff;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="vendedor-stat-label">Pedidos del Mes</div>
                <div class="vendedor-stat-value text-warning" data-stat="pedidos_mes">
                    {{ number_format($stats['pedidos_mes']) }}
                </div>
                <div class="vendedor-stat-meta text-muted">
                    <i class="bi bi-box-seam"></i>
                    Total procesados
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenido principal -->
        <div class="col-xl-8 mb-4">
            <!-- Acciones Rápidas Modernas -->
            <div class="vendedor-quick-actions mb-4 animate-fade-in">
                <div class="vendedor-quick-actions-header">
                    <h3 class="vendedor-quick-actions-title">
                        <i class="bi bi-lightning-charge"></i>
                        Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('vendedor.pedidos.create') }}" class="vendedor-quick-action-btn">
                                <div class="vendedor-quick-action-icon">
                                    <i class="bi bi-plus-circle"></i>
                                </div>
                                <div class="vendedor-quick-action-label">Nuevo Pedido</div>
                                <div class="vendedor-quick-action-desc">Registrar venta</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('vendedor.clientes.index') }}" class="vendedor-quick-action-btn">
                                <div class="vendedor-quick-action-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="vendedor-quick-action-label">Mis Clientes</div>
                                <div class="vendedor-quick-action-desc">Gestionar</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('vendedor.comisiones.solicitar') }}" class="vendedor-quick-action-btn">
                                <div class="vendedor-quick-action-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="vendedor-quick-action-label">Solicitar Retiro</div>
                                <div class="vendedor-quick-action-desc">Comisiones</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('vendedor.referidos.invitar') }}" class="vendedor-quick-action-btn">
                                <div class="vendedor-quick-action-icon">
                                    <i class="bi bi-share"></i>
                                </div>
                                <div class="vendedor-quick-action-label">Invitar</div>
                                <div class="vendedor-quick-action-desc">Referir amigos</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Evolución Moderno -->
            <div class="vendedor-chart-card animate-fade-in">
                <div class="vendedor-chart-header">
                    <h3 class="vendedor-chart-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        Evolución de Ventas
                    </h3>
                    <span class="vendedor-badge wine">
                        <i class="bi bi-calendar-range"></i>
                        Últimos 6 Meses
                    </span>
                </div>
                <div class="vendedor-chart-body">
                    <div class="vendedor-chart-container">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Derecho Moderno -->
        <div class="col-xl-4 mb-4">
            <!-- Código de Referido Moderno -->
            @if(auth()->user()->codigo_referido)
            <div class="vendedor-referral-card mb-3 animate-scale-in">
                <div class="vendedor-referral-icon">
                    <i class="bi bi-share-fill"></i>
                </div>
                <div class="vendedor-referral-label">Tu Código de Referido</div>
                <div class="vendedor-referral-code">{{ auth()->user()->codigo_referido }}</div>
                <button id="copyReferralBtn" 
                        data-code="{{ auth()->user()->codigo_referido }}" 
                        class="vendedor-referral-btn">
                    <i class="bi bi-clipboard"></i> Copiar código
                </button>
            </div>
            @endif

            <!-- Progreso de Meta Moderno -->
            @if($progresoMetas['meta_mensual'] > 0)
            <div class="vendedor-progress-card mb-3 animate-fade-in">
                <div class="vendedor-progress-header">
                    <h3 class="vendedor-progress-title">
                        <i class="bi bi-bullseye"></i>
                        Progreso de Meta
                    </h3>
                    <span class="vendedor-badge {{ $progresoMetas['porcentaje_cumplimiento'] >= 100 ? 'success' : ($progresoMetas['porcentaje_cumplimiento'] >= 60 ? 'info' : 'warning') }}">
                        {{ number_format(to_float($progresoMetas['porcentaje_cumplimiento']), 1) }}%
                    </span>
                </div>
                <div class="vendedor-progress-body">
                    <div class="vendedor-progress-meta">
                        <div class="vendedor-progress-amount">
                            ${{ number_format(to_float($progresoMetas['ventas_actuales']), 0) }}
                        </div>
                        <div class="vendedor-progress-goal">
                            de ${{ number_format(to_float($progresoMetas['meta_mensual']), 0) }}
                        </div>
                    </div>
                    <div class="vendedor-progress-bar-container">
                        <div class="vendedor-progress-bar-fill" 
                             style="width:{{ min(to_float($progresoMetas['porcentaje_cumplimiento']), 100) }}%"
                             data-width="{{ min(to_float($progresoMetas['porcentaje_cumplimiento']), 100) }}%">
                        </div>
                    </div>
                    <div class="vendedor-progress-stats">
                        <div class="vendedor-progress-stat">
                            <span class="vendedor-progress-stat-label">Días restantes</span>
                            <span class="vendedor-progress-stat-value">{{ $progresoMetas['dias_restantes'] }}</span>
                        </div>
                        <div class="vendedor-progress-stat text-end">
                            <span class="vendedor-progress-stat-label">Estado</span>
                            <span class="vendedor-progress-stat-value">
                                @if($progresoMetas['porcentaje_cumplimiento'] >= 100)
                                    🎉 Logrado
                                @elseif($progresoMetas['porcentaje_cumplimiento'] >= 80)
                                    🔥 Excelente
                                @elseif($progresoMetas['porcentaje_cumplimiento'] >= 60)
                                    📈 Avanzando
                                @else
                                    💪 En progreso
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Pedidos Recientes Modernos -->
            <div class="vendedor-recent-list mb-3 animate-fade-in">
                <div class="vendedor-recent-header">
                    <h3 class="vendedor-recent-title">
                        <i class="bi bi-clock-history"></i>
                        Pedidos Recientes
                    </h3>
                    <a href="{{ route('vendedor.pedidos.index') }}" class="vendedor-btn-sm vendedor-btn-outline">
                        Ver todos
                    </a>
                </div>
                <div>
                    @if($pedidos_recientes->count() > 0)
                        @foreach($pedidos_recientes as $pedido)
                        <div class="vendedor-recent-item">
                            <div class="vendedor-recent-item-main">
                                <div class="vendedor-recent-item-title">{{ $pedido->numero_pedido }}</div>
                                <div class="vendedor-recent-item-subtitle">{{ $pedido->cliente->name }}</div>
                            </div>
                            <div class="vendedor-recent-item-meta">
                                <div class="vendedor-recent-item-value">${{ number_format($pedido->total_final, 0) }}</div>
                                <div class="vendedor-recent-item-date">{{ $pedido->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--gray-400);"></i>
                            <p class="text-muted mt-2 mb-3">No hay pedidos recientes</p>
                            <a href="{{ route('vendedor.pedidos.create') }}" class="vendedor-btn vendedor-btn-wine">
                                <i class="bi bi-plus-circle"></i> Crear primer pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actividad Reciente Moderna -->
            <div class="vendedor-activity-list animate-fade-in">
                <div class="vendedor-activity-header">
                    <h3 class="vendedor-activity-title">
                        <i class="bi bi-activity"></i>
                        Actividad Reciente
                    </h3>
                </div>
                <div class="vendedor-activity-body">
                    <div class="vendedor-activity-item">
                        <div class="vendedor-activity-icon" style="background:linear-gradient(135deg,var(--success),var(--success-dark));color:#fff;">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <div class="vendedor-activity-content">
                            <div class="vendedor-activity-text">Nueva venta registrada</div>
                            <div class="vendedor-activity-time">Hace 2 horas</div>
                        </div>
                    </div>

                    <div class="vendedor-activity-item">
                        <div class="vendedor-activity-icon" style="background:linear-gradient(135deg,var(--info),var(--info-dark));color:#fff;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="vendedor-activity-content">
                            <div class="vendedor-activity-text">Comisión recibida</div>
                            <div class="vendedor-activity-time">Hace 1 día</div>
                        </div>
                    </div>

                    <div class="vendedor-activity-item">
                        <div class="vendedor-activity-icon" style="background:linear-gradient(135deg,var(--wine),var(--wine-dark));color:#fff;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="vendedor-activity-content">
                            <div class="vendedor-activity-text">Nuevo referido registrado</div>
                            <div class="vendedor-activity-time">Hace 3 días</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Datos para los gráficos desde el servidor
window.evolucionVentasData = @json($evolucionVentas);
</script>
<script src="{{ asset('js/vendedor/dashboard-modern.js') }}?v={{ filemtime(public_path('js/vendedor/dashboard-modern.js')) }}"></script>
@endpush