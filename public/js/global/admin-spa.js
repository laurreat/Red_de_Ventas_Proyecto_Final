/**
 * Admin SPA Router y Manager
 * Sistema de navegación sin recarga para el dashboard de administrador
 */

class AdminSPA {
    constructor() {
        this.currentModule = 'dashboard';
        this.isLoading = false;
        this.cache = new Map();
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.charts = {};

        this.init();
    }

    init() {
        // Configurar headers AJAX
        if (this.csrfToken) {
            fetch.defaults = {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };
        }

        // Cargar módulo dashboard por defecto
        this.loadModuleData('dashboard');

        // Configurar event listeners
        this.setupEventListeners();

        // Configurar navegación del browser
        this.setupBrowserNavigation();

        console.log('Admin SPA initialized');
    }

    setupEventListeners() {
        // Prevenir navegación por defecto en todos los enlaces del sidebar
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const module = link.getAttribute('data-module');
                if (module) {
                    this.loadModule(module);
                }
            });
        });

        // Prevenir navegación en enlaces del dropdown de perfil
        document.querySelectorAll('.dropdown-item[onclick*="loadModule"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
            });
        });

        // Eventos de filtros y búsquedas
        document.getElementById('user-search')?.addEventListener('input',
            this.debounce(() => this.filterUsers(), 300));

        document.getElementById('user-role-filter')?.addEventListener('change',
            () => this.filterUsers());

        document.getElementById('user-status-filter')?.addEventListener('change',
            () => this.filterUsers());

        // Eventos de reportes
        document.getElementById('reporte-periodo')?.addEventListener('change',
            (e) => this.toggleCustomDateRange(e.target.value));
    }

    setupBrowserNavigation() {
        // Manejar navegación del browser (back/forward)
        window.addEventListener('popstate', (event) => {
            if (event.state && event.state.module) {
                this.loadModule(event.state.module, false);
            }
        });

        // Establecer estado inicial
        history.replaceState({ module: 'dashboard' }, '', '#dashboard');
    }

    loadModule(moduleName, pushState = true) {
        if (this.isLoading || moduleName === this.currentModule) return;

        console.log(`Loading module: ${moduleName}`);

        // Mostrar loading si es necesario
        this.showLoading();

        // Ocultar módulo actual
        this.hideCurrentModule();

        // Mostrar nuevo módulo
        this.showModule(moduleName);

        // Actualizar navegación
        this.updateNavigation(moduleName);

        // Cargar datos del módulo
        this.loadModuleData(moduleName);

        // Actualizar browser history
        if (pushState) {
            history.pushState({ module: moduleName }, '', `#${moduleName}`);
        }

        this.currentModule = moduleName;
        this.hideLoading();
    }

    hideCurrentModule() {
        const currentElement = document.getElementById(`module-${this.currentModule}`);
        if (currentElement) {
            currentElement.classList.remove('active');
        }
    }

    showModule(moduleName) {
        const moduleElement = document.getElementById(`module-${moduleName}`);
        if (moduleElement) {
            moduleElement.classList.add('active');
        }
    }

    updateNavigation(moduleName) {
        // Remover clase active de todos los nav-links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Activar el nav-link correspondiente
        const activeLink = document.querySelector(`[data-module="${moduleName}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }

    async loadModuleData(moduleName) {
        try {
            // Verificar cache
            if (this.cache.has(moduleName)) {
                const cachedData = this.cache.get(moduleName);
                this.renderModuleData(moduleName, cachedData);
                return;
            }

            // Cargar datos según el módulo
            switch (moduleName) {
                case 'dashboard':
                    await this.loadDashboardData();
                    break;
                case 'usuarios':
                    await this.loadUsersData();
                    break;
                case 'productos':
                    await this.loadProductsData();
                    break;
                case 'pedidos':
                    await this.loadOrdersData();
                    break;
                case 'reportes':
                    await this.loadReportsData();
                    break;
                case 'comisiones':
                    await this.loadCommissionsData();
                    break;
                case 'referidos':
                    await this.loadReferralsData();
                    break;
                case 'configuracion':
                    await this.loadConfigData();
                    break;
                case 'respaldos':
                    await this.loadBackupsData();
                    break;
                case 'logs':
                    await this.loadLogsData();
                    break;
                case 'perfil':
                    await this.loadProfileData();
                    break;
                case 'configuracion-perfil':
                    await this.loadProfileConfigData();
                    break;
                default:
                    console.warn(`No data loader for module: ${moduleName}`);
            }
        } catch (error) {
            console.error(`Error loading module ${moduleName}:`, error);
            this.showError(`Error cargando ${moduleName}: ${error.message}`);
        }
    }

    async loadDashboardData() {
        try {
            const response = await this.apiCall('/api/admin/dashboard');
            const data = response.data;

            // Actualizar métricas
            this.updateDashboardMetrics(data.stats);

            // Actualizar gráfico de ventas
            this.updateSalesChart(data.ventasSemanales);

            // Actualizar top vendedores
            this.updateTopVendedores(data.topVendedores);

            // Actualizar pedidos recientes
            this.updateRecentOrders(data.pedidos_recientes);

            // Actualizar última actualización
            document.getElementById('last-update').textContent = new Date().toLocaleString();

            // Guardar en cache
            this.cache.set('dashboard', data);

        } catch (error) {
            console.error('Error loading dashboard:', error);
        }
    }

    updateDashboardMetrics(stats) {
        const metricsContainer = document.getElementById('dashboard-metrics');

        metricsContainer.innerHTML = `
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                            <i class="bi bi-people fs-2" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${this.formatNumber(stats.total_usuarios)}</h3>
                        <p class="text-muted mb-0 small">Total Usuarios</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                            <i class="bi bi-person-badge fs-2" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${this.formatNumber(stats.total_vendedores)}</h3>
                        <p class="text-muted mb-0 small">Vendedores Activos</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                            <i class="bi bi-cart-check fs-2" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--primary-color);">${this.formatNumber(stats.pedidos_hoy)}</h3>
                        <p class="text-muted mb-0 small">Pedidos Hoy</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width: 60px; height: 60px; background-color: rgba(114, 47, 55, 0.1);">
                            <i class="bi bi-currency-dollar fs-2" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--primary-color);">$${this.formatNumber(stats.ventas_mes)}</h3>
                        <p class="text-muted mb-0 small">Ventas del Mes</p>
                    </div>
                </div>
            </div>
        `;
    }

    updateSalesChart(ventasData) {
        const ctx = document.getElementById('ventasChart').getContext('2d');

        // Destruir gráfico anterior si existe
        if (this.charts.ventas) {
            this.charts.ventas.destroy();
        }

        this.charts.ventas = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ventasData.map(d => d.fecha),
                datasets: [{
                    label: 'Ventas',
                    data: ventasData.map(d => d.ventas),
                    borderColor: '#722F37',
                    backgroundColor: 'rgba(114, 47, 55, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    updateTopVendedores(vendedores) {
        const container = document.getElementById('top-vendedores');

        if (!vendedores || vendedores.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay datos disponibles</p>';
            return;
        }

        container.innerHTML = vendedores.map((v, index) => `
            <div class="d-flex align-items-center mb-3 ${index < vendedores.length - 1 ? 'border-bottom pb-3' : ''}">
                <div class="me-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px;">
                        <span class="fw-bold">${index + 1}</span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-medium">${v.vendedor.name}</div>
                    <small class="text-muted">${v.pedidos_mes} pedidos</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-success">$${this.formatNumber(v.ventas_mes)}</div>
                </div>
            </div>
        `).join('');
    }

    updateRecentOrders(pedidos) {
        const container = document.getElementById('pedidos-recientes');

        if (!pedidos || pedidos.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay pedidos recientes</p>';
            return;
        }

        const tableHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pedidos.map(p => `
                            <tr>
                                <td><strong>${p.numero_pedido}</strong></td>
                                <td>${p.cliente.name}</td>
                                <td>${p.vendedor.name}</td>
                                <td class="fw-bold text-success">$${this.formatNumber(p.total_final)}</td>
                                <td><span class="badge bg-${this.getStatusColor(p.estado)}">${p.estado}</span></td>
                                <td>${new Date(p.created_at).toLocaleDateString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = tableHTML;
    }

    async loadUsersData() {
        try {
            const response = await this.apiCall('/api/admin/users');
            this.renderUsersTable(response.data);
            this.cache.set('usuarios', response.data);
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    renderUsersTable(users) {
        const tbody = document.getElementById('users-table-body');

        tbody.innerHTML = users.map(user => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 35px; height: 35px;">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">${user.name}</div>
                            ${user.apellidos ? `<small class="text-muted">${user.apellidos}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td><span class="badge bg-${this.getRoleColor(user.rol)}">${user.rol}</span></td>
                <td><span class="badge ${user.activo ? 'bg-success' : 'bg-secondary'}">${user.activo ? 'Activo' : 'Inactivo'}</span></td>
                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewUser('${user._id}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="editUser('${user._id}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${user.rol !== 'administrador' ? `
                            <button class="btn btn-outline-danger" onclick="deleteUser('${user._id}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    async loadProductsData() {
        try {
            const response = await this.apiCall('/api/admin/products');
            this.renderProductsGrid(response.data);
            this.cache.set('productos', response.data);
        } catch (error) {
            console.error('Error loading products:', error);
        }
    }

    renderProductsGrid(products) {
        const container = document.getElementById('productos-grid');

        container.innerHTML = products.map(product => `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="card-title">${product.nombre}</h6>
                            <span class="badge ${product.activo ? 'bg-success' : 'bg-secondary'}">
                                ${product.activo ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                        <p class="card-text text-muted">${product.descripcion || 'Sin descripción'}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-success">$${this.formatNumber(product.precio)}</span>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editProduct('${product._id}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteProduct('${product._id}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async apiCall(url, options = {}) {
        const defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };
        const response = await fetch(url, mergedOptions);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    showLoading() {
        this.isLoading = true;
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.remove('d-none');
        }
    }

    hideLoading() {
        this.isLoading = false;
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.add('d-none');
        }
    }

    showError(message) {
        console.error(message);
        // Aquí puedes implementar un toast o modal de error
        alert(message); // Temporal
    }

    formatNumber(number) {
        return new Intl.NumberFormat('es-CO').format(number);
    }

    getStatusColor(status) {
        const colors = {
            'pendiente': 'warning',
            'confirmado': 'info',
            'en_preparacion': 'primary',
            'listo': 'success',
            'en_camino': 'info',
            'entregado': 'success',
            'cancelado': 'danger'
        };
        return colors[status] || 'secondary';
    }

    getRoleColor(role) {
        const colors = {
            'administrador': 'danger',
            'lider': 'warning',
            'vendedor': 'primary',
            'cliente': 'info'
        };
        return colors[role] || 'secondary';
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Métodos específicos del módulo
    async refreshDashboard() {
        this.cache.delete('dashboard');
        await this.loadDashboardData();
    }

    async filterUsers() {
        const search = document.getElementById('user-search').value;
        const role = document.getElementById('user-role-filter').value;
        const status = document.getElementById('user-status-filter').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (role) params.append('role', role);
        if (status) params.append('status', status);

        try {
            const response = await this.apiCall(`/api/admin/users?${params.toString()}`);
            this.renderUsersTable(response.data);
        } catch (error) {
            console.error('Error filtering users:', error);
        }
    }

    toggleCustomDateRange(periodo) {
        const customFields = document.getElementById('fecha-personalizada');
        const customFieldsHasta = document.getElementById('fecha-personalizada-hasta');

        if (periodo === 'personalizado') {
            customFields.style.display = 'block';
            customFieldsHasta.style.display = 'block';
        } else {
            customFields.style.display = 'none';
            customFieldsHasta.style.display = 'none';
        }
    }
}

// Instanciar el SPA cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.adminSPA = new AdminSPA();
});

// Funciones globales para la navegación
function loadModule(moduleName) {
    if (window.adminSPA) {
        window.adminSPA.loadModule(moduleName);
    }
}

function refreshDashboard() {
    if (window.adminSPA) {
        window.adminSPA.refreshDashboard();
    }
}

function filterUsers() {
    if (window.adminSPA) {
        window.adminSPA.filterUsers();
    }
}

// Funciones placeholder para las acciones
function openUserModal(action, userId = null) {
    console.log(`Open user modal: ${action}`, userId);
    alert('Modal de usuario próximamente');
}

function viewUser(userId) {
    console.log('View user:', userId);
    alert('Ver usuario próximamente');
}

function editUser(userId) {
    console.log('Edit user:', userId);
    alert('Editar usuario próximamente');
}

function deleteUser(userId) {
    console.log('Delete user:', userId);
    if (confirm('¿Está seguro de eliminar este usuario?')) {
        alert('Eliminar usuario próximamente');
    }
}

function editProduct(productId) {
    console.log('Edit product:', productId);
    alert('Editar producto próximamente');
}

function deleteProduct(productId) {
    console.log('Delete product:', productId);
    if (confirm('¿Está seguro de eliminar este producto?')) {
        alert('Eliminar producto próximamente');
    }
}

// Funciones para cargar datos de módulos adicionales
async function loadOrdersData() {
    try {
        const response = await this.apiCall('/api/admin/orders');
        this.renderOrdersTable(response.data);
        this.cache.set('pedidos', response.data);
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

async function loadCommissionsData() {
    try {
        const response = await this.apiCall('/api/admin/commissions');
        this.renderCommissionsTable(response.data);
        this.cache.set('comisiones', response.data);
    } catch (error) {
        console.error('Error loading commissions:', error);
    }
}

async function loadReferralsData() {
    try {
        const response = await this.apiCall('/api/admin/referrals');
        this.renderReferralsNetwork(response.data);
        this.cache.set('referidos', response.data);
    } catch (error) {
        console.error('Error loading referrals:', error);
    }
}

async function loadReportsData() {
    try {
        const response = await this.apiCall('/api/admin/reports');
        this.renderReportsData(response.data);
        this.cache.set('reportes', response.data);
    } catch (error) {
        console.error('Error loading reports:', error);
    }
}

async function loadConfigData() {
    try {
        const response = await this.apiCall('/api/admin/config');
        this.renderConfigOptions(response.data);
        this.cache.set('configuracion', response.data);
    } catch (error) {
        console.error('Error loading config:', error);
    }
}

async function loadBackupsData() {
    try {
        const response = await this.apiCall('/api/admin/backups');
        this.renderBackupsList(response.data);
        this.cache.set('respaldos', response.data);
    } catch (error) {
        console.error('Error loading backups:', error);
    }
}

async function loadLogsData() {
    try {
        const response = await this.apiCall('/api/admin/logs');
        this.renderLogsList(response.data);
        this.cache.set('logs', response.data);
    } catch (error) {
        console.error('Error loading logs:', error);
    }
}

async function loadProfileData() {
    try {
        const response = await this.apiCall('/api/admin/profile');
        this.renderProfileData(response.data);
        this.cache.set('perfil', response.data);
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

async function loadProfileConfigData() {
    try {
        const response = await this.apiCall('/api/admin/profile/config');
        this.renderProfileConfigData(response.data);
        this.cache.set('configuracion-perfil', response.data);
    } catch (error) {
        console.error('Error loading profile config:', error);
    }
}