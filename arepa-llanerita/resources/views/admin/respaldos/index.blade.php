@extends('layouts.admin')

@section('title', 'Gestión de Respaldos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/respaldos.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Gestión de Respaldos</h2>
                    <h4 class="text-muted mb-0">Administra los respaldos del sistema</h4>
                </div>
                <div>
                    <button class="btn btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#cleanupBackupsModal">
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

    {{-- Mensajes flash manejados por AdminAlerts en admin-functions.js --}}

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-archive fs-2 text-primary"></i>
                    <h4 class="mt-2">{{ $stats['total_backups'] }}</h4>
                    <small class="text-muted">Total Respaldos</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-hdd fs-2 text-info"></i>
                    <h4 class="mt-2">{{ $stats['total_size'] }}</h4>
                    <small class="text-muted">Espacio Usado</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-2 text-success"></i>
                    <h5 class="mt-2">{{ $stats['last_backup'] ? $stats['last_backup']->diffForHumans() : 'Nunca' }}</h5>
                    <small class="text-muted">Último Respaldo</small>
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
                                                @if(pathinfo($backup['filename'], PATHINFO_EXTENSION) === 'json')
                                                <a href="{{ route('admin.respaldos.view', $backup['filename']) }}"
                                                   class="btn btn-info btn-sm" title="Ver JSON" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @endif
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
                                                        data-bs-toggle="modal" data-bs-target="#deleteBackupModal"
                                                        data-backup-filename="{{ $backup['filename'] }}"
                                                        data-backup-name="{{ $backup['filename'] }}" title="Eliminar">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-success" id="btnCreateBackup">
                        <i class="bi bi-plus me-1"></i> Crear Respaldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Limpiar Respaldos Antiguos -->
<div class="modal fade" id="cleanupBackupsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2 text-warning"></i>
                    Limpiar Respaldos Antiguos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas continuar con la limpieza de respaldos antiguos?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmCleanup">
                    <i class="bi bi-trash me-1"></i> Limpiar Respaldos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Respaldo Individual -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2 text-danger"></i>
                    Eliminar Respaldo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>¡Cuidado!</strong> Estás a punto de eliminar permanentemente este respaldo.
                </div>
                <p>¿Estás seguro de que deseas eliminar el respaldo <strong id="backupNameToDelete"></strong>?</p>
                <p class="text-muted small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                    <i class="bi bi-trash me-1"></i> Eliminar Respaldo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Notificación de Éxito -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Operación Exitosa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 mb-3">¡Éxito!</h4>
                    <p id="successMessage" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">
                    <i class="bi bi-check me-1"></i> Mantener Información Visible
                </button>
                <button type="button" class="btn btn-success" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Página
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Notificación de Error -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 mb-3">Oops! Algo salió mal</h4>
                    <p id="errorMessage" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Restaurar Base de Datos -->
<div class="modal fade" id="restoreBackupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Restaurar Base de Datos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 mb-3 text-warning">⚠️ Acción Crítica</h4>
                </div>

                <div class="alert alert-warning border-0" id="restoreImportantInfo" style="display: block !important; visibility: visible !important; opacity: 1 !important;">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle me-2"></i>
                        Información importante:
                    </h6>
                    <ul class="mb-0">
                        <li>Esta acción restaurará la base de datos desde el respaldo seleccionado</li>
                        <li><strong>Todos los datos actuales serán reemplazados</strong></li>
                        <li>La operación no se puede deshacer</li>
                        <li>Se recomienda crear un respaldo actual antes de continuar</li>
                    </ul>
                </div>

                <div class="bg-light p-3 rounded mt-3">
                    <h6><i class="bi bi-file-earmark-zip me-2"></i>Respaldo a restaurar:</h6>
                    <p id="restoreBackupName" class="mb-0 font-monospace text-primary"></p>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmRestore" required>
                    <label class="form-check-label text-danger fw-bold" for="confirmRestore">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        He leído y entiendo que esta acción reemplazará todos los datos actuales
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn" disabled onclick="ejecutarRestauracion()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Restaurar Base de Datos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor de Notificaciones Persistentes -->
<div id="persistentNotifications" class="persistent-notifications"></div>

@endsection

@push('scripts')
{{-- Variables globales para los módulos de respaldos --}}
<script>
window.respaldosRoutes = {
    create: '{{ route("admin.respaldos.create") }}',
    cleanup: '{{ route("admin.respaldos.cleanup") }}',
    delete: '/admin/respaldos/:filename',
    restore: '/admin/respaldos/:filename/restore'
};
window.respaldosCSRF = '{{ csrf_token() }}';
</script>

{{-- Módulos de funcionalidad de respaldos --}}
<script src="{{ asset('js/admin/respaldos-management.js') }}"></script>
@endpush