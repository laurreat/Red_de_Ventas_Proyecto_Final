@extends('layouts.admin')

@section('title', '- Red de Referidos')
@section('page-title', 'Red de Referidos MLM')

@section('content')
<div class="container-fluid">
    <!-- Header Profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold text-white">Red MLM - Arepa la Llanerita</h2>
                            <p class="text-white-50 mb-0">Visualización y gestión avanzada de la red de referidos</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light me-2" onclick="verVisualizacion()">
                                <i class="bi bi-diagram-3 me-1"></i>
                                Vista Gráfica
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-download me-1"></i>
                                    Exportar
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
    </div>

    {{-- Mensajes flash manejados por AdminAlerts en admin-functions.js --}}

    <!-- Buscador Profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background-color: rgba(114, 47, 55, 0.1);">
                            <i class="bi bi-search" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">Buscar Usuario en la Red</h5>
                            <small class="text-muted">Ingrese la cédula para visualizar la red específica de un usuario</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="searchUserForm" onsubmit="searchUserNetwork(event)">
                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-medium">Número de Cédula</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person-vcard text-muted"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control border-start-0"
                                        id="cedula_search"
                                        name="cedula"
                                        placeholder="Ej: 12345678"
                                        value="{{ request('cedula') }}"
                                        style="border-left: none !important;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                                    <i class="bi bi-search me-2"></i>
                                    Buscar Red
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="clearSearch()">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                                    Ver Red Completa
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <button type="button" class="btn btn-outline-info w-100" onclick="showRandomUser()">
                                    <i class="bi bi-shuffle me-2"></i>
                                    Aleatorio
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Usuario Seleccionado -->
                    @if($usuarioSeleccionado)
                    <div class="mt-3">
                        <div class="alert border-0 shadow-sm {{ $usuarioSeleccionado->rol === 'lider' ? 'alert-warning' : 'alert-info' }}">
                            <div class="d-flex align-items-start">
                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px; background-color: {{ $usuarioSeleccionado->rol === 'lider' ? 'rgba(255, 193, 7, 0.1)' : 'rgba(13, 110, 253, 0.1)' }};">
                                    <i class="bi {{ $usuarioSeleccionado->rol === 'lider' ? 'bi-star-fill' : 'bi-person-fill' }} fs-3"
                                        style="color: {{ $usuarioSeleccionado->rol === 'lider' ? '#ffc107' : '#0d6efd' }};"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="mb-0 me-2">Red centrada en: <strong>{{ $usuarioSeleccionado->name }}</strong></h6>
                                        <span class="badge bg-{{ $usuarioSeleccionado->rol === 'lider' ? 'warning' : 'primary' }}">
                                            {{ ucfirst($usuarioSeleccionado->rol) }}
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-person-vcard me-1"></i>
                                                <strong>Cédula:</strong> {{ $usuarioSeleccionado->cedula }}
                                            </small>
                                        </div>
                                        <div class="col-md-5">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-envelope me-1"></i>
                                                <strong>Email:</strong> {{ $usuarioSeleccionado->email }}
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <strong>Registro:</strong> {{ $usuarioSeleccionado->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        @if($usuarioSeleccionado->rol === 'vendedor')
                                        <div class="alert alert-light border py-2 px-3 mb-0">
                                            <small>
                                                <i class="bi bi-info-circle me-1"></i>
                                                <strong>Visualización para Vendedor:</strong> Se muestra el líder directo, hermanos y referidos propios para control completo de la red.
                                            </small>
                                        </div>
                                        @else
                                        <div class="alert alert-light border py-2 px-3 mb-0">
                                            <small>
                                                <i class="bi bi-star me-1"></i>
                                                <strong>Visualización para Líder:</strong> Se muestra la línea ascendente completa, hermanos y toda la descendencia.
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body p-4">
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
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de la Red -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $stats['total_vendedores'] }}</h3>
                    <p class="text-muted mb-0 small">Total Vendedores</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-award fs-2 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">{{ $stats['total_lideres'] }}</h3>
                    <p class="text-muted mb-0 small">Total Líderes</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-diagram-3 fs-2 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['usuarios_con_referidos'] }}</h3>
                    <p class="text-muted mb-0 small">Con Referidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-person-x fs-2 text-danger"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ $stats['usuarios_sin_referidor'] }}</h3>
                    <p class="text-muted mb-0 small">Sin Referidor</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-graph-up fs-2 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-primary">{{ $stats['promedio_referidos'] }}</h3>
                    <p class="text-muted mb-0 small">Promedio Referidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-star fs-2" style="color: var(--primary-color);"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">
                        {{ $stats['red_mas_grande'] ? $stats['red_mas_grande']['referidos'] : '0' }}
                    </h3>
                    <p class="text-muted mb-0 small">Mayor Red</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lista de Usuarios en la Red -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list-ul me-2"></i>
                        Usuarios en la Red ({{ $usuarios->total() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($usuarios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Referidor</th>
                                    <th>Referidos Directos</th>
                                    <th>Código Referido</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $usuario->name }}</div>
                                            <small class="text-muted">{{ $usuario->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($usuario->rol == 'lider')
                                        <span class="badge bg-warning text-dark">Líder</span>
                                        @else
                                        <span class="badge bg-primary">{{ ucfirst($usuario->rol) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($usuario->referidor)
                                        <div>
                                            <div class="fw-medium">{{ $usuario->referidor->name }}</div>
                                            <small class="text-muted">{{ $usuario->referidor->email }}</small>
                                        </div>
                                        @else
                                        <span class="text-muted">Sin referidor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $usuario->referidos->count() }}</span>
                                    </td>
                                    <td>
                                        <code class="bg-light p-1 rounded">{{ $usuario->codigo_referido ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        <div>
                                            <div>{{ $usuario->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $usuario->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.referidos.show', $usuario->_id) }}"
                                                class="btn btn-sm btn-outline-info" title="Ver red">
                                                <i class="bi bi-diagram-3"></i>
                                            </a>
                                            <a href="{{ route('admin.users.show', $usuario->_id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                                <i class="bi bi-person"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="p-4">
                        {{ $usuarios->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">No hay usuarios</h4>
                        <p class="text-muted">No se encontraron usuarios que coincidan con los filtros.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar con información adicional -->
        <div class="col-lg-4 mb-4">
            <!-- Top Referidores -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-trophy me-2"></i>
                        Top Referidores
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($redesActivas->count() > 0)
                    @foreach($redesActivas->take(5) as $index => $usuario)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($index == 0)
                                <div class="badge bg-warning text-dark fs-6">🥇</div>
                                @elseif($index == 1)
                                <div class="badge bg-secondary fs-6">🥈</div>
                                @elseif($index == 2)
                                <div class="badge bg-danger fs-6">🥉</div>
                                @else
                                <div class="badge bg-light text-dark">{{ $index + 1 }}</div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-medium">{{ $usuario->name }}</div>
                                <small class="text-muted">{{ ucfirst($usuario->rol) }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-color);">{{ $usuario->total_referidos ?? 0 }}</div>
                            <small class="text-muted">referidos</small>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-trophy fs-1"></i>
                        <p class="mt-2">No hay datos disponibles</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Red Más Grande -->
            @if($stats['red_mas_grande'])
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-star me-2"></i>
                        Red Más Grande
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-diagram-3 fs-1" style="color: var(--primary-color);"></i>
                    </div>
                    <h6 class="fw-semibold">{{ $stats['red_mas_grande']['usuario'] }}</h6>
                    <p class="text-muted mb-3">Líder de Red</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="fw-semibold mb-1" style="color: var(--primary-color);">{{ $stats['red_mas_grande']['referidos'] }}</h6>
                                <small class="text-muted">Referidos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-semibold mb-1 text-success">Activa</h6>
                            <small class="text-muted">Estado</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Visualización Jerárquica (muestra solo algunos niveles por performance) -->
    @if($referidos->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom-0" style="background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px; background: linear-gradient(135deg, #722f37, #8b3c44); box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);">
                                <i class="bi bi-diagram-3 text-white fs-5"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold" style="color: var(--primary-color);">
                                    Visualización de Red MLM
                                </h5>
                                <small class="text-muted">Representación interactiva y dinámica de la estructura de referidos</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background: linear-gradient(45deg, #0d6efd, #6610f2); color: white; padding: 8px 12px;">
                                <i class="bi bi-cpu me-1"></i>
                                D3.js Interactivo
                            </span>
                            <span class="badge" style="background: linear-gradient(45deg, #198754, #20c997); color: white; padding: 8px 12px;">
                                <i class="bi bi-graph-up me-1"></i>
                                Tiempo Real
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Controles de visualización profesionales -->
                    <div class="row mb-4">
                        <div class="col-lg-8 col-md-6">
                            <div class="card border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2 fw-semibold" style="color: var(--primary-color);">
                                        <i class="bi bi-sliders me-2"></i>Modo de Visualización
                                    </h6>
                                    <div class="btn-group w-100" role="group" aria-label="Tipo de visualización">
                                        <input type="radio" class="btn-check" name="viewType" id="treeView" value="tree" checked>
                                        <label class="btn btn-outline-primary" for="treeView" style="border-color: var(--primary-color); color: var(--primary-color);">
                                            <i class="bi bi-diagram-2 me-2"></i>
                                            <div>
                                                <div class="fw-medium">Vista Árbol</div>
                                                <small class="text-muted d-block">Estructura jerárquica</small>
                                            </div>
                                        </label>

                                        <input type="radio" class="btn-check" name="viewType" id="forceView" value="force">
                                        <label class="btn btn-outline-primary" for="forceView" style="border-color: var(--primary-color); color: var(--primary-color);">
                                            <i class="bi bi-diagram-3 me-2"></i>
                                            <div>
                                                <div class="fw-medium">Vista Fuerza</div>
                                                <small class="text-muted d-block">Simulación física</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2 fw-semibold" style="color: var(--primary-color);">
                                        <i class="bi bi-gear me-2"></i>Controles
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="resetZoom()">
                                            <i class="bi bi-arrows-angle-expand me-2"></i>Restablecer Zoom
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="exportSVG()">
                                            <i class="bi bi-download me-2"></i>Descargar SVG
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda Profesional -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-left: 4px solid var(--primary-color) !important;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <h6 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                                                <i class="bi bi-palette me-2"></i>Leyenda de Colores
                                            </h6>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="d-flex flex-wrap gap-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 shadow-sm"
                                                        style="width: 16px; height: 16px; background: linear-gradient(135deg, #722f37, #8b3c44);"></div>
                                                    <span class="fw-medium" style="color: #722f37;">Líder</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 shadow-sm"
                                                        style="width: 16px; height: 16px; background: linear-gradient(135deg, #0d6efd, #6610f2);"></div>
                                                    <span class="fw-medium" style="color: #0d6efd;">Vendedor</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 shadow-sm"
                                                        style="width: 16px; height: 16px; background: linear-gradient(135deg, #198754, #20c997);"></div>
                                                    <span class="fw-medium" style="color: #198754;">Más de 5 referidos</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 shadow-sm"
                                                        style="width: 16px; height: 16px; background: linear-gradient(135deg, #ffc107, #fd7e14);"></div>
                                                    <span class="fw-medium" style="color: #fd7e14;">Usuario seleccionado</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor de visualización profesional -->
                    <div class="position-relative">
                        <div id="network-container"
                            style="height: 600px;
                                    border: 2px solid #dee2e6;
                                    border-radius: 12px;
                                    position: relative;
                                    overflow: hidden;
                                    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                            <!-- El gráfico D3.js se renderizará aquí -->
                        </div>

                        <!-- Indicador de carga profesional -->
                        <div id="loading-indicator" class="position-absolute top-50 start-50 translate-middle" style="display: none;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status" style="color: var(--primary-color) !important;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <div class="fw-medium" style="color: var(--primary-color);">Generando visualización...</div>
                                <small class="text-muted">Procesando datos de la red MLM</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tooltip -->
                    <div id="network-tooltip" style="position: absolute; pointer-events: none; background: rgba(0,0,0,0.8); color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; opacity: 0; z-index: 1000; transition: opacity 0.2s;"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Métricas de Red Compactas -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="row text-center">
                        <div class="col-lg-3 col-6 mb-2">
                            <h5 class="mb-0 fw-bold" style="color: var(--primary-color);" id="total-nodes">0</h5>
                            <small class="text-muted">Nodos</small>
                        </div>
                        <div class="col-lg-3 col-6 mb-2">
                            <h5 class="mb-0 fw-bold text-success" id="total-connections">0</h5>
                            <small class="text-muted">Conexiones</small>
                        </div>
                        <div class="col-lg-3 col-6 mb-2">
                            <h5 class="mb-0 fw-bold text-info" id="max-depth">0</h5>
                            <small class="text-muted">Niveles</small>
                        </div>
                        <div class="col-lg-3 col-6 mb-2">
                            <h5 class="mb-0 fw-bold text-warning" id="avg-referrals">0</h5>
                            <small class="text-muted">Prom. Referidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer optimizado -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
                </small>
                <small class="text-muted">
                    <i class="bi bi-people me-1"></i>{{ $stats['total_vendedores'] + $stats['total_lideres'] }} usuarios
                </small>
            </div>
        </div>
    </div>
</div>

<!-- D3.js Library -->
<script src="https://d3js.org/d3.v7.min.js"></script>

<!-- Scripts de red de referidos organizados en módulos -->
<script src="{{ asset('js/admin/referidos-network-visualization.js') }}"></script>
<script src="{{ asset('js/admin/referidos-search-functions.js') }}"></script>
<script src="{{ asset('js/admin/referidos-export-functions.js') }}"></script>

<script>
    // Datos para visualización (desde el controlador)
    const redDataFromServer = {!! json_encode($redJerarquica ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
    const usuarioSeleccionadoFromServer = {!! json_encode($usuarioSeleccionado ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

    // Configurar rutas de exportación
    window.exportRoutes = {
        pdf: '{{ route("admin.referidos.exportar") }}',
        csv: '{{ route("admin.referidos.exportar") }}'
    };

    // Función para abrir detalles de usuario
    window.openUserDetails = function(nodeData) {
        const baseUrl = '{{ url("admin/referidos") }}';
        window.open(baseUrl + '/' + nodeData.id, '_blank');
    };

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, inicializando red de referidos...');
        console.log('D3.js available:', typeof d3 !== 'undefined');

        if (typeof d3 === 'undefined') {
            console.error('D3.js not loaded! Check internet connection or CDN.');
            const errorContainer = document.getElementById('network-container');
            if (errorContainer) {
                errorContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dc3545; flex-direction: column;">' +
                    '<i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>' +
                    '<p style="margin-top: 1rem;">Error: D3.js no se pudo cargar</p>' +
                    '<small>Verifique su conexión a internet</small>' +
                    '</div>';
            }
            return;
        }

        // Inicializar módulos
        if (window.NetworkVisualization) {
            window.NetworkVisualization.initializeDataFromGlobals(redDataFromServer, usuarioSeleccionadoFromServer);

            const container = document.getElementById('network-container');
            if (container) {
                console.log('Initializing visualization...');
                window.NetworkVisualization.initializeVisualization();
            } else {
                console.log('No network-container found, visualization section may not be displayed');
            }

            // Event listeners para cambio de vista
            document.querySelectorAll('input[name="viewType"]').forEach(input => {
                input.addEventListener('change', function() {
                    window.NetworkVisualization.changeViewType(this.value);
                });
            });
        }

        // Configurar validación en tiempo real para búsqueda
        if (window.ReferidosSearch) {
            window.ReferidosSearch.setupRealTimeValidation();
        }

        // Mostrar información del usuario seleccionado si existe
        if (usuarioSeleccionadoFromServer) {
            const selectedInfo = document.getElementById('selected-user-info');
            const selectedName = document.getElementById('selected-user-name');
            const selectedCedula = document.getElementById('selected-user-cedula');
            const selectedEmail = document.getElementById('selected-user-email');

            if (selectedInfo && selectedName && selectedCedula && selectedEmail) {
                selectedName.textContent = usuarioSeleccionadoFromServer.name;
                selectedCedula.textContent = usuarioSeleccionadoFromServer.cedula;
                selectedEmail.textContent = usuarioSeleccionadoFromServer.email;
                selectedInfo.style.display = 'block';
            }
        }

        // Ocultar indicador de carga al finalizar
        if (window.ReferidosSearch) {
            window.ReferidosSearch.hideLoadingIndicator();
        }
    });

    // Exponer datos globalmente para los módulos
    window.redData = redDataFromServer;
    window.usuarioSeleccionado = usuarioSeleccionadoFromServer;
</script>
@endsection