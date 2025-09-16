@extends('layouts.admin')

@section('title', '- Red de Referidos')
@section('page-title', 'Red de Referidos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Visualiza y administra la red de referidos MLM</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="showComingSoon('Exportar Red')">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                    <button class="btn btn-primary" onclick="showComingSoon('Análisis Avanzado')">
                        <i class="bi bi-graph-up me-1"></i>
                        Análisis Avanzado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Red -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-diagram-3 fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">156</h3>
                    <p class="text-muted mb-0 small">Total Referidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-people fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">142</h3>
                    <p class="text-muted mb-0 small">Referidos Activos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-layers fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">7</h3>
                    <p class="text-muted mb-0 small">Niveles de Profundidad</p>
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
                    <h3 class="fw-bold mb-1 text-info">23</h3>
                    <p class="text-muted mb-0 small">Nuevos este Mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-search me-2"></i>
                        Buscar en la Red
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Ver Líderes')">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Líderes (8)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Ver Vendedores')">
                                    <i class="bi bi-person-check me-2"></i>
                                    Vendedores (34)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Nuevos Referidos')">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Nuevos (23)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-secondary" onclick="showComingSoon('Buscar Usuario')">
                                    <i class="bi bi-search me-2"></i>
                                    Buscar Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Visualización de Red -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Mapa de la Red
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-5">
                        <i class="bi bi-diagram-3-fill fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Visualización de Red MLM</h5>
                        <p class="text-muted">Diagrama interactivo próximamente</p>
                        <p class="text-muted">Se integrará con D3.js para mostrar la estructura jerárquica</p>

                        <!-- Simulación simple de la estructura -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-center">
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 50px; height: 50px; background-color: var(--primary-color); color: white;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div><small>Administrador</small></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3 gap-4">
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 40px; height: 40px; background-color: rgba(114, 47, 55, 0.8); color: white;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div><small>Líder 1</small></div>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 40px; height: 40px; background-color: rgba(114, 47, 55, 0.8); color: white;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div><small>Líder 2</small></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3 gap-2">
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                         style="width: 30px; height: 30px; background-color: rgba(114, 47, 55, 0.6); color: white;">
                                        <i class="bi bi-person" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div><small style="font-size: 0.7rem;">V1</small></div>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                         style="width: 30px; height: 30px; background-color: rgba(114, 47, 55, 0.6); color: white;">
                                        <i class="bi bi-person" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div><small style="font-size: 0.7rem;">V2</small></div>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                         style="width: 30px; height: 30px; background-color: rgba(114, 47, 55, 0.6); color: white;">
                                        <i class="bi bi-person" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div><small style="font-size: 0.7rem;">V3</small></div>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                         style="width: 30px; height: 30px; background-color: rgba(114, 47, 55, 0.6); color: white;">
                                        <i class="bi bi-person" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div><small style="font-size: 0.7rem;">V4</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Referidores -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top Referidores
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Carlos Rodríguez</div>
                            <small class="text-muted">Líder</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">28</div>
                            <small class="text-muted">referidos</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Ana López</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">19</div>
                            <small class="text-muted">referidos</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Miguel Torres</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">15</div>
                            <small class="text-muted">referidos</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Laura Martínez</div>
                            <small class="text-muted">Vendedor</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">12</div>
                            <small class="text-muted">referidos</small>
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
                        <i class="bi bi-diagram-3 fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">La visualización completa de la red MLM estará disponible próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Diagrama interactivo con D3.js</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Búsqueda avanzada en la red</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Análisis de genealogía</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Filtros por nivel y estado</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Reportes de crecimiento</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Métricas de actividad</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Exportación de datos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Análisis predictivo</li>
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