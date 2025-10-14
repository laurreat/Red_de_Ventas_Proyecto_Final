@extends('layouts.lider')

@section('title', '- Mi Perfil')
@section('page-title', 'Mi Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/dashboard-modern.css') }}?v={{ filemtime(public_path('css/lider/dashboard-modern.css')) }}">
<style>
.perfil-header{background:linear-gradient(135deg,var(--wine),var(--wine-dark));border-radius:var(--radius-xl);padding:2rem;color:#fff;position:relative;overflow:hidden;margin-bottom:2rem}.perfil-avatar{width:120px;height:120px;border-radius:50%;border:4px solid rgba(255,255,255,.3);background:#fff;display:flex;align-items:center;justify-content:center;font-size:3rem;color:var(--wine);font-weight:700}.perfil-info-card{background:#fff;border-radius:var(--radius);box-shadow:var(--shadow-sm);border:1px solid var(--gray-200);margin-bottom:1.5rem}.perfil-info-header{background:linear-gradient(135deg,var(--wine),var(--wine-dark));color:#fff;padding:1rem 1.5rem;border-radius:var(--radius) var(--radius) 0 0}.perfil-info-body{padding:1.5rem}.perfil-activity-item{display:flex;align-items:start;padding:1rem;border-bottom:1px solid var(--gray-100);transition:background .2s}.perfil-activity-item:hover{background:var(--gray-50)}.perfil-activity-item:last-child{border-bottom:none}.perfil-activity-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}.perfil-notification-item{display:flex;align-items:start;padding:1rem;border-bottom:1px solid var(--gray-100);transition:all .2s;cursor:pointer}.perfil-notification-item:hover{background:var(--gray-50)}.perfil-notification-item.unread{background:var(--info-light)}.perfil-notification-item.unread:hover{background:#bfdbfe}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Profile Header -->
    <div class="perfil-header animate-fade-in-up">
        <div class="row align-items-center">
            <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                <div class="perfil-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
            <div class="col-md">
                <h2 class="mb-2 fw-bold">{{ auth()->user()->name }}</h2>
                <p class="mb-1 opacity-90">
                    <i class="bi bi-envelope me-2"></i>{{ auth()->user()->email }}
                </p>
                <p class="mb-0 opacity-90">
                    <i class="bi bi-shield-check me-2"></i>Rol: <strong>{{ ucfirst(auth()->user()->rol) }}</strong>
                </p>
            </div>
            <div class="col-md-auto text-center text-md-end mt-3 mt-md-0">
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil me-2"></i>Editar Perfil
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Form & Settings -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="perfil-info-card animate-fade-in-up animate-delay-1">
                <div class="perfil-info-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="perfil-info-body">
                    <form method="POST" action="{{ route('lider.perfil.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label fw-semibold">Rol</label>
                                <input type="text" class="form-control bg-light" id="rol" value="{{ ucfirst($user->rol) }}" readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                          id="direccion" name="direccion" rows="3">{{ old('direccion', $user->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3 fw-semibold">
                            <i class="bi bi-key me-2"></i>
                            Cambiar Contraseña
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">Nueva Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-wine">
                                <i class="bi bi-check-lg me-1"></i>Actualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Activity & Notifications -->
        <div class="col-lg-4">
            <!-- Notificaciones en Tiempo Real -->
            <div class="perfil-info-card animate-fade-in-up animate-delay-2">
                <div class="perfil-info-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-bell me-2"></i>
                            Notificaciones
                        </h5>
                        <span class="dashboard-realtime-indicator" style="background: rgba(255,255,255,.2); color: #fff;">
                            <span class="dashboard-realtime-dot" style="background: #fff;"></span>
                            En vivo
                        </span>
                    </div>
                </div>
                <div id="notificacionesLista" style="max-height: 300px; overflow-y: auto;">
                    <!-- Notificaciones se cargan aquí en tiempo real -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-wine" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 small">Cargando notificaciones...</p>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente en Tiempo Real -->
            <div class="perfil-info-card animate-fade-in-up animate-delay-3">
                <div class="perfil-info-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Mi Actividad
                        </h5>
                        <span class="dashboard-realtime-indicator" style="background: rgba(255,255,255,.2); color: #fff;">
                            <span class="dashboard-realtime-dot" style="background: #fff;"></span>
                            En vivo
                        </span>
                    </div>
                </div>
                <div id="actividadLista" style="max-height: 300px; overflow-y: auto;">
                    <!-- Actividad se carga aquí en tiempo real -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-wine" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 small">Cargando actividad...</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de Perfil -->
            <div class="perfil-info-card animate-fade-in-up animate-delay-4">
                <div class="perfil-info-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Mis Estadísticas
                    </h5>
                </div>
                <div class="perfil-info-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="mb-0 text-muted small">Total Ventas</p>
                            <h4 class="mb-0 text-wine fw-bold" id="total-ventas">$0</h4>
                        </div>
                        <div class="dashboard-stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--wine), var(--wine-light));">
                            <i class="bi bi-cart-check text-white"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="mb-0 text-muted small">Total Comisiones</p>
                            <h4 class="mb-0 text-success fw-bold" id="total-comisiones">$0</h4>
                        </div>
                        <div class="dashboard-stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success), #059669);">
                            <i class="bi bi-currency-dollar text-white"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-muted small">Total Referidos</p>
                            <h4 class="mb-0 text-info fw-bold" id="total-referidos">0</h4>
                        </div>
                        <div class="dashboard-stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--info), #2563eb);">
                            <i class="bi bi-people text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/dashboard-modern.js') }}?v={{ filemtime(public_path('js/lider/dashboard-modern.js')) }}"></script>
<script>
// Perfil Manager con Tiempo Real
class PerfilManager {
    constructor() {
        this.notificationInterval = 15000; // 15 segundos
        this.activityInterval = 20000; // 20 segundos
        this.statsInterval = 30000; // 30 segundos
        this.init();
    }

    init() {
        this.loadNotifications();
        this.loadActivity();
        this.loadStats();
        this.startRealTimeUpdates();
    }

    startRealTimeUpdates() {
        setInterval(() => this.loadNotifications(), this.notificationInterval);
        setInterval(() => this.loadActivity(), this.activityInterval);
        setInterval(() => this.loadStats(), this.statsInterval);
    }

    async loadNotifications() {
        try {
            const response = await fetch('/lider/perfil/notificaciones', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            this.renderNotifications(data.notificaciones || []);
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    renderNotifications(notificaciones) {
        const container = document.getElementById('notificacionesLista');
        if (notificaciones.length === 0) {
            container.innerHTML = `<div class="text-center py-4"><i class="bi bi-bell-slash fs-1 text-muted"></i><p class="text-muted mt-2">No hay notificaciones</p></div>`;
            return;
        }

        container.innerHTML = notificaciones.map(notif => `
            <div class="perfil-notification-item ${notif.leida ? '' : 'unread'}" onclick="perfilManager.markAsRead('${notif.id}')">
                <div class="perfil-activity-icon" style="background: linear-gradient(135deg, ${this.getNotifColor(notif.tipo)});">
                    <i class="bi bi-${this.getNotifIcon(notif.tipo)} text-white"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="mb-1 fw-semibold">${notif.titulo}</p>
                    <p class="mb-0 text-muted small">${notif.mensaje}</p>
                    <small class="text-muted">${notif.tiempo}</small>
                </div>
            </div>
        `).join('');
    }

    async loadActivity() {
        try {
            const response = await fetch('/lider/perfil/actividad', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            this.renderActivity(data.actividad || []);
        } catch (error) {
            console.error('Error loading activity:', error);
        }
    }

    renderActivity(actividades) {
        const container = document.getElementById('actividadLista');
        if (actividades.length === 0) {
            container.innerHTML = `<div class="text-center py-4"><i class="bi bi-activity fs-1 text-muted"></i><p class="text-muted mt-2">No hay actividad reciente</p></div>`;
            return;
        }

        container.innerHTML = actividades.map(act => `
            <div class="perfil-activity-item">
                <div class="perfil-activity-icon" style="background: linear-gradient(135deg, ${this.getActivityColor(act.tipo)});">
                    <i class="bi bi-${this.getActivityIcon(act.tipo)} text-white"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="mb-1 fw-semibold">${act.descripcion}</p>
                    <small class="text-muted">${act.tiempo}</small>
                </div>
            </div>
        `).join('');
    }

    async loadStats() {
        try {
            const response = await fetch('/lider/perfil/estadisticas', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            this.updateStats(data.estadisticas || {});
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    updateStats(stats) {
        if (stats.total_ventas !== undefined) {
            document.getElementById('total-ventas').textContent = '$' + stats.total_ventas.toLocaleString('es-CO');
        }
        if (stats.total_comisiones !== undefined) {
            document.getElementById('total-comisiones').textContent = '$' + stats.total_comisiones.toLocaleString('es-CO');
        }
        if (stats.total_referidos !== undefined) {
            document.getElementById('total-referidos').textContent = stats.total_referidos;
        }
    }

    async markAsRead(notifId) {
        try {
            await fetch(`/lider/perfil/notificaciones/${notifId}/leer`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            this.loadNotifications();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    getNotifColor(tipo) {
        const colors = {
            info: 'var(--info), #2563eb',
            success: 'var(--success), #059669',
            warning: 'var(--warning), #d97706',
            danger: 'var(--danger), #dc2626'
        };
        return colors[tipo] || colors.info;
    }

    getNotifIcon(tipo) {
        const icons = {
            info: 'info-circle',
            success: 'check-circle',
            warning: 'exclamation-triangle',
            danger: 'x-circle'
        };
        return icons[tipo] || 'bell';
    }

    getActivityColor(tipo) {
        const colors = {
            venta: 'var(--wine), var(--wine-light)',
            referido: 'var(--success), #059669',
            comision: 'var(--warning), #d97706',
            perfil: 'var(--info), #2563eb'
        };
        return colors[tipo] || colors.perfil;
    }

    getActivityIcon(tipo) {
        const icons = {
            venta: 'cart-check',
            referido: 'person-plus',
            comision: 'currency-dollar',
            perfil: 'person-circle'
        };
        return icons[tipo] || 'clock-history';
    }
}

let perfilManager;
document.addEventListener('DOMContentLoaded', () => {
    perfilManager = new PerfilManager();
});
</script>
@endpush
