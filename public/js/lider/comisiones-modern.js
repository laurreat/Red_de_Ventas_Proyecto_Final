/**
 * Comisiones Manager - Sistema de gestión de comisiones para líderes
 * @version 2.0
 */

class ComisionesManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupTableAnimations();
        this.setupEventListeners();
        this.setupTooltips();
        this.animateStats();
        this.setupModalHandlers();
    }

    setupTableAnimations() {
        const rows = document.querySelectorAll('.comisiones-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.animation = `fadeInUp 0.6s ease-out ${index * 0.05}s forwards`;
        });
    }

    setupEventListeners() {
        const filterForm = document.getElementById('comisionesFilterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                this.showLoading();
            });
        }

        // Event listeners para los botones de acción
        document.addEventListener('click', (e) => {
            // Ver detalles
            if (e.target.closest('.btn-ver-detalle')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-ver-detalle');
                const comisionId = btn.dataset.comisionId;
                if (comisionId) {
                    this.showDetalleComision(comisionId);
                }
            }

            // Cambiar estado
            if (e.target.closest('.btn-cambiar-estado')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-cambiar-estado');
                const comisionId = btn.dataset.comisionId;
                const estadoActual = btn.dataset.estadoActual;
                if (comisionId) {
                    this.showCambiarEstadoModal(comisionId, estadoActual);
                }
            }
        });
    }

    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[data-toggle="tooltip"]');
        tooltipElements.forEach(el => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(el);
            }
        });
    }

    animateStats() {
        const statCards = document.querySelectorAll('.comisiones-stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.animation = `scaleIn 0.6s ease-out ${index * 0.1}s forwards`;
        });
    }

    setupModalHandlers() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    showModal(modalId, type = 'primary') {
        const backdrop = this.createModalBackdrop();
        const modal = document.getElementById(modalId);
        if (!modal) return;

        backdrop.classList.add('show');
        modal.classList.add('show');

        if (type !== 'primary') {
            const header = modal.querySelector('.comisiones-modal-header');
            if (header) {
                header.classList.add(type);
            }
        }

        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                this.closeModal(modalId);
            }
        });

        const closeBtn = modal.querySelector('.comisiones-modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeModal(modalId));
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.querySelector('.comisiones-modal-backdrop');

        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => {
                backdrop.remove();
            }, 300);
        }
    }

    closeAllModals() {
        const modals = document.querySelectorAll('.comisiones-modal.show');
        modals.forEach(modal => {
            this.closeModal(modal.id);
        });
    }

    createModalBackdrop() {
        const existing = document.querySelector('.comisiones-modal-backdrop');
        if (existing) existing.remove();

        const backdrop = document.createElement('div');
        backdrop.className = 'comisiones-modal-backdrop';
        document.body.appendChild(backdrop);

        return backdrop;
    }

    showToast(message, type = 'success', title = '') {
        const toast = document.createElement('div');
        toast.className = `comisiones-toast ${type}`;

        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            danger: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const titleMap = {
            success: 'Éxito',
            error: 'Error',
            danger: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };

        const icon = iconMap[type] || iconMap.success;
        const toastTitle = title || titleMap[type] || titleMap.success;

        toast.innerHTML = `
            <div class="comisiones-toast-icon ${type}">
                <i class="bi ${icon}"></i>
            </div>
            <div class="comisiones-toast-content">
                <div class="comisiones-toast-title">${toastTitle}</div>
                <div class="comisiones-toast-message">${message}</div>
            </div>
            <button class="comisiones-toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;

        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 5000);

        return toast;
    }

    showLoading(text = 'Cargando...') {
        const existing = document.querySelector('.comisiones-loading');
        if (existing) return;

        const loading = document.createElement('div');
        loading.className = 'comisiones-loading';
        loading.innerHTML = `
            <div class="comisiones-spinner"></div>
            <div class="comisiones-loading-text">${text}</div>
        `;

        document.body.appendChild(loading);
        setTimeout(() => loading.classList.add('show'), 10);

        return loading;
    }

    hideLoading() {
        const loading = document.querySelector('.comisiones-loading');
        if (loading) {
            loading.classList.remove('show');
            setTimeout(() => loading.remove(), 300);
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }

    async showDetalleComision(comisionId) {
        this.showLoading('Cargando detalles...');

        try {
            const response = await fetch(`/lider/comisiones/${comisionId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            this.hideLoading();

            if (data.success) {
                this.renderDetalleModal(data);
            } else {
                this.showToast(data.message || 'Error al cargar los detalles', 'error');
            }
        } catch (error) {
            this.hideLoading();
            console.error('Error:', error);
            this.showToast('Error al cargar los detalles de la comisión', 'error');
        }
    }

    renderDetalleModal(data) {
        const modalHtml = `
            <div class="comisiones-modal-backdrop show" id="detalleBackdrop">
                <div class="comisiones-modal show large" id="detalleModal">
                    <div class="comisiones-modal-header info">
                        <h3 class="comisiones-modal-title">
                            <i class="bi bi-receipt"></i>
                            Detalle de Comisión
                        </h3>
                        <button class="comisiones-modal-close" onclick="comisionesManager.closeDetalleModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="comisiones-modal-body">
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Tipo de Comisión</span>
                            <span class="comisiones-detail-value">
                                <span class="comisiones-badge comisiones-badge-${data.tipo}">${data.tipo_formatted}</span>
                            </span>
                        </div>
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Monto</span>
                            <span class="comisiones-detail-value highlight">${this.formatCurrency(data.monto)}</span>
                        </div>
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Porcentaje</span>
                            <span class="comisiones-detail-value">${data.porcentaje}%</span>
                        </div>
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Estado</span>
                            <span class="comisiones-detail-value">
                                <span class="comisiones-badge comisiones-badge-${data.estado}">${data.estado_formatted}</span>
                            </span>
                        </div>
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Fecha de Generación</span>
                            <span class="comisiones-detail-value">${this.formatDate(data.created_at)}</span>
                        </div>
                        ${data.fecha_pago ? `
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Fecha de Pago</span>
                            <span class="comisiones-detail-value">${this.formatDate(data.fecha_pago)}</span>
                        </div>
                        ` : ''}
                        ${data.referido ? `
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Referido</span>
                            <span class="comisiones-detail-value">${data.referido.name}</span>
                        </div>
                        ` : ''}
                        ${data.pedido ? `
                        <div class="comisiones-detail-row">
                            <span class="comisiones-detail-label">Pedido</span>
                            <span class="comisiones-detail-value">#${data.pedido.numero_pedido || String(data.pedido.id).padStart(6, '0')}</span>
                        </div>
                        ` : ''}
                    </div>
                    <div class="comisiones-modal-footer">
                        <button type="button" class="comisiones-btn comisiones-btn-primary" onclick="comisionesManager.closeDetalleModal()">
                            <i class="bi bi-check-circle"></i>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = modalHtml;
        document.body.appendChild(container.firstElementChild);
    }

    closeDetalleModal() {
        const backdrop = document.getElementById('detalleBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => backdrop.remove(), 300);
        }
    }

    showCambiarEstadoModal(comisionId, estadoActual) {
        const modalHtml = `
            <div class="comisiones-modal-backdrop show" id="cambiarEstadoBackdrop">
                <div class="comisiones-modal show" id="cambiarEstadoModal">
                    <div class="comisiones-modal-header warning">
                        <h3 class="comisiones-modal-title">
                            <i class="bi bi-arrow-repeat"></i>
                            Cambiar Estado de Comisión
                        </h3>
                        <button class="comisiones-modal-close" onclick="comisionesManager.closeCambiarEstadoModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="comisiones-modal-body">
                        <p><strong>Estado actual:</strong> <span class="comisiones-badge comisiones-badge-${estadoActual}">${estadoActual}</span></p>

                        <div class="comisiones-form-group">
                            <label class="comisiones-form-label">Nuevo Estado</label>
                            <select id="nuevoEstado" class="comisiones-form-control">
                                <option value="pendiente" ${estadoActual === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="aprobado" ${estadoActual === 'aprobado' ? 'selected' : ''}>Aprobado</option>
                                <option value="pagado" ${estadoActual === 'pagado' ? 'selected' : ''}>Pagado</option>
                                <option value="rechazado" ${estadoActual === 'rechazado' ? 'selected' : ''}>Rechazado</option>
                            </select>
                        </div>
                    </div>
                    <div class="comisiones-modal-footer">
                        <button type="button" class="comisiones-btn comisiones-btn-secondary" onclick="comisionesManager.closeCambiarEstadoModal()">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                        <button type="button" class="comisiones-btn comisiones-btn-warning" onclick="comisionesManager.cambiarEstado('${comisionId}')">
                            <i class="bi bi-check-circle"></i>
                            Cambiar Estado
                        </button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = modalHtml;
        document.body.appendChild(container.firstElementChild);
    }

    closeCambiarEstadoModal() {
        const backdrop = document.getElementById('cambiarEstadoBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => backdrop.remove(), 300);
        }
    }

    async cambiarEstado(comisionId) {
        const nuevoEstado = document.getElementById('nuevoEstado').value;

        if (!nuevoEstado) {
            this.showToast('Selecciona un estado', 'warning');
            return;
        }

        this.showLoading('Cambiando estado...');

        try {
            const response = await fetch(`/lider/comisiones/${comisionId}/cambiar-estado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ estado: nuevoEstado })
            });

            const data = await response.json();
            this.hideLoading();

            if (data.success) {
                this.showToast(data.message || 'Estado actualizado correctamente', 'success');
                this.closeCambiarEstadoModal();

                // Recargar la página después de 1 segundo
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showToast(data.message || 'Error al cambiar el estado', 'error');
            }
        } catch (error) {
            this.hideLoading();
            console.error('Error:', error);
            this.showToast('Error al cambiar el estado de la comisión', 'error');
        }
    }
}

// Inicializar el manager cuando el DOM esté listo
let comisionesManager;

document.addEventListener('DOMContentLoaded', () => {
    comisionesManager = new ComisionesManager();
});

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.comisionesManager = comisionesManager;
}
