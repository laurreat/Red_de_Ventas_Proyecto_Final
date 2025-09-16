@extends('layouts.admin')

@section('title', '- Dashboard Administrador')
@section('page-title', 'Dashboard Administrador')

@section('content')
<div class="container-fluid">
    <!-- Header con información del día -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Panel de control general del sistema</p>
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
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['total_usuarios']) }}</h3>
                    <p class="text-muted mb-0 small">Total Usuarios</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-person-badge fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['total_vendedores']) }}</h3>
                    <p class="text-muted mb-0 small">Vendedores Activos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-boxes fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ number_format($stats['total_productos']) }}</h3>
                    <p class="text-muted mb-0 small">Productos</p>
                    @if($stats['productos_stock_bajo'] > 0)
                        <small class="text-danger d-block mt-1">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $stats['productos_stock_bajo'] }} con stock bajo
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format($stats['ventas_mes'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ventas del Mes</p>
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
                            <div class="fw-bold fs-4 text-success">{{ number_format($stats['pedidos_hoy']) }}</div>
                            <div class="text-muted small">Pedidos Hoy</div>
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
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4 text-warning">{{ number_format($stats['pedidos_pendientes']) }}</div>
                            <div class="text-muted small">Pedidos Pendientes</div>
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
                                <i class="bi bi-cash-coin fs-4" style="color: var(--primary-color);"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4" style="color: var(--primary-color);">${{ number_format($stats['comisiones_pendientes'], 0) }}</div>
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
                                 style="width: 48px; height: 48px; background-color: rgba(220, 53, 69, 0.1);">
                                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4 text-danger">{{ number_format($stats['productos_stock_bajo']) }}</div>
                            <div class="text-muted small">Stock Crítico</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pedidos Recientes -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Recientes
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Gestión de Pedidos')"
                       style="border-color: var(--primary-color); color: var(--primary-color);">
                        Ver todos
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pedidos_recientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Total</th>
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
                                            @if($pedido->vendedor)
                                                <span class="badge bg-info">{{ $pedido->vendedor->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>${{ number_format($pedido->total_final, 0) }}</strong>
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
                            <p class="text-muted mb-0">No hay pedidos recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos Populares -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-star me-2"></i>
                        Productos Populares
                    </h5>
                </div>
                <div class="card-body">
                    @if($productos_populares->count() > 0)
                        @foreach($productos_populares as $producto)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                <small class="text-muted">{{ $producto->categoria->nombre }}</small>
                                <div class="progress-custom mt-1">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar"
                                             style="width: {{ $productos_populares->first() && $productos_populares->first()->veces_vendido > 0 ? min(($producto->veces_vendido / $productos_populares->first()->veces_vendido) * 100, 100) : 0 }}%; background-color: var(--primary-color);"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end ms-3">
                                <div class="fw-bold" style="color: var(--primary-color);">{{ number_format($producto->veces_vendido) }}</div>
                                <small class="text-muted">vendidos</small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-box fs-1 text-muted"></i>
                            <p class="text-muted mb-0">No hay datos de ventas</p>
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush