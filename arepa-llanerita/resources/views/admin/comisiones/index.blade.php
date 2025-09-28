@extends('layouts.admin')

@section('title', '- Gestión de Comisiones')
@section('page-title', 'Gestión de Comisiones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Control y seguimiento de comisiones de vendedores</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-success me-2" onclick="calcularComisiones()">
                        <i class="bi bi-calculator me-1"></i>
                        Calcular
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="exportarComisiones()">
                        <i class="bi bi-download me-1"></i>
                        Exportar
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
                        Filtros de Período
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.comisiones.index') }}">
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
                            <div class="col-lg-4 col-md-6 mb-3">
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
                            <div class="col-lg-2 col-md-6 mb-3">
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

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">${{ number_format(to_float($stats['total_comisiones_ganadas']), 0) }}</h3>
                    <p class="text-muted mb-0 small">Comisiones Ganadas</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-clock fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">${{ number_format(to_float($stats['total_comisiones_pendientes']), 0) }}</h3>
                    <p class="text-muted mb-0 small">Comisiones Pendientes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['vendedores_activos'] }}</h3>
                    <p class="text-muted mb-0 small">Vendedores Activos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-bar-chart fs-2 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">${{ number_format(to_float($stats['promedio_comision']), 0) }}</h3>
                    <p class="text-muted mb-0 small">Promedio Comisión</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabla de Comisiones por Vendedor -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-table me-2"></i>
                        Comisiones por Vendedor
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($comisiones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
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
                                                <div class="fw-medium">{{ $comision->name }}</div>
                                                <small class="text-muted">{{ $comision->email }}</small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $comision->total_pedidos }}</span></td>
                                        <td><span class="badge bg-success">{{ $comision->pedidos_entregados }}</span></td>
                                        <td><strong>${{ number_format(to_float($comision->total_ventas), 0) }}</strong></td>
                                        <td><strong class="text-success">${{ number_format(to_float($comision->comision_ganada), 0) }}</strong></td>
                                        <td><strong class="text-warning">${{ number_format(to_float($comision->comision_pendiente), 0) }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.comisiones.show', $comision->id) }}"
                                               class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calculator fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay comisiones</h4>
                            <p class="text-muted">No se encontraron comisiones en el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Vendedores y Comisiones por Día -->
        <div class="col-lg-4 mb-4">
            <!-- Top 5 Vendedores -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top 5 Vendedores
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($topVendedores->count() > 0)
                        @foreach($topVendedores as $vendedor)
                            <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($loop->iteration == 1)
                                            <div class="badge bg-warning text-dark fs-6">🥇</div>
                                        @elseif($loop->iteration == 2)
                                            <div class="badge bg-secondary fs-6">🥈</div>
                                        @elseif($loop->iteration == 3)
                                            <div class="badge bg-danger fs-6">🥉</div>
                                        @else
                                            <div class="badge bg-light text-dark">{{ $loop->iteration }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $vendedor->name }}</div>
                                        <small class="text-muted">{{ $vendedor->pedidos_entregados }} entregados</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">${{ number_format(to_float($vendedor->comision_ganada), 0) }}</div>
                                    <small class="text-muted">${{ number_format(to_float($vendedor->total_ventas), 0) }} ventas</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-trophy fs-1"></i>
                            <p class="mt-2">No hay datos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mejor Vendedor del Período -->
            @if($stats['mejor_vendedor'])
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-star me-2"></i>
                        Vendedor Destacado
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-person-check fs-1" style="color: var(--primary-color);"></i>
                    </div>
                    <h6 class="fw-semibold">{{ $stats['mejor_vendedor']->name }}</h6>
                    <p class="text-muted mb-3">{{ $stats['mejor_vendedor']->email }}</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="fw-semibold mb-1 text-success">${{ number_format(to_float($stats['mejor_vendedor']->comision_ganada), 0) }}</h6>
                                <small class="text-muted">Comisión</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-semibold mb-1" style="color: var(--primary-color);">{{ $stats['mejor_vendedor']->pedidos_entregados }}</h6>
                            <small class="text-muted">Entregados</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Comisiones por Día -->
    @if($comisionesPorDia->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Evolución Diaria de Comisiones
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
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
                                    <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-primary">{{ $dia->pedidos }}</span></td>
                                    <td><strong>${{ number_format(to_float($dia->ventas), 0) }}</strong></td>
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

<script>
function calcularComisiones() {
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

    if (!fechaInicio || !fechaFin) {
        showToast('Por favor selecciona un período válido', 'error');
        return;
    }

    // Mostrar loading
    const btnCalcular = document.querySelector('button[onclick="calcularComisiones()"]');
    const originalText = btnCalcular.innerHTML;
    btnCalcular.disabled = true;
    btnCalcular.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Calculando...';

    // Realizar petición AJAX
    fetch('{{ route("admin.comisiones.calcular") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Comisiones calculadas: $${data.total_comisiones.toLocaleString()}`, 'success');

            // Recargar la página para mostrar los nuevos datos
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('Error al calcular comisiones', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al calcular comisiones', 'error');
    })
    .finally(() => {
        btnCalcular.disabled = false;
        btnCalcular.innerHTML = originalText;
    });
}

function exportarComisiones() {
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

    if (!fechaInicio || !fechaFin) {
        showToast('Por favor selecciona un período válido', 'error');
        return;
    }

    // Crear formulario para descargar
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.comisiones.exportar") }}';
    form.target = '_blank';

    // Token CSRF
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);

    // Fecha inicio
    const fechaInicioInput = document.createElement('input');
    fechaInicioInput.type = 'hidden';
    fechaInicioInput.name = 'fecha_inicio';
    fechaInicioInput.value = fechaInicio;
    form.appendChild(fechaInicioInput);

    // Fecha fin
    const fechaFinInput = document.createElement('input');
    fechaFinInput.type = 'hidden';
    fechaFinInput.name = 'fecha_fin';
    fechaFinInput.value = fechaFin;
    form.appendChild(fechaFinInput);

    // Formato
    const formatoInput = document.createElement('input');
    formatoInput.type = 'hidden';
    formatoInput.name = 'formato';
    formatoInput.value = 'csv';
    form.appendChild(formatoInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    showToast('Descargando reporte de comisiones...', 'success');
}
</script>
@endsection