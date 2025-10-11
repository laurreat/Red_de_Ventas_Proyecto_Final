@extends('layouts.admin')

@section('title', 'Notificaciones')

@push('styles')
    <link href="{{ asset('css/admin/notificaciones-modern.css') }}?v={{ filemtime(public_path('css/admin/notificaciones-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Header -->
    <div class="notif-header fade-in-up">
        <div class="notif-header-content">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <h1 class="notif-title">
                        <i class="bi bi-bell-fill me-2"></i>
                        Centro de Notificaciones
                    </h1>
                    <p class="notif-subtitle mb-0">
                        Gestiona todas tus notificaciones del sistema
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="notif-actions justify-content-lg-end">
                        <button class="notif-btn notif-btn-outline" onclick="crearNotificacionesPrueba()">
                            <i class="bi bi-plus-circle"></i>
                            Crear Pruebas
                        </button>
                        <button class="notif-btn notif-btn-white" onclick="marcarTodasLeidas()">
                            <i class="bi bi-check-all"></i>
                            Marcar Todas
                        </button>
                        <button class="notif-btn notif-btn-outline" onclick="limpiarLeidas()">
                            <i class="bi bi-trash"></i>
                            Limpiar Leídas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-1">
                <div class="notif-stat-icon" style="background: rgba(59,130,246,0.1); color: var(--info);">
                    <i class="bi bi-bell"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['total'] }}</h2>
                <p class="notif-stat-label">Total Notificaciones</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-2">
                <div class="notif-stat-icon" style="background: rgba(245,158,11,0.1); color: var(--warning);">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['no_leidas'] }}</h2>
                <p class="notif-stat-label">Sin Leer</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="notif-stat-card scale-in animate-delay-3">
                <div class="notif-stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2 class="notif-stat-value">{{ $stats['leidas'] }}</h2>
                <p class="notif-stat-label">Leídas</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="notif-filter-card fade-in">
        <div class="notif-filter-body">
            <form method="GET" action="{{ route('admin.notificaciones.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="notif-form-label">
                            <i class="bi bi-funnel me-1"></i>
                            Estado
                        </label>
                        <select name="filter" class="notif-form-control">
                            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>Sin Leer</option>
                            <option value="read" {{ $filter == 'read' ? 'selected' : '' }}>Leídas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="notif-form-label">
                            <i class="bi bi-tag me-1"></i>
                            Tipo
                        </label>
                        <select name="tipo" class="notif-form-control">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $tipoItem)
                            <option value="{{ $tipoItem }}" {{ $tipo == $tipoItem ? 'selected' : '' }}>
                                {{ ucfirst($tipoItem) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="notif-form-label">&nbsp;</label>
                        <button type="submit" class="notif-btn notif-btn-white" style="width: 100%;">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="notif-list-card fade-in">
        <div class="notif-list-header">
            <i class="bi bi-list-ul"></i>
            <h2 class="notif-list-title">
                Notificaciones {{ $filter != 'all' ? '(' . ucfirst(str_replace('_', ' ', $filter)) . ')' : '' }}
            </h2>
        </div>

        @if($notificaciones->count() > 0)
            @foreach($notificaciones as $notificacion)
            <div class="notif-item {{ !$notificacion->leida ? 'unread' : '' }}" data-id="{{ $notificacion->id }}">
                <div class="notif-item-content">
                    <div class="notif-icon-wrapper">
                        @switch($notificacion->tipo)
                            @case('pedido')
                                <i class="bi bi-cart notif-icon" style="color: var(--info);"></i>
                                @break
                            @case('venta')
                                <i class="bi bi-currency-dollar notif-icon" style="color: var(--success);"></i>
                                @break
                            @case('usuario')
                                <i class="bi bi-person notif-icon" style="color: #8b5cf6;"></i>
                                @break
                            @case('comision')
                                <i class="bi bi-wallet notif-icon" style="color: var(--warning);"></i>
                                @break
                            @case('sistema')
                                <i class="bi bi-gear notif-icon" style="color: var(--gray-500);"></i>
                                @break
                            @default
                                <i class="bi bi-bell notif-icon" style="color: var(--gray-500);"></i>
                        @endswitch
                    </div>

                    <div class="notif-body">
                        <h3 class="notif-item-title">{{ $notificacion->titulo }}</h3>
                        <p class="notif-item-message">{{ $notificacion->mensaje }}</p>
                        <div class="notif-item-meta">
                            <small style="color: var(--gray-500);">
                                <i class="bi bi-clock me-1"></i>
                                {{ $notificacion->created_at->diffForHumans() }}
                            </small>
                            <span class="notif-badge notif-badge-{{ $notificacion->tipo }}">
                                {{ ucfirst($notificacion->tipo) }}
                            </span>
                            @if(!$notificacion->leida)
                                <span class="notif-badge notif-badge-nuevo">Nuevo</span>
                            @endif
                        </div>
                    </div>

                    <div class="notif-actions-wrapper">
                        @if(!$notificacion->leida)
                            <button class="notif-action-btn notif-action-btn-success"
                                    onclick="marcarLeida('{{ $notificacion->id }}')"
                                    title="Marcar como leída">
                                <i class="bi bi-check"></i>
                            </button>
                        @endif
                        <button class="notif-action-btn notif-action-btn-danger"
                                onclick="eliminarNotificacion('{{ $notificacion->id }}')"
                                title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Paginación -->
            <div class="p-3">
                {{ $notificaciones->withQueryString()->links() }}
            </div>
        @else
            <div class="notif-empty">
                <i class="bi bi-bell-slash notif-empty-icon"></i>
                <h3 class="notif-empty-title">No hay notificaciones</h3>
                <p class="notif-empty-text">
                    @if($filter == 'unread')
                        No tienes notificaciones sin leer
                    @elseif($filter == 'read')
                        No tienes notificaciones leídas
                    @else
                        No tienes notificaciones en este momento
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
window.notificacionesRoutes = {
    marcarLeida: '{{ route("admin.notificaciones.marcar-leida", ":id") }}',
    marcarTodasLeidas: '{{ route("admin.notificaciones.marcar-todas-leidas") }}',
    eliminar: '{{ route("admin.notificaciones.eliminar", ":id") }}',
    limpiarLeidas: '{{ route("admin.notificaciones.limpiar-leidas") }}',
    crearPruebas: '{{ route("admin.notificaciones.crear-pruebas") }}'
};
window.notificacionesCSRF = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/admin/notificaciones-modern.js') }}?v={{ filemtime(public_path('js/admin/notificaciones-modern.js')) }}"></script>
@endpush
