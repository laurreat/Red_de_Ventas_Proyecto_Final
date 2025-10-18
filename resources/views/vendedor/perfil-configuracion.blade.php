@extends('layouts.vendedor')

@section('title', 'Configuración')

@section('page-title', 'Configuración')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/perfil-modern.css') }}?v={{ filemtime(public_path('css/vendedor/perfil-modern.css')) }}">
<style>
.perfil-config-section-header{margin-bottom:1.5rem}
.perfil-config-section-header h3{font-size:1.25rem;font-weight:600;color:var(--gray-900);margin:0 0 .5rem;display:flex;align-items:center;gap:.75rem}
.perfil-config-section-header p{color:var(--gray-600);font-size:.938rem;margin:0}
.perfil-config-actions{display:flex;justify-content:flex-end;gap:1rem;padding:2rem;background:var(--gray-50);border-radius:16px;margin-top:2rem}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    
    {{-- Header Hero --}}
    <div class="perfil-header fade-in-up">
        <div class="perfil-header-content" style="justify-content: space-between;">
            <div>
                <h1 style="margin-bottom: 0.5rem;"><i class="bi bi-gear"></i> Configuración</h1>
                <p style="opacity: 0.9; margin: 0;">Personaliza tu experiencia y preferencias</p>
            </div>
            <a href="{{ route('vendedor.perfil.index') }}" class="perfil-action-btn perfil-action-btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Perfil
            </a>
        </div>
    </div>
    
    {{-- Configuración Container --}}
    <div class="perfil-config-container scale-in">

        {{-- Privacidad y Visibilidad --}}
        <div class="perfil-config-section fade-in-up animate-delay-1">
            <div class="perfil-config-section-header">
                <h3><i class="bi bi-shield-check"></i> Privacidad y Visibilidad</h3>
                <p>Controla quién puede ver tu información y perfil</p>
            </div>

            <div class="perfil-config-item">
                <div class="perfil-config-info">
                    <h5>Perfil Público</h5>
                    <p>Permitir que otros vendedores en la red vean tu perfil básico y estadísticas</p>
                </div>
                <label class="perfil-switch">
                    <input type="checkbox" id="perfil_publico" {{ ($vendedor->perfil_publico ?? false) ? 'checked' : '' }}>
                    <span class="perfil-switch-slider"></span>
                </label>
            </div>

            <div class="perfil-config-item">
                <div class="perfil-config-info">
                    <h5>Mostrar Teléfono Públicamente</h5>
                    <p>Hacer visible tu número de teléfono a otros usuarios y clientes</p>
                </div>
                <label class="perfil-switch">
                    <input type="checkbox" id="mostrar_telefono" {{ ($vendedor->mostrar_telefono ?? false) ? 'checked' : '' }}>
                    <span class="perfil-switch-slider"></span>
                </label>
            </div>

            <div class="perfil-config-item">
                <div class="perfil-config-info">
                    <h5>Compartir Estadísticas</h5>
                    <p>Permitir que tu líder y equipo vean tus estadísticas de desempeño</p>
                </div>
                <label class="perfil-switch">
                    <input type="checkbox" id="mostrar_stats" {{ ($vendedor->mostrar_stats ?? true) ? 'checked' : '' }}>
                    <span class="perfil-switch-slider"></span>
                </label>
            </div>
        </div>
        
        {{-- Botones de Acción --}}
        <div class="perfil-config-actions fade-in-up animate-delay-6">
            <button type="button" class="perfil-btn perfil-btn-secondary" onclick="resetearConfiguracion()">
                <i class="bi bi-arrow-counterclockwise"></i> Restaurar Valores Predeterminados
            </button>
            <button type="button" class="perfil-btn perfil-btn-primary" onclick="guardarConfiguracion()">
                <i class="bi bi-check-circle"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="perfil-toast-container"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        perfilManager.showToast('{{ session('error') }}', 'danger');
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/perfil-modern.js') }}?v={{ filemtime(public_path('js/vendedor/perfil-modern.js')) }}"></script>
<script>
function guardarConfiguracion() {
    perfilManager.showLoading('Guardando configuración...');

    const configuracion = {
        perfil_publico: document.getElementById('perfil_publico').checked,
        mostrar_telefono: document.getElementById('mostrar_telefono').checked,
        mostrar_stats: document.getElementById('mostrar_stats').checked
    };

    fetch('{{ route('vendedor.perfil.update') }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(configuracion)
    })
    .then(res => res.json())
    .then(data => {
        perfilManager.hideLoading();
        if(data.success) {
            perfilManager.showToast('Configuración guardada exitosamente', 'success');
            // Recargar después de 1 segundo
            setTimeout(() => location.reload(), 1000);
        } else {
            perfilManager.showToast(data.message || 'Error al guardar configuración', 'danger');
        }
    })
    .catch(error => {
        perfilManager.hideLoading();
        perfilManager.showToast('Error de conexión', 'danger');
        console.error('Error:', error);
    });
}

function resetearConfiguracion() {
    perfilManager.confirmAction(
        '¿Estás seguro de restaurar todos los valores a su configuración predeterminada?',
        () => {
            // Valores predeterminados
            document.getElementById('perfil_publico').checked = false;
            document.getElementById('mostrar_telefono').checked = false;
            document.getElementById('mostrar_stats').checked = true;

            perfilManager.showToast('Configuración restablecida. Guarda los cambios para aplicar.', 'info');
        },
        'Restablecer Configuración'
    );
}
</script>
@endpush
