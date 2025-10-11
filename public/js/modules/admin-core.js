/**
 * Admin Core Module
 * Funcionalidades principales del dashboard SPA
 */

class AdminCore {
    constructor() {
        this.currentModule = 'dashboard';
        this.isLoading = false;
        this.cache = new Map();
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.charts = {};
        this.modules = {};

        this.init();
    }

    init() {
        // Configurar headers AJAX
        if (this.csrfToken) {
            this.setupAjaxDefaults();
        }

        // Cargar módulo dashboard por defecto
        this.loadModuleData('dashboard');

        // Configurar event listeners
        this.setupEventListeners();

        // Configurar navegación del browser
        this.setupBrowserNavigation();

        console.log('Admin Core initialized');
    }

    setupAjaxDefaults() {
        // Configurar headers por defecto para fetch
        window.fetch = ((originalFetch) => {
            return (...args) => {
                if (args[1]) {
                    args[1].headers = {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...args[1].headers
                    };
                } else {
                    args[1] = {
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    };
                }
                return originalFetch.apply(this, args);
            };
        })(window.fetch);
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

        // Toggle sidebar en móviles
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            this.toggleSidebar();
        });

        // Cerrar sidebar al hacer click fuera en móviles
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('adminSidebar');
                const toggle = document.getElementById('sidebarToggle');

                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
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
                    if (this.modules.dashboard) {
                        await this.modules.dashboard.loadData();
                    }
                    break;
                case 'usuarios':
                    if (this.modules.users) {
                        await this.modules.users.loadData();
                    }
                    break;
                case 'productos':
                    if (this.modules.products) {
                        await this.modules.products.loadData();
                    }
                    break;
                case 'pedidos':
                    if (this.modules.orders) {
                        await this.modules.orders.loadData();
                    }
                    break;
                case 'comisiones':
                    if (this.modules.commissions) {
                        await this.modules.commissions.loadData();
                    }
                    break;
                case 'referidos':
                    if (this.modules.referrals) {
                        await this.modules.referrals.loadData();
                    }
                    break;
                case 'reportes':
                    if (this.modules.reports) {
                        await this.modules.reports.loadData();
                    }
                    break;
                case 'configuracion':
                    if (this.modules.config) {
                        await this.modules.config.loadData();
                    }
                    break;
                case 'respaldos':
                    if (this.modules.backups) {
                        await this.modules.backups.loadData();
                    }
                    break;
                case 'logs':
                    if (this.modules.logs) {
                        await this.modules.logs.loadData();
                    }
                    break;
                case 'perfil':
                    if (this.modules.profile) {
                        await this.modules.profile.loadData();
                    }
                    break;
                default:
                    console.warn(`No module handler for: ${moduleName}`);
            }
        } catch (error) {
            console.error(`Error loading module ${moduleName}:`, error);
            this.showError(`Error cargando ${moduleName}: ${error.message}`);
        }
    }

    renderModuleData(moduleName, data) {
        // Esta función será sobrescrita por cada módulo específico
        console.log(`Rendering data for ${moduleName}:`, data);
    }

    registerModule(name, moduleInstance) {
        this.modules[name] = moduleInstance;
        console.log(`Module registered: ${name}`);
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

        try {
            const response = await fetch(url, mergedOptions);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Call Error:', error);
            throw error;
        }
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

        // Crear toast de error
        this.showToast(message, 'error');
    }

    showSuccess(message) {
        console.log(message);

        // Crear toast de éxito
        this.showToast(message, 'success');
    }

    showToast(message, type = 'info') {
        // Crear contenedor de toasts si no existe
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '11000';
            document.body.appendChild(toastContainer);
        }

        // Crear toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Mostrar toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remover toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const header = document.querySelector('.admin-header');
        const main = document.querySelector('.admin-main');

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('expanded');
            main.classList.toggle('expanded');
        }
    }

    formatNumber(number) {
        return new Intl.NumberFormat('es-CO').format(number);
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(amount);
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    formatDateTime(date) {
        return new Date(date).toLocaleString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    getStatusColor(status) {
        const colors = {
            'pendiente': 'warning',
            'confirmado': 'info',
            'en_preparacion': 'primary',
            'listo': 'success',
            'en_camino': 'info',
            'entregado': 'success',
            'cancelado': 'danger',
            'activo': 'success',
            'inactivo': 'secondary'
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

    // Métodos de utilidad para el cache
    setCacheData(key, data, ttl = 300000) { // TTL por defecto: 5 minutos
        const item = {
            data: data,
            timestamp: Date.now(),
            ttl: ttl
        };
        this.cache.set(key, item);
    }

    getCacheData(key) {
        const item = this.cache.get(key);
        if (!item) return null;

        const now = Date.now();
        if (now - item.timestamp > item.ttl) {
            this.cache.delete(key);
            return null;
        }

        return item.data;
    }

    clearCache(key = null) {
        if (key) {
            this.cache.delete(key);
        } else {
            this.cache.clear();
        }
    }
}

// Exportar para uso global
window.AdminCore = AdminCore;