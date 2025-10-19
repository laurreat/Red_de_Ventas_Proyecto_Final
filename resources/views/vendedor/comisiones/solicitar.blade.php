@extends('layouts.vendedor')

@section('title', 'Solicitar Retiro')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/comisiones-modern.css') }}?v={{ time() }}">
@endpush

@section('content')
<!-- Header -->
<div class="comisiones-header fade-in-up">
    <div class="comisiones-header-content">
        <h1 class="comisiones-header-title">
            <i class="bi bi-cash-stack"></i>
            Solicitar Retiro de Comisiones
        </h1>
        <p class="comisiones-header-subtitle">
            <i class="bi bi-bank me-2"></i>
            Retira tus comisiones disponibles
        </p>
    </div>
</div>

<!-- Disponible para retiro -->
<div class="row g-4 mb-4">
    <div class="col-md-6 mx-auto">
        <div class="comisiones-stat-card stat-disponible fade-in-up animate-delay-1" style="opacity:0">
            <div class="comisiones-stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($comisionesDisponibles, 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Disponible para Retiro</div>
        </div>
    </div>
</div>

@if($comisionesDisponibles >= 50000)
<!-- Formulario de solicitud -->
<div class="comisiones-solicitud-form fade-in-up animate-delay-2" style="opacity:0">
    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--dark);">
        <i class="bi bi-file-earmark-text"></i> Datos de la Solicitud
    </h3>

    <form id="solicitud-retiro-form" method="POST" action="{{ route('vendedor.comisiones.procesar') }}">
        @csrf

        <div class="comisiones-form-group">
            <label class="comisiones-form-label">Monto a Retirar</label>
            <input type="number" name="monto" class="comisiones-form-input" 
                   min="50000" 
                   max="{{ $comisionesDisponibles }}"
                   step="1000"
                   required
                   placeholder="Ingrese el monto">
            <div class="comisiones-form-hint">
                Mínimo: $50,000 COP | Máximo: ${{ number_format($comisionesDisponibles, 0, ',', '.') }}
            </div>
        </div>

        <div class="comisiones-form-group">
            <label class="comisiones-form-label">Método de Pago</label>
            <select name="metodo_pago" class="comisiones-form-select" required>
                <option value="">Seleccione un método</option>
                <option value="transferencia">Transferencia Bancaria</option>
                <option value="nequi">Nequi</option>
                <option value="daviplata">Daviplata</option>
            </select>
        </div>

        <div class="comisiones-form-group">
            <label class="comisiones-form-label">Datos de Pago</label>
            <textarea name="datos_pago" class="comisiones-form-textarea" required
                      placeholder="Ingrese los datos completos:&#10;- Número de cuenta / celular&#10;- Banco (si aplica)&#10;- Tipo de cuenta (si aplica)&#10;- Nombre del titular"></textarea>
            <div class="comisiones-form-hint">
                Proporcione todos los datos necesarios para procesar el pago
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="comisiones-btn-submit">
                <i class="bi bi-send"></i>
                Enviar Solicitud
            </button>
            <a href="{{ route('vendedor.comisiones.index') }}" class="comisiones-btn-back">
                <i class="bi bi-arrow-left"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>
@else
<!-- No hay suficiente para retiro -->
<div class="comisiones-empty fade-in-up animate-delay-2" style="opacity:0">
    <div class="comisiones-empty-icon">
        <i class="bi bi-exclamation-triangle"></i>
    </div>
    <h3 class="comisiones-empty-title">Monto insuficiente</h3>
    <p class="comisiones-empty-text">
        Necesitas al menos $50,000 COP en comisiones disponibles para solicitar un retiro.
        <br>
        Actualmente tienes: <strong style="color: var(--wine);">${{ number_format($comisionesDisponibles, 0, ',', '.') }}</strong>
    </p>
    <a href="{{ route('vendedor.comisiones.index') }}" class="comisiones-action-btn-view" style="margin-top: 1rem;">
        <i class="bi bi-arrow-left"></i>
        Volver a Comisiones
    </a>
</div>
@endif

<!-- Historial de solicitudes -->
@if(isset($solicitudesRetiro) && $solicitudesRetiro->count() > 0)
<div class="comisiones-solicitud-form fade-in-up animate-delay-3" style="opacity:0; margin-top: 2rem;">
    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--dark);">
        <i class="bi bi-clock-history"></i> Solicitudes Recientes
    </h3>

    <div class="comisiones-table-container">
        <table class="comisiones-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudesRetiro as $solicitud)
                <tr>
                    <td>{{ $solicitud->created_at ? $solicitud->created_at->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <strong style="color: var(--wine);">
                            ${{ number_format($solicitud->monto ?? 0, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>{{ $solicitud->metodo_pago_formateado ?? ucfirst($solicitud->metodo_pago ?? 'N/A') }}</td>
                    <td>
                        @if($solicitud->estado == 'pendiente')
                            <span class="comisiones-badge comisiones-badge-pendiente">
                                <i class="bi bi-clock"></i> Pendiente
                            </span>
                        @elseif($solicitud->estado == 'aprobado')
                            <span class="comisiones-badge comisiones-badge-proceso">
                                <i class="bi bi-hourglass"></i> Aprobado
                            </span>
                        @elseif($solicitud->estado == 'pagado')
                            <span class="comisiones-badge comisiones-badge-pagado">
                                <i class="bi bi-check-circle"></i> Pagado
                            </span>
                        @elseif($solicitud->estado == 'rechazado')
                            <span class="comisiones-badge comisiones-badge-rechazado">
                                <i class="bi bi-x-circle"></i> Rechazado
                            </span>
                        @else
                            <span class="comisiones-badge comisiones-badge-rechazado">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/comisiones-modern.js') }}?v={{ time() }}"></script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        comisionesManager.showToast('{{ session('success') }}', 'success', 'Éxito');
    });
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        comisionesManager.showToast('{{ session('error') }}', 'error', 'Error');
    });
</script>
@endif
@endpush
