/**
 * Admin Profile Module
 * Gestión del perfil del administrador
 */

class AdminProfile {
    constructor(core) {
        this.core = core;
        this.data = null;
        this.isDirty = false;
        this.avatarFile = null;

        this.init();
    }

    init() {
        console.log('Profile module initialized');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Formularios
        document.getElementById('profile-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.updateProfile();
        });

        document.getElementById('password-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.updatePassword();
        });

        document.getElementById('avatar-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.updateAvatar();
        });

        // Campos de entrada
        document.querySelectorAll('#profile-form input, #profile-form textarea').forEach(field => {
            field.addEventListener('input', () => {
                this.isDirty = true;
                this.showUnsavedChangesWarning();
            });
        });

        // Avatar upload
        document.getElementById('avatar-input')?.addEventListener('change', (e) => {
            this.handleAvatarUpload(e);
        });

        document.getElementById('btn-remove-avatar')?.addEventListener('click', () => {
            this.removeAvatar();
        });

        // Configuración de cuenta
        document.getElementById('two-factor-toggle')?.addEventListener('change', (e) => {
            this.toggleTwoFactor(e.target.checked);
        });

        document.getElementById('email-notifications')?.addEventListener('change', (e) => {
            this.updateNotificationPreferences();
        });

        // Botones de acción
        document.getElementById('btn-export-data')?.addEventListener('click', () => {
            this.exportUserData();
        });

        document.getElementById('btn-delete-account')?.addEventListener('click', () => {
            this.showDeleteAccountModal();
        });

        document.getElementById('btn-activity-log')?.addEventListener('click', () => {
            this.showActivityLog();
        });
    }

    async loadData() {
        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/profile');

            if (response.success) {
                this.data = response.data;
                this.populateProfileForm(response.data);
                this.renderProfileStats();
                this.renderActivityTimeline();
                this.core.setCacheData('profile', response.data);
            } else {
                throw new Error(response.message || 'Error al cargar perfil');
            }

        } catch (error) {
            console.error('Error loading profile:', error);
            this.core.showError('Error al cargar el perfil: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    populateProfileForm(data) {
        // Información básica
        this.setFormValue('profile-name', data.name);
        this.setFormValue('profile-apellidos', data.apellidos);
        this.setFormValue('profile-email', data.email);
        this.setFormValue('profile-telefono', data.telefono);
        this.setFormValue('profile-bio', data.bio);
        this.setFormValue('profile-ubicacion', data.ubicacion);

        // Avatar
        if (data.avatar) {
            this.updateAvatarPreview(data.avatar);
        }

        // Configuraciones
        this.setFormValue('two-factor-toggle', data.two_factor_enabled);
        this.setFormValue('email-notifications', data.email_notifications);
        this.setFormValue('push-notifications', data.push_notifications);

        // Información del rol
        document.getElementById('profile-rol')?.textContent = this.formatRole(data.rol);
        document.getElementById('profile-fecha-registro')?.textContent = data.created_at;

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
            } else {
                return field.value;
            }
        }
        return null;
    }

    async updateProfile() {
        if (!this.validateProfileForm()) return;

        try {
            this.core.showLoading();

            const profileData = {
                name: this.getFormValue('profile-name'),
                apellidos: this.getFormValue('profile-apellidos'),
                email: this.getFormValue('profile-email'),
                telefono: this.getFormValue('profile-telefono'),
                bio: this.getFormValue('profile-bio'),
                ubicacion: this.getFormValue('profile-ubicacion')
            };

            const response = await this.core.apiCall('/api/admin/profile', {
                method: 'POST',
                body: JSON.stringify(profileData)
            });

            if (response.success) {
                this.core.showSuccess('Perfil actualizado correctamente');
                this.isDirty = false;
                this.hideUnsavedChangesWarning();

                // Actualizar datos en cache
                this.data = { ...this.data, ...profileData };
                this.core.setCacheData('profile', this.data);

                // Actualizar header del usuario
                this.updateUserHeader(profileData);
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al actualizar perfil: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async updatePassword() {
        if (!this.validatePasswordForm()) return;

        try {
            this.core.showLoading();

            const passwordData = {
                current_password: this.getFormValue('current-password'),
                new_password: this.getFormValue('new-password'),
                new_password_confirmation: this.getFormValue('confirm-password')
            };

            const response = await this.core.apiCall('/api/admin/profile/password', {
                method: 'POST',
                body: JSON.stringify(passwordData)
            });

            if (response.success) {
                this.core.showSuccess('Contraseña actualizada correctamente');

                // Limpiar formulario
                document.getElementById('password-form').reset();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Error al actualizar contraseña: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validar archivo
        if (!file.type.startsWith('image/')) {
            this.core.showError('Solo se permiten archivos de imagen');
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            this.core.showError('El archivo no puede superar los 2MB');
            return;
        }

        this.avatarFile = file;

        // Mostrar preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.updateAvatarPreview(e.target.result);
        };
        reader.readAsDataURL(file);

        // Habilitar botón de subida
        document.getElementById('btn-upload-avatar').disabled = false;
    }

    updateAvatarPreview(src) {
        const preview = document.getElementById('avatar-preview');
        const currentAvatar = document.querySelector('.profile-avatar-large');

        if (preview) {
            preview.src = src;
            preview.style.display = 'block';
        }

        if (currentAvatar) {
            currentAvatar.style.backgroundImage = `url(${src})`;
            currentAvatar.innerHTML = ''; // Remover iniciales
        }
    }

    async updateAvatar() {
        if (!this.avatarFile) {
            this.core.showError('Selecciona una imagen primero');
            return;
        }

        try {
            this.core.showLoading();

            const formData = new FormData();
            formData.append('avatar', this.avatarFile);

            const response = await fetch('/api/admin/profile/avatar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.core.csrfToken
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.core.showSuccess('Avatar actualizado correctamente');
                this.avatarFile = null;
                document.getElementById('btn-upload-avatar').disabled = true;
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            this.core.showError('Error al actualizar avatar: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    async removeAvatar() {
        if (!confirm('¿Está seguro de eliminar su avatar?')) return;

        try {
            const response = await this.core.apiCall('/api/admin/profile/avatar', {
                method: 'DELETE'
            });

            if (response.success) {
                this.core.showSuccess('Avatar eliminado correctamente');

                // Resetear preview
                const preview = document.getElementById('avatar-preview');
                const currentAvatar = document.querySelector('.profile-avatar-large');

                if (preview) {
                    preview.style.display = 'none';
                }

                if (currentAvatar) {
                    currentAvatar.style.backgroundImage = '';
                    currentAvatar.innerHTML = this.getInitials(this.data.name);
                }
            }

        } catch (error) {
            this.core.showError('Error al eliminar avatar: ' + error.message);
        }
    }

    async toggleTwoFactor(enabled) {
        try {
            const response = await this.core.apiCall('/api/admin/profile/two-factor', {
                method: 'POST',
                body: JSON.stringify({ enabled })
            });

            if (response.success) {
                this.core.showSuccess(`Autenticación de dos factores ${enabled ? 'activada' : 'desactivada'}`);

                if (enabled && response.qr_code) {
                    this.showTwoFactorSetup(response.qr_code, response.secret);
                }
            }

        } catch (error) {
            this.core.showError('Error al configurar autenticación de dos factores: ' + error.message);
            // Revertir toggle
            document.getElementById('two-factor-toggle').checked = !enabled;
        }
    }

    showTwoFactorSetup(qrCode, secret) {
        const modalHtml = `
            <div class="modal fade" id="twoFactorModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-shield-check me-2"></i>
                                Configurar Autenticación de Dos Factores
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p>Escanea este código QR con tu aplicación de autenticación:</p>
                            <img src="${qrCode}" alt="QR Code" class="img-fluid mb-3">
                            <p><strong>Clave secreta:</strong></p>
                            <code class="bg-light p-2 rounded">${secret}</code>
                            <div class="mt-3">
                                <input type="text" class="form-control" id="verification-code" placeholder="Código de verificación">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="adminCore.modules.profile.verifyTwoFactor()">
                                Verificar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('twoFactorModal'));
        modal.show();
    }

    async verifyTwoFactor() {
        const code = document.getElementById('verification-code').value;

        if (!code) {
            this.core.showError('Ingresa el código de verificación');
            return;
        }

        try {
            const response = await this.core.apiCall('/api/admin/profile/two-factor/verify', {
                method: 'POST',
                body: JSON.stringify({ code })
            });

            if (response.success) {
                this.core.showSuccess('Autenticación de dos factores configurada correctamente');

                const modal = bootstrap.Modal.getInstance(document.getElementById('twoFactorModal'));
                modal?.hide();
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            this.core.showError('Código de verificación incorrecto');
        }
    }

    async updateNotificationPreferences() {
        const preferences = {
            email_notifications: this.getFormValue('email-notifications'),
            push_notifications: this.getFormValue('push-notifications'),
            sms_notifications: this.getFormValue('sms-notifications')
        };

        try {
            const response = await this.core.apiCall('/api/admin/profile/notifications', {
                method: 'POST',
                body: JSON.stringify(preferences)
            });

            if (response.success) {
                this.core.showSuccess('Preferencias de notificación actualizadas');
            }

        } catch (error) {
            this.core.showError('Error al actualizar preferencias: ' + error.message);
        }
    }

    renderProfileStats() {
        const container = document.getElementById('profile-stats');
        if (!container || !this.data) return;

        const stats = {
            sesiones_activas: this.data.active_sessions || 0,
            ultimo_acceso: this.data.last_login || 'Nunca',
            acciones_realizadas: this.data.actions_count || 0,
            tiempo_en_sistema: this.calculateSystemTime()
        };

        container.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-check fs-2 text-success mb-2"></i>
                            <h5>${stats.sesiones_activas}</h5>
                            <small class="text-muted">Sesiones Activas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-clock fs-2 text-info mb-2"></i>
                            <h6 class="small">${stats.ultimo_acceso}</h6>
                            <small class="text-muted">Último Acceso</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-activity fs-2 text-primary mb-2"></i>
                            <h5>${stats.acciones_realizadas}</h5>
                            <small class="text-muted">Acciones Realizadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-calendar-check fs-2 text-warning mb-2"></i>
                            <h6 class="small">${stats.tiempo_en_sistema}</h6>
                            <small class="text-muted">En el Sistema</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    renderActivityTimeline() {
        const container = document.getElementById('activity-timeline');
        if (!container) return;

        const activities = this.data.recent_activities || [];

        if (activities.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clock-history fs-1"></i>
                    <p>No hay actividad reciente</p>
                </div>
            `;
            return;
        }

        container.innerHTML = activities.map(activity => `
            <div class="d-flex mb-3">
                <div class="flex-shrink-0">
                    <div class="bg-${this.getActivityColor(activity.type)} rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 32px; height: 32px;">
                        <i class="bi ${this.getActivityIcon(activity.type)} text-white small"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="fw-medium">${activity.description}</div>
                    <small class="text-muted">${this.core.formatDateTime(activity.created_at)}</small>
                </div>
            </div>
        `).join('');
    }

    async exportUserData() {
        if (!confirm('¿Desea exportar todos sus datos personales?')) return;

        try {
            this.core.showLoading();

            const response = await this.core.apiCall('/api/admin/profile/export');

            if (response.success) {
                const blob = new Blob([JSON.stringify(response.data, null, 2)], {
                    type: 'application/json'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `datos_usuario_${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.core.showSuccess('Datos exportados correctamente');
            }

        } catch (error) {
            this.core.showError('Error al exportar datos: ' + error.message);
        } finally {
            this.core.hideLoading();
        }
    }

    showDeleteAccountModal() {
        this.core.showError('Eliminación de cuenta de administrador no permitida por seguridad');
    }

    showActivityLog() {
        this.core.showSuccess('Log de actividad detallado - Próximamente');
    }

    // Métodos de validación
    validateProfileForm() {
        const requiredFields = ['profile-name', 'profile-email'];
        let isValid = true;

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else if (field) {
                field.classList.remove('is-invalid');
            }
        });

        // Validar email
        const email = this.getFormValue('profile-email');
        if (email && !this.isValidEmail(email)) {
            document.getElementById('profile-email').classList.add('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    validatePasswordForm() {
        const currentPassword = this.getFormValue('current-password');
        const newPassword = this.getFormValue('new-password');
        const confirmPassword = this.getFormValue('confirm-password');

        let isValid = true;

        if (!currentPassword) {
            document.getElementById('current-password').classList.add('is-invalid');
            isValid = false;
        }

        if (!newPassword || newPassword.length < 8) {
            document.getElementById('new-password').classList.add('is-invalid');
            isValid = false;
        }

        if (newPassword !== confirmPassword) {
            document.getElementById('confirm-password').classList.add('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    // Métodos de utilidad
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    formatRole(rol) {
        const roles = {
            'administrador': 'Administrador',
            'lider': 'Líder',
            'vendedor': 'Vendedor'
        };
        return roles[rol] || rol;
    }

    getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    }

    calculateSystemTime() {
        if (!this.data.created_at) return 'N/A';

        const created = new Date(this.data.created_at);
        const now = new Date();
        const diff = now - created;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        if (days < 30) {
            return `${days} días`;
        } else if (days < 365) {
            return `${Math.floor(days / 30)} meses`;
        } else {
            return `${Math.floor(days / 365)} años`;
        }
    }

    getActivityColor(type) {
        const colors = {
            'login': 'success',
            'logout': 'secondary',
            'update': 'primary',
            'create': 'info',
            'delete': 'danger'
        };
        return colors[type] || 'secondary';
    }

    getActivityIcon(type) {
        const icons = {
            'login': 'bi-box-arrow-in-right',
            'logout': 'bi-box-arrow-left',
            'update': 'bi-pencil',
            'create': 'bi-plus',
            'delete': 'bi-trash'
        };
        return icons[type] || 'bi-circle';
    }

    updateUserHeader(profileData) {
        // Actualizar información del usuario en el header
        const nameElement = document.querySelector('.profile-name');
        if (nameElement) {
            nameElement.textContent = profileData.name;
        }
    }

    showUnsavedChangesWarning() {
        // Similar al módulo de configuración
        let warning = document.getElementById('profile-unsaved-warning');
        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'profile-unsaved-warning';
            warning.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            warning.style.cssText = 'top: 80px; right: 20px; z-index: 1060; max-width: 300px;';
            warning.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                Tienes cambios sin guardar en tu perfil
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            document.body.appendChild(warning);
        }
    }

    hideUnsavedChangesWarning() {
        const warning = document.getElementById('profile-unsaved-warning');
        if (warning) {
            warning.remove();
        }
    }
}

// Exportar para uso global
window.AdminProfile = AdminProfile;