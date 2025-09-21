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
                            <button type="button" class="btn btn-outline-light" onclick="exportarRed()">
                                <i class="bi bi-download me-1"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de Estado -->
    @if(session('success') || session('warning') || session('error') || session('info'))
    <div class="row mb-4">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Éxito:</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning border-0 shadow-sm alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Atención:</strong> {{ session('warning') }}
                        <br><small class="text-muted">Verifique que la cédula sea correcta o que el usuario tenga rol de vendedor/líder.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('info'))
            <div class="alert alert-info border-0 shadow-sm alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Información:</strong> {{ session('info') }}
                        <br><small class="text-muted">Este usuario puede no tener referidos o puede estar en un nivel sin descendencia.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
        </div>
    </div>
    @endif

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
    {{-- Mostrar siempre para debug --}}
    @if(true)
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

    <!-- Secciones de estadísticas adicionales removidas para optimizar espacio -->

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

<script>
    let svg, g, zoom;
    let currentViewType = 'tree';
    let simulation;
    let nodes = [];
    let links = [];

    // Configuración de colores y estilos
    const config = {
        colors: {
            lider: '#722f37',
            vendedor: '#0d6efd',
            active: '#198754',
            default: '#6c757d'
        },
        nodeRadius: {
            min: 8,
            max: 20
        }
    };

    // Datos para visualización (desde el controlador)
    const redData = {
        !!json_encode($redJerarquica ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!
    };
    const usuarioSeleccionado = {
        !!json_encode($usuarioSeleccionado ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!
    };

    console.log('Red Data loaded:', redData);
    console.log('Red Data count:', redData ? redData.length : 0);
    console.log('Usuario seleccionado:', usuarioSeleccionado);

    // Mostrar información del usuario seleccionado si existe
    if (usuarioSeleccionado) {
        const selectedInfo = document.getElementById('selected-user-info');
        const selectedName = document.getElementById('selected-user-name');
        const selectedCedula = document.getElementById('selected-user-cedula');
        const selectedEmail = document.getElementById('selected-user-email');

        if (selectedInfo && selectedName && selectedCedula && selectedEmail) {
            selectedName.textContent = usuarioSeleccionado.name;
            selectedCedula.textContent = usuarioSeleccionado.cedula;
            selectedEmail.textContent = usuarioSeleccionado.email;
            selectedInfo.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, looking for network-container...');
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

        const container = document.getElementById('network-container');
        console.log('Container found:', !!container);

        if (container) {
            console.log('Initializing visualization...');
            initializeVisualization();
        } else {
            console.log('No network-container found, visualization section may not be displayed');
        }

        // Event listeners para cambio de vista
        document.querySelectorAll('input[name="viewType"]').forEach(input => {
            input.addEventListener('change', function() {
                currentViewType = this.value;
                updateVisualization();
            });
        });
    });

    function initializeVisualization() {
        console.log('Starting initializeVisualization...');
        const container = document.getElementById('network-container');

        if (!container) {
            console.error('Network container not found!');
            return;
        }

        const width = container.clientWidth;
        const height = container.clientHeight;
        console.log('Container dimensions:', width, 'x', height);

        // Limpiar contenedor
        d3.select('#network-container').selectAll('*').remove();

        // Crear SVG
        svg = d3.select('#network-container')
            .append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', '0 0 ' + width + ' ' + height);

        // Grupo principal para zoom/pan
        g = svg.append('g');

        // Configurar zoom
        zoom = d3.zoom()
            .scaleExtent([0.1, 3])
            .on('zoom', function(event) {
                g.attr('transform', event.transform);
            });

        svg.call(zoom);

        // Procesar datos y crear visualización inicial
        processData();
        updateVisualization();
    }

    function processData() {
        console.log('=== PROCESANDO DATOS D3.js ===');
        console.log('redData recibido:', redData);

        @if($usuarioSeleccionado)
        console.log('🎯 Usuario seleccionado desde PHP:', {
            name: '{{ $usuarioSeleccionado->name }}',
            cedula: '{{ $usuarioSeleccionado->cedula }}',
            rol: '{{ $usuarioSeleccionado->rol }}'
        });
        console.log('📊 Datos estructurados recibidos para {{ $usuarioSeleccionado->name }}:', redData);
        @endif

        nodes = [];
        links = [];

        if (!redData || !Array.isArray(redData) || redData.length === 0) {
            console.log('No data available, showing empty state');
            showEmptyState();
            return;
        }

        console.log('Data found, processing', redData.length, 'root nodes');
        console.log('Primer nodo en redData:', redData[0]);

        // Convertir datos jerárquicos a formato de nodos y enlaces
        const nodeMap = new Map();

        function processNode(nodeData, level = 0, parentId = null) {
            const nodeId = nodeData.id;

            // Debug: Log de cada nodo procesado
            console.log(`Procesando nodo nivel ${level}:`, {
                id: nodeId,
                name: nodeData.name,
                cedula: nodeData.cedula,
                tipo: nodeData.tipo,
                parentId: parentId
            });

            const node = {
                id: nodeId,
                name: nodeData.name,
                email: nodeData.email,
                cedula: nodeData.cedula,
                tipo: nodeData.tipo,
                level: level,
                referidos_count: nodeData.referidos_count,
                parentId: parentId,
                children: nodeData.hijos || []
            };

            nodes.push(node);
            nodeMap.set(nodeId, node);

            // Crear enlace con el padre si existe
            if (parentId) {
                links.push({
                    source: parentId,
                    target: nodeId
                });
            }

            // Procesar hijos recursivamente (manejar tanto arrays como objetos)
            let children = nodeData.hijos;

            // Convertir objeto a array si es necesario
            if (children && typeof children === 'object' && !Array.isArray(children)) {
                console.log('⚠️ Convirtiendo hijos de objeto a array para:', nodeData.name);
                children = Object.values(children);
            }

            if (children && Array.isArray(children) && children.length > 0) {
                console.log(`👶 Procesando ${children.length} hijos de ${nodeData.name}`);
                children.forEach(child => {
                    processNode(child, level + 1, nodeId);
                });
            }
        }

        // Procesar todos los nodos raíz
        redData.forEach(rootNode => {
            processNode(rootNode, 0);
        });

        console.log('=== PROCESAMIENTO COMPLETO ===');
        console.log('Total nodos procesados:', nodes.length);
        console.log('Total enlaces:', links.length);
        console.log('Nombres de todos los nodos:', nodes.map(n => `${n.name} (${n.cedula}) - Tipo: ${n.tipo}`));

        // Verificar si hay nodos sin procesar
        if (nodes.length === 0) {
            console.error('❌ NO SE PROCESARON NODOS! Datos originales:', redData);
            showEmptyState();
            return;
        } else {
            console.log('✅ Nodos procesados correctamente');
        }

        console.log('Nodos completos:', nodes);
        console.log('Enlaces:', links);

        // Actualizar métricas en tiempo real
        updateNetworkMetrics();
    }

    function updateVisualization() {
        if (nodes.length === 0) {
            showEmptyState();
            return;
        }

        // Limpiar visualización anterior
        g.selectAll('*').remove();

        if (currentViewType === 'tree') {
            renderTreeView();
        } else {
            renderForceView();
        }
    }

    function renderTreeView() {
        const container = document.getElementById('network-container');
        const width = container.clientWidth;
        const height = container.clientHeight;

        // Obtener nodos raíz (sin padre)
        const rootNodes = nodes.filter(d => !d.parentId);
        console.log('Root nodes found:', rootNodes.map(n => n.name));

        // Para redes centradas en usuario específico, usar los datos tal como vienen
        @if($usuarioSeleccionado)
        const usuarioSeleccionado = {
            name: '{{ $usuarioSeleccionado->name }}',
            cedula: '{{ $usuarioSeleccionado->cedula }}',
            rol: '{{ $usuarioSeleccionado->rol }}'
        };
        console.log('Usuario seleccionado:', usuarioSeleccionado);

        // Usar los nodos tal como vienen, sin modificaciones
        const modifiedNodes = nodes;
        @else
        // Para vista general, manejar múltiples raíces si es necesario
        let modifiedNodes = nodes;

        if (rootNodes.length > 1) {
            // Solo crear nodo artificial si realmente hay múltiples raíces independientes
            const artificialRoot = {
                id: 'artificial-root',
                name: 'Red MLM Completa',
                email: '',
                tipo: 'root',
                level: -1,
                referidos_count: rootNodes.length,
                parentId: null
            };

            // Actualizar parentId de nodos raíz para que apunten a la raíz artificial
            modifiedNodes = nodes.map(node => {
                if (!node.parentId) {
                    return {
                        ...node,
                        parentId: 'artificial-root'
                    };
                }
                return node;
            });
            // Agregar la raíz artificial al inicio solo si se creó
            modifiedNodes = [artificialRoot, ...modifiedNodes];
        }
        @endif

        // Crear jerarquía con los nodos finales
        const finalNodes = modifiedNodes || nodes;
        const root = d3.stratify()
            .id(d => d.id)
            .parentId(d => d.parentId)
            (finalNodes);

        // Configurar layout de árbol
        const treeLayout = d3.tree()
            .size([width - 100, height - 100]);

        const treeData = treeLayout(root);

        // Renderizar árbol
        renderTree(treeData, width, height);
    }

    function renderTree(treeData, width, height) {
        // Determinar si hay raíz artificial para filtrarla
        const hasArtificialRoot = treeData.data && treeData.data.id === 'artificial-root';

        // Filtrar enlaces y nodos según el contexto
        let linksData = treeData.links();
        let nodesData = treeData.descendants();

        @if($usuarioSeleccionado)
        // Para usuarios específicos, mostrar todos los nodos sin filtrar
        console.log('Renderizando red específica para:', '{{ $usuarioSeleccionado->name }}');
        @else
        // Para vista general, filtrar raíz artificial si existe
        if (hasArtificialRoot) {
            linksData = linksData.filter(d =>
                d.source.data.id !== 'artificial-root' && d.target.data.id !== 'artificial-root'
            );
            nodesData = nodesData.filter(d => d.data.id !== 'artificial-root');
        }
        @endif

        // Crear enlaces
        const links = g.selectAll('.link')
            .data(linksData)
            .enter()
            .append('path')
            .attr('class', 'link')
            .attr('d', d3.linkHorizontal()
                .x(function(d) {
                    return d.y + 50;
                })
                .y(function(d) {
                    return d.x + 50;
                })
            )
            .style('fill', 'none')
            .style('stroke', '#ddd')
            .style('stroke-width', 2);

        // Crear nodos
        const nodeGroup = g.selectAll('.node')
            .data(nodesData)
            .enter()
            .append('g')
            .attr('class', 'node')
            .attr('transform', function(d) {
                return 'translate(' + (d.y + 50) + ', ' + (d.x + 50) + ')';
            })
            .style('cursor', 'pointer');

        // Círculos de nodos
        nodeGroup.append('circle')
            .attr('r', function(d) {
                return Math.max(config.nodeRadius.min,
                    Math.min(config.nodeRadius.max, 8 + d.data.referidos_count));
            })
            .style('fill', function(d) {
                // Debug: verificar datos del nodo para color
                console.log('Coloreando nodo:', {
                    d_data: d.data,
                    d_data_tipo: d.data ? d.data.tipo : 'N/A'
                });
                return getNodeColor(d.data);
            })
            .style('stroke', '#fff')
            .style('stroke-width', 2);

        // Etiquetas de nodos
        nodeGroup.append('text')
            .attr('dy', '0.31em')
            .attr('x', function(d) {
                return d.children ? -15 : 15;
            })
            .style('text-anchor', function(d) {
                return d.children ? 'end' : 'start';
            })
            .style('font-size', '12px')
            .style('font-weight', '500')
            .text(function(d) {
                // Debug: verificar qué datos están disponibles
                console.log('Renderizando texto para nodo:', {
                    d_data_name: d.data ? d.data.name : 'N/A',
                    d_name: d.name,
                    d_data: d.data,
                    d_completo: d
                });

                // Usar d.data.name para vista de árbol (hierarchical data)
                const name = d.data ? d.data.name : d.name;
                return name && name.length > 15 ? name.substring(0, 15) + '...' : (name || 'Sin nombre');
            });

        // Agregar eventos
        addNodeEvents(nodeGroup);
    }

    // Funciones duplicadas eliminadas - se usa renderTree() unificada

    function renderForceView() {
        const container = document.getElementById('network-container');
        const width = container.clientWidth;
        const height = container.clientHeight;

        // Detener simulación anterior si existe
        if (simulation) {
            simulation.stop();
        }

        // Crear simulación de fuerzas
        simulation = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(function(d) {
                return d.id;
            }).distance(100))
            .force('charge', d3.forceManyBody().strength(-300))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collision', d3.forceCollide().radius(function(d) {
                return Math.max(config.nodeRadius.min, Math.min(config.nodeRadius.max, 8 + d.referidos_count)) + 5;
            }));

        // Crear enlaces
        const link = g.selectAll('.link')
            .data(links)
            .enter()
            .append('line')
            .attr('class', 'link')
            .style('stroke', '#ddd')
            .style('stroke-width', 2);

        // Crear nodos
        const nodeGroup = g.selectAll('.node')
            .data(nodes)
            .enter()
            .append('g')
            .attr('class', 'node')
            .style('cursor', 'pointer')
            .call(d3.drag()
                .on('start', dragstarted)
                .on('drag', dragged)
                .on('end', dragended));

        // Círculos de nodos
        nodeGroup.append('circle')
            .attr('r', function(d) {
                return Math.max(config.nodeRadius.min,
                    Math.min(config.nodeRadius.max, 8 + d.referidos_count));
            })
            .style('fill', function(d) {
                return getNodeColor(d);
            })
            .style('stroke', '#fff')
            .style('stroke-width', 2);

        // Etiquetas de nodos
        nodeGroup.append('text')
            .attr('dy', '0.31em')
            .style('text-anchor', 'middle')
            .style('font-size', '10px')
            .style('font-weight', '500')
            .style('pointer-events', 'none')
            .text(function(d) {
                return d.name.length > 10 ? d.name.substring(0, 10) + '...' : d.name;
            });

        // Agregar eventos
        addNodeEvents(nodeGroup);

        // Actualizar posiciones en cada tick
        simulation.on('tick', function() {
            link
                .attr('x1', function(d) {
                    return d.source.x;
                })
                .attr('y1', function(d) {
                    return d.source.y;
                })
                .attr('x2', function(d) {
                    return d.target.x;
                })
                .attr('y2', function(d) {
                    return d.target.y;
                });

            nodeGroup.attr('transform', function(d) {
                return 'translate(' + d.x + ', ' + d.y + ')';
            });
        });
    }

    function getNodeColor(node) {
        if (node.tipo === 'lider') return config.colors.lider;
        if (node.referidos_count > 5) return config.colors.active;
        if (node.tipo === 'vendedor') return config.colors.vendedor;
        return config.colors.default;
    }

    function addNodeEvents(nodeSelection) {
        const tooltip = d3.select('#network-tooltip');

        nodeSelection
            .on('mouseover', function(event, d) {
                // Determinar si es vista de árbol (d.data) o vista de fuerza (d directo)
                const nodeData = d.data || d;

                tooltip
                    .style('opacity', 1)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 10) + 'px')
                    .html(`
                    <strong>${nodeData.name || 'Sin nombre'}</strong><br>
                    Cédula: ${nodeData.cedula || 'N/A'}<br>
                    Tipo: ${nodeData.tipo || 'N/A'}<br>
                    Email: ${nodeData.email || 'N/A'}<br>
                    Referidos: ${nodeData.referidos_count || 0}<br>
                    Nivel: ${nodeData.nivel || (nodeData.level ? nodeData.level + 1 : 'N/A')}
                `);
            })
            .on('mousemove', function(event) {
                tooltip
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 10) + 'px');
            })
            .on('mouseout', function() {
                tooltip.style('opacity', 0);
            })
            .on('click', function(event, d) {
                // Abrir detalles del usuario
                const baseUrl = '{{ url("admin/referidos") }}';
                window.open(baseUrl + '/' + d.id, '_blank');
            });
    }

    // Funciones de drag para vista de fuerza
    function dragstarted(event, d) {
        if (!event.active) simulation.alphaTarget(0.3).restart();
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(event, d) {
        d.fx = event.x;
        d.fy = event.y;
    }

    function dragended(event, d) {
        if (!event.active) simulation.alphaTarget(0);
        d.fx = null;
        d.fy = null;
    }

    function resetZoom() {
        svg.transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity);
    }

    function exportSVG() {
        const svgElement = document.querySelector('#network-container svg');
        const serializer = new XMLSerializer();
        const svgString = serializer.serializeToString(svgElement);

        const blob = new Blob([svgString], {
            type: 'image/svg+xml'
        });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'red-mlm.svg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function showEmptyState() {
        console.log('Showing empty state');
        const container = d3.select('#network-container');
        container.selectAll('*').remove();

        // Determinar el tipo de mensaje según el contexto
        let message = '';
        let submessage = '';
        let icon = 'bi-diagram-3';
        let color = '#6c757d';

        @if($usuarioSeleccionado)
        message = 'Red de {{ $usuarioSeleccionado->name }}';
        submessage = 'Este usuario no tiene una red de referidos configurada o los datos no están disponibles.';
        icon = 'bi-person-circle';
        color = '#ffc107';
        @elseif(request('cedula'))
        message = 'No se encontraron datos';
        submessage = 'No se encontró información de red para la cédula buscada: {{ request('
        cedula ') }}';
        icon = 'bi-search';
        color = '#dc3545';
        @else
        message = 'Red MLM no disponible';
        submessage = 'No hay datos de red disponibles en este momento. Puede ser que no existan usuarios con referidos en el sistema.';
        icon = 'bi-diagram-3';
        color = '#6c757d';
        @endif

        container.append('div')
            .style('display', 'flex')
            .style('align-items', 'center')
            .style('justify-content', 'center')
            .style('height', '100%')
            .style('color', color)
            .style('flex-direction', 'column')
            .style('padding', '2rem')
            .html(`
            <div style="text-align: center; max-width: 400px;">
                <div style="
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 1.5rem;
                    background: linear-gradient(135deg, ${color}20, ${color}10);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <i class="${icon}" style="font-size: 2.5rem; color: ${color};"></i>
                </div>
                <h5 style="margin-bottom: 1rem; color: #495057; font-weight: 600;">${message}</h5>
                <p style="margin-bottom: 1.5rem; color: #6c757d; line-height: 1.5;">${submessage}</p>
                <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                    <button onclick="clearSearch()" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Ver Red Completa
                    </button>
                    <button onclick="showRandomUser()" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-shuffle me-1"></i>
                        Usuario Aleatorio
                    </button>
                </div>
            </div>
        `);
    }

    function verVisualizacion() {
        // Enfocar en el contenedor de visualización
        document.getElementById('network-container').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    function exportarRed() {
        // Preparar datos para exportación
        const exportData = {
            timestamp: new Date().toISOString(),
            total_nodos: nodes.length,
            total_enlaces: links.length,
            nodos: nodes.map(node => ({
                id: node.id,
                nombre: node.name,
                email: node.email,
                tipo: node.tipo,
                nivel: node.level + 1,
                referidos_count: node.referidos_count
            })),
            enlaces: links.map(link => ({
                origen: typeof link.source === 'object' ? link.source.id : link.source,
                destino: typeof link.target === 'object' ? link.target.id : link.target
            }))
        };

        // Crear y descargar archivo JSON
        const blob = new Blob([JSON.stringify(exportData, null, 2)], {
            type: 'application/json'
        });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'red-mlm-' + new Date().toISOString().split('T')[0] + '.json';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    // Redimensionar al cambiar tamaño de ventana
    window.addEventListener('resize', function() {
        if (svg) {
            const container = document.getElementById('network-container');
            const width = container.clientWidth;
            const height = container.clientHeight;
            svg.attr('viewBox', '0 0 ' + width + ' ' + height);

            if (currentViewType === 'force' && simulation) {
                simulation.force('center', d3.forceCenter(width / 2, height / 2));
                simulation.alpha(0.3).restart();
            }
        }
    });

    // Funciones para manejo de búsqueda
    function searchUserNetwork(event) {
        event.preventDefault();
        const cedulaInput = document.getElementById('cedula_search');
        const cedula = cedulaInput.value.trim();

        // Validaciones en tiempo real
        if (!cedula) {
            showValidationError(cedulaInput, 'Por favor ingrese un número de cédula');
            return;
        }

        // Validar formato de cédula (solo números, 6-12 dígitos)
        if (!/^[0-9]{6,12}$/.test(cedula)) {
            showValidationError(cedulaInput, 'La cédula debe contener solo números (6-12 dígitos)');
            return;
        }

        // Limpiar errores de validación
        clearValidationError(cedulaInput);

        // Mostrar indicador de carga con mensaje específico
        showLoadingIndicator('Buscando usuario con cédula: ' + cedula);

        // Construir URL con parámetro de búsqueda
        const url = new URL(window.location.href);
        url.searchParams.set('cedula', cedula);

        // Redireccionar con el parámetro de búsqueda
        window.location.href = url.toString();
    }

    function showValidationError(input, message) {
        // Remover errores previos
        clearValidationError(input);

        // Agregar clase de error
        input.classList.add('is-invalid');

        // Crear elemento de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        errorDiv.id = input.id + '_error';

        // Insertar después del input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);

        // Focus en el input
        input.focus();
    }

    function clearValidationError(input) {
        // Remover clase de error
        input.classList.remove('is-invalid');

        // Remover mensaje de error si existe
        const errorElement = document.getElementById(input.id + '_error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Validación en tiempo real mientras se escribe
    document.addEventListener('DOMContentLoaded', function() {
        const cedulaInput = document.getElementById('cedula_search');
        if (cedulaInput) {
            cedulaInput.addEventListener('input', function() {
                const value = this.value.trim();

                // Limpiar errores previos si el campo está vacío
                if (!value) {
                    clearValidationError(this);
                    return;
                }

                // Validar formato en tiempo real
                if (!/^[0-9]*$/.test(value)) {
                    showValidationError(this, 'Solo se permiten números');
                } else if (value.length > 12) {
                    showValidationError(this, 'La cédula no puede tener más de 12 dígitos');
                } else if (value.length > 0 && value.length < 6) {
                    showValidationError(this, 'La cédula debe tener al menos 6 dígitos');
                } else {
                    clearValidationError(this);
                }
            });

            // Limpiar errores cuando se hace focus
            cedulaInput.addEventListener('focus', function() {
                if (this.value.trim() === '') {
                    clearValidationError(this);
                }
            });
        }
    });

    function clearSearch() {
        // Mostrar indicador de carga
        showLoadingIndicator('Cargando red completa');

        // Construir URL sin parámetros de búsqueda
        const url = new URL(window.location.href);
        url.searchParams.delete('cedula');
        url.searchParams.delete('search');

        // Redireccionar sin parámetros
        window.location.href = url.toString();
    }

    function showRandomUser() {
        // Mostrar indicador de carga
        showLoadingIndicator('Seleccionando usuario aleatorio');

        // Lista de cédulas de ejemplo para demostración
        const cedulasEjemplo = ['12345678', '87654321', '11111111', '22222222', '33333333'];
        const cedulaAleatoria = cedulasEjemplo[Math.floor(Math.random() * cedulasEjemplo.length)];

        document.getElementById('cedula_search').value = cedulaAleatoria;

        // Buscar el usuario aleatorio
        setTimeout(() => {
            const form = document.getElementById('searchUserForm');
            if (form) {
                form.dispatchEvent(new Event('submit'));
            }
        }, 500);
    }

    function showLoadingIndicator(customMessage = null) {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            // Actualizar mensaje si se proporciona uno personalizado
            if (customMessage) {
                const messageElement = indicator.querySelector('.fw-medium');
                const submessageElement = indicator.querySelector('.text-muted');
                if (messageElement) {
                    messageElement.textContent = customMessage;
                }
                if (submessageElement) {
                    submessageElement.textContent = 'Procesando solicitud en tiempo real...';
                }
            }
            indicator.style.display = 'block';
        }
    }

    function hideLoadingIndicator() {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    // Ocultar indicador de carga cuando la página esté lista
    document.addEventListener('DOMContentLoaded', function() {
        hideLoadingIndicator();
    });

    // Funciones para las nuevas herramientas
    function updateNetworkMetrics() {
        // Actualizar métricas de la red
        document.getElementById('total-nodes').textContent = nodes.length;
        document.getElementById('total-connections').textContent = links.length;

        // Calcular niveles máximos
        const maxLevel = Math.max(...nodes.map(n => n.level || 0)) + 1;
        document.getElementById('max-depth').textContent = maxLevel;

        // Calcular promedio de referidos
        const totalReferrals = nodes.reduce((sum, n) => sum + (n.referidos_count || 0), 0);
        const avgReferrals = nodes.length > 0 ? (totalReferrals / nodes.length).toFixed(1) : 0;
        document.getElementById('avg-referrals').textContent = avgReferrals;
    }

    function expandAllNodes() {
        console.log('Expandiendo todos los nodos...');
        // En vista de fuerza, aumentar la distancia entre nodos
        if (currentViewType === 'force' && simulation) {
            simulation.force('link').distance(150);
            simulation.alpha(0.3).restart();
        }
        // En vista de árbol, no hay mucho que expandir, pero podemos aumentar el zoom
        if (currentViewType === 'tree') {
            svg.transition()
                .duration(750)
                .call(zoom.scaleBy, 1.2);
        }
    }

    function collapseAllNodes() {
        console.log('Contrayendo todos los nodos...');
        // En vista de fuerza, reducir la distancia entre nodos
        if (currentViewType === 'force' && simulation) {
            simulation.force('link').distance(80);
            simulation.alpha(0.3).restart();
        }
        // En vista de árbol, reducir el zoom
        if (currentViewType === 'tree') {
            svg.transition()
                .duration(750)
                .call(zoom.scaleBy, 0.8);
        }
    }

    function centerOnUser() {
        console.log('Centrando vista...');
        // Resetear zoom y posición
        resetZoom();

        // Si hay un usuario seleccionado, intentar centrarlo
        if (usuarioSeleccionado && nodes.length > 0) {
            const userNode = nodes.find(n => n.id === usuarioSeleccionado._id);
            if (userNode && currentViewType === 'force') {
                // En vista de fuerza, aplicar fuerza hacia el centro en el usuario
                setTimeout(() => {
                    if (simulation) {
                        userNode.fx = simulation.force('center').x();
                        userNode.fy = simulation.force('center').y();
                        simulation.alpha(0.3).restart();

                        // Liberar la posición fija después de un momento
                        setTimeout(() => {
                            userNode.fx = null;
                            userNode.fy = null;
                        }, 2000);
                    }
                }, 100);
            }
        }
    }

    function downloadNetworkData() {
        console.log('Descargando datos de la red...');

        // Preparar datos completos para exportación
        const networkAnalysis = {
            timestamp: new Date().toISOString(),
            usuario_seleccionado: usuarioSeleccionado,
            metricas: {
                total_nodos: nodes.length,
                total_conexiones: links.length,
                niveles_maximos: Math.max(...nodes.map(n => n.level || 0)) + 1,
                promedio_referidos: nodes.length > 0 ?
                    (nodes.reduce((sum, n) => sum + (n.referidos_count || 0), 0) / nodes.length).toFixed(2) : 0
            },
            distribucion_por_tipo: {
                lideres: nodes.filter(n => n.tipo === 'lider').length,
                vendedores: nodes.filter(n => n.tipo === 'vendedor').length
            },
            distribucion_por_nivel: {},
            nodos: nodes.map(node => ({
                id: node.id,
                nombre: node.name,
                email: node.email,
                cedula: node.cedula || 'N/A',
                tipo: node.tipo,
                nivel: node.level + 1,
                referidos_count: node.referidos_count,
                parent_id: node.parentId
            })),
            conexiones: links.map(link => ({
                origen: typeof link.source === 'object' ? link.source.id : link.source,
                destino: typeof link.target === 'object' ? link.target.id : link.target
            }))
        };

        // Calcular distribución por nivel
        nodes.forEach(node => {
            const nivel = node.level + 1;
            networkAnalysis.distribucion_por_nivel[nivel] =
                (networkAnalysis.distribucion_por_nivel[nivel] || 0) + 1;
        });

        // Crear y descargar archivo JSON
        const blob = new Blob([JSON.stringify(networkAnalysis, null, 2)], {
            type: 'application/json'
        });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;

        const filename = usuarioSeleccionado ?
            'red-mlm-' + usuarioSeleccionado.cedula + '-' + new Date().toISOString().split('T')[0] + '.json' :
            'red-mlm-completa-' + new Date().toISOString().split('T')[0] + '.json';

        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        // Mostrar mensaje de éxito
        console.log('Datos descargados como:', filename);
    }
</script>
@endsection