/**
 * Dashboard Líder Manager v3.1 - Funcional y Optimizado
 * @author Claude Code
 * @license MIT
 */

class DashboardManager {
    constructor() {
        this.config = {
            updateInterval: 30000,
            notificationInterval: 15000,
            requestTimeout: 10000
        };

        this.timers = {
            update: null,
            notification: null
        };

        this.state = {
            isUpdating: false,
            isVisible: !document.hidden
        };

        this.init();
    }

    init() {
        console.log('[Dashboard] Inicializando...');
        this.setupVisibilityHandling();
        this.initAnimations();
        this.initToasts();
        this.startRealTimeUpdates();
        console.log('[Dashboard] Inicializado correctamente v3.1');
    }

    setupVisibilityHandling() {
        document.addEventListener('visibilitychange', () => {
            this.state.isVisible = !document.hidden;
            if (this.state.isVisible) {
                console.log('[Dashboard] Tab visible - reanudando actualizaciones');
                this.startRealTimeUpdates();
            } else {
                console.log('[Dashboard] Tab oculto - pausando actualizaciones');
                this.stopRealTimeUpdates();
            }
        });

        window.addEventListener('online', () => {
            console.log('[Dashboard] Conexión restaurada');
            this.showToast('Conexión restaurada', 'success');
        });

        window.addEventListener('offline', () => {
            console.log('[Dashboard] Conexión perdida');
            this.showToast('Sin conexión a internet', 'warning');
        });
    }

    initAnimations() {
        requestAnimationFrame(() => {
            const cards = document.querySelectorAll('.dashboard-stat-card');
            cards.forEach((card, idx) => {
                card.style.animationDelay = `${idx * 0.1}s`;
                card.classList.add('animate-fade-in-up');
            });
        });
    }

    initToasts() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.className = 'dashboard-toast-container';
        this.toastContainer.style.cssText = 'position:fixed;top:24px;right:24px;z-index:1080;pointer-events:none;display:flex;flex-direction:column;gap:12px';
        document.body.appendChild(this.toastContainer);
    }

    showToast(message, type = 'success', duration = 3000) {
        if (!message) return;

        const toast = document.createElement('div');
        toast.className = `dashboard-toast ${type}`;
        toast.style.pointerEvents = 'all';

        const icon = this.getToastIcon(type);
        toast.innerHTML = `<i class="bi bi-${icon}"></i><span>${this.sanitizeHTML(message)}</span>`;

        this.toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || icons.info;
    }

    startRealTimeUpdates() {
        if (this.timers.update) return;

        this.timers.update = setInterval(() => {
            this.updateDashboardStats();
        }, this.config.updateInterval);

        console.log('[Dashboard] Actualizaciones en tiempo real iniciadas');
    }

    stopRealTimeUpdates() {
        if (this.timers.update) {
            clearInterval(this.timers.update);
            this.timers.update = null;
        }
        console.log('[Dashboard] Actualizaciones en tiempo real detenidas');
    }

    async updateDashboardStats() {
        if (this.state.isUpdating || !this.state.isVisible) return;

        this.state.isUpdating = true;

        try {
            const response = await fetch('/lider/dashboard/stats', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data && data.success) {
                this.updateStatsUI(data);
                console.log('[Dashboard] Estadísticas actualizadas');
            }
        } catch (error) {
            console.error('[Dashboard] Error al actualizar:', error);
        } finally {
            this.state.isUpdating = false;
        }
    }

    updateStatsUI(data) {
        if (!data || !data.stats) return;

        const updates = [
            { id: 'equipo-total', value: data.stats.equipo_total },
            { id: 'ventas-mes', value: this.formatCurrency(data.stats.ventas_mes_actual) },
            { id: 'comisiones-mes', value: this.formatCurrency(data.stats.comisiones_mes) },
            { id: 'progreso-meta', value: `${parseFloat(data.stats.progreso_meta || 0).toFixed(1)}%` }
        ];

        updates.forEach(({ id, value }) => {
            this.updateStatCard(id, value);
        });
    }

    updateStatCard(id, value) {
        const element = document.querySelector(`[data-stat="${id}"]`);
        if (!element) return;

        if (element.textContent !== String(value)) {
            element.textContent = value;
            element.classList.add('animate-pulse');
            setTimeout(() => element.classList.remove('animate-pulse'), 600);
        }
    }

    getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    sanitizeHTML(str) {
        if (typeof str !== 'string') return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    formatCurrency(value) {
        const num = parseFloat(value) || 0;
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num).replace('COP', '$').trim();
    }

    destroy() {
        this.stopRealTimeUpdates();
        this.toastContainer?.remove();
        console.log('[Dashboard] Manager destruido');
    }
}

// Inicializar cuando el DOM esté listo
let dashboardManager;

document.addEventListener('DOMContentLoaded', () => {
    try {
        dashboardManager = new DashboardManager();
    } catch (error) {
        console.error('[Dashboard] Error de inicialización:', error);
    }
});

window.addEventListener('beforeunload', () => {
    dashboardManager?.destroy();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardManager;
}
