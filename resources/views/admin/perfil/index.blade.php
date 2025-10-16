@extends('layouts.admin')

@section('title', 'Mi Perfil')

@push('styles')
<link href="{{ asset('css/admin/perfil-modern.css') }}?v={{ filemtime(public_path('css/admin/perfil-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="perfil-header fade-in-up">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-4">
                    @if($user->avatar)
                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                        class="perfil-avatar"
                        alt="Avatar"
                        loading="lazy"
                        id="user-avatar">
                    @else
                    <div class="perfil-avatar" id="user-avatar-placeholder">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <h1 class="perfil-title mb-0">{{ $user->name }} {{ $user->apellidos }}</h1>
                        <p class="perfil-subtitle mb-3">{{ ucfirst($stats['rol_actual']) }} • {{ $stats['estado_cuenta'] }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                            </span>
                            @if($user->telefono)
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-telephone me-1"></i>{{ $user->telefono }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="perfil-header-actions justify-content-md-end">
                    <button class="btn-perfil btn-perfil-outline" data-perfil-action="download-data">
                        <i class="bi bi-download"></i>
                        <span class="d-none d-lg-inline">Descargar Datos</span>
                    </button>
                    <button class="btn-perfil btn-perfil-primary" data-perfil-action="show-activity">
                        <i class="bi bi-activity"></i>
                        <span class="d-none d-lg-inline">Ver Actividad</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="perfil-stat-card success fade-in-up animate-delay-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="perfil-stat-icon" style="background: linear-gradient(135deg, var(--wine), var(--wine-dark)); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cart-check text-white fs-4"></i>
                    </div>
                    <span class="perfil-realtime-indicator">
                        <span class="perfil-realtime-dot"></span>
                        <span class="d-none d-lg-inline">En vivo</span>
                    </span>
                </div>
                <div class="perfil-stat-value" data-stat="pedidos-cliente">{{ $stats['pedidos_como_cliente'] }}</div>
                <div class="perfil-stat-label">Pedidos como Cliente</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="perfil-stat-card info fade-in-up animate-delay-2">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="perfil-stat-icon" style="background: linear-gradient(135deg, var(--success), #059669); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-shop text-white fs-4"></i>
                    </div>
                    <span class="perfil-realtime-indicator">
                        <span class="perfil-realtime-dot"></span>
                        <span class="d-none d-lg-inline">En vivo</span>
                    </span>
                </div>
                <div class="perfil-stat-value" data-stat="pedidos-vendedor">{{ $stats['pedidos_como_vendedor'] }}</div>
                <div class="perfil-stat-label">Pedidos como Vendedor</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="perfil-stat-card warning fade-in-up animate-delay-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="perfil-stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-people text-white fs-4"></i>
                    </div>
                    <span class="perfil-realtime-indicator">
                        <span class="perfil-realtime-dot"></span>
                        <span class="d-none d-lg-inline">En vivo</span>
                    </span>
                </div>
                <div class="perfil-stat-value" data-stat="total-referidos">{{ $stats['total_referidos'] }}</div>
                <div class="perfil-stat-label">Total Referidos</div>
            </div>
        </div>
    </div>

    {{-- Resto del contenido se mantiene igual --}}
    <div class="row">
        {{-- Columna Principal --}}
        <div class="col-xl-8">
            {{-- Información Personal --}}
            <div class="perfil-card fade-in-up mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-wine">
                        <i class="bi bi-person me-2"></i>Información Personal
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editProfile">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.perfil.update') }}" enctype="multipart/form-data"
                        class="needs-profile-confirmation"
                        data-confirm-message="¿Estás seguro de actualizar tu información personal?"
                        id="updateProfileForm">
                        @csrf
                        <div class="row">
                            {{-- Avatar --}}
                            <div class="col-md-4 text-center mb-4">
                                <div class="position-relative d-inline-block" id="avatar-container" style="width: 150px; height: 150px;">
                                    @if($user->avatar)
                                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                                        class="perfil-avatar"
                                        width="150" height="150"
                                        style="object-fit: cover; display: block;" alt="Avatar"
                                        loading="lazy"
                                        id="user-avatar-form">
                                    @else
                                    <div class="perfil-avatar" id="user-avatar-placeholder-form" style="width: 150px; height: 150px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    @if($user->avatar)
                                    <button type="button" class="btn btn-sm btn-danger position-absolute rounded-circle"
                                        style="top: 0; right: 0; width: 36px; height: 36px; padding: 0; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"
                                        id="eliminar-avatar-btn" title="Eliminar foto">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <input type="file" name="avatar" class="form-control" accept="image/*,image/webp">
                                    <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>JPG, PNG, GIF, WEBP - Máx. 5MB</small>
                                    <small class="text-success d-none" id="preview-success"><i class="bi bi-check-circle me-1"></i>Vista previa cargada</small>
                                </div>
                            </div>

                            {{-- Datos Personales --}}
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Nombre *</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" name="apellidos" class="form-control" value="{{ $user->apellidos }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email *</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Teléfono</label>
                                        <input type="tel" name="telefono" class="form-control" value="{{ $user->telefono }}">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Dirección</label>
                                        <textarea name="direccion" class="form-control" rows="2">{{ $user->direccion }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control"
                                            value="{{ $user->fecha_nacimiento ? $user->fecha_nacimiento->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Biografía</label>
                                    <textarea name="bio" class="form-control" rows="3"
                                        placeholder="Cuéntanos algo sobre ti...">{{ $user->bio }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="editProfile">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save me-2"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-toggle="collapse" data-bs-target="#editProfile">
                                    <i class="bi bi-x-circle me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Seguridad --}}
            <div class="perfil-card fade-in-up mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-wine">
                        <i class="bi bi-shield-lock me-2"></i>Seguridad
                    </h5>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#changePassword">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="changePassword">
                        <form method="POST" action="{{ route('admin.perfil.update-password') }}"
                            class="needs-profile-confirmation"
                            data-confirm-message="¿Estás seguro de cambiar tu contraseña?"
                            id="updatePasswordForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Contraseña Actual *</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Nueva Contraseña *</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    <small class="text-muted">Mínimo 8 caracteres</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Confirmar *</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-shield-check me-2"></i> Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                    <div class="mt-3">
                        <p class="mb-0">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Última actualización: {{ $user->updated_at->format('d/m/Y') }}.
                            <small class="text-muted">Se recomienda cambiarla cada 90 días.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Lateral --}}
        <div class="col-xl-4">
            {{-- Actividad Reciente --}}
            <div class="perfil-card fade-in-up mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-wine">
                            <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                        </h5>
                        <span class="perfil-realtime-indicator">
                            <span class="perfil-realtime-dot"></span>
                            <span class="d-none d-lg-inline">En vivo</span>
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="actividad-realtime" class="perfil-activity-feed">
                        @if($actividadReciente['pedidos_recientes']->count() > 0)
                        <h6 class="px-3 pt-3 mb-2">Últimos Pedidos</h6>
                        @foreach($actividadReciente['pedidos_recientes'] as $pedido)
                        <div class="activity-item mx-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="fw-semibold">
                                        {{ $pedido->numero_pedido ?? 'Pedido #' . substr((string)$pedido->_id, -6) }}
                                    </small><br>
                                    <span class="activity-badge bg-{{ $pedido->estado == 'entregado' ? 'success' : ($pedido->estado == 'cancelado' ? 'danger' : 'warning') }} text-white">
                                        {{ ucfirst($pedido->estado ?? 'pendiente') }}
                                    </span>
                                    @if($pedido->total_final)
                                    <small class="text-muted ms-2">${{ number_format($pedido->total_final, 0) }}</small>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $pedido->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @if($actividadReciente['usuarios_recientes']->count() > 0)
                        <h6 class="px-3 pt-3 mb-2">Referidos Recientes</h6>
                        @foreach($actividadReciente['usuarios_recientes'] as $usuario)
                        <div class="activity-item mx-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="fw-semibold">{{ $usuario->name }} {{ $usuario->apellidos }}</small><br>
                                    @php
                                    $roleColor = [
                                    'administrador' => 'success',
                                    'lider' => 'info',
                                    'vendedor' => 'warning',
                                    'cliente' => 'secondary'
                                    ][$usuario->rol] ?? 'secondary';
                                    @endphp
                                    <span class="activity-badge bg-{{ $roleColor }} text-white">{{ ucfirst($usuario->rol ?? 'cliente') }}</span>
                                </div>
                                <small class="text-muted">{{ $usuario->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @if($actividadReciente['pedidos_recientes']->count() == 0 && $actividadReciente['usuarios_recientes']->count() == 0)
                        <div class="text-center py-4">
                            <i class="bi bi-activity fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay actividad reciente</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notificaciones --}}
            <div class="perfil-card fade-in-up mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-wine">
                        <i class="bi bi-bell me-2"></i>Notificaciones
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#notifConfig">
                        <i class="bi bi-gear"></i>
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.perfil.update-notifications') }}"
                        class="needs-profile-confirmation"
                        data-confirm-message="¿Actualizar preferencias de notificaciones?"
                        id="updateNotificationsForm">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_pedidos"
                                {{ $notificaciones['email_pedidos'] ? 'checked' : '' }}>
                            <label class="form-check-label">
                                <i class="bi bi-cart-check text-primary me-1"></i>
                                Notificaciones de pedidos
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_usuarios"
                                {{ $notificaciones['email_usuarios'] ? 'checked' : '' }}>
                            <label class="form-check-label">
                                <i class="bi bi-people text-success me-1"></i>
                                Usuarios nuevos
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_sistema"
                                {{ $notificaciones['email_sistema'] ? 'checked' : '' }}>
                            <label class="form-check-label">
                                <i class="bi bi-bell text-info me-1"></i>
                                Notificaciones del sistema
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="sms_urgente"
                                {{ $notificaciones['sms_urgente'] ? 'checked' : '' }}>
                            <label class="form-check-label">
                                <i class="bi bi-phone text-warning me-1"></i>
                                SMS alertas urgentes
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="push_browser"
                                {{ $notificaciones['push_browser'] ? 'checked' : '' }}>
                            <label class="form-check-label">
                                <i class="bi bi-browser-chrome text-danger me-1"></i>
                                Notificaciones push
                            </label>
                        </div>

                        <div class="collapse" id="notifConfig">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-bell-fill me-2"></i> Guardar Preferencias
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.perfilRoutes = {
        activity: '{{ route("admin.perfil.activity") }}',
        downloadData: '{{ route("admin.perfil.download-data") }}',
        deleteAvatar: '{{ route("admin.perfil.delete-avatar") }}'
    };
    window.perfilCSRF = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/admin/perfil-modern.js') }}?v={{ filemtime(public_path('js/admin/perfil-modern.js')) }}"></script>
@endpush