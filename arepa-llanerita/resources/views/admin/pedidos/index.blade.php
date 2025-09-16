@extends('layouts.admin')

@section('title', '- Pedidos')
@section('page-title', 'Gestión de Pedidos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra todos los pedidos del sistema</p>
                </div>
                <div>
                    <a href="#" class="btn btn-primary" onclick="showComingSoon('Crear Pedido')">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Pedido
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Pedidos -->
    <div class="row mb-4">
        <div class="col-xl-2-4 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 50px; height: 50px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-cart3 fs-4" style="color: var(--primary-color);"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--primary-color);">156</h4>
                    <p class="text-muted mb-0 small">Total Pedidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2-4 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 50px; height: 50px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-clock fs-4 text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-warning">12</h4>
                    <p class="text-muted mb-0 small">Pendientes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2-4 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 50px; height: 50px; background-color: rgba(13, 202, 240, 0.1);">
                        <i class="bi bi-arrow-repeat fs-4 text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-info">8</h4>
                    <p class="text-muted mb-0 small">En Proceso</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2-4 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 50px; height: 50px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-success">132</h4>
                    <p class="text-muted mb-0 small">Entregados</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2-4 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                         style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-x-circle fs-4 text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-danger">4</h4>
                    <p class="text-muted mb-0 small">Cancelados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Rápidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros Rápidos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-warning" onclick="showComingSoon('Pedidos Pendientes')">
                                    <i class="bi bi-clock me-2"></i>
                                    Pendientes (12)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-info" onclick="showComingSoon('Pedidos en Proceso')">
                                    <i class="bi bi-arrow-repeat me-2"></i>
                                    En Proceso (8)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-success" onclick="showComingSoon('Pedidos de Hoy')">
                                    <i class="bi bi-calendar-day me-2"></i>
                                    Hoy (23)
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Esta Semana')">
                                    <i class="bi bi-calendar-week me-2"></i>
                                    Esta Semana
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-secondary" onclick="showComingSoon('Buscar Pedido')">
                                    <i class="bi bi-search me-2"></i>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pedidos Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Recientes
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Ver Todos')">
                        Ver Todos
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-5">
                        <i class="bi bi-cart-dash fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">La gestión completa de pedidos estará disponible próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Listado completo de pedidos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Filtros por estado y fecha</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Búsqueda avanzada</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Editar estados de pedidos</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Detalles completos del pedido</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Tracking de entrega</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Reportes de pedidos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Notificaciones automáticas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .col-xl-2-4 {
        flex: 0 0 auto;
        width: 20%;
    }

    @media (max-width: 1199.98px) {
        .col-xl-2-4 {
            width: 25%;
        }
    }

    .col-lg-2-4 {
        flex: 0 0 auto;
        width: 20%;
    }

    @media (max-width: 991.98px) {
        .col-lg-2-4 {
            width: 33.333333%;
        }
    }
</style>
@endsection