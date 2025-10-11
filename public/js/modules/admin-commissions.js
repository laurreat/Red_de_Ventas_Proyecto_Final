/**
 * Admin Commissions Module
 * Gestión de comisiones del sistema
 */

class AdminCommissions {
    constructor(core) {
        this.core = core;
        this.data = null;
        this.filters = {
            estado: '',
            tipo: '',
            search: '',
            dateFrom: '',
            dateTo: ''
        };

        this.init();
    }

    init() {
        console.log('Commissions module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Filtros de comisiones
        document.getElementById('comision-estado-filter')?.addEventListener('change', (e) => {
            this.filters.estado = e.target.value;
            this.applyFilters();
        });

        document.getElementById('comision-tipo-filter')?.addEventListener('change', (e) => {
            this.filters.tipo = e.target.value;
            this.applyFilters();
        });

        document.getElementById('comision-search')?.addEventListener('input',
            this.core.debounce((e) => {
                this.filters.search = e.target.value;
                this.applyFilters();
            }, 300)
        );

        document.getElementById('comision-fecha-desde')?.addEventListener('change', (e) => {
            this.filters.dateFrom = e.target.value;
            this.applyFilters();
        });

        document.getElementById('comision-fecha-hasta')?.addEventListener('change', (e) => {
            this.filters.dateTo = e.target.value;
            this.applyFilters();
        });

        // Botones de acción
        document.getElementById('btn-exportar-comisiones')?.addEventListener('click', () => {
            this.exportCommissions();
        });

        document.getElementById('btn-calcular-comisiones')?.addEventListener('click', () => {
            this.calculateCommissions();
        });

        document.getElementById('btn-pagar-comisiones')?.addEventListener('click', () => {
            this.payCommissions();
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/commissions');

            if (response.success) {
                this.data = response.data;
                this.renderCommissions(response.data);
                this.renderStats(response.stats);
                this.core.setCacheData('commissions', response);
            } else {
                throw new Error(response.message || 'Error al cargar comisiones');
            }

        } catch (error) {
            console.error('Error loading commissions:', error);
            this.core.showError('Error al cargar las comisiones: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    renderStats(stats) {
        // Actualizar estadísticas principales
        const elements = {
            'comisiones-pendientes': stats.pendientes || 0,
            'comisiones-pagadas': stats.pagadas || 0,
            'comisiones-mes': stats.mes_actual || 0,
            'comisiones-total': stats.total || 0
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = this.core.formatCurrency(value);
            }
        });

        // Crear gráfico de comisiones
        this.renderCommissionsChart(stats);
    }

    renderCommissions(commissions) {
        const tbody = document.getElementById('comisiones-table-body');
        if (!tbody) return;

        if (!commissions || commissions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No hay comisiones para mostrar
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = commissions.map(comision => `
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
                            <div class="fw-medium">${comision.vendedor?.name || 'N/A'}</div>
                            <small class="text-muted">${comision.vendedor?.email || ''}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="fw-bold text-success">${this.core.formatCurrency(comision.monto)}</span>
                </td>
                <td>
                    <span class="badge bg-${this.getTipoColor(comision.tipo)}">${this.formatTipo(comision.tipo)}</span>
                </td>
                <td>
                    <span class="badge bg-${this.core.getStatusColor(comision.estado)}">${this.formatEstado(comision.estado)}</span>
                </td>
                <td>
                    <div>
                        <div>${this.core.formatDate(comision.created_at)}</div>
                        <small class="text-muted">${this.core.formatDateTime(comision.created_at).split(' ')[1]}</small>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="adminCore.modules.commissions.viewCommission('${comision._id}')" title="Ver detalles">
                            <i class="bi bi-eye"></i>
                        </button>
                        ${comision.estado === 'pendiente' ? `
                            <button class="btn btn-outline-success" onclick="adminCore.modules.commissions.approveCommission('${comision._id}')" title="Aprobar">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="adminCore.modules.commissions.rejectCommission('${comision._id}')" title="Rechazar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-outline-info" onclick="adminCore.modules.commissions.downloadReceipt('${comision._id}')" title="Descargar comprobante">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderCommissionsChart(stats) {
        const ctx = document.getElementById('comisionesChart');
        if (!ctx) return;

        // Destruir gráfico anterior si existe
        if (this.core.charts.commissions) {
            this.core.charts.commissions.destroy();
        }

        this.core.charts.commissions = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Pagadas', 'Rechazadas'],
                datasets: [{
                    data: [
                        stats.pendientes || 0,
                        stats.pagadas || 0,
                        stats.rechazadas || 0
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                return context.label + ': ' + adminCore.formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    async applyFilters() {
        try {
            const params = new URLSearchParams();

            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) {
                    params.append(key, value);
                }
            });

            const response = await this.core.apiCall(`/api/admin/commissions?${params.toString()}`);

            if (response.success) {
                this.renderCommissions(response.data);
            }

        } catch (error) {
            console.error('Error applying filters:', error);
            this.core.showError('Error al aplicar filtros');
        }
    }

    clearFilters() {
        this.filters = {
            estado: '',
            tipo: '',
            search: '',
            dateFrom: '',
            dateTo: ''
        };

        // Limpiar campos del formulario
        document.getElementById('comision-estado-filter').value = '';
        document.getElementById('comision-tipo-filter').value = '';
        document.getElementById('comision-search').value = '';
        document.getElementById('comision-fecha-desde').value = '';
        document.getElementById('comision-fecha-hasta').value = '';

        // Recargar datos
        this.loadData();
    }

    async viewCommission(id) {
        try {
            // Implementar modal para ver detalles de comisión
            this.core.showSuccess('Funcionalidad de ver comisión próximamente');
        } catch (error) {
            this.core.showError('Error al cargar detalles de la comisión');
        }
    }

    async approveCommission(id) {
        if (!confirm('¿Está seguro de aprobar esta comisión?')) return;

        try {
            const response = await this.core.apiCall(`/api/admin/commissions/${id}/approve`, {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Comisión aprobada correctamente');
                this.loadData(); // Recargar datos
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al aprobar la comisión: ' + error.message);
        }
    }

    async rejectCommission(id) {
        if (!confirm('¿Está seguro de rechazar esta comisión?')) return;

        try {
            const response = await this.core.apiCall(`/api/admin/commissions/${id}/reject`, {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Comisión rechazada');
                this.loadData(); // Recargar datos
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al rechazar la comisión: ' + error.message);
        }
    }

    async calculateCommissions() {
        if (!confirm('¿Desea calcular las comisiones pendientes? Esta acción puede tomar varios minutos.')) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/commissions/calculate', {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Comisiones calculadas correctamente');
                this.loadData(); // Recargar datos
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al calcular comisiones: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async payCommissions() {
        const selectedCommissions = this.getSelectedCommissions();

        if (selectedCommissions.length === 0) {
            this.core.showError('Seleccione al menos una comisión para pagar');
            return;
        }

        if (!confirm(`¿Desea procesar el pago de ${selectedCommissions.length} comisión(es)?`)) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/commissions/pay', {
                method: 'POST',
                body: JSON.stringify({
                    commissions: selectedCommissions
                })
            });

            if (response.success) {
                this.core.showSuccess('Comisiones pagadas correctamente');
                this.loadData(); // Recargar datos
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al pagar comisiones: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async exportCommissions() {
        try {
            const params = new URLSearchParams(this.filters);
            params.append('export', 'true');

            const response = await this.core.apiCall(`/api/admin/commissions/export?${params.toString()}`);

            if (response.success) {
                // Crear y descargar archivo
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `comisiones_${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.core.showSuccess('Reporte exportado correctamente');
            }

        } catch (error) {
            this.core.showError('Error al exportar comisiones: ' + error.message);
        }
    }

    downloadReceipt(id) {
        // Implementar descarga de comprobante
        this.core.showSuccess('Funcionalidad de descarga próximamente');
    }

    getSelectedCommissions() {
        const checkboxes = document.querySelectorAll('input[name="commission_ids"]:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    getTipoColor(tipo) {
        const colors = {
            'venta': 'primary',
            'referido': 'info',
            'liderazgo': 'warning',
            'bono': 'success'
        };
        return colors[tipo] || 'secondary';
    }

    formatTipo(tipo) {
        const tipos = {
            'venta': 'Venta',
            'referido': 'Referido',
            'liderazgo': 'Liderazgo',
            'bono': 'Bono'
        };
        return tipos[tipo] || tipo;
    }

    formatEstado(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'aprobada': 'Aprobada',
            'pagada': 'Pagada',
            'rechazada': 'Rechazada'
        };
        return estados[estado] || estado;
    }
}

// Exportar para uso global
window.AdminCommissions = AdminCommissions;