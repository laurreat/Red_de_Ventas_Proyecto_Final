@extends('layouts.admin')

@section('title', '- Productos')
@section('page-title', 'Gestión de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">Administra el catalogo completo de productos</h4>
                        </div>

                        <div>
                            <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Nuevo Producto
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Métricas de Productos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-boxes fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_productos'] }}</h3>
                    <p class="text-muted mb-0 small">Total Productos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['productos_activos'] }}</h3>
                    <p class="text-muted mb-0 small">Productos Activos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['productos_stock_bajo'] }}</h3>
                    <p class="text-muted mb-0 small">Stock Bajo</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-tags fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_categorias'] }}</h3>
                    <p class="text-muted mb-0 small">Categorías</p>
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
                        Filtros de Busqueda
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.productos.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Buscar producto</label>
                                <input type="text" class="form-control" name="buscar"
                                       placeholder="Nombre o descripcion..."
                                       value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="categoria">
                                    <option value="">Todas las categorias</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}"
                                                {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
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
            </div>
        </div>
    </div>

    <!-- Lista de Productos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Lista de Productos ({{ $productos->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($productos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoria</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos as $producto)
                                    <tr data-product-id="{{ $producto->_id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen) }}"
                                                             alt="{{ $producto->nombre }}"
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
                                                    <div class="fw-medium producto-nombre">{{ $producto->nombre }}</div>
                                                    @if($producto->descripcion)
                                                        <small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info categoria-badge">{{ $producto->categoria->nombre }}</span>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($producto->precio, 0) }}</strong>
                                        </td>
                                        <td>
                                            @if($producto->stock <= 5)
                                                <span class="badge bg-danger">{{ $producto->stock }}</span>
                                            @elseif($producto->stock <= 10)
                                                <span class="badge bg-warning">{{ $producto->stock }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $producto->stock }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($producto->activo)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.productos.show', $producto) }}"
                                                   class="btn btn-sm btn-outline-info" title="Ver">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.productos.edit', $producto) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm {{ $producto->activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}"
                                                        onclick="event.preventDefault(); toggleStatus('{{ $producto->_id }}'); return false;">
                                                    <i class="bi bi-{{ $producto->activo ? 'pause' : 'play' }}"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar"
                                                        onclick="event.preventDefault(); confirmDelete('{{ $producto->_id }}'); return false;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Formularios ocultos -->
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="p-4">
                            {{ $productos->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay productos</h4>
                            <p class="text-muted">No se encontraron productos que coincidan con los filtros.</p>
                            <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Crear primer producto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Modales de confirmación para esta página --}}
@include('admin.partials.modals')

@push('scripts')
<script src="{{ asset('js/admin/productos-management.js') }}"></script>
@endpush