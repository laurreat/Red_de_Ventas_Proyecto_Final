@extends('layouts.admin')

@section('title', '- Pedidos')
@section('page-title', 'Gestión de Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid fade-in">
    {{-- Header Hero --}}
    <div class="pedido-header scale-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="pedido-header-title">Gestión de Pedidos</h1>
                <p class="pedido-header-subtitle">Administra todos los pedidos del sistema de manera eficiente</p>
            </div>
            <div class="pedido-header-actions">
                <a href="{{ route('admin.pedidos.create') }}" class="pedido-btn pedido-btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nuevo Pedido</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Estadísticas de Pedidos --}}
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-1">
                <div class="pedido-stat-icon" style="background:rgba(114,47,55,0.1);color:var(--wine);">
                    <i class="bi bi-basket3"></i>
                </div>
                <div class="pedido-stat-value">{{ $stats['total_pedidos'] }}</div>
                <div class="pedido-stat-label">Total Pedidos</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-2">
                <div class="pedido-stat-icon" style="background:rgba(59,130,246,0.1);color:var(--info);">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="pedido-stat-value">{{ $stats['pedidos_hoy'] }}</div>
                <div class="pedido-stat-label">Pedidos Hoy</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-3">
                <div class="pedido-stat-icon" style="background:rgba(245,158,11,0.1);color:var(--warning);">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="pedido-stat-value">{{ $stats['pedidos_pendientes'] }}</div>
                <div class="pedido-stat-label">Pendientes</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-1">
                <div class="pedido-stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="pedido-stat-value">{{ $stats['pedidos_entregados'] }}</div>
                <div class="pedido-stat-label">Entregados</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-2">
                <div class="pedido-stat-icon" style="background:rgba(239,68,68,0.1);color:var(--danger);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="pedido-stat-value">{{ $stats['pedidos_cancelados'] }}</div>
                <div class="pedido-stat-label">Cancelados</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="pedido-stat-card fade-in-up animate-delay-3">
                <div class="pedido-stat-icon" style="background:rgba(114,47,55,0.1);color:var(--wine);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="pedido-stat-value">${{ format_currency($stats['ingresos_mes']) }}</div>
                <div class="pedido-stat-label">Ingresos del Mes</div>
            </div>
        </div>
    </div>

    {{-- Filtros de Búsqueda --}}
    <div class="pedido-filters-card fade-in-up">
        <div class="pedido-filters-header">
            <i class="bi bi-funnel"></i>
            <h3 class="pedido-filters-title">Filtros de Búsqueda</h3>
        </div>
        <div class="pedido-filters-body">
            <form method="GET" action="{{ route('admin.pedidos.index') }}" autocomplete="off">
                <div class="row">
                    <div class="col-lg-3 col-md-4 mb-3">
                        <label class="form-label fw-semibold">Buscar pedido</label>
                        <input type="text" class="form-control" name="buscar"
                               placeholder="Número de pedido, cliente..."
                               value="{{ request('buscar') }}"
                               style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" name="estado" style="border-radius:10px;padding:.75rem;">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $valor => $nombre)
                                <option value="{{ $valor }}" {{ request('estado') == $valor ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="form-label fw-semibold">Vendedor</label>
                        <select class="form-select" name="vendedor" style="border-radius:10px;padding:.75rem;">
                            <option value="">Todos los vendedores</option>
                            @foreach($vendedores as $vendedor)
                                <option value="{{ $vendedor->id }}" {{ request('vendedor') == $vendedor->id ? 'selected' : '' }}>
                                    {{ $vendedor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label fw-semibold">Fecha Desde</label>
                        <input type="date" class="form-control" name="fecha_desde"
                               value="{{ request('fecha_desde') }}" style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label fw-semibold">Fecha Hasta</label>
                        <input type="date" class="form-control" name="fecha_hasta"
                               value="{{ request('fecha_hasta') }}" style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-1 col-md-12 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" style="border-radius:10px;padding:.75rem;">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:.75rem;">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Pedidos --}}
    <div class="pedido-table-container fade-in-up">
        <div class="pedido-table-header">
            <h3 class="pedido-table-header-title">
                <i class="bi bi-list-ul"></i>
                <span>Lista de Pedidos ({{ $pedidos->total() }})</span>
            </h3>
        </div>

        @if($pedidos->count() > 0)
            <div class="table-responsive">
                <table class="pedido-table">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                        <tr class="fade-in-up">
                            <td>
                                <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">
                                    {{ $pedido->numero_pedido }}
                                </div>
                                <small style="color:var(--gray-500);">
                                    <i class="bi bi-box-seam"></i> {{ count($pedido->detalles_embebidos) }} producto(s)
                                </small>
                            </td>
                            <td>
                                <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">
                                    {{ $pedido->cliente->name }}
                                </div>
                                <small style="color:var(--gray-500);">{{ $pedido->cliente->email }}</small>
                            </td>
                            <td>
                                @if($pedido->vendedor)
                                    <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">
                                        {{ $pedido->vendedor->name }}
                                    </div>
                                    <small style="color:var(--gray-500);">{{ $pedido->vendedor->email }}</small>
                                @else
                                    <span style="color:var(--gray-500);">Sin vendedor</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:1.125rem;color:var(--wine);margin-bottom:.25rem;">
                                    ${{ format_currency($pedido->total_final) }}
                                </div>
                                @if($pedido->descuento > 0)
                                    <small style="color:var(--success);">
                                        <i class="bi bi-tag"></i> -${{ format_currency($pedido->descuento) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="pedido-badge pedido-badge-{{ $pedido->estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;">
                                    {{ $pedido->created_at->format('d/m/Y') }}
                                </div>
                                <small style="color:var(--gray-500);">
                                    <i class="bi bi-clock"></i> {{ $pedido->created_at->format('H:i') }}
                                </small>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex;gap:.25rem;">
                                    <a href="{{ route('admin.pedidos.show', $pedido) }}"
                                       class="pedido-action-btn pedido-action-btn-view"
                                       title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                                        <a href="{{ route('admin.pedidos.edit', $pedido) }}"
                                           class="pedido-action-btn pedido-action-btn-edit"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    <button type="button"
                                            class="pedido-action-btn pedido-action-btn-status"
                                            title="Cambiar Estado"
                                            onclick="showStatusSelector({{ json_encode($pedido->id) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode(ucfirst($pedido->estado)) }}, {{ json_encode($estados) }})">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    @if($pedido->estado != 'entregado')
                                        <button type="button"
                                                class="pedido-action-btn pedido-action-btn-delete"
                                                title="Eliminar"
                                                onclick="confirmDeletePedido({{ json_encode($pedido->id) }}, {{ json_encode($pedido->numero_pedido) }}, {{ json_encode($pedido->cliente->name ?? 'Cliente') }}, {{ json_encode('$' . format_currency($pedido->total_final)) }}, {{ json_encode(ucfirst($pedido->estado)) }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                {{-- Formularios ocultos --}}
                                <form id="status-form-{{ $pedido->id }}"
                                      action="{{ route('admin.pedidos.update-status', $pedido) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" id="estado-{{ $pedido->id }}">
                                </form>

                                <form id="delete-form-{{ $pedido->id }}"
                                      action="{{ route('admin.pedidos.destroy', $pedido) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div style="padding:1.5rem;">
                {{ $pedidos->appends(request()->query())->links() }}
            </div>
        @else
            <div class="pedido-empty-state">
                <div class="pedido-empty-state-icon">
                    <i class="bi bi-basket3"></i>
                </div>
                <h3 class="pedido-empty-state-title">No hay pedidos</h3>
                <p class="pedido-empty-state-text">No se encontraron pedidos que coincidan con los filtros aplicados.</p>
                <a href="{{ route('admin.pedidos.create') }}" class="pedido-btn pedido-btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    <span>Crear primer pedido</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Variables globales para el módulo de pedidos --}}
<script>
window.pedidosRoutes = {
    details: '{{ route("admin.pedidos.show", ":id") }}',
    updateStatus: '{{ route("admin.pedidos.update-status", ":id") }}',
    destroy: '{{ route("admin.pedidos.destroy", ":id") }}'
};
</script>

{{-- Módulo JavaScript optimizado --}}
<script src="{{ asset('js/admin/pedidos-modern.js') }}?v={{ filemtime(public_path('js/admin/pedidos-modern.js')) }}"></script>

{{-- Mostrar mensajes flash como toasts --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("success") }}', 'success');
    }
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ session("error") }}', 'error');
    }
});
</script>
@endif

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.pedidosManager) {
        window.pedidosManager.showToast('{{ $errors->first() }}', 'error');
    }
});
</script>
@endif
@endpush
