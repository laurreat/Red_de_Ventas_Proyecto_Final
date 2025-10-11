/**
 * Admin Backups Module
 * Gestión de respaldos del sistema
 */

class AdminBackups {
    constructor(core) {
        this.core = core;
        this.data = null;

        this.init();
    }

    init() {
        console.log('Backups module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Crear respaldo
        document.getElementById('btn-crear-respaldo')?.addEventListener('click', () => {
            this.showCreateBackupModal();
        });

        // Respaldo automático
        document.getElementById('btn-respaldo-automatico')?.addEventListener('click', () => {
            this.createAutomaticBackup();
        });

        // Programar respaldos
        document.getElementById('btn-programar-respaldos')?.addEventListener('click', () => {
            this.showScheduleModal();
        });

        // Restaurar respaldo
        document.getElementById('btn-restaurar-respaldo')?.addEventListener('click', () => {
            this.showRestoreModal();
        });

        // Configuración de respaldos
        document.getElementById('btn-config-respaldos')?.addEventListener('click', () => {
            this.showConfigModal();
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/backups');

            if (response.success) {
                this.data = response.data;
                this.renderBackupsList(response.data);
                this.renderBackupStats();
                this.core.setCacheData('backups', response.data);
            } else {
                throw new Error(response.message || 'Error al cargar respaldos');
            }

        } catch (error) {
            console.error('Error loading backups:', error);
            this.core.showError('Error al cargar los respaldos: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    renderBackupsList(backups) {
        const tbody = document.getElementById('respaldos-table-body');
        if (!tbody) return;

        if (!backups || backups.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-cloud-arrow-down fs-1 d-block mb-2"></i>
                        No hay respaldos disponibles
                        <br>
                        <button class="btn btn-primary btn-sm mt-2" onclick="adminCore.modules.backups.createAutomaticBackup()">
                            Crear Primer Respaldo
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = backups.map(backup => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-zip fs-4 text-primary me-3"></i>
                        <div>
                            <div class="fw-medium">${backup.nombre}</div>
                            <small class="text-muted">${backup.descripcion || 'Respaldo automático'}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="fw-medium">${backup.tamaño}</span>
                </td>
                <td>
                    <div>
                        <div>${this.core.formatDate(backup.created_at)}</div>
                        <small class="text-muted">${this.core.formatDateTime(backup.created_at).split(' ')[1]}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-${this.getBackupTypeColor(backup.tipo)}">${backup.tipo}</span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success" onclick="adminCore.modules.backups.downloadBackup('${backup.id}')" title="Descargar">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="adminCore.modules.backups.restoreBackup('${backup.id}')" title="Restaurar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="adminCore.modules.backups.viewBackupDetails('${backup.id}')" title="Detalles">
                            <i class="bi bi-info-circle"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="adminCore.modules.backups.deleteBackup('${backup.id}')" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderBackupStats() {
        if (!this.data) return;

        const stats = this.calculateBackupStats();

        // Actualizar estadísticas
        const elements = {
            'total-respaldos': stats.total,
            'espacio-usado': stats.espacioUsado,
            'ultimo-respaldo': stats.ultimoRespaldo,
            'proximo-respaldo': stats.proximoRespaldo
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });

        // Renderizar gráfico de respaldos
        this.renderBackupsChart(stats);
    }

    calculateBackupStats() {
        if (!this.data || this.data.length === 0) {
            return {
                total: 0,
                espacioUsado: '0 MB',
                ultimoRespaldo: 'Nunca',
                proximoRespaldo: 'No programado'
            };
        }

        const total = this.data.length;
        const espacioTotal = this.data.reduce((sum, backup) => {
            const size = parseFloat(backup.tamaño.replace(/[^\d.]/g, ''));
            return sum + size;
        }, 0);

        const ultimoBackup = this.data.sort((a, b) =>
            new Date(b.created_at) - new Date(a.created_at)
        )[0];

        return {
            total,
            espacioUsado: `${espacioTotal.toFixed(1)} MB`,
            ultimoRespaldo: this.core.formatDateTime(ultimoBackup.created_at),
            proximoRespaldo: 'Próximamente' // Implementar lógica de programación
        };
    }

    renderBackupsChart(stats) {
        const ctx = document.getElementById('backupsChart');
        if (!ctx) return;

        if (this.core.charts.backups) {
            this.core.charts.backups.destroy();
        }

        const typeCount = this.data.reduce((acc, backup) => {
            acc[backup.tipo] = (acc[backup.tipo] || 0) + 1;
            return acc;
        }, {});

        this.core.charts.backups = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(typeCount),
                datasets: [{
                    data: Object.values(typeCount),
                    backgroundColor: [
                        '#722F37',
                        '#ffc107',
                        '#198754',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    showCreateBackupModal() {
        const modalHtml = `
            <div class="modal fade" id="createBackupModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-cloud-arrow-down me-2"></i>
                                Crear Respaldo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="createBackupForm">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Respaldo</label>
                                    <select class="form-select" id="backup-type" required>
                                        <option value="completo">Completo (Base de datos + Archivos)</option>
                                        <option value="database">Solo Base de Datos</option>
                                        <option value="files">Solo Archivos</option>
                                        <option value="config">Solo Configuración</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción (Opcional)</label>
                                    <textarea class="form-control" id="backup-description" rows="3" placeholder="Describe el propósito de este respaldo..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="backup-compress" checked>
                                        <label class="form-check-label" for="backup-compress">
                                            Comprimir respaldo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="backup-encrypt">
                                        <label class="form-check-label" for="backup-encrypt">
                                            Cifrar respaldo
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="adminCore.modules.backups.createBackup()">
                                <i class="bi bi-cloud-arrow-down me-1"></i>
                                Crear Respaldo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remover modal existente
        const existingModal = document.getElementById('createBackupModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('createBackupModal'));
        modal.show();
    }

    async createBackup() {
        const type = document.getElementById('backup-type')?.value;
        const description = document.getElementById('backup-description')?.value;
        const compress = document.getElementById('backup-compress')?.checked;
        const encrypt = document.getElementById('backup-encrypt')?.checked;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/backups', {
                method: 'POST',
                body: JSON.stringify({
                    type,
                    description,
                    compress,
                    encrypt
                })
            });

            if (response.success) {
                this.core.showSuccess('Respaldo creado correctamente: ' + response.data.nombre);

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createBackupModal'));
                modal?.hide();

                // Recargar lista
                this.loadData();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al crear respaldo: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async createAutomaticBackup() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/backups', {
                method: 'POST',
                body: JSON.stringify({
                    type: 'completo',
                    description: 'Respaldo automático del ' + new Date().toLocaleDateString(),
                    compress: true,
                    auto: true
                })
            });

            if (response.success) {
                this.core.showSuccess('Respaldo automático creado correctamente');
                this.loadData();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al crear respaldo automático: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async downloadBackup(backupId) {
        try {
            const response = await this.core.apiCall(`/api/admin/backups/${backupId}/download`);

            if (response.success) {
                // Crear enlace de descarga
                const link = document.createElement('a');
                link.href = response.downloadUrl;
                link.download = response.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                this.core.showSuccess('Descarga iniciada');
            }

        } catch (error) {
            this.core.showError('Error al descargar respaldo: ' + error.message);
        }
    }

    async restoreBackup(backupId) {
        const backup = this.data.find(b => b.id === backupId);
        if (!backup) return;

        const confirmMessage = `¿Está seguro de que desea restaurar el respaldo "${backup.nombre}"?\n\n` +
                             'ADVERTENCIA: Esta acción sobrescribirá los datos actuales del sistema.';

        if (!confirm(confirmMessage)) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall(`/api/admin/backups/${backupId}/restore`, {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Respaldo restaurado correctamente. Recargando página...');

                // Recargar página después de 3 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al restaurar respaldo: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async deleteBackup(backupId) {
        const backup = this.data.find(b => b.id === backupId);
        if (!backup) return;

        if (!confirm(`¿Está seguro de eliminar el respaldo "${backup.nombre}"?`)) return;

        try {
            const response = await this.core.apiCall(`/api/admin/backups/${backupId}`, {
                method: 'DELETE'
            });

            if (response.success) {
                this.core.showSuccess('Respaldo eliminado correctamente');
                this.loadData();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al eliminar respaldo: ' + error.message);
        }
    }

    viewBackupDetails(backupId) {
        const backup = this.data.find(b => b.id === backupId);
        if (!backup) return;

        const modalHtml = `
            <div class="modal fade" id="backupDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-info-circle me-2"></i>
                                Detalles del Respaldo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre:</strong> ${backup.nombre}</p>
                                    <p><strong>Tipo:</strong> <span class="badge bg-${this.getBackupTypeColor(backup.tipo)}">${backup.tipo}</span></p>
                                    <p><strong>Tamaño:</strong> ${backup.tamaño}</p>
                                    <p><strong>Fecha:</strong> ${this.core.formatDateTime(backup.created_at)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Estado:</strong> <span class="badge bg-success">Completo</span></p>
                                    <p><strong>Comprimido:</strong> ${backup.compressed ? 'Sí' : 'No'}</p>
                                    <p><strong>Cifrado:</strong> ${backup.encrypted ? 'Sí' : 'No'}</p>
                                    <p><strong>Checksum:</strong> <code>${backup.checksum || 'N/A'}</code></p>
                                </div>
                            </div>
                            ${backup.descripcion ? `<div class="mt-3"><strong>Descripción:</strong><br>${backup.descripcion}</div>` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-success" onclick="adminCore.modules.backups.downloadBackup('${backup.id}')">
                                <i class="bi bi-download me-1"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById('backupDetailsModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('backupDetailsModal'));
        modal.show();
    }

    showScheduleModal() {
        this.core.showSuccess('Programación de respaldos automáticos - Próximamente');
    }

    showRestoreModal() {
        this.core.showSuccess('Asistente de restauración - Próximamente');
    }

    showConfigModal() {
        this.core.showSuccess('Configuración de respaldos - Próximamente');
    }

    getBackupTypeColor(type) {
        const colors = {
            'completo': 'primary',
            'database': 'success',
            'files': 'info',
            'config': 'warning'
        };
        return colors[type] || 'secondary';
    }
}

// Exportar para uso global
window.AdminBackups = AdminBackups;