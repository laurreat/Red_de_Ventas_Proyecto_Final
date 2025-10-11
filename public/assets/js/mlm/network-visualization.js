/**
 * MLM Network Visualization using D3.js
 * Visualizes multi-level marketing network structure
 */
class MLMNetworkVisualization {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = d3.select(`#${containerId}`);

        // Default configuration
        this.config = {
            width: options.width || 1200,
            height: options.height || 800,
            nodeRadius: options.nodeRadius || 20,
            linkDistance: options.linkDistance || 100,
            charge: options.charge || -300,
            colors: {
                admin: '#722f37',
                lider: '#ffc107',
                vendedor: '#17a2b8',
                cliente: '#6c757d'
            },
            ...options
        };

        this.nodes = [];
        this.links = [];
        this.simulation = null;
        this.svg = null;
        this.g = null;

        this.initVisualization();
    }

    initVisualization() {
        // Clear previous content
        this.container.selectAll('*').remove();

        // Create SVG
        this.svg = this.container
            .append('svg')
            .attr('width', this.config.width)
            .attr('height', this.config.height)
            .call(d3.zoom()
                .scaleExtent([0.1, 4])
                .on('zoom', (event) => {
                    this.g.attr('transform', event.transform);
                }));

        // Create main group
        this.g = this.svg.append('g');

        // Add patterns for node types
        this.createPatterns();

        // Initialize force simulation
        this.simulation = d3.forceSimulation()
            .force('link', d3.forceLink().id(d => d.id).distance(this.config.linkDistance))
            .force('charge', d3.forceManyBody().strength(this.config.charge))
            .force('center', d3.forceCenter(this.config.width / 2, this.config.height / 2))
            .force('collision', d3.forceCollide().radius(this.config.nodeRadius + 5));
    }

    createPatterns() {
        const defs = this.svg.append('defs');

        // Create gradient for leader nodes
        const gradient = defs.append('radialGradient')
            .attr('id', 'leader-gradient')
            .attr('cx', '30%')
            .attr('cy', '30%');

        gradient.append('stop')
            .attr('offset', '0%')
            .attr('stop-color', '#ffd700');

        gradient.append('stop')
            .attr('offset', '100%')
            .attr('stop-color', '#ffc107');

        // Create pattern for admin nodes
        const pattern = defs.append('pattern')
            .attr('id', 'admin-pattern')
            .attr('patternUnits', 'userSpaceOnUse')
            .attr('width', 4)
            .attr('height', 4);

        pattern.append('rect')
            .attr('width', 4)
            .attr('height', 4)
            .attr('fill', this.config.colors.admin);

        pattern.append('rect')
            .attr('width', 2)
            .attr('height', 2)
            .attr('fill', '#944a54');
    }

    loadData(apiUrl) {
        return fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.processData(data.data);
                    this.render();
                } else {
                    console.error('Error loading network data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching network data:', error);
            });
    }

    processData(rawData) {
        this.nodes = [];
        this.links = [];
        const nodeMap = new Map();

        // Process nodes recursively
        this.processNodes(rawData, null, 0, nodeMap);
    }

    processNodes(nodes, parentId, level, nodeMap) {
        nodes.forEach(nodeData => {
            const node = {
                id: nodeData.id,
                name: nodeData.name,
                email: nodeData.email,
                tipo: nodeData.tipo,
                codigo_referido: nodeData.codigo_referido,
                referidos_count: nodeData.referidos_count,
                level: level,
                parentId: parentId,
                x: Math.random() * this.config.width,
                y: Math.random() * this.config.height
            };

            this.nodes.push(node);
            nodeMap.set(node.id, node);

            // Create link to parent if exists
            if (parentId) {
                this.links.push({
                    source: parentId,
                    target: node.id,
                    type: 'referral'
                });
            }

            // Process children
            if (nodeData.children && nodeData.children.length > 0) {
                this.processNodes(nodeData.children, node.id, level + 1, nodeMap);
            }
        });
    }

    render() {
        // Clear previous render
        this.g.selectAll('.link').remove();
        this.g.selectAll('.node').remove();
        this.g.selectAll('.label').remove();

        // Draw links
        const link = this.g.selectAll('.link')
            .data(this.links)
            .enter().append('line')
            .attr('class', 'link')
            .attr('stroke', '#999')
            .attr('stroke-opacity', 0.6)
            .attr('stroke-width', 2);

        // Draw nodes
        const node = this.g.selectAll('.node')
            .data(this.nodes)
            .enter().append('circle')
            .attr('class', 'node')
            .attr('r', d => this.config.nodeRadius + (d.referidos_count * 2))
            .attr('fill', d => this.getNodeColor(d))
            .attr('stroke', '#fff')
            .attr('stroke-width', 2)
            .style('cursor', 'pointer')
            .call(this.createDragBehavior());

        // Add labels
        const label = this.g.selectAll('.label')
            .data(this.nodes)
            .enter().append('text')
            .attr('class', 'label')
            .attr('text-anchor', 'middle')
            .attr('dy', '.35em')
            .style('font-size', '12px')
            .style('font-weight', 'bold')
            .style('fill', '#333')
            .style('pointer-events', 'none')
            .text(d => d.name.split(' ')[0]); // First name only

        // Add node interactions
        this.addNodeInteractions(node);

        // Update simulation
        this.simulation
            .nodes(this.nodes)
            .on('tick', () => {
                link
                    .attr('x1', d => d.source.x)
                    .attr('y1', d => d.source.y)
                    .attr('x2', d => d.target.x)
                    .attr('y2', d => d.target.y);

                node
                    .attr('cx', d => d.x)
                    .attr('cy', d => d.y);

                label
                    .attr('x', d => d.x)
                    .attr('y', d => d.y);
            });

        this.simulation.force('link')
            .links(this.links);

        this.simulation.alpha(1).restart();
    }

    getNodeColor(node) {
        switch (node.tipo) {
            case 'admin':
                return 'url(#admin-pattern)';
            case 'lider':
                return 'url(#leader-gradient)';
            case 'vendedor':
                return this.config.colors.vendedor;
            default:
                return this.config.colors.cliente;
        }
    }

    createDragBehavior() {
        return d3.drag()
            .on('start', (event, d) => {
                if (!event.active) this.simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            })
            .on('drag', (event, d) => {
                d.fx = event.x;
                d.fy = event.y;
            })
            .on('end', (event, d) => {
                if (!event.active) this.simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
            });
    }

    addNodeInteractions(nodeSelection) {
        // Tooltip
        const tooltip = d3.select('body').append('div')
            .attr('class', 'mlm-tooltip')
            .style('opacity', 0);

        nodeSelection
            .on('mouseover', (event, d) => {
                tooltip.transition()
                    .duration(200)
                    .style('opacity', .9);

                tooltip.html(`
                    <div class="tooltip-header">
                        <strong>${d.name}</strong>
                        <span class="badge badge-${d.tipo}">${d.tipo.toUpperCase()}</span>
                    </div>
                    <div class="tooltip-body">
                        <p><i class="bi bi-envelope"></i> ${d.email}</p>
                        <p><i class="bi bi-people"></i> ${d.referidos_count} referidos</p>
                        <p><i class="bi bi-layers"></i> Nivel ${d.level + 1}</p>
                        ${d.codigo_referido ? `<p><i class="bi bi-qr-code"></i> ${d.codigo_referido}</p>` : ''}
                    </div>
                `)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 28) + 'px');
            })
            .on('mouseout', () => {
                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            })
            .on('click', (event, d) => {
                this.onNodeClick(d);
            });
    }

    onNodeClick(node) {
        // Emit custom event for node click
        const event = new CustomEvent('nodeClick', {
            detail: { node: node }
        });
        document.dispatchEvent(event);

        // Highlight node and its connections
        this.highlightNode(node);
    }

    highlightNode(targetNode) {
        // Reset all nodes and links
        this.g.selectAll('.node')
            .style('opacity', 0.3)
            .attr('stroke-width', 2);

        this.g.selectAll('.link')
            .style('opacity', 0.1);

        this.g.selectAll('.label')
            .style('opacity', 0.3);

        // Highlight target node
        this.g.selectAll('.node')
            .filter(d => d.id === targetNode.id)
            .style('opacity', 1)
            .attr('stroke-width', 4);

        // Highlight connected nodes and links
        const connectedNodes = new Set();
        connectedNodes.add(targetNode.id);

        this.links.forEach(link => {
            if (link.source.id === targetNode.id || link.target.id === targetNode.id) {
                connectedNodes.add(link.source.id);
                connectedNodes.add(link.target.id);
            }
        });

        this.g.selectAll('.node')
            .filter(d => connectedNodes.has(d.id))
            .style('opacity', 1);

        this.g.selectAll('.link')
            .filter(d => d.source.id === targetNode.id || d.target.id === targetNode.id)
            .style('opacity', 0.8);

        this.g.selectAll('.label')
            .filter(d => connectedNodes.has(d.id))
            .style('opacity', 1);

        // Auto reset after 3 seconds
        setTimeout(() => {
            this.resetHighlight();
        }, 3000);
    }

    resetHighlight() {
        this.g.selectAll('.node')
            .style('opacity', 1)
            .attr('stroke-width', 2);

        this.g.selectAll('.link')
            .style('opacity', 0.6);

        this.g.selectAll('.label')
            .style('opacity', 1);
    }

    // Public methods for external control
    centerNetwork() {
        const zoom = d3.zoom().scaleExtent([0.1, 4]);
        this.svg.transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity);
    }

    focusNode(nodeId) {
        const node = this.nodes.find(n => n.id === nodeId);
        if (node) {
            const zoom = d3.zoom().scaleExtent([0.1, 4]);
            const scale = 1.5;
            const x = -node.x * scale + this.config.width / 2;
            const y = -node.y * scale + this.config.height / 2;

            this.svg.transition()
                .duration(750)
                .call(zoom.transform, d3.zoomIdentity.translate(x, y).scale(scale));

            this.highlightNode(node);
        }
    }

    updateData(apiUrl) {
        this.loadData(apiUrl);
    }

    destroy() {
        if (this.simulation) {
            this.simulation.stop();
        }
        this.container.selectAll('*').remove();
        d3.select('.mlm-tooltip').remove();
    }
}

// Export for use in other scripts
window.MLMNetworkVisualization = MLMNetworkVisualization;