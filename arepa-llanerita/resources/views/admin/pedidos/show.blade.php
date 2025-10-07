@extends('layouts.admin')

@section('title', '- Ver Pedido')
@section('page-title', 'Detalles del Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/pedidos.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-muted mb-0">Información completa del pedido: <strong>{{ $pedido->numero_pedido }}</strong></h2>
                </div>
                <div>
                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver a Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes flash manejados por AdminAlerts en admin-functions.js --}}

    <div class="row">
        <!-- Información del Pedido -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-basket3 me-2"></i>
                        Información del Pedido
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Número de Pedido</h6>
                            <p class="text-muted">{{ $pedido->numero_pedido }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Estado</h6>
                            @switch($pedido->estado)
                                @case('pendiente')
                                    <span class="badge bg-warning fs-6">Pendiente</span>
                                    @break
                                @case('confirmado')
                                    <span class="badge bg-info fs-6">Confirmado</span>
                                    @break
                                @case('en_preparacion')
                                    <span class="badge bg-primary fs-6">En Preparación</span>
                                    @break
                                @case('listo')
                                    <span class="badge bg-secondary fs-6">Listo</span>
                                    @break
                                @case('en_camino')
                                    <span class="badge fs-6" style="background-color: var(--primary-color);">En Camino</span>
                                    @break
                                @case('entregado')
                                    <span class="badge bg-success fs-6">Entregado</span>
                                    @break
                                @case('cancelado')
                                    <span class="badge bg-danger fs-6">Cancelado</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Fecha de Pedido</h6>
                            <p class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Última Actualización</h6>
                            <p class="text-muted">{{ $pedido->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($pedido->observaciones)
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-2">Observaciones</h6>
                                <p class="text-muted">{{ $pedido->observaciones }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-box-seam me-2"></i>
                        Productos ({{ count($pedido->detalles_embebidos) }})
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
                                @foreach($pedido->detalles_embebidos as $detalle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @php
                                                    $producto = isset($detalle['producto_id']) ? $productosDetalles->get($detalle['producto_id']) : null;
                                                @endphp
                                                @if($producto && $producto->imagen)
                                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                                         alt="{{ $detalle['producto_nombre'] ?? 'Producto' }}"
                                                         class="rounded"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $detalle['producto_nombre'] ?? 'Producto no disponible' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($producto && $producto->categoria)
                                            <span class="badge bg-info">{{ $producto->categoria->nombre }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin categoría</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ format_currency($detalle['precio_unitario'] ?? 0) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $detalle['cantidad'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <strong>${{ format_currency($detalle['total'] ?? 0) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Cliente y Resumen -->
        <div class="col-lg-4">
            <!-- Cliente -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person me-2"></i>
                        Cliente
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person fs-1 text-muted"></i>
                        </div>
                        <h6 class="fw-semibold">{{ $pedido->cliente->name }}</h6>
                        <p class="text-muted mb-0">{{ $pedido->cliente->email }}</p>
                    </div>
                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="fw-semibold mb-1">{{ $pedido->cliente->pedidos_count ?? 0 }}</h6>
                                    <small class="text-muted">Pedidos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-semibold mb-1">Cliente</h6>
                                <small class="text-muted">{{ ucfirst($pedido->cliente->tipo_usuario) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendedor -->
            @if($pedido->vendedor)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-person-badge me-2"></i>
                            Vendedor
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person-badge fs-3 text-muted"></i>
                            </div>
                            <h6 class="fw-semibold">{{ $pedido->vendedor->name }}</h6>
                            <p class="text-muted mb-0">{{ $pedido->vendedor->email }}</p>
                            <small class="text-muted">{{ ucfirst($pedido->vendedor->tipo_usuario) }}</small>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Resumen del Pedido -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-calculator me-2"></i>
                        Resumen
                    </h5>
                </div>
                <div class="card-body p-4">
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

                    <div class="border-top pt-3">
                        <small class="text-muted">
                            <strong>Productos:</strong> {{ count($pedido->detalles_embebidos) }}<br>
                            <strong>Cantidad total:</strong> {{ array_sum(array_column($pedido->detalles_embebidos, 'cantidad')) }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('admin.pedidos.edit', $pedido) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i>
                                Editar Pedido
                            </a>
                        </div>
                    @endif

                    <!-- Cambiar Estado -->
                    <div class="dropdown d-grid mb-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Cambiar Estado
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'] as $valor => $nombre)
                                @if($valor != $pedido->estado)
                                    <li>
                                        <a class="dropdown-item" href="#"
                                           onclick="event.preventDefault(); confirmStatusChangePedido({{ json_encode($pedido->id) }}, {{ json_encode($valor) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode(ucfirst($pedido->estado)) }})">
                                            {{ $nombre }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    @if($pedido->estado != 'entregado')
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-danger"
                                    onclick="event.preventDefault(); confirmDeletePedido({{ json_encode($pedido->id) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode('$' . format_currency($pedido->total_final)) }}, {{ json_encode(ucfirst($pedido->estado)) }})">
                                <i class="bi bi-trash me-1"></i>
                                Eliminar Pedido
                            </button>
                        </div>
                    @endif

                    <!-- Formularios ocultos -->
                    <form id="status-form-{{ $pedido->id }}" action="{{ route('admin.pedidos.update-status', $pedido) }}" method="POST" class="d-none">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" id="estado-{{ $pedido->id }}">
                    </form>

                    <form id="delete-form-{{ $pedido->id }}" action="{{ route('admin.pedidos.destroy', $pedido) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir modales profesionales de pedidos -->
@include('admin.partials.modals-pedidos-professional')

{{-- Cargar scripts específicos para pedidos --}}
<script src="{{ asset('js/admin/pedidos-modals.js') }}"></script>

<script>
// Inicializar modales profesionales para pedidos en vista show
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Pedidos Show con modales profesionales cargado...');

    // Test completo de modales en vista show
    setTimeout(function() {
        console.log('🔍 Test modales en vista show:');
        console.log('- Bootstrap disponible:', typeof bootstrap !== 'undefined');
        console.log('- confirmDeletePedido disponible:', typeof confirmDeletePedido !== 'undefined');
        console.log('- confirmStatusChangePedido disponible:', typeof confirmStatusChangePedido !== 'undefined');

        // Verificar elementos HTML
        const deleteModal = document.getElementById('deletePedidoConfirmModal');
        const statusModal = document.getElementById('statusPedidoConfirmModal');
        console.log('- HTML deleteModal encontrado:', deleteModal !== null);
        console.log('- HTML statusModal encontrado:', statusModal !== null);

        if (typeof confirmDeletePedido !== 'undefined' && typeof confirmStatusChangePedido !== 'undefined') {
            console.log('✅ Funciones de modales disponibles en vista show');
        } else {
            console.error('❌ Funciones de modales no encontradas en vista show');
        }
    }, 500);
});
</script>
@endsection