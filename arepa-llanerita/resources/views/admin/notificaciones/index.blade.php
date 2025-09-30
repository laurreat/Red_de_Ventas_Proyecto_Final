@extends('layouts.admin')

@section('title', 'Notificaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Centro de Notificaciones</h2>
                    <p class="text-muted mb-0">Gestiona todas tus notificaciones del sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-info me-2" onclick="crearNotificacionesPrueba()">
                        <i class="bi bi-plus-circle me-1"></i>
                        Crear Pruebas
                    </button>
                    <button class="btn btn-outline-success me-2" onclick="marcarTodasLeidas()">
                        <i class="bi bi-check-all me-1"></i>
                        Marcar Todas como Leídas
                    </button>
                    <button class="btn btn-outline-danger" onclick="limpiarLeidas()">
                        <i class="bi bi-trash me-1"></i>
                        Limpiar Leídas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-bell fs-2 text-primary"></i>
                    <h4 class="mt-2">{{ $stats['total'] }}</h4>
                    <small class="text-muted">Total Notificaciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-bell-fill fs-2 text-warning"></i>
                    <h4 class="mt-2">{{ $stats['no_leidas'] }}</h4>
                    <small class="text-muted">Sin Leer</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h4 class="mt-2">{{ $stats['leidas'] }}</h4>
                    <small class="text-muted">Leídas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="color: black;">Estado</label>
                            <select name="filter" class="form-select">
                                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Todas</option>
                                <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>Sin Leer</option>
                                <option value="read" {{ $filter == 'read' ? 'selected' : '' }}>Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="color: black;">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="">Todos los tipos</option>
                                @foreach($tipos as $tipoItem)
                                <option value="{{ $tipoItem }}" {{ $tipo == $tipoItem ? 'selected' : '' }}>
                                    {{ ucfirst($tipoItem) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list me-2"></i>
                        Notificaciones {{ $filter != 'all' ? '(' . ucfirst($filter) . ')' : '' }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($notificaciones->count() > 0)
                        @foreach($notificaciones as $notificacion)
                        <div class="notification-item p-3 border-bottom {{ !$notificacion->leida ? 'bg-light' : '' }}"
                             data-id="{{ $notificacion->id }}">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-3">
                                    @switch($notificacion->tipo)
                                        @case('pedido')
                                            <i class="bi bi-cart text-primary fs-4"></i>
                                            @break
                                        @case('venta')
                                            <i class="bi bi-currency-dollar text-success fs-4"></i>
                                            @break
                                        @case('usuario')
                                            <i class="bi bi-person text-info fs-4"></i>
                                            @break
                                        @case('comision')
                                            <i class="bi bi-wallet text-warning fs-4"></i>
                                            @break
                                        @case('sistema')
                                            <i class="bi bi-gear text-secondary fs-4"></i>
                                            @break
                                        @default
                                            <i class="bi bi-bell text-muted fs-4"></i>
                                    @endswitch
                                </div>

                                <div class="notification-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $notificacion->titulo }}</h6>
                                            <p class="mb-1 text-muted">{{ $notificacion->mensaje }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $notificacion->created_at->diffForHumans() }}
                                                <span class="badge bg-secondary ms-2">{{ ucfirst($notificacion->tipo) }}</span>
                                                @if(!$notificacion->leida)
                                                    <span class="badge bg-warning ms-1">Nuevo</span>
                                                @endif
                                            </small>
                                        </div>

                                        <div class="notification-actions">
                                            @if(!$notificacion->leida)
                                                <button class="btn btn-sm btn-outline-success me-1"
                                                        onclick="marcarLeida('{{ $notificacion->id }}')"
                                                        title="Marcar como leída">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="eliminarNotificacion('{{ $notificacion->id }}')"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Paginación -->
                        <div class="p-3">
                            {{ $notificaciones->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay notificaciones</h4>
                            <p class="text-muted">
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
        </div>
    </div>
</div>

<!-- Modales de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage">¿Estás seguro de realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmModalAction">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultado -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="resultModalHeader">
                <h5 class="modal-title" id="resultModalTitle">Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="resultModalMessage">Operación completada</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/notificaciones.css') }}">
@endpush

@push('scripts')
{{-- Variables globales para los módulos de notificaciones --}}
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

{{-- Módulos de funcionalidad de notificaciones --}}
<script src="{{ asset('js/admin/notificaciones-management.js') }}"></script>
@endpush