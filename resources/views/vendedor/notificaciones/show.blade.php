@extends('layouts.vendedor')

@section('title', 'Detalle de Notificación')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/notificaciones-modern.css') }}?v={{ filemtime(public_path('css/vendedor/notificaciones-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- Header Hero --}}
    <div class="notif-header fade-in-up">
        <div class="notif-header-content">
            <div class="notif-header-info">
                <h1>
                    <i class="bi bi-bell"></i>
                    Detalle de Notificación
                </h1>
                <p>Información completa de la notificación</p>
            </div>
            <div class="notif-header-actions">
                <a href="{{ route('vendedor.notificaciones.index') }}" class="notif-action-btn notif-action-btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                @if(!$notificacion->leida)
                    <button onclick="marcarComoLeida()" class="notif-action-btn notif-action-btn-primary" id="markReadBtn">
                        <i class="bi bi-check2"></i> Marcar como leída
                    </button>
                @endif
                <button onclick="eliminarNotificacion()" class="notif-action-btn notif-action-btn-secondary">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- Detalle de Notificación --}}
    <div class="notif-detail-container scale-in">
        <div class="notif-detail-header">
            <div class="notif-icon-wrapper type-{{ $notificacion->tipo ?? 'sistema' }}" style="width:64px;height:64px;font-size:2rem">
                @php
                    $icons = [
                        'pedido' => 'bi-cart-check-fill',
                        'venta' => 'bi-currency-dollar',
                        'comision' => 'bi-cash-coin',
                        'pago' => 'bi-wallet2',
                        'sistema' => 'bi-gear-fill',
                        'alerta' => 'bi-exclamation-triangle-fill',
                        'urgente' => 'bi-exclamation-circle-fill',
                        'importante' => 'bi-star-fill',
                        'mensaje' => 'bi-chat-dots-fill'
                    ];
                    $icon = $icons[$notificacion->tipo ?? 'sistema'] ?? 'bi-bell-fill';
                @endphp
                <i class="bi {{ $icon }}"></i>
            </div>

            <div style="flex:1">
                <h2 style="font-size:1.75rem;font-weight:700;color:var(--gray-900);margin:0 0 0.5rem">
                    {{ $notificacion->titulo }}
                </h2>

                <div class="notif-meta" style="margin-top:1rem">
                    <span class="notif-meta-item">
                        <i class="bi bi-clock"></i>
                        {{ $notificacion->created_at->format('d/m/Y H:i') }}
                        <span style="opacity:0.7">({{ $notificacion->created_at->diffForHumans() }})</span>
                    </span>
                    <span class="notif-meta-item">
                        <i class="bi bi-tag"></i>
                        <span class="notif-badge notif-badge-{{ $notificacion->tipo === 'pedido' || $notificacion->tipo === 'venta' ? 'success' : ($notificacion->tipo === 'comision' || $notificacion->tipo === 'pago' ? 'warning' : ($notificacion->tipo === 'urgente' || $notificacion->tipo === 'importante' ? 'danger' : 'info')) }}">
                            {{ ucfirst($notificacion->tipo ?? 'sistema') }}
                        </span>
                    </span>
                    @if(!$notificacion->leida)
                        <span class="notif-badge notif-badge-warning">
                            <i class="bi bi-bell-fill"></i> No leída
                        </span>
                    @else
                        <span class="notif-badge notif-badge-success">
                            <i class="bi bi-check-circle-fill"></i> Leída
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="notif-detail-content">
            <h4 style="font-size:1.125rem;font-weight:600;color:var(--gray-800);margin:0 0 1rem">
                <i class="bi bi-envelope-open"></i> Mensaje
            </h4>
            <p style="font-size:1rem;line-height:1.8;color:var(--gray-700);margin:0 0 2rem;white-space:pre-wrap">{{ $notificacion->mensaje }}</p>

            @if($notificacion->datos_adicionales && count($notificacion->datos_adicionales) > 0)
                <h4 style="font-size:1.125rem;font-weight:600;color:var(--gray-800);margin:2rem 0 1rem">
                    <i class="bi bi-info-circle"></i> Información Adicional
                </h4>
                <div style="background:var(--gray-50);border-radius:12px;padding:1.5rem">
                    @foreach($notificacion->datos_adicionales as $key => $value)
                        <div style="display:flex;padding:0.75rem 0;border-bottom:1px solid var(--gray-200)">
                            <strong style="min-width:200px;color:var(--gray-700)">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            <span style="color:var(--gray-600)">
                                @if(is_array($value))
                                    {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($notificacion->leida && $notificacion->fecha_lectura)
                <div style="margin-top:2rem;padding:1rem;background:rgba(16,185,129,0.05);border-left:4px solid var(--success);border-radius:8px">
                    <strong style="color:var(--success)"><i class="bi bi-check-circle-fill"></i> Leída el:</strong>
                    <span style="color:var(--gray-700);margin-left:0.5rem">
                        {{ $notificacion->fecha_lectura->format('d/m/Y H:i') }}
                        ({{ $notificacion->fecha_lectura->diffForHumans() }})
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="notif-toast-container"></div>

<script>
const notificacionId = '{{ $notificacion->_id }}';

function marcarComoLeida() {
    notificacionesManager.showLoading();

    fetch(`/vendedor/notificaciones/${notificacionId}/marcar-leida`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        notificacionesManager.hideLoading();
        if(data.success) {
            notificacionesManager.showToast('Notificación marcada como leída', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            notificacionesManager.showToast(data.message || 'Error', 'danger');
        }
    })
    .catch(err => {
        notificacionesManager.hideLoading();
        notificacionesManager.showToast('Error de conexión', 'danger');
        console.error(err);
    });
}

function eliminarNotificacion() {
    notificacionesManager.confirmAction(
        '¿Estás seguro de eliminar esta notificación?',
        () => {
            notificacionesManager.showLoading();

            fetch(`/vendedor/notificaciones/${notificacionId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                notificacionesManager.hideLoading();
                if(data.success) {
                    notificacionesManager.showToast('Notificación eliminada', 'success');
                    setTimeout(() => {
                        window.location.href = '/vendedor/notificaciones';
                    }, 1000);
                } else {
                    notificacionesManager.showToast(data.message || 'Error', 'danger');
                }
            })
            .catch(err => {
                notificacionesManager.hideLoading();
                notificacionesManager.showToast('Error de conexión', 'danger');
                console.error(err);
            });
        },
        'Eliminar notificación'
    );
}
</script>

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/notificaciones-modern.js') }}?v={{ filemtime(public_path('js/vendedor/notificaciones-modern.js')) }}"></script>
@endpush
