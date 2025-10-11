/**
 * Admin Logs Module
 * Gestión de logs del sistema
 */

class AdminLogs {
    constructor(core) {
        this.core = core;
        this.data = null;
        this.filters = {
            level: '',
            date: '',
            search: ''
        };
        this.autoRefresh = false;
        this.refreshInterval = null;

        this.init();
    }

    init() {
        console.log('Logs module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Filtros
        document.getElementById('log-level-filter')?.addEventListener('change', (e) => {
            this.filters.level = e.target.value;
            this.applyFilters();
        });

        document.getElementById('log-date-filter')?.addEventListener('change', (e) => {
            this.filters.date = e.target.value;
            this.applyFilters();
        });

        document.getElementById('log-search')?.addEventListener('input',
            this.core.debounce((e) => {
                this.filters.search = e.target.value;
                this.applyFilters();
            }, 300)
        );

        // Botones de acción
        document.getElementById('btn-refresh-logs')?.addEventListener('click', () => {
            this.loadData();
        });

        document.getElementById('btn-clear-logs')?.addEventListener('click', () => {
            this.clearLogs();
        });

        document.getElementById('btn-export-logs')?.addEventListener('click', () => {
            this.exportLogs();
        });

        document.getElementById('btn-auto-refresh')?.addEventListener('click', () => {
            this.toggleAutoRefresh();
        });

        document.getElementById('btn-clear-filters')?.addEventListener('click', () => {
            this.clearFilters();
        });

        // Configuración de vista
        document.getElementById('log-live-view')?.addEventListener('change', (e) => {
            this.toggleLiveView(e.target.checked);
        });

        document.getElementById('log-auto-scroll')?.addEventListener('change', (e) => {
            this.toggleAutoScroll(e.target.checked);
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const params = new URLSearchParams(this.filters);
            const response = await this.core.apiCall(`/api/admin/logs?${params.toString()}`);

            if (response.success) {
                this.data = response.data;
                this.renderLogs(response.data);
                this.renderLogStats();
                this.core.setCacheData('logs', response.data);
            } else {
                throw new Error(response.message || 'Error al cargar logs');
            }

        } catch (error) {
            console.error('Error loading logs:', error);
            this.core.showError('Error al cargar los logs: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    renderLogs(logs) {
        const container = document.getElementById('logs-content');
        if (!container) return;

        if (!logs || logs.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-journal-text fs-1 d-block mb-2"></i>
                    No hay logs para mostrar
                    <br>
                    <small>Ajusta los filtros o verifica la configuración del sistema</small>
                </div>
            `;
            return;
        }

        // Limpiar contenido anterior
        container.innerHTML = '';

        // Renderizar cada entrada de log
        logs.forEach(log => {
            const logEntry = this.createLogEntry(log);
            container.appendChild(logEntry);
        });

        // Auto-scroll si está habilitado
        if (document.getElementById('log-auto-scroll')?.checked) {
            container.scrollTop = container.scrollHeight;
        }
    }

    createLogEntry(log) {
        const entry = document.createElement('div');
        entry.className = `log-entry ${log.level}`;
        entry.dataset.level = log.level;
        entry.dataset.timestamp = log.timestamp;

        const levelColors = {
            'error': '#f48771',
            'warning': '#dcdcaa',
            'info': '#9cdcfe',
            'debug': '#c586c0'
        };

        entry.innerHTML = `
            <span class="log-timestamp">[${log.timestamp}]</span>
            <span class="log-level" style="color: ${levelColors[log.level] || '#d4d4d4'}">${log.level.toUpperCase()}</span>
            <span class="log-message">${this.formatLogMessage(log.message)}</span>
            ${log.context ? `<details class="log-context mt-1">
                <summary class="text-muted small">Contexto</summary>
                <pre class="small mt-1">${JSON.stringify(log.context, null, 2)}</pre>
            </details>` : ''}
        `;

        // Hacer clickeable para mostrar detalles
        entry.addEventListener('click', () => {
            this.showLogDetails(log);
        });

        return entry;
    }

    formatLogMessage(message) {
        // Formatear mensajes de log para mejor legibilidad
        return message
            .replace(/ERROR/g, '<span class="text-danger fw-bold">ERROR</span>')
            .replace(/WARNING/g, '<span class="text-warning fw-bold">WARNING</span>')
            .replace(/INFO/g, '<span class="text-info fw-bold">INFO</span>')
            .replace(/DEBUG/g, '<span class="text-secondary fw-bold">DEBUG</span>')
            .replace(/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/g, '<span class="text-success">$1</span>')
            .replace(/(http[s]?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-primary">$1</a>')
            .replace(/(\w+@\w+\.\w+)/g, '<span class="text-info">$1</span>');
    }

    renderLogStats() {
        if (!this.data) return;

        const stats = this.calculateLogStats();

        // Actualizar estadísticas
        const elements = {
            'total-logs': stats.total,
            'logs-error': stats.errors,
            'logs-warning': stats.warnings,
            'logs-info': stats.info,
            'logs-debug': stats.debug
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = this.core.formatNumber(value);
            }
        });

        // Renderizar gráfico de distribución
        this.renderLogsChart(stats);
    }

    calculateLogStats() {
        if (!this.data || this.data.length === 0) {
            return {
                total: 0,
                errors: 0,
                warnings: 0,
                info: 0,
                debug: 0
            };
        }

        const stats = this.data.reduce((acc, log) => {
            acc.total++;
            acc[log.level] = (acc[log.level] || 0) + 1;
            return acc;
        }, { total: 0, error: 0, warning: 0, info: 0, debug: 0 });

        return {
            total: stats.total,
            errors: stats.error || 0,
            warnings: stats.warning || 0,
            info: stats.info || 0,
            debug: stats.debug || 0
        };
    }

    renderLogsChart(stats) {
        const ctx = document.getElementById('logsChart');
        if (!ctx) return;

        if (this.core.charts.logs) {
            this.core.charts.logs.destroy();
        }

        this.core.charts.logs = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Errores', 'Advertencias', 'Info', 'Debug'],
                datasets: [{
                    data: [stats.errors, stats.warnings, stats.info, stats.debug],
                    backgroundColor: [
                        '#dc3545',
                        '#ffc107',
                        '#0dcaf0',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#1e1e1e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#d4d4d4'
                        }
                    }
                }
            }
        });
    }

    showLogDetails(log) {
        const modalHtml = `
            <div class="modal fade" id="logDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-${this.getLogLevelColor(log.level)} text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-journal-text me-2"></i>
                                Detalles del Log - ${log.level.toUpperCase()}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Timestamp:</strong><br>
                                    <code>${log.timestamp}</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Nivel:</strong><br>
                                    <span class="badge bg-${this.getLogLevelColor(log.level)}">${log.level.toUpperCase()}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Mensaje:</strong><br>
                                <div class="bg-dark text-light p-3 rounded">
                                    <code>${log.message}</code>
                                </div>
                            </div>

                            ${log.context ? `
                                <div class="mb-3">
                                    <strong>Contexto:</strong>
                                    <div class="bg-dark text-light p-3 rounded">
                                        <pre class="mb-0"><code>${JSON.stringify(log.context, null, 2)}</code></pre>
                                    </div>
                                </div>
                            ` : ''}

                            <div class="mb-3">
                                <strong>Acciones:</strong><br>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="adminCore.modules.logs.copyLogEntry('${log.timestamp}')">
                                        <i class="bi bi-clipboard me-1"></i>Copiar
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="adminCore.modules.logs.searchSimilar('${log.level}')">
                                        <i class="bi bi-search me-1"></i>Buscar Similares
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="adminCore.modules.logs.exportSingle('${log.timestamp}')">
                                        <i class="bi bi-download me-1"></i>Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById('logDetailsModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        modal.show();
    }

    async applyFilters() {
        try {
            const params = new URLSearchParams();

            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) {
                    params.append(key, value);
                }
            });

            const response = await this.core.apiCall(`/api/admin/logs?${params.toString()}`);

            if (response.success) {
                this.renderLogs(response.data);
                this.renderLogStats();
            }

        } catch (error) {
            console.error('Error applying filters:', error);
            this.core.showError('Error al aplicar filtros');
        }
    }

    clearFilters() {
        this.filters = {
            level: '',
            date: '',
            search: ''
        };

        // Limpiar campos del formulario
        document.getElementById('log-level-filter').value = '';
        document.getElementById('log-date-filter').value = '';
        document.getElementById('log-search').value = '';

        // Recargar datos
        this.loadData();
    }

    async clearLogs() {
        if (!confirm('¿Está seguro de que desea eliminar todos los logs del sistema?\n\nEsta acción no se puede deshacer.')) {
            return;
        }

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/logs', {
                method: 'DELETE'
            });

            if (response.success) {
                this.core.showSuccess('Logs eliminados correctamente');
                this.loadData(); // Recargar datos
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al eliminar logs: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async exportLogs() {
        try {
            const params = new URLSearchParams(this.filters);
            params.append('export', 'true');

            const response = await this.core.apiCall(`/api/admin/logs/export?${params.toString()}`);

            if (response.success) {
                // Crear y descargar archivo
                const blob = new Blob([response.data], { type: 'text/plain' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `logs_${new Date().toISOString().split('T')[0]}.log`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.core.showSuccess('Logs exportados correctamente');
            }

        } catch (error) {
            this.core.showError('Error al exportar logs: ' + error.message);
        }
    }

    toggleAutoRefresh() {
        this.autoRefresh = !this.autoRefresh;
        const button = document.getElementById('btn-auto-refresh');

        if (this.autoRefresh) {
            button.classList.add('active');
            button.innerHTML = '<i class="bi bi-pause me-1"></i>Pausar';
            this.startAutoRefresh();
        } else {
            button.classList.remove('active');
            button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto-actualizar';
            this.stopAutoRefresh();
        }
    }

    startAutoRefresh() {
        this.stopAutoRefresh(); // Limpiar intervalo anterior
        this.refreshInterval = setInterval(() => {
            this.loadData();
        }, 5000); // Actualizar cada 5 segundos
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    toggleLiveView(enabled) {
        if (enabled) {
            this.startLiveView();
        } else {
            this.stopLiveView();
        }
    }

    startLiveView() {
        // Implementar vista en tiempo real con WebSockets o polling
        this.core.showSuccess('Vista en tiempo real activada');
        this.toggleAutoRefresh();
    }

    stopLiveView() {
        this.core.showSuccess('Vista en tiempo real desactivada');
        if (this.autoRefresh) {
            this.toggleAutoRefresh();
        }
    }

    toggleAutoScroll(enabled) {
        // El auto-scroll se maneja en renderLogs
        console.log('Auto-scroll:', enabled ? 'enabled' : 'disabled');
    }

    copyLogEntry(timestamp) {
        const logEntry = this.data.find(log => log.timestamp === timestamp);
        if (logEntry) {
            const text = `[${logEntry.timestamp}] ${logEntry.level.toUpperCase()}: ${logEntry.message}`;
            navigator.clipboard.writeText(text).then(() => {
                this.core.showSuccess('Log copiado al portapapeles');
            });
        }
    }

    searchSimilar(level) {
        this.filters.level = level;
        document.getElementById('log-level-filter').value = level;
        this.applyFilters();

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('logDetailsModal'));
        modal?.hide();
    }

    exportSingle(timestamp) {
        const logEntry = this.data.find(log => log.timestamp === timestamp);
        if (logEntry) {
            const text = JSON.stringify(logEntry, null, 2);
            const blob = new Blob([text], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `log_${timestamp.replace(/[:\s]/g, '_')}.json`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    }

    getLogLevelColor(level) {
        const colors = {
            'error': 'danger',
            'warning': 'warning',
            'info': 'info',
            'debug': 'secondary'
        };
        return colors[level] || 'secondary';
    }

    // Cleanup cuando se cambia de módulo
    cleanup() {
        this.stopAutoRefresh();
        this.stopLiveView();
    }
}

// Exportar para uso global
window.AdminLogs = AdminLogs;