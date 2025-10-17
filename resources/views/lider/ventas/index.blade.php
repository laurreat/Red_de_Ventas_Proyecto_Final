@extends('layouts.lider')

@section('title', ' - Ventas del Equipo')
@section('page-title', 'Ventas del Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/ventas-modern.css') }}?v={{ filemtime(public_path('css/lider/ventas-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="ventas-header fade-in-up">
        <div class="ventas-header-content">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="ventas-title">
                        <i class="bi bi-cart-check-fill"></i>
                        Ventas del Equipo
                    </h1>
                    <p class="ventas-subtitle mb-0">
                        Monitorea el rendimiento y las ventas de tu equipo en tiempo real
                    </p>
                </div>
                <div class="ventas-actions">
                    <button class="ventas-action-btn" onclick="ventasManager.exportarVentas('csv')">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        Exportar CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="ventas-stats-grid">
        <div class="ventas-stat-card fade-in-up animate-delay-1">
            <div class="ventas-stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="ventas-stat-value">${{ number_format($stats['ventas_periodo'], 0, ',', '.') }}</div>
            <div class="ventas-stat-label">Ventas del Periodo</div>
            @if($stats['crecimiento'] != 0)
                <div class="ventas-stat-change {{ $stats['crecimiento'] > 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $stats['crecimiento'] > 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['crecimiento']) }}% vs periodo anterior
                </div>
            @endif
        </div>

        <div class="ventas-stat-card fade-in-up animate-delay-2">
            <div class="ventas-stat-icon success">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div class="ventas-stat-value">{{ number_format($stats['pedidos_periodo']) }}</div>
            <div class="ventas-stat-label">Total de Pedidos</div>
            @if($stats['pedidos_periodo'] > 0)
                <div class="ventas-stat-change positive">
                    <i class="bi bi-check-circle"></i>
                    {{ $stats['conversion'] }}% conversión
                </div>
            @endif
        </div>

        <div class="ventas-stat-card fade-in-up animate-delay-3">
            <div class="ventas-stat-icon info">
                <i class="bi bi-calculator-fill"></i>
            </div>
            <div class="ventas-stat-value">${{ number_format($stats['ticket_promedio'], 0, ',', '.') }}</div>
            <div class="ventas-stat-label">Ticket Promedio</div>
        </div>

        <div class="ventas-stat-card fade-in-up animate-delay-4">
            <div class="ventas-stat-icon warning">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="ventas-stat-value">{{ $stats['vendedores_activos'] }}</div>
            <div class="ventas-stat-label">Vendedores Activos</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="ventas-chart-container fade-in-up animate-delay-1">
                <h3 class="ventas-chart-title">
                    <i class="bi bi-graph-up"></i>
                    Evolución de Ventas
                </h3>
                <div style="height:350px;position:relative">
                    <canvas id="evolucionVentasChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="ventas-chart-container fade-in-up animate-delay-2">
                <h3 class="ventas-chart-title">
                    <i class="bi bi-calendar3"></i>
                    Ventas por Día
                </h3>
                <div style="height:350px;position:relative">
                    <canvas id="ventasDiaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ventas-filter-card fade-in-up">
        <h3 class="ventas-filter-title">
            <i class="bi bi-funnel-fill"></i>
            Filtrar Ventas
        </h3>
        <form method="GET" action="{{ route('lider.ventas.index') }}">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label for="periodo" class="ventas-form-label">Periodo</label>
                    <select name="periodo" id="periodo" class="ventas-form-control">
                        <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Semana Actual</option>
                        <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                        <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                        <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre</option>
                        <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                        <option value="ultimos_30_dias" {{ $periodo == 'ultimos_30_dias' ? 'selected' : '' }}>Últimos 30 Días</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="vendedor" class="ventas-form-label">Vendedor</label>
                    <select name="vendedor" id="vendedor" class="ventas-form-control">
                        <option value="">Todos los Vendedores</option>
                        @foreach($vendedoresEquipo as $vendedorEquipo)
                            <option value="{{ $vendedorEquipo->id }}" {{ $vendedor == $vendedorEquipo->id ? 'selected' : '' }}>
                                {{ $vendedorEquipo->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="ventas-form-label">Estado</label>
                    <select name="estado" id="estado" class="ventas-form-control">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmado" {{ $estado == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="completado" {{ $estado == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="entregado" {{ $estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ $estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="cliente" class="ventas-form-label">Cliente</label>
                    <input type="text" name="cliente" id="cliente" class="ventas-form-control"
                           placeholder="Buscar cliente..." value="{{ $cliente }}">
                </div>

                <div class="col-lg-2 col-md-12">
                    <label class="ventas-form-label d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="ventas-action-btn" style="background:var(--wine);color:white;flex:1">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('lider.ventas.index') }}" class="ventas-action-btn"
                           style="background:rgba(107,114,128,0.15);color:#374151">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Ventas -->
    <div class="ventas-table-container fade-in-up">
        <div class="ventas-table-header">
            <h3 class="ventas-table-title">
                <i class="bi bi-list-ul"></i>
                Listado de Ventas
            </h3>
            <div class="ventas-stat-change positive">
                <i class="bi bi-check-circle"></i>
                {{ $ventas->total() }} resultados
            </div>
        </div>
        <div class="table-responsive">
            <table class="ventas-table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Pedido</th>
                        <th>Vendedor</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td data-label="Fecha">
                                <div style="font-weight:600;color:var(--dark)">
                                    {{ $venta->created_at->format('d/m/Y') }}
                                </div>
                                <div style="font-size:0.813rem;color:var(--muted)">
                                    {{ $venta->created_at->format('H:i') }} • {{ $venta->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td data-label="Pedido">
                                <span style="font-weight:700;color:var(--wine);font-size:1.063rem">
                                    #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td data-label="Vendedor">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="ventas-vendor-avatar">
                                        {{ strtoupper(substr($venta->vendedor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <div class="ventas-vendor-info">
                                        <div class="ventas-vendor-name">{{ $venta->vendedor->name ?? 'N/A' }}</div>
                                        <div class="ventas-vendor-meta">
                                            <i class="bi bi-person-badge"></i>
                                            {{ ucfirst($venta->vendedor->rol ?? 'vendedor') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Cliente">
                                <div class="ventas-client-info">
                                    <div class="ventas-client-avatar">
                                        {{ strtoupper(substr($venta->cliente->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="ventas-client-details">
                                        <div class="ventas-client-name">{{ $venta->cliente->name ?? 'Cliente' }}</div>
                                        <div class="ventas-client-email">{{ $venta->cliente->email ?? 'Sin email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Total">
                                <div class="ventas-amount">${{ number_format($venta->total_final, 0, ',', '.') }}</div>
                            </td>
                            <td data-label="Estado">
                                <span class="ventas-badge ventas-badge-{{ $venta->estado }}">
                                    <i class="bi bi-{{ $venta->estado == 'completado' ? 'check-circle' : ($venta->estado == 'pendiente' ? 'clock' : ($venta->estado == 'cancelado' ? 'x-circle' : 'info-circle')) }}"></i>
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </td>
                            <td data-label="Acciones">
                                <a href="{{ route('lider.ventas.show', $venta->id) }}"
                                   class="ventas-action-btn-icon primary"
                                   title="Ver Detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ventas-empty-state">
                                    <div class="ventas-empty-icon">
                                        <i class="bi bi-cart-x"></i>
                                    </div>
                                    <div class="ventas-empty-text">No se encontraron ventas</div>
                                    <div class="ventas-empty-subtext">
                                        Intenta ajustar los filtros de búsqueda o selecciona otro periodo
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ventas->hasPages())
            <div class="d-flex justify-content-center p-4">
                {{ $ventas->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Ranking de Vendedores -->
    @if($rankingVendedores->isNotEmpty())
        <div class="ventas-filter-card fade-in-up">
            <h3 class="ventas-filter-title">
                <i class="bi bi-trophy-fill"></i>
                Ranking de Vendedores - {{ ucfirst(str_replace('_', ' ', $periodo)) }}
            </h3>
            <div class="row g-3">
                @foreach($rankingVendedores->take(6) as $vendedorRanking)
                    @php
                        $rankClass = 'top';
                        if ($vendedorRanking->posicion === 1) $rankClass = 'gold';
                        elseif ($vendedorRanking->posicion === 2) $rankClass = 'silver';
                        elseif ($vendedorRanking->posicion === 3) $rankClass = 'bronze';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="ventas-ranking-card {{ $rankClass }} fade-in-up" style="animation-delay:{{ $loop->index * 0.1 }}s">
                            <div class="d-flex align-items-center gap-3">
                                <div class="ventas-ranking-position {{ $rankClass }}">
                                    @if($vendedorRanking->posicion <= 3)
                                        <span style="font-size:1.75rem">
                                            @if($vendedorRanking->posicion === 1)🥇
                                            @elseif($vendedorRanking->posicion === 2)🥈
                                            @else🥉@endif
                                        </span>
                                    @else
                                        #{{ $vendedorRanking->posicion }}
                                    @endif
                                </div>
                                <div class="ventas-ranking-info">
                                    <div class="ventas-ranking-name">{{ $vendedorRanking->name }}</div>
                                    <div class="ventas-ranking-value">
                                        ${{ number_format($vendedorRanking->total_ventas ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="ventas-ranking-meta">
                                        <span>
                                            <i class="bi bi-bag-check-fill"></i>
                                            {{ $vendedorRanking->total_pedidos ?? 0 }} pedidos
                                        </span>
                                        <span>
                                            <i class="bi bi-calculator"></i>
                                            ${{ number_format($vendedorRanking->ticket_promedio ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Top Productos -->
    @if($topProductos->isNotEmpty())
        <div class="ventas-table-container fade-in-up">
            <div class="ventas-table-header">
                <h3 class="ventas-table-title">
                    <i class="bi bi-box-seam-fill"></i>
                    Productos Más Vendidos
                </h3>
            </div>
            <div class="table-responsive">
                <table class="ventas-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Vendida</th>
                            <th>Ingresos Totales</th>
                            <th>Precio Unitario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProductos->take(10) as $producto)
                            <tr>
                                <td data-label="Producto">
                                    <div style="font-weight:600;color:var(--dark)">{{ $producto->nombre }}</div>
                                </td>
                                <td data-label="Cantidad">
                                    <span class="ventas-badge ventas-badge-confirmado">
                                        {{ $producto->total_vendido }} unidades
                                    </span>
                                </td>
                                <td data-label="Ingresos">
                                    <div class="ventas-amount">${{ number_format($producto->total_ingresos, 0, ',', '.') }}</div>
                                </td>
                                <td data-label="Precio">
                                    <div style="font-weight:600;color:var(--dark)">
                                        ${{ number_format($producto->precio, 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pasar datos a JavaScript
window.evolucionVentasData = {
    labels: {!! json_encode($evolucionVentas->pluck('mes')) !!},
    ventas: {!! json_encode($evolucionVentas->pluck('ventas')) !!},
    pedidos: {!! json_encode($evolucionVentas->pluck('pedidos')) !!}
};

window.ventasPorDiaData = {
    labels: {!! json_encode($ventasPorDia->pluck('dia')) !!},
    ventas: {!! json_encode($ventasPorDia->pluck('ventas')) !!}
};
</script>
<script src="{{ asset('js/lider/ventas-modern.js') }}?v={{ filemtime(public_path('js/lider/ventas-modern.js')) }}"></script>
@endpush
