@extends('layouts.cliente')

@section('title', ' - Mis Pedidos')
@section('header-title', 'Mis Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pedidos-cliente-glassmorphism.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Botón Nuevo Pedido -->
    <div class="mb-4 fade-in-up">
        <a href="{{ route('cliente.pedidos.create') }}" class="pedidos-btn pedidos-btn-white">
            <i class="bi bi-plus-circle"></i>
            Nuevo Pedido
        </a>
    </div>

    <!-- Stats Cards Interactivas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="pedidos-stat-card fade-in-up animate-delay-1">
                <div class="pedidos-stat-icon bg-success mx-auto">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="pedidos-stat-value">{{ $stats['total_pedidos'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Total Pedidos</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="pedidos-stat-card fade-in-up animate-delay-2">
                <div class="pedidos-stat-icon bg-warning mx-auto">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="pedidos-stat-value">{{ $stats['pedidos_pendientes'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Pendientes</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="pedidos-stat-card fade-in-up animate-delay-3">
                <div class="pedidos-stat-icon bg-info mx-auto">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="pedidos-stat-value">{{ $stats['pedidos_confirmados'] ?? 0 }}</div>
                <div class="pedidos-stat-label">En Proceso</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="pedidos-stat-card fade-in-up animate-delay-3">
                <div class="pedidos-stat-icon bg-success mx-auto">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="pedidos-stat-value">{{ $stats['pedidos_entregados'] ?? 0 }}</div>
                <div class="pedidos-stat-label">Entregados</div>
            </div>
        </div>
    </div>

    <!-- Filtros Profesionales -->
    <div class="pedidos-filters fade-in-up">
        <form method="GET" action="{{ route('cliente.pedidos.index') }}" id="filtrosForm">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-search me-1"></i>
                        Buscar
                    </label>
                    <input type="text" 
                           name="busqueda" 
                           class="form-control" 
                           placeholder="Número de pedido, dirección..."
                           value="{{ request('busqueda') }}">
                </div>
                
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-funnel me-1"></i>
                        Estado
                    </label>
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="en_preparacion" {{ request('estado') == 'en_preparacion' ? 'selected' : '' }}>En Preparación</option>
                        <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar me-1"></i>
                        Fecha
                    </label>
                    <input type="date" 
                           name="fecha" 
                           class="form-control" 
                           value="{{ request('fecha') }}">
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Pedidos -->
    <div class="pedidos-list-container fade-in-up">
        @forelse($pedidos as $pedido)
            <div class="pedido-card" data-pedido-id="{{ $pedido->_id }}">
                <div class="row align-items-center">
                    <!-- Información Principal -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pedido-icon">
                                <i class="bi bi-receipt fs-2 text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-primary fs-5">#{{ $pedido->numero_pedido }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2 mb-3 mb-md-0">
                        @php
                            $estadosConfig = [
                                'pendiente' => ['class' => 'warning', 'icon' => 'clock-history'],
                                'confirmado' => ['class' => 'info', 'icon' => 'check-circle'],
                                'en_preparacion' => ['class' => 'purple', 'icon' => 'hourglass-split'],
                                'enviado' => ['class' => 'success', 'icon' => 'truck'],
                                'entregado' => ['class' => 'success', 'icon' => 'check-circle-fill'],
                                'cancelado' => ['class' => 'danger', 'icon' => 'x-circle'],
                            ];
                            $estadoActual = $estadosConfig[$pedido->estado] ?? ['class' => 'secondary', 'icon' => 'circle'];
                        @endphp
                        <span class="pedidos-badge pedidos-badge-{{ $pedido->estado }}">
                            <i class="bi bi-{{ $estadoActual['icon'] }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                        </span>
                    </div>

                    <!-- Detalles -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="small text-muted mb-1">
                            <i class="bi bi-box me-1"></i>
                            {{ $pedido->totalItems() }} {{ Str::plural('producto', $pedido->totalItems()) }}
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ Str::limit($pedido->direccion_entrega, 30) }}
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="text-center">
                            <small class="text-muted d-block mb-1">Total</small>
                            <div class="fw-bold text-success fs-4">
                                ${{ number_format($pedido->total_final, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="col-md-2 text-md-end">
                        <div class="d-flex gap-2 justify-content-md-end">
                            <a href="{{ route('cliente.pedidos.show', $pedido->_id) }}" 
                               class="pedidos-action-btn pedidos-action-btn-view"
                               title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            @if($pedido->puedeSerCancelado())
                            <button type="button" 
                                    class="pedidos-action-btn pedidos-action-btn-cancel"
                                    onclick="pedidosManager.showCancelModal('{{ $pedido->_id }}', '{{ $pedido->numero_pedido }}')"
                                    title="Cancelar pedido">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                            
                            @if(in_array($pedido->estado, ['confirmado', 'enviado']))
                            <button type="button" 
                                    class="pedidos-action-btn pedidos-action-btn-track"
                                    onclick="pedidosManager.showTrackingModal('{{ $pedido->_id }}')"
                                    title="Rastrear pedido">
                                <i class="bi bi-truck"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="pedidos-empty-state">
                <i class="bi bi-inbox"></i>
                <h4>No tienes pedidos aún</h4>
                <p>Comienza a ordenar nuestros deliciosos productos</p>
                <a href="{{ route('cliente.pedidos.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear mi primer pedido
                </a>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($pedidos->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $pedidos->links() }}
    </div>
    @endif
</div>

<!-- Loading Overlay -->
<div class="pedidos-loading-overlay">
    <div class="pedidos-loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/pedidos-cliente-modern.js') }}?v={{ filemtime(public_path('js/pages/pedidos-cliente-modern.js')) }}"></script>
<script>
    // Inicializar el manager
    document.addEventListener('DOMContentLoaded', function() {
        window.pedidosManager = new PedidosClienteManager();
        
        // Mostrar mensajes flash
        @if(session('success'))
            pedidosManager.showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            pedidosManager.showToast('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endpush
