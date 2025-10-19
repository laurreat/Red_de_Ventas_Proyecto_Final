@extends('layouts.admin')

@section('title', '- Gestión de Productos')
@section('page-title', 'Gestión de Productos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/productos-modern.css') }}?v={{ filemtime(public_path('css/admin/productos-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="products-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="products-title">
                    <i class="bi bi-box-seam me-2"></i>
                    Gestión de Productos
                </h1>
                <p class="products-subtitle">
                    Administra el catálogo completo de productos
                </p>
            </div>
            <div class="products-header-actions">
                <a href="{{ route('admin.productos.create') }}" class="products-btn products-btn-white">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo Producto
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="product-stat-card fade-in-up">
                <div class="product-stat-icon" style="background: rgba(114, 47, 55, 0.1); color: #722F37;">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="product-stat-value">{{ $stats['total_productos'] }}</div>
                <div class="product-stat-label">Total de Productos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="product-stat-card fade-in-up animate-delay-1">
                <div class="product-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="product-stat-value">{{ $stats['productos_activos'] }}</div>
                <div class="product-stat-label">Productos Activos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="product-stat-card fade-in-up animate-delay-2">
                <div class="product-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="product-stat-value">{{ $stats['productos_stock_bajo'] }}</div>
                <div class="product-stat-label">Stock Bajo</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="product-stat-card fade-in-up animate-delay-3">
                <div class="product-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="product-stat-value">{{ $stats['total_categorias'] }}</div>
                <div class="product-stat-label">Categorías</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="products-filters fade-in-up">
        <form method="GET" action="{{ route('admin.productos.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar producto</label>
                    <input type="text" class="form-control" name="buscar"
                           placeholder="Nombre o descripción..."
                           value="{{ request('buscar') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="categoria">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                    {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="products-table-container fade-in-up">
        <div class="table-responsive">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr data-product-id="{{ $producto->_id }}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="product-image-wrapper">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}"
                                             alt="{{ $producto->nombre }}"
                                             class="product-image">
                                    @else
                                        <div class="product-placeholder">
                                            <i class="bi bi-image fs-3"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="product-name">{{ $producto->nombre }}</div>
                                    @if($producto->descripcion)
                                        <div class="product-description">{{ Str::limit($producto->descripcion, 60) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="product-badge product-badge-category">
                                <i class="bi bi-tag"></i>
                                {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>

                        <td>
                            <span class="product-badge product-badge-price">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </span>
                        </td>

                        <td>
                            @if($producto->stock <= 5)
                                <span class="product-badge product-badge-stock-low">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $producto->stock }}
                                </span>
                            @elseif($producto->stock <= 10)
                                <span class="product-badge product-badge-stock-medium">
                                    <i class="bi bi-dash-circle"></i>
                                    {{ $producto->stock }}
                                </span>
                            @else
                                <span class="product-badge product-badge-stock-high">
                                    <i class="bi bi-check-circle"></i>
                                    {{ $producto->stock }}
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($producto->activo)
                                <span class="product-badge product-badge-active">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Activo
                                </span>
                            @else
                                <span class="product-badge product-badge-inactive">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="product-actions" style="justify-content: center;">
                                <a href="{{ route('admin.productos.show', $producto) }}"
                                   class="product-action-btn product-action-btn-view"
                                   title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.productos.edit', $producto) }}"
                                   class="product-action-btn product-action-btn-edit"
                                   title="Editar producto">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <button type="button"
                                        class="product-action-btn product-action-btn-toggle"
                                        data-action="toggle-status"
                                        data-product-id="{{ $producto->_id }}"
                                        data-active="{{ $producto->activo ? 'true' : 'false' }}"
                                        data-product-name="{{ $producto->nombre }}"
                                        title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi bi-{{ $producto->activo ? 'pause' : 'play' }}"></i>
                                </button>

                                <button type="button"
                                        class="product-action-btn product-action-btn-delete"
                                        data-action="delete-product"
                                        data-product-id="{{ $producto->_id }}"
                                        data-product-name="{{ $producto->nombre }}"
                                        data-product-image="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : '' }}"
                                        title="Eliminar producto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            {{-- Hidden Forms --}}
                            <form id="toggle-form-{{ $producto->_id }}"
                                  action="{{ route('admin.productos.toggle-status', $producto) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('PATCH')
                            </form>

                            <form id="delete-form-{{ $producto->_id }}"
                                  action="{{ route('admin.productos.destroy', $producto) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 3rem;">
                            <div style="color: var(--gray-500);">
                                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                <p class="mb-2 fw-semibold">No se encontraron productos</p>
                                <p class="mb-3 small">No hay productos que coincidan con los filtros seleccionados</p>
                                <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Crear primer producto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($productos->hasPages())
        <div class="p-4 border-top">
            <div class="d-flex justify-content-center">
                {{ $productos->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/productos-modern.js') }}?v={{ filemtime(public_path('js/admin/productos-modern.js')) }}"></script>
@endpush
