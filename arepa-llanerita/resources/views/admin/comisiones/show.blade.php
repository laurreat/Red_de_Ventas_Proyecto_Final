@extends('layouts.admin')

@section('title', '- Detalle de Comisiones')
@section('page-title', 'Detalle de Comisiones - ' . $vendedor->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.comisiones.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Vendedor -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person-badge me-2"></i>
                        Información del Vendedor
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre</label>
                                <div class="fw-semibold">{{ $vendedor->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <div>{{ $vendedor->email }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Teléfono</label>
                                <div>{{ $vendedor->telefono ?? 'No especificado' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Registro</label>
                                <div>{{ $vendedor->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Vendedor -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-cart fs-2 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">{{ $statsVendedor['total_pedidos'] }}</h3>
                    <p class="text-muted mb-0 small">Total Pedidos</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $statsVendedor['pedidos_entregados'] }}</h3>
                    <p class="text-muted mb-0 small">Pedidos Entregados</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-currency-dollar fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format(to_float($statsVendedor['total_ventas']), 0) }}</h3>
                    <p class="text-muted mb-0 small">Total Ventas</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-percent fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ number_format($statsVendedor['tasa_conversion'], 1) }}%</h3>
                    <p class="text-muted mb-0 small">Tasa Conversión</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comisiones -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-cash-coin me-2"></i>
                        Comisión Ganada
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <h2 class="fw-bold text-success mb-2">${{ number_format(to_float($statsVendedor['comision_ganada']), 0) }}</h2>
                    <p class="text-muted mb-0">Comisión por pedidos entregados (10%)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-hourglass-split me-2"></i>
                        Comisión Pendiente
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <h2 class="fw-bold text-warning mb-2">${{ number_format(to_float($statsVendedor['comision_pendiente']), 0) }}</h2>
                    <p class="text-muted mb-0">Comisión por pedidos en proceso</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Evolución Mensual -->
    @if($comisionesPorMes->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Evolución Últimos 6 Meses
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mes</th>
                                    <th>Pedidos</th>
                                    <th>Ventas</th>
                                    <th>Comisión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comisionesPorMes as $mes)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ \Carbon\Carbon::createFromDate($mes->año, $mes->mes, 1)->format('F Y') }}
                                        </strong>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $mes->pedidos }}</span></td>
                                    <td><strong>${{ number_format(to_float($mes->ventas), 0) }}</strong></td>
                                    <td><strong class="text-success">${{ number_format(to_float($mes->comision), 0) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros para Pedidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-funnel me-2"></i>
                        Filtrar Pedidos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.comisiones.show', $vendedor->id) }}">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio"
                                       value="{{ $fechaInicio }}">
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin"
                                       value="{{ $fechaFin }}">
                            </div>
                            <div class="col-lg-4 col-md-12 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos del Período
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pedidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido #</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                        <th>Comisión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedidos as $pedido)
                                    <tr>
                                        <td>
                                            <strong>{{ $pedido->numero_pedido }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $pedido->cliente->name }}</div>
                                                <small class="text-muted">{{ $pedido->cliente->email }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($pedido->estado == 'entregado')
                                                <span class="badge bg-success">{{ ucfirst($pedido->estado) }}</span>
                                            @elseif($pedido->estado == 'cancelado')
                                                <span class="badge bg-danger">{{ ucfirst($pedido->estado) }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($pedido->estado) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>${{ number_format(to_float($pedido->total_final), 0) }}</strong>
                                        </td>
                                        <td>
                                            @if($pedido->estado == 'entregado')
                                                <strong class="text-success">${{ number_format(to_float($pedido->total_final) * 0.1, 0) }}</strong>
                                            @elseif($pedido->estado != 'cancelado')
                                                <small class="text-warning">Pendiente</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if($pedidos->hasPages())
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-center">
                                {{ $pedidos->links() }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay pedidos</h4>
                            <p class="text-muted">No se encontraron pedidos en el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection