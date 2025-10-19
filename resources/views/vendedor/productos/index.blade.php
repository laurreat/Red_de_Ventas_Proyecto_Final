@extends('layouts.vendedor')

@section('title', 'Catálogo de Productos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-professional.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-professional.css')) }}">
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
@endpush

@section('content')
<!-- Header Hero Mejorado -->
<div class="pedido-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="pedidos-header-icon-badge">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1 class="pedidos-header-title">
                Catálogo de Productos
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-collection me-2"></i>
                Consulta disponibilidad, precios y detalles de todos los productos
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                <button onclick="window.print()" class="pedidos-btn-secondary">
                    <i class="bi bi-printer"></i>
                    <span>Imprimir</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Grid Mejorado -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.1s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-total">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="pedidos-stat-trend positive">
                    <i class="bi bi-arrow-up"></i>
                    <span>5%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Total Productos</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-collection"></i> En catálogo
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.2s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-confirmed">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="pedidos-stat-trend positive">
                    <i class="bi bi-arrow-up"></i>
                    <span>12%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['activos'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Activos</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-check2"></i> Disponibles
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.3s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-pending">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="pedidos-stat-trend neutral">
                    <i class="bi bi-dash"></i>
                    <span>0%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['bajo_stock'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Bajo Stock</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-arrow-down-circle"></i> Requieren atención
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.4s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="pedidos-stat-trend negative">
                    <i class="bi bi-arrow-down"></i>
                    <span>3%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['sin_stock'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Sin Stock</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-inbox"></i> Agotados
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros Mejorados con Diseño Card -->
<div class="pedidos-filters-card fade-in-up animate-delay-1">
    <div class="pedidos-filters-header">
        <h3 class="pedidos-filters-title">
            <i class="bi bi-funnel"></i>
            Filtros de Búsqueda
        </h3>
        <button type="button" class="pedidos-filters-reset" onclick="document.getElementById('productos-filter-form').reset()">
            <i class="bi bi-arrow-counterclockwise"></i>
            Limpiar
        </button>
    </div>
    <form id="productos-filter-form" method="GET" action="{{ route('vendedor.productos.index') }}">
        <div class="pedidos-filter-grid">
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-search"></i>
                    Buscar Producto
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <input type="search" name="buscar" class="pedidos-filter-input" 
                           placeholder="Nombre o descripción..." value="{{ request('buscar') }}">
                    <i class="bi bi-search pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-folder"></i>
                    Categoría
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <select name="categoria" class="pedidos-filter-select">
                        <option value="">📁 Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->_id }}" {{ request('categoria') == $categoria->_id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-toggle-on"></i>
                    Estado
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <select name="estado" class="pedidos-filter-select">
                        <option value="">⚡ Todos los estados</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>✅ Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>❌ Inactivos</option>
                    </select>
                    <i class="bi bi-chevron-down pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-bar-chart"></i>
                    Stock
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <select name="stock" class="pedidos-filter-select">
                        <option value="">📊 Todos</option>
                        <option value="bajo" {{ request('stock') == 'bajo' ? 'selected' : '' }}>⚠️ Bajo Stock</option>
                        <option value="sin_stock" {{ request('stock') == 'sin_stock' ? 'selected' : '' }}>🚫 Sin Stock</option>
                    </select>
                    <i class="bi bi-chevron-down pedidos-filter-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="pedidos-filter-actions">
            <button type="submit" class="pedidos-btn-filter">
                <i class="bi bi-funnel-fill"></i>
                Aplicar Filtros
            </button>
            <button type="button" class="pedidos-btn-filter-secondary" onclick="window.location.href='{{ route('vendedor.productos.index') }}'">
                <i class="bi bi-x-circle"></i>
                Limpiar Todo
            </button>
        </div>
    </form>
</div>

<!-- Grid de Productos Mejorado -->
<div class="pedidos-table-wrapper fade-in-up animate-delay-2">
    @if($productos->count() > 0)
    <div class="pedidos-table-header">
        <div class="pedidos-table-header-left">
            <h3 class="pedidos-table-title">
                <i class="bi bi-box-seam"></i>
                Catálogo de Productos
            </h3>
            <span class="pedidos-table-count">{{ $productos->total() }} productos encontrados</span>
        </div>
        <div class="pedidos-table-header-right">
            <div class="pedidos-view-options">
                <button class="pedidos-view-btn" data-view="table">
                    <i class="bi bi-table"></i>
                </button>
                <button class="pedidos-view-btn active" data-view="grid">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="productos-grid-container">
        @foreach($productos as $index => $producto)
        <div class="producto-card-modern fade-in-up" style="animation-delay: {{ $index * 0.05 }}s">
            <!-- Imagen del Producto -->
            <div class="producto-card-image-wrapper">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                         alt="{{ $producto->nombre }}" 
                         class="producto-card-image"
                         loading="lazy"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'producto-card-image producto-card-placeholder\'><i class=\'bi bi-box-seam\'></i></div>';">
                @else
                    <div class="producto-card-image producto-card-placeholder">
                        <i class="bi bi-box-seam"></i>
                    </div>
                @endif
                
                <!-- Badge de estado -->
                @if($producto->stock == 0)
                    <span class="producto-status-badge badge-agotado">
                        <i class="bi bi-x-circle-fill"></i>
                        Agotado
                    </span>
                @elseif($producto->stock <= ($producto->stock_minimo ?? 5))
                    <span class="producto-status-badge badge-bajo-stock">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Bajo Stock
                    </span>
                @elseif($producto->activo)
                    <span class="producto-status-badge badge-disponible">
                        <i class="bi bi-check-circle-fill"></i>
                        Disponible
                    </span>
                @else
                    <span class="producto-status-badge badge-inactivo">
                        <i class="bi bi-dash-circle-fill"></i>
                        Inactivo
                    </span>
                @endif
            </div>
            
            <!-- Contenido del Producto -->
            <div class="producto-card-body">
                @if(isset($producto->categoria_data['nombre']))
                    <div class="producto-card-category">
                        <i class="bi bi-tag-fill"></i>
                        {{ $producto->categoria_data['nombre'] }}
                    </div>
                @endif
                
                <h3 class="producto-card-title">{{ $producto->nombre }}</h3>
                
                @if($producto->descripcion)
                    <p class="producto-card-description">
                        {{ Str::limit($producto->descripcion, 80) }}
                    </p>
                @endif
                
                <div class="producto-card-info-grid">
                    <div class="producto-info-item">
                        <div class="producto-info-label">
                            <i class="bi bi-cash"></i>
                            Precio
                        </div>
                        <div class="producto-info-value">
                            ${{ number_format(to_float($producto->precio), 0) }}
                        </div>
                    </div>
                    
                    <div class="producto-info-item">
                        <div class="producto-info-label">
                            <i class="bi bi-box"></i>
                            Stock
                        </div>
                        <div class="producto-info-value {{ $producto->stock <= ($producto->stock_minimo ?? 5) ? 'text-danger' : '' }}">
                            {{ $producto->stock }} unid.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Acciones del Producto -->
            <div class="producto-card-footer">
                <a href="{{ route('vendedor.productos.show', $producto->_id) }}" 
                   class="producto-card-btn producto-card-btn-primary">
                    <i class="bi bi-eye-fill"></i>
                    Ver Detalles
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación Mejorada -->
    @if($productos->hasPages())
    <div class="pedidos-pagination-wrapper">
        <div class="pedidos-pagination-info">
            Mostrando <strong>{{ $productos->firstItem() }}</strong> a <strong>{{ $productos->lastItem() }}</strong> de <strong>{{ $productos->total() }}</strong> productos
        </div>
        <nav class="pedidos-pagination" aria-label="Navegación de páginas">
            <ul class="pagination-list">
                {{-- Previous Page Link --}}
                @if ($productos->onFirstPage())
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link pagination-arrow">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a class="pagination-link pagination-arrow" href="{{ $productos->previousPageUrl() }}" rel="prev" aria-label="Página anterior">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $start = max($productos->currentPage() - 2, 1);
                    $end = min($start + 4, $productos->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @if($start > 1)
                    <li class="pagination-item">
                        <a class="pagination-link" href="{{ $productos->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="pagination-item disabled">
                            <span class="pagination-link pagination-dots">...</span>
                        </li>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $productos->currentPage())
                        <li class="pagination-item active" aria-current="page">
                            <span class="pagination-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a class="pagination-link" href="{{ $productos->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                @if($end < $productos->lastPage())
                    @if($end < $productos->lastPage() - 1)
                        <li class="pagination-item disabled">
                            <span class="pagination-link pagination-dots">...</span>
                        </li>
                    @endif
                    <li class="pagination-item">
                        <a class="pagination-link" href="{{ $productos->url($productos->lastPage()) }}">{{ $productos->lastPage() }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($productos->hasMorePages())
                    <li class="pagination-item">
                        <a class="pagination-link pagination-arrow" href="{{ $productos->nextPageUrl() }}" rel="next" aria-label="Página siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link pagination-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
    @else
    <!-- Empty State Mejorado -->
    <div class="pedidos-empty-state">
        <div class="pedidos-empty-illustration">
            <i class="bi bi-box-seam"></i>
        </div>
        <h3 class="pedidos-empty-title">No se encontraron productos</h3>
        <p class="pedidos-empty-message">
            @if(request()->hasAny(['buscar', 'categoria', 'estado', 'stock']))
                No hay productos que coincidan con los filtros seleccionados. <br>
                Intenta ajustar los criterios de búsqueda.
            @else
                No hay productos disponibles en el catálogo en este momento.
            @endif
        </p>
        <button onclick="window.location.href='{{ route('vendedor.productos.index') }}'" class="pedidos-btn-primary pedidos-btn-lg">
            <i class="bi bi-arrow-counterclockwise"></i>
            Restablecer Filtros
        </button>
    </div>
    @endif
</div>

<!-- Estilos adicionales para el grid de productos -->
<style>
/* Grid de Productos */
.productos-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* Paginación - Estilos limpios y sin conflictos */
.pedidos-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.75rem;
    border-top: 2px solid #f3f4f6;
    background: #f9fafb;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
    border-radius: 0 0 16px 16px;
}

.pedidos-pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.pedidos-pagination-info strong {
    color: #111827;
    font-weight: 600;
}

.pedidos-pagination {
    display: flex;
}

.pedidos-pagination nav {
    display: flex;
}

.pagination-list {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
    align-items: center;
}

.pagination-item {
    display: inline-block;
}

.pagination-link {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0.5rem 0.75rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    color: #374151;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s ease;
    gap: 0.375rem;
}

.pagination-link:hover {
    background: #722F37;
    border-color: #722F37;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.pagination-item.active .pagination-link {
    background: linear-gradient(135deg, #722F37 0%, #5a252c 100%);
    border-color: #722F37;
    color: white;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.pagination-item.disabled .pagination-link {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
    transform: none;
}

.pagination-item.disabled .pagination-link:hover {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #9ca3af;
    transform: none;
    box-shadow: none;
}

.pagination-arrow {
    min-width: 40px !important;
    width: 40px !important;
    padding: 0.5rem !important;
}

.pagination-arrow i {
    font-size: 1rem;
}

.pagination-arrow-text {
    display: none;
}

.pagination-dots {
    background: transparent !important;
    border: none !important;
    color: #9ca3af;
    cursor: default;
    min-width: 30px !important;
}

.pagination-dots:hover {
    background: transparent !important;
    border: none !important;
    color: #9ca3af;
    transform: none !important;
    box-shadow: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    .pedidos-pagination-wrapper {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

@media (max-width: 640px) {
    .pagination-link {
        min-width: 36px;
        height: 36px;
        padding: 0.375rem;
        font-size: 0.875rem;
    }
    
    .pagination-arrow {
        min-width: 36px !important;
        width: 36px !important;
    }
}

.producto-card-modern {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
}

.producto-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(114, 47, 55, 0.15);
}

.producto-card-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 75%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    overflow: hidden;
}

.producto-card-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.producto-card-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #dee2e6;
}

.producto-status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.badge-disponible {
    background: rgba(16, 185, 129, 0.95);
    color: white;
}

.badge-agotado {
    background: rgba(239, 68, 68, 0.95);
    color: white;
}

.badge-bajo-stock {
    background: rgba(245, 158, 11, 0.95);
    color: white;
}

.badge-inactivo {
    background: rgba(107, 114, 128, 0.95);
    color: white;
}

.producto-card-body {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.producto-card-category {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #722F37;
    background: rgba(114, 47, 55, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    width: fit-content;
}

.producto-card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    line-height: 1.4;
}

.producto-card-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
    margin: 0;
    flex: 1;
}

.producto-card-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: auto;
}

.producto-info-item {
    background: #f9fafb;
    padding: 0.75rem;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.producto-info-label {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.producto-info-value {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

.producto-card-footer {
    padding: 1rem 1.25rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.producto-card-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.producto-card-btn-primary {
    background: linear-gradient(135deg, #722F37 0%, #5a252c 100%);
    color: white;
}

.producto-card-btn-primary:hover {
    background: linear-gradient(135deg, #5a252c 0%, #722F37 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
    color: white;
}

@media (max-width: 768px) {
    .productos-grid-container {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 576px) {
    .productos-grid-container {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

<script>
// Inicializar PedidosManager
window.pedidosManager = new PedidosManager();

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    if (window.pedidosManager) {
        pedidosManager.showToast("{{ session('success') }}", 'success', 3000);
    }
    @endif

    @if(session('error'))
    if (window.pedidosManager) {
        pedidosManager.showToast("{{ session('error') }}", 'error', 5000);
    }
    @endif
    
    // View switcher
    document.querySelectorAll('.pedidos-view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pedidos-view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Aquí puedes agregar lógica para cambiar entre vista tabla y grid
        });
    });
});
</script>
@endpush
