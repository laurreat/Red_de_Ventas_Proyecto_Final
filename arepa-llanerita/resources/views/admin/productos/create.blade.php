@extends('layouts.admin')

@section('title', '- Crear Producto')
@section('page-title', 'Crear Nuevo Producto')

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
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Nuevo Producto
                </h1>
                <p class="products-subtitle">Agregar producto al catálogo</p>
            </div>
            <div class="products-header-actions">
                <a href="{{ route('admin.productos.index') }}" class="products-btn products-btn-white">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
        @csrf
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
                                       value="{{ old('nombre') }}"
                                       required
                                       placeholder="Ej: Arepa Reina Pepiada">
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
                                                {{ old('categoria_id') == $categoria->_id ? 'selected' : '' }}>
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
                                          rows="4"
                                          placeholder="Describe el producto, sus características, ingredientes...">{{ old('descripcion') }}</textarea>
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
                                           value="{{ old('precio') }}"
                                           min="0"
                                           step="100"
                                           required
                                           placeholder="15000">
                                </div>
                                @error('precio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Stock Inicial <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       name="stock"
                                       value="{{ old('stock', 0) }}"
                                       min="0"
                                       required
                                       placeholder="100">
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
                        <label class="form-label">Subir Imagen</label>
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

                        {{-- Preview de Imagen --}}
                        <div id="imagePreview" class="product-preview-wrapper mt-3" style="display:none;">
                            <img id="preview" src="" class="product-preview-image" alt="Preview">
                        </div>

                        {{-- Placeholder --}}
                        <div id="placeholderImage" class="mt-3">
                            <div class="product-preview-placeholder">
                                <i class="bi bi-cloud-upload"></i>
                                <p class="mb-0">Vista previa de la imagen</p>
                                <small class="text-muted">Arrastra o selecciona una imagen</small>
                            </div>
                        </div>
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
                                   {{ old('activo', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Producto Activo
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Los productos activos aparecen en el catálogo
                        </small>
                    </div>
                </div>

                {{-- Card de Acciones --}}
                <div class="role-info-card fade-in-up animate-delay-3">
                    <div class="role-info-card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2 product-btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Crear Producto
                        </button>
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
