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
        topVentas: '#8B0000',       // Rojo oscuro intenso (TOP VENTAS - más de 20 referidos)
        lider: '#722F37',           // Vino tinto oscuro (LÍDER)
        topReferidos: '#B8860B',    // Dorado oscuro (TOP REFERIDOS - 10-20 referidos)
        vendedorActivo: '#A8556A',  // Vino rosado (VENDEDOR ACTIVO - 5-10 referidos)
        vendedor: '#C89FA6',        // Vino rosado claro (VENDEDOR - 1-5 referidos)
        cliente: '#E8D5D9',         // Rosa pálido (CLIENTE/INACTIVO - 0 referidos)
        selected: '#FFD700',        // Dorado brillante (USUARIO SELECCIONADO)
        
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
        topVentas: 20,      // +20 referidos
        topReferidos: 10,   // 10-20 referidos
        vendedorActivo: 5,  // 5-10 referidos
        vendedor: 1         // 1-5 referidos
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

/**
 * Procesar datos jerárquicos para D3.js
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

    function processNode(nodeData, level = 0, parentId = null) {
        const nodeId = nodeData.id;

        // PREVENIR DUPLICADOS - Si ya se procesó este nodo, saltar silenciosamente
        if (processedIds.has(nodeId)) {
            // Duplicado detectado, omitir sin warning (esto es normal en estructuras MLM complejas)
            return;
        }

        // Procesar cada nodo
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
        processedIds.add(nodeId); // Marcar como procesado

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
    moduleRedData.forEach(rootNode => {
        processNode(rootNode, 0);
    });

    console.log('=== PROCESAMIENTO COMPLETO ===');
    console.log('Total nodos procesados:', nodes.length);
    console.log('Total enlaces:', links.length);
    console.log('Nombres de todos los nodos:', nodes.map(n => `${n.name} (${n.cedula}) - Tipo: ${n.tipo}`));

    // Verificar si hay nodos sin procesar
    if (nodes.length === 0) {
        console.error('❌ NO SE PROCESARON NODOS! Datos originales:', moduleRedData);
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

    // Configurar layout de árbol
    const treeLayout = d3.tree()
        .size([width - 100, height - 100]);

    const treeData = treeLayout(root);

    // Renderizar árbol
    renderTree(treeData, width, height);
}

/**
 * Renderizar árbol con datos procesados
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
        .style('stroke', 'rgba(114, 47, 55, 0.2)')
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
                Math.min(config.nodeRadius.max, 10 + d.data.referidos_count));
        })
        .style('fill', function(d) {
            return getNodeColor(d.data);
        })
        .style('stroke', function(d) {
            return getNodeBorderColor(d.data);
        })
        .style('stroke-width', function(d) {
            return getNodeBorderWidth(d.data);
        });

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
            // Usar d.data.name para vista de árbol (hierarchical data)
            const name = d.data ? d.data.name : d.name;
            return name && name.length > 15 ? name.substring(0, 15) + '...' : (name || 'Sin nombre');
        });

    // Agregar eventos
    addNodeEvents(nodeGroup);
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
}

/**
 * Obtener color del nodo según su tipo y características
 * ORDEN DE PRIORIDAD:
 * 1. Usuario seleccionado (Dorado brillante)
 * 2. Top ventas (+20 referidos) - Rojo oscuro
 * 3. Top referidos (10-20 referidos) - Dorado oscuro
 * 4. Vendedor activo (5-10 referidos) - Vino rosado
 * 5. Líder - Vino tinto oscuro
 * 6. Vendedor (1-5 referidos) - Vino rosado claro
 * 7. Cliente/Inactivo (0 referidos) - Rosa pálido
 */
function getNodeColor(node) {
    // Prioridad 1: Usuario seleccionado/actual (DORADO BRILLANTE)
    if (moduleUsuarioSeleccionado && node.id === moduleUsuarioSeleccionado.id) {
        return config.colors.selected; // #FFD700
    }

    const referidosCount = node.referidos_count || 0;

    // Prioridad 2: Top Ventas (+20 referidos) - ROJO OSCURO
    if (referidosCount >= config.thresholds.topVentas) {
        return config.colors.topVentas; // #8B0000
    }

    // Prioridad 3: Top Referidos (10-20 referidos) - DORADO OSCURO
    if (referidosCount >= config.thresholds.topReferidos) {
        return config.colors.topReferidos; // #B8860B
    }

    // Prioridad 4: Vendedor Activo (5-10 referidos) - VINO ROSADO
    if (referidosCount >= config.thresholds.vendedorActivo) {
        return config.colors.vendedorActivo; // #A8556A
    }

    // Prioridad 5: Líder (independiente de referidos si <5) - VINO TINTO OSCURO
    if (node.tipo === 'lider') {
        return config.colors.lider; // #722F37
    }

    // Prioridad 6: Vendedor con 1-5 referidos - VINO ROSADO CLARO
    if (referidosCount >= config.thresholds.vendedor) {
        return config.colors.vendedor; // #C89FA6
    }

    // Prioridad 7: Cliente/Inactivo (0 referidos) - ROSA PÁLIDO
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
 * Agregar eventos a los nodos
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
            .style('transition', 'opacity 0.2s');
    }

    nodeSelection
        .on('mouseover', function(event, d) {
            // Determinar si es vista de árbol (d.data) o vista de fuerza (d directo)
            const nodeData = d.data || d;
            const referidosCount = nodeData.referidos_count || 0;

            // Determinar categoría para mostrar en tooltip
            let categoria = 'Cliente/Inactivo';
            if (referidosCount >= config.thresholds.topVentas) {
                categoria = '🏆 Top Ventas';
            } else if (referidosCount >= config.thresholds.topReferidos) {
                categoria = '⭐ Top Referidos';
            } else if (referidosCount >= config.thresholds.vendedorActivo) {
                categoria = '✅ Vendedor Activo';
            } else if (nodeData.tipo === 'lider') {
                categoria = '👑 Líder';
            } else if (referidosCount >= config.thresholds.vendedor) {
                categoria = '👤 Vendedor';
            }

            // Resaltar nodo
            d3.select(this).select('circle')
                .style('filter', 'brightness(1.2)')
                .style('stroke-width', function(d) {
                    const currentWidth = getNodeBorderWidth(nodeData);
                    return currentWidth + 2;
                });

            tooltip
                .style('opacity', 1)
                .style('left', (event.pageX + 15) + 'px')
                .style('top', (event.pageY - 15) + 'px')
                .html(`
                <div style="min-width: 200px;">
                    <strong style="font-size: 14px; color: #FFD700;">${nodeData.name || 'Sin nombre'}</strong><br>
                    <span style="color: #aaa;">Categoría:</span> <strong style="color: #FFD700;">${categoria}</strong><br>
                    <span style="color: #aaa;">Cédula:</span> ${nodeData.cedula || 'N/A'}<br>
                    <span style="color: #aaa;">Tipo:</span> ${nodeData.tipo ? nodeData.tipo.charAt(0).toUpperCase() + nodeData.tipo.slice(1) : 'N/A'}<br>
                    <span style="color: #aaa;">Email:</span> ${nodeData.email || 'N/A'}<br>
                    <span style="color: #aaa;">Referidos:</span> <strong style="color: #4CAF50;">${referidosCount}</strong><br>
                    <span style="color: #aaa;">Nivel:</span> ${nodeData.nivel || (nodeData.level ? nodeData.level + 1 : 'N/A')}<br>
                    <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #444; color: #FFD700; font-size: 11px;">
                        💡 Click para ver detalles
                    </div>
                </div>
            `);
        })
        .on('mousemove', function(event) {
            tooltip
                .style('left', (event.pageX + 15) + 'px')
                .style('top', (event.pageY - 15) + 'px');
        })
        .on('mouseout', function(event, d) {
            const nodeData = d.data || d;
            
            // Restaurar estilo normal
            d3.select(this).select('circle')
                .style('filter', 'none')
                .style('stroke-width', getNodeBorderWidth(nodeData));

            tooltip.style('opacity', 0);
        })
        .on('click', function(event, d) {
            // Obtener datos del nodo
            const nodeData = d.data || d;
            
            // Redirigir a la página de detalles del usuario
            if (nodeData.id && nodeData.id !== 'artificial-root') {
                // Usar la ruta de show de referidos para ver la red del usuario
                const url = window.routes && window.routes.show 
                    ? window.routes.show.replace(':id', nodeData.id)
                    : `/admin/referidos/${nodeData.id}`;
                
                // Mostrar feedback visual
                d3.select(this).select('circle')
                    .transition()
                    .duration(200)
                    .attr('r', function(d) {
                        const currentRadius = Math.max(config.nodeRadius.min,
                            Math.min(config.nodeRadius.max, 10 + (nodeData.referidos_count || 0)));
                        return currentRadius * 1.2;
                    })
                    .transition()
                    .duration(200)
                    .attr('r', function(d) {
                        return Math.max(config.nodeRadius.min,
                            Math.min(config.nodeRadius.max, 10 + (nodeData.referidos_count || 0)));
                    });

                // Redirigir después de la animación
                setTimeout(() => {
                    window.location.href = url;
                }, 300);
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