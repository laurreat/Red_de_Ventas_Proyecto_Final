@extends('layouts.admin')

@section('title', '- Crear Pedido')
@section('page-title', 'Crear Nuevo Pedido')

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
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Pedido
                </h1>
                <p class="pedido-header-subtitle">Complete los datos para registrar un nuevo pedido en el sistema</p>
            </div>
            <div class="pedido-header-actions">
                <a href="{{ route('admin.pedidos.index') }}" class="pedido-btn pedido-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver a Pedidos</span>
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.pedidos.store') }}" method="POST" id="pedidoForm" autocomplete="off">
        @csrf

        <div class="row">
            {{-- Información del Pedido --}}
            <div class="col-lg-8">
                {{-- Búsqueda de Cliente y Vendedor --}}
                <div class="pedido-detail-card fade-in-up">
                    <div class="pedido-detail-header">
                        <i class="bi bi-search"></i>
                        <h3 class="pedido-detail-title">Búsqueda de Cliente y Vendedor</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="row">
                            {{-- Búsqueda de Cliente por Cédula --}}
                            <div class="col-md-6 mb-4">
                                <label for="cliente_cedula" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill text-wine"></i> Cédula del Cliente *
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control @error('cliente_id') is-invalid @enderror"
                                           id="cliente_cedula"
                                           placeholder="Ingrese cédula del cliente"
                                           style="border-radius:10px 0 0 10px;padding:.75rem;">
                                    <button class="btn btn-primary" type="button" id="btn-buscar-cliente"
                                            style="border-radius:0 10px 10px 0;padding:.75rem 1.25rem;">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>

                                {{-- Información del cliente encontrado --}}
                                <div id="cliente-info" class="mt-3" style="display: none;">
                                    <div style="background:rgba(16,185,129,0.1);border:2px solid var(--success);border-radius:12px;padding:1rem;">
                                        <div class="d-flex align-items-center">
                                            <div style="width:50px;height:50px;border-radius:50%;background:var(--success);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:1rem;">
                                                <i class="bi bi-person-check-fill"></i>
                                            </div>
                                            <div style="flex:1;">
                                                <div style="font-weight:700;color:var(--success);margin-bottom:.25rem;">Cliente encontrado</div>
                                                <div style="font-weight:600;color:var(--gray-900);" id="cliente-nombre"></div>
                                                <div style="font-size:.875rem;color:var(--gray-600);" id="cliente-email"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Campo oculto para el ID del cliente --}}
                                <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id') }}">

                                @error('cliente_id')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">
                                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Búsqueda de Vendedor por Cédula --}}
                            <div class="col-md-6 mb-4">
                                <label for="vendedor_cedula" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge-fill text-info"></i> Cédula del Vendedor (Opcional)
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control @error('vendedor_id') is-invalid @enderror"
                                           id="vendedor_cedula"
                                           placeholder="Ingrese cédula del vendedor"
                                           style="border-radius:10px 0 0 10px;padding:.75rem;">
                                    <button class="btn btn-primary" type="button" id="btn-buscar-vendedor"
                                            style="border-radius:0;padding:.75rem 1.25rem;">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <button class="btn btn-warning" type="button" id="btn-limpiar-vendedor"
                                            style="display:none;border-radius:0 10px 10px 0;padding:.75rem 1.25rem;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                {{-- Información del vendedor encontrado --}}
                                <div id="vendedor-info" class="mt-3" style="display: none;">
                                    <div style="background:rgba(59,130,246,0.1);border:2px solid var(--info);border-radius:12px;padding:1rem;">
                                        <div class="d-flex align-items-center">
                                            <div style="width:50px;height:50px;border-radius:50%;background:var(--info);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:1rem;">
                                                <i class="bi bi-person-badge-fill"></i>
                                            </div>
                                            <div style="flex:1;">
                                                <div style="font-weight:700;color:var(--info);margin-bottom:.25rem;">Vendedor encontrado</div>
                                                <div style="font-weight:600;color:var(--gray-900);" id="vendedor-nombre"></div>
                                                <div style="font-size:.875rem;color:var(--gray-600);" id="vendedor-email"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Campo oculto para el ID del vendedor --}}
                                <input type="hidden" id="vendedor_id" name="vendedor_id" value="{{ old('vendedor_id') }}">

                                @error('vendedor_id')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">
                                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Descuento --}}
                            <div class="col-md-6 mb-3">
                                <label for="descuento" class="form-label fw-semibold">
                                    <i class="bi bi-tag-fill text-success"></i> Descuento (COP)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius:10px 0 0 10px;">$</span>
                                    <input type="number"
                                           class="form-control @error('descuento') is-invalid @enderror"
                                           id="descuento"
                                           name="descuento"
                                           value="{{ old('descuento', 0) }}"
                                           min="0"
                                           step="100"
                                           onchange="calcularTotal()"
                                           style="border-radius:0 10px 10px 0;padding:.75rem;">
                                </div>
                                @error('descuento')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Observaciones --}}
                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text-fill text-gray-600"></i> Observaciones
                                </label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Observaciones adicionales del pedido..."
                                          style="border-radius:10px;padding:.75rem;">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div style="color:var(--danger);font-size:.875rem;margin-top:.5rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Productos del Pedido --}}
                <div class="pedido-detail-card fade-in-up animate-delay-1" style="margin-top:1.5rem;">
                    <div class="pedido-detail-header">
                        <i class="bi bi-box-seam"></i>
                        <h3 class="pedido-detail-title">Productos del Pedido</h3>
                    </div>
                    <div class="pedido-detail-body">
                        {{-- Selector de Productos --}}
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-search"></i> Buscar Producto
                                </label>
                                <select class="form-select" id="producto_selector" style="border-radius:10px;padding:.75rem;">
                                    <option value="">Seleccione un producto...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}"
                                                data-nombre="{{ $producto->nombre }}"
                                                data-precio="{{ $producto->precio }}"
                                                data-stock="{{ $producto->stock }}"
                                                data-imagen="{{ $producto->imagen }}">
                                            {{ $producto->nombre }} - ${{ number_format($producto->precio, 0) }} (Stock: {{ $producto->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad_input" min="1" value="1"
                                       style="border-radius:10px;padding:.75rem;">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100" onclick="agregarProducto()"
                                        style="border-radius:10px;padding:.75rem;">
                                    <i class="bi bi-plus-circle"></i> Agregar
                                </button>
                            </div>
                        </div>

                        {{-- Lista de Productos Agregados --}}
                        <div id="productos-container">
                            <div class="pedido-empty-state" id="sin-productos">
                                <div class="pedido-empty-state-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h4 class="pedido-empty-state-title">No hay productos agregados</h4>
                                <p class="pedido-empty-state-text">Busca y agrega productos usando el selector de arriba</p>
                            </div>
                        </div>

                        @error('productos')
                            <div style="color:var(--danger);font-size:.875rem;margin-top:1rem;">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Resumen y Acciones --}}
            <div class="col-lg-4">
                {{-- Resumen del Pedido --}}
                <div class="pedido-detail-card fade-in-up animate-delay-2">
                    <div class="pedido-detail-header">
                        <i class="bi bi-calculator"></i>
                        <h3 class="pedido-detail-title">Resumen del Pedido</h3>
                    </div>
                    <div class="pedido-detail-body">
                        <div class="pedido-info-grid" style="grid-template-columns:1fr;">
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-box-seam"></i> Productos
                                </div>
                                <div class="pedido-info-value" id="cantidad-productos">0</div>
                            </div>
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-cash"></i> Subtotal
                                </div>
                                <div class="pedido-info-value" id="subtotal-display">$0</div>
                            </div>
                            <div class="pedido-info-item">
                                <div class="pedido-info-label">
                                    <i class="bi bi-tag"></i> Descuento
                                </div>
                                <div class="pedido-info-value" id="descuento-display" style="color:var(--success);">$0</div>
                            </div>
                            <div class="pedido-info-item" style="background:linear-gradient(135deg,var(--wine),var(--wine-dark));border:none;">
                                <div class="pedido-info-label" style="color:rgba(255,255,255,0.9);">
                                    <i class="bi bi-cash-stack"></i> Total Final
                                </div>
                                <div class="pedido-info-value" id="total-final" style="color:#fff;font-size:1.75rem;">$0</div>
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
                        <button type="submit" class="pedido-btn pedido-btn-primary" id="btn-crear" disabled style="width:100%;margin-bottom:.75rem;justify-content:center;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Crear Pedido</span>
                        </button>
                        <a href="{{ route('admin.pedidos.index') }}" class="pedido-btn pedido-btn-outline" style="width:100%;justify-content:center;">
                            <i class="bi bi-x-circle"></i>
                            <span>Cancelar</span>
                        </a>
                    </div>
                </div>

                {{-- Ayuda --}}
                <div class="pedido-detail-card fade-in-up animate-delay-3" style="margin-top:1.5rem;background:rgba(59,130,246,0.05);border-color:var(--info);">
                    <div class="pedido-detail-body" style="padding:1.25rem;">
                        <div style="display:flex;gap:1rem;">
                            <div style="font-size:2rem;color:var(--info);">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <div style="flex:1;">
                                <h6 style="font-weight:700;color:var(--info);margin-bottom:.75rem;">Instrucciones</h6>
                                <ul style="font-size:.875rem;color:var(--gray-700);margin:0;padding-left:1.25rem;">
                                    <li style="margin-bottom:.5rem;">Busca al cliente por cédula (obligatorio)</li>
                                    <li style="margin-bottom:.5rem;">Opcionalmente asigna un vendedor</li>
                                    <li style="margin-bottom:.5rem;">Agrega productos al pedido</li>
                                    <li style="margin-bottom:.5rem;">Verifica el resumen antes de crear</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Variables globales para búsquedas AJAX --}}
<script>
window.searchUrls = {
    cliente: '{{ route("admin.pedidos.search-cliente") }}',
    vendedor: '{{ route("admin.pedidos.search-vendedor") }}'
};
</script>

{{-- Módulo JavaScript principal --}}
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

{{-- Funcionalidad específica de Create --}}
<script src="{{ asset('js/admin/pedidos-create.js') }}?v={{ time() }}"></script>

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
