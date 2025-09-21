/**
 * Admin SPA Main Application
 * Inicializador principal del dashboard SPA con pre-carga de módulos
 */

class AdminSPA {
    constructor() {
        this.adminCore = null;
        this.modules = {};
        this.preloadedModules = new Set();
        this.isInitialized = false;

        this.init();
    }

    async init() {
        try {
            console.log('Initializing Admin SPA...');

            // Verificar dependencias
            if (typeof AdminCore === 'undefined') {
                console.error('AdminCore not loaded');
                return;
            }

            // Inicializar núcleo
            this.adminCore = new AdminCore();
            window.adminCore = this.adminCore; // Hacer disponible globalmente

            // Pre-cargar todos los módulos
            await this.preloadModules();

            // Registrar módulos en AdminCore
            this.registerModules();

            // Configurar manejo de errores global
            this.setupGlobalErrorHandling();

            // Configurar eventos de visibilidad de página
            this.setupPageVisibilityHandling();

            this.isInitialized = true;
            console.log('Admin SPA initialized successfully');

        } catch (error) {
            console.error('Error initializing Admin SPA:', error);
            this.showError('Error al inicializar el dashboard');
        }
    }

    async preloadModules() {
        console.log('Pre-loading modules...');

        // Lista de módulos a pre-cargar
        const modulesToPreload = [
            { name: 'dashboard', class: 'DashboardModule', required: true },
            { name: 'users', class: 'UsuariosModule', required: true },
            { name: 'products', class: 'ProductosModule', required: true },
            { name: 'orders', class: 'PedidosModule', required: true },
            { name: 'commissions', class: 'AdminCommissions', required: false },
            { name: 'referrals', class: 'AdminReferrals', required: false },
            { name: 'config', class: 'AdminConfig', required: false },
            { name: 'backups', class: 'AdminBackups', required: false },
            { name: 'logs', class: 'AdminLogs', required: false },
            { name: 'profile', class: 'AdminProfile', required: false }
        ];

        for (const moduleInfo of modulesToPreload) {
            try {
                // Verificar si la clase del módulo existe
                if (window[moduleInfo.class]) {
                    const moduleInstance = new window[moduleInfo.class](this.adminCore);
                    this.modules[moduleInfo.name] = moduleInstance;
                    this.preloadedModules.add(moduleInfo.name);
                    console.log(`✓ Module pre-loaded: ${moduleInfo.name}`);
                } else if (moduleInfo.required) {
                    console.warn(`⚠ Required module class not found: ${moduleInfo.class}`);
                } else {
                    console.log(`ⓘ Optional module not available: ${moduleInfo.class}`);
                }
            } catch (error) {
                if (moduleInfo.required) {
                    console.error(`✗ Error pre-loading required module ${moduleInfo.name}:`, error);
                    throw error;
                } else {
                    console.warn(`⚠ Error pre-loading optional module ${moduleInfo.name}:`, error);
                }
            }
        }

        console.log(`Pre-loaded ${this.preloadedModules.size} modules:`, Array.from(this.preloadedModules));
    }

    registerModules() {
        // Registrar módulos en AdminCore
        Object.entries(this.modules).forEach(([name, moduleInstance]) => {
            this.adminCore.registerModule(name, moduleInstance);
        });

        console.log('All modules registered in AdminCore');
    }

    setupGlobalErrorHandling() {
        // Manejo de errores JavaScript no capturados
        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
            this.showError('Error inesperado en la aplicación');
        });

        // Manejo de promesas rechazadas no capturadas
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.showError('Error en operación asíncrona');
        });
    }

    setupPageVisibilityHandling() {
        // Pausar operaciones cuando la página no está visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Página oculta - pausar auto-refresh y operaciones pesadas
                Object.values(this.modules).forEach(module => {
                    if (typeof module.stopAutoRefresh === 'function') {
                        module.stopAutoRefresh();
                    }
                });
            } else {
                // Página visible - reanudar operaciones
                Object.values(this.modules).forEach(module => {
                    if (typeof module.startAutoRefresh === 'function') {
                        module.startAutoRefresh();
                    }
                });
            }
        });
    }

    // API pública
    loadModule(moduleName) {
        if (!this.isInitialized) {
            console.warn('SPA not yet initialized, deferring module load');
            return;
        }

        if (this.adminCore) {
            this.adminCore.loadModule(moduleName);
        } else {
            console.error('AdminCore not initialized');
        }
    }

    refreshModule(moduleName) {
        const module = this.modules[moduleName];
        if (module && typeof module.loadData === 'function') {
            return module.loadData();
        }
    }

    showError(message) {
        if (this.adminCore) {
            this.adminCore.showError(message);
        } else {
            console.error(message);
            alert(message);
        }
    }

    showSuccess(message) {
        if (this.adminCore) {
            this.adminCore.showSuccess(message);
        } else {
            console.log(message);
            alert(message);
        }
    }

    // Métodos de utilidad
    formatCurrency(amount) {
        return this.adminCore ? this.adminCore.formatCurrency(amount) : `$${amount}`;
    }

    formatDate(date) {
        return this.adminCore ? this.adminCore.formatDate(date) : date;
    }

    getCurrentModule() {
        return this.adminCore ? this.adminCore.currentModule : null;
    }

    isModuleLoaded(moduleName) {
        return this.preloadedModules.has(moduleName);
    }

    // Cleanup
    destroy() {
        // Limpiar módulos
        Object.values(this.modules).forEach(module => {
            if (typeof module.destroy === 'function') {
                module.destroy();
            }
        });

        this.modules = {};
        this.preloadedModules.clear();
        this.isInitialized = false;

        console.log('Admin SPA destroyed');
    }
}

// Inicializar SPA cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.adminSPA = new AdminSPA();

    // Exportar funciones globales para compatibilidad
    window.loadModule = function(moduleName) {
        if (window.adminSPA) {
            return window.adminSPA.loadModule(moduleName);
        }
    };

    window.refreshDashboard = function() {
        if (window.adminSPA) {
            return window.adminSPA.refreshModule('dashboard');
        }
    };

    window.formatCurrency = function(amount) {
        return window.adminSPA ? window.adminSPA.formatCurrency(amount) : `$${amount}`;
    };

    window.formatDate = function(date) {
        return window.adminSPA ? window.adminSPA.formatDate(date) : date;
    };

    window.showSuccess = function(message) {
        if (window.adminSPA) {
            window.adminSPA.showSuccess(message);
        } else {
            alert(message);
        }
    };

    window.showError = function(message) {
        if (window.adminSPA) {
            window.adminSPA.showError(message);
        } else {
            alert(message);
        }
    };

    window.getCurrentModule = function() {
        return window.adminSPA ? window.adminSPA.getCurrentModule() : null;
    };
});

// Limpiar al salir de la página
window.addEventListener('beforeunload', function() {
    if (window.adminSPA) {
        window.adminSPA.destroy();
    }
});

// Exportar para uso global
window.AdminSPA = AdminSPA;