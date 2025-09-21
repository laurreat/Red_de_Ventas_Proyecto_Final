@extends('layouts.admin')

@section('title', '- Editar Producto')
@section('page-title', 'Editar Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white mb-1">Modificar información del producto: <strong>{{ $producto->nombre }}</strong></h5>
                        </div>

                        <div>
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-light" >
                                <i class="bi bi-arrow-left me-1"></i>
                                Volver a Productos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Información Básica -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-info-circle me-2"></i>
                            Información del Producto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre"
                                       name="nombre"
                                       value="{{ old('nombre', $producto->nombre) }}"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="categoria_id" class="form-label">Categoría *</label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror"
                                        id="categoria_id"
                                        name="categoria_id"
                                        required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                          id="descripcion"
                                          name="descripcion"
                                          rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="precio" class="form-label">Precio (COP) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('precio') is-invalid @enderror"
                                           id="precio"
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

                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Stock Actual *</label>
                                <input type="number"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       id="stock"
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

            <!-- Imagen y Configuración -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-image me-2"></i>
                            Imagen del Producto
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Imagen Actual -->
                        @if($producto->imagen)
                            <div class="mb-3">
                                <label class="form-label">Imagen Actual</label>
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         alt="{{ $producto->nombre }}"
                                         class="img-fluid rounded"
                                         style="max-height: 200px;"
                                         id="currentImage">
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                {{ $producto->imagen ? 'Cambiar Imagen' : 'Subir Imagen' }}
                            </label>
                            <input type="file"
                                   class="form-control @error('imagen') is-invalid @enderror"
                                   id="imagen"
                                   name="imagen"
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo: 2MB</small>
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview de nueva imagen -->
                        <div id="imagePreview" class="text-center" style="display: none;">
                            <label class="form-label">Vista Previa</label>
                            <img id="preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>

                        @if(!$producto->imagen)
                            <div class="text-center" id="placeholderImage">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <div class="text-muted">
                                        <i class="bi bi-image fs-1"></i>
                                        <p class="mb-0 mt-2">Sin imagen</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estado del Producto -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-gear me-2"></i>
                            Configuración
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="activo"
                                   name="activo"
                                   {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Producto Activo
                            </label>
                            <small class="d-block text-muted">Los productos activos aparecen en el catálogo</small>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                            <i class="bi bi-info-circle me-2"></i>
                            Información
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <small class="text-muted">
                            <strong>Creado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}<br>
                            <strong>Actualizado:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}<br>
                            @if($producto->veces_vendido)
                                <strong>Veces vendido:</strong> {{ $producto->veces_vendido }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar
                            </a>
                            <div>
                                <a href="{{ route('admin.productos.show', $producto) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-1"></i>
                                    Ver Producto
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Actualizar Producto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';

            // Ocultar imagen actual si existe
            const currentImage = document.getElementById('currentImage');
            if (currentImage) {
                currentImage.style.opacity = '0.5';
            }

            // Ocultar placeholder si existe
            const placeholder = document.getElementById('placeholderImage');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection