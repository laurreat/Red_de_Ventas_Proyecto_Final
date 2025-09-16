@extends('layouts.vendedor')

@section('title', '- Dashboard Vendedor')
@section('page-title', 'Dashboard Vendedor')

@section('content')
<div class="container-fluid">
    <!-- Header con información del día -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">¡Hola {{ auth()->user()->name }}! Aquí tienes tu resumen de ventas</p>
                    <small class="text-muted">Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
                </div>
                <div>
                    <span class="badge" style="background-color: var(--primary-color); font-size: 0.875rem; position: relative; z-index: 1;">
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
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format($stats['ventas_mes'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ventas del Mes</p>
                    @if(isset($stats['meta_mensual']) && $stats['meta_mensual'] > 0)
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-target"></i>
                            {{ $stats['meta_mensual'] > 0 ? number_format(min(($stats['ventas_mes'] / $stats['meta_mensual']) * 100, 100), 1) : 0 }}% de meta
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-cash-coin fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format($stats['comisiones_ganadas'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Comisiones Ganadas</p>
                    @if(isset($stats['comisiones_disponibles']) && $stats['comisiones_disponibles'] > 0)
                        <small class="text-success d-block mt-1">
                            <i class="bi bi-check-circle"></i>
                            ${{ number_format($stats['comisiones_disponibles'], 0) }} disponibles
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['total_referidos']) }}</h3>
                    <p class="text-muted mb-0 small">Mis Referidos</p>
                    @if(isset($stats['nuevos_referidos_mes']) && $stats['nuevos_referidos_mes'] > 0)
                        <small class="text-success d-block mt-1">
                            <i class="bi bi-plus-circle"></i>
                            +{{ $stats['nuevos_referidos_mes'] }} este mes
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
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

    <!-- Métricas Secundarias -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(40, 167, 69, 0.1);">
                                <i class="bi bi-cart-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4 text-success">{{ number_format($stats['pedidos_hoy'] ?? 0) }}</div>
                            <div class="text-muted small">Ventas Hoy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(114, 47, 55, 0.1);">
                                <i class="bi bi-people-fill fs-4" style="color: var(--primary-color);"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4" style="color: var(--primary-color);">{{ number_format($stats['referidos_activos'] ?? 0) }}</div>
                            <div class="text-muted small">Referidos Activos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(255, 193, 7, 0.1);">
                                <i class="bi bi-cash-stack text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4 text-warning">${{ number_format($stats['comisiones_pendientes'] ?? 0, 0) }}</div>
                            <div class="text-muted small">Comisiones Pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(23, 162, 184, 0.1);">
                                <i class="bi bi-star-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4 text-info">{{ auth()->user()->nivel_vendedor ?? 1 }}</div>
                            <div class="text-muted small">Nivel Vendedor</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Últimas Ventas Recientes -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Mis Ventas Recientes
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Historial Completo')"
                       style="border-color: var(--primary-color); color: var(--primary-color);">
                        Ver todas
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($pedidos_recientes) && $pedidos_recientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Comisión</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedidos_recientes as $pedido)
                                    <tr>
                                        <td>
                                            <strong>{{ $pedido->numero_pedido }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="bi bi-person text-white small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $pedido->cliente->name }}</div>
                                                    <small class="text-muted">{{ $pedido->cliente->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($pedido->total_final, 0) }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">${{ number_format($pedido->comision_vendedor ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pendiente' => 'warning',
                                                    'confirmado' => 'info',
                                                    'en_preparacion' => 'primary',
                                                    'listo' => 'success',
                                                    'en_camino' => 'info',
                                                    'entregado' => 'success',
                                                    'cancelado' => 'danger'
                                                ];
                                            @endphp
                                            <span class="status-badge bg-{{ $statusColors[$pedido->estado] ?? 'secondary' }} text-white">
                                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <p class="text-muted mb-0">No tienes ventas recientes</p>
                            <button class="btn btn-primary btn-sm mt-2" onclick="showComingSoon('Nueva Venta')">
                                Registrar primera venta
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-lightning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="#" class="d-block text-decoration-none" onclick="showComingSoon('Nueva Venta')">
                                <div class="text-center p-3 rounded" style="background-color: rgba(114, 47, 55, 0.05); border: 1px solid rgba(114, 47, 55, 0.1);">
                                    <i class="bi bi-plus-circle fs-2 mb-2" style="color: var(--primary-color);"></i>
                                    <div class="fw-bold" style="color: var(--primary-color);">Nueva Venta</div>
                                    <small class="text-muted">Registrar</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="#" class="d-block text-decoration-none" onclick="showComingSoon('Productos')">
                                <div class="text-center p-3 rounded" style="background-color: rgba(40, 167, 69, 0.05); border: 1px solid rgba(40, 167, 69, 0.1);">
                                    <i class="bi bi-boxes fs-2 mb-2 text-success"></i>
                                    <div class="fw-bold text-success">Productos</div>
                                    <small class="text-muted">Catálogo</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="#" class="d-block text-decoration-none" onclick="showComingSoon('Mis Clientes')">
                                <div class="text-center p-3 rounded" style="background-color: rgba(23, 162, 184, 0.05); border: 1px solid rgba(23, 162, 184, 0.1);">
                                    <i class="bi bi-people fs-2 mb-2 text-info"></i>
                                    <div class="fw-bold text-info">Clientes</div>
                                    <small class="text-muted">Gestionar</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="#" class="d-block text-decoration-none" onclick="showComingSoon('Reportes')">
                                <div class="text-center p-3 rounded" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.1);">
                                    <i class="bi bi-graph-up fs-2 mb-2 text-warning"></i>
                                    <div class="fw-bold text-warning">Reportes</div>
                                    <small class="text-muted">Análisis</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->codigo_referido)
                    <div class="mt-3 pt-3 border-top">
                        <div class="text-center">
                            <i class="bi bi-share fs-3 mb-2" style="color: var(--primary-color);"></i>
                            <div class="fw-bold mb-1">Código de Referido</div>
                            <div class="fs-4 fw-bold mb-2" style="color: var(--primary-color);">{{ auth()->user()->codigo_referido }}</div>
                            <button class="btn btn-outline-primary btn-sm" onclick="copyReferralCode()"
                                    style="border-color: var(--primary-color); color: var(--primary-color);">
                                <i class="bi bi-clipboard me-1"></i>
                                Copiar código
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Meta y Red de Referidos -->
            @if(isset($stats['meta_mensual']) && $stats['meta_mensual'] > 0)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-target me-2"></i>
                        Progreso de Meta
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $progress = $stats['meta_mensual'] > 0 ? ($stats['ventas_mes'] / $stats['meta_mensual']) * 100 : 0;
                    @endphp
                    <div class="text-center mb-3">
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
                        <div class="fw-bold" style="color: var(--primary-color);">
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
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Meta: ${{ number_format($stats['meta_mensual']) }}</span>
                        <strong>{{ number_format(min($progress, 100), 1) }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ min($progress, 100) }}%; background-color: var(--primary-color);"></div>
                    </div>
                    <small class="text-muted">Faltante: ${{ number_format(max($stats['meta_mensual'] - $stats['ventas_mes'], 0)) }}</small>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Red de Referidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="fs-1" style="color: var(--primary-color);">{{ $stats['total_referidos'] }}</div>
                        <div class="text-muted">Referidos Totales</div>
                    </div>

                    @if($stats['total_referidos'] > 0)
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="fw-bold text-success">{{ $stats['referidos_activos'] ?? 0 }}</div>
                                    <small class="text-muted">Activos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold text-info">{{ $stats['nuevos_referidos_mes'] ?? 0 }}</div>
                                <small class="text-muted">Nuevos</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted mb-2 small">Comparte tu código y empieza a ganar comisiones</p>
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
    // Actualizar métricas cada 5 minutos
    setInterval(function() {
        window.location.reload();
    }, 300000);

    // Mostrar tooltips en las métricas
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips solo si Bootstrap está disponible
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        } else {
            // Fallback: usar atributo title nativo si Bootstrap no está disponible
            var tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipElements.forEach(function(element) {
                if (element.getAttribute('data-bs-title')) {
                    element.setAttribute('title', element.getAttribute('data-bs-title'));
                }
            });
        }
    });

    function copyReferralCode() {
        const code = '{{ auth()->user()->codigo_referido ?? "" }}';
        navigator.clipboard.writeText(code).then(function() {
            alert('Código copiado al portapapeles');
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
</script>
@endpush