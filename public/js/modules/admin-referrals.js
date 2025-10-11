/**
 * Admin Referrals Module
 * Gestión de red de referidos MLM
 */

class AdminReferrals {
    constructor(core) {
        this.core = core;
        this.data = null;
        this.networkData = null;
        this.selectedNode = null;

        this.init();
    }

    init() {
        console.log('Referrals module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Botones de vista
        document.getElementById('btn-vista-arbol')?.addEventListener('click', () => {
            this.showTreeView();
        });

        document.getElementById('btn-vista-lista')?.addEventListener('click', () => {
            this.showListView();
        });

        document.getElementById('btn-vista-estadisticas')?.addEventListener('click', () => {
            this.showStatsView();
        });

        // Filtros
        document.getElementById('referido-nivel-filter')?.addEventListener('change', (e) => {
            this.filterByLevel(e.target.value);
        });

        document.getElementById('referido-estado-filter')?.addEventListener('change', (e) => {
            this.filterByStatus(e.target.value);
        });

        document.getElementById('referido-search')?.addEventListener('input',
            this.core.debounce((e) => {
                this.searchReferrals(e.target.value);
            }, 300)
        );

        // Exportar
        document.getElementById('btn-exportar-red')?.addEventListener('click', () => {
            this.exportNetwork();
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/referrals');

            if (response.success) {
                this.data = response.data;
                this.networkData = response.data.red;
                this.renderStats(response.data.stats);
                this.renderNetworkTree(response.data.red);
                this.core.setCacheData('referrals', response.data);
            } else {
                throw new Error(response.message || 'Error al cargar red de referidos');
            }

        } catch (error) {
            console.error('Error loading referrals:', error);
            this.core.showError('Error al cargar la red de referidos: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    renderStats(stats) {
        // Actualizar estadísticas principales
        const elements = {
            'total-referidos': stats.total_referidos || 0,
            'referidos-activos': stats.referidos_activos || 0,
            'referidos-mes': stats.referidos_mes || 0,
            'niveles-red': this.calculateNetworkLevels(),
            'top-referidor': this.getTopReferrer()
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = typeof value === 'number' ? this.core.formatNumber(value) : value;
            }
        });

        // Crear gráficos
        this.renderReferralsChart(stats);
        this.renderLevelsChart();
    }

    renderNetworkTree(networkData) {
        const container = document.getElementById('referidos-tree');
        if (!container) return;

        container.innerHTML = '';

        // Crear estructura del árbol
        const treeContainer = document.createElement('div');
        treeContainer.className = 'tree-container';
        treeContainer.style.cssText = `
            width: 100%;
            height: 600px;
            overflow: auto;
            position: relative;
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 2rem;
        `;

        if (!networkData || networkData.length === 0) {
            treeContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-diagram-3 fs-1 d-block mb-3"></i>
                    <h5>No hay red de referidos</h5>
                    <p>Aún no se han registrado referidos en el sistema</p>
                </div>
            `;
            container.appendChild(treeContainer);
            return;
        }

        // Renderizar nodos principales
        networkData.forEach((node, index) => {
            const nodeElement = this.createNodeElement(node, 0, index);
            treeContainer.appendChild(nodeElement);
        });

        container.appendChild(treeContainer);

        // Inicializar interactividad
        this.initializeTreeInteraction();
    }

    createNodeElement(node, level, index) {
        const nodeDiv = document.createElement('div');
        nodeDiv.className = 'referral-node';
        nodeDiv.style.cssText = `
            display: inline-block;
            background: ${this.getNodeColor(node.rol)};
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 0.5rem;
            text-align: center;
            min-width: 150px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            margin-left: ${level * 50}px;
        `;

        nodeDiv.innerHTML = `
            <div class="node-header">
                <strong>${node.name}</strong>
                <div class="node-role">
                    <small class="badge bg-light text-dark">${this.formatRole(node.rol)}</small>
                </div>
            </div>
            <div class="node-stats mt-2">
                <small>Referidos: ${node.referidos || 0}</small>
                ${node.ventas_mes ? `<br><small>Ventas: ${this.core.formatCurrency(node.ventas_mes)}</small>` : ''}
            </div>
            ${node.activo ? '<i class="bi bi-check-circle position-absolute top-0 end-0 text-success"></i>' :
                            '<i class="bi bi-x-circle position-absolute top-0 end-0 text-danger"></i>'}
        `;

        // Eventos del nodo
        nodeDiv.addEventListener('click', () => {
            this.selectNode(node, nodeDiv);
        });

        nodeDiv.addEventListener('mouseenter', () => {
            nodeDiv.style.transform = 'scale(1.05)';
            nodeDiv.style.boxShadow = '0 0.5rem 1rem rgba(0,0,0,0.15)';
        });

        nodeDiv.addEventListener('mouseleave', () => {
            if (!nodeDiv.classList.contains('selected')) {
                nodeDiv.style.transform = 'scale(1)';
                nodeDiv.style.boxShadow = 'none';
            }
        });

        return nodeDiv;
    }

    selectNode(node, element) {
        // Remover selección anterior
        document.querySelectorAll('.referral-node.selected').forEach(el => {
            el.classList.remove('selected');
            el.style.borderColor = 'transparent';
            el.style.transform = 'scale(1)';
        });

        // Seleccionar nodo actual
        element.classList.add('selected');
        element.style.borderColor = '#ffc107';
        element.style.transform = 'scale(1.05)';

        this.selectedNode = node;
        this.showNodeDetails(node);
    }

    showNodeDetails(node) {
        const detailsContainer = document.getElementById('node-details');
        if (!detailsContainer) return;

        detailsContainer.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Detalles de ${node.name}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Rol:</strong> ${this.formatRole(node.rol)}</p>
                            <p><strong>Estado:</strong>
                                <span class="badge bg-${node.activo ? 'success' : 'danger'}">
                                    ${node.activo ? 'Activo' : 'Inactivo'}
                                </span>
                            </p>
                            <p><strong>Referidos Directos:</strong> ${node.referidos || 0}</p>
                            <p><strong>Fecha de Registro:</strong> ${this.core.formatDate(node.created_at)}</p>
                        </div>
                        <div class="col-md-6">
                            ${node.ventas_mes ? `<p><strong>Ventas del Mes:</strong> ${this.core.formatCurrency(node.ventas_mes)}</p>` : ''}
                            ${node.comisiones_mes ? `<p><strong>Comisiones del Mes:</strong> ${this.core.formatCurrency(node.comisiones_mes)}</p>` : ''}
                            <p><strong>Nivel en la Red:</strong> ${this.calculateNodeLevel(node)}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm me-2" onclick="adminCore.modules.referrals.viewUserProfile('${node.id}')">
                            <i class="bi bi-eye me-1"></i>Ver Perfil
                        </button>
                        <button class="btn btn-outline-info btn-sm me-2" onclick="adminCore.modules.referrals.viewReferralHistory('${node.id}')">
                            <i class="bi bi-clock-history me-1"></i>Historial
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="adminCore.modules.referrals.generateReport('${node.id}')">
                            <i class="bi bi-file-earmark-text me-1"></i>Reporte
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    showTreeView() {
        document.getElementById('vista-arbol')?.classList.remove('d-none');
        document.getElementById('vista-lista')?.classList.add('d-none');
        document.getElementById('vista-estadisticas')?.classList.add('d-none');

        // Actualizar botones
        this.updateViewButtons('arbol');
    }

    showListView() {
        document.getElementById('vista-arbol')?.classList.add('d-none');
        document.getElementById('vista-lista')?.classList.remove('d-none');
        document.getElementById('vista-estadisticas')?.classList.add('d-none');

        this.renderReferralsList();
        this.updateViewButtons('lista');
    }

    showStatsView() {
        document.getElementById('vista-arbol')?.classList.add('d-none');
        document.getElementById('vista-lista')?.classList.add('d-none');
        document.getElementById('vista-estadisticas')?.classList.remove('d-none');

        this.renderDetailedStats();
        this.updateViewButtons('estadisticas');
    }

    updateViewButtons(activeView) {
        document.querySelectorAll('.btn-vista').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(`btn-vista-${activeView}`)?.classList.add('active');
    }

    renderReferralsList() {
        const container = document.getElementById('referrals-list-container');
        if (!container || !this.networkData) return;

        const flatList = this.flattenNetworkData(this.networkData);

        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Referido por</th>
                            <th>Referidos Directos</th>
                            <th>Nivel</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${flatList.map(user => `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-${this.getNodeColor(user.rol)} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 35px; height: 35px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium">${user.name}</div>
                                            <small class="text-muted">${user.email || ''}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-${this.getRoleColorClass(user.rol)}">${this.formatRole(user.rol)}</span></td>
                                <td>${user.referido_por || '-'}</td>
                                <td>${user.referidos || 0}</td>
                                <td>Nivel ${this.calculateNodeLevel(user)}</td>
                                <td><span class="badge bg-${user.activo ? 'success' : 'secondary'}">${user.activo ? 'Activo' : 'Inactivo'}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="adminCore.modules.referrals.viewUserProfile('${user.id}')" title="Ver perfil">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="adminCore.modules.referrals.viewReferralHistory('${user.id}')" title="Historial">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    renderDetailedStats() {
        const container = document.getElementById('detailed-stats-container');
        if (!container || !this.data) return;

        const stats = this.calculateDetailedStats();

        container.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-people fs-1 text-primary mb-3"></i>
                            <h3 class="text-primary">${stats.totalUsers}</h3>
                            <p class="text-muted">Total Usuarios en Red</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-diagram-3 fs-1 text-success mb-3"></i>
                            <h3 class="text-success">${stats.maxLevel}</h3>
                            <p class="text-muted">Niveles de Profundidad</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-trophy fs-1 text-warning mb-3"></i>
                            <h3 class="text-warning">${stats.topReferrer}</h3>
                            <p class="text-muted">Mayor Referidor</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Distribución por Roles</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="rolesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Crecimiento Mensual</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Renderizar gráficos
        this.renderRolesChart(stats.roleDistribution);
        this.renderGrowthChart(stats.monthlyGrowth);
    }

    renderReferralsChart(stats) {
        const ctx = document.getElementById('referralsOverviewChart');
        if (!ctx) return;

        if (this.core.charts.referralsOverview) {
            this.core.charts.referralsOverview.destroy();
        }

        this.core.charts.referralsOverview = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total', 'Activos', 'Este Mes'],
                datasets: [{
                    label: 'Referidos',
                    data: [
                        stats.total_referidos || 0,
                        stats.referidos_activos || 0,
                        stats.referidos_mes || 0
                    ],
                    backgroundColor: [
                        '#722F37',
                        '#198754',
                        '#0dcaf0'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Métodos auxiliares
    getNodeColor(rol) {
        const colors = {
            'administrador': '#dc3545',
            'lider': '#ffc107',
            'vendedor': '#0d6efd',
            'cliente': '#6c757d'
        };
        return colors[rol] || '#6c757d';
    }

    getRoleColorClass(rol) {
        const colors = {
            'administrador': 'danger',
            'lider': 'warning',
            'vendedor': 'primary',
            'cliente': 'secondary'
        };
        return colors[rol] || 'secondary';
    }

    formatRole(rol) {
        const roles = {
            'administrador': 'Administrador',
            'lider': 'Líder',
            'vendedor': 'Vendedor',
            'cliente': 'Cliente'
        };
        return roles[rol] || rol;
    }

    calculateNetworkLevels() {
        if (!this.networkData) return 0;
        // Implementar cálculo de niveles
        return 5; // Placeholder
    }

    getTopReferrer() {
        if (!this.networkData) return 'N/A';
        // Implementar búsqueda del top referidor
        return 'Juan Pérez'; // Placeholder
    }

    calculateNodeLevel(node) {
        // Implementar cálculo del nivel del nodo
        return 1; // Placeholder
    }

    flattenNetworkData(data, level = 1) {
        let result = [];

        data.forEach(item => {
            result.push({...item, level});
            if (item.children && item.children.length > 0) {
                result = result.concat(this.flattenNetworkData(item.children, level + 1));
            }
        });

        return result;
    }

    calculateDetailedStats() {
        // Implementar cálculos estadísticos detallados
        return {
            totalUsers: this.networkData?.length || 0,
            maxLevel: 5,
            topReferrer: 'Juan Pérez',
            roleDistribution: {
                administrador: 1,
                lider: 5,
                vendedor: 25,
                cliente: 100
            },
            monthlyGrowth: [10, 15, 20, 25, 30, 35]
        };
    }

    initializeTreeInteraction() {
        // Implementar interacciones del árbol (zoom, pan, etc.)
        console.log('Tree interaction initialized');
    }

    async exportNetwork() {
        try {
            const response = await this.core.apiCall('/api/admin/referrals/export');

            if (response.success) {
                // Implementar descarga del archivo
                this.core.showSuccess('Red exportada correctamente');
            }
        } catch (error) {
            this.core.showError('Error al exportar la red: ' + error.message);
        }
    }

    async viewUserProfile(userId) {
        this.core.showSuccess(`Ver perfil de usuario ${userId} - Próximamente`);
    }

    async viewReferralHistory(userId) {
        this.core.showSuccess(`Ver historial de referidos ${userId} - Próximamente`);
    }

    async generateReport(userId) {
        this.core.showSuccess(`Generar reporte de ${userId} - Próximamente`);
    }
}

// Exportar para uso global
window.AdminReferrals = AdminReferrals;