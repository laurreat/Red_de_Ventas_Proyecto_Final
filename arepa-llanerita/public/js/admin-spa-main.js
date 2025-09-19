/**
 * Admin SPA Main Application
 * Inicializador principal del dashboard SPA
 */

// Variables globales
let adminCore = null;

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Admin SPA...');

    // Verificar dependencias
    if (typeof AdminCore === 'undefined') {
        console.error('AdminCore not loaded');
        return;
    }

    // Inicializar núcleo
    adminCore = new AdminCore();
    window.adminCore = adminCore; // Hacer disponible globalmente

    // Registrar módulos cuando estén disponibles
    registerModules();

    // Configurar manejo de errores global
    setupGlobalErrorHandling();

    // Configurar eventos de visibilidad de página
    setupPageVisibilityHandling();

    console.log('Admin SPA initialized successfully');
});

function registerModules() {
    // Registrar módulos cuando estén disponibles
    setTimeout(() => {
        // Módulo de dashboard (incluido en admin-spa.js original)
        if (typeof AdminDashboard !== 'undefined') {
            adminCore.registerModule('dashboard', new AdminDashboard(adminCore));
        }

        // Módulo de comisiones
        if (typeof AdminCommissions !== 'undefined') {
            adminCore.registerModule('commissions', new AdminCommissions(adminCore));
        }

        // Módulo de red de referidos
        if (typeof AdminReferrals !== 'undefined') {
            adminCore.registerModule('referrals', new AdminReferrals(adminCore));
        }

        // Módulo de configuración
        if (typeof AdminConfig !== 'undefined') {
            adminCore.registerModule('config', new AdminConfig(adminCore));
        }

        // Módulo de respaldos
        if (typeof AdminBackups !== 'undefined') {
            adminCore.registerModule('backups', new AdminBackups(adminCore));
        }

        // Módulo de logs
        if (typeof AdminLogs !== 'undefined') {
            adminCore.registerModule('logs', new AdminLogs(adminCore));
        }

        // Módulo de perfil
        if (typeof AdminProfile !== 'undefined') {
            adminCore.registerModule('profile', new AdminProfile(adminCore));
        }

        console.log('Modules registered:', Object.keys(adminCore.modules));
    }, 100);
}

function setupGlobalErrorHandling() {
    // Manejo de errores JavaScript no capturados
    window.addEventListener('error', function(event) {
        console.error('Global error:', event.error);

        if (adminCore) {
            adminCore.showError('Error inesperado en la aplicación');
        }
    });

    // Manejo de promesas rechazadas no capturadas
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);

        if (adminCore) {
            adminCore.showError('Error en operación asíncrona');
        }
    });
}

function setupPageVisibilityHandling() {
    // Pausar operaciones cuando la página no está visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Página oculta - pausar auto-refresh y operaciones pesadas
            if (adminCore?.modules?.logs?.autoRefresh) {
                adminCore.modules.logs.stopAutoRefresh();
            }
        } else {
            // Página visible - reanudar operaciones
            if (adminCore?.modules?.logs?.autoRefresh) {
                adminCore.modules.logs.startAutoRefresh();
            }
        }
    });
}

// Funciones globales para compatibilidad con HTML existente
function loadModule(moduleName) {
    if (adminCore) {
        adminCore.loadModule(moduleName);
    } else {
        console.error('AdminCore not initialized');
    }
}

function refreshDashboard() {
    if (adminCore?.modules?.dashboard) {
        adminCore.modules.dashboard.loadData();
    }
}

// Funciones de utilidad globales
function formatCurrency(amount) {
    return adminCore ? adminCore.formatCurrency(amount) : `$${amount}`;
}

function formatDate(date) {
    return adminCore ? adminCore.formatDate(date) : date;
}

function showSuccess(message) {
    if (adminCore) {
        adminCore.showSuccess(message);
    } else {
        alert(message);
    }
}

function showError(message) {
    if (adminCore) {
        adminCore.showError(message);
    } else {
        alert(message);
    }
}

// Exportar para uso global
window.loadModule = loadModule;
window.refreshDashboard = refreshDashboard;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.showSuccess = showSuccess;
window.showError = showError;