@extends('layouts.admin')

@section('title', 'Mi Perfil')

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
                    <button class="btn btn-outline-info me-2" onclick="descargarDatos()">
                        <i class="bi bi-download me-1"></i>
                        Descargar Datos
                    </button>
                    <button class="btn btn-primary" onclick="verActividad()">
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
            <div class="card border-0 shadow-sm mb-4">
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
                    <form method="POST" action="{{ route('admin.perfil.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Avatar -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                                             class="rounded-circle" width="150" height="150"
                                             style="object-fit: cover;" alt="Avatar">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 150px; height: 150px; font-size: 3rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    @if($user->avatar)
                                        <button type="button" class="btn btn-sm btn-danger position-absolute"
                                                style="top: 0; right: 0;" onclick="eliminarAvatar()">
                                            <i class="bi bi-x"></i>
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
                        <form method="POST" action="{{ route('admin.perfil.update-password') }}">
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-graph-up me-2"></i>
                        Mis Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 style="color: black;">{{ $stats['pedidos_gestionados'] }}</h4>
                                <small class="text-muted">Pedidos Gestionados</small>
                            </div>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <div class="border rounded p-3">
                                <h4 style="color: black;">{{ $stats['usuarios_creados'] }}</h4>
                                <small class="text-muted">Usuarios Creados</small>
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
            <div class="card border-0 shadow-sm mb-4">
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
                    <form method="POST" action="{{ route('admin.perfil.update-notifications') }}">
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-clock-history me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    @if($actividadReciente['pedidos_recientes']->count() > 0)
                        <h6 style="color: black;">Últimos Pedidos Gestionados</h6>
                        @foreach($actividadReciente['pedidos_recientes'] as $pedido)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <small style="color: black;">Pedido #{{ $pedido->_id }}</small><br>
                                <span class="badge bg-{{ $pedido->estado == 'entregado' ? 'success' : 'warning' }}">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $pedido->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    @endif

                    @if($actividadReciente['usuarios_recientes']->count() > 0)
                        <h6 style="color: black;" class="mt-3">Usuarios Creados Recientemente</h6>
                        @foreach($actividadReciente['usuarios_recientes'] as $usuario)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <small style="color: black;">{{ $usuario->name }}</small><br>
                                <span class="badge bg-info">{{ ucfirst($usuario->rol) }}</span>
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
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mi Actividad Detallada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function eliminarAvatar() {
    if(confirm('¿Estás seguro de eliminar tu avatar?')) {
        fetch('{{ route("admin.perfil.delete-avatar") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Avatar eliminado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

function descargarDatos() {
    window.location.href = '{{ route("admin.perfil.download-data") }}';
}

function verActividad() {
    fetch('{{ route("admin.perfil.activity") }}')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let html = '<div class="row">';

                // Resumen
                html += '<div class="col-12 mb-4">';
                html += '<h6>Resumen de Actividad</h6>';
                html += '<div class="row">';
                html += `<div class="col-md-4 text-center">
                            <div class="border rounded p-2">
                                <h5>${data.data.resumen.accesos_ultimo_mes}</h5>
                                <small>Accesos último mes</small>
                            </div>
                         </div>`;
                html += `<div class="col-md-4 text-center">
                            <div class="border rounded p-2">
                                <h5>${data.data.resumen.promedio_accesos_diarios}</h5>
                                <small>Promedio diario</small>
                            </div>
                         </div>`;
                html += `<div class="col-md-4 text-center">
                            <div class="border rounded p-2">
                                <h5>${data.data.resumen.tiempo_sesion_promedio}</h5>
                                <small>Tiempo promedio</small>
                            </div>
                         </div>`;
                html += '</div></div>';

                // Actividad reciente
                if(data.data.pedidos.length > 0) {
                    html += '<div class="col-md-6"><h6>Pedidos Recientes</h6>';
                    data.data.pedidos.slice(0, 10).forEach(pedido => {
                        html += `<div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                    <small>Pedido #${pedido._id}</small>
                                    <small class="text-muted">${new Date(pedido.created_at).toLocaleDateString()}</small>
                                 </div>`;
                    });
                    html += '</div>';
                }

                if(data.data.usuarios_creados.length > 0) {
                    html += '<div class="col-md-6"><h6>Usuarios Creados</h6>';
                    data.data.usuarios_creados.slice(0, 10).forEach(usuario => {
                        html += `<div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                    <small>${usuario.name}</small>
                                    <small class="text-muted">${new Date(usuario.created_at).toLocaleDateString()}</small>
                                 </div>`;
                    });
                    html += '</div>';
                }

                html += '</div>';
                document.getElementById('activityContent').innerHTML = html;
            } else {
                document.getElementById('activityContent').innerHTML = '<div class="alert alert-danger">Error al cargar actividad</div>';
            }
        })
        .catch(error => {
            document.getElementById('activityContent').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });

    new bootstrap.Modal(document.getElementById('activityModal')).show();
}
</script>
@endsection