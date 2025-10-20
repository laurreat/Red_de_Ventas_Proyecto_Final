@extends('layouts.cliente')

@section('title', ' - Mis Notificaciones')
@section('header-title', 'Mis Notificaciones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/notificaciones.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid notificaciones-container">
    
    <!-- Header de Notificaciones -->
    <div class="notificaciones-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="notificaciones-title">
                    <i class="bi bi-bell-fill"></i>
                    Centro de Notificaciones
                </h2>
                <p class="notificaciones-subtitle">
                    Mantente al día con todas tus notificaciones
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="notificaciones-actions">
                    <button class="btn btn-outline-success" id="btnMarcarTodas">
                        <i class="bi bi-check-all"></i>
                        Marcar todas como leídas
                    </button>
                    <button class="btn btn-outline-info" id="btnLimpiarLeidas">
                        <i class="bi bi-check2-circle"></i>
                        Limpiar leídas
                    </button>
                    <button class="btn btn-outline-danger" id="btnLimpiarAntiguas">
                        <i class="bi bi-trash"></i>
                        Limpiar antiguas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Estadísticas -->
    <div class="row mb-4">
        <!-- Estadísticas Rápidas -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total</div>
                    <div class="stat-value" id="totalNotificaciones">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="bi bi-envelope-open"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">No Leídas</div>
                    <div class="stat-value" id="noLeidasCount">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="bi bi-envelope-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Leídas</div>
                    <div class="stat-value" id="leidasCount">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="bi bi-calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Hoy</div>
                    <div class="stat-value" id="hoyCount">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-card mb-4">
        <div class="filtros-header">
            <i class="bi bi-funnel"></i>
            Filtros
        </div>
        <div class="filtros-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" id="filtroTipo" onchange="notificacionesModule.aplicarFiltros()">
                        <option value="">Todos</option>
                        <option value="pedido">Pedidos</option>
                        <option value="venta">Ventas</option>
                        <option value="comision">Comisiones</option>
                        <option value="usuario">Usuarios</option>
                        <option value="sistema">Sistema</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" onchange="notificacionesModule.aplicarFiltros()">
                        <option value="">Todas</option>
                        <option value="0">No Leídas</option>
                        <option value="1">Leídas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Período</label>
                    <select class="form-select" id="filtroPeriodo" onchange="notificacionesModule.aplicarFiltros()">
                        <option value="">Todo</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta Semana</option>
                        <option value="mes">Este Mes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="filtroBuscar" placeholder="Buscar en notificaciones..." onkeyup="notificacionesModule.buscar()">
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="notificaciones-list-card">
        <div class="notificaciones-list-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i>
                    Notificaciones
                    <span class="badge bg-primary ms-2" id="resultadosCount">0</span>
                </h5>
                <div class="view-toggle">
                    <button class="btn btn-sm" id="btnVistaLista">
                        <i class="bi bi-list"></i>
                    </button>
                    <button class="btn btn-sm active" id="btnVistaGrid">
                        <i class="bi bi-grid"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="notificaciones-list-body" id="notificacionesListBody">
            <!-- Loading State -->
            <div class="loading-state text-center py-5" id="loadingState">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted">Cargando notificaciones...</p>
            </div>

            <!-- Empty State -->
            <div class="empty-state text-center py-5" id="emptyState" style="display: none;">
                <div class="empty-icon mb-3">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h4>No hay notificaciones</h4>
                <p class="text-muted">No tienes notificaciones en este momento</p>
            </div>

            <!-- Grid de Notificaciones -->
            <div class="notificaciones-grid" id="notificacionesGrid">
                <!-- Las notificaciones se cargan aquí dinámicamente -->
            </div>
        </div>

        <!-- Paginación -->
        <div class="notificaciones-list-footer" id="paginationContainer">
            <!-- La paginación se genera dinámicamente -->
        </div>
    </div>

</div>

<!-- Modal de Detalles de Notificación - Glassmorphism -->
<div class="modal fade" id="notificacionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1056;">
        <div class="modal-content glass-modal" style="background: rgba(255, 255, 255, 0.98) !important; overflow: visible !important; z-index: 1057 !important;">
            <div class="modal-glass-bg" style="pointer-events: none !important; z-index: 0 !important;"></div>
            <div class="modal-header glass-header" style="position: relative; z-index: 10;">
                <div class="modal-title-wrapper">
                    <div class="modal-icon-wrapper" id="modalIconWrapper">
                        <i class="bi bi-bell" id="modalIcono"></i>
                    </div>
                    <div class="modal-title-content">
                        <h5 class="modal-title" id="modalTituloTexto"></h5>
                        <div class="modal-subtitle">
                            <span class="modal-badge" id="modalTipoBadge"></span>
                            <span class="modal-time" id="modalFechaHeader"></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close-glass" data-bs-dismiss="modal" aria-label="Close" style="position: relative; z-index: 20; pointer-events: auto;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body glass-body" style="position: relative; z-index: 10;">
                <div class="modal-content-wrapper">
                    <div id="modalContenido"></div>
                </div>
                <div class="modal-meta-glass">
                    <div class="meta-item">
                        <i class="bi bi-clock-history"></i>
                        <div class="meta-content">
                            <span class="meta-label">Fecha</span>
                            <span class="meta-value" id="modalFecha"></span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-tag"></i>
                        <div class="meta-content">
                            <span class="meta-label">Categoría</span>
                            <span class="meta-value" id="modalTipo"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer glass-footer" style="position: relative; z-index: 10;">
                <button type="button" class="btn-glass btn-glass-danger" id="btnEliminarNotificacion" style="position: relative; z-index: 20; pointer-events: auto;">
                    <i class="bi bi-trash"></i>
                    <span>Eliminar</span>
                </button>
                <button type="button" class="btn-glass btn-glass-secondary" data-bs-dismiss="modal" style="position: relative; z-index: 20; pointer-events: auto;">
                    <i class="bi bi-x-circle"></i>
                    <span>Cerrar</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/modules/glass-modal.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pages/notificaciones-module.js') }}?v={{ time() }}"></script>
<script>
// Limpiar modales y backdrops al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Limpiar cualquier backdrop huérfano
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    
    // Asegurar que body no tenga clases de modal
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    
    // Limpiar modal de notificación si existe duplicado
    const modalElement = document.getElementById('notificacionModal');
    if (modalElement) {
        const instance = bootstrap.Modal.getInstance(modalElement);
        if (instance) {
            instance.dispose();
        }
    }
});

// Event delegation para clicks en notificaciones
document.addEventListener('click', function(e) {
    // Click en card para ver detalles
    const card = e.target.closest('[data-notification-click]');
    if (card) {
        const id = card.dataset.notificationClick;
        console.log('Click en notificación, ID:', id);
        if (window.notificacionesModule && id && id !== 'undefined') {
            window.notificacionesModule.verDetalle(id);
        }
        return;
    }
    
    // Marcar como leída
    const markReadBtn = e.target.closest('[data-mark-read]');
    if (markReadBtn) {
        e.stopPropagation();
        const id = markReadBtn.dataset.markRead;
        console.log('Marcar como leída, ID:', id);
        if (window.notificacionesModule && id && id !== 'undefined') {
            window.notificacionesModule.marcarComoLeida(id);
        }
        return;
    }
    
    // Eliminar notificación
    const deleteBtn = e.target.closest('[data-delete-notif]');
    if (deleteBtn) {
        e.stopPropagation();
        const id = deleteBtn.dataset.deleteNotif;
        console.log('Eliminar notificación, ID:', id, 'Tipo:', typeof id);
        if (window.notificacionesModule && id && id !== 'undefined') {
            window.notificacionesModule.confirmarEliminar(id);
        } else {
            console.error('ID inválido o módulo no disponible', {
                id: id,
                module: !!window.notificacionesModule,
                isValidId: id && id !== 'undefined'
            });
        }
        return;
    }
});
</script>
@endpush
