@extends('layouts.admin')

@section('title', '- Crear Pedido')
@section('page-title', 'Crear Nuevo Pedido')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Crear un nuevo pedido para un cliente</p>
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

    <form action="{{ route('admin.pedidos.store') }}" method="POST" id="pedidoForm">
        @csrf

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
                            <!-- Búsqueda de Cliente por Cédula -->
                            <div class="col-md-6 mb-3">
                                <label for="cliente_cedula" class="form-label">Cédula del Cliente *</label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control @error('cliente_id') is-invalid @enderror"
                                           id="cliente_cedula"
                                           placeholder="Ingrese cédula del cliente"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-buscar-cliente">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>

                                <!-- Información del cliente encontrado -->
                                <div id="cliente-info" class="mt-2" style="display: none;">
                                    <div class="card border-success">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-check text-success me-2"></i>
                                                <div>
                                                    <small class="text-success fw-bold">Cliente encontrado:</small>
                                                    <div class="small" id="cliente-nombre"></div>
                                                    <div class="small text-muted" id="cliente-email"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campo oculto para el ID del cliente -->
                                <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id') }}">

                                @error('cliente_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Búsqueda de Vendedor por Cédula -->
                            <div class="col-md-6 mb-3">
                                <label for="vendedor_cedula" class="form-label">Cédula del Vendedor (Opcional)</label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control @error('vendedor_id') is-invalid @enderror"
                                           id="vendedor_cedula"
                                           placeholder="Ingrese cédula del vendedor"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-buscar-vendedor">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" type="button" id="btn-limpiar-vendedor" style="display: none;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>

                                <!-- Información del vendedor encontrado -->
                                <div id="vendedor-info" class="mt-2" style="display: none;">
                                    <div class="card border-info">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-badge text-info me-2"></i>
                                                <div>
                                                    <small class="text-info fw-bold">Vendedor encontrado:</small>
                                                    <div class="small" id="vendedor-nombre"></div>
                                                    <div class="small text-muted" id="vendedor-email"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campo oculto para el ID del vendedor -->
                                <input type="hidden" id="vendedor_id" name="vendedor_id" value="{{ old('vendedor_id') }}">

                                @error('vendedor_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="descuento" class="form-label">Descuento (COP)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('descuento') is-invalid @enderror"
                                           id="descuento"
                                           name="descuento"
                                           value="{{ old('descuento', 0) }}"
                                           min="0"
                                           step="100"
                                           onchange="calcularTotal()">
                                </div>
                                @error('descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Observaciones adicionales del pedido...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-box-seam me-2"></i>
                            Productos del Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Selector de Productos -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Seleccionar Producto</label>
                                <select class="form-select" id="producto_selector">
                                    <option value="">Buscar producto...</option>
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
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad_input" min="1" value="1">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary d-block w-100" onclick="agregarProducto()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Productos Agregados -->
                        <div id="productos-container">
                            <div class="text-center py-4 text-muted" id="sin-productos">
                                <i class="bi bi-box fs-1"></i>
                                <p class="mt-2">No hay productos agregados</p>
                            </div>
                        </div>

                        @error('productos')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
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
                            <span id="cantidad-productos">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal-display">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <span id="descuento-display">$0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Final:</strong>
                            <strong id="total-final" style="color: var(--primary-color); font-size: 1.1em;">$0</strong>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-crear" disabled>
                                <i class="bi bi-check-circle me-1"></i>
                                Crear Pedido
                            </button>
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

{{-- JavaScript específico movido a: public/js/admin/pedidos-create.js --}}
<script>
// Pasar URLs de búsqueda de PHP a JavaScript
window.searchUrls = {
    cliente: '{{ route("admin.pedidos.search-cliente") }}',
    vendedor: '{{ route("admin.pedidos.search-vendedor") }}'
};
</script>
<script src="{{ asset('js/admin/pedidos-create.js') }}"></script>
@endsection