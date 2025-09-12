@extends('layouts.app')

@section('title', '- Dashboard Administrador')

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
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .progress-custom {
        height: 8px;
        border-radius: 10px;
        background-color: #f1f3f4;
    }
    
    .progress-custom .progress-bar {
        border-radius: 10px;
        background: linear-gradient(135deg, var(--arepa-primary) 0%, var(--arepa-accent) 100%);
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
                    <h1 class="h3 mb-0 fw-bold">Dashboard Administrador</h1>
                    <p class="text-muted mb-0">Panel de control general del sistema</p>
                </div>
                <div>
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
                    <i class="bi bi-people text-primary fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_usuarios']) }}</div>
                    <div class="metric-label">Total Usuarios</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge text-info fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_vendedores']) }}</div>
                    <div class="metric-label">Vendedores Activos</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-boxes text-warning fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_productos']) }}</div>
                    <div class="metric-label">Productos</div>
                    @if($stats['productos_stock_bajo'] > 0)
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $stats['productos_stock_bajo'] }} con stock bajo
                        </small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-success fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['ventas_mes'], 0) }}</div>
                    <div class="metric-label">Ventas del Mes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Secundarias -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-cart-check text-success fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4">{{ number_format($stats['pedidos_hoy']) }}</div>
                            <div class="text-muted small">Pedidos Hoy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history text-warning fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4">{{ number_format($stats['pedidos_pendientes']) }}</div>
                            <div class="text-muted small">Pedidos Pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-cash-coin text-info fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4">${{ number_format($stats['comisiones_pendientes'], 0) }}</div>
                            <div class="text-muted small">Comisiones Pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold fs-4">{{ number_format($stats['productos_stock_bajo']) }}</div>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Recientes
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Gestión de Pedidos')">
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
                                            <strong>${{ number_format($pedido->total, 0) }}</strong>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
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
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ min(($producto->veces_vendido / $productos_populares->first()->veces_vendido) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end ms-3">
                                <div class="fw-bold text-primary">{{ number_format($producto->veces_vendido) }}</div>
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