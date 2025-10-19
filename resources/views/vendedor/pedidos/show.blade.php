@extends('layouts.vendedor')

@section('title', 'Detalles del Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid fade-in">
    {{-- Header Hero --}}
    <div class="pedido-header scale-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="pedido-header-title">
                    <i class="bi bi-file-earmark-text"></i> Pedido {{ $pedido->numero_pedido }}
                </h1>
                <p class="pedido-header-subtitle">
                    Información completa del pedido ·
                    <span class="pedido-badge pedido-badge-{{ $pedido->estado }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                </p>
            </div>
            <div class="pedido-header-actions">
                @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                    <a href="{{ route('vendedor.pedidos.edit', $pedido->_id) }}" class="pedido-btn pedido-btn-primary">
                        <i class="bi bi-pencil"></i>
                        <span>Editar</span>
                    </a>
                @endif
                <a href="{{ route('vendedor.pedidos.index') }}" class="pedido-btn pedido-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Columna Principal (Información del Pedido y Productos) --}}
        <div class="col-lg-8">
            {{-- Información del Pedido --}}
            <div class="pedido-detail-card fade-in-up">
                <div class="pedido-detail-header">
                    <i class="bi bi-info-circle"></i>
                    <h3 class="pedido-detail-title">Información del Pedido</h3>
                </div>
                <div class="pedido-detail-body">
                    <div class="pedido-info-grid">
                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-hash"></i> Número de Pedido
                            </div>
                            <div class="pedido-info-value">{{ $pedido->numero_pedido }}</div>
                        </div>

                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-circle-fill"></i> Estado Actual
                            </div>
                            <div class="pedido-info-value">
                                <span class="pedido-badge pedido-badge-{{ $pedido->estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                </span>
                            </div>
                        </div>

                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-calendar3"></i> Fecha de Creación
                            </div>
                            <div class="pedido-info-value">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-clock-history"></i> Última Actualización
                            </div>
                            <div class="pedido-info-value">{{ $pedido->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    @if($pedido->notas)
                        <div style="margin-top:1.5rem;padding:1.25rem;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-200);">
                            <div class="pedido-info-label" style="margin-bottom:.75rem;">
                                <i class="bi bi-chat-left-text"></i> Notas
                            </div>
                            <p style="margin:0;color:var(--gray-700);line-height:1.6;">{{ $pedido->notas }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Productos del Pedido --}}
            <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-box-seam"></i>
                    @php
                        $productosData = $pedido->productos ?? $pedido->detalles ?? [];
                        $conteoProductos = is_array($productosData) ? count($productosData) : 0;
                    @endphp
                    <h3 class="pedido-detail-title">Productos del Pedido ({{ $conteoProductos }})</h3>
                </div>
                <div class="pedido-detail-body">
                    @if($conteoProductos > 0)
                        <div class="pedido-productos-list">
                            @foreach($productosData as $producto)
                                @php
                                    $nombreProducto = $producto['nombre']
                                        ?? $producto['producto_data']['nombre']
                                        ?? ($producto['producto'] ?? null)['nombre']
                                        ?? 'Producto sin nombre';

                                    $codigoProducto = $producto['codigo']
                                        ?? $producto['producto_data']['codigo']
                                        ?? ($producto['producto'] ?? null)['codigo']
                                        ?? 'N/A';

                                    $precio = $producto['precio']
                                        ?? $producto['precio_unitario']
                                        ?? $producto['producto_data']['precio']
                                        ?? 0;

                                    $cantidad = $producto['cantidad'] ?? 0;
                                    $subtotal = $producto['subtotal'] ?? ($precio * $cantidad);
                                @endphp
                                <div class="pedido-producto-item">
                                    <div class="pedido-producto-content">
                                        <div class="pedido-producto-info">
                                            <div class="pedido-producto-nombre">{{ $nombreProducto }}</div>
                                            <div class="pedido-producto-detalles">
                                                Código: {{ $codigoProducto }} · Cantidad: {{ $cantidad }} × ${{ number_format(to_float($precio), 0) }}
                                            </div>
                                        </div>
                                        <div class="pedido-producto-precio">
                                            ${{ number_format(to_float($subtotal), 0) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="pedido-empty-state">
                            <div class="pedido-empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h4 class="pedido-empty-state-title">Sin productos</h4>
                            <p class="pedido-empty-state-text">Este pedido no tiene productos registrados.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Información de Entrega --}}
            @if($pedido->direccion_entrega || $pedido->telefono_entrega)
            <div class="pedido-detail-card fade-in-up animate-delay-2" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-truck"></i>
                    <h3 class="pedido-detail-title">Información de Entrega</h3>
                </div>
                <div class="pedido-detail-body">
                    <div class="pedido-info-grid">
                        @if($pedido->direccion_entrega)
                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-geo-alt"></i> Dirección
                            </div>
                            <div class="pedido-info-value">{{ $pedido->direccion_entrega }}</div>
                        </div>
                        @endif

                        @if($pedido->telefono_entrega)
                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-telephone"></i> Teléfono
                            </div>
                            <div class="pedido-info-value">{{ $pedido->telefono_entrega }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar Derecho --}}
        <div class="col-lg-4">
            {{-- Cliente --}}
            <div class="pedido-detail-card fade-in-up animate-delay-2">
                <div class="pedido-detail-header">
                    <i class="bi bi-person-circle"></i>
                    <h3 class="pedido-detail-title">Cliente</h3>
                </div>
                <div class="pedido-detail-body">
                    <div class="pedido-cliente-card">
                        <div class="pedido-cliente-avatar">
                            {{ strtoupper(substr($pedido->cliente_data['name'] ?? 'C', 0, 2)) }}
                        </div>
                        <div class="pedido-cliente-info">
                            <div class="pedido-cliente-nombre">{{ $pedido->cliente_data['name'] ?? 'N/A' }}</div>
                            <div class="pedido-cliente-email">{{ $pedido->cliente_data['email'] ?? 'Sin email' }}</div>
                            @if(isset($pedido->cliente_data['telefono']))
                                <div class="pedido-cliente-telefono">
                                    <i class="bi bi-telephone"></i> {{ $pedido->cliente_data['telefono'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen de Totales --}}
            <div class="pedido-detail-card fade-in-up animate-delay-2" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-calculator"></i>
                    <h3 class="pedido-detail-title">Resumen de Totales</h3>
                </div>
                <div class="pedido-detail-body">
                    <div class="pedido-totales">
                        <div class="pedido-total-item">
                            <span class="pedido-total-label">Subtotal:</span>
                            <span class="pedido-total-value">${{ number_format(to_float($pedido->subtotal ?? 0), 0) }}</span>
                        </div>

                        @if(isset($pedido->descuento) && $pedido->descuento > 0)
                        <div class="pedido-total-item">
                            <span class="pedido-total-label">Descuento:</span>
                            <span class="pedido-total-value" style="color:var(--danger);">-${{ number_format(to_float($pedido->descuento), 0) }}</span>
                        </div>
                        @endif

                        <div class="pedido-total-item pedido-total-final">
                            <span class="pedido-total-label">Total:</span>
                            <span class="pedido-total-value">${{ number_format(to_float($pedido->total_final ?? 0), 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-gear"></i>
                    <h3 class="pedido-detail-title">Acciones</h3>
                </div>
                <div class="pedido-detail-body">
                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                        <a href="{{ route('vendedor.pedidos.edit', $pedido->_id) }}"
                           class="pedido-btn pedido-btn-primary"
                           style="width:100%;margin-bottom:.75rem;justify-content:center;">
                            <i class="bi bi-pencil"></i>
                            <span>Editar Pedido</span>
                        </a>
                    @endif

                    {{-- Cambiar Estado con Modal --}}
                    <button type="button"
                            class="pedido-btn pedido-btn-outline"
                            style="width:100%;margin-bottom:.75rem;justify-content:center;background:rgba(59,130,246,0.05);color:var(--info);border-color:var(--info);"
                            data-action="status-pedido"
                            data-pedido-id="{{ $pedido->_id }}"
                            data-numero-pedido="{{ $pedido->numero_pedido }}"
                            data-cliente-nombre="{{ $pedido->cliente_data['name'] ?? 'Cliente' }}"
                            data-estado-actual="{{ $pedido->estado }}"
                            data-estados="{{ json_encode(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado']) }}">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Cambiar Estado</span>
                    </button>

                    @if($pedido->estado != 'entregado')
                        <button type="button"
                                class="pedido-btn pedido-btn-outline"
                                style="width:100%;justify-content:center;background:rgba(239,68,68,0.05);color:var(--danger);border-color:var(--danger);"
                                data-action="delete-pedido"
                                data-pedido-id="{{ $pedido->_id }}"
                                data-numero-pedido="{{ $pedido->numero_pedido }}"
                                data-cliente-nombre="{{ $pedido->cliente_data['name'] ?? 'Cliente' }}"
                                data-total-final="${{ number_format(to_float($pedido->total_final ?? 0), 0) }}"
                                data-estado="{{ ucfirst($pedido->estado) }}">
                            <i class="bi bi-trash"></i>
                            <span>Eliminar Pedido</span>
                        </button>
                    @endif

                    {{-- Formularios ocultos --}}
                    <form id="status-form-{{ $pedido->_id }}"
                          action="{{ route('vendedor.pedidos.update-estado', $pedido->_id) }}"
                          method="POST"
                          class="d-none">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" id="estado-{{ $pedido->_id }}">
                    </form>

                    <form id="delete-form-{{ $pedido->_id }}"
                          action="{{ route('vendedor.pedidos.destroy', $pedido->_id) }}"
                          method="POST"
                          class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

<script>
// Inicializar PedidosManager
window.pedidosManager = new PedidosManager();

// Debug: Verificar que el formulario existe
console.log('Formulario de eliminación:', document.getElementById('delete-form-{{ $pedido->_id }}'));
console.log('Formulario de estado:', document.getElementById('status-form-{{ $pedido->_id }}'));
</script>

{{-- Mostrar mensajes flash --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("success") }}', 'success');
    }
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("error") }}', 'error');
    }
});
</script>
@endif
@endpush
