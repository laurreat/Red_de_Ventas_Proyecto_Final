@extends('layouts.admin')

@section('title', '- Ver Producto')
@section('page-title', 'Detalles del Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">{{ $producto->nombre }}</h4>
                            <p class="text-white-50 mb-0">Informacion detallada del producto</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-light me-2">
                                <i class="bi bi-arrow-left me-1"></i>
                                Volver
                            </a>
                            <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i>
                                Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Producto -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-info-circle me-2"></i>
                        Informacion del Producto
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Nombre</label>
                            <p class="fs-5 fw-medium">{{ $producto->nombre }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Categoria</label>
                            <p class="fs-6">
                                <span class="badge bg-info fs-6">{{ $producto->categoria->nombre }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Precio</label>
                            <p class="fs-4 fw-bold text-success">${{ number_format($producto->precio, 0) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Stock Disponible</label>
                            <p class="fs-5">
                                @if($producto->stock <= 5)
                                    <span class="badge bg-danger fs-6">{{ $producto->stock }} unidades</span>
                                @elseif($producto->stock <= 10)
                                    <span class="badge bg-warning fs-6">{{ $producto->stock }} unidades</span>
                                @else
                                    <span class="badge bg-success fs-6">{{ $producto->stock }} unidades</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Estado</label>
                            <p class="fs-6">
                                @if($producto->activo)
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-pause-circle me-1"></i>
                                        Inactivo
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Fecha de Creacion</label>
                            <p class="fs-6">{{ $producto->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($producto->descripcion)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-medium text-muted">Descripcion</label>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-0">{{ $producto->descripcion }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Imagen del Producto y Acciones -->
        <div class="col-lg-4 mb-4">
            <!-- Imagen del Producto -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-image me-2"></i>
                        Imagen del Producto
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <div class="text-center">
                                <i class="bi bi-image fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Sin imagen</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>
                            Editar Producto
                        </a>

                        <button type="button"
                                class="btn {{ $producto->activo ? 'btn-warning' : 'btn-success' }}"
                                onclick="toggleStatus('{{ $producto->id }}')">
                            <i class="bi bi-{{ $producto->activo ? 'pause' : 'play' }} me-2"></i>
                            {{ $producto->activo ? 'Desactivar' : 'Activar' }} Producto
                        </button>

                        <button type="button"
                                class="btn btn-danger"
                                onclick="confirmDelete('{{ $producto->id }}')">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Producto
                        </button>

                        <hr class="my-3">

                        <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-2"></i>
                            Ver Todos los Productos
                        </a>
                    </div>

                    <!-- Formularios ocultos -->
                    <form id="toggle-form-{{ $producto->id }}"
                          action="{{ route('admin.productos.toggle-status', $producto) }}"
                          method="POST" class="d-none">
                        @csrf
                        @method('PATCH')
                    </form>

                    <form id="delete-form-{{ $producto->id }}"
                          action="{{ route('admin.productos.destroy', $producto) }}"
                          method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Producto -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bar-chart me-2"></i>
                        Informacion Adicional
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-calendar-plus fs-2 text-primary mb-2"></i>
                                <h6 class="fw-semibold mb-1">Creado</h6>
                                <p class="text-muted small mb-0">{{ $producto->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-calendar-check fs-2 text-info mb-2"></i>
                                <h6 class="fw-semibold mb-1">Actualizado</h6>
                                <p class="text-muted small mb-0">{{ $producto->updated_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-tag fs-2 text-success mb-2"></i>
                                <h6 class="fw-semibold mb-1">ID del Producto</h6>
                                <p class="text-muted small mb-0">#{{ $producto->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-boxes fs-2 text-warning mb-2"></i>
                                <h6 class="fw-semibold mb-1">Categoría ID</h6>
                                <p class="text-muted small mb-0">#{{ $producto->categoria_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Los scripts JavaScript se cargan desde admin-functions.js --}}
@endsection