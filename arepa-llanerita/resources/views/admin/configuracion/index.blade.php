@extends('layouts.admin')

@section('title', 'Configuración del Sistema')

@push('styles')
<style>
/* CSS simplificado para modales que funcione correctamente */
.modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 1055 !important;
    outline: 0 !important;
}

.modal.show {
    display: flex !important;
    opacity: 1 !important;
}

.modal-dialog {
    max-width: 500px !important;
    width: auto !important;
    margin: 0 auto !important;
}

.modal-content {
    background-color: white !important;
    border: 1px solid rgba(0,0,0,.2) !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
}

/* Centrar modales verticalmente */
.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
}

.modal-dialog-centered::before {
    content: '';
    display: block;
    height: calc(100vh - 1rem);
    height: -webkit-min-content;
    height: -moz-min-content;
    height: min-content;
}

/* Prevenir scroll del body cuando modal está abierto */
.modal-open {
    overflow: hidden;
}

/* Efectos de animación para modales nativos */
.modal:not(.fade) {
    transition: opacity 0.15s linear;
}

.modal.fade {
    transition: opacity 0.15s linear;
}

.modal.fade:not(.show) {
    opacity: 0;
}

.modal.fade.show {
    opacity: 1;
}

/* Asegurar que el backdrop fade funcione */
.modal-backdrop.fade {
    opacity: 0;
    transition: opacity 0.15s linear;
}

.modal-backdrop.fade.show {
    opacity: 0.5;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Configuración del Sistema</h2>
                    <p class="text-muted mb-0">Configuración general del sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-info me-2" onclick="crearBackup()">
                        <i class="bi bi-download me-1"></i>
                        Crear Backup
                    </button>
                    <button class="btn btn-outline-warning me-2" onclick="limpiarCache()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Limpiar Cache
                    </button>
                    <button class="btn btn-primary" onclick="mostrarInfoSistema()">
                        <i class="bi bi-info-circle me-1"></i>
                        Info Sistema
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estadísticas del Sistema -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h4 class="mt-2">{{ $estadisticas['usuarios_totales'] ?? 0 }}</h4>
                    <small class="text-muted">Usuarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-cart fs-2 text-success"></i>
                    <h4 class="mt-2">{{ $estadisticas['pedidos_totales'] ?? 0 }}</h4>
                    <small class="text-muted">Pedidos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-box fs-2 text-warning"></i>
                    <h4 class="mt-2">{{ $estadisticas['productos_totales'] ?? 0 }}</h4>
                    <small class="text-muted">Productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar fs-2 text-info"></i>
                    <h4 class="mt-2">${{ number_format($estadisticas['ventas_mes_actual'] ?? 0) }}</h4>
                    <small class="text-muted">Ventas Este Mes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-hdd fs-2 text-secondary"></i>
                    <h5 class="mt-2">{{ $estadisticas['espacio_storage']['usado'] ?? 'N/A' }}</h5>
                    <small class="text-muted">Espacio Usado</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración General -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Configuración General
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#configGeneral">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.configuracion.update-general') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Nombre de la Empresa</label>
                                    <input type="text" name="nombre_empresa" class="form-control"
                                           value="{{ $configuraciones['general']['nombre_empresa'] ?? 'Arepa la Llanerita' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Teléfono</label>
                                    <input type="text" name="telefono_empresa" class="form-control"
                                           value="{{ $configuraciones['general']['telefono_empresa'] ?? '+57 300 123 4567' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Email</label>
                                    <input type="email" name="email_empresa" class="form-control"
                                           value="{{ $configuraciones['general']['email_empresa'] ?? 'info@arepallanerita.com' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Dirección</label>
                                    <textarea name="direccion_empresa" class="form-control" rows="2">{{ $configuraciones['general']['direccion_empresa'] ?? 'Calle 123 #45-67, Bogotá, Colombia' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Zona Horaria</label>
                                    <select name="timezone" class="form-select">
                                        <option value="America/Bogota" {{ ($configuraciones['general']['timezone'] ?? 'America/Bogota') == 'America/Bogota' ? 'selected' : '' }}>America/Bogota</option>
                                        <option value="America/Lima" {{ ($configuraciones['general']['timezone'] ?? '') == 'America/Lima' ? 'selected' : '' }}>America/Lima</option>
                                        <option value="America/Caracas" {{ ($configuraciones['general']['timezone'] ?? '') == 'America/Caracas' ? 'selected' : '' }}>America/Caracas</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Moneda</label>
                                    <select name="moneda" class="form-select">
                                        <option value="COP" {{ ($configuraciones['general']['moneda'] ?? 'COP') == 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                                        <option value="USD" {{ ($configuraciones['general']['moneda'] ?? '') == 'USD' ? 'selected' : '' }}>USD - Dólar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="configGeneral">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#configGeneral">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración MLM -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Configuración MLM / Red de Ventas
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#configMlm">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.configuracion.update-mlm') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Comisión Directa (%)</label>
                                    <input type="number" name="comision_directa" class="form-control" step="0.1" min="0" max="100"
                                           value="{{ $configuraciones['mlm']['comision_directa'] ?? 10.0 }}">
                                    <small class="text-muted">Comisión por venta directa</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Comisión por Referido (%)</label>
                                    <input type="number" name="comision_referido" class="form-control" step="0.1" min="0" max="100"
                                           value="{{ $configuraciones['mlm']['comision_referido'] ?? 3.0 }}">
                                    <small class="text-muted">Comisión por ventas de referidos</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Comisión de Líder (%)</label>
                                    <input type="number" name="comision_lider" class="form-control" step="0.1" min="0" max="100"
                                           value="{{ $configuraciones['mlm']['comision_lider'] ?? 2.0 }}">
                                    <small class="text-muted">Comisión adicional para líderes</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Bonificación Líder (%)</label>
                                    <input type="number" name="bonificacion_lider" class="form-control" step="0.1" min="0" max="100"
                                           value="{{ $configuraciones['mlm']['bonificacion_lider'] ?? 5.0 }}">
                                    <small class="text-muted">Bonificación por liderazgo</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Niveles Máximos</label>
                                    <input type="number" name="niveles_maximos" class="form-control" min="1" max="10"
                                           value="{{ $configuraciones['mlm']['niveles_maximos'] ?? 5 }}">
                                    <small class="text-muted">Profundidad máxima de la red</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Mínimo Ventas/Mes (COP)</label>
                                    <input type="number" name="minimo_ventas_mes" class="form-control" min="0"
                                           value="{{ $configuraciones['mlm']['minimo_ventas_mes'] ?? 100000 }}">
                                    <small class="text-muted">Ventas mínimas para mantener comisiones</small>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="configMlm">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Configuración MLM
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#configMlm">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración de Pedidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-cart me-2"></i>
                        Configuración de Pedidos
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#configPedidos">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.configuracion.update-pedidos') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Tiempo de Preparación (min)</label>
                                    <input type="number" name="tiempo_preparacion" class="form-control" min="5" max="180"
                                           value="{{ $configuraciones['pedidos']['tiempo_preparacion'] ?? 30 }}">
                                    <small class="text-muted">Tiempo estimado de preparación</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Costo de Envío (COP)</label>
                                    <input type="number" name="costo_envio" class="form-control" min="0"
                                           value="{{ $configuraciones['pedidos']['costo_envio'] ?? 5000 }}">
                                    <small class="text-muted">Costo base de envío</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="color: black;">Envío Gratis Desde (COP)</label>
                                    <input type="number" name="envio_gratis_desde" class="form-control" min="0"
                                           value="{{ $configuraciones['pedidos']['envio_gratis_desde'] ?? 50000 }}">
                                    <small class="text-muted">Monto mínimo para envío gratis</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6 style="color: black;">Estados de Pedidos Disponibles:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($configuraciones['pedidos']['estados_disponibles'] ?? [] as $key => $estado)
                                        <span class="badge bg-secondary">{{ $estado }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="configPedidos">
                            <div class="border-top pt-3 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Configuración de Pedidos
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#configPedidos">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notificaciones -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bell me-2"></i>
                        Configuración de Notificaciones
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#configNotif">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.configuracion.update-notificaciones') }}">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_pedidos" id="email_pedidos"
                                   {{ ($configuraciones['notificaciones']['email_pedidos'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_pedidos" style="color: black;">
                                Notificaciones de nuevos pedidos por email
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_comisiones" id="email_comisiones"
                                   {{ ($configuraciones['notificaciones']['email_comisiones'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_comisiones" style="color: black;">
                                Notificaciones de comisiones por email
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_nuevos_referidos" id="email_referidos"
                                   {{ ($configuraciones['notificaciones']['email_nuevos_referidos'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_referidos" style="color: black;">
                                Notificaciones de nuevos referidos
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="sms_pedidos_entregados" id="sms_entregados"
                                   {{ ($configuraciones['notificaciones']['sms_pedidos_entregados'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_entregados" style="color: black;">
                                SMS cuando pedidos son entregados
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="whatsapp_recordatorios" id="whatsapp_recordatorios"
                                   {{ ($configuraciones['notificaciones']['whatsapp_recordatorios'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="whatsapp_recordatorios" style="color: black;">
                                Recordatorios por WhatsApp
                            </label>
                        </div>
                        <div class="collapse" id="configNotif">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Notificaciones
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#configNotif">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-shield-check me-2"></i>
                        Estado del Sistema
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-success">✓</h4>
                                <small class="text-muted">Sistema Activo</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="{{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? 'text-success' : 'text-warning' }}">{{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? '✓' : '⚠' }}</h4>
                                <small class="text-muted">Backups {{ ($configuraciones['sistema']['backup_automatico'] ?? true) ? 'Activos' : 'Inactivos' }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-info">{{ $configuraciones['sistema']['version'] ?? '1.0.0' }}</h4>
                                <small class="text-muted">Versión</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-primary">{{ $configuraciones['sistema']['logs_dias_retention'] ?? 30 }}d</h4>
                                <small class="text-muted">Retención Logs</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-warning btn-sm" onclick="limpiarLogs()">
                                    <i class="bi bi-trash"></i> Limpiar Logs Antiguos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal para Información del Sistema -->
<div class="modal fade" id="infoSistemaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información del Sistema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="infoSistemaContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modales de confirmación para configuración --}}

{{-- Modal de confirmación para crear backup --}}
<div class="modal fade" id="backupConfirmModal" tabindex="-1" aria-labelledby="backupConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-cloud-download me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="backupConfirmModalLabel">Crear Backup del Sistema</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(23, 162, 184, 0.1);">
                        <i class="bi bi-hdd-stack text-info fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas crear un backup del sistema?</h6>
                    <p class="text-muted mb-0 small">Se creará una copia de seguridad de toda la base de datos. Este proceso puede tomar unos minutos.</p>
                </div>
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Se incluirán: usuarios, productos, pedidos, comisiones y configuraciones.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-info" id="confirmBackupBtn">
                    <i class="bi bi-download me-1"></i>
                    Crear Backup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para limpiar cache --}}
<div class="modal fade" id="cacheConfirmModal" tabindex="-1" aria-labelledby="cacheConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                <div class="d-flex align-items-center text-dark">
                    <i class="bi bi-arrow-clockwise me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="cacheConfirmModalLabel">Limpiar Cache del Sistema</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-speedometer2 text-warning fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas limpiar el cache del sistema?</h6>
                    <p class="text-muted mb-0 small">Esto mejorará el rendimiento y aplicará los cambios de configuración inmediatamente.</p>
                </div>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <small>Se limpiarán: cache de aplicación, rutas, configuración, vistas y optimizaciones.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmCacheBtn">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Limpiar Cache
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para limpiar logs --}}
<div class="modal fade" id="logsConfirmModal" tabindex="-1" aria-labelledby="logsConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-trash me-2 fs-4"></i>
                    <h5 class="modal-title mb-0" id="logsConfirmModalLabel">Limpiar Logs Antiguos</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-file-earmark-x text-danger fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">¿Deseas limpiar los logs antiguos del sistema?</h6>
                    <p class="text-muted mb-0 small">Se eliminarán todos los archivos de log con más de 30 días de antigüedad para liberar espacio.</p>
                </div>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-diamond me-2"></i>
                    <small>Esta acción no se puede deshacer. Solo se eliminarán logs antiguos (>30 días).</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmLogsBtn">
                    <i class="bi bi-trash me-1"></i>
                    Limpiar Logs
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de resultados --}}
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="resultModalHeader">
                <div class="d-flex align-items-center" id="resultModalHeaderContent">
                    <i class="bi bi-check-circle me-2 fs-4" id="resultModalIcon"></i>
                    <h5 class="modal-title mb-0" id="resultModalLabel">Resultado</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="resultModalClose"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px;" id="resultIconContainer">
                        <i class="bi bi-check-circle fs-1" id="resultIcon"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="resultTitle">Operación Completada</h6>
                    <p class="text-muted mb-0" id="resultMessage">La operación se ha completado exitosamente.</p>
                </div>
                <div id="resultDetails" class="d-none">
                    <div class="bg-light rounded p-3">
                        <h6 class="mb-2"><i class="bi bi-info-circle me-1"></i>Detalles:</h6>
                        <ul class="mb-0" id="resultDetailsList"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check me-1"></i>
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funciones de configuración -->
<script>
// Declarar funciones en el ámbito global
console.log('Configuración: Cargando funciones JavaScript...');

// Verificar que Bootstrap esté disponible
console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
// Verificación silenciosa de Bootstrap - usando fallback jQuery/nativo
if (typeof bootstrap !== 'undefined') {
    console.log('Bootstrap Modal disponible');
}

function crearBackup() {
    console.log('crearBackup() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal');
        $('#backupConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de backup');
        const modal = document.getElementById('backupConfirmModal');
        if (modal) {
            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo (no en el contenido)
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    // Cerrar modal
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    modal.style.backgroundColor = '';
                    modal.style.alignItems = '';
                    modal.style.justifyContent = '';
                    modal.removeAttribute('aria-modal');
                    modal.removeAttribute('role');

                    if (modalDialog) {
                        modalDialog.style.margin = '';
                        modalDialog.style.zIndex = '';
                        modalDialog.style.position = '';
                    }

                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            });

            console.log('Modal de backup mostrado con métodos nativos');
        } else {
            console.error('Modal backupConfirmModal no encontrado');
            alert('Error: Modal no encontrado');
        }
    }
}

// Función que se ejecuta al confirmar backup
document.addEventListener('DOMContentLoaded', function() {
    const confirmBackupBtn = document.getElementById('confirmBackupBtn');
    console.log('confirmBackupBtn encontrado:', confirmBackupBtn);

    if (confirmBackupBtn) {
        confirmBackupBtn.addEventListener('click', function() {
    // Cerrar modal de confirmación
    if (typeof $ !== 'undefined') {
        $('#backupConfirmModal').modal('hide');
    } else {
        const modal = document.getElementById('backupConfirmModal');
        const backdrop = document.getElementById('backup-backdrop');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.style.backgroundColor = '';
            modal.style.alignItems = '';
            modal.style.justifyContent = '';
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');

            // Limpiar estilos del modal-dialog
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '';
                modalDialog.style.zIndex = '';
                modalDialog.style.position = '';
            }
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    }

    // Mostrar indicador de carga en el botón
    const button = this;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Creando backup...';
    button.disabled = true;

    fetch('{{ route("admin.configuracion.backup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showResultModal(
                'success',
                'Backup Creado Exitosamente',
                'El backup del sistema se ha creado correctamente.',
                [
                    `Archivo: ${data.filename}`,
                    `Tamaño: ${data.size}`,
                    `Colecciones: ${data.collections}`,
                    `Ubicación: ${data.path}`
                ]
            );
        } else {
            showResultModal('error', 'Error al Crear Backup', data.message);
        }
    })
    .catch(error => {
        showResultModal('error', 'Error al Crear Backup', error.message);
    })
    .finally(() => {
        // Restaurar botón
        button.innerHTML = originalText;
        button.disabled = false;
    });
        });
    } else {
        console.error('confirmBackupBtn no encontrado');
    }
});

function limpiarCache() {
    console.log('limpiarCache() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal de cache');
        $('#cacheConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de cache');
        const modal = document.getElementById('cacheConfirmModal');
        if (modal) {
            console.log('Modal encontrado:', modal);
            console.log('Clases del modal antes:', modal.className);
            console.log('Estilo display antes:', modal.style.display);

            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo (no en el contenido)
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    // Cerrar modal
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    modal.style.backgroundColor = '';
                    modal.style.alignItems = '';
                    modal.style.justifyContent = '';
                    modal.removeAttribute('aria-modal');
                    modal.removeAttribute('role');

                    if (modalDialog) {
                        modalDialog.style.margin = '';
                        modalDialog.style.zIndex = '';
                        modalDialog.style.position = '';
                    }

                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            });

            console.log('Modal de cache mostrado con métodos nativos');
            console.log('Clases del modal después:', modal.className);
            console.log('Estilo display después:', modal.style.display);
        } else {
            console.error('Modal cacheConfirmModal no encontrado en el DOM');
            alert('Error: Modal no encontrado');
        }
    }
}

// Función que se ejecuta al confirmar limpiar cache
document.getElementById('confirmCacheBtn').addEventListener('click', function() {
    // Cerrar modal de confirmación
    if (typeof $ !== 'undefined') {
        $('#cacheConfirmModal').modal('hide');
    } else {
        const modal = document.getElementById('cacheConfirmModal');
        const backdrop = document.getElementById('cache-backdrop');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.style.backgroundColor = '';
            modal.style.alignItems = '';
            modal.style.justifyContent = '';
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');

            // Limpiar estilos del modal-dialog
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '';
                modalDialog.style.zIndex = '';
                modalDialog.style.position = '';
            }
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    }

    // Mostrar indicador de carga
    const button = this;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Limpiando...';
    button.disabled = true;

    fetch('{{ route("admin.configuracion.limpiar-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showResultModal(
                'success',
                'Cache Limpiado Exitosamente',
                'El cache del sistema se ha limpiado correctamente. La página se recargará automáticamente.',
                data.cleared.map(c => c)
            );

            // Recargar después de mostrar el modal
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showResultModal('error', 'Error al Limpiar Cache', data.message);
        }
    })
    .catch(error => {
        showResultModal('error', 'Error al Limpiar Cache', error.message);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
});

function limpiarLogs() {
    console.log('limpiarLogs() llamada');

    // Usar jQuery si está disponible
    if (typeof $ !== 'undefined') {
        console.log('Usando jQuery para mostrar modal de logs');
        $('#logsConfirmModal').modal('show');
    } else {
        // Fallback: usar métodos nativos
        console.log('Usando métodos nativos para mostrar modal de logs');
        const modal = document.getElementById('logsConfirmModal');
        if (modal) {
            // Mostrar modal de forma simple y directa
            modal.classList.remove('fade');
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');

            // Asegurar que el modal-dialog esté visible
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '0';
                modalDialog.style.zIndex = '1060';
                modalDialog.style.position = 'relative';
            }

            // Deshabilitar scroll del body
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            // Cerrar modal al hacer clic en el fondo (no en el contenido)
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    // Cerrar modal
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    modal.style.backgroundColor = '';
                    modal.style.alignItems = '';
                    modal.style.justifyContent = '';
                    modal.removeAttribute('aria-modal');
                    modal.removeAttribute('role');

                    if (modalDialog) {
                        modalDialog.style.margin = '';
                        modalDialog.style.zIndex = '';
                        modalDialog.style.position = '';
                    }

                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            });

            console.log('Modal de logs mostrado con métodos nativos');
        } else {
            console.error('Modal logsConfirmModal no encontrado');
            alert('Error: Modal no encontrado');
        }
    }
}

// Función que se ejecuta al confirmar limpiar logs
document.getElementById('confirmLogsBtn').addEventListener('click', function() {
    // Cerrar modal de confirmación
    if (typeof $ !== 'undefined') {
        $('#logsConfirmModal').modal('hide');
    } else {
        const modal = document.getElementById('logsConfirmModal');
        const backdrop = document.getElementById('logs-backdrop');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.style.backgroundColor = '';
            modal.style.alignItems = '';
            modal.style.justifyContent = '';
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');

            // Limpiar estilos del modal-dialog
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.margin = '';
                modalDialog.style.zIndex = '';
                modalDialog.style.position = '';
            }
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    }

    // Mostrar indicador de carga
    const button = this;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Limpiando logs...';
    button.disabled = true;

    fetch('{{ route("admin.configuracion.limpiar-logs") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showResultModal(
                'success',
                'Logs Limpiados Exitosamente',
                data.message
            );
        } else {
            showResultModal('error', 'Error al Limpiar Logs', data.message);
        }
    })
    .catch(error => {
        showResultModal('error', 'Error al Limpiar Logs', error.message);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
});

function mostrarInfoSistema() {
    console.log('mostrarInfoSistema() llamada');

    // Primero mostrar el modal con loading
    const infoModal = document.getElementById('infoSistemaModal');
    console.log('infoModal encontrado:', infoModal);

    if (!infoModal) {
        console.error('Modal infoSistemaModal no encontrado');
        alert('Error: Modal no encontrado');
        return;
    }

    // Mostrar modal inmediatamente con contenido de carga
    if (typeof $ !== 'undefined') {
        $(infoModal).modal('show');
    } else {
        // Mostrar modal de forma simple y directa
        infoModal.classList.remove('fade');
        infoModal.classList.add('show');
        infoModal.style.display = 'flex';
        infoModal.style.alignItems = 'center';
        infoModal.style.justifyContent = 'center';
        infoModal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        infoModal.setAttribute('aria-modal', 'true');
        infoModal.setAttribute('role', 'dialog');

        // Asegurar que el modal-dialog esté visible
        const modalDialog = infoModal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.margin = '0';
            modalDialog.style.zIndex = '1060';
            modalDialog.style.position = 'relative';
        }

        // Deshabilitar scroll del body
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';

        // Cerrar modal al hacer clic en el fondo (no en el contenido)
        infoModal.addEventListener('click', function(e) {
            if (e.target === infoModal) {
                // Cerrar modal
                infoModal.classList.remove('show');
                infoModal.style.display = 'none';
                infoModal.style.backgroundColor = '';
                infoModal.style.alignItems = '';
                infoModal.style.justifyContent = '';
                infoModal.removeAttribute('aria-modal');
                infoModal.removeAttribute('role');

                const modalDialog = infoModal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.margin = '';
                    modalDialog.style.zIndex = '';
                    modalDialog.style.position = '';
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });
    }

    // Ahora hacer el fetch para cargar los datos
    fetch('{{ route("admin.configuracion.info-sistema") }}')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let html = '';

                // Información del Sistema
                html += `<div class="mb-4">
                    <h6 class="text-primary mb-3"><i class="bi bi-cpu me-2"></i>Información del Sistema</h6>
                    <div class="row">`;
                Object.keys(data.data.sistema).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.sistema[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                // Información de la Aplicación
                html += `<div class="mb-4">
                    <h6 class="text-success mb-3"><i class="bi bi-app me-2"></i>Información de la Aplicación</h6>
                    <div class="row">`;
                Object.keys(data.data.aplicacion).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.aplicacion[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                // Estadísticas
                html += `<div class="mb-4">
                    <h6 class="text-warning mb-3"><i class="bi bi-graph-up me-2"></i>Estadísticas de Uso</h6>
                    <div class="row">`;
                Object.keys(data.data.estadisticas).forEach(key => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>${key}:</strong>
                                <span class="text-muted">${data.data.estadisticas[key]}</span>
                            </div>
                        </div>
                    `;
                });
                html += `</div></div>`;

                document.getElementById('infoSistemaContent').innerHTML = html;
            } else {
                document.getElementById('infoSistemaContent').innerHTML =
                    '<div class="alert alert-danger">❌ Error al cargar información: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            document.getElementById('infoSistemaContent').innerHTML =
                '<div class="alert alert-danger">❌ Error de conexión: ' + error.message + '</div>';
        });

    console.log('Modal de info sistema mostrado y datos cargados');
}

// Función para mostrar modal de resultados
function showResultModal(type, title, message, details = null) {
    const modal = document.getElementById('resultModal');
    const header = document.getElementById('resultModalHeader');
    const headerContent = document.getElementById('resultModalHeaderContent');
    const modalIcon = document.getElementById('resultModalIcon');
    const close = document.getElementById('resultModalClose');
    const iconContainer = document.getElementById('resultIconContainer');
    const icon = document.getElementById('resultIcon');
    const modalTitle = document.getElementById('resultTitle');
    const modalMessage = document.getElementById('resultMessage');
    const detailsContainer = document.getElementById('resultDetails');
    const detailsList = document.getElementById('resultDetailsList');

    // Configurar colores y estilos según el tipo
    if (type === 'success') {
        header.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        headerContent.className = 'd-flex align-items-center text-white';
        modalIcon.className = 'bi bi-check-circle me-2 fs-4';
        close.className = 'btn-close btn-close-white';
        iconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        icon.className = 'bi bi-check-circle text-success fs-1';
    } else if (type === 'error') {
        header.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
        headerContent.className = 'd-flex align-items-center text-white';
        modalIcon.className = 'bi bi-exclamation-triangle me-2 fs-4';
        close.className = 'btn-close btn-close-white';
        iconContainer.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
        icon.className = 'bi bi-exclamation-triangle text-danger fs-1';
    } else if (type === 'warning') {
        header.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
        headerContent.className = 'd-flex align-items-center text-dark';
        modalIcon.className = 'bi bi-exclamation-triangle me-2 fs-4';
        close.className = 'btn-close';
        iconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
        icon.className = 'bi bi-exclamation-triangle text-warning fs-1';
    }

    // Establecer contenido
    modalTitle.textContent = title;
    modalMessage.textContent = message;

    // Manejar detalles
    if (details && details.length > 0) {
        detailsList.innerHTML = '';
        details.forEach(detail => {
            const li = document.createElement('li');
            li.textContent = detail;
            detailsList.appendChild(li);
        });
        detailsContainer.classList.remove('d-none');
    } else {
        detailsContainer.classList.add('d-none');
    }

    // Mostrar modal
    if (typeof $ !== 'undefined') {
        $(modal).modal('show');
    } else {
        // Mostrar modal de forma simple y directa
        modal.classList.remove('fade');
        modal.classList.add('show');
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');

        // Asegurar que el modal-dialog esté visible
        const modalDialog = modal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.margin = '0';
            modalDialog.style.zIndex = '1060';
            modalDialog.style.position = 'relative';
        }

        // Deshabilitar scroll del body
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';

        // Cerrar modal al hacer clic en el fondo (no en el contenido)
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                // Cerrar modal
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.style.backgroundColor = '';
                modal.style.alignItems = '';
                modal.style.justifyContent = '';
                modal.removeAttribute('aria-modal');
                modal.removeAttribute('role');

                if (modalDialog) {
                    modalDialog.style.margin = '';
                    modalDialog.style.zIndex = '';
                    modalDialog.style.position = '';
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });

        console.log('Modal de resultado mostrado con métodos nativos');
    }
}

// Asegurar que las funciones estén disponibles globalmente
window.crearBackup = crearBackup;
window.limpiarCache = limpiarCache;
window.limpiarLogs = limpiarLogs;
window.mostrarInfoSistema = mostrarInfoSistema;

console.log('Configuración: Funciones cargadas:', {
    crearBackup: typeof crearBackup,
    limpiarCache: typeof limpiarCache,
    limpiarLogs: typeof limpiarLogs,
    mostrarInfoSistema: typeof mostrarInfoSistema
});

// Confirmar que están en window
console.log('Configuración: Funciones en window:', {
    crearBackup: typeof window.crearBackup,
    limpiarCache: typeof window.limpiarCache,
    limpiarLogs: typeof window.limpiarLogs,
    mostrarInfoSistema: typeof window.mostrarInfoSistema
});

// Agregar event listeners para cerrar modales manualmente cuando Bootstrap no está disponible
function setupModalCloseHandlers() {
    // Función para cerrar modal
    function closeModal(modalId, backdropId = null) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (typeof $ !== 'undefined') {
                $(modal).modal('hide');
            } else {
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.style.backgroundColor = '';
                modal.style.alignItems = '';
                modal.style.justifyContent = '';
                modal.removeAttribute('aria-modal');
                modal.removeAttribute('role');

                // Limpiar estilos del modal-dialog
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    modalDialog.style.margin = '';
                    modalDialog.style.zIndex = '';
                    modalDialog.style.position = '';
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        }
    }

    // Event listeners para botones de cerrar
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                const modalId = modal.id;
                let backdropId;

                // Mapear backdrop según modal
                switch(modalId) {
                    case 'backupConfirmModal':
                        backdropId = 'backup-backdrop';
                        break;
                    case 'cacheConfirmModal':
                        backdropId = 'cache-backdrop';
                        break;
                    case 'logsConfirmModal':
                        backdropId = 'logs-backdrop';
                        break;
                    case 'infoSistemaModal':
                        backdropId = 'info-backdrop';
                        break;
                    case 'resultModal':
                        backdropId = 'result-backdrop';
                        break;
                }

                closeModal(modalId, backdropId);
            }
        });
    });

    // Cerrar modal al hacer click en el backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const modalId = e.target.id?.replace('-backdrop', '') + 'Modal';
            if (modalId) {
                closeModal(modalId.replace('Modal', 'Modal'), e.target.id);
            }
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modalId = openModal.id;
                let backdropId;

                switch(modalId) {
                    case 'backupConfirmModal':
                        backdropId = 'backup-backdrop';
                        break;
                    case 'cacheConfirmModal':
                        backdropId = 'cache-backdrop';
                        break;
                    case 'logsConfirmModal':
                        backdropId = 'logs-backdrop';
                        break;
                    case 'infoSistemaModal':
                        backdropId = 'info-backdrop';
                        break;
                    case 'resultModal':
                        backdropId = 'result-backdrop';
                        break;
                }

                closeModal(modalId, backdropId);
            }
        }
    });
}

// Configurar manejadores de cierre después de que el DOM esté listo
document.addEventListener('DOMContentLoaded', setupModalCloseHandlers);
</script>

@endsection