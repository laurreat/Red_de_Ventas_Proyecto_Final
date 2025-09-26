/**
 * Funciones JavaScript para el perfil del admin
 * Arepa la Llanerita - Sistema de Ventas
 */

class PerfilAdmin {
    constructor() {
        this.modal = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initModal();
        console.log('✅ PerfilAdmin inicializado correctamente');
    }

    bindEvents() {
        // Event listeners para los botones principales
        document.addEventListener('DOMContentLoaded', () => {
            const descargarBtn = document.getElementById('descargar-datos-btn');
            const actividadBtn = document.getElementById('ver-actividad-btn');
            const eliminarAvatarBtn = document.getElementById('eliminar-avatar-btn');

            if (descargarBtn) {
                descargarBtn.addEventListener('click', () => this.descargarDatos());
            }

            if (actividadBtn) {
                actividadBtn.addEventListener('click', () => this.verActividad());
            }

            if (eliminarAvatarBtn) {
                eliminarAvatarBtn.addEventListener('click', () => this.eliminarAvatar());
            }

            console.log('📎 Event listeners configurados');
        });
    }

    initModal() {
        const modalElement = document.getElementById('activityModal');
        if (modalElement && typeof bootstrap !== 'undefined') {
            this.modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Event listeners para el modal
            modalElement.addEventListener('hidden.bs.modal', () => {
                console.log('🗂️ Modal cerrado');
                this.resetModalContent();
            });

            modalElement.addEventListener('show.bs.modal', () => {
                console.log('🗂️ Modal abierto');
            });
        }
    }

    /**
     * Descargar datos del usuario en formato JSON
     */
    async descargarDatos() {
        const btn = document.getElementById('descargar-datos-btn');
        if (!btn) {
            console.error('❌ Botón de descarga no encontrado');
            return;
        }

        const originalHTML = btn.innerHTML;
        const originalClasses = btn.className;

        try {
            // Mostrar estado de carga
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generando...';
            btn.disabled = true;
            btn.className = originalClasses.replace('btn-outline-info', 'btn-secondary');

            // Crear elemento de descarga
            const a = document.createElement('a');
            a.href = window.routes?.downloadData || '/admin/perfil/download';
            a.download = `mis_datos_arepa_llanerita_${Date.now()}.json`;
            a.style.display = 'none';

            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            // Mostrar éxito
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-check me-1"></i>Descargado';
                btn.className = originalClasses.replace('btn-outline-info', 'btn-success');

                // Volver al estado original
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.className = originalClasses;
                    btn.disabled = false;
                }, 2000);
            }, 500);

            console.log('✅ Descarga iniciada correctamente');

        } catch (error) {
            console.error('❌ Error al descargar:', error);

            btn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Error';
            btn.className = originalClasses.replace('btn-outline-info', 'btn-danger');

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.className = originalClasses;
                btn.disabled = false;
            }, 3000);

            this.showAlert('Error al descargar los datos. Inténtalo de nuevo.', 'error');
        }
    }

    /**
     * Mostrar modal con actividad detallada del usuario
     */
    async verActividad() {
        if (!this.modal) {
            console.error('❌ Modal no inicializado');
            return;
        }

        // Mostrar modal con contenido de carga
        this.showLoadingModal();
        this.modal.show();

        try {
            const response = await fetch(window.routes?.activity || '/admin/perfil/activity');
            const data = await response.json();

            if (data.success) {
                this.renderActivityContent(data.data);
                console.log('✅ Actividad cargada correctamente');
            } else {
                this.showModalError(data.message || 'No se pudo cargar la actividad.');
            }

        } catch (error) {
            console.error('❌ Error al obtener actividad:', error);
            this.showModalError('Error de conexión. Verifica tu conexión e inténtalo de nuevo.');
        }
    }

    /**
     * Eliminar avatar del usuario
     */
    async eliminarAvatar() {
        if (!confirm('¿Estás seguro de eliminar tu avatar?')) {
            return;
        }

        try {
            const response = await fetch(window.routes?.deleteAvatar || '/admin/perfil/avatar', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Avatar eliminado exitosamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showAlert('Error: ' + data.message, 'error');
            }

        } catch (error) {
            console.error('❌ Error al eliminar avatar:', error);
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }

    /**
     * Mostrar modal con contenido de carga
     */
    showLoadingModal() {
        const content = document.getElementById('activityContent');
        if (content) {
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-perfil mb-3"></div>
                    <h6 class="text-muted">Cargando actividad...</h6>
                    <p class="small text-muted">Obteniendo tus datos más recientes</p>
                </div>
            `;
        }
    }

    /**
     * Renderizar contenido de actividad en el modal
     */
    renderActivityContent(data) {
        let html = '<div class="row">';

        // Resumen de estadísticas
        html += this.renderSummaryStats(data.resumen);

        // Pedidos recientes
        if (data.pedidos && data.pedidos.length > 0) {
            html += this.renderRecentOrders(data.pedidos);
        }

        // Referidos recientes
        if (data.usuarios_referidos && data.usuarios_referidos.length > 0) {
            html += this.renderRecentReferrals(data.usuarios_referidos);
        } else {
            html += `
                <div class="col-md-6">
                    <h6><i class="bi bi-people me-2"></i>Referidos Recientes</h6>
                    <div class="text-center py-3">
                        <i class="bi bi-person-plus-fill text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No tienes referidos recientes</p>
                    </div>
                </div>
            `;
        }

        html += '</div>';

        // Agregar botones de acción
        html += `
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-perfil-outline me-2" onclick="perfilAdmin.descargarDatos()">
                        <i class="bi bi-download me-1"></i>Descargar Datos Completos
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        `;

        document.getElementById('activityContent').innerHTML = html;
    }

    renderSummaryStats(resumen) {
        return `
            <div class="col-12 mb-4">
                <h6><i class="bi bi-graph-up me-2"></i>Resumen de Actividad</h6>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <div class="stats-card">
                            <div class="stats-number">${resumen.pedidos_como_cliente || 0}</div>
                            <div class="stats-label">Como Cliente</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="stats-card">
                            <div class="stats-number">${resumen.pedidos_como_vendedor || 0}</div>
                            <div class="stats-label">Como Vendedor</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="stats-card">
                            <div class="stats-number">${resumen.total_referidos || 0}</div>
                            <div class="stats-label">Total Referidos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="stats-card">
                            <div class="stats-number">${resumen.accesos_ultimo_mes || 0}</div>
                            <div class="stats-label">Accesos/Mes</div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Último acceso: ${resumen.ultimo_acceso || 'Desconocido'}
                    </small>
                </div>
            </div>
        `;
    }

    renderRecentOrders(pedidos) {
        let html = `
            <div class="col-md-6">
                <h6><i class="bi bi-cart3 me-2"></i>Pedidos Recientes (Últimos 30 días)</h6>
                <div style="max-height: 300px; overflow-y: auto;">
        `;

        pedidos.slice(0, 10).forEach(pedido => {
            const fecha = new Date(pedido.created_at).toLocaleDateString('es-CO');
            const badgeClass = this.getStatusBadgeClass(pedido.estado);
            const tipoIcon = pedido.tipo === 'vendedor' ? 'bi-person-badge' : 'bi-person';

            html += `
                <div class="activity-item border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>
                                <i class="bi ${tipoIcon} me-1"></i>
                                <strong>${pedido.numero_pedido}</strong> (${pedido.tipo})
                            </small><br>
                            <span class="badge bg-${badgeClass} activity-badge">${pedido.estado}</span>
                            <small class="text-muted ms-2">
                                $${this.formatCurrency(pedido.total_final)}
                            </small>
                        </div>
                        <small class="text-muted">${fecha}</small>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        return html;
    }

    renderRecentReferrals(referidos) {
        let html = `
            <div class="col-md-6">
                <h6><i class="bi bi-people me-2"></i>Referidos Recientes</h6>
                <div style="max-height: 300px; overflow-y: auto;">
        `;

        referidos.slice(0, 10).forEach(usuario => {
            const fecha = new Date(usuario.created_at).toLocaleDateString('es-CO');
            const roleClass = this.getRoleBadgeClass(usuario.rol);
            const statusClass = usuario.activo ? 'success' : 'secondary';

            html += `
                <div class="activity-item border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>
                                <strong>${usuario.name} ${usuario.apellidos}</strong>
                            </small><br>
                            <span class="badge bg-${roleClass} activity-badge">${usuario.rol}</span>
                            <span class="badge bg-${statusClass} activity-badge ms-1">
                                ${usuario.activo ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                        <small class="text-muted">${fecha}</small>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        return html;
    }

    /**
     * Mostrar error en el modal
     */
    showModalError(message) {
        const content = document.getElementById('activityContent');
        if (content) {
            content.innerHTML = `
                <div class="alert alert-perfil-error">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${message}
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-perfil-primary" onclick="perfilAdmin.verActividad()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            `;
        }
    }

    /**
     * Resetear contenido del modal
     */
    resetModalContent() {
        const content = document.getElementById('activityContent');
        if (content) {
            content.innerHTML = `
                <div class="text-center">
                    <div class="spinner-perfil"></div>
                </div>
            `;
        }
    }

    /**
     * Mostrar alerta temporal
     */
    showAlert(message, type = 'info') {
        const alertClass = `alert-perfil-${type}`;
        const iconClass = type === 'success' ? 'bi-check-circle' :
                         type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alert.innerHTML = `
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Auto-remove después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    /**
     * Utilidades
     */
    getStatusBadgeClass(estado) {
        const statusMap = {
            'entregado': 'success',
            'cancelado': 'danger',
            'en_camino': 'info',
            'listo': 'warning',
            'confirmado': 'primary'
        };
        return statusMap[estado] || 'secondary';
    }

    getRoleBadgeClass(rol) {
        const roleMap = {
            'administrador': 'success',
            'lider': 'info',
            'vendedor': 'warning',
            'cliente': 'secondary'
        };
        return roleMap[rol] || 'secondary';
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO').format(parseFloat(amount) || 0);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.perfilAdmin = new PerfilAdmin();
});

// Compatibilidad con las funciones globales existentes
window.descargarDatos = function() {
    if (window.perfilAdmin) {
        window.perfilAdmin.descargarDatos();
    }
};

window.verActividad = function() {
    if (window.perfilAdmin) {
        window.perfilAdmin.verActividad();
    }
};

window.eliminarAvatar = function() {
    if (window.perfilAdmin) {
        window.perfilAdmin.eliminarAvatar();
    }
};