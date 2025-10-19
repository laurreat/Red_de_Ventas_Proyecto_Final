@extends('layouts.vendedor')

@section('title', 'Gestión de Pedidos')

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
                Gestión de Pedidos
            </h1>
            <p class="pedidos-header-subtitle">
                <i class="bi bi-graph-up me-2"></i>
                Administra y supervisa todos tus pedidos de manera eficiente
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="pedidos-header-actions">
                <a href="{{ route('vendedor.pedidos.create') }}" class="pedidos-btn-primary">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nuevo Pedido</span>
                </a>
                <button onclick="pedidosManager.showModal('export')" class="pedidos-btn-secondary">
                    <i class="bi bi-download"></i>
                    <span>Exportar</span>
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
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div class="pedidos-stat-trend positive">
                    <i class="bi bi-arrow-up"></i>
                    <span>12%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Total Pedidos</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-calendar-week"></i> Esta semana
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.2s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-pending">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="pedidos-stat-trend neutral">
                    <i class="bi bi-dash"></i>
                    <span>0%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['pendientes'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Pendientes</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-clock-history"></i> Requieren atención
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.3s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-confirmed">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="pedidos-stat-trend positive">
                    <i class="bi bi-arrow-up"></i>
                    <span>8%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">{{ $stats['confirmados'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Confirmados</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-truck"></i> En proceso
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="pedidos-stat-card fade-in-up" style="animation-delay:0.4s;opacity:0;animation-fill-mode:forwards;">
            <div class="pedidos-stat-header">
                <div class="pedidos-stat-icon stat-sales">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="pedidos-stat-trend positive">
                    <i class="bi bi-arrow-up"></i>
                    <span>15%</span>
                </div>
            </div>
            <div class="pedidos-stat-body">
                <div class="pedidos-stat-value">${{ number_format(to_float($stats['total_ventas'] ?? 0), 0) }}</div>
                <div class="pedidos-stat-label">Total Ventas</div>
                <div class="pedidos-stat-footer">
                    <small class="text-muted">
                        <i class="bi bi-graph-up"></i> Este mes
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
            Filtros Avanzados
        </h3>
        <button type="button" class="pedidos-filters-reset" onclick="document.getElementById('pedidos-filter-form').reset()">
            <i class="bi bi-arrow-counterclockwise"></i>
            Limpiar
        </button>
    </div>
    <form id="pedidos-filter-form" method="GET" action="{{ route('vendedor.pedidos.index') }}">
        <div class="pedidos-filter-grid">
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-filter-circle"></i>
                    Estado del Pedido
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <select name="estado" class="pedidos-filter-select">
                        <option value="">📋 Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                        <option value="preparando" {{ request('estado') == 'preparando' ? 'selected' : '' }}>🔧 Preparando</option>
                        <option value="en_camino" {{ request('estado') == 'en_camino' ? 'selected' : '' }}>🚚 En Camino</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>📦 Entregado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                    </select>
                    <i class="bi bi-chevron-down pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-calendar-event"></i>
                    Fecha Desde
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <input type="date" name="fecha_desde" class="pedidos-filter-input" value="{{ request('fecha_desde') }}">
                    <i class="bi bi-calendar3 pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-calendar-check"></i>
                    Fecha Hasta
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <input type="date" name="fecha_hasta" class="pedidos-filter-input" value="{{ request('fecha_hasta') }}">
                    <i class="bi bi-calendar3 pedidos-filter-icon"></i>
                </div>
            </div>
            
            <div class="pedidos-filter-item">
                <label class="pedidos-filter-label">
                    <i class="bi bi-search"></i>
                    Buscar Cliente
                </label>
                <div class="pedidos-filter-input-wrapper">
                    <input type="text" name="cliente" class="pedidos-filter-input" placeholder="Nombre del cliente..." value="{{ request('cliente') }}">
                    <i class="bi bi-person pedidos-filter-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="pedidos-filter-actions">
            <button type="submit" class="pedidos-btn-filter">
                <i class="bi bi-funnel-fill"></i>
                Aplicar Filtros
            </button>
            <button type="button" class="pedidos-btn-filter-secondary" onclick="window.location.href='{{ route('vendedor.pedidos.index') }}'">
                <i class="bi bi-x-circle"></i>
                Limpiar Todo
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Pedidos Mejorada -->
<div class="pedidos-table-wrapper fade-in-up animate-delay-2">
    @if($pedidos->count() > 0)
    <div class="pedidos-table-header">
        <div class="pedidos-table-header-left">
            <h3 class="pedidos-table-title">
                <i class="bi bi-list-ul"></i>
                Lista de Pedidos
            </h3>
            <span class="pedidos-table-count">{{ $pedidos->total() }} pedidos encontrados</span>
        </div>
        <div class="pedidos-table-header-right">
            <div class="pedidos-view-options">
                <button class="pedidos-view-btn active" data-view="table">
                    <i class="bi bi-table"></i>
                </button>
                <button class="pedidos-view-btn" data-view="grid">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="pedidos-table-container">
        <table class="pedidos-table">
            <thead>
                <tr>
                    <th class="pedidos-th-number">
                        <div class="th-content">
                            <i class="bi bi-hash"></i>
                            N° Pedido
                        </div>
                    </th>
                    <th class="pedidos-th-client">
                        <div class="th-content">
                            <i class="bi bi-person-badge"></i>
                            Cliente
                        </div>
                    </th>
                    <th class="pedidos-th-products">
                        <div class="th-content">
                            <i class="bi bi-cart"></i>
                            Productos
                        </div>
                    </th>
                    <th class="pedidos-th-date">
                        <div class="th-content">
                            <i class="bi bi-calendar3"></i>
                            Fecha
                        </div>
                    </th>
                    <th class="pedidos-th-status">
                        <div class="th-content">
                            <i class="bi bi-flag"></i>
                            Estado
                        </div>
                    </th>
                    <th class="pedidos-th-total">
                        <div class="th-content">
                            <i class="bi bi-cash-coin"></i>
                            Total
                        </div>
                    </th>
                    <th class="pedidos-th-actions">
                        <div class="th-content">
                            <i class="bi bi-gear"></i>
                            Acciones
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $index => $pedido)
                <tr class="pedidos-table-row" style="animation-delay: {{ $index * 0.05 }}s">
                    <td class="pedidos-td-number">
                        <div class="pedidos-order-number">
                            <i class="bi bi-receipt"></i>
                            <strong>{{ $pedido->numero_pedido }}</strong>
                        </div>
                    </td>
                    <td class="pedidos-td-client">
                        <div class="pedidos-client-info">
                            <div class="pedidos-client-avatar">
                                {{ strtoupper(substr($pedido->cliente_data['name'] ?? 'N', 0, 2)) }}
                            </div>
                            <div class="pedidos-client-details">
                                <div class="pedidos-client-name">{{ $pedido->cliente_data['name'] ?? 'N/A' }}</div>
                                <div class="pedidos-client-email">{{ $pedido->cliente_data['email'] ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="pedidos-td-products">
                        <div class="pedidos-products-summary">
                            @php
                                $productos = $pedido->productos ?? [];
                                $totalProductos = is_array($productos) ? count($productos) : 0;
                                $totalItems = 0;
                                if (is_array($productos)) {
                                    foreach ($productos as $prod) {
                                        $totalItems += $prod['cantidad'] ?? 0;
                                    }
                                }
                            @endphp
                            <div class="fw-bold text-wine">
                                <i class="bi bi-box-seam"></i>
                                {{ $totalProductos }} producto{{ $totalProductos != 1 ? 's' : '' }}
                            </div>
                            <small class="text-muted">{{ $totalItems }} unidad{{ $totalItems != 1 ? 'es' : '' }}</small>
                        </div>
                    </td>
                    <td class="pedidos-td-date">
                        <div class="pedidos-date-info">
                            <div class="pedidos-date-main">
                                <i class="bi bi-calendar-day"></i>
                                {{ $pedido->created_at->format('d/m/Y') }}
                            </div>
                            <div class="pedidos-date-time">
                                <i class="bi bi-clock"></i>
                                {{ $pedido->created_at->format('H:i') }}
                            </div>
                        </div>
                    </td>
                    <td class="pedidos-td-status">
                        <span class="pedidos-badge pedidos-badge-{{ $pedido->estado }}">
                            @switch($pedido->estado)
                                @case('pendiente')
                                    <i class="bi bi-hourglass-split"></i>
                                    @break
                                @case('confirmado')
                                    <i class="bi bi-check-circle"></i>
                                    @break
                                @case('preparando')
                                    <i class="bi bi-gear"></i>
                                    @break
                                @case('en_camino')
                                    <i class="bi bi-truck"></i>
                                    @break
                                @case('entregado')
                                    <i class="bi bi-box-seam"></i>
                                    @break
                                @case('cancelado')
                                    <i class="bi bi-x-circle"></i>
                                    @break
                            @endswitch
                            {{ ucfirst($pedido->estado) }}
                        </span>
                    </td>
                    <td class="pedidos-td-total">
                        <div class="pedidos-total-amount">
                            <strong>${{ number_format(to_float($pedido->total_final ?? 0), 0) }}</strong>
                        </div>
                    </td>
                    <td class="pedidos-td-actions">
                        <div class="pedidos-actions-group">
                            <a href="{{ route('vendedor.pedidos.show', $pedido->_id) }}" class="pedidos-action-btn pedidos-action-btn-view" title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($pedido->estado == 'pendiente')
                            <a href="{{ route('vendedor.pedidos.edit', $pedido->_id) }}" class="pedidos-action-btn pedidos-action-btn-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif

                            {{-- Dropdown de más opciones --}}
                            <div class="dropdown d-inline-block">
                                <button class="pedidos-action-btn pedidos-action-btn-more dropdown-toggle"
                                        type="button"
                                        id="dropdownPedido{{ $pedido->_id }}"
                                        data-bs-toggle="dropdown"
                                        data-bs-boundary="viewport"
                                        data-bs-reference="parent"
                                        data-bs-display="static"
                                        aria-expanded="false"
                                        title="Más opciones">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end pedidos-dropdown-portal" 
                                    aria-labelledby="dropdownPedido{{ $pedido->_id }}"
                                    data-popper-placement="bottom-end">
                                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                                    <li>
                                        <button class="dropdown-item"
                                                type="button"
                                                data-action="status-pedido"
                                                data-pedido-id="{{ $pedido->_id }}"
                                                data-numero-pedido="{{ $pedido->numero_pedido }}"
                                                data-cliente-nombre="{{ $pedido->cliente_data['name'] ?? 'Cliente' }}"
                                                data-estado-actual="{{ $pedido->estado }}"
                                                data-estados='{{ json_encode(['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'en_preparacion' => 'En Preparación', 'listo' => 'Listo', 'en_camino' => 'En Camino', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado']) }}'>
                                            <i class="bi bi-arrow-repeat text-info me-2"></i>
                                            Cambiar Estado
                                        </button>
                                    </li>
                                    @endif
                                    @if($pedido->estado != 'entregado')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item text-danger"
                                                type="button"
                                                data-action="delete-pedido"
                                                data-pedido-id="{{ $pedido->_id }}"
                                                data-numero-pedido="{{ $pedido->numero_pedido }}"
                                                data-cliente-nombre="{{ $pedido->cliente_data['name'] ?? 'Cliente' }}"
                                                data-total-final="${{ number_format(to_float($pedido->total_final ?? 0), 0) }}"
                                                data-estado="{{ ucfirst($pedido->estado) }}">
                                            <i class="bi bi-trash me-2"></i>
                                            Eliminar Pedido
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Formularios ocultos --}}
                            <form id="status-form-{{ $pedido->_id }}"
                                  action="{{ route('vendedor.pedidos.update-estado', $pedido->_id) }}"
                                  method="POST"
                                  class="d-none">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" id="estado-{{ $pedido->_id }}">
                            </form>

                            <form id="delete-form-{{ $pedido->_id }}"
                                  action="{{ route('vendedor.pedidos.destroy', $pedido->_id) }}"
                                  method="POST"
                                  class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación Mejorada -->
    <div class="pedidos-pagination-wrapper">
        <div class="pedidos-pagination-info">
            Mostrando <strong>{{ $pedidos->firstItem() }}</strong> a <strong>{{ $pedidos->lastItem() }}</strong> de <strong>{{ $pedidos->total() }}</strong> pedidos
        </div>
        <div class="pedidos-pagination">
            {{ $pedidos->links() }}
        </div>
    </div>
    @else
    <!-- Empty State Mejorado -->
    <div class="pedidos-empty-state">
        <div class="pedidos-empty-illustration">
            <i class="bi bi-inbox"></i>
        </div>
        <h3 class="pedidos-empty-title">No hay pedidos registrados</h3>
        <p class="pedidos-empty-message">
            Aún no tienes pedidos en tu historial. <br>
            Crea tu primer pedido para comenzar a gestionar tus ventas.
        </p>
        <a href="{{ route('vendedor.pedidos.create') }}" class="pedidos-btn-primary pedidos-btn-lg">
            <i class="bi bi-plus-circle-fill"></i>
            Crear Primer Pedido
        </a>
    </div>
    @endif
</div>

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

    // SOLUCIÓN DEFINITIVA: Mover dropdown fuera de la tabla usando portal
    const dropdownPortalContainer = document.createElement('div');
    dropdownPortalContainer.id = 'dropdown-portal-container';
    dropdownPortalContainer.style.cssText = 'position: fixed; top: 0; left: 0; z-index: 9999; pointer-events: none;';
    document.body.appendChild(dropdownPortalContainer);

    // Inicializar dropdowns con Popper.js configurado
    document.querySelectorAll('.pedidos-actions-group .dropdown-toggle').forEach(toggle => {
        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!menu) return;
        
        // Cuando se abre el dropdown
        toggle.addEventListener('show.bs.dropdown', function(e) {
            // Mover el menú al portal
            dropdownPortalContainer.appendChild(menu);
            menu.style.pointerEvents = 'auto';
            
            // Calcular posición
            const rect = toggle.getBoundingClientRect();
            menu.style.position = 'fixed';
            menu.style.zIndex = '9999';
            
            // Usar requestAnimationFrame para asegurar que se aplique después del render
            requestAnimationFrame(() => {
                const menuRect = menu.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const spaceBelow = viewportHeight - rect.bottom;
                
                // Posición vertical
                if (spaceBelow < menuRect.height && rect.top > menuRect.height) {
                    menu.style.top = (rect.top - menuRect.height - 8) + 'px';
                } else {
                    menu.style.top = (rect.bottom + 8) + 'px';
                }
                
                // Posición horizontal (alineado a la derecha)
                const leftPosition = rect.right - menuRect.width;
                if (leftPosition < 10) {
                    menu.style.left = '10px';
                } else if (leftPosition + menuRect.width > window.innerWidth) {
                    menu.style.left = (window.innerWidth - menuRect.width - 10) + 'px';
                } else {
                    menu.style.left = leftPosition + 'px';
                }
                
                // Agregar clase para animación
                menu.classList.add('show');
            });
        });
        
        // Cuando se cierra el dropdown
        toggle.addEventListener('hide.bs.dropdown', function(e) {
            menu.classList.remove('show');
            // Devolver el menú a su contenedor original
            setTimeout(() => {
                if (dropdown && !menu.classList.contains('show')) {
                    dropdown.appendChild(menu);
                    menu.style.position = '';
                    menu.style.top = '';
                    menu.style.left = '';
                    menu.style.pointerEvents = '';
                }
            }, 300);
        });
    });

    // Cerrar dropdown al hacer scroll
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const openDropdown = document.querySelector('.pedidos-actions-group .dropdown.show');
            if (openDropdown) {
                const toggle = openDropdown.querySelector('.dropdown-toggle');
                if (toggle) {
                    // Usar el método de Bootstrap para cerrar
                    const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }
            }
        }, 100);
    }, true);
});
</script>
@endpush
