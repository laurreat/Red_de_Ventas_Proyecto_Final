@extends('layouts.admin')

@section('title', '- Reportes de Ventas')
@section('page-title', 'Reportes de Ventas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Análisis detallado de ventas y rendimiento</p>
                </div>
                <div>
                    <button class="btn btn-danger" type="button" id="exportButton" onclick="exportarReporte()">
                        <i class="bi bi-file-pdf me-1"></i>
                        Exportar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros de Reporte
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.reportes.ventas') }}">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio"
                                       value="{{ $fechaInicio }}">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin"
                                       value="{{ $fechaFin }}">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Vendedor</label>
                                <select class="form-select" name="vendedor_id">
                                    <option value="">Todos los vendedores</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}"
                                                {{ $vendedorId == $vendedor->id ? 'selected' : '' }}>
                                            {{ $vendedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Generar Reporte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-cart-check fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_ventas'] }}</h3>
                    <p class="text-muted mb-0 small">Total Ventas</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format($stats['total_ingresos'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Total Ingresos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-receipt fs-2 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">${{ number_format($stats['ticket_promedio'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ticket Promedio</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-box-seam fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['productos_vendidos'] }}</h3>
                    <p class="text-muted mb-0 small">Productos Vendidos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ventas por Día -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Ventas por Día
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($ventasPorDia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cantidad Pedidos</th>
                                        <th>Total Ventas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasPorDia as $fecha => $data)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-primary">{{ $data['cantidad'] }}</span></td>
                                        <td><strong>${{ number_format($data['total'], 0) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-graph-down fs-1"></i>
                            <p class="mt-2">No hay datos de ventas en el período seleccionado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ventas por Estado -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-pie-chart me-2"></i>
                        Ventas por Estado
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($ventasPorEstado->count() > 0)
                        @foreach($ventasPorEstado as $estado => $data)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                                    <br>
                                    <small class="text-muted">{{ $data['cantidad'] }} pedidos</small>
                                </div>
                                <div class="text-end">
                                    <strong>${{ number_format($data['total'], 0) }}</strong>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-pie-chart fs-1"></i>
                            <p class="mt-2">No hay datos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas por Vendedor -->
    @if($ventasPorVendedor->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person-badge me-2"></i>
                        Rendimiento por Vendedor
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Vendedor</th>
                                    <th>Pedidos</th>
                                    <th>Total Ventas</th>
                                    <th>Comisión Estimada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasPorVendedor as $data)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $data['vendedor'] }}</div>
                                            <small class="text-muted">{{ $data['email'] }}</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $data['cantidad_pedidos'] }}</span></td>
                                    <td><strong>${{ number_format($data['total_ventas'], 0) }}</strong></td>
                                    <td><strong class="text-success">${{ number_format($data['comision_estimada'], 0) }}</strong></td>
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
        <!-- Productos Más Vendidos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Productos Más Vendidos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($productosMasVendidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Vendidos</th>
                                        <th>Ingresos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosMasVendidos as $data)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $data['producto'] }}</div>
                                        </td>
                                        <td><span class="badge bg-info">{{ $data['categoria'] }}</span></td>
                                        <td><span class="badge bg-success">{{ $data['cantidad_vendida'] }}</span></td>
                                        <td><strong>${{ number_format($data['total_ingresos'], 0) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-box fs-1"></i>
                            <p class="mt-2">No hay productos vendidos en el período</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Clientes Más Activos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-people me-2"></i>
                        Clientes Más Activos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($clientesMasActivos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Pedidos</th>
                                        <th>Total Gastado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientesMasActivos as $data)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $data['cliente'] }}</div>
                                                <small class="text-muted">{{ $data['email'] }}</small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $data['cantidad_pedidos'] }}</span></td>
                                        <td><strong>${{ number_format($data['total_gastado'], 0) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-people fs-1"></i>
                            <p class="mt-2">No hay clientes activos en el período</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportarReporte() {
    // Obtener los valores actuales de los filtros
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
    const vendedorId = document.querySelector('select[name="vendedor_id"]').value;

    // Mostrar mensaje de carga
    const button = document.getElementById('exportButton');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise me-1 spin"></i> Generando PDF...';
    button.disabled = true;

    // Construir URL de exportación
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    });

    if (vendedorId) {
        params.append('vendedor_id', vendedorId);
    }

    const url = `{{ route('admin.reportes.exportar-ventas') }}?${params.toString()}`;

    // Usar fetch para manejar errores correctamente
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }

        // Si la respuesta es exitosa, proceder con la descarga
        return response.blob().then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;

            // Nombre del archivo PDF
            const fechaStr = fechaInicio.replace(/-/g, '_') + '_al_' + fechaFin.replace(/-/g, '_');
            link.download = `reporte_ventas_${fechaStr}.pdf`;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);

            // Mostrar mensaje de éxito
            if (typeof showSuccessToast === 'function') {
                showSuccessToast('¡Reporte PDF descargado exitosamente!');
            }
        });
    })
    .catch(error => {
        console.error('Error al exportar:', error);

        // Mostrar mensaje de error
        if (typeof showErrorToast === 'function') {
            const mensaje = error.error || error.message || 'Error al generar el reporte PDF. Inténtalo nuevamente.';
            showErrorToast(mensaje);
        } else {
            alert('Error al generar el reporte: ' + (error.error || error.message || 'Error desconocido'));
        }
    })
    .finally(() => {
        // Restaurar botón
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Estilos para la animación de carga
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
@endsection