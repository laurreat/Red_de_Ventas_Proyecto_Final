/**
 * Pedidos Module JavaScript
 * Funcionalidades específicas para el módulo de gestión de pedidos
 */

class PedidosModule {
    constructor(adminCore) {
        this.adminCore = adminCore;
        this.currentPage = 1;
        this.itemsPerPage = 15;
        this.filters = {
            search: '',
            estado: '',
            fecha_desde: '',
            fecha_hasta: '',
            vendedor: '',
            cliente: ''
        };
        this.sortBy = 'created_at';
        this.sortOrder = 'desc';

        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('Pedidos Module initialized');
    }

    setupEventListeners() {
        // Búsqueda con debounce
        const searchInput = document.getElementById('pedido-search');
        if (searchInput) {
            searchInput.addEventListener('input',
                this.adminCore.debounce((e) => {
                    this.filters.search = e.target.value;
                    this.currentPage = 1;
                    this.loadData();
                }, 300)
            );
        }

        // Filtros
        document.getElementById('pedido-estado-filter')?.addEventListener('change', (e) => {
            this.filters.estado = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        document.getElementById('pedido-fecha-desde')?.addEventListener('change', (e) => {
            this.filters.fecha_desde = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        document.getElementById('pedido-fecha-hasta')?.addEventListener('change', (e) => {
            this.filters.fecha_hasta = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        // Botón filtrar
        document.getElementById('filter-pedidos-btn')?.addEventListener('click', () => {
            this.loadData();
        });

        // Botón limpiar filtros
        document.getElementById('clear-pedido-filters-btn')?.addEventListener('click', () => {
            this.clearFilters();
        });

        // Botón nuevo pedido
        document.getElementById('btn-nuevo-pedido')?.addEventListener('click', () => {
            this.openPedidoModal('create');
        });

        // Botón exportar
        document.getElementById('btn-export-pedidos')?.addEventListener('click', () => {
            this.exportPedidos();
        });

        // Ordenamiento
        document.getElementById('pedido-sort')?.addEventListener('change', (e) => {
            const [sortBy, sortOrder] = e.target.value.split(':');
            this.sortBy = sortBy;
            this.sortOrder = sortOrder;
            this.loadData();
        });
    }

    async loadData() {
        try {
            this.adminCore.showLoading();

            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                sortBy: this.sortBy,
                sortOrder: this.sortOrder,
                ...this.filters
            });

            const response = await this.adminCore.apiCall(`/api/admin/orders?${params.toString()}`);

            this.renderPedidosTable(response.data.orders);
            this.renderPagination(response.data.pagination);
            this.updateStats(response.data.stats);

            // Guardar en cache
            this.adminCore.setCacheData('pedidos', response.data);

        } catch (error) {
            console.error('Error loading orders:', error);
            this.adminCore.showError('Error al cargar pedidos');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    renderPedidosTable(pedidos) {
        const tbody = document.getElementById('pedidos-table-body');
        if (!tbody) return;

        if (!pedidos || pedidos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <div class="py-4">
                            <i class="bi bi-cart fs-1 d-block mb-2"></i>
                            No se encontraron pedidos
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = pedidos.map(pedido => `
            <tr data-pedido-id="${pedido._id}">
                <td>
                    <span class="pedido-numero">${pedido.numero_pedido}</span>
                </td>
                <td>
                    <div class="cliente-info">
                        <div class="cliente-avatar">
                            ${this.getUserInitials(pedido.cliente.name)}
                        </div>
                        <div class="cliente-nombre">${pedido.cliente.name}</div>
                    </div>
                </td>
                <td>
                    <div class="vendedor-info">
                        <div class="vendedor-avatar">
                            ${this.getUserInitials(pedido.vendedor.name)}
                        </div>
                        <div class="vendedor-nombre">${pedido.vendedor.name}</div>
                    </div>
                </td>
                <td>
                    <div class="pedido-total">${this.adminCore.formatCurrency(pedido.total_final)}</div>
                    ${pedido.items ? `<small class="text-muted">${pedido.items.length} items</small>` : ''}
                </td>
                <td>
                    <span class="pedido-status status-${pedido.estado}">
                        ${this.formatEstado(pedido.estado)}
                    </span>
                </td>
                <td>
                    <div class="pedido-fecha">${this.adminCore.formatDate(pedido.created_at)}</div>
                    <small class="text-muted">${this.adminCore.formatDateTime(pedido.created_at).split(' ')[1]}</small>
                </td>
                <td>
                    <div class="pedido-actions">
                        <button class="pedido-action-btn btn-pedido-view" onclick="viewPedido('${pedido._id}')" title="Ver detalles">
                            <i class="bi bi-eye"></i>
                        </button>
                        ${this.canEditPedido(pedido.estado) ? `
                            <button class="pedido-action-btn btn-pedido-edit" onclick="editPedido('${pedido._id}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                        ` : ''}
                        ${this.canUpdateStatus(pedido.estado) ? `
                            <div class="dropdown d-inline">
                                <button class="pedido-action-btn btn-pedido-status dropdown-toggle" data-bs-toggle="dropdown" title="Cambiar estado">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    ${this.getStatusOptions(pedido.estado, pedido._id)}
                                </ul>
                            </div>
                        ` : ''}
                        ${pedido.estado === 'pendiente' ? `
                            <button class="pedido-action-btn btn-pedido-delete" onclick="deletePedido('${pedido._id}')" title="Cancelar">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('pedidos-pagination');
        if (!container || !pagination) return;

        const { currentPage, totalPages, totalItems } = pagination;

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '<nav aria-label="Paginación pedidos"><ul class="pagination justify-content-center">';

        // Botón anterior
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="changePedidoPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
        `;

        // Páginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="changePedidoPage(${i})">${i}</button>
                </li>
            `;
        }

        // Botón siguiente
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="changePedidoPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        `;

        paginationHTML += '</ul></nav>';

        // Información de paginación
        paginationHTML += `
            <div class="text-center mt-2">
                <small class="text-muted">
                    Mostrando ${((currentPage - 1) * this.itemsPerPage) + 1} - ${Math.min(currentPage * this.itemsPerPage, totalItems)} de ${totalItems} pedidos
                </small>
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    updateStats(stats) {
        if (!stats) return;

        const containers = {
            'total-pedidos-stat': stats.total,
            'pedidos-hoy-stat': stats.hoy,
            'pedidos-pendientes-stat': stats.pendientes,
            'valor-total-stat': this.adminCore.formatCurrency(stats.valor_total)
        };

        Object.entries(containers).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }

    getUserInitials(name) {
        return name.split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    formatEstado(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'confirmado': 'Confirmado',
            'en_preparacion': 'En Preparación',
            'listo': 'Listo',
            'en_camino': 'En Camino',
            'entregado': 'Entregado',
            'cancelado': 'Cancelado'
        };
        return estados[estado] || estado;
    }

    canEditPedido(estado) {
        return ['pendiente', 'confirmado'].includes(estado);
    }

    canUpdateStatus(estado) {
        return estado !== 'entregado' && estado !== 'cancelado';
    }

    getStatusOptions(currentStatus, pedidoId) {
        const statusFlow = {
            'pendiente': ['confirmado', 'cancelado'],
            'confirmado': ['en_preparacion', 'cancelado'],
            'en_preparacion': ['listo'],
            'listo': ['en_camino'],
            'en_camino': ['entregado']
        };

        const nextStatuses = statusFlow[currentStatus] || [];

        return nextStatuses.map(status => `
            <li>
                <a class="dropdown-item" href="#" onclick="updatePedidoStatus('${pedidoId}', '${status}')">
                    <span class="pedido-status status-${status}">${this.formatEstado(status)}</span>
                </a>
            </li>
        `).join('');
    }

    clearFilters() {
        this.filters = {
            search: '',
            estado: '',
            fecha_desde: '',
            fecha_hasta: '',
            vendedor: '',
            cliente: ''
        };

        // Limpiar campos de filtro
        document.getElementById('pedido-search').value = '';
        document.getElementById('pedido-estado-filter').value = '';
        document.getElementById('pedido-fecha-desde').value = '';
        document.getElementById('pedido-fecha-hasta').value = '';

        this.currentPage = 1;
        this.loadData();
    }

    changePedidoPage(page) {
        this.currentPage = page;
        this.loadData();
    }

    async viewPedido(pedidoId) {
        try {
            this.adminCore.showLoading();

            const response = await this.adminCore.apiCall(`/api/admin/orders/${pedidoId}`);
            const pedido = response.data;

            this.showPedidoModal(pedido);

        } catch (error) {
            console.error('Error loading order details:', error);
            this.adminCore.showError('Error al cargar detalles del pedido');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    showPedidoModal(pedido) {
        // TODO: Implementar modal de detalles de pedido
        console.log('Pedido details:', pedido);
        this.adminCore.showSuccess('Modal de detalles próximamente');
    }

    async editPedido(pedidoId) {
        console.log('Edit pedido:', pedidoId);
        this.adminCore.showError('Edición de pedido próximamente');
    }

    async updatePedidoStatus(pedidoId, newStatus) {
        try {
            this.adminCore.showLoading();

            await this.adminCore.apiCall(`/api/admin/orders/${pedidoId}/status`, {
                method: 'PATCH',
                body: JSON.stringify({ estado: newStatus })
            });

            this.adminCore.showSuccess(`Estado del pedido actualizado a: ${this.formatEstado(newStatus)}`);
            this.loadData();

        } catch (error) {
            console.error('Error updating order status:', error);
            this.adminCore.showError('Error al actualizar estado del pedido');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    async deletePedido(pedidoId) {
        if (!confirm('¿Está seguro de cancelar este pedido? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            this.adminCore.showLoading();

            await this.adminCore.apiCall(`/api/admin/orders/${pedidoId}`, {
                method: 'DELETE'
            });

            this.adminCore.showSuccess('Pedido cancelado correctamente');
            this.loadData();

        } catch (error) {
            console.error('Error deleting order:', error);
            this.adminCore.showError('Error al cancelar pedido');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    async openPedidoModal(action, pedidoId = null) {
        console.log(`Opening pedido modal: ${action}`, pedidoId);
        this.adminCore.showError('Modal de pedido próximamente');
    }

    async exportPedidos() {
        try {
            this.adminCore.showLoading();

            const params = new URLSearchParams({
                ...this.filters,
                export: 'excel'
            });

            const response = await fetch(`/api/admin/orders/export?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.adminCore.csrfToken,
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            });

            if (!response.ok) {
                throw new Error('Error al exportar pedidos');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `pedidos_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            this.adminCore.showSuccess('Pedidos exportados correctamente');

        } catch (error) {
            console.error('Error exporting orders:', error);
            this.adminCore.showError('Error al exportar pedidos');
        } finally {
            this.adminCore.hideLoading();
        }
    }
}

// Funciones globales para compatibilidad
window.viewPedido = function(pedidoId) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.viewPedido(pedidoId);
    }
};

window.editPedido = function(pedidoId) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.editPedido(pedidoId);
    }
};

window.updatePedidoStatus = function(pedidoId, newStatus) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.updatePedidoStatus(pedidoId, newStatus);
    }
};

window.deletePedido = function(pedidoId) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.deletePedido(pedidoId);
    }
};

window.changePedidoPage = function(page) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.changePedidoPage(page);
    }
};

window.openPedidoModal = function(action, pedidoId = null) {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.openPedidoModal(action, pedidoId);
    }
};

window.exportPedidos = function() {
    if (window.adminSPA && window.adminSPA.modules.orders) {
        window.adminSPA.modules.orders.exportPedidos();
    }
};

// Exportar módulo
window.PedidosModule = PedidosModule;