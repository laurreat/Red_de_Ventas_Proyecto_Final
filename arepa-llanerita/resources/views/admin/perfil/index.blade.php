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
{{-- Variables globales para los módulos de perfil --}}
<script>
window.perfilRoutes = {
    activity: '{{ route("admin.perfil.activity") }}',
    downloadData: '{{ route("admin.perfil.download-data") }}',
    deleteAvatar: '{{ route("admin.perfil.delete-avatar") }}'
};
window.perfilCSRF = '{{ csrf_token() }}';
window.perfilUserInitial = '{{ strtoupper(substr($user->name, 0, 1)) }}';
</script>

{{-- Módulos de funcionalidad de perfil - Orden importante --}}
<script src="{{ asset('js/admin/perfil-download.js') }}"></script>
<script src="{{ asset('js/admin/perfil-avatar.js') }}"></script>
<script src="{{ asset('js/admin/perfil-activity.js') }}"></script>
<script src="{{ asset('js/admin/perfil-forms.js') }}"></script>

{{-- Incluir modales específicos para perfil --}}
@include('admin.partials.modals-profile')

@endpush