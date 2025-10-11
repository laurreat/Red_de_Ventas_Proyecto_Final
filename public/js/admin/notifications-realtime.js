/**
 * Sistema de Notificaciones en Tiempo Real
 * Polling automático cada 30 segundos
 */
class RealtimeNotifications {
    constructor() {
        this.pollInterval = 30000; // 30 segundos
        this.routes = {
            count: '/admin/notificaciones/contar-no-leidas',
            dropdown: '/admin/notificaciones/dropdown'
        };
        this.timer = null;
        this.lastCount = 0;
        this.init();
    }

    init() {
        // Iniciar polling
        this.startPolling();

        // Actualizar al cargar la página
        this.updateNotifications();

        console.log('✅ Sistema de notificaciones en tiempo real inicializado');
    }

    startPolling() {
        // Limpiar timer anterior si existe
        if (this.timer) {
            clearInterval(this.timer);
        }

        // Iniciar nuevo polling
        this.timer = setInterval(() => {
            this.updateNotifications();
        }, this.pollInterval);

        // Limpiar al salir de la página
        window.addEventListener('beforeunload', () => {
            if (this.timer) {
                clearInterval(this.timer);
            }
        });
    }

    async updateNotifications() {
        try {
            const response = await fetch(this.routes.count, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.warn('Error al obtener contador de notificaciones');
                return;
            }

            const data = await response.json();

            if (data.success && typeof data.count !== 'undefined') {
                this.updateBadge(data.count);

                // Si hay nuevas notificaciones, mostrar toast
                if (data.count > this.lastCount && this.lastCount > 0) {
                    this.showNewNotificationToast(data.count - this.lastCount);
                    // Reproducir sonido si está habilitado
                    this.playNotificationSound();
                }

                this.lastCount = data.count;
            }
        } catch (error) {
            console.error('Error en polling de notificaciones:', error);
        }
    }

    updateBadge(count) {
        // Actualizar badge en navbar
        const badges = document.querySelectorAll('.notification-badge, [data-notification-count]');

        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
                badge.classList.add('pulse-animation');

                // Remover animación después de 1 segundo
                setTimeout(() => {
                    badge.classList.remove('pulse-animation');
                }, 1000);
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        });

        // Actualizar título de la página
        this.updatePageTitle(count);
    }

    updatePageTitle(count) {
        const originalTitle = document.title.replace(/^\(\d+\)\s*/, '');

        if (count > 0) {
            document.title = `(${count}) ${originalTitle}`;
        } else {
            document.title = originalTitle;
        }
    }

    showNewNotificationToast(newCount) {
        // Verificar si existe el sistema de toasts
        if (typeof window.notificacionesManager !== 'undefined' && window.notificacionesManager.showToast) {
            const message = newCount === 1
                ? 'Tienes 1 nueva notificación'
                : `Tienes ${newCount} nuevas notificaciones`;

            window.notificacionesManager.showToast('info', message, 4000);
        } else {
            // Fallback: crear toast simple
            this.createSimpleToast(newCount);
        }
    }

    createSimpleToast(count) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            font-size: 0.938rem;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        `;

        const message = count === 1
            ? '🔔 Tienes 1 nueva notificación'
            : `🔔 Tienes ${count} nuevas notificaciones`;

        toast.textContent = message;
        document.body.appendChild(toast);

        // Auto-remove después de 4 segundos
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    playNotificationSound() {
        // Opcional: reproducir sonido de notificación
        // Se puede activar si el usuario lo permite
        try {
            // const audio = new Audio('/sounds/notification.mp3');
            // audio.volume = 0.3;
            // audio.play().catch(e => console.log('No se pudo reproducir sonido'));
        } catch (e) {
            // Ignorar errores de audio
        }
    }

    // Método para pausar polling (útil cuando el usuario está en la página de notificaciones)
    pause() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    // Método para reanudar polling
    resume() {
        this.startPolling();
    }
}

// Animaciones CSS para toasts (si no existen)
if (!document.getElementById('notification-animations')) {
    const style = document.createElement('style');
    style.id = 'notification-animations';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .pulse-animation {
            animation: pulse 0.6s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }
    `;
    document.head.appendChild(style);
}

// Inicializar automáticamente
document.addEventListener('DOMContentLoaded', () => {
    window.realtimeNotifications = new RealtimeNotifications();
});

// Pausar polling en página de notificaciones
if (window.location.pathname.includes('/notificaciones')) {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.realtimeNotifications) {
            // Pausar durante 5 segundos para no interferir
            window.realtimeNotifications.pause();

            setTimeout(() => {
                if (window.realtimeNotifications) {
                    window.realtimeNotifications.resume();
                }
            }, 5000);
        }
    });
}
