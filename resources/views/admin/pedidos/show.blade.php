@extends('layouts.admin')

@section('title', '- Ver Pedido')
@section('page-title', 'Detalles del Pedido')

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
                    <a href="{{ route('admin.pedidos.edit', $pedido) }}" class="pedido-btn pedido-btn-primary">
                        <i class="bi bi-pencil"></i>
                        <span>Editar</span>
                    </a>
                @endif
                <a href="{{ route('admin.pedidos.index') }}" class="pedido-btn pedido-btn-outline">
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

                    @if($pedido->observaciones)
                        <div style="margin-top:1.5rem;padding:1.25rem;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-200);">
                            <div class="pedido-info-label" style="margin-bottom:.75rem;">
                                <i class="bi bi-chat-left-text"></i> Observaciones
                            </div>
                            <p style="margin:0;color:var(--gray-700);line-height:1.6;">{{ $pedido->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Productos del Pedido --}}
            <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-box-seam"></i>
                    <h3 class="pedido-detail-title">Productos del Pedido ({{ count($pedido->detalles_embebidos) }})</h3>
                </div>
                <div class="pedido-detail-body" style="padding:0;">
                    @if(count($pedido->detalles_embebidos) > 0)
                        @foreach($pedido->detalles_embebidos as $index => $detalle)
                            @php
                                $producto = isset($detalle['producto_id']) ? $productosDetalles->get($detalle['producto_id']) : null;
                            @endphp
                            <div class="pedido-product-item {{ $index > 0 ? '' : 'fade-in-up' }}" style="{{ $index > 0 ? 'margin-top:0;' : '' }}">
                                {{-- Imagen del producto --}}
                                @if($producto && $producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         alt="{{ $detalle['producto_nombre'] ?? 'Producto' }}"
                                         class="pedido-product-img">
                                @else
                                    <div class="pedido-product-img" style="background:var(--gray-200);display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-box-seam fs-4 text-muted"></i>
                                    </div>
                                @endif

                                {{-- Información del producto --}}
                                <div class="pedido-product-info">
                                    <div class="pedido-product-name">{{ $detalle['producto_nombre'] ?? 'Producto no disponible' }}</div>
                                    <div class="pedido-product-details">
                                        <span style="margin-right:1rem;">
                                            <i class="bi bi-tag"></i> Precio: <strong>${{ format_currency($detalle['precio_unitario'] ?? 0) }}</strong>
                                        </span>
                                        <span>
                                            <i class="bi bi-box"></i> Cantidad: <strong>{{ $detalle['cantidad'] ?? 0 }}</strong>
                                        </span>
                                    </div>
                                </div>

                                {{-- Precio total --}}
                                <div class="pedido-product-price">
                                    ${{ format_currency($detalle['total'] ?? 0) }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="pedido-empty-state" style="padding:3rem 2rem;">
                            <div class="pedido-empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h4 class="pedido-empty-state-title">No hay productos</h4>
                            <p class="pedido-empty-state-text">Este pedido no tiene productos registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral (Cliente, Vendedor, Resumen y Acciones) --}}
        <div class="col-lg-4">
            {{-- Cliente --}}
            <div class="pedido-detail-card fade-in-up animate-delay-2">
                <div class="pedido-detail-header">
                    <i class="bi bi-person-circle"></i>
                    <h3 class="pedido-detail-title">Cliente</h3>
                </div>
                <div class="pedido-detail-body" style="text-align:center;">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--wine),var(--wine-dark));color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;margin-bottom:1rem;box-shadow:0 8px 16px rgba(114,47,55,0.2);">
                        {{ strtoupper(substr($pedido->cliente->name, 0, 2)) }}
                    </div>
                    <h5 style="font-weight:700;color:var(--gray-900);margin-bottom:.5rem;">{{ $pedido->cliente->name }}</h5>
                    <p style="color:var(--gray-600);margin-bottom:.25rem;"><i class="bi bi-envelope"></i> {{ $pedido->cliente->email }}</p>
                    @if(isset($pedido->cliente->telefono))
                        <p style="color:var(--gray-600);margin-bottom:0;"><i class="bi bi-phone"></i> {{ $pedido->cliente->telefono }}</p>
                    @endif

                    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--gray-200);">
                        <div class="row text-center">
                            <div class="col-6" style="border-right:1px solid var(--gray-200);">
                                <div style="font-size:1.5rem;font-weight:700;color:var(--wine);margin-bottom:.25rem;">
                                    {{ $pedido->cliente->pedidos_count ?? 0 }}
                                </div>
                                <small style="color:var(--gray-500);font-weight:500;">Total Pedidos</small>
                            </div>
                            <div class="col-6">
                                <div style="font-size:1.5rem;font-weight:700;color:var(--info);margin-bottom:.25rem;">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <small style="color:var(--gray-500);font-weight:500;">Cliente Activo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vendedor --}}
            @if($pedido->vendedor)
                <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-person-badge"></i>
                        <h3 class="pedido-detail-title">Vendedor</h3>
                    </div>
                    <div class="pedido-detail-body" style="text-align:center;">
                        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--info),#2563eb);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin-bottom:1rem;box-shadow:0 8px 16px rgba(59,130,246,0.2);">
                            {{ strtoupper(substr($pedido->vendedor->name, 0, 2)) }}
                        </div>
                        <h6 style="font-weight:700;color:var(--gray-900);margin-bottom:.5rem;">{{ $pedido->vendedor->name }}</h6>
                        <p style="color:var(--gray-600);margin-bottom:.25rem;font-size:.875rem;"><i class="bi bi-envelope"></i> {{ $pedido->vendedor->email }}</p>
                        <span class="pedido-badge pedido-badge-confirmado" style="margin-top:.5rem;">Vendedor Asignado</span>
                    </div>
                </div>
            @endif

            {{-- Resumen Financiero --}}
            <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-calculator"></i>
                    <h3 class="pedido-detail-title">Resumen Financiero</h3>
                </div>
                <div class="pedido-detail-body">
                    <div class="pedido-info-grid" style="grid-template-columns:1fr;">
                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-box-seam"></i> Total Productos
                            </div>
                            <div class="pedido-info-value">{{ count($pedido->detalles_embebidos) }} producto(s)</div>
                        </div>

                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-calculator"></i> Cantidad Total
                            </div>
                            <div class="pedido-info-value">{{ array_sum(array_column($pedido->detalles_embebidos, 'cantidad')) }} unidad(es)</div>
                        </div>

                        <div class="pedido-info-item">
                            <div class="pedido-info-label">
                                <i class="bi bi-cash"></i> Subtotal
                            </div>
                            <div class="pedido-info-value">${{ format_currency($pedido->subtotal) }}</div>
                        </div>

                        @if($pedido->descuento > 0)
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-tag"></i> Descuento
                                </div>
                                <div class="pedido-info-value" style="color:var(--success);">-${{ format_currency($pedido->descuento) }}</div>
                            </div>
                        @endif

                        <div class="pedido-info-item" style="background:linear-gradient(135deg,var(--wine),var(--wine-dark));border:none;">
                            <div class="pedido-info-label" style="color:rgba(255,255,255,0.9);">
                                <i class="bi bi-cash-stack"></i> Total Final
                            </div>
                            <div class="pedido-info-value" style="color:#fff;font-size:1.75rem;">${{ format_currency($pedido->total_final) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                <div class="pedido-detail-header">
                    <i class="bi bi-gear"></i>
                    <h3 class="pedido-detail-title">Acciones Disponibles</h3>
                </div>
                <div class="pedido-detail-body">
                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                        <a href="{{ route('admin.pedidos.edit', $pedido) }}" class="pedido-btn pedido-btn-primary" style="width:100%;margin-bottom:.75rem;justify-content:center;">
                            <i class="bi bi-pencil-square"></i>
                            <span>Editar Pedido</span>
                        </a>
                    @endif

                    {{-- Cambiar Estado con Modal --}}
                    <button type="button"
                            class="pedido-btn pedido-btn-outline"
                            style="width:100%;margin-bottom:.75rem;justify-content:center;background:rgba(59,130,246,0.05);color:var(--info);border-color:var(--info);"
                            onclick="showStatusSelector({{ json_encode($pedido->id) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode(ucfirst($pedido->estado)) }}, {{ json_encode(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado']) }})">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Cambiar Estado</span>
                    </button>

                    @if($pedido->estado != 'entregado')
                        <button type="button"
                                class="pedido-btn pedido-btn-outline"
                                style="width:100%;justify-content:center;background:rgba(239,68,68,0.05);color:var(--danger);border-color:var(--danger);"
                                onclick="confirmDeletePedido({{ json_encode($pedido->id) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode('$' . format_currency($pedido->total_final)) }}, {{ json_encode(ucfirst($pedido->estado)) }})">
                            <i class="bi bi-trash"></i>
                            <span>Eliminar Pedido</span>
                        </button>
                    @endif

                    {{-- Formularios ocultos --}}
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
                </div>
            </div>

            {{-- Timeline de Estados --}}
            <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;background:var(--gray-50);border-color:var(--gray-300);">
                <div class="pedido-detail-header" style="background:var(--gray-100);">
                    <i class="bi bi-clock-history"></i>
                    <h3 class="pedido-detail-title">Seguimiento del Pedido</h3>
                </div>
                <div class="pedido-detail-body">
                    @php
                        $estadosTimeline = [
                            'pendiente' => ['nombre' => 'Pendiente', 'icono' => 'clock', 'color' => 'warning'],
                            'confirmado' => ['nombre' => 'Confirmado', 'icono' => 'check-circle', 'color' => 'info'],
                            'en_preparacion' => ['nombre' => 'En Preparación', 'icono' => 'gear', 'color' => 'primary'],
                            'listo' => ['nombre' => 'Listo', 'icono' => 'box-seam', 'color' => 'secondary'],
                            'en_camino' => ['nombre' => 'En Camino', 'icono' => 'truck', 'color' => 'wine'],
                            'entregado' => ['nombre' => 'Entregado', 'icono' => 'check-circle-fill', 'color' => 'success']
                        ];
                        $estadosOrden = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado'];
                        $estadoActualIndex = array_search($pedido->estado, $estadosOrden);
                        $esCancelado = $pedido->estado === 'cancelado';
                    @endphp

                    @foreach($estadosOrden as $index => $estado)
                        @php
                            $estadoInfo = $estadosTimeline[$estado];
                            $esActual = $pedido->estado === $estado;
                            $esCompletado = $index <= $estadoActualIndex && !$esCancelado;
                        @endphp

                        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:{{ $index < count($estadosOrden) - 1 ? '1rem' : '0' }};">
                            <div style="width:40px;height:40px;border-radius:50%;background:{{ $esCancelado && !$esActual ? 'var(--gray-200)' : ($esCompletado ? ($estado === 'en_camino' ? 'var(--wine)' : 'var(--' . $estadoInfo['color'] . ')') : 'var(--gray-200)') }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;transition:var(--transition);{{ $esActual ? 'box-shadow:0 0 0 4px rgba(114,47,55,0.2);' : '' }}">
                                <i class="bi bi-{{ $estadoInfo['icono'] }}"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:{{ $esActual ? '700' : '600' }};color:{{ $esActual ? 'var(--wine)' : 'var(--gray-700)' }};font-size:.938rem;">
                                    {{ $estadoInfo['nombre'] }}
                                    @if($esActual && !$esCancelado)
                                        <span class="pedido-badge pedido-badge-{{ $estado }}" style="margin-left:.5rem;font-size:.75rem;">Estado Actual</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($esCancelado)
                        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--gray-300);">
                            <div style="display:flex;align-items:center;gap:1rem;">
                                <div style="width:40px;height:40px;border-radius:50%;background:var(--danger);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;box-shadow:0 0 0 4px rgba(239,68,68,0.2);">
                                    <i class="bi bi-x-circle-fill"></i>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:700;color:var(--danger);font-size:.938rem;">
                                        Cancelado
                                        <span class="pedido-badge pedido-badge-cancelado" style="margin-left:.5rem;font-size:.75rem;">Estado Actual</span>
                                    </div>
                                    <small style="color:var(--gray-600);">El pedido fue cancelado y el stock devuelto</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Variables globales --}}
<script>
window.pedidosRoutes = {
    details: '{{ route("admin.pedidos.show", ":id") }}',
    updateStatus: '{{ route("admin.pedidos.update-status", ":id") }}',
    destroy: '{{ route("admin.pedidos.destroy", ":id") }}'
};
</script>

{{-- Módulo JavaScript principal --}}
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

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

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ $errors->first() }}', 'error');
    }
});
</script>
@endif
@endpush
