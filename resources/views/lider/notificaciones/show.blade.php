@extends('layouts.lider')

@section('title', 'Detalle de Notificación')

@push('styles')
    <link href="{{ asset('css/lider/notificaciones-modern.css') }}?v={{ filemtime(public_path('css/lider/notificaciones-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Header -->
    <div class="notif-header fade-in-up">
        <div class="notif-header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @switch($notificacion->tipo)
                            @case('pedido')
                                <div class="notif-stat-icon" style="background: rgba(59,130,246,0.2); color: var(--info); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-cart"></i>
                                </div>
                                @break
                            @case('venta')
                                <div class="notif-stat-icon" style="background: rgba(16,185,129,0.2); color: var(--success); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                @break
                            @case('usuario')
                                <div class="notif-stat-icon" style="background: rgba(139,92,246,0.2); color: #8b5cf6; width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-person"></i>
                                </div>
                                @break
                            @case('comision')
                                <div class="notif-stat-icon" style="background: rgba(245,158,11,0.2); color: var(--warning); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-wallet"></i>
                                </div>
                                @break
                            @case('meta')
                                <div class="notif-stat-icon" style="background: rgba(114,47,55,0.2); color: var(--wine); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-target"></i>
                                </div>
                                @break
                            @case('equipo')
                                <div class="notif-stat-icon" style="background: rgba(16,185,129,0.2); color: var(--success); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-people"></i>
                                </div>
                                @break
                            @case('sistema')
                                <div class="notif-stat-icon" style="background: rgba(107,114,128,0.2); color: var(--gray-500); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-gear"></i>
                                </div>
                                @break
                            @default
                                <div class="notif-stat-icon" style="background: rgba(107,114,128,0.2); color: var(--gray-500); width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-bell"></i>
                                </div>
                        @endswitch
                        <div>
                            <h1 class="notif-title mb-2">{{ $notificacion->titulo }}</h1>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="notif-badge notif-badge-{{ $notificacion->tipo }}">
                                    {{ ucfirst($notificacion->tipo) }}
                                </span>
                                @if(!$notificacion->leida)
                                    <span class="notif-badge notif-badge-nuevo">Sin Leer</span>
                                @else
                                    <span class="notif-badge" style="background: rgba(16,185,129,0.1); color: var(--success);">
                                        Leída
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="notif-actions justify-content-lg-end">
                        <a href="{{ route('lider.notificaciones.index') }}" class="notif-btn notif-btn-outline">
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </a>
                        @if(!$notificacion->leida)
                            <button class="notif-btn notif-btn-white" onclick="marcarLeida('{{ $notificacion->id }}')">
                                <i class="bi bi-check"></i>
                                Marcar Leída
                            </button>
                        @endif
                        <button class="notif-btn notif-btn-outline" onclick="eliminarNotificacion('{{ $notificacion->id }}')">
                            <i class="bi bi-trash"></i>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-lg-8">
            <!-- Mensaje Principal -->
            <div class="notif-list-card fade-in scale-in">
                <div class="notif-list-header">
                    <i class="bi bi-envelope-open"></i>
                    <h2 class="notif-list-title">Mensaje</h2>
                </div>
                <div class="p-4">
                    <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray-700); margin: 0;">
                        {{ $notificacion->mensaje }}
                    </p>
                </div>
            </div>

            <!-- Datos Adicionales -->
            @if($notificacion->datos_adicionales && count($notificacion->datos_adicionales) > 0)
            <div class="notif-list-card fade-in scale-in animate-delay-1" style="margin-top: 1.5rem;">
                <div class="notif-list-header">
                    <i class="bi bi-info-circle"></i>
                    <h2 class="notif-list-title">Información Adicional</h2>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @foreach($notificacion->datos_adicionales as $key => $value)
                        <div class="col-md-6">
                            <div style="background: var(--gray-50); border-radius: 12px; padding: 1.25rem; border: 1px solid var(--gray-200);">
                                <label class="notif-form-label mb-2">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </label>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--gray-900);">
                                    @if(is_array($value))
                                        {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Actividad -->
            <div class="notif-list-card fade-in scale-in animate-delay-2" style="margin-top: 1.5rem;">
                <div class="notif-list-header">
                    <i class="bi bi-clock-history"></i>
                    <h2 class="notif-list-title">Historial</h2>
                </div>
                <div class="p-4">
                    <div class="timeline">
                        <div class="timeline-item" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--info); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 0.938rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.25rem 0;">
                                    Notificación Creada
                                </h4>
                                <p style="font-size: 0.875rem; color: var(--gray-500); margin: 0;">
                                    {{ $notificacion->created_at->format('d/m/Y H:i:s') }}
                                    <span style="color: var(--gray-400);">•</span>
                                    {{ $notificacion->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if($notificacion->leida && $notificacion->fecha_lectura)
                        <div class="timeline-item" style="display: flex; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: rgba(16,185,129,0.1); color: var(--success); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 0.938rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.25rem 0;">
                                    Marcada como Leída
                                </h4>
                                <p style="font-size: 0.875rem; color: var(--gray-500); margin: 0;">
                                    {{ $notificacion->fecha_lectura->format('d/m/Y H:i:s') }}
                                    <span style="color: var(--gray-400);">•</span>
                                    {{ $notificacion->fecha_lectura->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Info Rápida -->
            <div class="notif-list-card fade-in scale-in animate-delay-1">
                <div class="notif-list-header">
                    <i class="bi bi-speedometer2"></i>
                    <h2 class="notif-list-title">Información</h2>
                </div>
                <div class="p-3">
                    <div style="margin-bottom: 1.25rem;">
                        <label class="notif-form-label mb-2">Estado</label>
                        <div>
                            @if($notificacion->leida)
                                <span class="notif-badge" style="background: rgba(16,185,129,0.1); color: var(--success); padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Leída
                                </span>
                            @else
                                <span class="notif-badge notif-badge-nuevo" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Sin Leer
                                </span>
                            @endif
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label class="notif-form-label mb-2">Tipo</label>
                        <div>
                            <span class="notif-badge notif-badge-{{ $notificacion->tipo }}" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                {{ ucfirst($notificacion->tipo) }}
                            </span>
                        </div>
                    </div>

                    @if($notificacion->canal)
                    <div style="margin-bottom: 1.25rem;">
                        <label class="notif-form-label mb-2">Canal</label>
                        <div style="font-size: 0.938rem; font-weight: 500; color: var(--gray-700);">
                            <i class="bi bi-broadcast me-1"></i>
                            {{ ucfirst($notificacion->canal) }}
                        </div>
                    </div>
                    @endif

                    <div style="margin-bottom: 1.25rem;">
                        <label class="notif-form-label mb-2">Creada</label>
                        <div style="font-size: 0.938rem; font-weight: 500; color: var(--gray-700);">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $notificacion->created_at->format('d/m/Y') }}
                        </div>
                        <div style="font-size: 0.813rem; color: var(--gray-500); margin-top: 0.25rem;">
                            {{ $notificacion->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if($notificacion->leida && $notificacion->fecha_lectura)
                    <div>
                        <label class="notif-form-label mb-2">Leída</label>
                        <div style="font-size: 0.938rem; font-weight: 500; color: var(--gray-700);">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $notificacion->fecha_lectura->format('d/m/Y') }}
                        </div>
                        <div style="font-size: 0.813rem; color: var(--gray-500); margin-top: 0.25rem;">
                            {{ $notificacion->fecha_lectura->diffForHumans() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="notif-list-card fade-in scale-in animate-delay-2" style="margin-top: 1.5rem;">
                <div class="notif-list-header">
                    <i class="bi bi-lightning"></i>
                    <h2 class="notif-list-title">Acciones Rápidas</h2>
                </div>
                <div class="p-3">
                    @if(!$notificacion->leida)
                    <button class="notif-action-btn notif-action-btn-success"
                            style="width: 100%; justify-content: center; margin-bottom: 0.75rem; padding: 0.875rem;"
                            onclick="marcarLeida('{{ $notificacion->id }}')">
                        <i class="bi bi-check-circle"></i>
                        Marcar como Leída
                    </button>
                    @endif

                    <button class="notif-action-btn notif-action-btn-danger"
                            style="width: 100%; justify-content: center; padding: 0.875rem;"
                            onclick="eliminarNotificacion('{{ $notificacion->id }}')">
                        <i class="bi bi-trash"></i>
                        Eliminar Notificación
                    </button>

                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                        <a href="{{ route('lider.notificaciones.index') }}"
                           style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; background: var(--gray-50); border-radius: 10px; text-decoration: none; color: var(--gray-700); font-weight: 500; transition: all 0.2s;"
                           onmouseover="this.style.background='var(--gray-100)'"
                           onmouseout="this.style.background='var(--gray-50)'">
                            <i class="bi bi-list"></i>
                            Ver Todas las Notificaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.notificacionesRoutes = {
    marcarLeida: '{{ route("lider.notificaciones.marcar-leida", ":id") }}',
    eliminar: '{{ route("lider.notificaciones.eliminar", ":id") }}',
    index: '{{ route("lider.notificaciones.index") }}'
};
window.notificacionesCSRF = '{{ csrf_token() }}';

// Extender el manager para redirección después de eliminar
document.addEventListener('DOMContentLoaded', function() {
    const originalEliminar = window.notificacionesManager.eliminarNotificacion;
    window.notificacionesManager.eliminarNotificacion = async function(id) {
        this.showModal('danger','Eliminar Notificación','¿Estás seguro de eliminar esta notificación? Esta acción no se puede deshacer.',[{text:'Cancelar',type:'secondary',action:()=>this.closeModal()},{text:'Eliminar',type:'primary',action:async()=>{this.closeModal();const url=this.routes.eliminar?.replace(':id',id);if(!url)return this.showToast('error','URL no configurada');try{this.showLoading();const response=await fetch(url,{method:'DELETE',headers:{'X-CSRF-TOKEN':this.csrf,'Content-Type':'application/json'}});const data=await response.json();this.hideLoading();if(data.success){this.showToast('success','Notificación eliminada');setTimeout(()=>window.location.href=this.routes.index,1000)}else{this.showToast('error',data.message||'Error al eliminar')}}catch(error){this.hideLoading();console.error('Error:',error);this.showToast('error','Error de conexión')}}}]);
    };
});
</script>
<script src="{{ asset('js/lider/notificaciones-modern.js') }}?v={{ filemtime(public_path('js/lider/notificaciones-modern.js')) }}"></script>
@endpush
