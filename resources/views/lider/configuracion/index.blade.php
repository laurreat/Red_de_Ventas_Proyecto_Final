@extends('layouts.lider')

@section('title', '- Configuración')
@section('page-title', 'Configuración')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Configuraciones del Dashboard
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('lider.configuracion.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="text-primary">Notificaciones</h6>
                            <hr>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notificaciones_email"
                                       name="notificaciones_email" value="1"
                                       {{ $configuraciones['notificaciones_email'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="notificaciones_email">
                                    <strong>Notificaciones por Email</strong>
                                    <div class="small text-muted">Recibir notificaciones importantes por correo electrónico</div>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notificaciones_push"
                                       name="notificaciones_push" value="1"
                                       {{ $configuraciones['notificaciones_push'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="notificaciones_push">
                                    <strong>Notificaciones Push</strong>
                                    <div class="small text-muted">Recibir notificaciones emergentes en el navegador</div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-primary">Dashboard</h6>
                            <hr>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="mostrar_rendimiento"
                                       name="mostrar_rendimiento" value="1"
                                       {{ $configuraciones['mostrar_rendimiento'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="mostrar_rendimiento">
                                    <strong>Mostrar Métricas de Rendimiento</strong>
                                    <div class="small text-muted">Mostrar gráficos y estadísticas detalladas en el dashboard</div>
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tema_dashboard" class="form-label">Tema del Dashboard</label>
                                    <select class="form-select" id="tema_dashboard" name="tema_dashboard">
                                        <option value="claro" {{ $configuraciones['tema_dashboard'] == 'claro' ? 'selected' : '' }}>Claro</option>
                                        <option value="oscuro" {{ $configuraciones['tema_dashboard'] == 'oscuro' ? 'selected' : '' }}>Oscuro</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="zona_horaria" class="form-label">Zona Horaria</label>
                                    <select class="form-select" id="zona_horaria" name="zona_horaria">
                                        <option value="America/Bogota" {{ $configuraciones['zona_horaria'] == 'America/Bogota' ? 'selected' : '' }}>Colombia (GMT-5)</option>
                                        <option value="America/New_York" {{ $configuraciones['zona_horaria'] == 'America/New_York' ? 'selected' : '' }}>New York (GMT-5/-4)</option>
                                        <option value="America/Mexico_City" {{ $configuraciones['zona_horaria'] == 'America/Mexico_City' ? 'selected' : '' }}>México (GMT-6)</option>
                                        <option value="America/Argentina/Buenos_Aires" {{ $configuraciones['zona_horaria'] == 'America/Argentina/Buenos_Aires' ? 'selected' : '' }}>Argentina (GMT-3)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-primary">Información de Cuenta</h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Usuario:</strong> {{ $user->name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                    <p class="mb-1"><strong>Rol:</strong> {{ ucfirst($user->rol) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                                    <p class="mb-1"><strong>Última actividad:</strong> {{ $user->updated_at->diffForHumans() }}</p>
                                    @if($user->lider_id)
                                        <p class="mb-1"><strong>Líder asignado:</strong> ID #{{ $user->lider_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Acciones Adicionales -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Zona Peligrosa
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Las siguientes acciones son irreversibles.</p>

                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3">
                        <div>
                            <h6 class="mb-1">Exportar Datos</h6>
                            <small class="text-muted">Descarga todos tus datos en formato JSON</small>
                        </div>
                        <button class="btn btn-outline-secondary" onclick="showComingSoon('Exportar Datos')">
                            <i class="bi bi-download me-1"></i>
                            Exportar
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 border border-danger rounded">
                        <div>
                            <h6 class="mb-1 text-danger">Eliminar Cuenta</h6>
                            <small class="text-muted">Esta acción eliminará permanentemente tu cuenta</small>
                        </div>
                        <button class="btn btn-outline-danger" onclick="showComingSoon('Eliminar Cuenta')">
                            <i class="bi bi-trash me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showComingSoon(feature) {
    alert(`${feature} estará disponible próximamente. ¡Estamos trabajando en ello!`);
}
</script>
@endpush