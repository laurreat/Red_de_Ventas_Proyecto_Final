@extends('layouts.admin')

@section('title', '- Editar Pedido')
@section('page-title', 'Editar Pedido')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-muted mb-0">Modificar pedido: <strong>{{ $pedido->numero_pedido }}</strong></h2>
                </div>
                <div>
                    <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn btn-outline-info me-2">
                        <i class="bi bi-eye me-1"></i>
                        Ver Pedido
                    </a>
                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver a Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes flash manejados por AdminAlerts en admin-functions.js --}}

    <form id="pedido-form" action="{{ route('admin.pedidos.update', $pedido) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Información del Pedido -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-info-circle me-2"></i>
                            Información del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Pedido</label>
                                <input type="text" class="form-control" value="{{ $pedido->numero_pedido }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                        id="estado"
                                        name="estado"
                                        required>
                                    @foreach(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'] as $valor => $nombre)
                                        <option value="{{ $valor }}"
                                                {{ old('estado', $pedido->estado) == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente</label>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $pedido->cliente->name }}</div>
                                        <small class="text-muted">{{ $pedido->cliente->email }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendedor</label>
                                @if($pedido->vendedor)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-badge text-muted"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $pedido->vendedor->name }}</div>
                                            <small class="text-muted">{{ $pedido->vendedor->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Sin vendedor asignado</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Creación</label>
                                <input type="text" class="form-control" value="{{ $pedido->created_at->format('d/m/Y H:i') }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Última Actualización</label>
                                <input type="text" class="form-control" value="{{ $pedido->updated_at->format('d/m/Y H:i') }}" readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Observaciones adicionales del pedido...">{{ old('observaciones', $pedido->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos del Pedido -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-box-seam me-2"></i>
                            Productos del Pedido ({{ count($pedido->detalles_embebidos) }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio Unit.</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($detalle->producto->imagen)
                                                        <img src="{{ asset('storage/' . $detalle->producto->imagen) }}"
                                                             alt="{{ $detalle->producto->nombre }}"
                                                             class="rounded"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                             style="width: 50px; height: 50px;">
                                                            <i class="bi bi-image text-white"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $detalle->producto->nombre }}</div>
                                                    @if($detalle->producto->descripcion)
                                                        <small class="text-muted">{{ Str::limit($detalle->producto->descripcion, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $detalle->producto->categoria->nombre }}</span>
                                        </td>
                                        <td>
                                            <strong>${{ format_currency($detalle->precio_unitario) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                                        </td>
                                        <td>
                                            <strong>${{ format_currency($detalle->total) }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen y Acciones -->
            <div class="col-lg-4">
                <!-- Resumen del Pedido -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-calculator me-2"></i>
                            Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos:</span>
                            <span>{{ count($pedido->detalles_embebidos) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cantidad total:</span>
                            <span>{{ array_sum(array_column($pedido->detalles_embebidos, 'cantidad')) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ format_currency($pedido->subtotal) }}</span>
                        </div>
                        @if($pedido->descuento > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Descuento:</span>
                                <span>-${{ format_currency($pedido->descuento) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Final:</strong>
                            <strong style="color: var(--primary-color); font-size: 1.1em;">${{ format_currency($pedido->total_final) }}</strong>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Para modificar productos, elimina este pedido y crea uno nuevo.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Estado y Seguimiento -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-list-check me-2"></i>
                            Seguimiento
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            @php
                                $estados = [
                                    'pendiente' => ['nombre' => 'Pendiente', 'icono' => 'clock', 'color' => 'warning'],
                                    'confirmado' => ['nombre' => 'Confirmado', 'icono' => 'check-circle', 'color' => 'info'],
                                    'en_preparacion' => ['nombre' => 'En Preparación', 'icono' => 'gear', 'color' => 'primary'],
                                    'listo' => ['nombre' => 'Listo', 'icono' => 'box-seam', 'color' => 'secondary'],
                                    'en_camino' => ['nombre' => 'En Camino', 'icono' => 'truck', 'color' => 'primary'],
                                    'entregado' => ['nombre' => 'Entregado', 'icono' => 'check-circle-fill', 'color' => 'success'],
                                    'cancelado' => ['nombre' => 'Cancelado', 'icono' => 'x-circle', 'color' => 'danger']
                                ];

                                $estadosOrden = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado'];
                                $estadoActualIndex = array_search($pedido->estado, $estadosOrden);
                            @endphp

                            @foreach($estadosOrden as $index => $estado)
                                @php
                                    $estadoInfo = $estados[$estado];
                                    $esActual = $pedido->estado === $estado;
                                    $esCompletado = $index < $estadoActualIndex || $esActual;
                                    $esCancelado = $pedido->estado === 'cancelado';
                                @endphp

                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $esCancelado && !$esActual ? 'bg-light' : ($esCompletado ? 'bg-'.$estadoInfo['color'] : 'bg-light') }}"
                                             style="width: 35px; height: 35px;">
                                            <i class="bi bi-{{ $estadoInfo['icono'] }} {{ $esCancelado && !$esActual ? 'text-muted' : ($esCompletado ? 'text-white' : 'text-muted') }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium {{ $esActual ? 'text-'.$estadoInfo['color'] : ($esCancelado && !$esActual ? 'text-muted' : '') }}">
                                            {{ $estadoInfo['nombre'] }}
                                            @if($esActual)
                                                <span class="badge bg-{{ $estadoInfo['color'] }} ms-2">Actual</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($pedido->estado === 'cancelado')
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger"
                                             style="width: 35px; height: 35px;">
                                            <i class="bi bi-x-circle text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium text-danger">
                                            Cancelado
                                            <span class="badge bg-danger ms-2">Actual</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary"
                                    onclick="event.preventDefault(); confirmSavePedido('pedido-form', 'Los cambios del pedido se guardarán y actualizarán en el sistema.')">
                                <i class="bi bi-check-circle me-1"></i>
                                Actualizar Pedido
                            </button>
                            <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>
                                Ver Detalles
                            </a>
                            <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Incluir modales profesionales de pedidos -->
@include('admin.partials.modals-pedidos-professional')

{{-- Cargar scripts específicos para pedidos --}}
<script src="{{ asset('js/admin/pedidos-modals.js') }}"></script>

{{-- Estilos específicos movidos a: public/css/admin/pedidos-edit.css --}}
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-edit.css') }}">
@endsection