@extends('layouts.publico')

@section('title', '- ' . $producto->nombre)
@section('page-title', $producto->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Navegación -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <i class="bi bi-house me-1"></i>Inicio
                </a>
            </li>
            <li class="breadcrumb-item">
                <span class="badge bg-info">{{ $producto->categoria->nombre }}</span>
            </li>
            <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
        </ol>
    </nav>

    <!-- Producto Principal -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                         class="card-img-top"
                         style="height: 400px; object-fit: cover;"
                         alt="{{ $producto->nombre }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                         style="height: 400px;">
                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-info fs-6 mb-2">{{ $producto->categoria->nombre }}</span>
                        <h1 class="display-6 fw-bold" style="color: var(--primary-color);">
                            {{ $producto->nombre }}
                        </h1>
                    </div>

                    @if($producto->descripcion)
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-2">Descripción</h5>
                            <p class="text-muted">{{ $producto->descripcion }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="fw-semibold">Precio</h5>
                                <h2 class="text-primary fw-bold">${{ number_format($producto->precio, 0) }}</h2>
                            </div>
                            <div class="col-6">
                                <h5 class="fw-semibold">Disponibilidad</h5>
                                <p class="mb-0">
                                    @if($producto->stock > 10)
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle me-1"></i>En Stock ({{ $producto->stock }})
                                        </span>
                                    @elseif($producto->stock > 0)
                                        <span class="badge bg-warning fs-6">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Últimas unidades ({{ $producto->stock }})
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle me-1"></i>Agotado
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button"
                                class="btn btn-primary btn-lg"
                                onclick="redirectToLogin('{{ $producto->nombre }}')"
                                @if($producto->stock <= 0) disabled @endif>
                            <i class="bi bi-cart-plus me-2"></i>
                            @if($producto->stock <= 0)
                                Producto Agotado
                            @else
                                Comprar Ahora
                            @endif
                        </button>

                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Volver al catálogo
                        </a>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <p class="mb-0 small text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Para realizar una compra necesitas <strong>iniciar sesión</strong> o <strong>crear una cuenta</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($productosRelacionados->count() > 0)
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold mb-4" style="color: var(--primary-color);">
                    <i class="bi bi-grid me-2"></i>Productos Relacionados
                </h3>
            </div>
        </div>

        <div class="row">
            @foreach($productosRelacionados as $relacionado)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-relative">
                            @if($relacionado->imagen)
                                <img src="{{ asset('storage/' . $relacionado->imagen) }}"
                                     class="card-img-top"
                                     style="height: 150px; object-fit: cover;"
                                     alt="{{ $relacionado->nombre }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                     style="height: 150px;">
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold">{{ $relacionado->nombre }}</h6>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">${{ number_format($relacionado->precio, 0) }}</span>
                                    <small class="text-muted">Stock: {{ $relacionado->stock }}</small>
                                </div>
                                <a href="{{ url('catalogo/' . $relacionado->id) }}"
                                   class="btn btn-sm btn-outline-primary w-100">
                                    Ver producto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white text-center py-4">
                    <h4 class="fw-bold mb-3">¿Listo para hacer tu pedido?</h4>
                    <p class="mb-4">Crea tu cuenta y accede a todas las funciones de nuestra plataforma</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-light">
                            <i class="bi bi-person-plus me-2"></i>Registrarse
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function redirectToLogin(productName) {
        if (confirm(`¿Te interesa "${productName}"? Para realizar una compra necesitas iniciar sesión. ¿Deseas continuar?`)) {
            window.location.href = '{{ route("login") }}';
        }
    }
</script>
@endsection