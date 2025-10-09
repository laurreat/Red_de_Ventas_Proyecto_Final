/**
 * ============================================
 * Dashboard Administrador - JavaScript
 * Funcionalidad: Interactividad, Animaciones, Actualización en Tiempo Real
 * Optimizado: PWA, Performance, Seguridad
 * Versión: 2.0
 * ============================================
 */

class AdminDashboard {
    constructor() {
        this.config = {
            updateInterval: 60000, // 1 minuto
            animationDelay: 100,
            cacheTime: 300000, // 5 minutos
        };

        this.state = {
            isUpdating: false,
            lastUpdate: null,
            stats: {},
        };

        this.init();
    }

    /**
     * Inicializar dashboard
     */
    init() {
        this.setupEventListeners();
        this.initAnimations();
        this.startAutoUpdate();
        this.initCharts();
        this.initTooltips();
        this.checkConnectivity();
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Detectar cuando la ventana está visible para actualizar
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshDashboard();
            }
        });

        // Refresh manual
        const refreshBtn = document.querySelector('[data-action="refresh"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.refreshDashboard(true);
            });
        }

        // Animaciones de hover en cards
        this.setupCardHoverEffects();

        // Lazy loading de imágenes
        this.setupLazyLoading();
    }

    /**
     * Efectos de hover en cards
     */
    setupCardHoverEffects() {
        const cards = document.querySelectorAll('.stat-card, .content-card');

        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    /**
     * Lazy loading de imágenes
     */
    setupLazyLoading() {
        const images = document.querySelectorAll('img[loading="lazy"]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    /**
     * Inicializar animaciones
     */
    initAnimations() {
        // Animación de números contadores
        this.animateCounters();

        // Animación de barras de progreso
        this.animateProgressBars();

        // Fade in de elementos
        this.fadeInElements();
    }

    /**
     * Animar contadores numéricos
     */
    animateCounters() {
        const counters = document.querySelectorAll('.stat-value');

        counters.forEach((counter, index) => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, '')) || 0;
            const duration = 1000;
            const start = 0;
            const increment = target / (duration / 16);

            let current = start;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            };

            setTimeout(() => updateCounter(), index * this.config.animationDelay);
        });
    }

    /**
     * Animar barras de progreso
     */
    animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar-fill');

        progressBars.forEach((bar, index) => {
            const width = bar.style.width || bar.dataset.width || '0%';
            bar.style.width = '0%';

            setTimeout(() => {
                bar.style.width = width;
            }, index * this.config.animationDelay);
        });
    }

    /**
     * Fade in de elementos
     */
    fadeInElements() {
        const elements = document.querySelectorAll('.fade-in-up');

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            elements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                observer.observe(el);
            });
        }
    }

    /**
     * Actualizar dashboard automáticamente
     */
    startAutoUpdate() {
        setInterval(() => {
            if (!document.hidden) {
                this.refreshDashboard();
            }
        }, this.config.updateInterval);
    }

    /**
     * Refrescar datos del dashboard
     */
    async refreshDashboard(manual = false) {
        if (this.state.isUpdating) return;

        // Si es actualización manual, mostrar feedback
        if (manual) {
            this.showRefreshIndicator();
        }

        this.state.isUpdating = true;

        try {
            const response = await this.fetchDashboardData();

            if (response.success) {
                this.updateDashboardUI(response.data);
                this.state.lastUpdate = new Date();

                if (manual) {
                    this.showNotification('Dashboard actualizado correctamente', 'success');
                }
            }
        } catch (error) {
            console.error('Error refreshing dashboard:', error);

            if (manual) {
                this.showNotification('Error al actualizar dashboard', 'error');
            }
        } finally {
            this.state.isUpdating = false;
            this.hideRefreshIndicator();
        }
    }

    /**
     * Obtener datos del dashboard
     */
    async fetchDashboardData() {
        try {
            const response = await fetch('/api/dashboard/stats', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            // Retornar datos cacheados si hay error de red
            return this.getCachedData();
        }
    }

    /**
     * Actualizar UI con nuevos datos
     */
    updateDashboardUI(data) {
        // Actualizar stats
        if (data.stats) {
            Object.keys(data.stats).forEach(key => {
                const element = document.querySelector(`[data-stat="${key}"]`);
                if (element) {
                    this.updateStatValue(element, data.stats[key]);
                }
            });
        }

        // Actualizar pedidos recientes
        if (data.pedidos_recientes) {
            this.updateRecentOrders(data.pedidos_recientes);
        }

        // Actualizar productos populares
        if (data.productos_populares) {
            this.updatePopularProducts(data.productos_populares);
        }

        // Cachear datos
        this.cacheData(data);
    }

    /**
     * Actualizar valor de stat con animación
     */
    updateStatValue(element, newValue) {
        const currentValue = parseInt(element.textContent.replace(/[^0-9]/g, '')) || 0;
        const target = parseInt(newValue);

        if (currentValue === target) return;

        // Agregar indicador de cambio
        const isIncrease = target > currentValue;
        const changeClass = isIncrease ? 'stat-increase' : 'stat-decrease';

        element.classList.add(changeClass);
        setTimeout(() => element.classList.remove(changeClass), 1000);

        // Animar el cambio
        const duration = 500;
        const increment = (target - currentValue) / (duration / 16);
        let current = currentValue;

        const animate = () => {
            current += increment;

            if ((increment > 0 && current < target) || (increment < 0 && current > target)) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(animate);
            } else {
                element.textContent = target.toLocaleString();
            }
        };

        animate();
    }

    /**
     * Actualizar tabla de pedidos recientes
     */
    updateRecentOrders(orders) {
        const tbody = document.querySelector('.orders-table tbody');
        if (!tbody || !orders.length) return;

        const rows = orders.map(order => this.createOrderRow(order)).join('');
        tbody.innerHTML = rows;
    }

    /**
     * Crear fila de pedido
     */
    createOrderRow(order) {
        const statusClass = this.getStatusClass(order.estado);

        return `
            <tr data-order-id="${order.id}">
                <td data-label="Pedido">
                    <span class="order-number">${order.numero_pedido}</span>
                </td>
                <td data-label="Cliente">
                    <div class="order-client">
                        <div class="client-avatar">${order.cliente.name.charAt(0).toUpperCase()}</div>
                        <div class="client-info">
                            <span class="client-name">${order.cliente.name}</span>
                            <span class="client-email">${order.cliente.email}</span>
                        </div>
                    </div>
                </td>
                <td data-label="Total">
                    <span class="order-amount">$${this.formatCurrency(order.total_final)}</span>
                </td>
                <td data-label="Estado">
                    <span class="order-status ${statusClass}">${this.formatStatus(order.estado)}</span>
                </td>
                <td data-label="Fecha">
                    <span class="order-date">${this.formatDate(order.created_at)}</span>
                </td>
            </tr>
        `;
    }

    /**
     * Actualizar productos populares
     */
    updatePopularProducts(products) {
        const container = document.querySelector('.product-list');
        if (!container || !products.length) return;

        const items = products.map((product, index) => this.createProductItem(product, index)).join('');
        container.innerHTML = items;

        // Re-animar barras de progreso
        this.animateProgressBars();
    }

    /**
     * Crear item de producto
     */
    createProductItem(product, index) {
        const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
        const maxSales = product.maxSales || product.cantidad_vendida;
        const percentage = maxSales > 0 ? (product.cantidad_vendida / maxSales) * 100 : 0;

        return `
            <div class="product-item">
                <div class="product-rank ${rankClass}">${index + 1}</div>
                <div class="product-info">
                    <span class="product-name">${product.nombre}</span>
                    <span class="product-category">${product.categoria || 'Sin categoría'}</span>
                </div>
                <div class="product-progress">
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-fill" data-width="${percentage}%" style="width: 0%;"></div>
                    </div>
                    <div class="progress-label">${percentage.toFixed(1)}%</div>
                </div>
                <div class="product-sales">
                    <span class="sales-value">${product.cantidad_vendida}</span>
                    <span class="sales-label">vendidos</span>
                </div>
            </div>
        `;
    }

    /**
     * Inicializar gráficos (si existen)
     */
    initCharts() {
        // Implementar gráficos con Chart.js si es necesario
        const chartElements = document.querySelectorAll('[data-chart]');

        chartElements.forEach(el => {
            // Aquí se inicializarían los gráficos
            console.log('Chart element found:', el.dataset.chart);
        });
    }

    /**
     * Inicializar tooltips
     */
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');

        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            tooltipElements.forEach(el => {
                new bootstrap.Tooltip(el);
            });
        }
    }

    /**
     * Verificar conectividad
     */
    checkConnectivity() {
        window.addEventListener('online', () => {
            this.showNotification('Conexión restaurada', 'success');
            this.refreshDashboard();
        });

        window.addEventListener('offline', () => {
            this.showNotification('Sin conexión a internet', 'warning');
        });
    }

    /**
     * Mostrar indicador de refresh
     */
    showRefreshIndicator() {
        const indicator = document.querySelector('[data-action="refresh"]');
        if (indicator) {
            indicator.classList.add('spinning');
            indicator.style.pointerEvents = 'none';
        }
    }

    /**
     * Ocultar indicador de refresh
     */
    hideRefreshIndicator() {
        const indicator = document.querySelector('[data-action="refresh"]');
        if (indicator) {
            indicator.classList.remove('spinning');
            indicator.style.pointerEvents = 'auto';
        }
    }

    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info') {
        // Usar el sistema de alertas existente si está disponible
        if (window.adminAlerts) {
            switch(type) {
                case 'success':
                    window.adminAlerts.showSuccess('', message);
                    break;
                case 'error':
                    window.adminAlerts.showError('', message);
                    break;
                case 'warning':
                    window.adminAlerts.showWarning('', message);
                    break;
                default:
                    window.adminAlerts.showInfo('', message);
            }
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    /**
     * Cachear datos
     */
    cacheData(data) {
        if ('localStorage' in window) {
            try {
                localStorage.setItem('dashboard_cache', JSON.stringify({
                    data: data,
                    timestamp: Date.now()
                }));
            } catch (e) {
                console.warn('No se pudo cachear datos:', e);
            }
        }
    }

    /**
     * Obtener datos cacheados
     */
    getCachedData() {
        if ('localStorage' in window) {
            try {
                const cached = localStorage.getItem('dashboard_cache');
                if (cached) {
                    const { data, timestamp } = JSON.parse(cached);

                    // Verificar si el cache aún es válido
                    if (Date.now() - timestamp < this.config.cacheTime) {
                        return { success: true, data: data };
                    }
                }
            } catch (e) {
                console.warn('Error al leer cache:', e);
            }
        }

        return { success: false, data: {} };
    }

    /**
     * Helpers
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    formatStatus(status) {
        const statusMap = {
            'pendiente': 'Pendiente',
            'confirmado': 'Confirmado',
            'en_preparacion': 'En Preparación',
            'listo': 'Listo',
            'en_camino': 'En Camino',
            'entregado': 'Entregado',
            'cancelado': 'Cancelado'
        };

        return statusMap[status] || status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    getStatusClass(status) {
        const classMap = {
            'pendiente': 'pendiente',
            'confirmado': 'confirmado',
            'en_preparacion': 'confirmado',
            'listo': 'entregado',
            'en_camino': 'confirmado',
            'entregado': 'entregado',
            'cancelado': 'cancelado'
        };

        return classMap[status] || 'pendiente';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('es-VE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    }
}

/**
 * Inicializar cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
    console.log('✅ Admin Dashboard initialized');
});

/**
 * Service Worker para PWA (si está disponible)
 */
if ('serviceWorker' in navigator && 'production' === 'production') {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('✅ Service Worker registered:', registration);
            })
            .catch(error => {
                console.log('❌ Service Worker registration failed:', error);
            });
    });
}
