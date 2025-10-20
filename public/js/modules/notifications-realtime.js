/**
 * SISTEMA DE NOTIFICACIONES EN TIEMPO REAL
 * Maneja el polling, actualización y visualización de notificaciones
 */

class NotificationManager {
    constructor(options = {}) {
        this.pollInterval = options.pollInterval || 30000; // 30 segundos por defecto
        this.maxNotifications = options.maxNotifications || 10;
        this.pollTimer = null;
        this.lastCheck = null;
        this.isPolling = false;
        this.notificaciones = [];
        
        this.init();
    }

    init() {
        // Cargar notificaciones iniciales
        this.loadNotifications();
        
        // Iniciar polling
        this.startPolling();
        
        // Event listeners
        this.setupEventListeners();
        
        // Marcar notificación como leída al hacer clic
        this.setupNotificationClickHandlers();
    }

    setupEventListeners() {
        // Botón de marcar todas como leídas
        const btnMarkAll = document.getElementById('btnMarkAllRead');
        if (btnMarkAll) {
            btnMarkAll.addEventListener('click', () => this.markAllAsRead());
        }

        // Limpiar notificaciones antiguas cuando se cierra el dropdown
        document.addEventListener('hidden.bs.dropdown', (e) => {
            if (e.target.id === 'notificationsDropdown') {
                this.cleanupOldNotifications();
            }
        });
    }

    async loadNotifications() {
        try {
            const response = await fetch('/cliente/notificaciones/no-leidas', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.notificaciones = data.notificaciones;
                this.updateUI(data.notificaciones, data.count);
                this.lastCheck = new Date();
            }
        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
        }
    }

    async checkNewNotifications() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        
        try {
            const response = await fetch('/cliente/notificaciones/conteo', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success && data.count > 0) {
                // Si hay nuevas notificaciones, cargarlas todas
                await this.loadNotifications();
            } else if (data.count === 0) {
                // Actualizar contador a 0
                this.updateBadge(0);
            }
        } catch (error) {
            console.error('Error al verificar notificaciones:', error);
        } finally {
            this.isPolling = false;
        }
    }

    updateUI(notificaciones, count) {
        this.updateBadge(count);
        this.renderNotifications(notificaciones);
    }

    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
                
                // Animación de pulso para nuevas notificaciones
                badge.classList.add('pulse-animation');
                setTimeout(() => badge.classList.remove('pulse-animation'), 1000);
            } else {
                badge.style.display = 'none';
            }
        }
    }

    renderNotifications(notificaciones) {
        const container = document.querySelector('.notifications-list');
        if (!container) return;

        if (notificaciones.length === 0) {
            container.innerHTML = `
                <div class="notifications-empty">
                    <i class="bi bi-bell-slash"></i>
                    <h6>No tienes notificaciones</h6>
                    <p>Cuando tengas nuevas notificaciones aparecerán aquí</p>
                </div>
            `;
            return;
        }

        container.innerHTML = notificaciones.slice(0, this.maxNotifications).map(notif => 
            this.renderNotificationItem(notif)
        ).join('');

        // Re-aplicar event handlers
        this.setupNotificationClickHandlers();
    }

    renderNotificationItem(notif) {
        const iconMap = {
            'pedido': { icon: 'bi-box-seam-fill', class: 'pedido' },
            'venta': { icon: 'bi-currency-dollar', class: 'venta' },
            'usuario': { icon: 'bi-person-fill', class: 'usuario' },
            'comision': { icon: 'bi-star-fill', class: 'comision' },
            'sistema': { icon: 'bi-gear-fill', class: 'sistema' }
        };

        const iconData = iconMap[notif.tipo] || { icon: 'bi-bell-fill', class: 'sistema' };
        const timeAgo = this.getTimeAgo(notif.created_at);
        const isUnread = !notif.leida;

        return `
            <a href="#" 
               class="notification-item ${isUnread ? 'unread' : ''}" 
               data-notification-id="${notif._id}"
               onclick="notificationManager.handleNotificationClick(event, '${notif._id}')">
                <div class="notification-content">
                    <div class="notification-icon ${iconData.class}">
                        <i class="bi ${iconData.icon}"></i>
                    </div>
                    <div class="notification-body">
                        <div class="notification-title">${this.escapeHtml(notif.titulo)}</div>
                        <div class="notification-message">${this.escapeHtml(notif.mensaje)}</div>
                        <div class="notification-time">
                            <i class="bi bi-clock"></i> ${timeAgo}
                        </div>
                    </div>
                </div>
                ${isUnread ? '<span class="notification-unread-dot"></span>' : ''}
            </a>
        `;
    }

    async handleNotificationClick(event, notificationId) {
        event.preventDefault();
        
        try {
            const response = await fetch(`/cliente/notificaciones/${notificationId}/marcar-leida`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // Actualizar UI
                const notifElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notifElement) {
                    notifElement.classList.remove('unread');
                    const dot = notifElement.querySelector('.notification-unread-dot');
                    if (dot) dot.remove();
                }
                
                // Recargar conteo
                await this.checkNewNotifications();
                
                // Mostrar mensaje de éxito
                if (typeof showSuccessToast !== 'undefined') {
                    showSuccessToast('Notificación marcada como leída');
                }
            }
        } catch (error) {
            console.error('Error al marcar notificación como leída:', error);
        }
    }

    async markAllAsRead() {
        // Mostrar confirmación con modal glassmorphism
        GlassModal.confirm({
            title: 'Marcar Todas como Leídas',
            message: '¿Estás seguro de que deseas marcar todas las notificaciones como leídas?',
            icon: 'bi-check-all',
            iconColor: '#3b82f6',
            iconBg: 'rgba(59, 130, 246, 0.2)',
            confirmText: 'Sí, marcar todas',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-info',
            onConfirm: async () => {
                try {
                    const response = await fetch('/cliente/notificaciones/marcar-todas-leidas', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Actualizar UI
                        this.updateBadge(0);
                        this.renderNotifications([]);
                        
                        // Mostrar mensaje de éxito con modal glassmorphism
                        GlassModal.success(
                            '¡Listo!',
                            'Todas las notificaciones han sido marcadas como leídas'
                        );
                    } else {
                        GlassModal.error(
                            'Error',
                            data.message || 'No se pudieron marcar las notificaciones como leídas'
                        );
                    }
                } catch (error) {
                    console.error('Error al marcar todas como leídas:', error);
                    GlassModal.error(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor. Por favor, intenta nuevamente.'
                    );
                }
            }
        });
    }

    setupNotificationClickHandlers() {
        // Los handlers se configuran dinámicamente en el HTML con onclick
        // Esto es para compatibilidad y simplicidad
    }

    cleanupOldNotifications() {
        // Opcional: limpiar notificaciones muy antiguas del DOM
        // Este método se puede expandir según necesidades
    }

    startPolling() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
        }

        this.pollTimer = setInterval(() => {
            this.checkNewNotifications();
        }, this.pollInterval);
    }

    stopPolling() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    }

    destroy() {
        this.stopPolling();
        this.notificaciones = [];
    }

    // Utilidades
    getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            año: 31536000,
            mes: 2592000,
            semana: 604800,
            día: 86400,
            hora: 3600,
            minuto: 60
        };

        for (const [name, secondsInInterval] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInInterval);
            if (interval >= 1) {
                return interval === 1 ? `Hace 1 ${name}` : `Hace ${interval} ${name}s`;
            }
        }

        return 'Hace un momento';
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

// Instancia global
let notificationManager = null;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si estamos en una página con notificaciones
    if (document.querySelector('.header-notifications')) {
        notificationManager = new NotificationManager({
            pollInterval: 30000, // Polling cada 30 segundos
            maxNotifications: 10
        });
        
        console.log('Sistema de notificaciones en tiempo real inicializado');
    }
});

// Limpiar al salir de la página
window.addEventListener('beforeunload', function() {
    if (notificationManager) {
        notificationManager.destroy();
    }
});
