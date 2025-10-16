@extends('layouts.admin')

@section('title', 'Logs del Sistema')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/logs-modern.css') }}?v={{ filemtime(public_path('css/admin/logs-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header Hero --}}
    <div class="logs-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="logs-header-title">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Logs del Sistema
                </h1>
                <p class="logs-header-subtitle">
                    Monitoreo y gestión centralizada de logs de la aplicación
                </p>
            </div>
            <div class="logs-header-actions">
                <button class="logs-action-btn logs-action-btn-warning" data-logs-action="clear">
                    <i class="bi bi-trash"></i>
                    Limpiar Principal
                </button>
                <button class="logs-action-btn logs-action-btn-danger" data-logs-action="cleanup">
                    <i class="bi bi-archive"></i>
                    Limpiar Antiguos
                </button>
                <button class="logs-action-btn logs-action-btn-success" data-logs-action="export">
                    <i class="bi bi-download"></i>
                    Exportar
                </button>
                <button class="logs-action-btn" data-logs-action="refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                    Actualizar
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-1">
                <i class="bi bi-file-text logs-stat-icon text-primary"></i>
                <div class="logs-stat-value">{{ $stats['total_logs'] }}</div>
                <div class="logs-stat-label">Total Logs</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-2">
                <i class="bi bi-exclamation-triangle logs-stat-icon text-danger"></i>
                <div class="logs-stat-value">{{ $stats['errors_today'] }}</div>
                <div class="logs-stat-label">Errores Hoy</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-3">
                <i class="bi bi-exclamation-circle logs-stat-icon text-warning"></i>
                <div class="logs-stat-value">{{ $stats['warnings_today'] }}</div>
                <div class="logs-stat-label">Advertencias Hoy</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-4">
                <i class="bi bi-info-circle logs-stat-icon text-info"></i>
                <div class="logs-stat-value">{{ $stats['info_today'] }}</div>
                <div class="logs-stat-label">Info Hoy</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-5">
                <i class="bi bi-files logs-stat-icon text-secondary"></i>
                <div class="logs-stat-value">{{ $stats['log_files_count'] }}</div>
                <div class="logs-stat-label">Archivos</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="logs-stat-card animate-delay-6">
                <i class="bi bi-hdd logs-stat-icon text-success"></i>
                <div class="logs-stat-value">{{ $stats['total_size'] }}</div>
                <div class="logs-stat-label">Tamaño Total</div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="logs-section-card animate-delay-1">
        <div class="logs-section-header">
            <h5 class="logs-section-title">
                <i class="bi bi-funnel"></i>
                Filtros de Búsqueda
            </h5>
        </div>
        <div class="logs-filter-form">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="logs-filter-label">Nivel de Log</label>
                    <select name="filter" class="logs-filter-control">
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Todos los niveles</option>
                        <option value="error" {{ $filter == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="warning" {{ $filter == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="info" {{ $filter == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="debug" {{ $filter == 'debug' ? 'selected' : '' }}>Debug</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="logs-filter-label">Fecha</label>
                    <input type="date" name="date" class="logs-filter-control" value="{{ $date }}">
                </div>
                <div class="col-md-4">
                    <label class="logs-filter-label">Buscar en logs</label>
                    <input type="text" name="search" class="logs-filter-control"
                           placeholder="Buscar en el contenido..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="logs-filter-btn">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen por Niveles --}}
    <div class="logs-section-card animate-delay-2">
        <div class="logs-section-header">
            <h5 class="logs-section-title">
                <i class="bi bi-bar-chart"></i>
                Resumen del Día ({{ $date }})
            </h5>
        </div>
        <div class="logs-summary-grid">
            @foreach($levelSummary as $level => $count)
            <div class="logs-summary-item">
                <div class="logs-summary-icon">
                    @switch($level)
                        @case('error')
                            <i class="bi bi-x-circle text-danger"></i>
                            @break
                        @case('warning')
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            @break
                        @case('info')
                            <i class="bi bi-info-circle text-info"></i>
                            @break
                        @default
                            <i class="bi bi-bug text-secondary"></i>
                    @endswitch
                </div>
                <div class="logs-summary-content">
                    <div class="logs-summary-value">{{ $count }}</div>
                    <div class="logs-summary-label">{{ $level }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabla de Logs --}}
    <div class="logs-section-card animate-delay-3">
        <div class="logs-section-header">
            <h5 class="logs-section-title">
                <i class="bi bi-list"></i>
                Logs {{ $filter != 'all' ? '(' . ucfirst($filter) . ')' : '' }}
                @if($search)
                    - Búsqueda: "{{ $search }}"
                @endif
            </h5>
            <button class="logs-refresh-btn" data-logs-action="refresh">
                <i class="bi bi-arrow-clockwise"></i>
                Actualizar
            </button>
        </div>

        @if(count($logs) > 0)
            <div class="logs-table-container">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Nivel</th>
                            <th>Mensaje</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <small style="color:#6c757d">{{ $log['date'] }}</small><br>
                                <strong style="color:#722F37">{{ $log['time'] }}</strong>
                            </td>
                            <td>
                                @switch($log['level'])
                                    @case('error')
                                        <span class="logs-badge logs-badge-error">ERROR</span>
                                        @break
                                    @case('warning')
                                        <span class="logs-badge logs-badge-warning">WARNING</span>
                                        @break
                                    @case('info')
                                        <span class="logs-badge logs-badge-info">INFO</span>
                                        @break
                                    @case('debug')
                                        <span class="logs-badge logs-badge-debug">DEBUG</span>
                                        @break
                                    @default
                                        <span class="logs-badge logs-badge-success">{{ strtoupper($log['level']) }}</span>
                                @endswitch
                            </td>
                            <td class="logs-message-cell">
                                <div class="logs-message-text">
                                    {{ Str::limit($log['message'], 150) }}
                                </div>
                                @if(strlen($log['message']) > 150)
                                    <button class="logs-message-expand" data-message="{{ htmlspecialchars($log['message'], ENT_QUOTES, 'UTF-8') }}">
                                        Ver completo <i class="bi bi-arrow-right"></i>
                                    </button>
                                @endif
                            </td>
                            <td>
                                <small style="color:#6c757d;font-family:monospace">{{ $log['file'] }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(count($logs) >= 1000)
                <div class="logs-info-limit">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Límite alcanzado:</strong> Se muestran los primeros 1000 logs. Usa filtros para refinar la búsqueda.
                    </div>
                </div>
            @endif
        @else
            <div class="logs-empty-state">
                <i class="bi bi-file-text logs-empty-icon"></i>
                <h4 class="logs-empty-title">No se encontraron logs</h4>
                <p class="logs-empty-text">
                    @if($search)
                        No hay logs que coincidan con "<strong>{{ $search }}</strong>"
                    @else
                        No hay logs para la fecha y filtros seleccionados
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Variables globales para LogsManager --}}
<script>
window.logsRoutes = {
    clear: '{{ route("admin.logs.clear") }}',
    cleanup: '{{ route("admin.logs.cleanup") }}',
    export: '{{ route("admin.logs.export") }}',
    stats: '{{ route("admin.logs.stats") }}'
};
window.logsCSRF = '{{ csrf_token() }}';
window.logsCurrentDate = '{{ $date }}';
window.logsToday = '{{ now()->format("Y-m-d") }}';
</script>

{{-- Logs Manager Moderno --}}
<script src="{{ asset('js/admin/logs-modern.js') }}?v={{ filemtime(public_path('js/admin/logs-modern.js')) }}"></script>
@endpush
