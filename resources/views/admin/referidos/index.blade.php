@extends('layouts.admin')

@section('title', '- Red de Referidos')
@section('page-title', 'Red de Referidos MLM')

@push('styles')
<link href="{{ asset('css/admin/referidos-modern.css') }}?v={{ filemtime(public_path('css/admin/referidos-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero Profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="referidos-header animate-fade-in">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="referidos-header-title mb-0">Red MLM - Arepa la Llanerita</h2>
                        <p class="referidos-header-subtitle mb-0">Visualización y gestión avanzada de la red de referidos</p>
                    </div>
                    <div class="referidos-header-actions mt-3 mt-md-0">
                        <button type="button" class="btn btn-light" onclick="verVisualizacion()">
                            <i class="bi bi-diagram-3 me-2"></i>Vista Gráfica
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download me-2"></i>Exportar
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportarRedPDF()">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Exportar PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportarRedCSV()">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Exportar CSV
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="exportarRed()">
                                    <i class="bi bi-code-square me-2"></i>Exportar JSON (Dev)
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador Profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="referidos-search-card animate-fade-in-up animate-delay-1">
                <div class="referidos-search-header">
                    <div class="d-flex align-items-center">
                        <div class="referidos-search-icon-wrapper me-3">
                            <i class="bi bi-search" style="color: var(--wine); font-size: 1.3rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: var(--wine);">Buscar Usuario en la Red</h5>
                            <small class="text-muted">Ingrese la cédula para visualizar la red específica de un usuario</small>
                        </div>
                    </div>
                </div>
                <div class="referidos-search-body">
                    <form id="searchUserForm" data-action="search">
                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-medium">Número de Cédula</label>
                                <div class="referidos-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person-vcard"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        id="cedula_search"
                                        name="cedula"
                                        placeholder="Ej: 12345678"
                                        value="{{ request('cedula') }}"
                                        autocomplete="off"
                                        data-cedula-input>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <button type="submit" class="referidos-btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Buscar Red
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <button type="button" class="referidos-btn-outline w-100" data-action="clear-search">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Ver Red Completa
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <button type="button" class="btn btn-outline-info w-100" data-action="random-user">
                                    <i class="bi bi-shuffle me-2"></i>Aleatorio
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Usuario Seleccionado -->
                    @if($usuarioSeleccionado)
                    <div class="mt-3 referidos-user-selected">
                        <div class="d-flex align-items-start">
                            <div class="referidos-user-avatar me-3">
                                <i class="bi {{ $usuarioSeleccionado->rol === 'lider' ? 'bi-star-fill' : 'bi-person-fill' }} text-white fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                    <h6 class="mb-0 me-2">Red centrada en: <strong>{{ $usuarioSeleccionado->name }}</strong></h6>
                                    <span class="referidos-badge-{{ $usuarioSeleccionado->rol === 'lider' ? 'lider' : 'vendedor' }}">
                                        {{ ucfirst($usuarioSeleccionado->rol) }}
                                    </span>
                                </div>
                                <div class="referidos-user-info-grid">
                                    <small class="text-muted">
                                        <i class="bi bi-person-vcard me-1"></i>
                                        <strong>Cédula:</strong> {{ $usuarioSeleccionado->cedula }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-envelope me-1"></i>
                                        <strong>Email:</strong> {{ $usuarioSeleccionado->email }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <strong>Registro:</strong> {{ $usuarioSeleccionado->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de la Red -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-1">
                <div class="referidos-stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="referidos-stat-value">{{ $stats['total_vendedores'] }}</div>
                <div class="referidos-stat-label">Total Vendedores</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-2">
                <div class="referidos-stat-icon">
                    <i class="bi bi-award"></i>
                </div>
                <div class="referidos-stat-value">{{ $stats['total_lideres'] }}</div>
                <div class="referidos-stat-label">Total Líderes</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-3">
                <div class="referidos-stat-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="referidos-stat-value">{{ $stats['usuarios_con_referidos'] }}</div>
                <div class="referidos-stat-label">Con Referidos</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-4">
                <div class="referidos-stat-icon">
                    <i class="bi bi-person-x"></i>
                </div>
                <div class="referidos-stat-value">{{ $stats['usuarios_sin_referidor'] }}</div>
                <div class="referidos-stat-label">Sin Referidor</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-1">
                <div class="referidos-stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="referidos-stat-value">{{ $stats['promedio_referidos'] }}</div>
                <div class="referidos-stat-label">Promedio Referidos</div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-2">
                <div class="referidos-stat-icon">
                    <i class="bi bi-star"></i>
                </div>
                <div class="referidos-stat-value">
                    {{ $stats['red_mas_grande'] ? $stats['red_mas_grande']['referidos'] : '0' }}
                </div>
                <div class="referidos-stat-label">Mayor Red</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="referidos-filters animate-fade-in-up animate-delay-2">
                <h5 class="mb-3 fw-semibold" style="color: var(--wine);">
                    <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
                </h5>
                <form method="GET" action="{{ route('admin.referidos.index') }}">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label class="form-label">Buscar usuario</label>
                            <input type="text" class="form-control" name="search"
                                placeholder="Nombre, email o código..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label">Tipo de Usuario</label>
                            <select class="form-select" name="tipo">
                                <option value="">Todos los tipos</option>
                                <option value="vendedor" {{ $tipo == 'vendedor' ? 'selected' : '' }}>Vendedores</option>
                                <option value="lider" {{ $tipo == 'lider' ? 'selected' : '' }}>Líderes</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label">Nivel en Red</label>
                            <select class="form-select" name="nivel">
                                <option value="">Todos los niveles</option>
                                <option value="1" {{ $nivel == '1' ? 'selected' : '' }}>Nivel 1 (Raíz)</option>
                                <option value="2" {{ $nivel == '2' ? 'selected' : '' }}>Nivel 2</option>
                                <option value="3" {{ $nivel == '3' ? 'selected' : '' }}>Nivel 3</option>
                                <option value="4+" {{ $nivel == '4+' ? 'selected' : '' }}>Nivel 4+</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="referidos-btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lista de Usuarios -->
        <div class="col-lg-8 mb-4">
            <div class="referidos-table-container animate-fade-in-up animate-delay-3">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--wine);">
                        <i class="bi bi-list-ul me-2"></i>Usuarios en la Red ({{ $usuarios->total() }})
                    </h5>
                </div>
                @if($usuarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Referidor</th>
                                <th>Referidos</th>
                                <th>Código</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr data-cedula="{{ $usuario->cedula ?? '' }}">
                                <td>
                                    <div class="fw-medium">{{ $usuario->name }}</div>
                                    <small class="text-muted">{{ $usuario->email }}</small>
                                </td>
                                <td>
                                    @if($usuario->rol == 'lider')
                                    <span class="referidos-badge-lider">
                                        <i class="bi bi-star-fill"></i>Líder
                                    </span>
                                    @else
                                    <span class="referidos-badge-vendedor">
                                        <i class="bi bi-person-fill"></i>{{ ucfirst($usuario->rol) }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->referidor)
                                    <div class="fw-medium">{{ $usuario->referidor->name }}</div>
                                    <small class="text-muted">{{ $usuario->referidor->email }}</small>
                                    @else
                                    <span class="text-muted">Sin referidor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="referidos-badge-success">
                                        {{ $usuario->referidos_count_real ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="referidos-badge-code">{{ $usuario->codigo_referido ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div>{{ $usuario->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $usuario->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.referidos.show', $usuario->_id) }}"
                                            class="referidos-action-btn-view" title="Ver red">
                                            <i class="bi bi-diagram-3"></i>
                                        </a>
                                        <a href="{{ route('admin.users.show', $usuario->_id) }}"
                                            class="referidos-action-btn-profile" title="Ver perfil">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($usuarios->hasPages())
                <div class="p-4 border-top">
                    <div class="d-flex justify-content-center">
                        {{ $usuarios->appends(request()->query())->links('vendor.pagination.custom') }}
                    </div>
                </div>
                @endif
                @else
                <div class="referidos-empty-state">
                    <i class="bi bi-people referidos-empty-icon"></i>
                    <h4 class="referidos-empty-title">No hay usuarios</h4>
                    <p class="referidos-empty-text">No se encontraron usuarios que coincidan con los filtros.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Top Referidores -->
        <div class="col-lg-4 mb-4">
            <div class="referidos-top-card animate-slide-in animate-delay-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--wine);">
                        <i class="bi bi-trophy me-2"></i>Top Referidores
                    </h5>
                </div>
                <div class="p-3">
                    @if($redesActivas->count() > 0)
                    @foreach($redesActivas->take(5) as $index => $usuario)
                    <div class="referidos-top-item">
                        <div class="d-flex align-items-center">
                            <div class="referidos-top-medal">
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </div>
                            <div>
                                <div class="fw-medium">{{ $usuario->name }}</div>
                                <small class="text-muted">{{ ucfirst($usuario->rol) }}</small>
                            </div>
                        </div>
                        <div class="referidos-top-value">{{ $usuario->referidos_reales ?? 0 }}</div>
                    </div>
                    @endforeach
                    @else
                    <div class="referidos-empty-state py-4">
                        <i class="bi bi-trophy referidos-empty-icon"></i>
                        <p class="referidos-empty-text">No hay datos disponibles</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Visualización de Red -->
    @if($referidos->isNotEmpty())
    <div class="row mt-5">
        <div class="col-12">
            <div class="referidos-network-card animate-fade-in-up animate-delay-4">
                <!-- Header con mejor espaciado -->
                <div class="referidos-network-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="referidos-icon-circle">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold" style="color: var(--wine);">Visualización de Red MLM</h4>
                                <p class="mb-0 text-muted" style="font-size: 0.95rem;">Representación interactiva y dinámica de la estructura de referidos</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="referidos-tech-badge">
                                <i class="bi bi-cpu me-2"></i>D3.js Interactivo
                            </span>
                            <span class="referidos-tech-badge">
                                <i class="bi bi-graph-up me-2"></i>Tiempo Real
                            </span>
                        </div>
                    </div>
                </div>

                <div class="referidos-network-body">
                    <!-- Controles con mejor organización -->
                    <div class="row g-4 mb-5">
                        <!-- Modos de Visualización -->
                        <div class="col-lg-7 col-md-12">
                            <div class="referidos-control-card">
                                <div class="referidos-control-header">
                                    <i class="bi bi-sliders me-2"></i>
                                    <h6 class="mb-0">Modo de Visualización</h6>
                                </div>
                                <div class="referidos-control-body">
                                    <div class="referidos-view-options">
                                        <input type="radio" class="btn-check" name="viewType" id="treeView" value="tree" checked>
                                        <label class="referidos-view-option" for="treeView">
                                            <div class="referidos-view-icon">
                                                <i class="bi bi-diagram-2"></i>
                                            </div>
                                            <div class="referidos-view-info">
                                                <div class="referidos-view-title">Vista Árbol</div>
                                                <div class="referidos-view-desc">Jerarquía vertical organizada</div>
                                            </div>
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="viewType" id="forceView" value="force">
                                        <label class="referidos-view-option" for="forceView">
                                            <div class="referidos-view-icon">
                                                <i class="bi bi-diagram-3"></i>
                                            </div>
                                            <div class="referidos-view-info">
                                                <div class="referidos-view-title">Vista Fuerza</div>
                                                <div class="referidos-view-desc">Red dinámica interactiva</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Controles de Acción -->
                        <div class="col-lg-5 col-md-12">
                            <div class="referidos-control-card">
                                <div class="referidos-control-header">
                                    <i class="bi bi-gear me-2"></i>
                                    <h6 class="mb-0">Controles de Red</h6>
                                </div>
                                <div class="referidos-control-body">
                                    <button class="referidos-action-button" onclick="resetZoom()">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                        <span>Restablecer Vista</span>
                                    </button>
                                    <button class="referidos-action-button" onclick="exportSVG()">
                                        <i class="bi bi-download"></i>
                                        <span>Descargar Visualización</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda Mejorada -->
                    <div class="referidos-legend-card mb-5">
                        <div class="referidos-legend-header">
                            <i class="bi bi-palette-fill me-2"></i>
                            <h6 class="mb-0">Código de Colores de la Red MLM</h6>
                        </div>
                        <div class="referidos-legend-body">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #FFD700; border: 3px solid #FF8C00;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">Usuario Seleccionado</div>
                                            <div class="legend-color-desc">Nodo actual en vista</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #8B0000;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">🏆 Top Ventas</div>
                                            <div class="legend-color-desc">Más de 20 referidos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #B8860B;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">⭐ Top Referidos</div>
                                            <div class="legend-color-desc">10-20 referidos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #A8556A;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">✅ Vendedor Activo</div>
                                            <div class="legend-color-desc">5-10 referidos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #722F37;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">👑 Líder</div>
                                            <div class="legend-color-desc">Rol de líder</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #C89FA6;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">👤 Vendedor</div>
                                            <div class="legend-color-desc">1-5 referidos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="legend-color-item">
                                        <div class="legend-color-circle" style="background: #E8D5D9;"></div>
                                        <div class="legend-color-info">
                                            <div class="legend-color-title">Cliente/Inactivo</div>
                                            <div class="legend-color-desc">Sin referidos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="referidos-legend-footer">
                            <i class="bi bi-info-circle me-2"></i>
                            <span><strong>Tip:</strong> Haz clic en cualquier nodo para ver los detalles completos del usuario y su red de referidos.</span>
                        </div>
                    </div>

                    <!-- Contenedor de Red con mejor altura -->
                    <div class="referidos-visualization-wrapper">
                        <div id="referidos-network-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Red con mejor espaciado -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="referidos-metrics-card">
                <div class="referidos-metrics-header">
                    <i class="bi bi-graph-up-arrow me-2"></i>
                    <h6 class="mb-0">Métricas de la Red</h6>
                </div>
                <div class="row g-4 text-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="metric-value" id="total-nodes">0</div>
                            <div class="metric-label">Nodos Totales</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="bi bi-link-45deg"></i>
                            </div>
                            <div class="metric-value" id="total-connections">0</div>
                            <div class="metric-label">Conexiones</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <div class="metric-value" id="max-depth">0</div>
                            <div class="metric-label">Niveles de Profundidad</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div class="metric-value" id="avg-referrals">0</div>
                            <div class="metric-label">Promedio Referidos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- D3.js Library (CDN con fallback) -->
<script src="https://d3js.org/d3.v7.min.js"
        onerror="console.error('D3.js CDN failed, loading from backup...')"></script>

@endsection

@push('scripts')
<script>
    // Configurar rutas globales para el manager
    window.routes = {
        index: '{{ route("admin.referidos.index") }}',
        export: '{{ route("admin.referidos.exportar") }}',
        show: '{{ url("admin/referidos") }}/:id'
    };

    // Datos para visualización
    window.redData = {!! json_encode($redJerarquica ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
    window.usuarioSeleccionado = {!! json_encode($usuarioSeleccionado ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
</script>

<!-- Scripts de red de referidos -->
<script src="{{ asset('js/admin/referidos-network-visualization.js') }}?v={{ filemtime(public_path('js/admin/referidos-network-visualization.js')) }}"></script>
<script src="{{ asset('js/admin/referidos-modern.js') }}?v={{ filemtime(public_path('js/admin/referidos-modern.js')) }}"></script>

<script>
// Inicializar visualización de red cuando D3 esté disponible
document.addEventListener('DOMContentLoaded', function() {
    if (typeof d3 !== 'undefined' && window.NetworkVisualization) {
        window.NetworkVisualization.initializeDataFromGlobals(window.redData, window.usuarioSeleccionado);
        const container = document.getElementById('referidos-network-container');
        if (container) {
            window.NetworkVisualization.initializeVisualization();
        }

        // Event listeners para cambio de vista
        document.querySelectorAll('input[name="viewType"]').forEach(input => {
            input.addEventListener('change', function() {
                // Actualizar clases activas en los botones de vista
                document.querySelectorAll('.referidos-view-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector(`label[for="${this.id}"]`).classList.add('active');

                // Cambiar vista D3.js
                window.NetworkVisualization.changeViewType(this.value);
            });
        });
    }
});

// Funciones de utilidad para visualización
function resetZoom() {
    window.NetworkVisualization?.resetZoom?.();
}

function exportSVG() {
    window.NetworkVisualization?.exportSVG?.();
}
</script>
@endpush
