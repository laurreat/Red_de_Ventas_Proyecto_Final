@extends('layouts.admin')

@section('title', '- Editar Producto')
@section('page-title', 'Editar Producto')

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
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar Producto
                </h1>
                <p class="products-subtitle">
                    Modificando: <strong>{{ $producto->nombre }}</strong>
                </p>
            </div>
            <div class="products-header-actions">
                <a href="{{ route('admin.productos.show', $producto) }}" class="products-btn products-btn-white">
                    <i class="bi bi-eye"></i>
                    Ver
                </a>
                <a href="{{ route('admin.productos.index') }}" class="products-btn products-btn-white">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.productos.update', $producto) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Columna Principal - Información --}}
            <div class="col-lg-8">
                <div class="role-info-card fade-in-up">
                    <div class="role-info-card-header">
                        <i class="bi bi-info-circle"></i>
                        <h3 class="role-info-card-title">Información del Producto</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">
                                    Nombre del Producto <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       name="nombre"
                                       value="{{ old('nombre', $producto->nombre) }}"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select class="form-select product-category-select @error('categoria_id') is-invalid @enderror"
                                        name="categoria_id"
                                        required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->_id }}"
                                                {{ old('categoria_id', $producto->categoria_id) == $categoria->_id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                          name="descripcion"
                                          rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Precio <span class="text-danger">*</span>
                                </label>
                                <div class="product-input-group input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('precio') is-invalid @enderror"
                                           name="precio"
                                           value="{{ old('precio', $producto->precio) }}"
                                           min="0"
                                           step="100"
                                           required>
                                </div>
                                @error('precio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Stock <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       name="stock"
                                       value="{{ old('stock', $producto->stock) }}"
                                       min="0"
                                       required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Lateral - Imagen y Configuración --}}
            <div class="col-lg-4">
                {{-- Card de Imagen --}}
                <div class="role-info-card fade-in-up animate-delay-1">
                    <div class="role-info-card-header">
                        <i class="bi bi-image"></i>
                        <h3 class="role-info-card-title">Imagen del Producto</h3>
                    </div>
                    <div class="role-info-card-body">
                        @if($producto->imagen)
                            <div class="text-center mb-3">
                                <div class="product-preview-wrapper">
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         class="product-preview-image"
                                         alt="{{ $producto->nombre }}">
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-check-circle"></i> Imagen actual
                                </small>
                            </div>
                        @endif

                        <label class="form-label">Cambiar Imagen</label>
                        <input type="file"
                               class="form-control @error('imagen') is-invalid @enderror"
                               name="imagen"
                               accept="image/*">
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> JPG, PNG o WebP. Máx: 2MB
                        </small>
                        @error('imagen')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Preview de Nueva Imagen --}}
                        <div id="imagePreview" class="product-preview-wrapper mt-3" style="display:none;">
                            <img id="preview" src="" class="product-preview-image" alt="Preview">
                        </div>

                        @if(!$producto->imagen)
                            <div id="placeholderImage" class="mt-3">
                                <div class="product-preview-placeholder">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p class="mb-0">Sin imagen</p>
                                    <small class="text-muted">Selecciona una imagen</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card de Configuración --}}
                <div class="role-info-card fade-in-up animate-delay-2">
                    <div class="role-info-card-header">
                        <i class="bi bi-gear"></i>
                        <h3 class="role-info-card-title">Configuración</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="activo"
                                   id="activo"
                                   {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Producto Activo
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Desactiva para ocultar del catálogo
                        </small>
                    </div>
                </div>

                {{-- Card de Información --}}
                <div class="role-info-card fade-in-up animate-delay-2">
                    <div class="role-info-card-header">
                        <i class="bi bi-clock-history"></i>
                        <h3 class="role-info-card-title">Información del Sistema</h3>
                    </div>
                    <div class="role-info-card-body">
                        <div class="role-info-item">
                            <span class="role-info-label">
                                <i class="bi bi-calendar-plus"></i> Creado:
                            </span>
                            <span class="role-info-value">
                                {{ $producto->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="role-info-item">
                            <span class="role-info-label">
                                <i class="bi bi-calendar-check"></i> Actualizado:
                            </span>
                            <span class="role-info-value">
                                {{ $producto->updated_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card de Acciones --}}
                <div class="role-info-card fade-in-up animate-delay-3">
                    <div class="role-info-card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2 product-btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Actualizar Producto
                        </button>
                        <a href="{{ route('admin.productos.show', $producto) }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="bi bi-eye"></i>
                            Ver Detalles
                        </a>
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/productos-modern.js') }}?v={{ filemtime(public_path('js/admin/productos-modern.js')) }}"></script>
@endpush
