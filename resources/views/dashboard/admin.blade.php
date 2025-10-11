@extends('layouts.admin')

@section('title', '- Dashboard Administrador')
@section('page-title', 'Dashboard Administrador')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard.css') }}?v={{ filemtime(public_path('css/pages/admin-dashboard.css')) }}">
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Header Hero Section --}}
    <div class="dashboard-hero fade-in-up">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Bienvenido, {{ Auth::user()->name }}</h1>
                <p>Panel de Control General del Sistema - Vista completa de tu negocio</p>
            </div>
            <div>
                <span class="hero-date">
                    <i class="bi bi-calendar-check"></i>
                    {{ now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Main Stats Grid --}}
    <div class="stats-grid">
        {{-- Total Usuarios --}}
        <div class="stat-card fade-in-up animate-delay-1">
            <div class="stat-card-header">
                <div class="stat-icon primary">
                    <i class="bi bi-people"></i>
                </div>
                @php
                    $prevUsers = $stats['total_usuarios'] - 5; // Simulado para ejemplo
                    $userTrend = $stats['total_usuarios'] > $prevUsers ? 'up' : 'down';
                @endphp
                <span class="stat-trend {{ $userTrend }}">
                    <i class="bi bi-arrow-{{ $userTrend == 'up' ? 'up' : 'down' }}"></i>
                    +5
                </span>
            </div>
            <div class="stat-value" data-stat="total_usuarios">{{ number_format($stats['total_usuarios']) }}</div>
            <div class="stat-label">Total Usuarios</div>
            <div class="stat-extra">
                <i class="bi bi-person-badge"></i>
                <span>{{ number_format($stats['total_vendedores']) }} vendedores activos</span>
            </div>
        </div>

        {{-- Ventas del Mes --}}
        <div class="stat-card fade-in-up animate-delay-2">
            <div class="stat-card-header">
                <div class="stat-icon success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="stat-trend up">
                    <i class="bi bi-arrow-up"></i>
                    +12%
                </span>
            </div>
            <div class="stat-value text-success" data-stat="ventas_mes">${{ number_format($stats['ventas_mes'], 0) }}</div>
            <div class="stat-label">Ventas del Mes</div>
            <div class="stat-extra">
                <i class="bi bi-cash-coin"></i>
                <span>Hoy: ${{ number_format($stats['ventas_hoy'], 0) }}</span>
            </div>
        </div>

        {{-- Pedidos Pendientes --}}
        <div class="stat-card fade-in-up animate-delay-3">
            <div class="stat-card-header">
                <div class="stat-icon warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                @if($stats['pedidos_pendientes'] > 10)
                <span class="stat-trend down">
                    <i class="bi bi-exclamation-triangle"></i>
                    Alta
                </span>
                @endif
            </div>
            <div class="stat-value text-warning" data-stat="pedidos_pendientes">{{ number_format($stats['pedidos_pendientes']) }}</div>
            <div class="stat-label">Pedidos Pendientes</div>
            <div class="stat-extra">
                <i class="bi bi-cart-check"></i>
                <span>{{ number_format($stats['pedidos_hoy']) }} pedidos hoy</span>
            </div>
        </div>

        {{-- Productos --}}
        <div class="stat-card fade-in-up animate-delay-4">
            <div class="stat-card-header">
                <div class="stat-icon {{ $stats['productos_stock_bajo'] > 0 ? 'danger' : 'info' }}">
                    <i class="bi bi-boxes"></i>
                </div>
                @if($stats['productos_stock_bajo'] > 0)
                <span class="stat-trend down">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ $stats['productos_stock_bajo'] }}
                </span>
                @endif
            </div>
            <div class="stat-value {{ $stats['productos_stock_bajo'] > 0 ? 'text-danger' : '' }}" data-stat="total_productos">{{ number_format($stats['total_productos']) }}</div>
            <div class="stat-label">Total Productos</div>
            @if($stats['productos_stock_bajo'] > 0)
            <div class="stat-extra text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <span>{{ $stats['productos_stock_bajo'] }} con stock crítico</span>
            </div>
            @else
            <div class="stat-extra">
                <i class="bi bi-check-circle"></i>
                <span>Inventario óptimo</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Content Grid: Pedidos y Productos --}}
    <div class="content-grid">
        {{-- Recent Orders --}}
        <div class="content-card fade-in-up">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="bi bi-list-ul"></i>
                    Pedidos Recientes
                </h3>
                <a href="{{ route('admin.pedidos.index') }}" class="content-card-action">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="content-card-body">
                @if($pedidos_recientes->count() > 0)
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos_recientes as $pedido)
                            <tr data-order-id="{{ $pedido->id }}">
                                <td data-label="Pedido">
                                    <span class="order-number">#{{ $pedido->numero_pedido }}</span>
                                </td>
                                <td data-label="Cliente">
                                    <div class="order-client">
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($pedido->cliente->name ?? 'C', 0, 1)) }}
                                        </div>
                                        <div class="client-info">
                                            <span class="client-name">{{ $pedido->cliente->name ?? 'Cliente' }}</span>
                                            <span class="client-email">{{ $pedido->cliente->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Total">
                                    <span class="order-amount">${{ number_format(is_object($pedido->total_final) ? $pedido->total_final->jsonSerialize() : $pedido->total_final, 0) }}</span>
                                </td>
                                <td data-label="Estado">
                                    @php
                                        $statusClasses = [
                                            'pendiente' => 'pendiente',
                                            'confirmado' => 'confirmado',
                                            'en_preparacion' => 'confirmado',
                                            'listo' => 'entregado',
                                            'en_camino' => 'confirmado',
                                            'entregado' => 'entregado',
                                            'cancelado' => 'cancelado'
                                        ];
                                        $statusClass = $statusClasses[$pedido->estado] ?? 'pendiente';
                                    @endphp
                                    <span class="order-status {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                    </span>
                                </td>
                                <td data-label="Fecha">
                                    <span class="order-date">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="bi bi-cart-x"></i>
                        <p>No hay pedidos recientes</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Popular Products --}}
        <div class="content-card fade-in-up">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="bi bi-star"></i>
                    Productos Populares
                </h3>
                <a href="{{ route('admin.productos.index') }}" class="content-card-action">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="content-card-body">
                @if($productos_populares->count() > 0)
                    <div class="product-list">
                        @foreach($productos_populares->take(5) as $index => $producto)
                        @php
                            $maxVentas = $productos_populares->first()->cantidad_vendida ?? 1;
                            $percentage = $maxVentas > 0 ? ($producto->cantidad_vendida / $maxVentas) * 100 : 0;
                            $rankClass = $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : ''));
                        @endphp
                        <div class="product-item">
                            <div class="product-rank {{ $rankClass }}">{{ $index + 1 }}</div>
                            <div class="product-info">
                                <span class="product-name">{{ $producto->nombre }}</span>
                                <span class="product-category">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                            </div>
                            <div class="product-progress">
                                <div class="progress-bar-wrapper">
                                    <div class="progress-bar-fill" data-width="{{ $percentage }}%" style="width: {{ $percentage }}%;"></div>
                                </div>
                                <div class="progress-label">{{ number_format($percentage, 1) }}%</div>
                            </div>
                            <div class="product-sales">
                                <span class="sales-value">{{ number_format($producto->cantidad_vendida) }}</span>
                                <span class="sales-label">vendidos</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-box"></i>
                        <p>No hay datos de ventas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Secondary Stats Row --}}
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        {{-- Comisiones Pendientes --}}
        <div class="stat-card fade-in-up">
            <div class="stat-card-header">
                <div class="stat-icon primary">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
            <div class="stat-value text-wine" data-stat="comisiones_pendientes">${{ number_format($stats['comisiones_pendientes'], 0) }}</div>
            <div class="stat-label">Comisiones Pendientes</div>
        </div>

        {{-- Total Comisiones del Mes --}}
        <div class="stat-card fade-in-up animate-delay-1">
            <div class="stat-card-header">
                <div class="stat-icon success">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="stat-value text-success" data-stat="total_comisiones_mes">${{ number_format($stats['total_comisiones_mes'], 0) }}</div>
            <div class="stat-label">Comisiones del Mes</div>
        </div>

        {{-- Clientes Activos --}}
        <div class="stat-card fade-in-up animate-delay-2">
            <div class="stat-card-header">
                <div class="stat-icon info">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
            <div class="stat-value text-info" data-stat="clientes_activos">{{ number_format($stats['clientes_activos']) }}</div>
            <div class="stat-label">Clientes Activos</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/dashboard-admin.js') }}?v={{ filemtime(public_path('js/admin/dashboard-admin.js')) }}"></script>
<script>
    // Configuración de rutas para el dashboard
    window.dashboardConfig = {
        routes: {
            stats: '{{ route('dashboard') }}',
            pedidos: '{{ route('admin.pedidos.index') }}',
            productos: '{{ route('admin.productos.index') }}'
        },
        csrf: '{{ csrf_token() }}'
    };

    // Detectar soporte PWA
    if ('serviceWorker' in navigator) {
        console.log('✅ Service Worker compatible');
    }

    // Performance Monitoring
    if (window.performance && window.performance.timing) {
        window.addEventListener('load', () => {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log(`⚡ Tiempo de carga: ${(pageLoadTime / 1000).toFixed(2)}s`);

            // Enviar métrica si es mayor a 3 segundos
            if (pageLoadTime > 3000) {
                console.warn('⚠️ La página tardó más de 3 segundos en cargar');
            }
        });
    }
</script>
@endpush
