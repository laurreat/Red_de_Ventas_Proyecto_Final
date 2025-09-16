@extends('layouts.admin')

@section('title', '- Productos')
@section('page-title', 'Gestión de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Administra el catálogo completo de productos</p>
                </div>
                <div>
                    <a href="#" class="btn btn-primary" onclick="showComingSoon('Crear Producto')">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Productos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-boxes fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">18</h3>
                    <p class="text-muted mb-0 small">Total Productos</p>
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
                    <h3 class="fw-bold mb-1 text-success">16</h3>
                    <p class="text-muted mb-0 small">En Stock</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">2</h3>
                    <p class="text-muted mb-0 small">Stock Bajo</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-tags fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">8</h3>
                    <p class="text-muted mb-0 small">Categorías</p>
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
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Lista de Productos')">
                                    <i class="bi bi-list me-2"></i>
                                    Ver Todos los Productos
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Gestión de Categorías')">
                                    <i class="bi bi-tags me-2"></i>
                                    Gestionar Categorías
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Control de Inventario')">
                                    <i class="bi bi-box-seam me-2"></i>
                                    Control de Inventario
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="showComingSoon('Importar Productos')">
                                    <i class="bi bi-upload me-2"></i>
                                    Importar Productos
                                </button>
                            </div>
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
                        <i class="bi bi-gear fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Módulo en Desarrollo</h4>
                        <p class="text-muted">La gestión completa de productos estará disponible próximamente.</p>
                        <p class="text-muted"><strong>Funcionalidades planeadas:</strong></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>CRUD completo de productos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Gestión de categorías</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Control de inventario</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Carga de imágenes</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Precios y descuentos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Variantes de productos</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Importación masiva</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Reportes de inventario</li>
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