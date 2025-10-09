@extends('layouts.admin')

@section('title', '- Detalles del Producto')
@section('page-title', 'Detalles del Producto')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/productos-modern.css') }}?v={{ filemtime(public_path('css/admin/productos-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="products-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="product-image-wrapper" style="width:80px;height:80px">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-image">
                    @else
                        <div class="product-placeholder"><i class="bi bi-image fs-3"></i></div>
                    @endif
                </div>
                <div>
                    <h1 class="products-title">{{ $producto->nombre }}</h1>
                    <p class="products-subtitle">Información detallada del producto</p>
                </div>
            </div>
            <div class="products-header-actions">
                <a href="{{ route('admin.productos.edit',$producto) }}" class="products-btn products-btn-white">
                    <i class="bi bi-pencil"></i>Editar
                </a>
                <a href="{{ route('admin.productos.index') }}" class="products-btn products-btn-white">
                    <i class="bi bi-arrow-left"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Información Principal --}}
            <div class="role-info-card fade-in-up">
                <div class="role-info-card-header">
                    <i class="bi bi-info-circle"></i>
                    <h3 class="role-info-card-title">Información del Producto</h3>
                </div>
                <div class="role-info-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Nombre:</span>
                                <span class="role-info-value">{{ $producto->nombre }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Categoría:</span>
                                <span class="product-badge product-badge-category">
                                    <i class="bi bi-tag"></i>{{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Precio:</span>
                                <span class="product-badge product-badge-price" style="font-size:1.25rem">${{ number_format($producto->precio,0,',','.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Stock:</span>
                                @if($producto->stock<=5)
                                    <span class="product-badge product-badge-stock-low"><i class="bi bi-exclamation-circle"></i>{{ $producto->stock }}</span>
                                @elseif($producto->stock<=10)
                                    <span class="product-badge product-badge-stock-medium"><i class="bi bi-dash-circle"></i>{{ $producto->stock }}</span>
                                @else
                                    <span class="product-badge product-badge-stock-high"><i class="bi bi-check-circle"></i>{{ $producto->stock }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Estado:</span>
                                @if($producto->activo)
                                    <span class="product-badge product-badge-active"><i class="bi bi-check-circle-fill"></i>Activo</span>
                                @else
                                    <span class="product-badge product-badge-inactive"><i class="bi bi-x-circle-fill"></i>Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="role-info-item">
                                <span class="role-info-label">Creado:</span>
                                <span class="role-info-value">{{ $producto->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        @if($producto->descripcion)
                        <div class="col-12">
                            <div class="role-info-item" style="flex-direction:column;align-items:flex-start">
                                <span class="role-info-label mb-2">Descripción:</span>
                                <div style="background:var(--gray-50);padding:1rem;border-radius:10px;width:100%">{{ $producto->descripcion }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Imagen --}}
            <div class="role-info-card fade-in-up animate-delay-1">
                <div class="role-info-card-header">
                    <i class="bi bi-image"></i>
                    <h3 class="role-info-card-title">Imagen del Producto</h3>
                </div>
                <div class="role-info-card-body text-center">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" style="width:100%;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1)">
                    @else
                        <div style="background:var(--gray-100);padding:3rem;border-radius:12px">
                            <i class="bi bi-image" style="font-size:4rem;color:var(--gray-400)"></i>
                            <p class="text-muted mt-2">Sin imagen</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones --}}
            <div class="role-info-card fade-in-up animate-delay-2">
                <div class="role-info-card-header">
                    <i class="bi bi-lightning"></i>
                    <h3 class="role-info-card-title">Acciones Rápidas</h3>
                </div>
                <div class="role-info-card-body">
                    <a href="{{ route('admin.productos.edit',$producto) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i>Editar Producto
                    </a>
                    <button type="button" class="btn btn-{{ $producto->activo?'secondary':'success' }} w-100 mb-2" data-action="toggle-status" data-product-id="{{ $producto->_id }}" data-active="{{ $producto->activo?'true':'false' }}" data-product-name="{{ $producto->nombre }}">
                        <i class="bi bi-{{ $producto->activo?'pause':'play' }}"></i>{{ $producto->activo?'Desactivar':'Activar' }}
                    </button>
                    <button type="button" class="btn btn-danger w-100 mb-2" data-action="delete-product" data-product-id="{{ $producto->_id }}" data-product-name="{{ $producto->nombre }}" data-product-image="{{ $producto->imagen?asset('storage/'.$producto->imagen):'' }}">
                        <i class="bi bi-trash"></i>Eliminar
                    </button>
                    <hr>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-list"></i>Ver Todos
                    </a>

                    <form id="toggle-form-{{ $producto->_id }}" action="{{ route('admin.productos.toggle-status',$producto) }}" method="POST" class="d-none">@csrf @method('PATCH')</form>
                    <form id="delete-form-{{ $producto->_id }}" action="{{ route('admin.productos.destroy',$producto) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/productos-modern.js') }}?v={{ filemtime(public_path('js/admin/productos-modern.js')) }}"></script>
@endpush
