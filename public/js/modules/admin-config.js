/**
 * Admin Configuration Module
 * Gestión de configuración del sistema
 */

class AdminConfig {
    constructor(core) {
        this.core = core;
        this.data = null;
        this.isDirty = false;

        this.init();
    }

    init() {
        console.log('Configuration module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Formularios de configuración
        document.getElementById('config-general-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveGeneralConfig();
        });

        document.getElementById('config-comisiones-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveCommissionsConfig();
        });

        document.getElementById('config-notificaciones-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveNotificationsConfig();
        });

        // Botones de acción
        document.getElementById('btn-limpiar-cache')?.addEventListener('click', () => {
            this.clearSystemCache();
        });

        document.getElementById('btn-backup-db')?.addEventListener('click', () => {
            this.createDatabaseBackup();
        });

        document.getElementById('btn-test-email')?.addEventListener('click', () => {
            this.testEmailConfiguration();
        });

        document.getElementById('btn-system-info')?.addEventListener('click', () => {
            this.showSystemInfo();
        });

        // Detectar cambios en formularios
        this.setupFormChangeDetection();
    }

    setupFormChangeDetection() {
        const forms = ['config-general-form', 'config-comisiones-form', 'config-notificaciones-form'];

        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('input', () => {
                    this.isDirty = true;
                    this.showUnsavedChangesWarning();
                });
            }
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/config');

            if (response.success) {
                this.data = response.data;
                this.populateConfigForms(response.data);
                this.renderSystemInfo(response.data.sistema);
                this.core.setCacheData('config', response.data);
            } else {
                throw new Error(response.message || 'Error al cargar configuración');
            }

        } catch (error) {
            console.error('Error loading configuration:', error);
            this.core.showError('Error al cargar la configuración: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    populateConfigForms(data) {
        // Configuración general
        if (data.empresa) {
            this.setFormValue('empresa-nombre', data.empresa.nombre);
            this.setFormValue('empresa-email', data.empresa.email);
            this.setFormValue('empresa-telefono', data.empresa.telefono);
            this.setFormValue('empresa-direccion', data.empresa.direccion);
            this.setFormValue('empresa-ciudad', data.empresa.ciudad);
            this.setFormValue('empresa-pais', data.empresa.pais);
        }

        // Configuración de comisiones
        if (data.comisiones) {
            this.setFormValue('comision-venta', data.comisiones.venta);
            this.setFormValue('comision-referido', data.comisiones.referido);
            this.setFormValue('comision-liderazgo', data.comisiones.liderazgo);
            this.setFormValue('minimo-retiro', data.comisiones.minimo_retiro);
            this.setFormValue('maximo-retiro', data.comisiones.maximo_retiro);
            this.setFormValue('frecuencia-pago', data.comisiones.frecuencia_pago);
        }

        // Configuración de notificaciones
        if (data.notificaciones) {
            this.setFormValue('email-enabled', data.notificaciones.email_enabled);
            this.setFormValue('sms-enabled', data.notificaciones.sms_enabled);
            this.setFormValue('push-enabled', data.notificaciones.push_enabled);
            this.setFormValue('email-pedidos', data.notificaciones.email_pedidos);
            this.setFormValue('email-comisiones', data.notificaciones.email_comisiones);
        }

        this.isDirty = false;
    }

    setFormValue(fieldId, value) {
        const field = document.getElementById(fieldId);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = value;
            } else {
                field.value = value || '';
            }
        }
    }

    getFormValue(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            if (field.type === 'checkbox') {
                return field.checked;
            } else if (field.type === 'number') {
                return parseFloat(field.value) || 0;
            } else {
                return field.value;
            }
        }
        return null;
    }

    async saveGeneralConfig() {
        try {
            const configData = {
                empresa: {
                    nombre: this.getFormValue('empresa-nombre'),
                    email: this.getFormValue('empresa-email'),
                    telefono: this.getFormValue('empresa-telefono'),
                    direccion: this.getFormValue('empresa-direccion'),
                    ciudad: this.getFormValue('empresa-ciudad'),
                    pais: this.getFormValue('empresa-pais')
                }
            };

            const response = await this.core.apiCall('/api/admin/config', {
                method: 'POST',
                body: JSON.stringify(configData)
            });

            if (response.success) {
                this.core.showSuccess('Configuración general guardada correctamente');
                this.isDirty = false;
                this.hideUnsavedChangesWarning();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al guardar configuración general: ' + error.message);
        }
    }

    async saveCommissionsConfig() {
        try {
            const configData = {
                comisiones: {
                    venta: this.getFormValue('comision-venta'),
                    referido: this.getFormValue('comision-referido'),
                    liderazgo: this.getFormValue('comision-liderazgo'),
                    minimo_retiro: this.getFormValue('minimo-retiro'),
                    maximo_retiro: this.getFormValue('maximo-retiro'),
                    frecuencia_pago: this.getFormValue('frecuencia-pago')
                }
            };

            const response = await this.core.apiCall('/api/admin/config', {
                method: 'POST',
                body: JSON.stringify(configData)
            });

            if (response.success) {
                this.core.showSuccess('Configuración de comisiones guardada correctamente');
                this.isDirty = false;
                this.hideUnsavedChangesWarning();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al guardar configuración de comisiones: ' + error.message);
        }
    }

    async saveNotificationsConfig() {
        try {
            const configData = {
                notificaciones: {
                    email_enabled: this.getFormValue('email-enabled'),
                    sms_enabled: this.getFormValue('sms-enabled'),
                    push_enabled: this.getFormValue('push-enabled'),
                    email_pedidos: this.getFormValue('email-pedidos'),
                    email_comisiones: this.getFormValue('email-comisiones')
                }
            };

            const response = await this.core.apiCall('/api/admin/config', {
                method: 'POST',
                body: JSON.stringify(configData)
            });

            if (response.success) {
                this.core.showSuccess('Configuración de notificaciones guardada correctamente');
                this.isDirty = false;
                this.hideUnsavedChangesWarning();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al guardar configuración de notificaciones: ' + error.message);
        }
    }

    async clearSystemCache() {
        if (!confirm('¿Está seguro de que desea limpiar el caché del sistema?')) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/cache/clear', {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Caché del sistema limpiado correctamente');
                // Limpiar también el caché local
                this.core.clearCache();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al limpiar caché: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async createDatabaseBackup() {
        if (!confirm('¿Desea crear un respaldo completo de la base de datos?')) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/backups', {
                method: 'POST',
                body: JSON.stringify({
                    type: 'full',
                    description: 'Respaldo manual desde configuración'
                })
            });

            if (response.success) {
                this.core.showSuccess('Respaldo creado correctamente: ' + response.data.nombre);
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al crear respaldo: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async testEmailConfiguration() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/config/test-email', {
                method: 'POST'
            });

            if (response.success) {
                this.core.showSuccess('Email de prueba enviado correctamente');
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al enviar email de prueba: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    showSystemInfo() {
        const modal = document.getElementById('systemInfoModal');
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            // Crear modal dinámicamente
            this.createSystemInfoModal();
        }
    }

    createSystemInfoModal() {
        const modalHtml = `
            <div class="modal fade" id="systemInfoModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-info-circle me-2"></i>
                                Información del Sistema
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="system-info-content">
                            <!-- Se carga dinámicamente -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.loadSystemInfoContent();

        const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
        modal.show();
    }

    async loadSystemInfoContent() {
        const container = document.getElementById('system-info-content');
        if (!container) return;

        if (this.data && this.data.sistema) {
            this.renderSystemInfoContent(container, this.data.sistema);
        } else {
            container.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';

            try {
                const response = await this.core.apiCall('/api/admin/config/system-info');
                if (response.success) {
                    this.renderSystemInfoContent(container, response.data);
                }
            } catch (error) {
                container.innerHTML = '<div class="alert alert-danger">Error al cargar información del sistema</div>';
            }
        }
    }

    renderSystemInfoContent(container, systemInfo) {
        container.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Aplicación</h6>
                    <table class="table table-sm">
                        <tr><td>Versión:</td><td>${systemInfo.version || 'N/A'}</td></tr>
                        <tr><td>Laravel:</td><td>${systemInfo.laravel_version || 'N/A'}</td></tr>
                        <tr><td>PHP:</td><td>${systemInfo.php_version || 'N/A'}</td></tr>
                        <tr><td>Timezone:</td><td>${systemInfo.timezone || 'N/A'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Base de Datos</h6>
                    <table class="table table-sm">
                        <tr><td>Motor:</td><td>${systemInfo.database || 'N/A'}</td></tr>
                        <tr><td>Caché:</td><td>${systemInfo.cache_driver || 'N/A'}</td></tr>
                        <tr><td>Almacenamiento:</td><td>${systemInfo.storage_driver || 'N/A'}</td></tr>
                        <tr><td>Cola:</td><td>${systemInfo.queue_driver || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <h6>Extensiones PHP</h6>
                    <div class="d-flex flex-wrap">
                        ${(systemInfo.php_extensions || []).map(ext =>
                            `<span class="badge bg-success me-1 mb-1">${ext}</span>`
                        ).join('')}
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Memoria</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar" style="width: ${systemInfo.memory_usage || 0}%"></div>
                    </div>
                    <small>Uso: ${systemInfo.memory_used || 'N/A'} / ${systemInfo.memory_limit || 'N/A'}</small>
                </div>
                <div class="col-md-6">
                    <h6>Espacio en Disco</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-info" style="width: ${systemInfo.disk_usage || 0}%"></div>
                    </div>
                    <small>Uso: ${systemInfo.disk_used || 'N/A'} / ${systemInfo.disk_total || 'N/A'}</small>
                </div>
            </div>
        `;
    }

    renderSystemInfo(systemInfo) {
        const container = document.getElementById('system-info');
        if (!container) return;

        container.innerHTML = `
            <div class="row">
                <div class="col-6">
                    <p class="mb-1"><strong>Versión:</strong> ${systemInfo.version || 'N/A'}</p>
                    <p class="mb-1"><strong>Laravel:</strong> ${systemInfo.laravel_version || 'N/A'}</p>
                    <p class="mb-1"><strong>PHP:</strong> ${systemInfo.php_version || 'N/A'}</p>
                </div>
                <div class="col-6">
                    <p class="mb-1"><strong>DB:</strong> ${systemInfo.database || 'N/A'}</p>
                    <p class="mb-1"><strong>Caché:</strong> ${systemInfo.cache_driver || 'N/A'}</p>
                    <p class="mb-1"><strong>Zona:</strong> ${systemInfo.timezone || 'N/A'}</p>
                </div>
            </div>
            <div class="mt-2">
                <button class="btn btn-outline-info btn-sm" onclick="adminCore.modules.config.showSystemInfo()">
                    <i class="bi bi-info-circle me-1"></i>
                    Ver Detalles
                </button>
            </div>
        `;
    }

    showUnsavedChangesWarning() {
        let warning = document.getElementById('unsaved-warning');
        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'unsaved-warning';
            warning.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            warning.style.cssText = 'top: 80px; right: 20px; z-index: 1060; max-width: 300px;';
            warning.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                Tienes cambios sin guardar
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            document.body.appendChild(warning);
        }
    }

    hideUnsavedChangesWarning() {
        const warning = document.getElementById('unsaved-warning');
        if (warning) {
            warning.remove();
        }
    }

    // Configuraciones específicas
    async updateMaintenanceMode(enabled) {
        try {
            const response = await this.core.apiCall('/api/admin/config/maintenance', {
                method: 'POST',
                body: JSON.stringify({ enabled })
            });

            if (response.success) {
                this.core.showSuccess(`Modo mantenimiento ${enabled ? 'activado' : 'desactivado'}`);
            }
        } catch (error) {
            this.core.showError('Error al cambiar modo mantenimiento: ' + error.message);
        }
    }

    async updateDebugMode(enabled) {
        try {
            const response = await this.core.apiCall('/api/admin/config/debug', {
                method: 'POST',
                body: JSON.stringify({ enabled })
            });

            if (response.success) {
                this.core.showSuccess(`Modo debug ${enabled ? 'activado' : 'desactivado'}`);
            }
        } catch (error) {
            this.core.showError('Error al cambiar modo debug: ' + error.message);
        }
    }

    // Validaciones
    validateConfigForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    resetConfigForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            this.isDirty = false;
            this.hideUnsavedChangesWarning();
        }
    }
}

// Exportar para uso global
window.AdminConfig = AdminConfig;