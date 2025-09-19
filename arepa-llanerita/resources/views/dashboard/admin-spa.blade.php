@extends('layouts.admin-spa')

@section('title', '- Dashboard Administrador')
@section('page-title', 'Dashboard Administrador')

@section('content')
<div class="spa-container">
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <!-- Dashboard Principal (por defecto visible) -->
    <div id="module-dashboard" class="spa-module active">
        <!-- Header con información del día -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-1">Panel de Control</h2>
                        <p class="text-muted mb-0">Panel de control general del sistema</p>
                        <small class="text-muted">Última actualización: <span id="last-update">{{ now()->format('d/m/Y H:i') }}</span></small>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="row mb-4" id="dashboard-metrics">
            <!-- Las métricas se cargarán dinámicamente -->
        </div>

        <!-- Gráficos y tablas del dashboard -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Evolución de Ventas</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Top Vendedores</h6>
                    </div>
                    <div class="card-body" id="top-vendedores">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Pedidos Recientes</h6>
                    </div>
                    <div class="card-body" id="pedidos-recientes">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Usuarios -->
    <div id="module-usuarios" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Gestión de Usuarios</h2>
            <button class="btn btn-primary" onclick="openUserModal('create')">
                <i class="bi bi-person-plus me-1"></i>
                Nuevo Usuario
            </button>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Buscar usuarios..." id="user-search">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="user-role-filter">
                            <option value="">Todos los roles</option>
                            <option value="administrador">Administrador</option>
                            <option value="lider">Líder</option>
                            <option value="vendedor">Vendedor</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="user-status-filter">
                            <option value="">Todos los estados</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" onclick="filterUsers()">
                            <i class="bi bi-funnel me-1"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="users-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <nav id="users-pagination">
                    <!-- Paginación dinámica -->
                </nav>
            </div>
        </div>
    </div>

    <!-- Módulo de Productos -->
    <div id="module-productos" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Gestión de Productos</h2>
            <button class="btn btn-primary" onclick="openProductModal('create')">
                <i class="bi bi-plus-circle me-1"></i>
                Nuevo Producto
            </button>
        </div>

        <!-- Grid de productos -->
        <div class="row" id="productos-grid">
            <!-- Se carga dinámicamente -->
        </div>
    </div>

    <!-- Módulo de Pedidos -->
    <div id="module-pedidos" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Gestión de Pedidos</h2>
            <div>
                <button class="btn btn-outline-primary me-2" onclick="exportPedidos()">
                    <i class="bi bi-download me-1"></i>
                    Exportar
                </button>
                <button class="btn btn-primary" onclick="openPedidoModal('create')">
                    <i class="bi bi-cart-plus me-1"></i>
                    Nuevo Pedido
                </button>
            </div>
        </div>

        <!-- Filtros de pedidos -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-select" id="pedido-estado-filter">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="en_preparacion">En Preparación</option>
                            <option value="listo">Listo</option>
                            <option value="en_camino">En Camino</option>
                            <option value="entregado">Entregado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="pedido-fecha-desde">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="pedido-fecha-hasta">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary w-100" onclick="filterPedidos()">
                            <i class="bi bi-funnel me-1"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de pedidos -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="pedidos-table">
                        <thead>
                            <tr>
                                <th>N° Pedido</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pedidos-table-body">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Reportes -->
    <div id="module-reportes" class="spa-module">
        <h2 class="h4 mb-4">Reportes de Ventas</h2>

        <!-- Filtros de reportes -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Periodo</label>
                        <select class="form-select" id="reporte-periodo">
                            <option value="hoy">Hoy</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes" selected>Este Mes</option>
                            <option value="trimestre">Este Trimestre</option>
                            <option value="ano">Este Año</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="fecha-personalizada" style="display: none;">
                        <label class="form-label">Desde</label>
                        <input type="date" class="form-control" id="reporte-fecha-desde">
                    </div>
                    <div class="col-md-3" id="fecha-personalizada-hasta" style="display: none;">
                        <label class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="reporte-fecha-hasta">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary d-block w-100" onclick="generateReport()">
                            <i class="bi bi-graph-up me-1"></i>
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido del reporte -->
        <div id="reporte-content">
            <!-- Se carga dinámicamente -->
        </div>
    </div>

    <!-- Módulo de Comisiones -->
    <div id="module-comisiones" class="spa-module">
        <h2 class="h4 mb-4">Gestión de Comisiones</h2>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning" id="comisiones-pendientes">$0</h5>
                        <p class="card-text">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success" id="comisiones-pagadas">$0</h5>
                        <p class="card-text">Pagadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary" id="comisiones-mes">$0</h5>
                        <p class="card-text">Este Mes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info" id="comisiones-total">$0</h5>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de comisiones -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Comisiones</h6>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportComisiones()">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="comisiones-table">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="comisiones-table-body">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Red de Referidos -->
    <div id="module-referidos" class="spa-module">
        <h2 class="h4 mb-4">Red de Referidos</h2>

        <!-- Visualización de la red -->
        <div class="card mb-4">
            <div class="card-body">
                <div id="referidos-tree">
                    <!-- Árbol de referidos dinámico -->
                </div>
            </div>
        </div>

        <!-- Estadísticas de referidos -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-primary" id="total-referidos">0</h5>
                        <p class="text-muted">Total Referidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-success" id="referidos-activos">0</h5>
                        <p class="text-muted">Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-info" id="referidos-mes">0</h5>
                        <p class="text-muted">Este Mes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Pedidos -->
    <div id="module-pedidos" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Gestión de Pedidos</h2>
            <div>
                <button class="btn btn-outline-primary btn-sm me-2" onclick="exportPedidos()">
                    <i class="bi bi-download me-1"></i>
                    Exportar
                </button>
                <button class="btn btn-primary" onclick="openPedidoModal('create')">
                    <i class="bi bi-plus me-1"></i>
                    Nuevo Pedido
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Buscar pedidos..." id="pedido-search">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="pedido-estado-filter">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="en_preparacion">En Preparación</option>
                            <option value="listo">Listo</option>
                            <option value="entregado">Entregado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="pedido-fecha-filter">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary" onclick="clearPedidoFilters()">
                            <i class="bi bi-x-circle me-1"></i>
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de pedidos -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N° Pedido</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pedidos-table-body">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Reportes -->
    <div id="module-reportes" class="spa-module">
        <h2 class="h4 mb-4">Reportes y Estadísticas</h2>

        <!-- Filtros de reporte -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Período</label>
                        <select class="form-select" id="reporte-periodo">
                            <option value="hoy">Hoy</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="trimestre">Este Trimestre</option>
                            <option value="año">Este Año</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="fecha-personalizada" style="display: none;">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha-desde">
                    </div>
                    <div class="col-md-3" id="fecha-personalizada-hasta" style="display: none;">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha-hasta">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button class="btn btn-primary" onclick="generateReport()">
                                <i class="bi bi-bar-chart me-1"></i>
                                Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de reportes -->
        <div id="reportes-content">
            <!-- Se genera dinámicamente -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Ventas por Período</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="reporteVentasChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Productos Más Vendidos</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="reporteProductosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Configuración -->
    <div id="module-configuracion" class="spa-module">
        <h2 class="h4 mb-4">Configuración del Sistema</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Configuración General</h6>
                    </div>
                    <div class="card-body">
                        <form id="config-general-form">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Empresa</label>
                                <input type="text" class="form-control" id="empresa-nombre">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email de Contacto</label>
                                <input type="email" class="form-control" id="empresa-email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="empresa-telefono">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Configuración de Comisiones</h6>
                    </div>
                    <div class="card-body">
                        <form id="config-comisiones-form">
                            <div class="mb-3">
                                <label class="form-label">Comisión por Venta (%)</label>
                                <input type="number" class="form-control" id="comision-venta" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comisión por Referido (%)</label>
                                <input type="number" class="form-control" id="comision-referido" step="0.1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mínimo para Retiro</label>
                                <input type="number" class="form-control" id="minimo-retiro">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuraciones adicionales -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Herramientas del Sistema</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="loadModule('respaldos')">
                                <i class="bi bi-cloud-arrow-down me-2"></i>
                                Gestionar Respaldos
                            </button>
                            <button class="btn btn-outline-info" onclick="loadModule('logs')">
                                <i class="bi bi-journal-text me-2"></i>
                                Ver Logs del Sistema
                            </button>
                            <button class="btn btn-outline-warning" onclick="clearCache()">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                Limpiar Caché
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Información del Sistema</h6>
                    </div>
                    <div class="card-body" id="system-info">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Respaldos -->
    <div id="module-respaldos" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Respaldos del Sistema</h2>
            <button class="btn btn-primary" onclick="createBackup()">
                <i class="bi bi-cloud-arrow-down me-1"></i>
                Crear Respaldo
            </button>
        </div>

        <!-- Lista de respaldos -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Respaldos Disponibles</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="respaldos-table-body">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Logs -->
    <div id="module-logs" class="spa-module">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Logs del Sistema</h2>
            <div>
                <button class="btn btn-outline-danger btn-sm me-2" onclick="clearLogs()">
                    <i class="bi bi-trash me-1"></i>
                    Limpiar Logs
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="refreshLogs()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Actualizar
                </button>
            </div>
        </div>

        <!-- Filtros de logs -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-select" id="log-level-filter">
                            <option value="">Todos los niveles</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                            <option value="debug">Debug</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="log-date-filter">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Buscar en logs..." id="log-search">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="clearLogFilters()">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de logs -->
        <div class="card">
            <div class="card-body">
                <div class="logs-container" id="logs-content" style="height: 500px; overflow-y: auto; font-family: monospace; background-color: #f8f9fa; padding: 1rem; border-radius: 0.375rem;">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo de Perfil -->
    <div id="module-perfil" class="spa-module">
        <h2 class="h4 mb-4">Mi Perfil</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Información Personal</h6>
                    </div>
                    <div class="card-body">
                        <form id="profile-form">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="profile-name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="profile-apellidos">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="profile-email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="profile-telefono">
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Cambiar Contraseña</h6>
                    </div>
                    <div class="card-body">
                        <form id="password-form">
                            <div class="mb-3">
                                <label class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirm-password">
                            </div>
                            <button type="submit" class="btn btn-warning">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@include('admin.partials.modales')

@endsection

@push('scripts')
<script src="{{ asset('js/admin-spa.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush