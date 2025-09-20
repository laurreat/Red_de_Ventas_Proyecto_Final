@extends('layouts.admin')

@section('title', 'Configuración Personal')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Configuración Personal</h2>
                    <p class="text-muted mb-0">Personaliza tu experiencia en el sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-warning me-2" onclick="resetearConfiguracion()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Restablecer
                    </button>
                    <button class="btn btn-success" onclick="exportarConfiguracion()">
                        <i class="bi bi-download me-1"></i>
                        Exportar
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

    <!-- Navegación por pestañas -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#interfaz" type="button">
                        <i class="bi bi-palette me-2"></i>Interfaz
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#notificaciones" type="button">
                        <i class="bi bi-bell me-2"></i>Notificaciones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#privacidad" type="button">
                        <i class="bi bi-shield-lock me-2"></i>Privacidad
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#seguridad" type="button">
                        <i class="bi bi-key me-2"></i>Seguridad
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#dashboard" type="button">
                        <i class="bi bi-grid me-2"></i>Dashboard
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="tab-content">
        <!-- Configuración de Interfaz -->
        <div class="tab-pane fade show active" id="interfaz" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-palette me-2"></i>
                        Configuración de Interfaz
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion-personal.update-interfaz') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Tema de la Interfaz</label>
                                    <select name="tema" class="form-select">
                                        @foreach($temasDisponibles as $value => $label)
                                            <option value="{{ $value }}" {{ $configuracion['interfaz']['tema'] == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Idioma</label>
                                    <select name="idioma" class="form-select">
                                        @foreach($idiomasDisponibles as $value => $label)
                                            <option value="{{ $value }}" {{ $configuracion['interfaz']['idioma'] == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Zona Horaria</label>
                                    <select name="zona_horaria" class="form-select">
                                        @foreach($zonasHorarias as $value => $label)
                                            <option value="{{ $value }}" {{ $configuracion['interfaz']['zona_horaria'] == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Formato de Fecha</label>
                                    <select name="formato_fecha" class="form-select">
                                        <option value="d/m/Y" {{ $configuracion['interfaz']['formato_fecha'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ $configuracion['interfaz']['formato_fecha'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="Y-m-d" {{ $configuracion['interfaz']['formato_fecha'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Formato de Hora</label>
                                    <select name="formato_hora" class="form-select">
                                        <option value="H:i" {{ $configuracion['interfaz']['formato_hora'] == 'H:i' ? 'selected' : '' }}>24 horas (14:30)</option>
                                        <option value="h:i A" {{ $configuracion['interfaz']['formato_hora'] == 'h:i A' ? 'selected' : '' }}>12 horas (2:30 PM)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="sidebar_collapsed"
                                               {{ $configuracion['interfaz']['sidebar_collapsed'] ? 'checked' : '' }}>
                                        <label class="form-check-label" style="color: black;">
                                            Sidebar contraído por defecto
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar Configuración de Interfaz
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Notificaciones -->
        <div class="tab-pane fade" id="notificaciones" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bell me-2"></i>
                        Configuración de Notificaciones
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion-personal.update-notificaciones') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: black;">Notificaciones por Email</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_pedidos"
                                           {{ $configuracion['notificaciones']['email_pedidos'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Nuevos pedidos y cambios de estado
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_usuarios"
                                           {{ $configuracion['notificaciones']['email_usuarios'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Nuevos usuarios registrados
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_sistema"
                                           {{ $configuracion['notificaciones']['email_sistema'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Alertas del sistema
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_comisiones"
                                           {{ $configuracion['notificaciones']['email_comisiones'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Comisiones y pagos
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_reportes"
                                           {{ $configuracion['notificaciones']['email_reportes'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Reportes semanales/mensuales
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: black;">Otras Notificaciones</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="sms_urgente"
                                           {{ $configuracion['notificaciones']['sms_urgente'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        SMS para alertas urgentes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="push_browser"
                                           {{ $configuracion['notificaciones']['push_browser'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Notificaciones push del navegador
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="sonido_notificaciones"
                                           {{ $configuracion['notificaciones']['sonido_notificaciones'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Sonidos de notificación
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Frecuencia de Resumen</label>
                                    <select name="frecuencia_digest" class="form-select">
                                        <option value="never" {{ $configuracion['notificaciones']['frecuencia_digest'] == 'never' ? 'selected' : '' }}>Nunca</option>
                                        <option value="daily" {{ $configuracion['notificaciones']['frecuencia_digest'] == 'daily' ? 'selected' : '' }}>Diario</option>
                                        <option value="weekly" {{ $configuracion['notificaciones']['frecuencia_digest'] == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar Configuración de Notificaciones
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Privacidad -->
        <div class="tab-pane fade" id="privacidad" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-shield-lock me-2"></i>
                        Configuración de Privacidad
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion-personal.update-privacidad') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: black;">Visibilidad del Perfil</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="perfil_publico"
                                           {{ $configuracion['privacidad']['perfil_publico'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Perfil público (visible para otros usuarios)
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="mostrar_email"
                                           {{ $configuracion['privacidad']['mostrar_email'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Mostrar email en el perfil
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="mostrar_telefono"
                                           {{ $configuracion['privacidad']['mostrar_telefono'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Mostrar teléfono en el perfil
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: black;">Actividad y Contacto</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="mostrar_ultima_conexion"
                                           {{ $configuracion['privacidad']['mostrar_ultima_conexion'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Mostrar última conexión
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="permitir_mensajes"
                                           {{ $configuracion['privacidad']['permitir_mensajes'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Permitir mensajes de otros usuarios
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="indexar_perfil"
                                           {{ $configuracion['privacidad']['indexar_perfil'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Permitir indexación del perfil en buscadores
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Nota:</strong> Los cambios en la privacidad pueden tardar unos minutos en aplicarse completamente.
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar Configuración de Privacidad
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración de Seguridad -->
        <div class="tab-pane fade" id="seguridad" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-key me-2"></i>
                        Configuración de Seguridad
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion-personal.update-seguridad') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: black;">Sesiones y Acceso</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="sesiones_multiples"
                                           {{ $configuracion['seguridad']['sesiones_multiples'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Permitir sesiones múltiples
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Logout automático (minutos)</label>
                                    <input type="number" name="logout_automatico" class="form-control"
                                           min="5" max="480" value="{{ $configuracion['seguridad']['logout_automatico'] }}">
                                    <small class="text-muted">Entre 5 y 480 minutos</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="verificacion_2fa"
                                           {{ $configuracion['seguridad']['verificacion_2fa'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Verificación en dos pasos (2FA)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: black;">Alertas y Monitoreo</h6>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="alertas_login"
                                           {{ $configuracion['seguridad']['alertas_login'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Alertas de inicio de sesión
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="historial_actividad"
                                           {{ $configuracion['seguridad']['historial_actividad'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Mantener historial de actividad
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Actualización automática (segundos)</label>
                                    <input type="number" name="refresh_automatico" class="form-control"
                                           min="10" max="300" value="{{ $configuracion['seguridad']['refresh_automatico'] ?? 30 }}">
                                    <small class="text-muted">Entre 10 y 300 segundos</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar Configuración de Seguridad
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuración del Dashboard -->
        <div class="tab-pane fade" id="dashboard" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-grid me-2"></i>
                        Configuración del Dashboard
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion-personal.update-dashboard') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color: black;">Widgets Activos</h6>
                                <div class="row">
                                    @foreach($widgetsDisponibles as $key => $label)
                                    <div class="col-12 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="widgets_activos[]" value="{{ $key }}"
                                                   {{ in_array($key, $configuracion['dashboard']['widgets_activos']) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="color: black;">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: black;">Diseño y Comportamiento</h6>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Layout del Dashboard</label>
                                    <select name="layout_dashboard" class="form-select">
                                        <option value="grid" {{ $configuracion['dashboard']['layout_dashboard'] == 'grid' ? 'selected' : '' }}>Cuadrícula</option>
                                        <option value="list" {{ $configuracion['dashboard']['layout_dashboard'] == 'list' ? 'selected' : '' }}>Lista</option>
                                        <option value="compact" {{ $configuracion['dashboard']['layout_dashboard'] == 'compact' ? 'selected' : '' }}>Compacto</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Densidad de Información</label>
                                    <select name="densidade_informacion" class="form-select">
                                        <option value="compact" {{ $configuracion['dashboard']['densidade_informacion'] == 'compact' ? 'selected' : '' }}>Compacta</option>
                                        <option value="normal" {{ $configuracion['dashboard']['densidade_informacion'] == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="detailed" {{ $configuracion['dashboard']['densidade_informacion'] == 'detailed' ? 'selected' : '' }}>Detallada</option>
                                    </select>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="mostrar_tips"
                                           {{ $configuracion['dashboard']['mostrar_tips'] ? 'checked' : '' }}>
                                    <label class="form-check-label" style="color: black;">
                                        Mostrar consejos y ayuda
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar Configuración del Dashboard
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function resetearConfiguracion() {
    if(confirm('¿Estás seguro de restablecer toda tu configuración personal a los valores por defecto? Esta acción no se puede deshacer.')) {
        fetch('{{ route("admin.configuracion-personal.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Configuración restablecida exitosamente');
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

function exportarConfiguracion() {
    window.location.href = '{{ route("admin.configuracion-personal.export") }}';
}

// Mostrar vista previa del tema
document.querySelector('select[name="tema"]').addEventListener('change', function() {
    const tema = this.value;
    const body = document.body;

    // Remover clases de tema existentes
    body.classList.remove('theme-light', 'theme-dark', 'theme-auto');

    // Aplicar nueva clase de tema
    if (tema === 'dark') {
        body.classList.add('theme-dark');
    } else if (tema === 'auto') {
        body.classList.add('theme-auto');
    } else {
        body.classList.add('theme-light');
    }
});

// Actualizar formato de fecha en tiempo real
document.querySelector('select[name="formato_fecha"]').addEventListener('change', function() {
    const formato = this.value;
    const fecha = new Date();
    let fechaFormateada;

    switch(formato) {
        case 'd/m/Y':
            fechaFormateada = fecha.getDate().toString().padStart(2, '0') + '/' +
                             (fecha.getMonth() + 1).toString().padStart(2, '0') + '/' +
                             fecha.getFullYear();
            break;
        case 'm/d/Y':
            fechaFormateada = (fecha.getMonth() + 1).toString().padStart(2, '0') + '/' +
                             fecha.getDate().toString().padStart(2, '0') + '/' +
                             fecha.getFullYear();
            break;
        case 'Y-m-d':
            fechaFormateada = fecha.getFullYear() + '-' +
                             (fecha.getMonth() + 1).toString().padStart(2, '0') + '-' +
                             fecha.getDate().toString().padStart(2, '0');
            break;
    }

    // Mostrar vista previa (podrías agregar un elemento para mostrar esto)
    console.log('Vista previa de fecha:', fechaFormateada);
});

// Validar formularios antes del envío
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const logoutInput = this.querySelector('input[name="logout_automatico"]');
        if (logoutInput) {
            const valor = parseInt(logoutInput.value);
            if (valor < 5 || valor > 480) {
                e.preventDefault();
                alert('El tiempo de logout automático debe estar entre 5 y 480 minutos.');
                return false;
            }
        }

        const refreshInput = this.querySelector('input[name="refresh_automatico"]');
        if (refreshInput) {
            const valor = parseInt(refreshInput.value);
            if (valor < 10 || valor > 300) {
                e.preventDefault();
                alert('El tiempo de actualización automática debe estar entre 10 y 300 segundos.');
                return false;
            }
        }
    });
});
</script>
@endsection