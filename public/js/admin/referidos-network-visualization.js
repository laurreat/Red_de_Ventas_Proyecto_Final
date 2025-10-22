/**
 * Visualización de Red MLM con D3.js
 * Módulo principal para renderizado y gestión de red de referidos
 */

let svg, g, zoom;
let currentViewType = 'tree';
let simulation;
let nodes = [];
let links = [];

// Configuración de colores y estilos - SISTEMA DE COLORES AVANZADO
const config = {
    colors: {
        // Categorías principales
        topVentasPorMonto: '#DC143C',   // Crimson (TOP VENTAS POR MONTO - más de $5,000,000)
        topReferidos: '#8B0000',        // Rojo oscuro intenso (TOP REFERIDOS - más de 20 referidos)
        lider: '#722F37',               // Vino tinto oscuro (LÍDER)
        topVentasMenor: '#B8860B',      // Dorado oscuro (VENTAS ALTAS - $2,000,000 - $5,000,000)
        clienteTopReferidor: '#4169E1', // Azul Real (CLIENTE TOP REFERIDOR - clientes con 5+ referidos) ⭐ NUEVO
        vendedorActivo: '#A8556A',      // Vino rosado (VENDEDOR ACTIVO - 5-10 referidos)
        vendedor: '#C89FA6',            // Vino rosado claro (VENDEDOR - 1-4 referidos)
        clienteConReferidos: '#87CEEB', // Azul cielo (CLIENTE con 1-4 referidos) ⭐ NUEVO
        cliente: '#E8D5D9',             // Rosa pálido (CLIENTE/INACTIVO - 0 referidos)
        selected: '#FFD700',            // Dorado brillante (USUARIO SELECCIONADO)
        
        // Bordes
        border: '#ffffff',
        selectedBorder: '#FF8C00',  // Naranja oscuro para seleccionado
        activeBorder: '#ffffff'
    },
    nodeRadius: {
        min: 10,
        max: 25
    },
    // Umbrales para categorización
    thresholds: {
        topVentasPorMonto: 5000000,    // +$5,000,000 en ventas
        topVentasMenor: 2000000,       // $2,000,000 - $5,000,000 en ventas
        topReferidos: 20,              // +20 referidos
        clienteTopReferidor: 5,        // 5+ referidos para cliente ⭐ NUEVO
        vendedorActivo: 5,             // 5-10 referidos para vendedor
        vendedor: 1                    // 1-4 referidos
    }
};

// Variables globales que serán inicializadas desde la vista
let moduleRedData = [];
let moduleUsuarioSeleccionado = null;

/**
 * Inicializar datos desde variables globales de la vista
 */
function initializeDataFromGlobals(moduleRedDataFromView, moduleUsuarioSeleccionadoFromView) {
    moduleRedData = moduleRedDataFromView || [];
    moduleUsuarioSeleccionado = moduleUsuarioSeleccionadoFromView || null;

    console.log('Red Data loaded:', moduleRedData);
    console.log('Red Data count:', moduleRedData ? moduleRedData.length : 0);
    console.log('Usuario seleccionado:', moduleUsuarioSeleccionado);
}

/**
 * Inicializar la visualización principal
 */
function initializeVisualization() {
    console.log('Starting initializeVisualization...');
    // Buscar el contenedor con ambos IDs posibles
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');

    if (!container) {
        console.error('Network container not found! Looking for: referidos-network-container or network-container');
        return;
    }

    console.log('Container found:', container.id);

    const width = container.clientWidth;
    const height = container.clientHeight;
    console.log('Container dimensions:', width, 'x', height);

    // Limpiar contenedor
    d3.select('#' + container.id).selectAll('*').remove();

    // Crear SVG
    svg = d3.select('#' + container.id)
        .append('svg')
        .attr('width', '100%')
        .attr('height', '100%')
        .attr('viewBox', '0 0 ' + width + ' ' + height);

    // Grupo principal para zoom/pan
    g = svg.append('g');

    // Configurar zoom con mejor control
    zoom = d3.zoom()
        .scaleExtent([0.1, 5]) // Más rango de zoom
        .on('zoom', function(event) {
            g.attr('transform', event.transform);
        });

    svg.call(zoom)
        .on("dblclick.zoom", null); // Deshabilitar doble click para zoom

    // Agregar controles de zoom manual
    addZoomControls();

    // Procesar datos y crear visualización inicial
    processData();
    updateVisualization();
}

/**
 * Agregar controles de zoom manual
 */
function addZoomControls() {
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    
    // Crear contenedor de controles si no existe
    let controls = container.querySelector('.zoom-controls');
    if (!controls) {
        controls = document.createElement('div');
        controls.className = 'zoom-controls';
        controls.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 1000;
        `;
        
        // Botón zoom in
        const zoomIn = document.createElement('button');
        zoomIn.innerHTML = '<i class="bi bi-zoom-in"></i>';
        zoomIn.className = 'btn btn-sm btn-light shadow-sm';
        zoomIn.title = 'Acercar (Zoom In)';
        zoomIn.onclick = () => {
            svg.transition().call(zoom.scaleBy, 1.3);
        };
        
        // Botón zoom out
        const zoomOut = document.createElement('button');
        zoomOut.innerHTML = '<i class="bi bi-zoom-out"></i>';
        zoomOut.className = 'btn btn-sm btn-light shadow-sm';
        zoomOut.title = 'Alejar (Zoom Out)';
        zoomOut.onclick = () => {
            svg.transition().call(zoom.scaleBy, 0.7);
        };
        
        // Botón reset
        const reset = document.createElement('button');
        reset.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        reset.className = 'btn btn-sm btn-light shadow-sm';
        reset.title = 'Restablecer Vista';
        reset.onclick = () => resetZoom();
        
        controls.appendChild(zoomIn);
        controls.appendChild(zoomOut);
        controls.appendChild(reset);
        container.style.position = 'relative';
        container.appendChild(controls);
    }
}

/**
 * Resetear zoom a la vista inicial
 */
function resetZoom() {
    if (currentViewType === 'tree') {
        const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
        const nodesData = g.selectAll('.node').data();
        centerTreeView(nodesData, container.clientWidth, container.clientHeight);
    } else {
        svg.transition().duration(750).call(zoom.transform, d3.zoomIdentity);
    }
}

/**
 * Exportar SVG
 */
function exportSVG() {
    const svgElement = document.querySelector('#referidos-network-container svg') || 
                      document.querySelector('#network-container svg');
    if (!svgElement) return;
    
    const svgData = new XMLSerializer().serializeToString(svgElement);
    const blob = new Blob([svgData], {type: 'image/svg+xml'});
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `red-mlm-${new Date().toISOString().split('T')[0]}.svg`;
    link.click();
    URL.revokeObjectURL(url);
}

/**
 * Procesar datos jerárquicos para D3.js (Optimizado)
 */
function processData() {
    console.log('=== PROCESANDO DATOS D3.js ===');
    console.log('moduleRedData recibido:', moduleRedData);

    nodes = [];
    links = [];

    if (!moduleRedData || !Array.isArray(moduleRedData) || moduleRedData.length === 0) {
        console.log('No data available, showing empty state');
        showEmptyState();
        return;
    }

    console.log('Data found, processing', moduleRedData.length, 'root nodes');
    console.log('Primer nodo en moduleRedData:', moduleRedData[0]);

    // Convertir datos jerárquicos a formato de nodos y enlaces
    const nodeMap = new Map();
    const processedIds = new Set(); // Para detectar duplicados

    // Procesamiento optimizado con batching
    function processNode(nodeData, level = 0, parentId = null) {
        const nodeId = nodeData.id;

        // PREVENIR DUPLICADOS
        if (processedIds.has(nodeId)) {
            return;
        }

        // Crear nodo de forma más eficiente
        const node = {
            id: nodeId,
            name: nodeData.name,
            email: nodeData.email,
            cedula: nodeData.cedula,
            tipo: nodeData.tipo,
            level: level,
            referidos_count: nodeData.referidos_count,
            total_ventas: nodeData.total_ventas || 0, // Agregar total de ventas
            parentId: parentId,
            children: nodeData.hijos || []
        };

        nodes.push(node);
        nodeMap.set(nodeId, node);
        processedIds.add(nodeId);

        // Crear enlace con el padre si existe
        if (parentId) {
            links.push({
                source: parentId,
                target: nodeId
            });
        }

        // Procesar hijos de forma optimizada
        let children = nodeData.hijos;

        // Convertir objeto a array si es necesario
        if (children && typeof children === 'object' && !Array.isArray(children)) {
            children = Object.values(children);
        }

        if (children && Array.isArray(children) && children.length > 0) {
            // Usar requestIdleCallback para procesamiento no bloqueante de grandes conjuntos
            children.forEach(child => {
                processNode(child, level + 1, nodeId);
            });
        }
    }

    // Procesar todos los nodos raíz
    moduleRedData.forEach(rootNode => {
        processNode(rootNode, 0);
    });

    console.log('=== PROCESAMIENTO COMPLETO ===');
    console.log('Total nodos procesados:', nodes.length);
    console.log('Total enlaces:', links.length);

    // Verificar si hay nodos sin procesar
    if (nodes.length === 0) {
        console.error('❌ NO SE PROCESARON NODOS! Datos originales:', moduleRedData);
        showEmptyState();
        return;
    } else {
        console.log('✅ Nodos procesados correctamente');
    }

    // Actualizar métricas en tiempo real
    updateNetworkMetrics();
}

/**
 * Actualizar visualización según el tipo seleccionado
 */
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

/**
 * Renderizar vista de árbol
 */
function renderTreeView() {
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    const width = container.clientWidth;
    const height = container.clientHeight;

    // Obtener nodos raíz (sin padre)
    const rootNodes = nodes.filter(d => !d.parentId);
    console.log('Root nodes found:', rootNodes.map(n => n.name));

    // Usar los nodos tal como vienen, sin modificaciones para usuario seleccionado
    let modifiedNodes = nodes;

    // Para vista general, manejar múltiples raíces si es necesario
    if (!moduleUsuarioSeleccionado && rootNodes.length > 1) {
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

    // Crear jerarquía con los nodos finales
    const finalNodes = modifiedNodes || nodes;
    const root = d3.stratify()
        .id(d => d.id)
        .parentId(d => d.parentId)
        (finalNodes);

    // Calcular altura dinámica basada en el número de nodos
    const totalNodes = root.descendants().length;
    const minNodeSpacing = 80; // Espaciado mínimo entre nodos (aumentado de ~40 a 80)
    const calculatedHeight = Math.max(height, totalNodes * minNodeSpacing);
    
    // Calcular profundidad máxima para el ancho
    const maxDepth = Math.max(...root.descendants().map(d => d.depth));
    const nodeWidth = 250; // Espacio horizontal entre niveles (aumentado de ~150 a 250)
    const calculatedWidth = Math.max(width, maxDepth * nodeWidth + 200);

    console.log('Tree dimensions:', {
        totalNodes,
        maxDepth,
        calculatedWidth,
        calculatedHeight
    });

    // Configurar layout de árbol con tamaños dinámicos
    const treeLayout = d3.tree()
        .size([calculatedHeight - 100, calculatedWidth - 200])
        .separation((a, b) => {
            // Mayor separación entre nodos hermanos
            return a.parent === b.parent ? 1.5 : 2;
        });

    const treeData = treeLayout(root);

    // Renderizar árbol
    renderTree(treeData, calculatedWidth, calculatedHeight);
}

/**
 * Renderizar árbol con datos procesados (Optimizado)
 */
function renderTree(treeData, width, height) {
    // Determinar si hay raíz artificial para filtrarla
    const hasArtificialRoot = treeData.data && treeData.data.id === 'artificial-root';

    // Filtrar enlaces y nodos según el contexto
    let linksData = treeData.links();
    let nodesData = treeData.descendants();

    // Para usuarios específicos, mostrar todos los nodos sin filtrar
    if (moduleUsuarioSeleccionado) {
        console.log('Renderizando red específica para:', moduleUsuarioSeleccionado.name);
    } else if (hasArtificialRoot) {
        // Para vista general, filtrar raíz artificial si existe
        linksData = linksData.filter(d =>
            d.source.data.id !== 'artificial-root' && d.target.data.id !== 'artificial-root'
        );
        nodesData = nodesData.filter(d => d.data.id !== 'artificial-root');
    }

    // Ajustar viewBox del SVG para mostrar todo el árbol
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;
    
    // Usar dimensiones calculadas o del contenedor
    const viewBoxWidth = Math.max(width, containerWidth);
    const viewBoxHeight = Math.max(height, containerHeight);
    
    svg.attr('viewBox', `0 0 ${viewBoxWidth} ${viewBoxHeight}`);

    // Padding mejorado para los nodos
    const paddingX = 100;
    const paddingY = 60;

    // Crear enlaces con curvas suaves - Optimizado
    const linkGenerator = d3.linkHorizontal()
        .x(d => d.y + paddingX)
        .y(d => d.x + paddingY);

    const links = g.selectAll('.link')
        .data(linksData)
        .enter()
        .append('path')
        .attr('class', 'link')
        .attr('d', linkGenerator)
        .style('fill', 'none')
        .style('stroke', 'rgba(114, 47, 55, 0.3)')
        .style('stroke-width', 2.5)
        .style('stroke-linecap', 'round');

    // Crear nodos con mejor espaciado
    const nodeGroup = g.selectAll('.node')
        .data(nodesData)
        .enter()
        .append('g')
        .attr('class', 'node')
        .attr('transform', d => `translate(${d.y + paddingX}, ${d.x + paddingY})`)
        .style('cursor', 'pointer');

    // Círculos de nodos con mejor tamaño
    nodeGroup.append('circle')
        .attr('r', d => {
            const baseRadius = 12;
            const extraRadius = Math.min(8, d.data.referidos_count * 0.5);
            return baseRadius + extraRadius;
        })
        .style('fill', d => getNodeColor(d.data))
        .style('stroke', d => getNodeBorderColor(d.data))
        .style('stroke-width', d => getNodeBorderWidth(d.data))
        .style('filter', 'drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.2))');

    // Etiquetas de nodos con mejor posicionamiento
    nodeGroup.append('text')
        .attr('dy', '0.35em')
        .attr('x', d => d.children ? -25 : 25)
        .style('text-anchor', d => d.children ? 'end' : 'start')
        .style('font-size', '13px')
        .style('font-weight', '600')
        .style('fill', '#2d3748')
        .style('paint-order', 'stroke')
        .style('stroke', '#ffffff')
        .style('stroke-width', '3px')
        .style('stroke-linecap', 'round')
        .style('stroke-linejoin', 'round')
        .text(d => {
            const name = d.data ? d.data.name : d.name;
            return name && name.length > 20 ? name.substring(0, 20) + '...' : (name || 'Sin nombre');
        });

    // Agregar información adicional (tipo y referidos)
    nodeGroup.append('text')
        .attr('dy', '1.8em')
        .attr('x', d => d.children ? -25 : 25)
        .style('text-anchor', d => d.children ? 'end' : 'start')
        .style('font-size', '10px')
        .style('font-weight', '500')
        .style('fill', '#718096')
        .text(d => {
            const tipo = d.data.tipo ? d.data.tipo.charAt(0).toUpperCase() + d.data.tipo.slice(1) : '';
            const refs = d.data.referidos_count || 0;
            return `${tipo} • ${refs} ref${refs !== 1 ? 's' : ''}`;
        });

    // Agregar eventos
    addNodeEvents(nodeGroup);

    // Centrar la vista inicial en el árbol - LLAMADA INMEDIATA
    requestAnimationFrame(() => {
        centerTreeView(nodesData, viewBoxWidth, viewBoxHeight);
    });
}

/**
 * Renderizar vista de fuerza
 */
function renderForceView() {
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    const width = container.clientWidth;
    const height = container.clientHeight;

    // Detener simulación anterior si existe
    if (simulation) {
        simulation.stop();
    }

    // Crear simulación de fuerzas con parámetros optimizados
    simulation = d3.forceSimulation(nodes)
        .force('link', d3.forceLink(links).id(function(d) {
            return d.id;
        }).distance(120).strength(0.5))
        .force('charge', d3.forceManyBody().strength(-400))
        .force('center', d3.forceCenter(width / 2, height / 2))
        .force('collision', d3.forceCollide().radius(function(d) {
            return Math.max(config.nodeRadius.min, Math.min(config.nodeRadius.max, 8 + d.referidos_count)) + 10;
        }))
        .alphaDecay(0.02) // Convergencia más rápida
        .velocityDecay(0.3); // Reduce oscilaciones

    // Crear enlaces
    const link = g.selectAll('.link')
        .data(links)
        .enter()
        .append('line')
        .attr('class', 'link')
        .style('stroke', 'rgba(114, 47, 55, 0.2)')
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
                Math.min(config.nodeRadius.max, 10 + d.referidos_count));
        })
        .style('fill', function(d) {
            return getNodeColor(d);
        })
        .style('stroke', function(d) {
            return getNodeBorderColor(d);
        })
        .style('stroke-width', function(d) {
            return getNodeBorderWidth(d);
        });

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

    // Centrar y aplicar zoom inicial después de estabilizar
    simulation.on('end', function() {
        centerForceView(nodes, width, height);
    });
}

/**
 * Obtener color del nodo según su tipo y características
 * ORDEN DE PRIORIDAD:
 * 1. Usuario seleccionado (Dorado brillante)
 * 2. Top ventas por monto (+$5,000,000) - Crimson
 * 3. Top referidos (+20 referidos) - Rojo oscuro
 * 4. Ventas altas ($2M - $5M) - Dorado oscuro
 * 5. Cliente Top Referidor (cliente con 5+ referidos) - Azul Real ⭐ NUEVO
 * 6. Vendedor activo (5-10 referidos) - Vino rosado
 * 7. Líder - Vino tinto oscuro
 * 8. Vendedor (1-4 referidos) - Vino rosado claro
 * 9. Cliente con referidos (1-4) - Azul cielo ⭐ NUEVO
 * 10. Cliente/Inactivo (0 referidos) - Rosa pálido
 */
function getNodeColor(node) {
    // Prioridad 1: Usuario seleccionado/actual (DORADO BRILLANTE)
    if (moduleUsuarioSeleccionado && node.id === moduleUsuarioSeleccionado.id) {
        return config.colors.selected; // #FFD700
    }

    const referidosCount = node.referidos_count || 0;
    const totalVentas = node.total_ventas || 0;

    // Prioridad 2: Top Ventas por Monto (+$5,000,000) - CRIMSON
    if (totalVentas >= config.thresholds.topVentasPorMonto) {
        return config.colors.topVentasPorMonto; // #DC143C
    }

    // Prioridad 3: Top Referidos (+20 referidos) - ROJO OSCURO
    if (referidosCount >= config.thresholds.topReferidos) {
        return config.colors.topReferidos; // #8B0000
    }

    // Prioridad 4: Ventas Altas ($2M - $5M) - DORADO OSCURO
    if (totalVentas >= config.thresholds.topVentasMenor) {
        return config.colors.topVentasMenor; // #B8860B
    }

    // Prioridad 5: Cliente Top Referidor (cliente con 5+ referidos) - AZUL REAL ⭐ NUEVO
    if (node.tipo === 'cliente' && referidosCount >= config.thresholds.clienteTopReferidor) {
        return config.colors.clienteTopReferidor; // #4169E1
    }

    // Prioridad 6: Vendedor Activo (5-10 referidos) - VINO ROSADO
    if (node.tipo !== 'cliente' && referidosCount >= config.thresholds.vendedorActivo) {
        return config.colors.vendedorActivo; // #A8556A
    }

    // Prioridad 7: Líder (independiente de referidos si <5) - VINO TINTO OSCURO
    if (node.tipo === 'lider') {
        return config.colors.lider; // #722F37
    }

    // Prioridad 8: Vendedor con 1-4 referidos - VINO ROSADO CLARO
    if (node.tipo !== 'cliente' && referidosCount >= config.thresholds.vendedor) {
        return config.colors.vendedor; // #C89FA6
    }

    // Prioridad 9: Cliente con referidos (1-4) - AZUL CIELO ⭐ NUEVO
    if (node.tipo === 'cliente' && referidosCount >= config.thresholds.vendedor) {
        return config.colors.clienteConReferidos; // #87CEEB
    }

    // Prioridad 10: Cliente/Inactivo (0 referidos) - ROSA PÁLIDO
    return config.colors.cliente; // #E8D5D9
}

/**
 * Obtener color del borde del nodo
 */
function getNodeBorderColor(node) {
    // Usuario seleccionado tiene borde naranja oscuro
    if (moduleUsuarioSeleccionado && node.id === moduleUsuarioSeleccionado.id) {
        return config.colors.selectedBorder; // #FF8C00 - Naranja oscuro
    }

    const referidosCount = node.referidos_count || 0;

    // Top ventas y top referidos tienen borde blanco destacado
    if (referidosCount >= config.thresholds.vendedorActivo) {
        return config.colors.activeBorder; // #ffffff - Blanco
    }

    // Resto tiene borde gris claro
    return '#e0e0e0';
}

/**
 * Obtener ancho del borde del nodo
 */
function getNodeBorderWidth(node) {
    // Usuario seleccionado tiene borde más grueso (destacado)
    if (moduleUsuarioSeleccionado && node.id === moduleUsuarioSeleccionado.id) {
        return 6;
    }

    const referidosCount = node.referidos_count || 0;

    // Top ventas tienen borde muy grueso
    if (referidosCount >= config.thresholds.topVentas) {
        return 5;
    }

    // Top referidos y vendedores activos tienen borde grueso
    if (referidosCount >= config.thresholds.vendedorActivo) {
        return 4;
    }

    // Resto tiene borde normal
    return 2;
}

/**
 * Agregar eventos a los nodos (Optimizado)
 */
function addNodeEvents(nodeSelection) {
    // Crear tooltip si no existe
    let tooltip = d3.select('#network-tooltip');
    if (tooltip.empty()) {
        tooltip = d3.select('body')
            .append('div')
            .attr('id', 'network-tooltip')
            .style('position', 'absolute')
            .style('background', 'rgba(0, 0, 0, 0.9)')
            .style('color', 'white')
            .style('padding', '12px 16px')
            .style('border-radius', '8px')
            .style('pointer-events', 'none')
            .style('opacity', 0)
            .style('z-index', 10000)
            .style('font-size', '13px')
            .style('line-height', '1.6')
            .style('box-shadow', '0 4px 12px rgba(0, 0, 0, 0.3)')
            .style('transition', 'opacity 0.15s'); // Transición más rápida
    }

    // Usar debounce para el tooltip en caso de muchos nodos
    let tooltipTimer;

    nodeSelection
        .on('mouseover', function(event, d) {
            clearTimeout(tooltipTimer);
            
            const nodeData = d.data || d;
            const referidosCount = nodeData.referidos_count || 0;
            const totalVentas = nodeData.total_ventas || 0;

            // Determinar categoría con nuevas categorías de clientes
            let categoria = 'Cliente';
            let categoriaIcon = 'bi-person';
            if (totalVentas >= config.thresholds.topVentasPorMonto) {
                categoria = 'Top Ventas';
                categoriaIcon = 'bi-trophy-fill';
            } else if (referidosCount >= config.thresholds.topReferidos) {
                categoria = 'Red Grande';
                categoriaIcon = 'bi-star-fill';
            } else if (totalVentas >= config.thresholds.topVentasMenor) {
                categoria = 'Ventas Altas';
                categoriaIcon = 'bi-currency-dollar';
            } else if (nodeData.tipo === 'cliente' && referidosCount >= config.thresholds.clienteTopReferidor) {
                categoria = 'Cliente Top Referidor'; // ⭐ NUEVO
                categoriaIcon = 'bi-person-hearts';
            } else if (nodeData.tipo !== 'cliente' && referidosCount >= config.thresholds.vendedorActivo) {
                categoria = 'Red Activa';
                categoriaIcon = 'bi-people-fill';
            } else if (nodeData.tipo === 'lider') {
                categoria = 'Líder';
                categoriaIcon = 'bi-award-fill';
            } else if (nodeData.tipo !== 'cliente' && referidosCount >= config.thresholds.vendedor) {
                categoria = 'Vendedor';
                categoriaIcon = 'bi-person-fill';
            } else if (nodeData.tipo === 'cliente' && referidosCount >= config.thresholds.vendedor) {
                categoria = 'Cliente con Referidos'; // ⭐ NUEVO
                categoriaIcon = 'bi-person-plus';
            }

            // Resaltar nodo
            d3.select(this).select('circle')
                .style('filter', 'brightness(1.2)')
                .style('stroke-width', getNodeBorderWidth(nodeData) + 2);

            // Formatear ventas
            const ventasFormateadas = totalVentas > 0 
                ? '$' + totalVentas.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                : '$0';

            // Template mejorado del tooltip con más información
            const tooltipHTML = `
                <div style="min-width: 250px; max-width: 350px;">
                    <div style="border-bottom: 2px solid #FFD700; padding-bottom: 8px; margin-bottom: 8px;">
                        <strong style="font-size: 15px; color: #FFD700; display: block;">
                            <i class="bi ${categoriaIcon}"></i> ${nodeData.name || 'Sin nombre'}
                        </strong>
                        <span style="color: #aaa; font-size: 12px;">${nodeData.email || 'Sin email'}</span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                        <div>
                            <span style="color: #888; font-size: 11px;">CATEGORÍA</span><br>
                            <strong style="color: #FFD700; font-size: 13px;">${categoria}</strong>
                        </div>
                        <div>
                            <span style="color: #888; font-size: 11px;">ROL</span><br>
                            <strong style="color: #4CAF50; font-size: 13px;">${nodeData.tipo ? nodeData.tipo.toUpperCase() : 'N/A'}</strong>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                        <div>
                            <span style="color: #888; font-size: 11px;">REFERIDOS</span><br>
                            <strong style="color: #4CAF50; font-size: 14px;">
                                <i class="bi bi-people"></i> ${referidosCount}
                            </strong>
                        </div>
                        <div>
                            <span style="color: #888; font-size: 11px;">VENTAS TOTALES</span><br>
                            <strong style="color: #4CAF50; font-size: 14px;">
                                <i class="bi bi-cash-coin"></i> ${ventasFormateadas}
                            </strong>
                        </div>
                    </div>
                    
                    ${nodeData.cedula ? `
                        <div style="margin-bottom: 8px;">
                            <span style="color: #888; font-size: 11px;">CÉDULA</span><br>
                            <span style="color: #ddd; font-size: 13px;">${nodeData.cedula}</span>
                        </div>
                    ` : ''}
                    
                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #444; color: #FFD700; font-size: 11px; text-align: center;">
                        <i class="bi bi-hand-index"></i> Click para ver detalles completos
                    </div>
                </div>
            `;

            tooltip
                .style('opacity', 1)
                .style('left', (event.pageX + 15) + 'px')
                .style('top', (event.pageY - 15) + 'px')
                .html(tooltipHTML);
        })
        .on('mousemove', function(event) {
            tooltip
                .style('left', (event.pageX + 15) + 'px')
                .style('top', (event.pageY - 15) + 'px');
        })
        .on('mouseout', function(event, d) {
            clearTimeout(tooltipTimer);
            tooltipTimer = setTimeout(() => {
                const nodeData = d.data || d;
                
                // Restaurar estilo normal
                d3.select(this).select('circle')
                    .style('filter', 'none')
                    .style('stroke-width', getNodeBorderWidth(nodeData));

                tooltip.style('opacity', 0);
            }, 50); // Pequeño delay para evitar flickering
        })
        .on('click', function(event, d) {
            const nodeData = d.data || d;
            
            if (nodeData.id && nodeData.id !== 'artificial-root') {
                const url = window.routes && window.routes.show 
                    ? window.routes.show.replace(':id', nodeData.id)
                    : `/admin/referidos/${nodeData.id}`;
                
                // Animación de click más rápida
                d3.select(this).select('circle')
                    .transition()
                    .duration(150)
                    .attr('r', function(d) {
                        const currentRadius = Math.max(config.nodeRadius.min,
                            Math.min(config.nodeRadius.max, 10 + (nodeData.referidos_count || 0)));
                        return currentRadius * 1.2;
                    })
                    .transition()
                    .duration(150)
                    .attr('r', function(d) {
                        return Math.max(config.nodeRadius.min,
                            Math.min(config.nodeRadius.max, 10 + (nodeData.referidos_count || 0)));
                    });

                setTimeout(() => {
                    window.location.href = url;
                }, 250);
            }
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

/**
 * Resetear zoom
 */
function resetZoom() {
    svg.transition()
        .duration(750)
        .call(zoom.transform, d3.zoomIdentity);
}

/**
 * Exportar SVG
 */
function exportSVG() {
    const svgElement = document.querySelector('#referidos-network-container svg') || document.querySelector('#network-container svg');

    if (!svgElement) {
        console.error('SVG element not found for export');
        return;
    }

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

/**
 * Mostrar estado vacío
 */
function showEmptyState() {
    console.log('Showing empty state');
    const containerId = document.getElementById('referidos-network-container') ? 'referidos-network-container' : 'network-container';
    const container = d3.select('#' + containerId);
    container.selectAll('*').remove();

    // Determinar el tipo de mensaje según el contexto
    let message = 'Red MLM no disponible';
    let submessage = 'No hay datos de red disponibles en este momento.';
    let icon = 'bi-diagram-3';
    let color = '#6c757d';

    if (moduleUsuarioSeleccionado) {
        message = `Red de ${moduleUsuarioSeleccionado.name}`;
        submessage = 'Este usuario no tiene una red de referidos configurada.';
        icon = 'bi-person-circle';
        color = '#ffc107';
    }

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
        </div>
    `);
}

/**
 * Actualizar métricas de la red
 */
function updateNetworkMetrics() {
    // Actualizar métricas de la red
    const totalNodesEl = document.getElementById('total-nodes');
    const totalConnectionsEl = document.getElementById('total-connections');
    const maxDepthEl = document.getElementById('max-depth');
    const avgReferralsEl = document.getElementById('avg-referrals');

    if (totalNodesEl) totalNodesEl.textContent = nodes.length;
    if (totalConnectionsEl) totalConnectionsEl.textContent = links.length;

    // Calcular niveles máximos
    const maxLevel = Math.max(...nodes.map(n => n.level || 0)) + 1;
    if (maxDepthEl) maxDepthEl.textContent = maxLevel;

    // Calcular promedio de referidos
    const totalReferrals = nodes.reduce((sum, n) => sum + (n.referidos_count || 0), 0);
    const avgReferrals = nodes.length > 0 ? (totalReferrals / nodes.length).toFixed(1) : 0;
    if (avgReferralsEl) avgReferralsEl.textContent = avgReferrals;
}

// Redimensionar al cambiar tamaño de ventana
window.addEventListener('resize', function() {
    if (svg) {
        const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
        const width = container.clientWidth;
        const height = container.clientHeight;
        svg.attr('viewBox', '0 0 ' + width + ' ' + height);

        if (currentViewType === 'force' && simulation) {
            simulation.force('center', d3.forceCenter(width / 2, height / 2));
            simulation.alpha(0.3).restart();
        }
    }
});

// Exponer funciones globales necesarias
window.NetworkVisualization = {
    initializeDataFromGlobals,
    initializeVisualization,
    updateVisualization,
    resetZoom,
    exportSVG,
    changeViewType: function(viewType) {
        currentViewType = viewType;
        updateVisualization();
    }
};

/**
 * Centrar la vista del árbol (Optimizado con más zoom)
 */
function centerTreeView(nodesData, viewBoxWidth, viewBoxHeight) {
    if (!nodesData || nodesData.length === 0) return;

    // Calcular el centro del árbol
    const paddingX = 100;
    const paddingY = 60;
    
    const xValues = nodesData.map(d => d.y + paddingX);
    const yValues = nodesData.map(d => d.x + paddingY);
    
    const minX = Math.min(...xValues);
    const maxX = Math.max(...xValues);
    const minY = Math.min(...yValues);
    const maxY = Math.max(...yValues);
    
    const treeWidth = maxX - minX;
    const treeHeight = maxY - minY;
    const treeCenterX = (minX + maxX) / 2;
    const treeCenterY = (minY + maxY) / 2;
    
    // Calcular el centro del viewport
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;
    const viewportCenterX = containerWidth / 2;
    const viewportCenterY = containerHeight / 2;
    
    // Calcular escala para que el árbol quepa en el viewport con margen - MÁS ZOOM
    const marginFactor = 0.75; // 75% del viewport para más zoom
    const scaleX = (containerWidth * marginFactor) / treeWidth;
    const scaleY = (containerHeight * marginFactor) / treeHeight;
    const initialScale = Math.min(scaleX, scaleY, 1.2); // Permitir zoom hasta 1.2x
    
    // Calcular el desplazamiento
    const translateX = viewportCenterX - (treeCenterX * initialScale);
    const translateY = viewportCenterY - (treeCenterY * initialScale);
    
    console.log('Tree centering:', {
        treeSize: { width: treeWidth, height: treeHeight },
        treeCenter: { x: treeCenterX, y: treeCenterY },
        viewportCenter: { x: viewportCenterX, y: viewportCenterY },
        scale: initialScale,
        translate: { x: translateX, y: translateY }
    });
    
    // Aplicar transformación inicial con animación suave
    const initialTransform = d3.zoomIdentity
        .translate(translateX, translateY)
        .scale(initialScale);
    
    svg.transition()
        .duration(500) // Reducido de 750ms a 500ms
        .call(zoom.transform, initialTransform);
}

/**
 * Centrar la vista de fuerza (Optimizado con más zoom)
 */
function centerForceView(nodesData, width, height) {
    if (!nodesData || nodesData.length === 0) return;

    // Calcular límites de los nodos
    const xValues = nodesData.map(d => d.x);
    const yValues = nodesData.map(d => d.y);
    
    const minX = Math.min(...xValues);
    const maxX = Math.max(...xValues);
    const minY = Math.min(...yValues);
    const maxY = Math.max(...yValues);
    
    const graphWidth = maxX - minX;
    const graphHeight = maxY - minY;
    const graphCenterX = (minX + maxX) / 2;
    const graphCenterY = (minY + maxY) / 2;
    
    // Calcular el centro del viewport
    const viewportCenterX = width / 2;
    const viewportCenterY = height / 2;
    
    // Calcular escala para que el grafo quepa en el viewport - MÁS ZOOM
    const marginFactor = 0.7; // 70% del viewport para más zoom
    const scaleX = (width * marginFactor) / graphWidth;
    const scaleY = (height * marginFactor) / graphHeight;
    const initialScale = Math.min(scaleX, scaleY, 1.5); // Permitir más zoom
    
    // Calcular el desplazamiento
    const translateX = viewportCenterX - (graphCenterX * initialScale);
    const translateY = viewportCenterY - (graphCenterY * initialScale);
    
    console.log('Force centering:', {
        graphSize: { width: graphWidth, height: graphHeight },
        graphCenter: { x: graphCenterX, y: graphCenterY },
        viewportCenter: { x: viewportCenterX, y: viewportCenterY },
        scale: initialScale,
        translate: { x: translateX, y: translateY }
    });
    
    // Aplicar transformación con animación
    const initialTransform = d3.zoomIdentity
        .translate(translateX, translateY)
        .scale(initialScale);
    
    svg.transition()
        .duration(500)
        .call(zoom.transform, initialTransform);
}

// ===== OPTIMIZACIÓN DE CARGA =====
// Usar IntersectionObserver para carga lazy de visualización
if ('IntersectionObserver' in window) {
    const observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !svg) {
                console.log('📊 Iniciando visualización (lazy load)');
                // Pequeño delay para mejorar la percepción de velocidad
                setTimeout(() => {
                    initializeVisualization();
                }, 100);
                observer.disconnect();
            }
        });
    }, observerOptions);

    // Observar el contenedor cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('referidos-network-container') || 
                         document.getElementById('network-container');
        if (container) {
            observer.observe(container);
        }
    });
}

/**
 * Centrar la vista del árbol
 */
function centerTreeView(nodesData, viewBoxWidth, viewBoxHeight) {
    if (!nodesData || nodesData.length === 0) return;

    // Calcular el centro del árbol
    const paddingX = 100;
    const paddingY = 60;
    
    const xValues = nodesData.map(d => d.y + paddingX);
    const yValues = nodesData.map(d => d.x + paddingY);
    
    const minX = Math.min(...xValues);
    const maxX = Math.max(...xValues);
    const minY = Math.min(...yValues);
    const maxY = Math.max(...yValues);
    
    const treeWidth = maxX - minX;
    const treeHeight = maxY - minY;
    const treeCenterX = (minX + maxX) / 2;
    const treeCenterY = (minY + maxY) / 2;
    
    // Calcular el centro del viewport
    const container = document.getElementById('referidos-network-container') || document.getElementById('network-container');
    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;
    const viewportCenterX = containerWidth / 2;
    const viewportCenterY = containerHeight / 2;
    
    // Calcular escala para que el árbol quepa en el viewport con margen - MÁS ZOOM
    const marginFactor = 0.75; // 75% del viewport para más zoom
    const scaleX = (containerWidth * marginFactor) / treeWidth;
    const scaleY = (containerHeight * marginFactor) / treeHeight;
    const initialScale = Math.min(scaleX, scaleY, 1.2); // Permitir zoom hasta 1.2x
    
    // Calcular el desplazamiento
    const translateX = viewportCenterX - (treeCenterX * initialScale);
    const translateY = viewportCenterY - (treeCenterY * initialScale);
    
    console.log('Tree centering:', {
        treeSize: { width: treeWidth, height: treeHeight },
        treeCenter: { x: treeCenterX, y: treeCenterY },
        viewportCenter: { x: viewportCenterX, y: viewportCenterY },
        scale: initialScale,
        translate: { x: translateX, y: translateY }
    });
    
    // Aplicar transformación inicial con animación suave
    const initialTransform = d3.zoomIdentity
        .translate(translateX, translateY)
        .scale(initialScale);
    
    svg.transition()
        .duration(500) // Reducido de 750ms a 500ms
        .call(zoom.transform, initialTransform);
}

/**
 * Centrar la vista de fuerza
 */
function centerForceView(nodesData, width, height) {
    if (!nodesData || nodesData.length === 0) return;

    // Calcular límites de los nodos
    const xValues = nodesData.map(d => d.x);
    const yValues = nodesData.map(d => d.y);
    
    const minX = Math.min(...xValues);
    const maxX = Math.max(...xValues);
    const minY = Math.min(...yValues);
    const maxY = Math.max(...yValues);
    
    const graphWidth = maxX - minX;
    const graphHeight = maxY - minY;
    const graphCenterX = (minX + maxX) / 2;
    const graphCenterY = (minY + maxY) / 2;
    
    // Calcular el centro del viewport
    const viewportCenterX = width / 2;
    const viewportCenterY = height / 2;
    
    // Calcular escala para que el grafo quepa en el viewport - MÁS ZOOM
    const marginFactor = 0.7; // 70% del viewport para más zoom
    const scaleX = (width * marginFactor) / graphWidth;
    const scaleY = (height * marginFactor) / graphHeight;
    const initialScale = Math.min(scaleX, scaleY, 1.5); // Permitir más zoom
    
    // Calcular el desplazamiento
    const translateX = viewportCenterX - (graphCenterX * initialScale);
    const translateY = viewportCenterY - (graphCenterY * initialScale);
    
    console.log('Force centering:', {
        graphSize: { width: graphWidth, height: graphHeight },
        graphCenter: { x: graphCenterX, y: graphCenterY },
        viewportCenter: { x: viewportCenterX, y: viewportCenterY },
        scale: initialScale,
        translate: { x: translateX, y: translateY }
    });
    
    // Aplicar transformación con animación
    const initialTransform = d3.zoomIdentity
        .translate(translateX, translateY)
        .scale(initialScale);
    
    svg.transition()
        .duration(500)
        .call(zoom.transform, initialTransform);
}