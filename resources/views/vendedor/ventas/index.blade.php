@extends('layouts.vendedor')

@section('title', 'Historial de Ventas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/historial-ventas-modern.css') }}?v={{ filemtime(public_path('css/vendedor/historial-ventas-modern.css')) }}">
@endpush

@section('content')
<!-- Header Hero -->
<div class="historial-header fade-in-up">
    <div class="historial-header-content">
        <h1 class="historial-header-title">
            📊 Historial de Ventas
        </h1>
        <p class="historial-header-subtitle">
            Consulta y administra tu historial completo de ventas
        </p>
        <div class="historial-header-actions">
            <a href="{{ route('vendedor.ventas.create') }}" class="historial-btn-primary">
                ➕ Nueva Venta
            </a>
            <button id="export-all-btn" class="historial-btn-secondary">
                📥 Exportar Todo
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-1 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-total">📋</div>
            <div class="historial-stat-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="historial-stat-label">Total Ventas</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-2 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-completadas">✅</div>
            <div class="historial-stat-value">{{ $stats['completadas'] ?? 0 }}</div>
            <div class="historial-stat-label">Completadas</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-3 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-pendientes">⏳</div>
            <div class="historial-stat-value">{{ $stats['pendientes'] ?? 0 }}</div>
            <div class="historial-stat-label">Pendientes</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-1 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-ventas">💰</div>
            <div class="historial-stat-value">${{ number_format(to_float($stats['total_ventas'] ?? 0), 0) }}</div>
            <div class="historial-stat-label">Total Vendido</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-2 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-comisiones">💵</div>
            <div class="historial-stat-value">${{ number_format(to_float($stats['comisiones_totales'] ?? 0), 0) }}</div>
            <div class="historial-stat-label">Comisiones</div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2 mb-3 animate-delay-3 fade-in-up">
        <div class="historial-stat-card">
            <div class="historial-stat-icon stat-total">📈</div>
            <div class="historial-stat-value">{{ $stats['ventas_mes'] ?? 0 }}</div>
            <div class="historial-stat-label">Este Mes</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="historial-filters fade-in-up animate-delay-1">
    <form id="historial-filter-form" method="GET" action="{{ route('vendedor.ventas.index') }}">
        <div class="historial-filter-row">
            <div class="historial-filter-group">
                <label class="historial-filter-label">Estado</label>
                <select name="estado" class="historial-filter-select">
                    <option value="">Todos</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="historial-filter-group">
                <label class="historial-filter-label">Método de Pago</label>
                <select name="metodo_pago" class="historial-filter-select">
                    <option value="">Todos</option>
                    <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta" {{ request('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                    <option value="credito" {{ request('metodo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                </select>
            </div>
            <div class="historial-filter-group">
                <label class="historial-filter-label">Desde</label>
                <input type="date" name="fecha_desde" class="historial-filter-input" value="{{ request('fecha_desde') }}">
            </div>
            <div class="historial-filter-group">
                <label class="historial-filter-label">Hasta</label>
                <input type="date" name="fecha_hasta" class="historial-filter-input" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="historial-filter-group">
                <button type="submit" class="historial-btn-primary" style="width:100%">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Ventas Table -->
<div class="historial-table-container fade-in-up animate-delay-2">
    @if($ventas->count() > 0)
    <table class="historial-table">
        <thead>
            <tr>
                <th>N° Venta</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Método Pago</th>
                <th>Total</th>
                <th>Comisión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>
                    <span class="historial-venta-numero">{{ $venta->numero_venta }}</span>
                </td>
                <td>
                    <div class="historial-cliente-info">
                        <div class="historial-cliente-avatar">
                            {{ strtoupper(substr($venta->cliente_data['name'] ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <p class="historial-cliente-name">{{ $venta->cliente_data['name'] ?? 'N/A' }}</p>
                            <p class="historial-cliente-email">{{ $venta->cliente_data['email'] ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="historial-badge historial-badge-{{ $venta->estado }}">
                        {{ ucfirst($venta->estado) }}
                    </span>
                </td>
                <td>
                    <span class="historial-badge historial-badge-{{ $venta->metodo_pago }}">
                        {{ ucfirst($venta->metodo_pago) }}
                    </span>
                </td>
                <td>
                    <span class="historial-monto">${{ number_format($venta->total_final, 2) }}</span>
                </td>
                <td>
                    <span style="color: var(--success); font-weight: 600;">
                        ${{ number_format($venta->comision_vendedor ?? 0, 2) }}
                    </span>
                </td>
                <td>
                    <button class="historial-action-btn historial-action-btn-view"
                            data-id="{{ $venta->_id }}"
                            title="Ver Detalles">
                        👁️
                    </button>
                    <button class="historial-action-btn historial-action-btn-export"
                            data-id="{{ $venta->_id }}"
                            title="Exportar">
                        📄
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="historial-pagination">
        {{ $ventas->links() }}
    </div>
    @else
    <div class="historial-empty-state">
        <div class="historial-empty-icon">📊</div>
        <h3 class="historial-empty-title">No hay ventas registradas</h3>
        <p class="historial-empty-message">Comienza a registrar tus ventas para ver el historial aquí</p>
        <a href="{{ route('vendedor.ventas.create') }}" class="historial-btn-primary">
            ➕ Registrar Primera Venta
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/historial-ventas-modern.js') }}?v={{ filemtime(public_path('js/vendedor/historial-ventas-modern.js')) }}"></script>
<script>
@if(session('success'))
const successMessage = "{{ session('success') }}";
@endif
@if(session('error'))
const errorMessage = "{{ session('error') }}";
@endif
</script>
@endpush
