@extends('layouts.publico')

@section('title', '- Catálogo de Productos')
@section('page-title')
    @if(request('categoria'))
        @php
            $categoriaActual = $categorias->firstWhere('id', request('categoria'));
        @endphp
        {{ $categoriaActual ? $categoriaActual->nombre : 'Categoría' }}
    @else
        Catálogo de Productos
    @endif
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <div class="stats-icon" style="background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-grid fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_productos'] }}</h3>
                    <p class="text-muted mb-0 small">Total Productos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <div class="stats-icon" style="background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-tags fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['total_categorias'] }}</h3>
                    <p class="text-muted mb-0 small">Categorías</p>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <div class="stats-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-eye fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['productos_filtrados'] }}</h3>
                    <p class="text-muted mb-0 small">Mostrando Ahora</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="search-section">
                <form method="GET" action="{{ url('/') }}" id="searchForm">
                    <div class="row align-items-end">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-search me-1" style="color: var(--primary-color);"></i>Buscar producto
                            </label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   name="buscar"
                                   id="searchInput"
                                   placeholder="Busca por nombre o descripción del producto..."
                                   value="{{ request('buscar') }}"
                                   autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-2"></i>Buscar Productos
                                </button>
                                @if(request('buscar') || request('categoria'))
                                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar Filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(request('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Productos Grid -->
    @if($productos->count() > 0)
        <div class="row">
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0" style="color: var(--primary-color);">
                        <i class="bi bi-grid me-2"></i>
                        @if(request('categoria'))
                            @php
                                $categoriaActual = $categorias->firstWhere('id', request('categoria'));
                            @endphp
                            Productos en "{{ $categoriaActual ? $categoriaActual->nombre : 'Categoría' }}"
                        @else
                            Todos los Productos
                        @endif
                        <span class="badge bg-light text-dark">{{ $productos->total() }}</span>
                    </h4>
                    <small class="text-muted">
                        Mostrando {{ $productos->count() }} de {{ $productos->total() }} productos
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($productos as $producto)
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card product-card h-100 shadow-sm">
                        <div class="product-image">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}"
                                     class="card-img-top"
                                     style="height: 220px; object-fit: cover;"
                                     alt="{{ $producto->nombre }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                     style="height: 220px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif

                            <!-- Stock Indicator -->
                            @if($producto->stock <= 5)
                                <span class="stock-indicator bg-danger text-white">
                                    @if($producto->stock == 0) Agotado @else Últimas {{ $producto->stock }} @endif
                                </span>
                            @elseif($producto->stock <= 10)
                                <span class="stock-indicator bg-warning text-white">Pocas unidades</span>
                            @endif

                            <!-- Category Badge -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-info">{{ $producto->categoria->nombre }}</span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="card-title fw-bold mb-2">{{ $producto->nombre }}</h5>
                            @if($producto->descripcion)
                                <p class="card-text text-muted small flex-grow-1 mb-3">
                                    {{ Str::limit($producto->descripcion, 85) }}
                                </p>
                            @endif

                            <div class="mt-auto">
                                <!-- Price -->
                                <div class="text-center mb-3">
                                    <span class="price-badge fs-5">
                                        ${{ number_format($producto->precio, 0) }}
                                    </span>
                                </div>

                                <!-- Stock Info -->
                                <div class="text-center mb-3">
                                    <small class="text-muted">
                                        <i class="bi bi-box me-1"></i>{{ $producto->stock }} disponibles
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ url('catalogo/' . $producto->id) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Ver Detalles
                                    </a>
                                    <button type="button"
                                            class="btn btn-primary btn-sm"
                                            onclick="redirectToLogin('{{ $producto->nombre }}')"
                                            @if($producto->stock <= 0) disabled @endif>
                                        <i class="bi bi-cart-plus me-1"></i>
                                        @if($producto->stock <= 0) Agotado @else Comprar Ahora @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $productos->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12 text-center py-5">
                <i class="bi bi-search" style="font-size: 4rem; color: var(--primary-color);"></i>
                <h3 class="mt-3" style="color: var(--primary-color);">No se encontraron productos</h3>
                <p class="text-muted">Intenta ajustar tus filtros de búsqueda</p>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Ver todos los productos
                </a>
            </div>
        </div>
    @endif

    <!-- Call to Action -->
    @if($productos->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                    <div class="card-body text-white text-center py-5">
                        <h3 class="fw-bold mb-3">¿Te gusta lo que ves?</h3>
                        <p class="lead mb-4">Únete a nuestra comunidad y comienza a hacer pedidos hoy mismo</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Crear cuenta
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function redirectToLogin(productName) {
        // Mostrar mensaje personalizado
        if (confirm('Para realizar una compra necesitas iniciar sesión. ¿Deseas continuar?')) {
            window.location.href = '{{ route("login") }}';
        }
    }
</script>
@endsection