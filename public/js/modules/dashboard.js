/**
 * Dashboard Module JavaScript
 * Funcionalidades específicas para el módulo de dashboard principal
 */

class DashboardModule {
    constructor(adminCore) {
        this.adminCore = adminCore;
        this.charts = {};
        this.refreshInterval = null;
        this.autoRefreshEnabled = true;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startAutoRefresh();
        console.log('Dashboard Module initialized');
    }

    setupEventListeners() {
        // Botón de actualizar dashboard
        document.getElementById('refreshDashboard')?.addEventListener('click', () => {
            this.refreshData();
        });

        // Toggle auto-refresh
        document.getElementById('toggleAutoRefresh')?.addEventListener('change', (e) => {
            this.autoRefreshEnabled = e.target.checked;
            if (this.autoRefreshEnabled) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        });
    }

    async loadData() {
        try {
            this.adminCore.showLoading();

            const response = await this.adminCore.apiCall('/api/admin/dashboard');
            const data = response.data;

            // Actualizar métricas
            this.updateMetrics(data.stats);

            // Actualizar gráfico de ventas
            this.updateSalesChart(data.ventasSemanales);

            // Actualizar top vendedores
            this.updateTopVendedores(data.topVendedores);

            // Actualizar pedidos recientes
            this.updateRecentOrders(data.pedidos_recientes);

            // Actualizar actividad reciente
            this.updateRecentActivity(data.actividad_reciente);

            // Actualizar última actualización
            this.updateLastUpdate();

            // Guardar en cache
            this.adminCore.setCacheData('dashboard', data);

        } catch (error) {
            console.error('Error loading dashboard:', error);
            this.adminCore.showError('Error al cargar el dashboard');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    updateMetrics(stats) {
        const metricsContainer = document.getElementById('dashboard-metrics');
        if (!metricsContainer) return;

        metricsContainer.innerHTML = `
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card metric-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon">
                            <i class="bi bi-people fs-2"></i>
                        </div>
                        <h3 class="metric-value">${this.adminCore.formatNumber(stats.total_usuarios)}</h3>
                        <p class="metric-label">Total Usuarios</p>
                        <div class="trend-indicator ${stats.usuarios_trend > 0 ? 'trend-up' : 'trend-down'}">
                            <i class="bi bi-arrow-${stats.usuarios_trend > 0 ? 'up' : 'down'}"></i>
                            ${Math.abs(stats.usuarios_trend)}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card metric-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon">
                            <i class="bi bi-person-badge fs-2"></i>
                        </div>
                        <h3 class="metric-value">${this.adminCore.formatNumber(stats.total_vendedores)}</h3>
                        <p class="metric-label">Vendedores Activos</p>
                        <div class="trend-indicator ${stats.vendedores_trend > 0 ? 'trend-up' : 'trend-down'}">
                            <i class="bi bi-arrow-${stats.vendedores_trend > 0 ? 'up' : 'down'}"></i>
                            ${Math.abs(stats.vendedores_trend)}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card metric-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon">
                            <i class="bi bi-cart-check fs-2"></i>
                        </div>
                        <h3 class="metric-value">${this.adminCore.formatNumber(stats.pedidos_hoy)}</h3>
                        <p class="metric-label">Pedidos Hoy</p>
                        <div class="trend-indicator ${stats.pedidos_trend > 0 ? 'trend-up' : 'trend-down'}">
                            <i class="bi bi-arrow-${stats.pedidos_trend > 0 ? 'up' : 'down'}"></i>
                            ${Math.abs(stats.pedidos_trend)}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card metric-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon">
                            <i class="bi bi-currency-dollar fs-2"></i>
                        </div>
                        <h3 class="metric-value">${this.adminCore.formatCurrency(stats.ventas_mes)}</h3>
                        <p class="metric-label">Ventas del Mes</p>
                        <div class="trend-indicator ${stats.ventas_trend > 0 ? 'trend-up' : 'trend-down'}">
                            <i class="bi bi-arrow-${stats.ventas_trend > 0 ? 'up' : 'down'}"></i>
                            ${Math.abs(stats.ventas_trend)}%
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    updateSalesChart(ventasData) {
        const ctx = document.getElementById('ventasChart');
        if (!ctx) return;

        // Destruir gráfico anterior si existe
        if (this.charts.ventas) {
            this.charts.ventas.destroy();
        }

        this.charts.ventas = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ventasData.map(d => this.adminCore.formatDate(d.fecha)),
                datasets: [{
                    label: 'Ventas Diarias',
                    data: ventasData.map(d => d.ventas),
                    borderColor: 'var(--primary-color)',
                    backgroundColor: 'rgba(114, 47, 55, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'var(--primary-color)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return `Ventas: ${this.adminCore.formatCurrency(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => this.adminCore.formatCurrency(value)
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    updateTopVendedores(vendedores) {
        const container = document.getElementById('top-vendedores');
        if (!container) return;

        if (!vendedores || vendedores.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay datos disponibles</p>';
            return;
        }

        container.innerHTML = vendedores.map((v, index) => `
            <div class="vendedor-item">
                <div class="vendedor-rank">${index + 1}</div>
                <div class="vendedor-info">
                    <div class="vendedor-name">${v.vendedor.name}</div>
                    <div class="vendedor-stats">${v.pedidos_mes} pedidos este mes</div>
                </div>
                <div class="vendedor-sales">${this.adminCore.formatCurrency(v.ventas_mes)}</div>
            </div>
        `).join('');
    }

    updateRecentOrders(pedidos) {
        const container = document.getElementById('pedidos-recientes');
        if (!container) return;

        if (!pedidos || pedidos.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay pedidos recientes</p>';
            return;
        }

        const tableHTML = `
            <div class="table-responsive">
                <table class="table table-hover pedidos-recientes-table">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pedidos.map(p => `
                            <tr>
                                <td><span class="pedido-numero">${p.numero_pedido}</span></td>
                                <td>${p.cliente.name}</td>
                                <td>${p.vendedor.name}</td>
                                <td><span class="pedido-total">${this.adminCore.formatCurrency(p.total_final)}</span></td>
                                <td><span class="badge bg-${this.adminCore.getStatusColor(p.estado)}">${p.estado}</span></td>
                                <td>${this.adminCore.formatDateTime(p.created_at)}</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewPedido('${p._id}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = tableHTML;
    }

    updateRecentActivity(actividad) {
        const container = document.getElementById('actividad-reciente');
        if (!container) return;

        if (!actividad || actividad.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay actividad reciente</p>';
            return;
        }

        container.innerHTML = actividad.map(a => `
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="bi bi-${this.getActivityIcon(a.tipo)}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${a.titulo}</div>
                    <div class="activity-description">${a.descripcion}</div>
                    <div class="activity-time">${this.adminCore.formatDateTime(a.created_at)}</div>
                </div>
            </div>
        `).join('');
    }

    getActivityIcon(tipo) {
        const icons = {
            'nuevo_usuario': 'person-plus',
            'nuevo_pedido': 'cart-plus',
            'pedido_entregado': 'check-circle',
            'pago_comision': 'currency-dollar',
            'nuevo_producto': 'box',
            'configuracion': 'gear'
        };
        return icons[tipo] || 'info-circle';
    }

    updateLastUpdate() {
        const element = document.getElementById('last-update');
        if (element) {
            element.textContent = new Date().toLocaleString('es-CO');
        }
    }

    async refreshData() {
        // Limpiar cache y recargar datos
        this.adminCore.clearCache('dashboard');
        await this.loadData();
        this.adminCore.showSuccess('Dashboard actualizado correctamente');
    }

    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        // Actualizar cada 5 minutos si está habilitado
        this.refreshInterval = setInterval(() => {
            if (this.autoRefreshEnabled && this.adminCore.currentModule === 'dashboard') {
                this.refreshData();
            }
        }, 300000); // 5 minutos
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    destroy() {
        // Limpiar recursos al destruir el módulo
        this.stopAutoRefresh();

        // Destruir gráficos
        Object.values(this.charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });

        this.charts = {};
    }
}

// Funciones globales para compatibilidad
window.refreshDashboard = function() {
    if (window.adminSPA && window.adminSPA.modules.dashboard) {
        window.adminSPA.modules.dashboard.refreshData();
    }
};

window.viewPedido = function(pedidoId) {
    // Cambiar a módulo de pedidos y mostrar detalles
    if (window.adminSPA) {
        window.adminSPA.loadModule('pedidos');
        // TODO: Implementar mostrar detalles específicos del pedido
    }
};

// Exportar módulo
window.DashboardModule = DashboardModule;