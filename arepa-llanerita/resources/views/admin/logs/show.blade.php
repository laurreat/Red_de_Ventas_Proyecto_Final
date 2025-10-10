@extends('layouts.admin')

@section('title', 'Ver Log - ' . $filename)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/logs-modern.css') }}?v={{ filemtime(public_path('css/admin/logs-modern.css')) }}">
<style>
.log-file-viewer{background:#282c34;color:#abb2bf;padding:1.5rem;border-radius:8px;font-family:'Courier New',monospace;font-size:0.9rem;line-height:1.6;overflow-x:auto;max-height:70vh;white-space:pre-wrap;word-wrap:break-word}.log-line{border-left:3px solid transparent;padding-left:0.75rem;margin-bottom:0.5rem}.log-line-error{border-left-color:#dc3545;background:rgba(220,53,69,0.1)}.log-line-warning{border-left-color:#ffc107;background:rgba(255,193,7,0.1)}.log-line-info{border-left-color:#17a2b8;background:rgba(23,162,184,0.1)}.log-line-debug{border-left-color:#6c757d;background:rgba(108,117,125,0.1)}.log-file-meta{background:linear-gradient(135deg,#f8f9fa,#fff);border:2px solid #e9ecef;border-radius:10px;padding:1.5rem;margin-bottom:1.5rem}.log-file-meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem}.log-file-meta-item{display:flex;flex-direction:column;gap:0.5rem}.log-file-meta-label{font-size:0.85rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}.log-file-meta-value{font-size:1.1rem;color:#722F37;font-weight:600}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header Hero --}}
    <div class="logs-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="logs-header-title">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Ver Archivo de Log
                </h1>
                <p class="logs-header-subtitle">
                    {{ $filename }}
                </p>
            </div>
            <div class="logs-header-actions">
                <a href="{{ route('admin.logs.index') }}" class="logs-action-btn">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
                <a href="{{ route('admin.logs.download', $filename) }}" class="logs-action-btn logs-action-btn-success">
                    <i class="bi bi-download"></i>
                    Descargar
                </a>
                @if($filename !== 'laravel.log')
                <button class="logs-action-btn logs-action-btn-danger" onclick="confirmarEliminar()">
                    <i class="bi bi-trash"></i>
                    Eliminar
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Metadatos del Archivo --}}
    <div class="log-file-meta">
        <div class="log-file-meta-grid">
            <div class="log-file-meta-item">
                <div class="log-file-meta-label">Nombre del Archivo</div>
                <div class="log-file-meta-value">{{ $filename }}</div>
            </div>
            <div class="log-file-meta-item">
                <div class="log-file-meta-label">Tamaño</div>
                <div class="log-file-meta-value">{{ number_format($size / 1024, 2) }} KB</div>
            </div>
            <div class="log-file-meta-item">
                <div class="log-file-meta-label">Última Modificación</div>
                <div class="log-file-meta-value">{{ $lastModified->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="log-file-meta-item">
                <div class="log-file-meta-label">Líneas Totales</div>
                <div class="log-file-meta-value">{{ count(explode("\n", $content)) }}</div>
            </div>
        </div>
    </div>

    {{-- Contenido del Log --}}
    <div class="logs-section-card animate-delay-1">
        <div class="logs-section-header">
            <h5 class="logs-section-title">
                <i class="bi bi-code-square"></i>
                Contenido del Archivo
            </h5>
            <button class="logs-refresh-btn" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
                Recargar
            </button>
        </div>
        <div style="padding:1.5rem">
            <div class="log-file-viewer">@foreach(explode("\n", $content) as $line)
<div class="log-line @if(str_contains($line, 'ERROR')) log-line-error @elseif(str_contains($line, 'WARNING')) log-line-warning @elseif(str_contains($line, 'INFO')) log-line-info @elseif(str_contains($line, 'DEBUG')) log-line-debug @endif">{{ $line }}</div>
@endforeach</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.logsRoutes = {
    delete: '{{ route("admin.logs.delete", $filename) }}',
    index: '{{ route("admin.logs.index") }}'
};
window.logsCSRF = '{{ csrf_token() }}';
window.logsFilename = '{{ $filename }}';

function confirmarEliminar() {
    if (confirm('¿Estás seguro de eliminar el archivo "' + window.logsFilename + '"?\n\nEsta acción no se puede deshacer.')) {
        eliminarArchivo();
    }
}

async function eliminarArchivo() {
    try {
        const response = await fetch(window.logsRoutes.delete, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.logsCSRF,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Archivo eliminado exitosamente');
            window.location.href = window.logsRoutes.index;
        } else {
            alert('Error al eliminar archivo: ' + data.message);
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
    }
}
</script>

<script src="{{ asset('js/admin/logs-modern.js') }}?v={{ filemtime(public_path('js/admin/logs-modern.js')) }}"></script>
@endpush
