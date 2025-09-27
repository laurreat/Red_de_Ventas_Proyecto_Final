@extends('layouts.admin')

@section('title', 'Mi Perfil')

@push('styles')
    <link href="{{ asset('css/admin/perfil.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Mi Perfil</h2>
                    <p class="text-muted mb-0">Gestiona tu información personal y configuración</p>
                </div>
                <div>
                    <button class="btn btn-perfil btn-perfil-outline me-2" id="descargar-datos-btn">
                        <i class="bi bi-download me-1"></i>
                        Descargar Datos
                    </button>
                    <button class="btn btn-perfil btn-perfil-primary" id="ver-actividad-btn">
                        <i class="bi bi-activity me-1"></i>
                        Ver Actividad
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Información Personal -->
        <div class="col-xl-8">
            <div class="card perfil-card mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-person me-2"></i>
                        Información Personal
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editProfile">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.perfil.update') }}" enctype="multipart/form-data"
                          class="needs-profile-confirmation"
                          data-confirm-message="¿Estás seguro de actualizar tu información personal? Los cambios se aplicarán a tu perfil."
                          id="updateProfileForm">
                        @csrf
                        <div class="row">
                            <!-- Avatar -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="position-relative d-inline-block" id="avatar-container">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                                             class="rounded-circle border border-3 border-light shadow"
                                             width="150" height="150"
                                             style="object-fit: cover;" alt="Avatar" id="user-avatar">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-3 border-light shadow"
                                             style="width: 150px; height: 150px; font-size: 3rem;" id="user-avatar-placeholder">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    @if($user->avatar)
                                        <button type="button" class="btn btn-sm btn-danger position-absolute rounded-circle"
                                                style="top: 5px; right: 5px; width: 32px; height: 32px; padding: 0;"
                                                id="eliminar-avatar-btn" title="Eliminar foto">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <input type="file" name="avatar" class="form-control" accept="image/*">
                                    <small class="text-muted">JPG, PNG, GIF - Máx. 2MB</small>
                                </div>
                            </div>

                            <!-- Datos Personales -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Nombre *</label>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Apellidos</label>
                                            <input type="text" name="apellidos" class="form-control"
                                                   value="{{ $user->apellidos }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Email *</label>
                                            <input type="email" name="email" class="form-control"
                                                   value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Teléfono</label>
                                            <input type="tel" name="telefono" class="form-control"
                                                   value="{{ $user->telefono }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Dirección</label>
                                            <textarea name="direccion" class="form-control" rows="2">{{ $user->direccion }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: black;">Fecha de Nacimiento</label>
                                            <input type="date" name="fecha_nacimiento" class="form-control"
                                                   value="{{ $user->fecha_nacimiento ? $user->fecha_nacimiento->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Biografía</label>
                                    <textarea name="bio" class="form-control" rows="3"
                                              placeholder="Cuéntanos algo sobre ti...">{{ $user->bio }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="editProfile">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#editProfile">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-shield-lock me-2"></i>
                        Seguridad
                    </h5>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#changePassword">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="changePassword">
                        <form method="POST" action="{{ route('admin.perfil.update-password') }}"
                              class="needs-profile-confirmation"
                              data-confirm-message="¿Estás seguro de cambiar tu contraseña? Esta acción es irreversible."
                              id="updatePasswordForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: black;">Contraseña Actual *</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: black;">Nueva Contraseña *</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                        <small class="text-muted">Mínimo 8 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: black;">Confirmar Contraseña *</label>
                                        <input type="password" name="new_password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-shield-check"></i> Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p style="color: black;">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                Tu contraseña fue actualizada por última vez el {{ $user->updated_at->format('d/m/Y') }}.
                                Se recomienda cambiarla cada 90 días.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-xl-4">
            <!-- Estadísticas del Usuario -->
            <div class="card perfil-card mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Mis Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['pedidos_como_cliente'] }}</div>
                                <div class="stats-label">Pedidos como Cliente</div>
                            </div>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['pedidos_como_vendedor'] }}</div>
                                <div class="stats-label">Pedidos como Vendedor</div>
                            </div>
                        </div>
                        <div class="col-12 text-center mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['total_referidos'] }}</div>
                                <div class="stats-label">Total Referidos</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong style="color: black;">Información de Cuenta</strong>
                                </div>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="color: black;">Rol:</span>
                                        <span class="badge bg-primary">{{ ucfirst($stats['rol_actual']) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="color: black;">Estado:</span>
                                        <span class="badge bg-success">{{ $stats['estado_cuenta'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="color: black;">Registro:</span>
                                        <span style="color: black;">{{ $stats['fecha_registro']->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span style="color: black;">Último acceso:</span>
                                        <span style="color: black;">{{ $stats['ultimo_acceso']->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="card perfil-card mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bell me-2"></i>
                        Notificaciones
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#notifConfig">
                        <i class="bi bi-gear"></i>
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.perfil.update-notifications') }}"
                          class="needs-profile-confirmation"
                          data-confirm-message="¿Estás seguro de actualizar tus preferencias de notificaciones?"
                          id="updateNotificationsForm">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_pedidos"
                                   {{ $notificaciones['email_pedidos'] ? 'checked' : '' }}>
                            <label class="form-check-label" style="color: black;">
                                Notificaciones de pedidos por email
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_usuarios"
                                   {{ $notificaciones['email_usuarios'] ? 'checked' : '' }}>
                            <label class="form-check-label" style="color: black;">
                                Notificaciones de usuarios nuevos
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_sistema"
                                   {{ $notificaciones['email_sistema'] ? 'checked' : '' }}>
                            <label class="form-check-label" style="color: black;">
                                Notificaciones del sistema
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="sms_urgente"
                                   {{ $notificaciones['sms_urgente'] ? 'checked' : '' }}>
                            <label class="form-check-label" style="color: black;">
                                SMS para alertas urgentes
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="push_browser"
                                   {{ $notificaciones['push_browser'] ? 'checked' : '' }}>
                            <label class="form-check-label" style="color: black;">
                                Notificaciones push del navegador
                            </label>
                        </div>

                        <div class="collapse" id="notifConfig">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check"></i> Guardar Preferencias
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="card perfil-card">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-clock-history me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    @if($actividadReciente['pedidos_recientes']->count() > 0)
                        <h6 style="color: black;">Últimos Pedidos</h6>
                        @foreach($actividadReciente['pedidos_recientes'] as $pedido)
                        <div class="activity-item d-flex justify-content-between align-items-center">
                            <div>
                                <small style="color: black;">
                                    {{ $pedido->numero_pedido ?? 'Pedido #' . substr((string)$pedido->_id, -6) }}
                                </small><br>
                                <span class="badge activity-badge bg-{{ $pedido->estado == 'entregado' ? 'success' : ($pedido->estado == 'cancelado' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($pedido->estado ?? 'pendiente') }}
                                </span>
                                @if($pedido->total_final)
                                    <small class="text-muted ms-2">${{ number_format($pedido->total_final, 0) }}</small>
                                @endif
                            </div>
                            <small class="text-muted">{{ $pedido->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    @endif

                    @if($actividadReciente['usuarios_recientes']->count() > 0)
                        <h6 style="color: black;" class="mt-3">Mis Referidos Recientes</h6>
                        @foreach($actividadReciente['usuarios_recientes'] as $usuario)
                        <div class="activity-item d-flex justify-content-between align-items-center">
                            <div>
                                <small style="color: black;">{{ $usuario->name }} {{ $usuario->apellidos }}</small><br>
                                @php
                                    $roleColor = [
                                        'administrador' => 'success',
                                        'lider' => 'info',
                                        'vendedor' => 'warning',
                                        'cliente' => 'secondary'
                                    ][$usuario->rol] ?? 'secondary';
                                @endphp
                                <span class="badge activity-badge bg-{{ $roleColor }}">{{ ucfirst($usuario->rol ?? 'cliente') }}</span>
                                <span class="badge activity-badge bg-{{ $usuario->activo ? 'success' : 'danger' }} ms-1">
                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $usuario->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    @endif

                    @if($actividadReciente['pedidos_recientes']->count() == 0 && $actividadReciente['usuarios_recientes']->count() == 0)
                        <p class="text-muted text-center">No hay actividad reciente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Actividad -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0" style="background: linear-gradient(135deg, #722f37 0%, #8b3c44 100%); color: white;">
                <div>
                    <h5 class="modal-title text-white" id="activityModalLabel">
                        <i class="bi bi-activity me-2"></i>Mi Actividad Detallada
                    </h5>
                    <p class="mb-0 small text-white-50">Últimos 30 días de actividad en el sistema</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="activityContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <h6 class="text-muted">Cargando actividad...</h6>
                    <p class="small text-muted">Obteniendo tus datos más recientes</p>
                </div>
            </div>
            <div class="modal-footer border-top-0" style="background-color: #f8f9fa;">
                <small class="text-muted me-auto">
                    <i class="bi bi-info-circle me-1"></i>
                    Los datos se actualizan en tiempo real
                </small>
                <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Perfil JS cargado');

    // Función para descargar datos
    function descargarDatos() {
        const btn = document.getElementById('descargar-datos-btn');
        if (!btn) return;

        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generando...';
        btn.disabled = true;

        // Crear link de descarga
        const a = document.createElement('a');
        a.href = '{{ route("admin.perfil.download-data") }}';
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        // Feedback visual
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-check me-1"></i>¡Descargado!';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }, 2000);
        }, 1000);
    }

    // Función para ver actividad
    function verActividad() {
        console.log('📊 Abriendo modal de actividad');

        const modalElement = document.getElementById('activityModal');
        if (!modalElement) {
            console.error('❌ Modal no encontrado');
            return;
        }

        // Limpiar modal instance previo
        const existingModal = bootstrap.Modal.getInstance(modalElement);
        if (existingModal) {
            existingModal.dispose();
        }

        // Crear nueva instancia del modal
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: false, // Deshabilitamos el backdrop de Bootstrap para usar el nuestro
            keyboard: true,
            focus: true
        });

        // Cargar contenido inicial
        document.getElementById('activityContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3"></div>
                <h6>Cargando actividad...</h6>
                <p class="small text-muted">Por favor espera...</p>
            </div>
        `;

        // Aplicar glassmorphism y z-index
        modalElement.style.zIndex = '1055';
        modalElement.classList.add('fade');

        // Mostrar modal con animación
        modal.show();

        // Asegurar que el modal esté en primer plano con efecto glassmorphism
        modalElement.addEventListener('shown.bs.modal', function() {
            document.body.classList.add('modal-open');
            modalElement.style.background = 'rgba(0, 0, 0, 0.4)';
        }, { once: true });

        // Fetch data después de mostrar el modal
        setTimeout(() => {
            fetch('{{ route("admin.perfil.activity") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarActividad(data.data);
                    } else {
                        mostrarError(data.message || 'Error al cargar actividad');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error de conexión: ' + error.message);
                });
        }, 100);

        // Event listener para cerrar el modal
        modalElement.addEventListener('hidden.bs.modal', function () {
            document.getElementById('activityContent').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <h6 class="text-muted">Cargando actividad...</h6>
                </div>
            `;
            // Limpiar efectos glassmorphism
            modalElement.style.background = '';
            document.body.classList.remove('modal-open');
            // Remover backdrop si existe
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            // Restaurar overflow del body
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, { once: true });
    }

    // Función para mostrar actividad
    function mostrarActividad(data) {
        let html = '<div class="row">';

        // Resumen
        html += '<div class="col-12 mb-4">';
        html += '<h6><i class="bi bi-graph-up me-2"></i>Resumen de Actividad</h6>';
        html += '<div class="row">';
        html += `<div class="col-md-3 text-center mb-2">
                    <div class="border rounded p-3">
                        <h4 class="text-primary">${data.resumen?.pedidos_como_cliente || 0}</h4>
                        <small class="text-muted">Como Cliente</small>
                    </div>
                 </div>`;
        html += `<div class="col-md-3 text-center mb-2">
                    <div class="border rounded p-3">
                        <h4 class="text-primary">${data.resumen?.pedidos_como_vendedor || 0}</h4>
                        <small class="text-muted">Como Vendedor</small>
                    </div>
                 </div>`;
        html += `<div class="col-md-3 text-center mb-2">
                    <div class="border rounded p-3">
                        <h4 class="text-primary">${data.resumen?.total_referidos || 0}</h4>
                        <small class="text-muted">Referidos</small>
                    </div>
                 </div>`;
        html += `<div class="col-md-3 text-center mb-2">
                    <div class="border rounded p-3">
                        <h4 class="text-primary">${data.resumen?.accesos_ultimo_mes || 0}</h4>
                        <small class="text-muted">Accesos/Mes</small>
                    </div>
                 </div>`;
        html += '</div></div>';

        // Pedidos recientes
        if (data.pedidos && data.pedidos.length > 0) {
            html += '<div class="col-md-6">';
            html += '<h6><i class="bi bi-cart me-2"></i>Pedidos Recientes</h6>';
            data.pedidos.slice(0, 5).forEach(pedido => {
                const fecha = new Date(pedido.created_at).toLocaleDateString('es-CO');
                const badgeClass = pedido.estado === 'entregado' ? 'success' :
                                 pedido.estado === 'cancelado' ? 'danger' : 'warning';
                html += `<div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <small><strong>${pedido.numero_pedido}</strong></small><br>
                                <span class="badge bg-${badgeClass}">${pedido.estado}</span>
                                <small class="text-muted ms-2">${pedido.tipo}</small>
                            </div>
                            <small class="text-muted">${fecha}</small>
                         </div>`;
            });
            html += '</div>';
        }

        // Referidos
        if (data.usuarios_referidos && data.usuarios_referidos.length > 0) {
            html += '<div class="col-md-6">';
            html += '<h6><i class="bi bi-people me-2"></i>Referidos Recientes</h6>';
            data.usuarios_referidos.slice(0, 5).forEach(usuario => {
                const fecha = new Date(usuario.created_at).toLocaleDateString('es-CO');
                html += `<div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <small><strong>${usuario.name} ${usuario.apellidos}</strong></small><br>
                                <span class="badge bg-info">${usuario.rol}</span>
                            </div>
                            <small class="text-muted">${fecha}</small>
                         </div>`;
            });
            html += '</div>';
        } else {
            html += '<div class="col-md-6">';
            html += '<h6><i class="bi bi-people me-2"></i>Referidos</h6>';
            html += '<p class="text-muted text-center">No tienes referidos recientes</p>';
            html += '</div>';
        }

        html += '</div>';
        document.getElementById('activityContent').innerHTML = html;
    }

    // Función para mostrar errores
    function mostrarError(mensaje) {
        document.getElementById('activityContent').innerHTML = `
            <div class="text-center py-4">
                <div class="alert alert-danger d-inline-block">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${mensaje}
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary me-2" onclick="verActividad()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        `;
    }

    // Función para cerrar modal
    function cerrarModal() {
        const modalElement = document.getElementById('activityModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        // Forzar limpieza
        setTimeout(() => {
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 150);
    }

    // Event listeners
    const descargarBtn = document.getElementById('descargar-datos-btn');
    const actividadBtn = document.getElementById('ver-actividad-btn');

    if (descargarBtn) {
        descargarBtn.addEventListener('click', descargarDatos);
        console.log('✅ Botón descargar conectado');
    }

    if (actividadBtn) {
        actividadBtn.addEventListener('click', verActividad);
        console.log('✅ Botón actividad conectado');
    }

    console.log('✅ Todos los event listeners configurados');

    // Asegurar que el modal se pueda cerrar siempre
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('activityModal');
            if (modal && modal.classList.contains('show')) {
                cerrarModal();
            }
        }
    });

    // Función global para cerrar modal (disponible desde cualquier lugar)
    window.cerrarModal = cerrarModal;

    // Funcionalidad para eliminar avatar
    const eliminarAvatarBtn = document.getElementById('eliminar-avatar-btn');
    if (eliminarAvatarBtn) {
        eliminarAvatarBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?')) {
                eliminarAvatar();
            }
        });
    }

    function eliminarAvatar() {
        const btn = document.getElementById('eliminar-avatar-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        }

        fetch('{{ route("admin.perfil.delete-avatar") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la interfaz
                const avatarContainer = document.getElementById('avatar-container');
                const userName = '{{ strtoupper(substr($user->name, 0, 1)) }}';

                avatarContainer.innerHTML = `
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-3 border-light shadow"
                         style="width: 150px; height: 150px; font-size: 3rem;" id="user-avatar-placeholder">
                        ${userName}
                    </div>
                `;

                // Mostrar mensaje de éxito
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const container = document.querySelector('.container-fluid .row').querySelector('.col-12');
                container.appendChild(alertDiv);

                // Auto-remover el mensaje después de 5 segundos
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);

            } else {
                // Mostrar error
                alert('Error: ' + (data.message || 'Error al eliminar la foto'));

                // Restaurar botón
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-trash3"></i>';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al eliminar la foto');

            // Restaurar botón
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash3"></i>';
            }
        });
    }

    // Preview de imagen al seleccionar archivo
    const avatarInput = document.querySelector('input[name="avatar"]');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo es muy grande. Máximo 2MB permitido.');
                    e.target.value = '';
                    return;
                }

                // Validar tipo
                if (!file.type.match('image.*')) {
                    alert('Por favor selecciona un archivo de imagen válido.');
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarContainer = document.getElementById('avatar-container');
                    const currentAvatar = document.getElementById('user-avatar') || document.getElementById('user-avatar-placeholder');

                    if (currentAvatar) {
                        // Si era un placeholder DIV, crear nueva imagen
                        if (currentAvatar.tagName === 'DIV') {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'rounded-circle border border-3 border-light shadow';
                            img.style.width = '150px';
                            img.style.height = '150px';
                            img.style.objectFit = 'cover';
                            img.alt = 'Avatar Preview';
                            img.id = 'user-avatar';

                            currentAvatar.replaceWith(img);

                            // Remover botón de eliminar si existe
                            const deleteBtn = document.getElementById('eliminar-avatar-btn');
                            if (deleteBtn) {
                                deleteBtn.remove();
                            }
                        } else {
                            // Si ya era imagen, solo cambiar src
                            currentAvatar.src = e.target.result;
                            currentAvatar.alt = 'Avatar Preview';
                        }

                        // Mostrar mensaje de preview
                        const previewMsg = document.createElement('small');
                        previewMsg.className = 'text-info d-block mt-2';
                        previewMsg.id = 'preview-message';
                        previewMsg.innerHTML = '<i class="bi bi-info-circle me-1"></i>Vista previa - Guarda los cambios para confirmar';

                        // Remover mensaje anterior si existe
                        const existingMsg = document.getElementById('preview-message');
                        if (existingMsg) {
                            existingMsg.remove();
                        }

                        avatarContainer.parentNode.appendChild(previewMsg);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Funciones para modales de confirmación en perfil - FORZAR SOBRESCRITURA
    setTimeout(function() {
        console.log('🔧 Inicializando funciones para perfil con prioridad (después de app.js)...');

        // Interceptar formularios que necesitan confirmación
        const formsNeedingConfirmation = document.querySelectorAll('form.needs-profile-confirmation');
        console.log('📋 Formularios encontrados:', formsNeedingConfirmation.length);

        formsNeedingConfirmation.forEach((form, index) => {
            console.log(`📝 Configurando formulario ${index + 1}:`, form.id);

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('🛑 Formulario interceptado:', this.id);

                const message = this.dataset.confirmMessage || 'Los cambios se aplicarán a tu perfil.';
                const formId = this.id;

                // Determinar qué modal mostrar según el formulario
                if (formId.includes('Password')) {
                    console.log('🔑 Mostrando modal de contraseña');
                    confirmPasswordChange(formId, message);
                } else if (formId.includes('Notifications')) {
                    console.log('🔔 Mostrando modal de notificaciones');
                    confirmNotificationsUpdate(formId, message);
                } else {
                    console.log('👤 Mostrando modal de información personal');
                    confirmProfileInfoUpdate(formId, message);
                }
            });
        });

    // Función para confirmar actualización de información personal
    window.confirmProfileInfoUpdate = function(formId, message = 'Los cambios se aplicarán a tu información personal.') {
        console.log('confirmProfileInfoUpdate ejecutada para:', formId);

        // Actualizar contenido del modal
        const messageEl = document.getElementById('profileInfoMessage');
        if (messageEl) messageEl.textContent = message;

        // Configurar botón de confirmación
        const confirmBtn = document.getElementById('confirmProfileInfoBtn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            };
        }

        // Mostrar modal
        const modalElement = document.getElementById('profileInfoConfirmModal');
        if (modalElement) {
            console.log('Mostrando modal de actualización de información personal');
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');

            // Crear backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    };

    // Función para confirmar cambio de contraseña
    window.confirmPasswordChange = function(formId, message = 'Esta acción es irreversible. Asegúrate de recordar tu nueva contraseña.') {
        console.log('confirmPasswordChange ejecutada para:', formId);

        // Actualizar contenido del modal
        const messageEl = document.getElementById('passwordChangeMessage');
        if (messageEl) messageEl.textContent = message;

        // Configurar botón de confirmación
        const confirmBtn = document.getElementById('confirmPasswordChangeBtn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            };
        }

        // Mostrar modal
        const modalElement = document.getElementById('passwordChangeConfirmModal');
        if (modalElement) {
            console.log('Mostrando modal de cambio de contraseña');
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');

            // Crear backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    };

    // Función para confirmar actualización de notificaciones
    window.confirmNotificationsUpdate = function(formId, message = 'Se aplicarán las nuevas preferencias de notificación.') {
        console.log('confirmNotificationsUpdate ejecutada para:', formId);

        // Actualizar contenido del modal
        const messageEl = document.getElementById('notificationsMessage');
        if (messageEl) messageEl.textContent = message;

        // Configurar botón de confirmación
        const confirmBtn = document.getElementById('confirmNotificationsBtn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            };
        }

        // Mostrar modal
        const modalElement = document.getElementById('notificationsConfirmModal');
        if (modalElement) {
            console.log('Mostrando modal de actualización de notificaciones');
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');

            // Crear backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    };

    // Función para cerrar modales
    window.closeProfileModal = function(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            document.body.classList.remove('modal-open');

            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    };

    // Event listeners para cerrar modales
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) closeProfileModal(modal.id);
        });
    });

    // Cerrar con backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const openModal = document.querySelector('.modal.show');
            if (openModal) closeProfileModal(openModal.id);
        }
    });

        console.log('✅ Funciones de perfil inicializadas correctamente con prioridad');
    }, 1500); // Aumentar timeout para ejecutar después de app.js

});
</script>

{{-- Incluir modales específicos para perfil --}}
@include('admin.partials.modals-profile')

@endpush