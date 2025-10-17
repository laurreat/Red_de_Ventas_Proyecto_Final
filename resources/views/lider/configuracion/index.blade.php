@extends('layouts.lider')

@section('title', '- Configuración')
@section('page-title', 'Configuración')

@push('styles')
    <link href="{{ asset('css/lider/configuracion-modern.css') }}?v={{ filemtime(public_path('css/lider/configuracion-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Header -->
    <div class="config-header fade-in-up">
        <div class="config-header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="config-title">
                        <i class="bi bi-gear-fill me-2"></i>
                        Configuración
                    </h1>
                    <p class="config-subtitle mb-0">
                        Personaliza tu experiencia y gestiona las preferencias de tu cuenta
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
            <div class="col-lg-8">
                <!-- Notificaciones -->
                <div class="config-section scale-in">
                    <div class="config-section-header">
                        <i class="bi bi-bell"></i>
                        <h2 class="config-section-title">Notificaciones</h2>
                    </div>
                    <div class="config-section-body">
                        <div class="config-item">
                            <div class="config-item-header">
                                <div>
                                    <h3 class="config-item-title">Notificaciones por Email</h3>
                                    <p class="config-item-description">Recibe notificaciones importantes de ventas, comisiones y alertas del equipo por correo electrónico</p>
                                </div>
                                <label class="form-switch-modern">
                                    <input type="checkbox" name="notificaciones_email" value="1"
                                           {{ $configuraciones['notificaciones_email'] ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="config-item">
                            <div class="config-item-header">
                                <div>
                                    <h3 class="config-item-title">Notificaciones Push</h3>
                                    <p class="config-item-description">Recibe notificaciones emergentes en tiempo real mientras navegas en el sistema</p>
                                </div>
                                <label class="form-switch-modern">
                                    <input type="checkbox" name="notificaciones_push" value="1"
                                           {{ $configuraciones['notificaciones_push'] ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zona Peligrosa -->
                <div class="danger-zone scale-in animate-delay-1">
                    <div class="danger-zone-header">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <h2 class="config-section-title">Zona Peligrosa</h2>
                    </div>
                    <div class="danger-zone-body">
                        <p class="text-muted mb-4">Las siguientes acciones requieren confirmación y pueden ser irreversibles.</p>

                        <div class="danger-action">
                            <div class="danger-action-info">
                                <h6>Exportar Mis Datos</h6>
                                <small>Descarga todos tus datos personales y configuraciones en formato JSON</small>
                            </div>
                            <button type="button" class="config-btn config-btn-secondary" onclick="exportarDatos()">
                                <i class="bi bi-download"></i>
                                Exportar
                            </button>
                        </div>

                        <div class="danger-action" style="border-color: var(--danger);">
                            <div class="danger-action-info">
                                <h6 style="color: var(--danger);">Restablecer Configuración</h6>
                                <small>Vuelve a los valores predeterminados de todas las configuraciones</small>
                            </div>
                            <button type="button" class="config-btn config-btn-outline-danger" onclick="resetearConfiguracion()">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Restablecer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información de Cuenta -->
                <div class="config-section scale-in animate-delay-1">
                    <div class="config-section-header">
                        <i class="bi bi-person-circle"></i>
                        <h2 class="config-section-title">Tu Cuenta</h2>
                    </div>
                    <div class="config-section-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Usuario</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value" style="font-size: 0.813rem;">{{ $user->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Rol</div>
                                <div class="info-value">
                                    <span class="badge" style="background: var(--wine); font-size: 0.813rem;">
                                        {{ ucfirst($user->rol) }}
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Miembro desde</div>
                                <div class="info-value">{{ $user->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Última actividad</div>
                                <div class="info-value" style="font-size: 0.813rem;">{{ $user->updated_at->diffForHumans() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Estado</div>
                                <div class="info-value">
                                    <span class="badge bg-success" style="font-size: 0.813rem;">
                                        <i class="bi bi-check-circle"></i> Activo
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ route('lider.perfil.index') }}" class="config-btn config-btn-secondary" style="width: 100%; justify-content: center;">
                                <i class="bi bi-person-gear"></i>
                                Editar Perfil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Estado de Guardado -->
                <div class="config-section scale-in animate-delay-2">
                    <div class="config-section-body" style="padding: 1.5rem; text-align: center;">
                        <div id="saveStatus" style="display: none;">
                            <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--success);"></i>
                            <p class="text-success fw-bold mt-2 mb-1">Guardado Automático</p>
                            <p class="text-muted mb-0" style="font-size: 0.813rem;">
                                Tus cambios se guardan automáticamente
                            </p>
                        </div>
                        <div id="savingStatus" style="display: none;">
                            <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                                <span class="visually-hidden">Guardando...</span>
                            </div>
                            <p class="text-primary fw-bold mt-2 mb-0">Guardando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.configRoutes = {
    updateRealtime: '{{ route("lider.configuracion.update-realtime") }}',
    reset: '{{ route("lider.configuracion.reset") }}'
};
</script>
<script src="{{ asset('js/lider/configuracion-realtime.js') }}?v={{ filemtime(public_path('js/lider/configuracion-realtime.js')) }}"></script>
@endpush
