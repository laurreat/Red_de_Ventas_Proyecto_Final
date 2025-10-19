@extends('layouts.vendedor')

@section('title', 'Detalle de Comisión')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/comisiones-modern.css') }}?v={{ filemtime(public_path('css/vendedor/comisiones-modern.css')) }}">
@endpush

@section('content')
<!-- Header -->
<div class="comisiones-show-header fade-in-up">
    <h1 class="comisiones-show-title">
        <i class="bi bi-receipt"></i>
        Detalle de Comisión
    </h1>
    <p class="comisiones-show-subtitle">
        Información completa de la comisión
    </p>
</div>

<!-- Content -->
<div class="comisiones-show-content fade-in-up animate-delay-1" style="opacity:0">
    <div class="comisiones-show-grid">
        <!-- Columna Izquierda -->
        <div>
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Monto de Comisión</div>
                <div class="comisiones-detail-monto">${{ number_format($comision->monto, 0, ',', '.') }}</div>
            </div>

            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Estado</div>
                <div class="comisiones-detail-value">
                    @if($comision->estado == 'pendiente')
                        <span class="comisiones-badge comisiones-badge-pendiente">
                            <i class="bi bi-clock"></i> Pendiente
                        </span>
                    @elseif($comision->estado == 'en_proceso')
                        <span class="comisiones-badge comisiones-badge-proceso">
                            <i class="bi bi-hourglass"></i> En Proceso
                        </span>
                    @elseif($comision->estado == 'pagado')
                        <span class="comisiones-badge comisiones-badge-pagado">
                            <i class="bi bi-check-circle"></i> Pagado
                        </span>
                    @else
                        <span class="comisiones-badge comisiones-badge-rechazado">
                            {{ ucfirst($comision->estado) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Tipo de Comisión</div>
                <div class="comisiones-detail-value">
                    @if($comision->tipo == 'venta_directa')
                        <span class="comisiones-badge comisiones-badge-venta">
                            <i class="bi bi-cart-check"></i> Venta Directa
                        </span>
                    @else
                        <span class="comisiones-badge comisiones-badge-referido">
                            <i class="bi bi-people"></i> Referido
                        </span>
                    @endif
                </div>
            </div>

            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Porcentaje</div>
                <div class="comisiones-detail-value">{{ $comision->porcentaje ?? 0 }}%</div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div>
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Fecha de Registro</div>
                <div class="comisiones-detail-value">{{ $comision->created_at->format('d/m/Y H:i') }}</div>
            </div>

            @if($comision->fecha_pago)
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Fecha de Pago</div>
                <div class="comisiones-detail-value">{{ $comision->fecha_pago->format('d/m/Y') }}</div>
            </div>
            @endif

            @if(isset($comision->pedido_data))
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Relacionado con</div>
                <div class="comisiones-detail-value">
                    Pedido #{{ $comision->pedido_data['id'] ?? 'N/A' }}
                    <br>
                    <small style="color: var(--secondary);">
                        Monto del pedido: ${{ number_format($comision->pedido_data['total'] ?? 0, 0, ',', '.') }}
                    </small>
                </div>
            </div>
            @endif

            @if(isset($comision->referido_data))
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Referido</div>
                <div class="comisiones-detail-value">
                    {{ $comision->referido_data['name'] ?? 'N/A' }}
                    <br>
                    <small style="color: var(--secondary);">
                        {{ $comision->referido_data['email'] ?? '' }}
                    </small>
                </div>
            </div>
            @endif

            @if($comision->metodo_pago)
            <div class="comisiones-detail-group">
                <div class="comisiones-detail-label">Método de Pago</div>
                <div class="comisiones-detail-value">{{ ucfirst($comision->metodo_pago) }}</div>
            </div>
            @endif
        </div>
    </div>

    @if(isset($comision->detalles_calculo) && count($comision->detalles_calculo) > 0)
    <div style="padding: 2rem; border-top: 2px solid var(--border);">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: var(--dark);">
            <i class="bi bi-calculator"></i> Detalles del Cálculo
        </h3>
        <div style="background: var(--light); padding: 1.5rem; border-radius: 8px;">
            @foreach($comision->detalles_calculo as $key => $value)
            <div style="margin-bottom: 0.5rem;">
                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Actions -->
<div class="comisiones-show-actions fade-in-up animate-delay-2" style="opacity:0">
    <a href="{{ route('vendedor.comisiones.index') }}" class="comisiones-btn-back">
        <i class="bi bi-arrow-left"></i>
        Volver al Listado
    </a>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/comisiones-modern.js') }}?v={{ filemtime(public_path('js/vendedor/comisiones-modern.js')) }}"></script>
@endpush
