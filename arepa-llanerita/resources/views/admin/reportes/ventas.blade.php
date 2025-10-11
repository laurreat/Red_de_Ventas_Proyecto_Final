@extends('layouts.admin')

@section('title', '- Reportes de Ventas')

@section('page-title', 'Reportes de Ventas')

@push('styles')
<link href="{{ asset('css/admin/reportes-modern.css') }}?v={{ filemtime(public_path('css/admin/reportes-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
{{-- Header de Reportes --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); border-radius: 0.5rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-white">
                        <h2 class="mb-2" style="font-weight: 700;">
                            <i class="bi bi-bar-chart-line me-2"></i>
                            Reportes de Ventas
                        </h2>
                        <p class="mb-0 opacity-90">Análisis detallado de ventas y rendimiento del negocio</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-light" type="button" id="exportButton" onclick="exportarReporte()">
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Exportar PDF
                        </button>
                        <button class="btn btn-outline-light ms-2" type="button" id="refreshButton" onclick="refrescarReportes()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros de Reporte --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="bi bi-funnel me-2 text-primary"></i>
                <h5 class="mb-0">Filtros de Reporte</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes.ventas') }}" autocomplete="off" id="filtrosForm">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event"></i> Fecha Inicio
                            </label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fechaInicio"
                                value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-check"></i> Fecha Fin
                            </label>
                            <input type="date" class="form-control" name="fecha_fin" id="fechaFin"
                                value="{{ $fechaFin }}">
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-badge"></i> Vendedor
                            </label>
                            <select class="form-select" name="vendedor_id" id="vendedorId">
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
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Generar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Estadísticas Generales --}}
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; background: rgba(114,47,55,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-cart-check" style="font-size: 1.75rem; color: var(--primary-color);"></i>
                    </div>
                </div>
                <h3 class="mb-1" style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                    {{ number_format((float)($stats['total_ventas'] ?? 0)) }}
                </h3>
                <p class="text-muted mb-0 fw-semibold small text-uppercase">Total Ventas</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; background: rgba(16,185,129,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-currency-dollar" style="font-size: 1.75rem; color: #10b981;"></i>
                    </div>
                </div>
                <h3 class="mb-1" style="font-size: 2rem; font-weight: 700; color: #10b981;">
                    ${{ format_number((float)($stats['total_ingresos'] ?? 0), 0) }}
                </h3>
                <p class="text-muted mb-0 fw-semibold small text-uppercase">Total Ingresos</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; background: rgba(59,130,246,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-receipt" style="font-size: 1.75rem; color: #3b82f6;"></i>
                    </div>
                </div>
                <h3 class="mb-1" style="font-size: 2rem; font-weight: 700; color: #3b82f6;">
                    ${{ format_number((float)($stats['ticket_promedio'] ?? 0), 0) }}
                </h3>
                <p class="text-muted mb-0 fw-semibold small text-uppercase">Ticket Promedio</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; background: rgba(245,158,11,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-box-seam" style="font-size: 1.75rem; color: #f59e0b;"></i>
                    </div>
                </div>
                <h3 class="mb-1" style="font-size: 2rem; font-weight: 700; color: #f59e0b;">
                    {{ number_format((float)($stats['productos_vendidos'] ?? 0)) }}
                </h3>
                <p class="text-muted mb-0 fw-semibold small text-uppercase">Productos Vendidos</p>
            </div>
        </div>
    </div>
</div>

{{-- Gráficos --}}
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="bi bi-graph-up me-2 text-primary"></i>
                <h5 class="mb-0">Ventas por Día</h5>
            </div>
            <div class="card-body">
                @if($ventasPorDia->count() > 0)
                <div style="position: relative; height: 350px;">
                    <canvas id="ventasPorDiaChart"></canvas>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-graph-down text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No hay datos disponibles</h5>
                    <p class="text-muted">No se encontraron ventas en el período seleccionado</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="bi bi-pie-chart me-2 text-primary"></i>
                <h5 class="mb-0">Ventas por Estado</h5>
            </div>
            <div class="card-body">
                @if($ventasPorEstado->count() > 0)
                <div style="position: relative; height: 300px;">
                    <canvas id="ventasPorEstadoChart"></canvas>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No hay datos disponibles</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

    {{-- Rendimiento por Vendedor --}}
    @if($ventasPorVendedor->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="reporte-table-card fade-in-up">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-person-badge"></i>
                        <span>Rendimiento por Vendedor</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="reporte-table">
                            <thead>
                                <tr>
                                    <th>Vendedor</th>
                                    <th>Pedidos</th>
                                    <th>Total Ventas</th>
                                    <th>Comisión Estimada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasPorVendedor as $data)
                                <tr class="fade-in-up">
                                    <td>
                                        <div>
                                            <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">{{ $data['vendedor'] }}</div>
                                            <small style="color:var(--gray-500);">{{ $data['email'] }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="reporte-badge reporte-badge-info">{{ (int)($data['cantidad_pedidos'] ?? 0) }} pedidos</span>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--wine);">${{ format_number((float)($data['total_ventas'] ?? 0), 0) }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--success);">${{ format_number((float)($data['comision_estimada'] ?? 0), 0) }}</strong>
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

    <div class="row">
        {{-- Productos Más Vendidos --}}
        <div class="col-lg-6 mb-4">
            <div class="reporte-table-card fade-in-up animate-delay-2">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-trophy"></i>
                        <span>Top 10 Productos</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($productosMasVendidos->count() > 0)
                    @foreach($productosMasVendidos->take(10) as $index => $data)
                    <div class="reporte-ranking-item">
                        <div class="reporte-ranking-position {{ $index === 0 ? 'top-1' : ($index === 1 ? 'top-2' : ($index === 2 ? 'top-3' : '')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="reporte-ranking-info">
                            <div class="reporte-ranking-name">{{ $data['producto'] }}</div>
                            <div class="reporte-ranking-detail">
                                <span class="reporte-badge reporte-badge-info">{{ $data['categoria'] }}</span>
                                <span style="margin-left:.5rem;">{{ (int)($data['cantidad_vendida'] ?? 0) }} unidades vendidas</span>
                            </div>
                        </div>
                        <div class="reporte-ranking-value">
                            ${{ format_number((float)($data['total_ingresos'] ?? 0), 0) }}
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="reporte-empty-state">
                        <div class="reporte-empty-state-icon">
                            <i class="bi bi-box"></i>
                        </div>
                        <p class="reporte-empty-state-text">No hay productos vendidos</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Clientes Más Activos --}}
        <div class="col-lg-6 mb-4">
            <div class="reporte-table-card fade-in-up animate-delay-3">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-people"></i>
                        <span>Top 10 Clientes</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($clientesMasActivos->count() > 0)
                    @foreach($clientesMasActivos->take(10) as $index => $data)
                    <div class="reporte-ranking-item">
                        <div class="reporte-ranking-position {{ $index === 0 ? 'top-1' : ($index === 1 ? 'top-2' : ($index === 2 ? 'top-3' : '')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="reporte-ranking-info">
                            <div class="reporte-ranking-name">{{ $data['cliente'] }}</div>
                            <div class="reporte-ranking-detail">
                                {{ $data['email'] }} · {{ (int)($data['cantidad_pedidos'] ?? 0) }} pedidos
                            </div>
                        </div>
                        <div class="reporte-ranking-value">
                            ${{ format_number((float)($data['total_gastado'] ?? 0), 0) }}
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="reporte-empty-state">
                        <div class="reporte-empty-state-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <p class="reporte-empty-state-text">No hay clientes activos</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js CDN (última versión estable) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>

<script>
    // Rutas para el módulo
    window.reportesRoutes = {
        exportarPDF: '{{ route("admin.reportes.exportar-ventas") }}',
        exportar: '{{ route("admin.reportes.exportar-ventas") }}'
    };

    // Datos para gráfico de ventas por día
    window.ventasPorDiaData = {
        labels: [
            @foreach($ventasPorDia as $fecha => $data)
                '{{ \Carbon\Carbon::parse($fecha)->format("d/m") }}',
            @endforeach
        ],
        data: [
            @foreach($ventasPorDia as $data)
                {{ (float)($data['total'] ?? 0) }},
            @endforeach
        ]
    };

    // Datos para gráfico de ventas por estado
    window.ventasPorEstadoData = {
        labels: [
            @foreach($ventasPorEstado as $estado => $data)
                '{{ ucfirst(str_replace("_", " ", $estado)) }}',
            @endforeach
        ],
        data: [
            @foreach($ventasPorEstado as $data)
                {{ (float)($data['total'] ?? 0) }},
            @endforeach
        ]
    };
</script>

{{-- Módulo de reportes minificado --}}
<script src="{{ asset('js/admin/reportes-modern.js') }}?v={{ filemtime(public_path('js/admin/reportes-modern.js')) }}"></script>

{{-- Función de actualización manual --}}
<script>
    function refrescarReportes() {
        const btn = document.getElementById('refreshButton');
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Actualizando...';
        btn.disabled = true;

        // Recargar la página con los filtros actuales
        document.getElementById('filtrosForm').submit();
    }

    // Auto-refresh opcional cada 5 minutos (si está habilitado)
    let autoRefreshEnabled = false; // Cambia a true para habilitar
    if (autoRefreshEnabled) {
        setInterval(function() {
            console.log('⏱️ Auto-refresh de reportes (cada 5 minutos)');
            document.getElementById('filtrosForm').submit();
        }, 300000); // 5 minutos
    }
</script>
@endpush