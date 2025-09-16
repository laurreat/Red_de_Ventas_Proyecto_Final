@extends('layouts.admin')

@section('title', '- Reportes de Ventas')
@section('page-title', 'Reportes de Ventas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Análisis detallado de las ventas del sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="showComingSoon('Exportar Reporte')">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                    <button class="btn btn-primary" onclick="showComingSoon('Generar Reporte')">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Ventas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">$2,458,000</h3>
                    <p class="text-muted mb-0 small">Ventas del Mes</p>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +12.5% vs mes anterior
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-cart-check fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">156</h3>
                    <p class="text-muted mb-0 small">Pedidos del Mes</p>
                    <small style="color: var(--primary-color);">
                        <i class="bi bi-arrow-up"></i> +8.3% vs mes anterior
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(13, 202, 240, 0.1);">
                        <i class="bi bi-graph-up fs-2 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-info">$15,780</h3>
                    <p class="text-muted mb-0 small">Ticket Promedio</p>
                    <small class="text-info">
                        <i class="bi bi-arrow-up"></i> +3.8% vs mes anterior
                    </small>
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
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">89</h3>
                    <p class="text-muted mb-0 small">Clientes Activos</p>
                    <small style="color: var(--primary-color);">
                        <i class="bi bi-arrow-up"></i> +15.2% vs mes anterior
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-calendar3 me-2"></i>
                        Filtros de Período
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="showComingSoon('Hoy')">
                                <i class="bi bi-calendar-day me-2"></i>
                                Hoy
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-primary w-100" onclick="showComingSoon('Esta Semana')">
                                <i class="bi bi-calendar-week me-2"></i>
                                Esta Semana
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="showComingSoon('Este Mes')">
                                <i class="bi bi-calendar-month me-2"></i>
                                Este Mes
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="showComingSoon('Trimestre')">
                                <i class="bi bi-calendar3 me-2"></i>
                                Trimestre
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="showComingSoon('Este Año')">
                                <i class="bi bi-calendar4 me-2"></i>
                                Este Año
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button class="btn btn-outline-secondary w-100" onclick="showComingSoon('Personalizado')">
                                <i class="bi bi-sliders me-2"></i>
                                Personalizado
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Ventas -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Tendencia de Ventas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Gráfico de Ventas</h5>
                        <p class="text-muted">Integración con Chart.js próximamente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Vendedores -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top Vendedores
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Ana López</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$485,000</div>
                            <small class="text-muted">23 pedidos</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Miguel Torres</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$420,000</div>
                            <small class="text-muted">19 pedidos</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Carlos Rodríguez</div>
                            <small class="text-muted">Líder</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">$380,000</div>
                            <small class="text-muted">15 pedidos</small>
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
                        <i class="bi bi-graph-up-arrow fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">Los reportes avanzados de ventas estarán disponibles próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Gráficos interactivos con Chart.js</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Reportes por período personalizado</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Análisis por vendedor</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Comparativas mensuales</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Exportación a PDF y Excel</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Métricas de rendimiento</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Análisis de productos más vendidos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Proyecciones de ventas</li>
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