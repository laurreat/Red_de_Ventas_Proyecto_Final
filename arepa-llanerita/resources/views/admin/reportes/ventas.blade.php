@extends('layouts.admin')

@section('title', '- Reportes de Ventas')
@section('page-title', 'Reportes de Ventas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/reportes-modern.css') }}?v={{ filemtime(public_path('css/admin/reportes-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid fade-in">
    {{-- Header Hero --}}
    <div class="reporte-header scale-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="reporte-header-title">
                    <i class="bi bi-bar-chart-line"></i> Reportes de Ventas
                </h1>
                <p class="reporte-header-subtitle">Análisis detallado de ventas y rendimiento del negocio</p>
            </div>
            <div class="reporte-header-actions">
                <button class="reporte-btn reporte-btn-danger" type="button" onclick="exportarReporte()">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros de Reporte --}}
    <div class="reporte-filters-card fade-in-up">
        <div class="reporte-filters-header">
            <i class="bi bi-funnel"></i>
            <h3 class="reporte-filters-title">Filtros de Reporte</h3>
        </div>
        <div class="reporte-filters-body">
            <form method="GET" action="{{ route('admin.reportes.ventas') }}" autocomplete="off">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar-event"></i> Fecha Inicio
                        </label>
                        <input type="date" class="form-control" name="fecha_inicio"
                               value="{{ $fechaInicio }}"
                               style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar-check"></i> Fecha Fin
                        </label>
                        <input type="date" class="form-control" name="fecha_fin"
                               value="{{ $fechaFin }}"
                               style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-badge"></i> Vendedor
                        </label>
                        <select class="form-select" name="vendedor_id" style="border-radius:10px;padding:.75rem;">
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
                        <button type="submit" class="btn btn-primary w-100" style="border-radius:10px;padding:.75rem;">
                            <i class="bi bi-search"></i> Generar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Estadísticas Generales --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-1">
                <div class="reporte-stat-icon" style="background:rgba(114,47,55,0.1);color:var(--wine);">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="reporte-stat-value">{{ $stats['total_ventas'] }}</div>
                <div class="reporte-stat-label">Total Ventas</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-2">
                <div class="reporte-stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="reporte-stat-value">${{ number_format((float)($stats['total_ingresos'] ?? 0), 0) }}</div>
                <div class="reporte-stat-label">Total Ingresos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-3">
                <div class="reporte-stat-icon" style="background:rgba(59,130,246,0.1);color:var(--info);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="reporte-stat-value">${{ number_format((float)($stats['ticket_promedio'] ?? 0), 0) }}</div>
                <div class="reporte-stat-label">Ticket Promedio</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-1">
                <div class="reporte-stat-icon" style="background:rgba(245,158,11,0.1);color:var(--warning);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="reporte-stat-value">{{ $stats['productos_vendidos'] }}</div>
                <div class="reporte-stat-label">Productos Vendidos</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Gráfico de Ventas por Día --}}
        <div class="col-lg-8 mb-4">
            <div class="reporte-chart-card fade-in-up">
                <div class="reporte-chart-header">
                    <h3 class="reporte-chart-title">
                        <i class="bi bi-graph-up"></i>
                        <span>Ventas por Día</span>
                    </h3>
                </div>
                <div class="reporte-chart-body">
                    @if($ventasPorDia->count() > 0)
                        <div class="reporte-chart-container">
                            <canvas id="ventasPorDiaChart"></canvas>
                        </div>
                    @else
                        <div class="reporte-empty-state">
                            <div class="reporte-empty-state-icon">
                                <i class="bi bi-graph-down"></i>
                            </div>
                            <h4 class="reporte-empty-state-title">No hay datos disponibles</h4>
                            <p class="reporte-empty-state-text">No se encontraron ventas en el período seleccionado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ventas por Estado --}}
        <div class="col-lg-4 mb-4">
            <div class="reporte-chart-card fade-in-up animate-delay-1">
                <div class="reporte-chart-header">
                    <h3 class="reporte-chart-title">
                        <i class="bi bi-pie-chart"></i>
                        <span>Ventas por Estado</span>
                    </h3>
                </div>
                <div class="reporte-chart-body">
                    @if($ventasPorEstado->count() > 0)
                        <div class="reporte-chart-container" style="height:300px;">
                            <canvas id="ventasPorEstadoChart"></canvas>
                        </div>
                    @else
                        <div class="reporte-empty-state" style="padding:2rem;">
                            <div class="reporte-empty-state-icon" style="font-size:3rem;">
                                <i class="bi bi-pie-chart"></i>
                            </div>
                            <p class="reporte-empty-state-text" style="margin:1rem 0 0 0;">No hay datos disponibles</p>
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
                    <i class="bi bi-person-badge"></i>
                    <h3 class="reporte-table-title">Rendimiento por Vendedor</h3>
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
                                        <span class="reporte-badge reporte-badge-info">{{ $data['cantidad_pedidos'] }} pedidos</span>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--wine);">${{ number_format((float)($data['total_ventas'] ?? 0), 0) }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--success);">${{ number_format((float)($data['comision_estimada'] ?? 0), 0) }}</strong>
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
                    <i class="bi bi-trophy"></i>
                    <h3 class="reporte-table-title">Top 10 Productos</h3>
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
                                        <span style="margin-left:.5rem;">{{ $data['cantidad_vendida'] }} unidades vendidas</span>
                                    </div>
                                </div>
                                <div class="reporte-ranking-value">
                                    ${{ number_format((float)($data['total_ingresos'] ?? 0), 0) }}
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
                    <i class="bi bi-people"></i>
                    <h3 class="reporte-table-title">Top 10 Clientes</h3>
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
                                        {{ $data['email'] }} · {{ $data['cantidad_pedidos'] }} pedidos
                                    </div>
                                </div>
                                <div class="reporte-ranking-value">
                                    ${{ number_format((float)($data['total_gastado'] ?? 0), 0) }}
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
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

{{-- Datos para gráficos --}}
<script>
// Datos de ventas por día
window.ventasPorDiaData = {
    labels: [@foreach($ventasPorDia as $fecha => $data)'{{ \Carbon\Carbon::parse($fecha)->format("d/m") }}',@endforeach],
    datasets: [{
        label: 'Ingresos Diarios',
        data: [@foreach($ventasPorDia as $data){{ (float)($data['total'] ?? 0) }},@endforeach],
        backgroundColor: 'rgba(114, 47, 55, 0.1)',
        borderColor: '#722F37',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#722F37',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
    }]
};

// Datos de ventas por estado
window.ventasPorEstadoData = {
    labels: [@foreach($ventasPorEstado as $estado => $data)'{{ ucfirst(str_replace("_", " ", $estado)) }}',@endforeach],
    datasets: [{
        data: [@foreach($ventasPorEstado as $data){{ (float)($data['total'] ?? 0) }},@endforeach],
        backgroundColor: [
            '#722F37', // Wine
            '#10b981', // Success
            '#3b82f6', // Info
            '#f59e0b', // Warning
            '#8b5cf6', // Purple
            '#ef4444', // Danger
            '#6b7280'  // Gray
        ],
        borderWidth: 0
    }]
};

// Función de exportación
function exportarReporte() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("admin.reportes.exportar-ventas") }}';

    const params = {
        fecha_inicio: '{{ $fechaInicio }}',
        fecha_fin: '{{ $fechaFin }}',
        vendedor_id: '{{ $vendedorId }}'
    };

    Object.keys(params).forEach(key => {
        if (params[key]) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

{{-- Inicialización de gráficos --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Ventas por Día
    const ventasPorDiaCanvas = document.getElementById('ventasPorDiaChart');
    if (ventasPorDiaCanvas && window.ventasPorDiaData) {
        new Chart(ventasPorDiaCanvas, {
            type: 'line',
            data: window.ventasPorDiaData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: '600' },
                            color: '#374151',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#722F37',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Ingresos: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            color: '#6b7280',
                            font: { size: 11 }
                        },
                        grid: { color: '#e5e7eb' }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280',
                            font: { size: 11 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Gráfico de Ventas por Estado
    const ventasPorEstadoCanvas = document.getElementById('ventasPorEstadoChart');
    if (ventasPorEstadoCanvas && window.ventasPorEstadoData) {
        new Chart(ventasPorEstadoCanvas, {
            type: 'doughnut',
            data: window.ventasPorEstadoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: '600' },
                            color: '#374151',
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: '#722F37',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    console.log('✓ Reportes de Ventas cargados correctamente');
});
</script>
@endpush
