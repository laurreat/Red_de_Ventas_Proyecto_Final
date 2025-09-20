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
    @if($redJerarquica && $redJerarquica->count() > 0)
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
                    <!-- Controles de visualización -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-group" role="group" aria-label="Tipo de visualización">
                            <input type="radio" class="btn-check" name="viewType" id="treeView" value="tree" checked>
                            <label class="btn btn-outline-primary btn-sm" for="treeView">
                                <i class="bi bi-diagram-2 me-1"></i>Vista Árbol
                            </label>

                            <input type="radio" class="btn-check" name="viewType" id="forceView" value="force">
                            <label class="btn btn-outline-primary btn-sm" for="forceView">
                                <i class="bi bi-diagram-3 me-1"></i>Vista Fuerza
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetZoom()">
                                <i class="bi bi-arrows-angle-expand"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="exportSVG()">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3 small">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #722f37;"></div>
                                    <span>Líder</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #0d6efd;"></div>
                                    <span>Vendedor</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #198754;"></div>
                                    <span>+ de 5 referidos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="network-container" style="height: 500px; border: 1px solid #dee2e6; border-radius: 0.375rem; position: relative; overflow: hidden;">
                        <!-- El gráfico D3.js se renderizará aquí -->
                    </div>

                    <!-- Tooltip -->
                    <div id="network-tooltip" style="position: absolute; pointer-events: none; background: rgba(0,0,0,0.8); color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; opacity: 0; z-index: 1000; transition: opacity 0.2s;"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
const redData = @json($redJerarquica ?? []);

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('network-container')) {
        initializeVisualization();
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
    const container = document.getElementById('network-container');
    const width = container.clientWidth;
    const height = container.clientHeight;

    // Limpiar contenedor
    d3.select('#network-container').selectAll('*').remove();

    // Crear SVG
    svg = d3.select('#network-container')
        .append('svg')
        .attr('width', '100%')
        .attr('height', '100%')
        .attr('viewBox', `0 0 ${width} ${height}`);

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
    nodes = [];
    links = [];

    if (!redData || !Array.isArray(redData) || redData.length === 0) {
        showEmptyState();
        return;
    }

    // Convertir datos jerárquicos a formato de nodos y enlaces
    const nodeMap = new Map();

    function processNode(nodeData, level = 0, parentId = null) {
        const nodeId = nodeData.id;

        const node = {
            id: nodeId,
            name: nodeData.name,
            email: nodeData.email,
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

        // Procesar hijos recursivamente
        if (nodeData.hijos && nodeData.hijos.length > 0) {
            nodeData.hijos.forEach(child => {
                processNode(child, level + 1, nodeId);
            });
        }
    }

    // Procesar todos los nodos raíz
    redData.forEach(rootNode => {
        processNode(rootNode, 0);
    });
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

    // Crear jerarquía
    const root = d3.stratify()
        .id(d => d.id)
        .parentId(d => d.parentId)
        (nodes);

    // Configurar layout de árbol
    const treeLayout = d3.tree()
        .size([width - 100, height - 100]);

    const treeData = treeLayout(root);

    // Crear enlaces
    const links = g.selectAll('.link')
        .data(treeData.links())
        .enter()
        .append('path')
        .attr('class', 'link')
        .attr('d', d3.linkHorizontal()
            .x(d => d.y + 50)
            .y(d => d.x + 50)
        )
        .style('fill', 'none')
        .style('stroke', '#ddd')
        .style('stroke-width', 2);

    // Crear nodos
    const nodeGroup = g.selectAll('.node')
        .data(treeData.descendants())
        .enter()
        .append('g')
        .attr('class', 'node')
        .attr('transform', d => `translate(${d.y + 50}, ${d.x + 50})`)
        .style('cursor', 'pointer');

    // Círculos de nodos
    nodeGroup.append('circle')
        .attr('r', d => Math.max(config.nodeRadius.min,
            Math.min(config.nodeRadius.max, 8 + d.data.referidos_count)))
        .style('fill', d => getNodeColor(d.data))
        .style('stroke', '#fff')
        .style('stroke-width', 2);

    // Etiquetas de nodos
    nodeGroup.append('text')
        .attr('dy', '0.31em')
        .attr('x', d => d.children ? -15 : 15)
        .style('text-anchor', d => d.children ? 'end' : 'start')
        .style('font-size', '12px')
        .style('font-weight', '500')
        .text(d => d.data.name.length > 15 ? d.data.name.substring(0, 15) + '...' : d.data.name);

    // Agregar eventos
    addNodeEvents(nodeGroup);
}

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
        .force('link', d3.forceLink(links).id(d => d.id).distance(100))
        .force('charge', d3.forceManyBody().strength(-300))
        .force('center', d3.forceCenter(width / 2, height / 2))
        .force('collision', d3.forceCollide().radius(d =>
            Math.max(config.nodeRadius.min, Math.min(config.nodeRadius.max, 8 + d.referidos_count)) + 5
        ));

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
        .attr('r', d => Math.max(config.nodeRadius.min,
            Math.min(config.nodeRadius.max, 8 + d.referidos_count)))
        .style('fill', d => getNodeColor(d))
        .style('stroke', '#fff')
        .style('stroke-width', 2);

    // Etiquetas de nodos
    nodeGroup.append('text')
        .attr('dy', '0.31em')
        .style('text-anchor', 'middle')
        .style('font-size', '10px')
        .style('font-weight', '500')
        .style('pointer-events', 'none')
        .text(d => d.name.length > 10 ? d.name.substring(0, 10) + '...' : d.name);

    // Agregar eventos
    addNodeEvents(nodeGroup);

    // Actualizar posiciones en cada tick
    simulation.on('tick', function() {
        link
            .attr('x1', d => d.source.x)
            .attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x)
            .attr('y2', d => d.target.y);

        nodeGroup.attr('transform', d => `translate(${d.x}, ${d.y})`);
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
            tooltip
                .style('opacity', 1)
                .style('left', (event.pageX + 10) + 'px')
                .style('top', (event.pageY - 10) + 'px')
                .html(`
                    <strong>${d.name}</strong><br>
                    Tipo: ${d.tipo}<br>
                    Email: ${d.email}<br>
                    Referidos: ${d.referidos_count}<br>
                    Nivel: ${d.level + 1}
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
            window.open(`${baseUrl}/${d.id}`, '_blank');
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

    const blob = new Blob([svgString], { type: 'image/svg+xml' });
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
    const container = d3.select('#network-container');
    container.selectAll('*').remove();

    container.append('div')
        .style('display', 'flex')
        .style('align-items', 'center')
        .style('justify-content', 'center')
        .style('height', '100%')
        .style('color', '#6c757d')
        .html(`
            <div style="text-align: center;">
                <i class="bi bi-diagram-3" style="font-size: 3rem;"></i>
                <p style="margin-top: 1rem;">No hay datos de red para mostrar</p>
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
    link.download = `red-mlm-${new Date().toISOString().split('T')[0]}.json`;
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
        svg.attr('viewBox', `0 0 ${width} ${height}`);

        if (currentViewType === 'force' && simulation) {
            simulation.force('center', d3.forceCenter(width / 2, height / 2));
            simulation.alpha(0.3).restart();
        }
    }
});
</script>
@endsection