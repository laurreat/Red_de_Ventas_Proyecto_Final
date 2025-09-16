@extends('layouts.admin')

@section('title', '- Comisiones')
@section('page-title', 'Gestión de Comisiones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra las comisiones del sistema MLM</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="showComingSoon('Procesar Comisiones')">
                        <i class="bi bi-gear me-1"></i>
                        Procesar Comisiones
                    </button>
                    <button class="btn btn-primary" onclick="showComingSoon('Configurar Comisiones')">
                        <i class="bi bi-sliders me-1"></i>
                        Configuración
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Comisiones -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-cash-stack fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">$425,000</h3>
                    <p class="text-muted mb-0 small">Comisiones del Mes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-clock fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">$125,000</h3>
                    <p class="text-muted mb-0 small">Pendientes de Pago</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">$300,000</h3>
                    <p class="text-muted mb-0 small">Pagadas este Mes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">28</h3>
                    <p class="text-muted mb-0 small">Vendedores Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-lightning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-warning" onclick="showComingSoon('Comisiones Pendientes')">
                                    <i class="bi bi-clock me-2"></i>
                                    Pendientes ($125,000)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-success" onclick="showComingSoon('Procesar Pagos')">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Procesar Pagos
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Historial de Pagos')">
                                    <i class="bi bi-clock-history me-2"></i>
                                    Historial de Pagos
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-secondary" onclick="showComingSoon('Exportar Reporte')">
                                    <i class="bi bi-download me-2"></i>
                                    Exportar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Configuración de Comisiones -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-sliders me-2"></i>
                        Configuración Actual
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 style="color: var(--primary-color);">5%</h4>
                                <small class="text-muted">Comisión Directa</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 style="color: var(--primary-color);">3%</h4>
                                <small class="text-muted">Comisión de Referido</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 style="color: var(--primary-color);">2%</h4>
                                <small class="text-muted">Comisión de Líder</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded mb-3">
                                <h4 style="color: var(--primary-color);">15°</h4>
                                <small class="text-muted">Día de Pago</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary" onclick="showComingSoon('Editar Configuración')">
                            <i class="bi bi-gear me-2"></i>
                            Editar Configuración
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Comisionistas -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top Comisionistas del Mes
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Ana López</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$24,250</div>
                            <small class="text-success">+18.5%</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Carlos Rodríguez</div>
                            <small class="text-muted">Líder</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$19,000</div>
                            <small class="text-success">+12.3%</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Miguel Torres</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$17,800</div>
                            <small class="text-success">+9.7%</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Laura Martínez</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$15,200</div>
                            <small class="text-success">+15.1%</small>
                        </div>
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
                        <i class="bi bi-cash-coin fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">El sistema completo de comisiones estará disponible próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Cálculo automático de comisiones</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Configuración flexible de porcentajes</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Procesamiento masivo de pagos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Historial detallado</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Reportes de comisiones</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Notificaciones automáticas</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Integración con bancos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Auditoría de comisiones</li>
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