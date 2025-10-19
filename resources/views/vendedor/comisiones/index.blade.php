@extends('layouts.vendedor')

@section('title', 'Mis Comisiones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/comisiones-modern.css') }}?v={{ time() }}">
@endpush

@section('content')
<!-- Header Hero con Acción -->
<div class="comisiones-header fade-in-up">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="comisiones-header-content">
                <h1 class="comisiones-header-title">
                    <i class="bi bi-cash-coin"></i>
                    Mis Comisiones
                </h1>
                <p class="comisiones-header-subtitle">
                    <i class="bi bi-graph-up me-2"></i>
                    Gestiona y consulta tus comisiones ganadas
                </p>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            @if(($stats['disponible_retiro'] ?? 0) >= 50000)
                <a href="{{ route('vendedor.comisiones.solicitar') }}" class="comisiones-action-btn-solicitar" style="font-size: 1rem; padding: 1rem 2rem;">
                    <i class="bi bi-cash-stack"></i>
                    Solicitar Retiro
                </a>
            @else
                <div style="color: rgba(255,255,255,0.8); font-size: 0.9rem; text-align: center;">
                    <i class="bi bi-info-circle"></i>
                    <div>Mínimo $50,000 para retiro</div>
                    <div style="font-size: 0.85rem; margin-top: 0.25rem;">
                        Te faltan: ${{ number_format(50000 - ($stats['disponible_retiro'] ?? 0), 0, ',', '.') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Stats Cards Grid -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="comisiones-stat-card stat-total fade-in-up animate-delay-1" style="opacity:0">
            <div class="comisiones-stat-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['total_ganado'] ?? 0, 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Total Ganado</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="comisiones-stat-card stat-disponible fade-in-up animate-delay-2" style="opacity:0" data-filter="pendiente">
            <div class="comisiones-stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['disponible_retiro'] ?? 0, 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Disponible para Retiro</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="comisiones-stat-card stat-proceso fade-in-up animate-delay-3" style="opacity:0" data-filter="en_proceso">
            <div class="comisiones-stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['en_proceso'] ?? 0, 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">En Proceso</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="comisiones-stat-card stat-pagado fade-in-up animate-delay-4" style="opacity:0" data-filter="pagado">
            <div class="comisiones-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['pagado'] ?? 0, 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Pagado</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="comisiones-filters fade-in-up animate-delay-5" style="opacity:0">
    <form id="comisiones-filter-form" method="GET" action="{{ route('vendedor.comisiones.index') }}">
        <div class="comisiones-filter-row">
            <div class="comisiones-filter-group">
                <label class="comisiones-filter-label">Estado</label>
                <select name="estado" class="comisiones-filter-select">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                </select>
            </div>

            <div class="comisiones-filter-group">
                <label class="comisiones-filter-label">Tipo</label>
                <select name="tipo" class="comisiones-filter-select">
                    <option value="">Todos</option>
                    <option value="venta_directa" {{ request('tipo') == 'venta_directa' ? 'selected' : '' }}>Venta Directa</option>
                    <option value="referido" {{ request('tipo') == 'referido' ? 'selected' : '' }}>Referido</option>
                </select>
            </div>

            <div class="comisiones-filter-group">
                <label class="comisiones-filter-label">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="comisiones-filter-input">
            </div>

            <div class="comisiones-filter-group">
                <label class="comisiones-filter-label">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="comisiones-filter-input">
            </div>

            <div class="comisiones-filter-group">
                <button type="submit" class="comisiones-btn-filter">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
            </div>

            <div class="comisiones-filter-group">
                <a href="{{ route('vendedor.comisiones.index') }}" class="comisiones-btn-reset">
                    <i class="bi bi-x-circle"></i>
                    Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de Comisiones -->
@if($comisiones->count() > 0)
<div class="comisiones-table-container fade-in-up animate-delay-5" style="opacity:0">
    <table class="comisiones-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comisiones as $comision)
            <tr>
                <td>{{ $comision->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($comision->tipo == 'venta_directa')
                        <span class="comisiones-badge comisiones-badge-venta">
                            <i class="bi bi-cart-check"></i> Venta Directa
                        </span>
                    @else
                        <span class="comisiones-badge comisiones-badge-referido">
                            <i class="bi bi-people"></i> Referido
                        </span>
                    @endif
                </td>
                <td>
                    @if(isset($comision->pedido_data['id']))
                        Pedido #{{ $comision->pedido_data['id'] }}
                    @elseif(isset($comision->referido_data['name']))
                        Referido: {{ $comision->referido_data['name'] }}
                    @else
                        Sin descripción
                    @endif
                </td>
                <td>
                    <strong style="color: var(--wine); font-size: 1.1rem;">
                        ${{ number_format($comision->monto, 0, ',', '.') }}
                    </strong>
                </td>
                <td>
                    @if($comision->estado == 'pendiente')
                        <span class="comisiones-badge comisiones-badge-pendiente">
                            <i class="bi bi-clock"></i> Pendiente
                        </span>
                    @elseif($comision->estado == 'en_proceso')
                        <span class="comisiones-badge comisiones-badge-proceso">
                            <i class="bi bi-hourglass"></i> En Proceso
                        </span>
                    @elseif($comision->estado == 'pagado')
                        <span class="comisiones-badge comisiones-badge-pagado">
                            <i class="bi bi-check-circle"></i> Pagado
                        </span>
                    @else
                        <span class="comisiones-badge comisiones-badge-rechazado">
                            <i class="bi bi-x-circle"></i> {{ ucfirst($comision->estado) }}
                        </span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('vendedor.comisiones.show', $comision->_id) }}" class="comisiones-action-btn comisiones-action-btn-view">
                        <i class="bi bi-eye"></i>
                        Ver
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Paginación -->
<div class="comisiones-pagination mt-4">
    {{ $comisiones->links() }}
</div>

@else
<!-- Estado vacío -->
<div class="comisiones-empty fade-in-up animate-delay-5" style="opacity:0">
    <div class="comisiones-empty-icon">
        <i class="bi bi-inbox"></i>
    </div>
    <h3 class="comisiones-empty-title">No hay comisiones</h3>
    <p class="comisiones-empty-text">
        Aún no tienes comisiones registradas. ¡Comienza a vender para ganar comisiones!
    </p>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/comisiones-modern.js') }}?v={{ time() }}"></script>
@endpush
