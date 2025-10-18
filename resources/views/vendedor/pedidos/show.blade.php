@extends('layouts.vendedor')

@section('title', 'Detalles del Pedido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-professional.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-professional.css')) }}">
@endpush

@section('content')
<!-- Header -->
<div class="pedidos-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="pedidos-header-icon-badge">
                <i class="bi bi-receipt"></i>
            </div>
            <h1 class="pedidos-header-title">
                Pedido #{{ $pedido->numero_pedido }}
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-calendar3 me-2"></i>
                {{ $pedido->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                @if($pedido->estado == 'pendiente')
                <a href="{{ route('vendedor.pedidos.edit', $pedido->_id) }}" class="pedidos-btn-primary">
                    <i class="bi bi-pencil"></i>
                    <span>Editar</span>
                </a>
                @endif
                <a href="{{ route('vendedor.pedidos.index') }}" class="pedidos-btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Content -->
<div class="row g-4">
    <!-- Main Info -->
    <div class="col-lg-8">
        <!-- Estado Card -->
        <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-1">
            <div class="pedidos-table-header">
                <div class="pedidos-table-header-left">
                    <h3 class="pedidos-table-title">
                        <i class="bi bi-flag"></i>
                        Estado del Pedido
                    </h3>
                </div>
            </div>
            <div class="p-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="pedidos-badge pedidos-badge-{{ $pedido->estado }}" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
                        @switch($pedido->estado)
                            @case('pendiente')
                                <i class="bi bi-hourglass-split"></i>
                                @break
                            @case('confirmado')
                                <i class="bi bi-check-circle"></i>
                                @break
                            @case('preparando')
                                <i class="bi bi-gear"></i>
                                @break
                            @case('en_camino')
                                <i class="bi bi-truck"></i>
                                @break
                            @case('entregado')
                                <i class="bi bi-box-seam"></i>
                                @break
                            @case('cancelado')
                                <i class="bi bi-x-circle"></i>
                                @break
                        @endswitch
                        {{ ucfirst($pedido->estado) }}
                    </span>
                    @if($pedido->estado != 'cancelado' && $pedido->estado != 'entregado')
                    <button class="pedidos-btn-filter" onclick="pedidosManager.showStatusModal('{{ $pedido->_id }}')">
                        <i class="bi bi-arrow-repeat"></i>
                        Cambiar Estado
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos Card -->
        <div class="pedidos-table-wrapper fade-in-up animate-delay-2">
            <div class="pedidos-table-header">
                <div class="pedidos-table-header-left">
                    <h3 class="pedidos-table-title">
                        <i class="bi bi-cart"></i>
                        Productos
                    </h3>
                    @php
                        $productosData = $pedido->productos ?? $pedido->detalles ?? [];
                        $conteoProductos = is_array($productosData) ? count($productosData) : 0;
                    @endphp
                    <span class="pedidos-table-count">{{ $conteoProductos }} productos</span>
                </div>
            </div>
            <div class="pedidos-table-container">
                @if($conteoProductos > 0)
                <table class="pedidos-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productosData as $producto)
                        <tr>
                            <td>
                                @php
                                    // Intentar obtener nombre de diferentes estructuras
                                    $nombreProducto = $producto['nombre'] 
                                        ?? $producto['producto_data']['nombre'] 
                                        ?? ($producto['producto'] ?? null)['nombre'] 
                                        ?? 'Producto sin nombre';
                                    
                                    $codigoProducto = $producto['codigo'] 
                                        ?? $producto['producto_data']['codigo'] 
                                        ?? ($producto['producto'] ?? null)['codigo'] 
                                        ?? 'N/A';
                                @endphp
                                <div class="fw-semibold">{{ $nombreProducto }}</div>
                                <small class="text-muted">Código: {{ $codigoProducto }}</small>
                            </td>
                            <td>{{ $producto['cantidad'] ?? 0 }}</td>
                            <td>
                                @php
                                    $precio = $producto['precio'] 
                                        ?? $producto['precio_unitario'] 
                                        ?? $producto['producto_data']['precio'] 
                                        ?? 0;
                                @endphp
                                ${{ number_format(to_float($precio), 0) }}
                            </td>
                            <td class="fw-bold text-wine">
                                ${{ number_format(to_float($producto['subtotal'] ?? 0), 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="pedidos-empty-state" style="padding: 3rem;">
                    <div class="pedidos-empty-illustration" style="font-size: 3rem;">
                        <i class="bi bi-cart-x"></i>
                    </div>
                    <h4 class="pedidos-empty-title" style="font-size: 1.25rem;">Sin productos</h4>
                    <p class="pedidos-empty-message" style="font-size: 0.875rem;">
                        Este pedido no tiene productos registrados.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Cliente Card -->
        <div class="pedidos-table-wrapper mb-4 fade-in-up animate-delay-1">
            <div class="pedidos-table-header">
                <div class="pedidos-table-header-left">
                    <h3 class="pedidos-table-title">
                        <i class="bi bi-person-badge"></i>
                        Cliente
                    </h3>
                </div>
            </div>
            <div class="p-4">
                <div class="pedidos-client-info mb-3">
                    <div class="pedidos-client-avatar" style="width: 56px; height: 56px; font-size: 1.25rem;">
                        {{ strtoupper(substr($pedido->cliente_data['name'] ?? 'N', 0, 2)) }}
                    </div>
                    <div class="pedidos-client-details">
                        <div class="pedidos-client-name" style="font-size: 1.125rem;">
                            {{ $pedido->cliente_data['name'] ?? 'N/A' }}
                        </div>
                        <div class="pedidos-client-email">
                            {{ $pedido->cliente_data['email'] ?? 'Sin email' }}
                        </div>
                    </div>
                </div>
                @if(isset($pedido->cliente_data['telefono']))
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-telephone text-muted"></i>
                    <span>{{ $pedido->cliente_data['telefono'] }}</span>
                </div>
                @endif
                @if(isset($pedido->direccion_entrega))
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-geo-alt text-muted mt-1"></i>
                    <span>{{ $pedido->direccion_entrega }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Totales Card -->
        <div class="pedidos-table-wrapper fade-in-up animate-delay-2">
            <div class="pedidos-table-header">
                <div class="pedidos-table-header-left">
                    <h3 class="pedidos-table-title">
                        <i class="bi bi-calculator"></i>
                        Totales
                    </h3>
                </div>
            </div>
            <div class="p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Subtotal:</span>
                    <strong>${{ number_format(to_float($pedido->subtotal ?? 0), 0) }}</strong>
                </div>
                @if(isset($pedido->descuento) && $pedido->descuento > 0)
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Descuento:</span>
                    <strong class="text-danger">-${{ number_format(to_float($pedido->descuento), 0) }}</strong>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">IVA (19%):</span>
                    <strong>${{ number_format(to_float($pedido->iva ?? 0), 0) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold" style="font-size: 1.125rem;">Total:</span>
                    <strong class="text-wine" style="font-size: 1.5rem;">
                        ${{ number_format(to_float($pedido->total_final ?? 0), 0) }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Notas Card -->
        @if(isset($pedido->notas) && $pedido->notas)
        <div class="pedidos-table-wrapper mt-4 fade-in-up animate-delay-3">
            <div class="pedidos-table-header">
                <div class="pedidos-table-header-left">
                    <h3 class="pedidos-table-title">
                        <i class="bi bi-sticky"></i>
                        Notas
                    </h3>
                </div>
            </div>
            <div class="p-4">
                <p class="mb-0">{{ $pedido->notas }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/pedidos-modern.js') }}?v={{ filemtime(public_path('js/vendedor/pedidos-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    pedidosManager.showToast("{{ session('success') }}", 'success', 3000);
    @endif
    
    @if(session('error'))
    pedidosManager.showToast("{{ session('error') }}", 'error', 5000);
    @endif
});

.text-wine {
    color: var(--wine-primary);
}
</script>
@endpush
