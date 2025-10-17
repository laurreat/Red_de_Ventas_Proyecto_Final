@extends('layouts.lider')

@section('title', ' - Red de Referidos')
@section('page-title', 'Red de Referidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/red-referidos-modern.css') }}?v={{ filemtime(public_path('css/lider/red-referidos-modern.css')) }}">
<style>
/* Mejoras de Filtros */
.red-filter-card {
    background: white !important;
    border-radius: 20px !important;
    padding: 2rem !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
    border: 1px solid rgba(114,47,55,0.1) !important;
    margin-bottom: 2rem;
}

.red-filter-title {
    font-size: 1.375rem !important;
    font-weight: 700 !important;
    color: #722F37 !important;
    margin-bottom: 1.5rem !important;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.red-filter-title i {
    font-size: 1.5rem;
    color: #722F37;
}

.red-form-label {
    font-size: 0.813rem !important;
    font-weight: 600 !important;
    color: #4b5563 !important;
    margin-bottom: 0.5rem !important;
    display: block;
}

.red-form-control {
    width: 100% !important;
    padding: 0.75rem 1rem !important;
    border: 1px solid #d1d5db !important;
    background: #f9fafb !important;
    border-radius: 10px !important;
    font-size: 0.938rem !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
    color: #1f2937 !important;
}

.red-form-control:hover {
    border-color: #722F37 !important;
    background: white !important;
}

.red-form-control:focus {
    border-color: #722F37 !important;
    background: white !important;
    box-shadow: 0 0 0 3px rgba(114,47,55,0.1) !important;
    outline: none !important;
}

.red-form-group {
    margin-bottom: 0 !important;
}

/* Mejoras de Botones */
.red-action-btn {
    padding: 0.75rem 1.5rem !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 0.938rem !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem !important;
    text-decoration: none !important;
    border: none !important;
    cursor: pointer !important;
    white-space: nowrap;
}

.red-action-btn-primary {
    background: #722F37 !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(114,47,55,0.25) !important;
}

.red-action-btn-primary:hover {
    background: #5a252d !important;
    box-shadow: 0 4px 12px rgba(114,47,55,0.35) !important;
    transform: translateY(-1px) !important;
    color: white !important;
}

.red-action-btn-outline {
    background: white !important;
    color: #722F37 !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
}

.red-action-btn-outline:hover {
    background: #f9fafb !important;
    border-color: #722F37 !important;
    color: #722F37 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
}

/* Botones de Tabla */
.red-action-btn-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1rem !important;
    transition: all 0.2s ease !important;
    border: none !important;
    cursor: pointer !important;
}

.red-action-btn-icon.red-action-btn-primary {
    background: #722F37 !important;
    color: white !important;
}

.red-action-btn-icon.red-action-btn-primary:hover {
    background: #5a252d !important;
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 4px 12px rgba(114,47,55,0.3) !important;
}

.red-action-btn-icon.red-action-btn-success {
    background: #10b981 !important;
    color: white !important;
}

.red-action-btn-icon.red-action-btn-success:hover {
    background: #059669 !important;
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 4px 12px rgba(16,185,129,0.3) !important;
}

/* Top Performers */
.top-performers-container {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(114,47,55,0.1);
    margin-bottom: 2rem;
}

.top-performers-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: #722F37;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.top-performers-title i {
    color: #f59e0b;
    font-size: 1.75rem;
}

.performer-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
}

.performer-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #10b981;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.8s ease;
}

.performer-card.gold::before {
    background: linear-gradient(180deg, #f59e0b, #d97706);
}

.performer-card.silver::before {
    background: linear-gradient(180deg, #64748b, #475569);
}

.performer-card.bronze::before {
    background: linear-gradient(180deg, #d97706, #b45309);
}

.performer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #722F37;
}

.performer-card:hover::before {
    width: 100%;
    opacity: 0.08;
}

.performer-rank {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 800;
    color: white;
    flex-shrink: 0;
}

.performer-rank.gold {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.performer-rank.silver {
    background: linear-gradient(135deg, #64748b, #475569);
}

.performer-rank.bronze {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.performer-rank.top {
    background: linear-gradient(135deg, #10b981, #059669);
}

.performer-info {
    flex: 1;
    padding-left: 1rem;
}

.performer-name {
    font-size: 1.063rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.performer-sales {
    font-size: 1.375rem;
    font-weight: 700;
    color: #10b981;
    margin-bottom: 0.25rem;
}

.performer-stats {
    font-size: 0.813rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.performer-stats i {
    color: #722F37;
}

@media (max-width: 768px) {
    .red-filter-card {
        padding: 1.5rem !important;
    }

    .red-form-group {
        margin-bottom: 1rem !important;
    }

    .red-action-btn {
        width: 100% !important;
    }

    .top-performers-container {
        padding: 1.5rem;
    }

    .performer-card {
        padding: 1.25rem;
    }

    .performer-rank {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
}

/* Modal Glassmorphism - Mensajes - Z-index mejorado */
#modalEnviarMensaje {
    z-index: 99999 !important;
    position: fixed !important;
}

#modalEnviarMensaje .modal-backdrop {
    z-index: 99998 !important;
    position: fixed !important;
}

.modal-backdrop.show {
    opacity: 0.7 !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    background: rgba(0, 0, 0, 0.6) !important;
}

.mensaje-modal-glass {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(25px) saturate(200%) !important;
    -webkit-backdrop-filter: blur(25px) saturate(200%) !important;
    border-radius: 24px !important;
    border: 2px solid rgba(255, 255, 255, 0.5) !important;
    box-shadow: 0 25px 70px rgba(114, 47, 55, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset,
                0 10px 40px rgba(0, 0, 0, 0.2) !important;
    overflow: hidden !important;
    position: relative !important;
    z-index: 100000 !important;
}

.mensaje-modal-header {
    background: linear-gradient(135deg,
        rgba(114, 47, 55, 0.98) 0%,
        rgba(90, 37, 45, 0.98) 100%) !important;
    backdrop-filter: blur(15px) !important;
    -webkit-backdrop-filter: blur(15px) !important;
    color: white !important;
    padding: 1.75rem 2rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 24px 24px 0 0 !important;
}

.mensaje-modal-header .modal-title {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: white !important;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.mensaje-vendedor-name {
    color: #fbbf24 !important;
    font-weight: 800 !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    margin-left: 0.5rem;
}

.mensaje-modal-body {
    padding: 2.5rem !important;
    background: rgba(255, 255, 255, 0.7) !important;
    max-height: 70vh;
    overflow-y: auto;
}

.mensaje-form-label {
    font-weight: 600 !important;
    color: #1f2937 !important;
    margin-bottom: 0.75rem !important;
    display: flex;
    align-items: center;
    font-size: 1rem !important;
}

.mensaje-form-control {
    border-radius: 12px !important;
    border: 2px solid rgba(114, 47, 55, 0.25) !important;
    padding: 0.875rem 1.25rem !important;
    font-size: 1rem !important;
    background: rgba(255, 255, 255, 0.95) !important;
    transition: all 0.3s ease !important;
    width: 100% !important;
}

.mensaje-form-control:focus {
    border-color: #722F37 !important;
    background: white !important;
    box-shadow: 0 0 0 4px rgba(114, 47, 55, 0.2) !important;
    outline: none !important;
}

.mensaje-textarea {
    resize: none !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    line-height: 1.6 !important;
    min-height: 120px !important;
}

.mensaje-contador {
    margin-top: 0.75rem !important;
    font-size: 0.875rem !important;
    color: #6b7280 !important;
    display: flex;
    align-items: center;
}

.mensaje-info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%) !important;
    border: 2px solid #bfdbfe !important;
    border-radius: 12px !important;
    padding: 1.25rem !important;
    color: #1e40af !important;
    display: flex;
    align-items: start;
    gap: 0.75rem;
    font-size: 0.938rem !important;
}

.mensaje-info-box i {
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.mensaje-modal-footer {
    padding: 1.75rem 2rem !important;
    background: rgba(249, 250, 251, 0.9) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-top: 1px solid rgba(114, 47, 55, 0.1) !important;
    border-radius: 0 0 24px 24px !important;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.mensaje-btn-cancel {
    padding: 0.875rem 2rem !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    background: rgba(107, 114, 128, 0.15) !important;
    border: 2px solid rgba(107, 114, 128, 0.4) !important;
    color: #374151 !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.mensaje-btn-cancel:hover {
    background: rgba(107, 114, 128, 0.25) !important;
    border-color: rgba(107, 114, 128, 0.6) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.mensaje-btn-send {
    padding: 0.875rem 2.5rem !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    background: linear-gradient(135deg, #722F37 0%, #5a252d 100%) !important;
    border: none !important;
    color: white !important;
    box-shadow: 0 4px 16px rgba(114, 47, 55, 0.5) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.mensaje-btn-send:hover {
    background: linear-gradient(135deg, #5a252d 0%, #722F37 100%) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(114, 47, 55, 0.6) !important;
    color: white !important;
}

.mensaje-btn-send:active {
    transform: translateY(-1px) !important;
}

/* Animación de entrada del modal mejorada */
.modal.fade .modal-dialog {
    transform: scale(0.85) translateY(-30px);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Responsive - PWA Mobile */
@media (max-width: 768px) {
    .mensaje-modal-glass {
        margin: 0.5rem !important;
        max-height: 95vh !important;
    }

    .modal-dialog.modal-lg {
        max-width: calc(100% - 1rem) !important;
        margin: 0.5rem !important;
    }

    .mensaje-modal-header {
        padding: 1.25rem 1.5rem !important;
    }

    .mensaje-modal-header .modal-title {
        font-size: 1.25rem !important;
        line-height: 1.4;
    }

    .mensaje-vendedor-name {
        display: block;
        margin-top: 0.25rem;
        margin-left: 0 !important;
    }

    .mensaje-modal-body {
        padding: 1.5rem !important;
        max-height: calc(95vh - 200px);
    }

    .mensaje-modal-footer {
        padding: 1.25rem 1.5rem !important;
        flex-direction: column;
    }

    .mensaje-btn-cancel,
    .mensaje-btn-send {
        width: 100% !important;
        padding: 1rem !important;
        justify-content: center;
    }

    .mensaje-form-control {
        font-size: 16px !important; /* Evita zoom en iOS */
    }

    .mensaje-textarea {
        min-height: 100px !important;
    }

    .mensaje-info-box {
        font-size: 0.875rem !important;
        padding: 1rem !important;
    }
}

/* PWA - Landscape mobile */
@media (max-height: 500px) and (orientation: landscape) {
    .mensaje-modal-body {
        max-height: 50vh !important;
        padding: 1rem !important;
    }

    .mensaje-textarea {
        min-height: 80px !important;
        rows: 3 !important;
    }

    .mensaje-modal-footer {
        padding: 1rem 1.5rem !important;
    }
}

/* Soporte para notch de iPhone */
@supports (padding: max(0px)) {
    .mensaje-modal-glass {
        padding-left: max(0px, env(safe-area-inset-left));
        padding-right: max(0px, env(safe-area-inset-right));
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="red-referidos-header fade-in-up">
        <div class="red-referidos-header-content">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="red-referidos-title">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Mi Red de Referidos
                    </h1>
                    <p class="red-referidos-subtitle mb-0">
                        Gestiona y monitorea el crecimiento de tu equipo
                    </p>
                </div>
                <div class="red-referidos-actions">
                    <a href="{{ route('lider.referidos.red') }}" class="red-referidos-action-btn">
                        <i class="bi bi-diagram-3-fill"></i>
                        Ver Estructura
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="red-referidos-stats-grid">
        <div class="red-referidos-stat-card fade-in-up animate-delay-1">
            <div class="red-referidos-stat-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="total_referidos">
                {{ $statsRed['total_referidos'] }}
            </div>
            <div class="red-referidos-stat-label">Total Red</div>
        </div>

        <div class="red-referidos-stat-card fade-in-up animate-delay-2">
            <div class="red-referidos-stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="activos">
                {{ $statsRed['activos'] }}
            </div>
            <div class="red-referidos-stat-label">Activos</div>
        </div>

        <div class="red-referidos-stat-card fade-in-up animate-delay-3">
            <div class="red-referidos-stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="nivel_1">
                {{ $statsRed['nivel_1'] }}
            </div>
            <div class="red-referidos-stat-label">Nivel 1</div>
        </div>

        <div class="red-referidos-stat-card fade-in-up animate-delay-4">
            <div class="red-referidos-stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="bi bi-diagram-2-fill"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="nivel_2">
                {{ $statsRed['nivel_2'] }}
            </div>
            <div class="red-referidos-stat-label">Nivel 2</div>
        </div>

        <div class="red-referidos-stat-card fade-in-up animate-delay-1">
            <div class="red-referidos-stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="ventas_totales">
                ${{ number_format($statsRed['ventas_totales'], 0, ',', '.') }}
            </div>
            <div class="red-referidos-stat-label">Ventas Red</div>
        </div>

        <div class="red-referidos-stat-card fade-in-up animate-delay-2">
            <div class="red-referidos-stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <div class="red-referidos-stat-value" data-stat="crecimiento_mensual">
                {{ $statsRed['crecimiento_mensual'] }}%
            </div>
            <div class="red-referidos-stat-label">Crecimiento</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="chart-container fade-in-up animate-delay-3">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Evolución de la Red
                    </h3>
                </div>
                <div class="chart-body">
                    <canvas id="evolucionRedChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="chart-container fade-in-up animate-delay-4">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribución por Niveles
                    </h3>
                </div>
                <div class="chart-body">
                    <canvas id="nivelesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="red-filter-card fade-in-up">
        <h3 class="red-filter-title">
            <i class="bi bi-funnel-fill"></i>
            Filtrar Referidos
        </h3>
        <form method="GET" action="{{ route('lider.referidos.index') }}">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="red-form-group">
                        <label for="search" class="red-form-label">Buscar</label>
                        <input type="text"
                               name="search"
                               id="search"
                               class="red-form-control"
                               placeholder="Nombre, email o cédula"
                               value="{{ $search }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="red-form-group">
                        <label for="nivel" class="red-form-label">Nivel</label>
                        <select name="nivel" id="nivel" class="red-form-control">
                            <option value="todos" {{ $nivel == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="directos" {{ $nivel == 'directos' ? 'selected' : '' }}>Directos</option>
                            <option value="segundo" {{ $nivel == 'segundo' ? 'selected' : '' }}>Segundo Nivel</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="red-form-group">
                        <label for="periodo" class="red-form-label">Periodo</label>
                        <select name="periodo" id="periodo" class="red-form-control">
                            <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                            <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Semana Actual</option>
                            <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre</option>
                            <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="red-form-group">
                        <label for="estado" class="red-form-label">Estado</label>
                        <select name="estado" id="estado" class="red-form-control">
                            <option value="">Todos</option>
                            <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ $estado == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12">
                    <label class="red-form-label d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="red-action-btn red-action-btn-primary flex-grow-1">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('lider.referidos.index') }}" class="red-action-btn red-action-btn-outline">
                            <i class="bi bi-arrow-clockwise"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Referidos -->
    <div class="red-referidos-table-container fade-in-up">
        <div class="red-referidos-table-header">
            <h3 class="red-referidos-table-title">
                <i class="bi bi-people me-2"></i>
                Mi Red de Referidos
            </h3>
        </div>
        <div class="table-responsive">
            <table class="red-referidos-table">
                <thead>
                    <tr>
                        <th>Referido</th>
                        <th>Nivel</th>
                        <th>Ventas</th>
                        <th>Sus Referidos</th>
                        <th>Rendimiento</th>
                        <th>Última Actividad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redConStats as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="red-referido-avatar">
                                        {{ strtoupper(substr($item['referido']->name, 0, 1)) }}
                                    </div>
                                    <div class="red-referido-info">
                                        <div class="red-referido-name">{{ $item['referido']->name }}</div>
                                        <div class="red-referido-email">{{ $item['referido']->email }}</div>
                                        <div class="red-referido-meta">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ $item['fecha_ingreso']->format('d/m/Y') }} ({{ $item['dias_activo'] }} días)
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="red-referidos-badge red-referidos-badge-nivel">
                                    Nivel {{ $item['nivel'] }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #10b981;">
                                    ${{ number_format($item['ventas_periodo'], 0, ',', '.') }}
                                </div>
                                <div style="font-size: 0.813rem; color: #6b7280;">
                                    {{ $item['pedidos_periodo'] }} pedidos
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $item['referidos_totales'] }}</div>
                                @if($item['referidos_periodo'] > 0)
                                    <div style="font-size: 0.813rem; color: #10b981;">
                                        +{{ $item['referidos_periodo'] }} este periodo
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill {{ $item['rendimiento'] >= 70 ? 'success' : ($item['rendimiento'] >= 40 ? 'warning' : 'danger') }}"
                                         style="width: {{ $item['rendimiento'] }}%">
                                        {{ round($item['rendimiento']) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item['ultima_actividad'])
                                    <div style="font-size: 0.875rem;">
                                        {{ $item['ultima_actividad']->diffForHumans() }}
                                    </div>
                                @else
                                    <span style="color: #9ca3af;">Sin actividad</span>
                                @endif
                            </td>
                            <td>
                                @if($item['referido']->activo)
                                    <span class="red-referidos-badge red-referidos-badge-success">
                                        <i class="bi bi-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="red-referidos-badge red-referidos-badge-danger">
                                        <i class="bi bi-x-circle"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('lider.equipo.show', $item['referido']->id) }}"
                                       class="red-action-btn-icon red-action-btn-primary"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Ver Detalles Completos">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <button type="button"
                                            class="red-action-btn-icon red-action-btn-success"
                                            onclick="abrirModalMensaje('{{ $item['referido']->id }}', '{{ $item['referido']->name }}')"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Enviar Mensaje al Vendedor">
                                        <i class="bi bi-chat-dots-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="red-referidos-empty-state">
                                    <div class="red-referidos-empty-icon">
                                        <i class="bi bi-person-x"></i>
                                    </div>
                                    <div class="red-referidos-empty-text">No se encontraron referidos</div>
                                    <div class="red-referidos-empty-subtext">
                                        Intenta cambiar los filtros de búsqueda
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Performers -->
    @if($topPerformers->isNotEmpty())
        <div class="top-performers-container fade-in-up">
            <h3 class="top-performers-title">
                <i class="bi bi-trophy-fill"></i>
                Top Performers del Periodo
            </h3>
            <div class="row g-3">
                @foreach($topPerformers->take(6) as $index => $performer)
                    @php
                        $rankClass = 'top';
                        $rankIcon = '★';
                        if ($index === 0) {
                            $rankClass = 'gold';
                            $rankIcon = '🥇';
                        } elseif ($index === 1) {
                            $rankClass = 'silver';
                            $rankIcon = '🥈';
                        } elseif ($index === 2) {
                            $rankClass = 'bronze';
                            $rankIcon = '🥉';
                        }
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="performer-card {{ $rankClass }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="performer-rank {{ $rankClass }}">
                                    @if($index < 3)
                                        <span style="font-size: 1.75rem;">{{ $rankIcon }}</span>
                                    @else
                                        <span>#{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="performer-info">
                                    <div class="performer-name">{{ $performer['referido']->name }}</div>
                                    <div class="performer-sales">
                                        ${{ number_format($performer['ventas_periodo'], 0, ',', '.') }}
                                    </div>
                                    <div class="performer-stats">
                                        <span>
                                            <i class="bi bi-cart-check-fill"></i>
                                            {{ $performer['pedidos_periodo'] }} pedidos
                                        </span>
                                        <span>
                                            <i class="bi bi-people-fill"></i>
                                            {{ $performer['referidos_periodo'] }} referidos
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

<!-- Modal para Enviar Mensaje con Glassmorphism - Fuera del container -->
<div class="modal fade" id="modalEnviarMensaje" tabindex="-1" aria-labelledby="modalEnviarMensajeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content mensaje-modal-glass">
            <div class="mensaje-modal-header">
                <h5 class="modal-title" id="modalEnviarMensajeLabel">
                    <i class="bi bi-envelope-fill me-2"></i>
                    Enviar Mensaje a <span id="nombreVendedor" class="mensaje-vendedor-name"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mensaje-modal-body">
                <form id="formEnviarMensaje">
                    <input type="hidden" id="vendedor_id" name="vendedor_id">

                    <div class="mb-4">
                        <label for="tipo_mensaje" class="mensaje-form-label">
                            <i class="bi bi-tag-fill me-2"></i>
                            Tipo de Mensaje
                        </label>
                        <select class="form-select mensaje-form-control" id="tipo_mensaje" name="tipo_mensaje" required>
                            <option value="">Selecciona un tipo...</option>
                            <option value="motivacion">💪 Motivación</option>
                            <option value="felicitacion">🎉 Felicitación</option>
                            <option value="recomendacion">💡 Recomendación</option>
                            <option value="alerta">⚠️ Alerta</option>
                            <option value="otro">📝 Otro</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="mensaje" class="mensaje-form-label">
                            <i class="bi bi-chat-text-fill me-2"></i>
                            Mensaje
                        </label>
                        <textarea class="form-control mensaje-form-control mensaje-textarea" id="mensaje" name="mensaje" rows="6" required
                                  placeholder="Escribe tu mensaje aquí... Dale indicaciones, motivación o feedback al vendedor."></textarea>
                        <div class="mensaje-contador">
                            <i class="bi bi-info-circle me-1"></i>
                            Máximo 1000 caracteres. <span id="contador" class="fw-bold">0</span>/1000
                        </div>
                    </div>

                    <div class="mensaje-info-box">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        <div>
                            <strong>Sugerencias:</strong> Sé específico y constructivo. Reconoce logros, da feedback claro o motiva con metas alcanzables.
                        </div>
                    </div>
                </form>
            </div>
            <div class="mensaje-modal-footer">
                <button type="button" class="btn mensaje-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn mensaje-btn-send" onclick="enviarMensaje()">
                    <i class="bi bi-send-fill me-2"></i>Enviar Mensaje
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/lider/red-referidos-modern.js') }}?v={{ filemtime(public_path('js/lider/red-referidos-modern.js')) }}"></script>
<script>
// Gráfico de Evolución de la Red
const evolucionCtx = document.getElementById('evolucionRedChart').getContext('2d');
new Chart(evolucionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucionRed->pluck('mes')) !!},
        datasets: [{
            label: 'Total Red',
            data: {!! json_encode($evolucionRed->pluck('total')) !!},
            borderColor: '#722F37',
            backgroundColor: 'rgba(114, 47, 55, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: '#722F37',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Gráfico de Distribución por Niveles
const nivelesCtx = document.getElementById('nivelesChart').getContext('2d');
const distribucionData = @json($distribucionNiveles);
new Chart(nivelesCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(distribucionData).map(key => `Nivel ${key}`),
        datasets: [{
            data: Object.values(distribucionData),
            backgroundColor: ['#722F37', '#10b981', '#3b82f6', '#f59e0b', '#ec4899'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 15, font: { size: 12 } } }
        }
    }
});

// Función para abrir modal de mensaje
function abrirModalMensaje(vendedorId, nombreVendedor) {
    document.getElementById('vendedor_id').value = vendedorId;
    document.getElementById('nombreVendedor').textContent = nombreVendedor;
    document.getElementById('mensaje').value = '';
    document.getElementById('tipo_mensaje').value = '';
    document.getElementById('contador').textContent = '0';

    const modal = new bootstrap.Modal(document.getElementById('modalEnviarMensaje'));
    modal.show();
}

// Contador de caracteres
document.getElementById('mensaje').addEventListener('input', function() {
    const contador = document.getElementById('contador');
    const caracteres = this.value.length;
    contador.textContent = caracteres;

    if (caracteres > 1000) {
        this.value = this.value.substring(0, 1000);
        contador.textContent = '1000';
    }
});

// Función para enviar mensaje
function enviarMensaje() {
    const form = document.getElementById('formEnviarMensaje');
    const vendedorId = document.getElementById('vendedor_id').value;
    const mensaje = document.getElementById('mensaje').value.trim();
    const tipoMensaje = document.getElementById('tipo_mensaje').value;

    // Validaciones
    if (!tipoMensaje) {
        Swal.fire({
            icon: 'warning',
            title: 'Tipo de mensaje requerido',
            text: 'Por favor selecciona un tipo de mensaje.',
            confirmButtonColor: '#722F37'
        });
        return;
    }

    if (!mensaje) {
        Swal.fire({
            icon: 'warning',
            title: 'Mensaje vacío',
            text: 'Por favor escribe un mensaje para el vendedor.',
            confirmButtonColor: '#722F37'
        });
        return;
    }

    if (mensaje.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Mensaje muy corto',
            text: 'El mensaje debe tener al menos 10 caracteres.',
            confirmButtonColor: '#722F37'
        });
        return;
    }

    // Preparar datos
    const formData = {
        vendedor_id: vendedorId,
        mensaje: mensaje,
        tipo_mensaje: tipoMensaje,
        _token: '{{ csrf_token() }}'
    };

    // Enviar petición AJAX
    fetch('{{ route("lider.referidos.enviar-mensaje") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEnviarMensaje'));
            modal.hide();

            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Mensaje Enviado!',
                text: data.message,
                confirmButtonColor: '#722F37',
                timer: 3000
            });

            // Limpiar formulario
            form.reset();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo enviar el mensaje.',
                confirmButtonColor: '#722F37'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'Hubo un problema al enviar el mensaje. Por favor intenta nuevamente.',
            confirmButtonColor: '#722F37'
        });
    });
}
</script>
@endpush
