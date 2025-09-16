@extends('layouts.admin')

@section('title', '- Red de Referidos')
@section('page-title', 'Red de Referidos MLM')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Visualización y gestión de la red de referidos MLM</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-info me-2" onclick="verVisualizacion()">
                        <i class="bi bi-diagram-3 me-1"></i>
                        Vista Gráfica
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportarRed()">
                        <i class="bi bi-download me-1"></i>
                        Exportar Red
                    </button>
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
                                            @if($usuario->tipo_usuario == 'lider')
                                                <span class="badge bg-warning text-dark">Líder</span>
                                            @else
                                                <span class="badge bg-primary">Vendedor</span>
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
                                                <a href="{{ route('admin.referidos.show', $usuario->id) }}"
                                                   class="btn btn-sm btn-outline-info" title="Ver red">
                                                    <i class="bi bi-diagram-3"></i>
                                                </a>
                                                <a href="{{ route('admin.users.show', $usuario->id) }}"
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
                                        <small class="text-muted">{{ ucfirst($usuario->tipo_usuario) }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: var(--primary-color);">{{ $usuario->referidos_count }}</div>
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
    @if($redJerarquica->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-2 me-2"></i>
                        Vista Jerárquica (Primeros 3 Niveles)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="network-container" style="height: 400px; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="bi bi-diagram-3 fs-1"></i>
                                <p class="mt-2">Visualización de Red MLM</p>
                                <small>Integración con D3.js próximamente</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function verVisualizacion() {
    alert('Vista gráfica en desarrollo - Se integrará con D3.js para visualización interactiva');
}

function exportarRed() {
    alert('Funcionalidad de exportación en desarrollo');
}

// Datos para visualización (preparados para D3.js)
const redData = @json($redJerarquica);
console.log('Datos de red MLM:', redData);
</script>
@endsection