@extends('layouts.admin')

@section('title', '- Detalle de Comisiones')
@section('page-title', 'Detalle de Comisiones - ' . $vendedor->name)

@push('styles')
    <link href="{{ asset('css/admin/comisiones-modern.css') }}?v={{ filemtime(public_path('css/admin/comisiones-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header de Navegación --}}
    <div class="row mb-4 animate-fade-in-up">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('admin.comisiones.index') }}" class="comisiones-action-btn">
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver al Listado
                </a>
                <button type="button" class="comisiones-action-btn" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>
                    Imprimir Reporte
                </button>
            </div>
        </div>
    </div>

    {{-- Header del Vendedor --}}
    <div class="comisiones-header animate-fade-in-up animate-delay-1">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <h1><i class="bi bi-person-badge me-2"></i>{{ $vendedor->name }}</h1>
                <p>Detalle de comisiones y rendimiento del vendedor</p>
            </div>
            <div class="text-end">
                <div class="badge bg-white text-wine px-3 py-2" style="font-size: 1rem;">
                    <i class="bi bi-calendar-range me-1"></i>
                    {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Información del Vendedor --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="comisiones-filter-card animate-fade-in-up animate-delay-2">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle me-2"></i>Información del Vendedor</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label text-muted fw-semibold">Nombre Completo</label>
                            <div class="fw-bold text-dark">{{ $vendedor->name }}</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label text-muted fw-semibold">Email</label>
                            <div class="text-dark">{{ $vendedor->email }}</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label text-muted fw-semibold">Teléfono</label>
                            <div class="text-dark">{{ $vendedor->telefono ?? 'No especificado' }}</div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label text-muted fw-semibold">Fecha de Registro</label>
                            <div class="text-dark">{{ $vendedor->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas Principales --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="comisiones-stat-card primary animate-fade-in-up animate-delay-3">
                <div class="icon-wrapper">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="stat-value">{{ $statsVendedor['total_pedidos'] }}</div>
                <div class="stat-label">Total Pedidos</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="comisiones-stat-card success animate-fade-in-up animate-delay-4">
                <div class="icon-wrapper">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-value">{{ $statsVendedor['pedidos_entregados'] }}</div>
                <div class="stat-label">Pedidos Entregados</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="comisiones-stat-card wine animate-fade-in-up animate-delay-5">
                <div class="icon-wrapper">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value">${{ number_format(to_float($statsVendedor['total_ventas']), 0) }}</div>
                <div class="stat-label">Total Ventas</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="comisiones-stat-card warning animate-fade-in-up animate-delay-5">
                <div class="icon-wrapper">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="stat-value">{{ number_format($statsVendedor['tasa_conversion'], 1) }}%</div>
                <div class="stat-label">Tasa Conversión</div>
            </div>
        </div>
    </div>

    {{-- Comisiones --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="comisiones-destacado-card animate-fade-in-up animate-delay-3" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(30, 126, 52, 0.05)); border-color: rgba(40, 167, 69, 0.2);">
                <div class="card-header" style="background: linear-gradient(135deg, var(--success), var(--success-dark));">
                    <h5><i class="bi bi-cash-coin me-2"></i>Comisión Ganada</h5>
                </div>
                <div class="card-body p-0" style="margin-top: 1.5rem;">
                    <div class="avatar" style="background: linear-gradient(135deg, var(--success), var(--success-dark));">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2" style="font-size: 2.5rem;">${{ number_format(to_float($statsVendedor['comision_ganada']), 0) }}</h2>
                    <p class="text-muted mb-0">Comisión por pedidos entregados (10%)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="comisiones-destacado-card animate-fade-in-up animate-delay-4" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.05), rgba(224, 168, 0, 0.05)); border-color: rgba(255, 193, 7, 0.2);">
                <div class="card-header" style="background: linear-gradient(135deg, var(--warning), var(--warning-dark));">
                    <h5><i class="bi bi-hourglass-split me-2"></i>Comisión Pendiente</h5>
                </div>
                <div class="card-body p-0" style="margin-top: 1.5rem;">
                    <div class="avatar" style="background: linear-gradient(135deg, var(--warning), var(--warning-dark));">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2" style="font-size: 2.5rem;">${{ number_format(to_float($statsVendedor['comision_pendiente']), 0) }}</h2>
                    <p class="text-muted mb-0">Comisión por pedidos en proceso</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Evolución Mensual --}}
    @if($comisionesPorMes->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="comisiones-table-container animate-fade-in-up animate-delay-5">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up me-2"></i>Evolución Últimos 6 Meses</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Pedidos</th>
                                    <th>Ventas</th>
                                    <th>Comisión</th>
                                    <th>Tendencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $prevComision = 0; @endphp
                                @foreach($comisionesPorMes as $mes)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::createFromDate($mes->año, $mes->mes, 1)->locale('es')->isoFormat('MMMM YYYY') }}</strong>
                                    </td>
                                    <td>
                                        <span class="comisiones-badge-primary">{{ $mes->pedidos }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-dark">${{ number_format(to_float($mes->ventas), 0) }}</strong>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format(to_float($mes->comision), 0) }}</strong>
                                    </td>
                                    <td>
                                        @if($prevComision > 0)
                                            @if($mes->comision > $prevComision)
                                                <span class="text-success"><i class="bi bi-arrow-up-circle-fill"></i> +{{ number_format((($mes->comision - $prevComision) / $prevComision) * 100, 1) }}%</span>
                                            @elseif($mes->comision < $prevComision)
                                                <span class="text-danger"><i class="bi bi-arrow-down-circle-fill"></i> {{ number_format((($mes->comision - $prevComision) / $prevComision) * 100, 1) }}%</span>
                                            @else
                                                <span class="text-muted"><i class="bi bi-dash-circle"></i> 0%</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                        @php $prevComision = $mes->comision; @endphp
                                    </td>
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

    {{-- Filtros de Pedidos --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="comisiones-filter-card animate-fade-in-up animate-delay-3">
                <div class="card-header">
                    <h5><i class="bi bi-funnel me-2"></i>Filtrar Pedidos del Vendedor</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.comisiones.show', $vendedor->id) }}">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}" required>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}" required>
                            </div>
                            <div class="col-lg-4 col-md-12 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-wine">
                                        <i class="bi bi-search me-1"></i>
                                        Aplicar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de Pedidos --}}
    <div class="row">
        <div class="col-12">
            <div class="comisiones-table-container animate-fade-in-up animate-delay-4">
                <div class="card-header">
                    <h5><i class="bi bi-list-ul me-2"></i>Pedidos del Período ({{ $pedidos->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    @if($pedidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
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
                                            <strong class="text-wine">{{ $pedido->numero_pedido }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $pedido->cliente->name ?? 'Cliente eliminado' }}</div>
                                                <small class="text-muted">{{ $pedido->cliente->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $pedido->created_at->format('d/m/Y') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($pedido->estado == 'entregado')
                                                <span class="comisiones-badge-success">{{ ucfirst($pedido->estado) }}</span>
                                            @elseif($pedido->estado == 'cancelado')
                                                <span class="comisiones-badge-danger">{{ ucfirst($pedido->estado) }}</span>
                                            @else
                                                <span class="comisiones-badge-warning">{{ ucfirst($pedido->estado) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-dark">${{ number_format(to_float($pedido->total_final), 0) }}</strong>
                                        </td>
                                        <td>
                                            @if($pedido->estado == 'entregado')
                                                <strong class="text-success">${{ number_format(to_float($pedido->total_final) * 0.1, 0) }}</strong>
                                                <br>
                                                <small class="comisiones-badge-success" style="font-size: 0.7rem;">Ganada</small>
                                            @elseif($pedido->estado != 'cancelado')
                                                <strong class="text-warning">${{ number_format(to_float($pedido->total_final) * 0.1, 0) }}</strong>
                                                <br>
                                                <small class="comisiones-badge-warning" style="font-size: 0.7rem;">Pendiente</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        @if($pedidos->hasPages())
                        <div class="card-footer bg-white border-top" style="background: #f8f9fa !important;">
                            <div class="d-flex justify-content-center">
                                {{ $pedidos->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h4>No hay pedidos</h4>
                            <p>No se encontraron pedidos en el período seleccionado para este vendedor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/comisiones-modern.js') }}?v={{ filemtime(public_path('js/admin/comisiones-modern.js')) }}"></script>
@endpush
