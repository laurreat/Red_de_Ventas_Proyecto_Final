@extends('layouts.admin')

@section('title', '- Pedidos')
@section('page-title', 'Gestión de Pedidos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra todos los pedidos del sistema</p>
                </div>
                <div>
                    <a href="{{ route('admin.pedidos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Pedido
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estadísticas de Pedidos -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-basket3 fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_pedidos'] }}</h3>
                    <p class="text-muted mb-0 small">Total Pedidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-clock fs-2 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">{{ $stats['pedidos_hoy'] }}</h3>
                    <p class="text-muted mb-0 small">Hoy</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['pedidos_pendientes'] }}</h3>
                    <p class="text-muted mb-0 small">Pendientes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['pedidos_entregados'] }}</h3>
                    <p class="text-muted mb-0 small">Entregados</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-x-circle fs-2 text-danger"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ $stats['pedidos_cancelados'] }}</h3>
                    <p class="text-muted mb-0 small">Cancelados</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-currency-dollar fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${{ number_format($stats['ingresos_mes'], 0) }}</h3>
                    <p class="text-muted mb-0 small">Ingresos del Mes</p>
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
                        Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.pedidos.index') }}">
                        <div class="row">
                            <div class="col-lg-3 col-md-4 mb-3">
                                <label class="form-label">Buscar pedido</label>
                                <input type="text" class="form-control" name="buscar"
                                       placeholder="Número de pedido, cliente..."
                                       value="{{ request('buscar') }}">
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="">Todos los estados</option>
                                    @foreach($estados as $valor => $nombre)
                                        <option value="{{ $valor }}"
                                                {{ request('estado') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label class="form-label">Vendedor</label>
                                <select class="form-select" name="vendedor">
                                    <option value="">Todos los vendedores</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}"
                                                {{ request('vendedor') == $vendedor->id ? 'selected' : '' }}>
                                            {{ $vendedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" name="fecha_desde"
                                       value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" name="fecha_hasta"
                                       value="{{ request('fecha_hasta') }}">
                            </div>
                            <div class="col-lg-1 col-md-12 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
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
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Lista de Pedidos ({{ $pedidos->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pedidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedidos as $pedido)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $pedido->numero_pedido }}</div>
                                                <small class="text-muted">{{ $pedido->detalles->count() }} producto(s)</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $pedido->cliente->name }}</div>
                                                <small class="text-muted">{{ $pedido->cliente->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($pedido->vendedor)
                                                <div>
                                                    <div class="fw-medium">{{ $pedido->vendedor->name }}</div>
                                                    <small class="text-muted">{{ $pedido->vendedor->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Sin vendedor</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>${{ number_format($pedido->total_final, 0) }}</strong>
                                                @if($pedido->descuento > 0)
                                                    <small class="text-success d-block">-${{ number_format($pedido->descuento, 0) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @switch($pedido->estado)
                                                @case('pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                    @break
                                                @case('confirmado')
                                                    <span class="badge bg-info">Confirmado</span>
                                                    @break
                                                @case('en_preparacion')
                                                    <span class="badge bg-primary">En Preparación</span>
                                                    @break
                                                @case('listo')
                                                    <span class="badge bg-secondary">Listo</span>
                                                    @break
                                                @case('en_camino')
                                                    <span class="badge" style="background-color: var(--primary-color);">En Camino</span>
                                                    @break
                                                @case('entregado')
                                                    <span class="badge bg-success">Entregado</span>
                                                    @break
                                                @case('cancelado')
                                                    <span class="badge bg-danger">Cancelado</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div>
                                                <div>{{ $pedido->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        title="Ver Detalles"
                                                        onclick="pedidosManager.showDetails({{ $pedido->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                                                    <a href="{{ route('admin.pedidos.edit', $pedido) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            data-bs-toggle="dropdown" title="Estado">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach($estados as $valor => $nombre)
                                                            @if($valor != $pedido->estado)
                                                                <li>
                                                                    <a class="dropdown-item" href="#"
                                                                       onclick="event.preventDefault(); pedidosManager.updateStatus({{ $pedido->id }}, '{{ $valor }}', '{{ $pedido->numero_pedido }}', '{{ $pedido->cliente->name }}', '{{ $pedido->estado }}', '{{ $nombre }}')">
                                                                        {{ $nombre }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @if($pedido->estado != 'entregado')
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar"
                                                            onclick="event.preventDefault(); pedidosManager.confirmDelete({{ $pedido->id }}, '{{ $pedido->numero_pedido }}', '{{ $pedido->cliente->name }}', '${{ number_format($pedido->total_final, 0) }}', '{{ ucfirst($pedido->estado) }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Formularios ocultos -->
                                            <form id="status-form-{{ $pedido->id }}"
                                                  action="{{ route('admin.pedidos.update-status', $pedido) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" id="estado-{{ $pedido->id }}">
                                            </form>

                                            <form id="delete-form-{{ $pedido->id }}"
                                                  action="{{ route('admin.pedidos.destroy', $pedido) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="p-4">
                            {{ $pedidos->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-basket3 fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay pedidos</h4>
                            <p class="text-muted">No se encontraron pedidos que coincidan con los filtros.</p>
                            <a href="{{ route('admin.pedidos.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Crear primer pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir modales de pedidos -->
@include('admin.partials.modals-pedidos')

<script>
// Variables globales para las rutas
window.pedidosRoutes = {
    details: '{{ route("admin.pedidos.show", ":id") }}',
    updateStatus: '{{ route("admin.pedidos.update-status", ":id") }}',
    destroy: '{{ route("admin.pedidos.destroy", ":id") }}'
};

// Inicializar el manager de pedidos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Pedidos Index JS cargado...');

    // Inicializar el manager si existe
    if (typeof PedidosManager !== 'undefined') {
        window.pedidosManager = new PedidosManager();
        console.log('✅ PedidosManager inicializado correctamente');
    } else {
        console.error('❌ PedidosManager no encontrado');
    }
});

// Funciones de fallback (por si el manager no carga)
setTimeout(function() {
    if (typeof window.pedidosManager === 'undefined') {
        console.log('⚠️ Cargando funciones de fallback para pedidos...');

        window.updateStatus = function(pedidoId, estado) {
            if (confirm('¿Estás seguro de que quieres cambiar el estado de este pedido?')) {
                document.getElementById('estado-' + pedidoId).value = estado;
                document.getElementById('status-form-' + pedidoId).submit();
            }
        };

        window.confirmDelete = function(pedidoId) {
            if (confirm('¿Estás seguro de que quieres eliminar este pedido? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form-' + pedidoId).submit();
            }
        };

        // Crear manager básico
        window.pedidosManager = {
            updateStatus: function(id, estado, numero, cliente, estadoActual, estadoNuevo) {
                window.updateStatus(id, estado);
            },
            confirmDelete: function(id, numero, cliente, total, estado) {
                window.confirmDelete(id);
            },
            showDetails: function(id) {
                window.location.href = window.pedidosRoutes.details.replace(':id', id);
            }
        };
    }
}, 1000);
</script>
@endsection