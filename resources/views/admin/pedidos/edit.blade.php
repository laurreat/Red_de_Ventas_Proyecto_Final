@extends('layouts.admin')

@section('title', '- Editar Pedido')
@section('page-title', 'Editar Pedido')

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
                    <i class="bi bi-pencil-square"></i> Editar Pedido {{ $pedido->numero_pedido }}
                </h1>
                <p class="pedido-header-subtitle">
                    Modifica el estado y observaciones del pedido ·
                    <span class="pedido-badge pedido-badge-{{ $pedido->estado }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                    </span>
                </p>
            </div>
            <div class="pedido-header-actions">
                <a href="{{ route('admin.pedidos.show', $pedido) }}" class="pedido-btn pedido-btn-primary">
                    <i class="bi bi-eye"></i>
                    <span>Ver Detalles</span>
                </a>
                <a href="{{ route('admin.pedidos.index') }}" class="pedido-btn pedido-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
    </div>

    <form id="pedido-form" action="{{ route('admin.pedidos.update', $pedido) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Columna Principal (Información Editable) --}}
            <div class="col-lg-8">
                {{-- Información del Pedido --}}
                <div class="pedido-detail-card fade-in-up">
                    <div class="pedido-detail-header">
                        <i class="bi bi-info-circle"></i>
                        <h3 class="pedido-detail-title">Información del Pedido</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="row">
                            {{-- Número de Pedido (Solo lectura) --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hash"></i> Número de Pedido
                                </label>
                                <input type="text" class="form-control" value="{{ $pedido->numero_pedido }}" readonly
                                       style="border-radius:10px;padding:.75rem;background:var(--gray-100);cursor:not-allowed;">
                            </div>

                            {{-- Estado del Pedido (Editable) --}}
                            <div class="col-md-6 mb-4">
                                <label for="estado" class="form-label fw-semibold">
                                    <i class="bi bi-circle-fill" style="color:var(--wine);"></i> Estado del Pedido *
                                </label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                        id="estado"
                                        name="estado"
                                        required
                                        style="border-radius:10px;padding:.75rem;">
                                    @foreach(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'] as $valor => $nombre)
                                        <option value="{{ $valor }}" {{ old('estado', $pedido->estado) == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">
                                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                    </div>
                                @enderror
                                <small style="color:var(--gray-600);margin-top:.5rem;display:block;">
                                    <i class="bi bi-info-circle"></i> Si cambias a "Cancelado", el stock se devolverá automáticamente
                                </small>
                            </div>

                            {{-- Fechas (Solo lectura) --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar3"></i> Fecha de Creación
                                </label>
                                <input type="text" class="form-control" value="{{ $pedido->created_at->format('d/m/Y H:i') }}" readonly
                                       style="border-radius:10px;padding:.75rem;background:var(--gray-100);cursor:not-allowed;">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-clock-history"></i> Última Actualización
                                </label>
                                <input type="text" class="form-control" value="{{ $pedido->updated_at->format('d/m/Y H:i') }}" readonly
                                       style="border-radius:10px;padding:.75rem;background:var(--gray-100);cursor:not-allowed;">
                            </div>

                            {{-- Observaciones (Editable) --}}
                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text-fill"></i> Observaciones
                                </label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="4"
                                          placeholder="Agrega observaciones adicionales sobre el pedido..."
                                          style="border-radius:10px;padding:.75rem;">{{ old('observaciones', $pedido->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cliente y Vendedor (Solo Visualización) --}}
                <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-people"></i>
                        <h3 class="pedido-detail-title">Cliente y Vendedor</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="row">
                            {{-- Cliente --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-circle"></i> Cliente
                                </label>
                                <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:12px;padding:1rem;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,var(--wine),var(--wine-dark));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;flex-shrink:0;">
                                            {{ strtoupper(substr($pedido->cliente->name, 0, 2)) }}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">{{ $pedido->cliente->name }}</div>
                                            <small style="color:var(--gray-600);"><i class="bi bi-envelope"></i> {{ $pedido->cliente->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Vendedor --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-badge"></i> Vendedor
                                </label>
                                @if($pedido->vendedor)
                                    <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:12px;padding:1rem;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,var(--info),#2563eb);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;flex-shrink:0;">
                                                {{ strtoupper(substr($pedido->vendedor->name, 0, 2)) }}
                                            </div>
                                            <div style="flex:1;">
                                                <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">{{ $pedido->vendedor->name }}</div>
                                                <small style="color:var(--gray-600);"><i class="bi bi-envelope"></i> {{ $pedido->vendedor->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:12px;padding:1rem;text-align:center;color:var(--gray-500);">
                                        <i class="bi bi-person-x fs-3"></i>
                                        <p style="margin:.5rem 0 0 0;">Sin vendedor asignado</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Productos del Pedido (Solo Visualización) --}}
                <div class="pedido-detail-card fade-in-up animate-delay-2" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-box-seam"></i>
                        <h3 class="pedido-detail-title">Productos del Pedido ({{ count($pedido->detalles_embebidos) }})</h3>
                    </div>
                    <div class="pedido-detail-body" style="padding:0;">
                        @if(count($pedido->detalles_embebidos) > 0)
                            @foreach($pedido->detalles_embebidos as $index => $detalle)
                                @php
                                    $producto = isset($detalle['producto_id']) ? \App\Models\Producto::find($detalle['producto_id']) : null;
                                @endphp
                                <div class="pedido-product-item" style="{{ $index > 0 ? 'margin-top:0;' : '' }}">
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

                        {{-- Nota informativa --}}
                        <div style="padding:1.5rem;background:rgba(59,130,246,0.05);border-top:1px solid var(--gray-200);">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-info-circle-fill" style="color:var(--info);font-size:1.5rem;flex-shrink:0;"></i>
                                <div style="flex:1;">
                                    <h6 style="font-weight:700;color:var(--info);margin-bottom:.5rem;">Información Importante</h6>
                                    <p style="margin:0;color:var(--gray-700);font-size:.875rem;">
                                        Los productos no pueden ser editados una vez creado el pedido. Para modificar productos, debes eliminar este pedido y crear uno nuevo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral (Resumen, Timeline y Acciones) --}}
            <div class="col-lg-4">
                {{-- Resumen Financiero --}}
                <div class="pedido-detail-card fade-in-up animate-delay-3">
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
                                <div style="width:36px;height:36px;border-radius:50%;background:{{ $esCancelado && !$esActual ? 'var(--gray-200)' : ($esCompletado ? ($estado === 'en_camino' ? 'var(--wine)' : 'var(--' . $estadoInfo['color'] . ')') : 'var(--gray-200)') }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.125rem;flex-shrink:0;">
                                    <i class="bi bi-{{ $estadoInfo['icono'] }}"></i>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:{{ $esActual ? '700' : '500' }};color:{{ $esActual ? 'var(--wine)' : 'var(--gray-700)' }};font-size:.875rem;">
                                        {{ $estadoInfo['nombre'] }}
                                        @if($esActual && !$esCancelado)
                                            <span class="pedido-badge pedido-badge-{{ $estado }}" style="margin-left:.5rem;font-size:.688rem;">Actual</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($esCancelado)
                            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--gray-300);">
                                <div style="display:flex;align-items:center;gap:1rem;">
                                    <div style="width:36px;height:36px;border-radius:50%;background:var(--danger);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.125rem;flex-shrink:0;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-weight:700;color:var(--danger);font-size:.875rem;">
                                            Cancelado
                                            <span class="pedido-badge pedido-badge-cancelado" style="margin-left:.5rem;font-size:.688rem;">Actual</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-gear"></i>
                        <h3 class="pedido-detail-title">Acciones</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <button type="submit" class="pedido-btn pedido-btn-primary" style="width:100%;margin-bottom:.75rem;justify-content:center;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Guardar Cambios</span>
                        </button>

                        <a href="{{ route('admin.pedidos.show', $pedido) }}" class="pedido-btn pedido-btn-outline" style="width:100%;margin-bottom:.75rem;justify-content:center;background:rgba(59,130,246,0.05);color:var(--info);border-color:var(--info);">
                            <i class="bi bi-eye"></i>
                            <span>Ver Detalles</span>
                        </a>

                        <a href="{{ route('admin.pedidos.index') }}" class="pedido-btn pedido-btn-outline" style="width:100%;justify-content:center;">
                            <i class="bi bi-x-circle"></i>
                            <span>Cancelar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
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
