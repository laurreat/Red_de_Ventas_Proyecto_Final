@extends('layouts.admin')

@section('title', 'Configuración del Sistema')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/configuracion.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Configuración del Sistema</h2>
                    <h4 class="text-muted mb-0">Configuración general del sistema</h4>
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
                                    <textarea name="direccion_empresa" class="form-control" rows="4">{{ $configuraciones['general']['direccion_empresa'] ?? 'Calle 123 #45-67, Bogotá, Colombia' }}</textarea>
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

{{-- Scripts de configuración organizados en módulos --}}
<script src="{{ asset('js/admin/configuracion-system-functions.js') }}"></script>
<script src="{{ asset('js/admin/configuracion-modal-handlers.js') }}"></script>
<script src="{{ asset('js/admin/configuracion-event-handlers.js') }}"></script>

<script>
    // Configurar variables globales para los módulos
    window.configuracionRoutes = {
        backup: '{{ route("admin.configuracion.backup") }}',
        limpiarCache: '{{ route("admin.configuracion.limpiar-cache") }}',
        limpiarLogs: '{{ route("admin.configuracion.limpiar-logs") }}',
        infoSistema: '{{ route("admin.configuracion.info-sistema") }}'
    };
    window.configuracionCSRF = '{{ csrf_token() }}';
</script>

@endsection