@extends('layouts.lider')

@section('title', ' - Solicitar Pago de Comisiones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/comisiones-modern.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="comisiones-header fade-in-up">
        <div class="comisiones-header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="comisiones-title">
                        <i class="bi bi-send-fill"></i>
                        Solicitar Pago de Comisiones
                    </h1>
                    <p class="comisiones-subtitle">
                        Solicita el retiro de tus comisiones disponibles de manera rápida y segura
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="comisiones-actions">
                        <a href="{{ route('lider.comisiones.index') }}" class="comisiones-action-btn">
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de Solicitud -->
        <div class="col-lg-8 mb-4">
            <div class="comisiones-filter-card fade-in-up">
                <h3 class="comisiones-filter-title">
                    <i class="bi bi-cash-stack"></i>
                    Datos de la Solicitud
                </h3>

                <!-- Alerta Informativa -->
                <div class="comisiones-alert info">
                    <i class="bi bi-info-circle-fill comisiones-alert-icon"></i>
                    <div class="comisiones-alert-content">
                        <div class="comisiones-alert-title">Información Importante</div>
                        <div class="comisiones-alert-message">
                            El monto mínimo para solicitar es de <strong>$50,000 COP</strong>.
                            El procesamiento de la solicitud toma entre <strong>24 a 48 horas hábiles</strong>.
                        </div>
                    </div>
                </div>

                <div class="comisiones-filter-card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('lider.comisiones.procesar') }}" method="POST">
                        @csrf

                        <!-- Monto Disponible -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Comisiones Disponibles:</strong>
                                    ${{ number_format(auth()->user()->comisiones_disponibles, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Monto a Solicitar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monto" class="form-label">Monto a Solicitar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('monto') is-invalid @enderror"
                                           id="monto"
                                           name="monto"
                                           value="{{ old('monto') }}"
                                           min="50000"
                                           max="{{ auth()->user()->comisiones_disponibles }}"
                                           step="1000"
                                           required>
                                </div>
                                <small class="form-text text-muted">Monto mínimo: $50,000</small>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="metodo_pago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                                <select class="form-control @error('metodo_pago') is-invalid @enderror"
                                        id="metodo_pago"
                                        name="metodo_pago"
                                        required>
                                    <option value="">Selecciona un método</option>
                                    @foreach($metodosPago as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('metodo_pago') == $valor ? 'selected' : '' }}>
                                            {{ $etiqueta }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('metodo_pago')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Datos de Pago -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="datos_pago" class="form-label">Datos de Pago <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('datos_pago') is-invalid @enderror"
                                          id="datos_pago"
                                          name="datos_pago"
                                          rows="4"
                                          placeholder="Ingresa los datos necesarios para el pago (número de cuenta, número de teléfono, etc.)"
                                          required>{{ old('datos_pago') }}</textarea>
                                <small class="form-text text-muted">
                                    <div id="datos_ayuda">
                                        <div data-metodo="transferencia" class="ayuda-metodo" style="display: none;">
                                            <strong>Transferencia Bancaria:</strong> Incluye banco, tipo de cuenta, número de cuenta y nombre del titular.
                                        </div>
                                        <div data-metodo="nequi" class="ayuda-metodo" style="display: none;">
                                            <strong>Nequi:</strong> Número de teléfono asociado a la cuenta Nequi.
                                        </div>
                                        <div data-metodo="daviplata" class="ayuda-metodo" style="display: none;">
                                            <strong>Daviplata:</strong> Número de teléfono asociado a la cuenta Daviplata.
                                        </div>
                                        <div data-metodo="efectivo" class="ayuda-metodo" style="display: none;">
                                            <strong>Efectivo:</strong> Dirección completa para la entrega del dinero.
                                        </div>
                                    </div>
                                </small>
                                @error('datos_pago')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="3"
                                          placeholder="Cualquier información adicional relevante para el pago">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>
                                    Enviar Solicitud
                                </button>
                                <a href="{{ route('lider.comisiones.index') }}" class="btn btn-secondary ms-2">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-lg-4">
            <!-- Información del Proceso -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Información del Proceso</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="bi bi-clock me-1"></i>
                            Tiempo de Procesamiento
                        </h6>
                        <p class="small text-muted mb-0">
                            Las solicitudes se procesan en un plazo de 24 a 48 horas hábiles.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="bi bi-shield-check me-1"></i>
                            Seguridad
                        </h6>
                        <p class="small text-muted mb-0">
                            Todos los datos de pago son verificados antes del procesamiento.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="bi bi-cash-stack me-1"></i>
                            Monto Mínimo
                        </h6>
                        <p class="small text-muted mb-0">
                            El monto mínimo para solicitudes es de $50,000 COP.
                        </p>
                    </div>

                    <div>
                        <h6 class="text-primary">
                            <i class="bi bi-telephone me-1"></i>
                            Soporte
                        </h6>
                        <p class="small text-muted mb-0">
                            Si tienes dudas, contacta a soporte al WhatsApp: +57 300 123 4567
                        </p>
                    </div>
                </div>
            </div>

            <!-- Historial Reciente -->
            @if($historialSolicitudes->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Historial Reciente</h6>
                    </div>
                    <div class="card-body">
                        @foreach($historialSolicitudes as $solicitud)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <div>
                                    <div class="font-weight-bold text-primary">
                                        ${{ number_format($solicitud->monto, 0, ',', '.') }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $solicitud->created_at->format('d/m/Y') }} - {{ ucfirst($solicitud->metodo_pago) }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $solicitud->estado == 'pagado' ? 'success' : ($solicitud->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($solicitud->estado) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodoPagoSelect = document.getElementById('metodo_pago');
    const ayudaMetodos = document.querySelectorAll('.ayuda-metodo');

    function mostrarAyuda() {
        const metodoSeleccionado = metodoPagoSelect.value;

        // Ocultar todas las ayudas
        ayudaMetodos.forEach(function(ayuda) {
            ayuda.style.display = 'none';
        });

        // Mostrar la ayuda correspondiente
        if (metodoSeleccionado) {
            const ayudaActiva = document.querySelector(`[data-metodo="${metodoSeleccionado}"]`);
            if (ayudaActiva) {
                ayudaActiva.style.display = 'block';
            }
        }
    }

    // Mostrar ayuda al cambiar método de pago
    metodoPagoSelect.addEventListener('change', mostrarAyuda);

    // Mostrar ayuda inicial si hay un método seleccionado
    mostrarAyuda();

    // Validación del monto
    const montoInput = document.getElementById('monto');
    const maxMonto = {{ auth()->user()->comisiones_disponibles }};

    montoInput.addEventListener('input', function() {
        const valor = parseInt(this.value);

        if (valor > maxMonto) {
            this.setCustomValidity(`El monto no puede ser mayor a $${maxMonto.toLocaleString()}`);
        } else if (valor < 50000) {
            this.setCustomValidity('El monto mínimo es $50,000');
        } else {
            this.setCustomValidity('');
        }
    });

    // Formatear el monto mientras se escribe
    montoInput.addEventListener('blur', function() {
        if (this.value) {
            const valor = parseInt(this.value);
            if (!isNaN(valor)) {
                this.value = valor;
            }
        }
    });
});
</script>
@endpush
@endsection