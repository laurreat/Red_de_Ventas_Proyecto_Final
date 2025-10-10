@extends('layouts.admin')

@section('title', '- Red de Referidos - ' . $usuario->name)
@section('page-title', 'Red de Referidos')

@push('styles')
<link href="{{ asset('css/admin/referidos-modern.css') }}?v={{ filemtime(public_path('css/admin/referidos-modern.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header del Usuario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="referidos-header animate-fade-in">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="referidos-user-avatar me-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-person-circle text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h4 class="referidos-header-title mb-1">{{ $usuario->name }}</h4>
                            <p class="referidos-header-subtitle mb-1">{{ $usuario->email }}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="referidos-badge-{{ $usuario->rol == 'lider' ? 'lider' : 'vendedor' }}">
                                    @if($usuario->rol == 'lider')
                                        <i class="bi bi-star-fill me-1"></i>
                                    @else
                                        <i class="bi bi-person-fill me-1"></i>
                                    @endif
                                    {{ ucfirst($usuario->rol) }}
                                </span>
                                @if($usuario->codigo_referido)
                                <span class="referidos-badge-code">
                                    <i class="bi bi-qr-code me-1"></i>{{ $usuario->codigo_referido }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('admin.referidos.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Red de Referidos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Usuario -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-1">
                <div class="referidos-stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="referidos-stat-value">{{ $statsUsuario['referidos_directos'] }}</div>
                <div class="referidos-stat-label">Referidos Directos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-2">
                <div class="referidos-stat-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="referidos-stat-value">{{ $statsUsuario['referidos_totales'] }}</div>
                <div class="referidos-stat-label">Red Total</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-3">
                <div class="referidos-stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="referidos-stat-value">${{ number_format($statsUsuario['ventas_referidos'], 0) }}</div>
                <div class="referidos-stat-label">Ventas Red</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="referidos-stat-card animate-scale-in animate-delay-4">
                <div class="referidos-stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="referidos-stat-value">${{ number_format($statsUsuario['comisiones_referidos'], 0) }}</div>
                <div class="referidos-stat-label">Comisiones</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Red Jerárquica -->
        <div class="col-xl-8 mb-4">
            <div class="referidos-table-container animate-fade-in-up animate-delay-2">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--wine);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Estructura de Red (Nivel {{ $statsUsuario['nivel_en_red'] }})
                    </h5>
                </div>
                <div class="p-4">
                    @if(count($redCompleta) > 0)
                        <div class="red-jerarquica">
                            @include('admin.referidos.partials.red-node', ['red' => $redCompleta, 'nivel' => 1])
                        </div>
                    @else
                        <div class="referidos-empty-state">
                            <i class="bi bi-people referidos-empty-icon"></i>
                            <h4 class="referidos-empty-title">Sin Referidos</h4>
                            <p class="referidos-empty-text">Este usuario aún no tiene referidos en su red</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-xl-4 mb-4">
            <div class="referidos-top-card animate-slide-in animate-delay-3">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0 fw-semibold" style="color: var(--wine);">
                        <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="p-3">
                    @if($actividadReciente->count() > 0)
                        @foreach($actividadReciente as $actividad)
                        <div class="referidos-top-item">
                            <div class="d-flex align-items-center">
                                <div class="referidos-user-avatar me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="bi bi-person-plus text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $actividad->name }}</div>
                                    <small class="text-muted">{{ $actividad->email }}</small>
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $actividad->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <span class="referidos-badge-{{ $actividad->rol == 'lider' ? 'lider' : 'vendedor' }}">
                                    {{ ucfirst($actividad->rol) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="referidos-empty-state py-4">
                            <i class="bi bi-clock referidos-empty-icon"></i>
                            <p class="referidos-empty-text">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row">
        <div class="col-12">
            <div class="referidos-filters animate-fade-in-up animate-delay-4">
                <h5 class="mb-3 fw-semibold" style="color: var(--wine);">
                    <i class="bi bi-info-circle me-2"></i>Información Adicional
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                <i class="bi bi-calendar-event me-2"></i>Fecha de Registro
                            </label>
                            <div class="p-2 bg-light rounded">
                                {{ $statsUsuario['fecha_registro']->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                <i class="bi bi-qr-code me-2"></i>Código de Referido
                            </label>
                            <div class="p-2 bg-light rounded">
                                <span class="referidos-badge-code">
                                    {{ $usuario->codigo_referido ?? 'No asignado' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                <i class="bi bi-layers me-2"></i>Nivel en la Red
                            </label>
                            <div class="p-2 bg-light rounded">
                                Nivel {{ $statsUsuario['nivel_en_red'] }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                <i class="bi bi-check-circle me-2"></i>Estado
                            </label>
                            <div class="p-2 bg-light rounded">
                                <span class="referidos-badge-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>Activo
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/referidos-modern.js') }}?v={{ filemtime(public_path('js/admin/referidos-modern.js')) }}"></script>

<script>
// Configuración específica para vista de detalle
window.routes = {
    index: '{{ route("admin.referidos.index") }}',
    show: '{{ route("admin.referidos.show", $usuario->_id) }}'
};

window.usuarioActual = {!! json_encode([
    'id' => $usuario->_id,
    'name' => $usuario->name,
    'email' => $usuario->email,
    'rol' => $usuario->rol,
    'codigo_referido' => $usuario->codigo_referido ?? null
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Vista de detalle de referido cargada:', window.usuarioActual.name);
});
</script>
@endpush
