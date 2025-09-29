@extends('layouts.admin')

@section('title', 'Gestión de Respaldos')

<style>
/* CSS simplificado para modales funcionales */
.modal {
    z-index: 9999 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(0, 0, 0, 0.5) !important;
    display: none !important; /* Ocultos por defecto */
}

.modal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-dialog {
    position: relative !important;
    max-width: 500px !important;
    width: 90% !important;
    margin: 0 auto !important;
    z-index: 10000 !important;
}

.modal-content {
    background: white !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    position: relative !important;
    z-index: 10001 !important;
}

.modal-backdrop {
    display: none !important;
}

/* Asegurar que solo un modal sea visible a la vez */
body.modal-open .modal:not(.show) {
    display: none !important;
}

/* Notificaciones persistentes */
.persistent-notifications {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 10050 !important;
    max-width: 400px;
    width: 100%;
}

.persistent-notification {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    transform: translateX(100%);
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
}

.persistent-notification.show {
    transform: translateX(0);
}

.persistent-notification.hide {
    transform: translateX(100%);
    opacity: 0;
}

.notification-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #6c757d;
    z-index: 10;
}

.notification-close:hover {
    color: #495057;
}

/* Información importante permanente */
.important-info-panel {
    position: fixed;
    bottom: 20px;
    left: 20px;
    max-width: 350px;
    z-index: 10049 !important;
    transition: all 0.3s ease-in-out;
}

.important-info-panel.minimized {
    transform: translateY(calc(100% - 50px));
}

.important-info-panel .panel-toggle {
    position: absolute;
    top: -15px;
    right: 15px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.important-info-panel .panel-toggle:hover {
    background: #0056b3;
}

/* Información importante del modal de restaurar - SIEMPRE VISIBLE */
#restoreImportantInfo {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 999999 !important;
}

#restoreImportantInfo * {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
</style>

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
<script>
console.log('🔧 Script de respaldos cargando...');
console.log('✅ Bootstrap disponible:', typeof bootstrap !== 'undefined');
console.log('✅ Document estado:', document.readyState);

// Variables globales
let backupFilenameToDelete = '';

// Funciones para modales profesionales
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    ocultarTodosLosModales();
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
    }, 300);
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    ocultarTodosLosModales();
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
    }, 300);
}

// Funciones simples para modales
function ocultarTodosLosModales() {
    console.log('Ocultando todos los modales...');
    const modales = document.querySelectorAll('.modal');
    modales.forEach(function(modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    });

    // Remover todas las capas de fondo oscuro (backdrop)
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });

    // Restaurar scroll del body
    document.body.style.overflow = '';
    document.body.classList.remove('modal-open');
}

function mostrarModal(modalId) {
    console.log('Mostrando modal:', modalId);

    // Primero ocultar todos los modales y limpiar cualquier resto
    ocultarTodosLosModales();

    // Esperar un momento para que se complete la limpieza
    setTimeout(function() {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Configurar el modal
            modal.removeAttribute('aria-hidden');
            modal.style.display = 'flex';
            modal.style.zIndex = '1055'; // Z-index alto para estar por encima
            modal.classList.add('show');

            // Crear backdrop limpio
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '1050';
            document.body.appendChild(backdrop);

            // Configurar body para modal
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';

            console.log('✅ Modal ' + modalId + ' mostrado correctamente');

            // Enfocar el primer elemento interactivo del modal
            setTimeout(function() {
                const firstInput = modal.querySelector('input, select, button');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 100);
        } else {
            console.error('❌ Modal no encontrado:', modalId);
        }
    }, 100);
}

function ocultarModal(modalId) {
    console.log('Ocultando modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        // Remover backdrop asociado
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });

        // Restaurar body
        document.body.style.overflow = '';
        document.body.classList.remove('modal-open');

        // Limpiar formularios
        const forms = modal.querySelectorAll('form');
        forms.forEach(function(form) {
            form.reset();
        });

        console.log('✅ Modal ' + modalId + ' ocultado correctamente');
    }
}

// Configurar todo cuando la página esté lista
window.addEventListener('load', function() {
    console.log('✅ Página cargada, configurando modales...');

    // Asegurar que todos los modales estén ocultos al inicio
    ocultarTodosLosModales();

    // Botones para abrir modales
    const botonesAbrir = document.querySelectorAll('[data-bs-toggle="modal"]');
    console.log('Botones encontrados:', botonesAbrir.length);

    botonesAbrir.forEach(function(boton, index) {
        const targetModal = boton.getAttribute('data-bs-target');
        console.log('✅ Configurando botón', index + 1, 'para modal:', targetModal);

        boton.onclick = function(e) {
            e.preventDefault();
            console.log('🔄 CLIC EN BOTÓN DETECTADO!');

            const modalId = this.getAttribute('data-bs-target').substring(1);
            console.log('🎯 Modal objetivo:', modalId);

            // Verificar que el modal existe antes de intentar mostrarlo
            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error('❌ Modal no encontrado:', modalId);
                return;
            }

            // Para modal de eliminar
            if (modalId === 'deleteBackupModal') {
                const filename = this.getAttribute('data-backup-filename');
                const backupName = this.getAttribute('data-backup-name');

                console.log('🔍 Atributos del botón:');
                console.log('  - data-backup-filename:', filename);
                console.log('  - data-backup-name:', backupName);

                backupFilenameToDelete = filename;
                const nameElement = document.getElementById('backupNameToDelete');
                if (nameElement) {
                    nameElement.textContent = backupName;
                }
                console.log('📄 Variable global actualizada:', backupFilenameToDelete);
            }

            mostrarModal(modalId);
        };
    });

    // Botones para cerrar modales
    const botonesCerrar = document.querySelectorAll('[data-bs-dismiss="modal"]');
    console.log('✅ Botones cerrar encontrados:', botonesCerrar.length);

    botonesCerrar.forEach(function(boton, index) {
        console.log('⚙️ Configurando botón cerrar', index + 1);
        boton.onclick = function(e) {
            e.preventDefault();
            console.log('❌ CLIC EN CERRAR DETECTADO!');
            const modal = this.closest('.modal');
            if (modal) {
                console.log('🎯 Cerrando modal:', modal.id);
                ocultarModal(modal.id);
            } else {
                console.log('⚠️ No se encontró modal padre, cerrando todos');
                ocultarTodosLosModales();
            }
        };
    });

    // Cerrar modal al hacer clic en el backdrop (pero no en elementos dentro del modal)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            console.log('🔄 Clic en backdrop detectado, cerrando todos los modales');
            ocultarTodosLosModales();
        } else if (e.target.classList.contains('modal') && !e.target.closest('.modal-content')) {
            console.log('🔄 Clic fuera del contenido del modal');
            ocultarTodosLosModales();
        }
    });

    // =================== CONFIGURACIÓN DE BOTONES DE ACCIÓN (EVENT DELEGATION) ===================

    console.log('🔧 Configurando botones de acción con event delegation...');

    // Event delegation para todos los botones de acción en modales
    document.addEventListener('click', function(e) {
        // Debug completo del evento
        console.log('🔍 Evento click detectado:', {
            target: e.target,
            tagName: e.target.tagName,
            id: e.target.id,
            className: e.target.className
        });

        // Mensaje muy visible para cualquier clic
        if (e.target.id && (e.target.id === 'btnCreateBackup' || e.target.id === 'btnConfirmCleanup' || e.target.id === 'btnConfirmDelete')) {
            console.log('🚨 ¡CLIC EN BOTÓN DE ACCIÓN DETECTADO!', e.target.id);
        }

        // Determinar el botón que fue clickeado (incluso si se hizo clic en un ícono)
        let targetButton = e.target;
        if (e.target.tagName !== 'BUTTON') {
            targetButton = e.target.closest('button');
        }

        // Debug: mostrar qué botón fue identificado
        if (targetButton) {
            console.log('🎯 Botón identificado:', {
                id: targetButton.id,
                className: targetButton.className,
                type: targetButton.type
            });
        } else {
            console.log('❌ No se identificó ningún botón');
            return; // Si no es un botón, salir
        }

        // Botón crear backup
        if (targetButton && targetButton.id === 'btnCreateBackup') {
            e.preventDefault();
            console.log('📝 CREAR RESPALDO - BOTÓN DETECTADO!');
            console.log('🔍 Ejecutando creación de respaldo...');

            const btn = document.getElementById('btnCreateBackup');
            const form = document.getElementById('createBackupForm');

            if (!form) {
                console.error('❌ Formulario no encontrado');
                return;
            }

            btn.innerHTML = 'Creando...';
            btn.disabled = true;

            const formData = new FormData(form);

            console.log('🚀 Enviando petición para crear respaldo...');
            fetch('{{ route("admin.respaldos.create") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                console.log('📥 Respuesta del servidor:', response.status);
                if (!response.ok) {
                    throw new Error('Error del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if(data.success) {
                    showSuccessModal('¡Respaldo creado exitosamente! Archivo: ' + data.filename + ' (Formato JSON fácil de exportar)');
                    // Información importante permanece visible - no recargar automáticamente
                } else {
                    showErrorModal('Error al crear respaldo: ' + (data.message || 'Error desconocido'));
                    btn.innerHTML = '<i class="bi bi-plus me-1"></i> Crear Respaldo';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                showErrorModal('Error de conexión: ' + error.message);
                btn.innerHTML = '<i class="bi bi-plus me-1"></i> Crear Respaldo';
                btn.disabled = false;
            });
        }

        // Botón limpiar respaldos
        if (targetButton && targetButton.id === 'btnConfirmCleanup') {
            e.preventDefault();
            console.log('🧹 LIMPIAR RESPALDOS - BOTÓN DETECTADO!');
            console.log('🔍 Ejecutando limpieza de respaldos...');

            const btn = document.getElementById('btnConfirmCleanup');
            btn.innerHTML = 'Limpiando...';
            btn.disabled = true;

            fetch('{{ route("admin.respaldos.cleanup") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    ocultarModal('cleanupBackupsModal');
                    showSuccessModal(data.message + '. Operación completada exitosamente.');
                    // Información importante permanece visible - no recargar automáticamente
                } else {
                    showErrorModal('Error: ' + data.message);
                    btn.innerHTML = '<i class="bi bi-trash me-1"></i> Limpiar Respaldos';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                showErrorModal('Error de conexión: ' + error.message);
                btn.innerHTML = '<i class="bi bi-trash me-1"></i> Limpiar Respaldos';
                btn.disabled = false;
            });
        }

        // Botón eliminar respaldo
        if (targetButton && targetButton.id === 'btnConfirmDelete') {
            e.preventDefault();
            console.log('🗑️ ELIMINAR RESPALDO - BOTÓN DETECTADO!');
            console.log('📁 Archivo a eliminar:', backupFilenameToDelete);
            console.log('🔍 Ejecutando eliminación de respaldo...');

            if (!backupFilenameToDelete) {
                showErrorModal('Error: No hay archivo seleccionado para eliminar.');
                console.error('❌ backupFilenameToDelete está vacío');
                return;
            }

            const btn = document.getElementById('btnConfirmDelete');
            btn.innerHTML = 'Eliminando...';
            btn.disabled = true;

            fetch('/admin/respaldos/' + encodeURIComponent(backupFilenameToDelete), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    ocultarModal('deleteBackupModal');
                    showSuccessModal(data.message + '. Operación completada exitosamente.');
                    // Información importante permanece visible - no recargar automáticamente
                } else {
                    showErrorModal('Error: ' + data.message);
                    btn.innerHTML = '<i class="bi bi-trash me-1"></i> Eliminar Respaldo';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                showErrorModal('Error de conexión: ' + error.message);
                btn.innerHTML = '<i class="bi bi-trash me-1"></i> Eliminar Respaldo';
                btn.disabled = false;
            });
        }
    });

    console.log('✅ Event delegation configurado para botones de acción');

    // Verificar que todos los botones existan al cargar
    console.log('🔍 Verificando existencia de botones...');
    const buttons = [
        'btnCreateBackup',
        'btnConfirmCleanup',
        'btnConfirmDelete'
    ];

    buttons.forEach(function(buttonId) {
        const btn = document.getElementById(buttonId);
        console.log(`${buttonId}:`, btn ? '✅ Existe' : '❌ No encontrado');
    });

    console.log('✅ Configuración de modales completada');
});



// Variables globales para restauración
let backupFilenameToRestore = '';

// Función para restaurar backup con modal profesional
function restaurarBackup(filename) {
    console.log('Iniciando proceso de restauración para:', filename);

    // Guardar el filename globalmente
    backupFilenameToRestore = filename;

    // Mostrar el nombre del archivo en el modal
    document.getElementById('restoreBackupName').textContent = filename;

    // Resetear el checkbox
    const checkbox = document.getElementById('confirmRestore');
    checkbox.checked = false;

    // Deshabilitar el botón de confirmación
    document.getElementById('confirmRestoreBtn').disabled = true;

    // Mostrar el modal
    mostrarModal('restoreBackupModal');
}

// Función para manejar el checkbox de confirmación
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('confirmRestore');
    const confirmBtn = document.getElementById('confirmRestoreBtn');

    if (checkbox && confirmBtn) {
        checkbox.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
            if (this.checked) {
                confirmBtn.classList.remove('btn-outline-warning');
                confirmBtn.classList.add('btn-warning');
            } else {
                confirmBtn.classList.add('btn-outline-warning');
                confirmBtn.classList.remove('btn-warning');
            }
        });
    }
});

// Función para ejecutar la restauración
function ejecutarRestauracion() {
    console.log('Ejecutando restauración para:', backupFilenameToRestore);

    // Verificar que el checkbox esté marcado
    const checkbox = document.getElementById('confirmRestore');
    if (!checkbox.checked) {
        showErrorModal('Debes confirmar que entiendes las consecuencias de esta acción.');
        return;
    }

    // Deshabilitar el botón para evitar dobles clics
    const confirmBtn = document.getElementById('confirmRestoreBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Restaurando...';

    // Realizar la restauración
    fetch(`/admin/respaldos/${backupFilenameToRestore}/restore`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            confirm: true
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta de restauración:', data);

        // Ocultar el modal de restauración
        ocultarTodosLosModales();

        // Mostrar resultado
        if (data.success) {
            let message = data.message;
            if (data.warning) {
                message += '\n\n⚠️ Advertencia: ' + data.warning;
            }
            showSuccessModal(message);

            // Información importante permanece visible - no recargar automáticamente
            // El usuario puede recargar manualmente si desea ver los cambios reflejados
        } else {
            showErrorModal('Error al restaurar: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error en restauración:', error);

        // Ocultar el modal de restauración
        ocultarTodosLosModales();

        // Mostrar error
        showErrorModal('Error de conexión: ' + error.message);
    })
    .finally(() => {
        // Restaurar el botón
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    });
}

// =================== SISTEMA DE NOTIFICACIONES PERSISTENTES ===================

// Variable para contar notificaciones
let notificationCounter = 0;

// Función para crear notificaciones persistentes
function showPersistentNotification(message, type = 'info', persistent = true, duration = 0) {
    const container = document.getElementById('persistentNotifications');
    if (!container) return;

    notificationCounter++;
    const notificationId = 'notification-' + notificationCounter;

    // Configurar colores según el tipo
    let bgColor, borderColor, iconClass;
    switch (type) {
        case 'success':
            bgColor = 'bg-success';
            borderColor = 'border-success';
            iconClass = 'bi-check-circle-fill text-success';
            break;
        case 'warning':
            bgColor = 'bg-warning';
            borderColor = 'border-warning';
            iconClass = 'bi-exclamation-triangle-fill text-warning';
            break;
        case 'danger':
            bgColor = 'bg-danger';
            borderColor = 'border-danger';
            iconClass = 'bi-exclamation-triangle-fill text-danger';
            break;
        default:
            bgColor = 'bg-info';
            borderColor = 'border-info';
            iconClass = 'bi-info-circle-fill text-info';
    }

    // Crear la notificación
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `persistent-notification ${borderColor}`;
    notification.innerHTML = `
        <div class="card border-0 ${borderColor}">
            <div class="card-header ${bgColor} text-white py-2">
                <div class="d-flex align-items-center">
                    <i class="${iconClass} me-2"></i>
                    <small class="fw-bold">Información del Sistema</small>
                </div>
            </div>
            <div class="card-body p-3">
                <p class="mb-0 small">${message}</p>
                ${persistent ? '' : `<div class="progress mt-2" style="height: 2px;"><div class="progress-bar bg-${type}" style="width: 100%; transition: width ${duration}ms linear;"></div></div>`}
            </div>
        </div>
        ${persistent ? `<button class="notification-close" onclick="closePersistentNotification('${notificationId}')">&times;</button>` : ''}
    `;

    // Agregar al contenedor
    container.appendChild(notification);

    // Animar entrada
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Si no es persistente, removerla automáticamente
    if (!persistent && duration > 0) {
        // Animar la barra de progreso
        const progressBar = notification.querySelector('.progress-bar');
        if (progressBar) {
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
        }

        setTimeout(() => {
            closePersistentNotification(notificationId);
        }, duration);
    }

    return notificationId;
}

// Función para cerrar notificaciones persistentes
function closePersistentNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.classList.add('hide');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}


// =================== MODIFICAR FUNCIONES EXISTENTES PARA USAR NOTIFICACIONES PERSISTENTES ===================

// Modificar showSuccessModal para también mostrar notificación persistente
const originalShowSuccessModal = showSuccessModal;
function showSuccessModal(message) {
    // Mostrar modal original
    originalShowSuccessModal(message);

    // Sin notificaciones automáticas adicionales
}

// Modificar showErrorModal para también mostrar notificación persistente
const originalShowErrorModal = showErrorModal;
function showErrorModal(message) {
    // Mostrar modal original
    originalShowErrorModal(message);

    // Sin notificaciones automáticas adicionales
}

// =================== INICIALIZACIÓN ===================

// Sin notificaciones automáticas

// =================== PROTECCIÓN DE INFORMACIÓN IMPORTANTE ===================

// Función para asegurar que la información importante siempre esté visible
function ensureImportantInfoVisible() {
    const importantInfo = document.getElementById('restoreImportantInfo');
    if (importantInfo) {
        importantInfo.style.display = 'block';
        importantInfo.style.visibility = 'visible';
        importantInfo.style.opacity = '1';

        // Asegurar que todos los elementos hijos también sean visibles
        const children = importantInfo.querySelectorAll('*');
        children.forEach(child => {
            child.style.display = 'block';
            child.style.visibility = 'visible';
            child.style.opacity = '1';
        });
    }
}

// Interceptar la apertura del modal para asegurar visibilidad
document.addEventListener('DOMContentLoaded', function() {
    // Agregar evento al botón de restaurar para asegurar que la información esté visible
    document.addEventListener('click', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('onclick') &&
            e.target.getAttribute('onclick').includes('restaurarBackup')) {
            setTimeout(() => {
                ensureImportantInfoVisible();
            }, 300);
        }
    });
});

// Monitorear cambios en el DOM para mantener la información visible
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' || mutation.type === 'childList') {
                const modal = document.getElementById('restoreBackupModal');
                if (modal && modal.classList.contains('show')) {
                    ensureImportantInfoVisible();
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('restoreBackupModal');
        if (modal) {
            observer.observe(modal, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: ['style', 'class']
            });
        }
    });
}

</script>
@endpush