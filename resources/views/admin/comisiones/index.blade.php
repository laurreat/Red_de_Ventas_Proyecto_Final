@extends('layouts.admin')

@section('title', '- Gestión de Comisiones')
@section('page-title', 'Gestión de Comisiones')

@push('styles')
    <link href="{{ asset('css/admin/comisiones-modern.css') }}?v={{ filemtime(public_path('css/admin/comisiones-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="comisiones-header animate-fade-in-up">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1><i class="bi bi-calculator me-2"></i>Gestión de Comisiones</h1>
                <p>Control y seguimiento de comisiones de vendedores</p>
            </div>
            <div class="actions">
                <button type="button" class="btn" data-action="calcular-comisiones">
                    <i class="bi bi-calculator me-1"></i>
                    Calcular Comisiones
                </button>
                <button type="button" class="btn" data-action="exportar-comisiones">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="comisiones-filter-card animate-fade-in-up animate-delay-1">
        <div class="card-header">
            <h5><i class="bi bi-funnel me-2"></i>Filtros de Período</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.comisiones.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ $fechaInicio }}" required>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ $fechaFin }}" required>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label">Vendedor</label>
                        <select class="form-select" name="vendedor_id">
                            <option value="">Todos los vendedores</option>
                            @foreach($vendedores as $vendedor)
                                <option value="{{ $vendedor->id }}" {{ $vendedorId == $vendedor->id ? 'selected' : '' }}>
                                    {{ $vendedor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-wine">
                                <i class="bi bi-search me-1"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="comisiones-stat-card success animate-fade-in-up animate-delay-2">
                <div class="icon-wrapper">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value">${{ number_format(to_float($stats['total_comisiones_ganadas']), 0) }}</div>
                <div class="stat-label">Comisiones Ganadas</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="comisiones-stat-card warning animate-fade-in-up animate-delay-3">
                <div class="icon-wrapper">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-value">${{ number_format(to_float($stats['total_comisiones_pendientes']), 0) }}</div>
                <div class="stat-label">Comisiones Pendientes</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="comisiones-stat-card wine animate-fade-in-up animate-delay-4">
                <div class="icon-wrapper">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value">{{ $stats['vendedores_activos'] }}</div>
                <div class="stat-label">Vendedores Activos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="comisiones-stat-card primary animate-fade-in-up animate-delay-5">
                <div class="icon-wrapper">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <div class="stat-value">${{ number_format(to_float($stats['promedio_comision']), 0) }}</div>
                <div class="stat-label">Promedio Comisión</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tabla de Comisiones --}}
        <div class="col-lg-8 mb-4">
            <div class="comisiones-table-container animate-fade-in-up animate-delay-2">
                <div class="card-header">
                    <h5><i class="bi bi-table me-2"></i>Comisiones por Vendedor</h5>
                </div>
                <div class="card-body p-0">
                    @if($comisiones->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Pedidos</th>
                                        <th>Entregados</th>
                                        <th>Total Ventas</th>
                                        <th>Comisión Ganada</th>
                                        <th>Comisión Pendiente</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comisiones as $comision)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $comision->name }}</div>
                                                <small class="text-muted">{{ $comision->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="comisiones-badge-primary">{{ $comision->total_pedidos }}</span>
                                        </td>
                                        <td>
                                            <span class="comisiones-badge-success">{{ $comision->pedidos_entregados }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-dark">${{ number_format(to_float($comision->total_ventas), 0) }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format(to_float($comision->comision_ganada), 0) }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-warning">${{ number_format(to_float($comision->comision_pendiente), 0) }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.comisiones.show', $comision->id) }}"
                                               class="comisiones-action-btn comisiones-action-btn-view"
                                               title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-calculator"></i>
                            <h4>No hay comisiones</h4>
                            <p>No se encontraron comisiones en el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 mb-4">
            {{-- Top 5 Vendedores --}}
            <div class="comisiones-top-card animate-fade-in-up animate-delay-3 mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-trophy me-2"></i>Top 5 Vendedores</h5>
                </div>
                <div class="card-body p-0">
                    @if($topVendedores->count() > 0)
                        @foreach($topVendedores as $vendedor)
                            <div class="comisiones-top-item">
                                <div class="d-flex align-items-center">
                                    <div class="rank {{ $loop->iteration == 1 ? 'gold' : ($loop->iteration == 2 ? 'silver' : ($loop->iteration == 3 ? 'bronze' : '')) }}"
                                         style="{{ $loop->iteration > 3 ? 'background: #e9ecef; color: #6c757d;' : '' }}">
                                        @if($loop->iteration == 1)
                                            🥇
                                        @elseif($loop->iteration == 2)
                                            🥈
                                        @elseif($loop->iteration == 3)
                                            🥉
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </div>
                                    <div class="info">
                                        <h6>{{ $vendedor->name }}</h6>
                                        <small>{{ $vendedor->pedidos_entregados }} pedidos entregados</small>
                                    </div>
                                </div>
                                <div class="stats">
                                    <div class="amount">${{ number_format(to_float($vendedor->comision_ganada), 0) }}</div>
                                    <div class="sales">${{ number_format(to_float($vendedor->total_ventas), 0) }} en ventas</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-trophy" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay datos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vendedor Destacado --}}
            @if($stats['mejor_vendedor'])
            <div class="comisiones-destacado-card animate-fade-in-up animate-delay-4">
                <div class="card-header">
                    <h5><i class="bi bi-star me-2"></i>Vendedor Destacado</h5>
                </div>
                <div class="card-body p-0" style="margin-top: 1.5rem;">
                    <div class="avatar">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <h6>{{ $stats['mejor_vendedor']->name }}</h6>
                    <div class="email">{{ $stats['mejor_vendedor']->email }}</div>
                    <div class="metrics">
                        <div class="metric">
                            <h6 class="text-success">${{ number_format(to_float($stats['mejor_vendedor']->comision_ganada), 0) }}</h6>
                            <small>Comisión</small>
                        </div>
                        <div class="metric">
                            <h6 class="text-wine">{{ $stats['mejor_vendedor']->pedidos_entregados }}</h6>
                            <small>Entregados</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Evolución Diaria --}}
    @if($comisionesPorDia->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="comisiones-table-container animate-fade-in-up animate-delay-5">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up me-2"></i>Evolución Diaria de Comisiones</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Pedidos</th>
                                    <th>Ventas</th>
                                    <th>Comisiones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comisionesPorDia as $dia)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</strong></td>
                                    <td><span class="comisiones-badge-primary">{{ $dia->pedidos }}</span></td>
                                    <td><strong class="text-dark">${{ number_format(to_float($dia->ventas), 0) }}</strong></td>
                                    <td><strong class="text-success">${{ number_format(to_float($dia->comisiones), 0) }}</strong></td>
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
</div>
@endsection

@push('scripts')
<script>
    window.comisionesRoutes = {
        calcular: '{{ route("admin.comisiones.calcular") }}',
        exportar: '{{ route("admin.comisiones.exportar") }}'
    };
    window.comisionesCSRF = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/admin/comisiones-modern.js') }}?v={{ filemtime(public_path('js/admin/comisiones-modern.js')) }}"></script>
@endpush
