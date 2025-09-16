@extends('layouts.admin')

@section('title', '- Configuración')
@section('page-title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Configuración general del sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="showComingSoon('Importar Configuración')">
                        <i class="bi bi-upload me-1"></i>
                        Importar
                    </button>
                    <button class="btn btn-primary" onclick="showComingSoon('Guardar Cambios')">
                        <i class="bi bi-check me-1"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración General -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Configuración General
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Nombre de la Empresa</label>
                                <input type="text" class="form-control" value="Arepa la Llanerita" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Teléfono</label>
                                <input type="text" class="form-control" value="+57 300 123 4567" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" class="form-control" value="info@arepallanerita.com" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Moneda</label>
                                <select class="form-select" disabled>
                                    <option selected>COP - Peso Colombiano</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Zona Horaria</label>
                                <select class="form-select" disabled>
                                    <option selected>America/Bogota</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Idioma</label>
                                <select class="form-select" disabled>
                                    <option selected>Español</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Zonas de Entrega -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-geo-alt me-2"></i>
                        Zonas de Entrega
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Agregar Zona')">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">Centro de Bogotá</div>
                                <small class="text-muted">Costo: $5,000 - Tiempo: 30-45 min</small>
                            </div>
                            <span class="badge bg-success">Activa</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">Norte de Bogotá</div>
                                <small class="text-muted">Costo: $7,000 - Tiempo: 45-60 min</small>
                            </div>
                            <span class="badge bg-success">Activa</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">Sur de Bogotá</div>
                                <small class="text-muted">Costo: $8,000 - Tiempo: 60-90 min</small>
                            </div>
                            <span class="badge bg-warning">Limitada</span>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary" onclick="showComingSoon('Gestionar Zonas')">
                            Gestionar Todas las Zonas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cupones y Promociones -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-ticket-perforated me-2"></i>
                        Cupones Activos
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Crear Cupón')">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">BIENVENIDO10</div>
                                <small class="text-muted">10% descuento - Nuevos clientes</small>
                            </div>
                            <span class="badge bg-success">Activo</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">VERANO2024</div>
                                <small class="text-muted">15% descuento - Promoción verano</small>
                            </div>
                            <span class="badge bg-warning">Próximamente</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-medium">REFERIDO5</div>
                                <small class="text-muted">$5,000 descuento - Por referido</small>
                            </div>
                            <span class="badge bg-success">Activo</span>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary" onclick="showComingSoon('Gestionar Cupones')">
                            Gestionar Todos los Cupones
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notificaciones -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bell me-2"></i>
                        Configuración de Notificaciones
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label">
                            Notificaciones de nuevos pedidos
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label">
                            Alertas de stock bajo
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label">
                            Recordatorios de comisiones
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label">
                            Notificaciones por email
                        </label>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary" onclick="showComingSoon('Configurar Notificaciones')">
                            Configurar Notificaciones
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguridad -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-shield-check me-2"></i>
                        Seguridad del Sistema
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 class="text-success">✓</h4>
                                <small class="text-muted">SSL Activado</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 class="text-success">✓</h4>
                                <small class="text-muted">Backups Diarios</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 class="text-warning">⚠</h4>
                                <small class="text-muted">2FA Opcional</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 class="text-success">✓</h4>
                                <small class="text-muted">Logs Activos</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-outline-secondary" onclick="showComingSoon('Configurar Seguridad')">
                            Configurar Seguridad
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de Desarrollo -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center py-4">
                        <i class="bi bi-sliders fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">Las configuraciones avanzadas estarán disponibles próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Configuración completa de empresa</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Gestión de zonas de entrega</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Sistema de cupones avanzado</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Configuración de notificaciones</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Configuración de seguridad</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Backup automático</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Logs del sistema</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Integración con APIs</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection