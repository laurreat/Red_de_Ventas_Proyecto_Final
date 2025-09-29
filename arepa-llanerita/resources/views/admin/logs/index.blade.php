@extends('layouts.admin')

@section('title', 'Logs del Sistema')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Logs del Sistema</h2>
                    <p class="text-muted mb-0">Monitoreo y gestión de logs de la aplicación</p>
                </div>
                <div>
                    <button class="btn btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#confirmClearModal">
                        <i class="bi bi-trash me-1"></i>
                        Limpiar Log Principal
                    </button>
                    <button class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                        <i class="bi bi-archive me-1"></i>
                        Limpiar Antiguos
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-file-text fs-2 text-primary"></i>
                    <h4 class="mt-2">{{ $stats['total_logs'] }}</h4>
                    <small class="text-muted">Total Logs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                    <h4 class="mt-2">{{ $stats['errors_today'] }}</h4>
                    <small class="text-muted">Errores Hoy</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-circle fs-2 text-warning"></i>
                    <h4 class="mt-2">{{ $stats['warnings_today'] }}</h4>
                    <small class="text-muted">Advertencias Hoy</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-info-circle fs-2 text-info"></i>
                    <h4 class="mt-2">{{ $stats['info_today'] }}</h4>
                    <small class="text-muted">Info Hoy</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-files fs-2 text-secondary"></i>
                    <h4 class="mt-2">{{ $stats['log_files_count'] }}</h4>
                    <small class="text-muted">Archivos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-hdd fs-2 text-success"></i>
                    <h4 class="mt-2">{{ $stats['total_size'] }}</h4>
                    <small class="text-muted">Tamaño Total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" style="color: black;">Nivel de Log</label>
                            <select name="filter" class="form-select">
                                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Todos los niveles</option>
                                <option value="error" {{ $filter == 'error' ? 'selected' : '' }}>Error</option>
                                <option value="warning" {{ $filter == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="info" {{ $filter == 'info' ? 'selected' : '' }}>Info</option>
                                <option value="debug" {{ $filter == 'debug' ? 'selected' : '' }}>Debug</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: black;">Fecha</label>
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="color: black;">Buscar en logs</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Buscar en el contenido..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen por Niveles -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-bar-chart me-2"></i>
                        Resumen del Día ({{ $date }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($levelSummary as $level => $count)
                        <div class="col-md-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="me-3">
                                    @switch($level)
                                        @case('error')
                                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                                            @break
                                        @case('warning')
                                            <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
                                            @break
                                        @case('info')
                                            <i class="bi bi-info-circle fs-4 text-info"></i>
                                            @break
                                        @default
                                            <i class="bi bi-bug fs-4 text-secondary"></i>
                                    @endswitch
                                </div>
                                <div>
                                    <h5 class="mb-0" style="color: black;">{{ $count }}</h5>
                                    <small class="text-muted text-capitalize">{{ $level }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-list me-2"></i>
                        Logs {{ $filter != 'all' ? '(' . ucfirst($filter) . ')' : '' }}
                        @if($search)
                            - Búsqueda: "{{ $search }}"
                        @endif
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="obtenerEstadisticas()">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                </div>
                <div class="card-body">
                    @if(count($logs) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="color: black;">Fecha/Hora</th>
                                        <th style="color: black;">Nivel</th>
                                        <th style="color: black;">Mensaje</th>
                                        <th style="color: black;">Archivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td style="color: black;">
                                            <small>{{ $log['date'] }}</small><br>
                                            <strong>{{ $log['time'] }}</strong>
                                        </td>
                                        <td>
                                            @switch($log['level'])
                                                @case('error')
                                                    <span class="badge bg-danger">ERROR</span>
                                                    @break
                                                @case('warning')
                                                    <span class="badge bg-warning text-dark">WARNING</span>
                                                    @break
                                                @case('info')
                                                    <span class="badge bg-info">INFO</span>
                                                    @break
                                                @case('debug')
                                                    <span class="badge bg-secondary">DEBUG</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ strtoupper($log['level']) }}</span>
                                            @endswitch
                                        </td>
                                        <td style="color: black;">
                                            <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($log['message'], 100) }}
                                            </div>
                                            @if(strlen($log['message']) > 100)
                                                <button class="btn btn-sm btn-link p-0" onclick="mostrarMensajeCompleto({{ json_encode($log['message']) }})">
                                                    Ver completo
                                                </button>
                                            @endif
                                        </td>
                                        <td style="color: black;">
                                            <small>{{ $log['file'] }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($logs) >= 1000)
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Se muestran los primeros 1000 logs. Usa filtros para refinar la búsqueda.
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-text fs-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No se encontraron logs</h4>
                            <p class="text-muted">
                                @if($search)
                                    No hay logs que coincidan con "{{ $search }}"
                                @else
                                    No hay logs para la fecha y filtros seleccionados
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Limpiar Logs Antiguos -->
<div class="modal fade" id="cleanupModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Limpiar Logs Antiguos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cleanupForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="color: black;">Eliminar logs más antiguos a:</label>
                        <select name="days" class="form-select" required>
                            <option value="7">7 días</option>
                            <option value="14">14 días</option>
                            <option value="30" selected>30 días</option>
                            <option value="60">60 días</option>
                            <option value="90">90 días</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción eliminará permanentemente los archivos de log antiguos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Limpiar Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Exportar Logs -->
<div class="modal fade" id="exportModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="exportForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: black;">Fecha Inicio</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ now()->subDays(7)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: black;">Fecha Fin</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: black;">Nivel (Opcional)</label>
                        <select name="level" class="form-select">
                            <option value="">Todos los niveles</option>
                            <option value="error">Solo Errores</option>
                            <option value="warning">Solo Warnings</option>
                            <option value="info">Solo Info</option>
                            <option value="debug">Solo Debug</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-1"></i> Exportar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Mensaje Completo -->
<div class="modal fade" id="messageModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mensaje Completo del Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="messageContent" style="color: black; max-height: 400px; overflow-y: auto; word-wrap: break-word; white-space: pre-wrap;"></pre>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Limpiar Log Principal -->
<div class="modal fade" id="confirmClearModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmar Limpieza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>¿Estás seguro de limpiar el log principal?</strong>
                </div>
                <p>Esta acción eliminará todo el contenido del archivo de log principal y no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="confirmarLimpiarLogs()">
                    <i class="bi bi-trash me-1"></i> Sí, Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Éxito -->
<div class="modal fade" id="successModal" tabindex="-1" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="bi bi-check-circle me-2"></i>
                    Operación Exitosa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Error -->
<div class="modal fade" id="errorModal" tabindex="-1" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* Forzar modales por encima de todo */
.modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 99999 !important;
    display: none !important;
    background: rgba(0, 0, 0, 0.5) !important;
}

.modal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-backdrop {
    display: none !important;
}

.modal-dialog {
    position: relative !important;
    z-index: 99999 !important;
    margin: 0 !important;
    max-width: 90% !important;
    width: auto !important;
}

.modal-content {
    position: relative !important;
    z-index: 99999 !important;
    background: white !important;
    border-radius: 8px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5) !important;
    border: none !important;
}

.modal-header,
.modal-body,
.modal-footer {
    position: relative !important;
    z-index: 99999 !important;
}

/* Asegurar que todos los elementos sean interactivos */
.modal-content *,
.modal button,
.modal input,
.modal select,
.modal textarea,
.btn,
.form-control,
.form-select {
    position: relative !important;
    z-index: 99999 !important;
}

/* Ocultar cualquier backdrop que pueda interferir */
.modal-backdrop.show {
    display: none !important;
}

/* Prevenir scroll del body */
body.modal-open {
    overflow: hidden !important;
}
</style>
<script>
// Función para forzar modal por encima de todo
function forceModalOnTop(modalElement) {
    if (modalElement && modalElement.classList.contains('show')) {
        modalElement.style.position = 'fixed';
        modalElement.style.top = '0';
        modalElement.style.left = '0';
        modalElement.style.right = '0';
        modalElement.style.bottom = '0';
        modalElement.style.width = '100%';
        modalElement.style.height = '100%';
        modalElement.style.zIndex = '99999';
        modalElement.style.display = 'flex';
        modalElement.style.alignItems = 'center';
        modalElement.style.justifyContent = 'center';
        modalElement.style.background = 'rgba(0, 0, 0, 0.5)';

        // Ocultar cualquier backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }
}

// Funciones para mostrar modales
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    setTimeout(() => forceModalOnTop(document.getElementById('successModal')), 10);
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
    setTimeout(() => forceModalOnTop(document.getElementById('errorModal')), 10);
}

// Limpiar log principal - función de confirmación
function confirmarLimpiarLogs() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmClearModal'));
    modal.hide();

    fetch('{{ route("admin.logs.clear") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showSuccessModal('Log principal limpiado exitosamente');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showErrorModal('Error: ' + data.message);
        }
    })
    .catch(error => {
        showErrorModal('Error de conexión: ' + error.message);
    });
}

// Interceptar todos los modales para forzarlos por encima
document.addEventListener('shown.bs.modal', function (event) {
    forceModalOnTop(event.target);
});

// También interceptar cuando se muestran para forzar inmediatamente
document.addEventListener('show.bs.modal', function (event) {
    setTimeout(() => forceModalOnTop(event.target), 10);
});

// Limpiar logs antiguos
document.addEventListener('DOMContentLoaded', function() {
    const cleanupForm = document.getElementById('cleanupForm');
    if (cleanupForm) {
        cleanupForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("admin.logs.cleanup") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).hide();

                if(data.success) {
                    showSuccessModal(data.message + '\nEspacio liberado: ' + data.space_freed);
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    showErrorModal('Error: ' + data.message);
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).hide();
                showErrorModal('Error de conexión: ' + error.message);
            });
        });
    }

    // Exportar logs
    const exportForm = document.getElementById('exportForm');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Crear un enlace temporal para descargar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.logs.export") }}';
            form.style.display = 'none';

            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Agregar datos del formulario
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        });
    }
});

// Mostrar mensaje completo
function mostrarMensajeCompleto(mensaje) {
    document.getElementById('messageContent').textContent = mensaje;
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}

// Obtener estadísticas actualizadas
function obtenerEstadisticas() {
    fetch('{{ route("admin.logs.stats") }}')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Aquí podrías actualizar las estadísticas sin recargar la página
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error al obtener estadísticas:', error);
        });
}

// Auto-refresh cada 30 segundos si estamos viendo logs del día actual
@if($date == now()->format('Y-m-d'))
setInterval(function() {
    // Solo auto-refresh si no hay modales abiertos
    if(!document.querySelector('.modal.show')) {
        obtenerEstadisticas();
    }
}, 30000);
@endif
</script>
@endpush