@extends('layouts.admin')

@section('title', 'Gestión de Respaldos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Gestión de Respaldos</h2>
                    <p class="text-muted mb-0">Administra los respaldos del sistema</p>
                </div>
                <div>
                    <button class="btn btn-outline-warning me-2" onclick="limpiarRespaldos()">
                        <i class="bi bi-trash me-1"></i>
                        Limpiar Antiguos
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                        <i class="bi bi-plus me-1"></i>
                        Crear Respaldo
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

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-archive fs-2 text-primary"></i>
                    <h4 class="mt-2">{{ $stats['total_backups'] }}</h4>
                    <small class="text-muted">Total Respaldos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-hdd fs-2 text-info"></i>
                    <h4 class="mt-2">{{ $stats['total_size'] }}</h4>
                    <small class="text-muted">Espacio Usado</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-2 text-success"></i>
                    <h5 class="mt-2">{{ $stats['last_backup'] ? $stats['last_backup']->diffForHumans() : 'Nunca' }}</h5>
                    <small class="text-muted">Último Respaldo</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-gear fs-2 {{ $stats['automatic_enabled'] ? 'text-success' : 'text-warning' }}"></i>
                    <h5 class="mt-2">{{ $stats['automatic_enabled'] ? 'Activo' : 'Inactivo' }}</h5>
                    <small class="text-muted">Backup Automático</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración de Backup Automático -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-gear me-2"></i>
                        Configuración de Respaldo Automático
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#configBackup">
                        <i class="bi bi-pencil"></i> Configurar
                    </button>
                </div>
                <div class="card-body">
                    <form id="scheduleForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Frecuencia</label>
                                    <select name="frequency" class="form-select">
                                        <option value="hourly" {{ $config['frequency'] == 'hourly' ? 'selected' : '' }}>Cada Hora</option>
                                        <option value="daily" {{ $config['frequency'] == 'daily' ? 'selected' : '' }}>Diario</option>
                                        <option value="weekly" {{ $config['frequency'] == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                        <option value="monthly" {{ $config['frequency'] == 'monthly' ? 'selected' : '' }}>Mensual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Retención (días)</label>
                                    <input type="number" name="retention_days" class="form-control" min="1" max="365"
                                           value="{{ $config['retention_days'] }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Incluir</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include_database"
                                               {{ $config['include_database'] ? 'checked' : '' }}>
                                        <label class="form-check-label" style="color: black;">Base de Datos</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include_files"
                                               {{ $config['include_files'] ? 'checked' : '' }}>
                                        <label class="form-check-label" style="color: black;">Archivos</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" style="color: black;">Estado</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled"
                                               {{ $stats['automatic_enabled'] ? 'checked' : '' }}>
                                        <label class="form-check-label" style="color: black;">Backup Automático</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="configBackup">
                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Guardar Configuración
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#configBackup">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Respaldos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-archive me-2"></i>
                        Respaldos Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($backups) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="color: black;">Archivo</th>
                                        <th style="color: black;">Tipo</th>
                                        <th style="color: black;">Tamaño</th>
                                        <th style="color: black;">Fecha de Creación</th>
                                        <th style="color: black;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                    <tr>
                                        <td style="color: black;">
                                            <i class="bi bi-file-earmark-zip me-2"></i>
                                            <strong>{{ $backup['filename'] }}</strong>
                                        </td>
                                        <td style="color: black;">
                                            @switch($backup['type'])
                                                @case('database')
                                                    <span class="badge bg-primary">Base de Datos</span>
                                                    @break
                                                @case('files')
                                                    <span class="badge bg-info">Archivos</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-success">Completo</span>
                                            @endswitch
                                        </td>
                                        <td style="color: black;">{{ $backup['size'] }}</td>
                                        <td style="color: black;">
                                            {{ $backup['created_at']->format('d/m/Y H:i:s') }}
                                            <br>
                                            <small class="text-muted">{{ $backup['created_at']->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.respaldos.download', $backup['filename']) }}"
                                                   class="btn btn-success btn-sm" title="Descargar">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @if($backup['can_restore'])
                                                <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="restaurarBackup('{{ $backup['filename'] }}')" title="Restaurar">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="eliminarBackup('{{ $backup['filename'] }}')" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No hay respaldos disponibles</h4>
                            <p class="text-muted">Crea tu primer respaldo para comenzar</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                                <i class="bi bi-plus me-1"></i> Crear Primer Respaldo
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Uso del Storage -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-hdd me-2"></i>
                        Uso del Almacenamiento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h5 style="color: black;">{{ $stats['storage_used']['backups'] }}</h5>
                                <small class="text-muted">Usado por Respaldos</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h5 style="color: black;">{{ $stats['storage_used']['available'] }}</h5>
                                <small class="text-muted">Espacio Disponible</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h5 style="color: black;">{{ $stats['storage_used']['total'] }}</h5>
                                <small class="text-muted">Espacio Total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear Backup -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Respaldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBackupForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="color: black;">Tipo de Respaldo</label>
                        <select name="type" class="form-select" required>
                            <option value="full">Completo (Base de datos + Archivos)</option>
                            <option value="database">Solo Base de Datos</option>
                            <option value="files">Solo Archivos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: black;">Descripción (Opcional)</label>
                        <input type="text" name="description" class="form-control"
                               placeholder="Ej: Backup antes de actualización">
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Nota:</strong> El proceso puede tomar varios minutos dependiendo del tamaño de los datos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus me-1"></i> Crear Respaldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Configurar respaldo automático
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('{{ route("admin.respaldos.schedule") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Configuración actualizada exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

// Crear backup
document.getElementById('createBackupForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Creando...';
    submitBtn.disabled = true;

    fetch('{{ route("admin.respaldos.create") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Respaldo creado exitosamente: ' + data.filename);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function eliminarBackup(filename) {
    if(confirm('¿Estás seguro de eliminar este respaldo? Esta acción no se puede deshacer.')) {
        fetch(`/admin/respaldos/${filename}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Respaldo eliminado exitosamente');
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

function restaurarBackup(filename) {
    if(confirm('¿Estás seguro de restaurar este respaldo? Esta acción reemplazará los datos actuales.')) {
        if(confirm('ÚLTIMA CONFIRMACIÓN: Esto restaurará la base de datos. ¿Continuar?')) {
            fetch(`/admin/respaldos/${filename}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    confirm: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message + (data.warning ? '\n\n' + data.warning : ''));
                    if(!data.warning) {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    }
}

function limpiarRespaldos() {
    if(confirm('¿Deseas eliminar los respaldos antiguos según la configuración de retención?')) {
        fetch('{{ route("admin.respaldos.cleanup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message + '\nEspacio liberado: ' + data.space_freed);
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
</script>
@endsection