@extends('layouts.vendedor')

@section('title', 'Nueva Venta')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/nueva-venta-modern.css') }}?v={{ filemtime(public_path('css/vendedor/nueva-venta-modern.css')) }}">
@endpush

@section('content')
<!-- Header -->
<div class="venta-header fade-in-up">
    <h1 class="venta-header-title">
        🛒 Nueva Venta
    </h1>
    <p class="venta-header-subtitle">Registra una nueva venta de manera rápida y eficiente</p>
</div>

<form id="nueva-venta-form" method="POST" action="{{ route('vendedor.ventas.store') }}">
    @csrf
    <input type="hidden" name="cliente_id" id="cliente_id">

    <!-- Cliente Card -->
    <div class="venta-form-card fade-in-up animate-delay-1">
        <div class="venta-form-card-header">
            <div class="venta-form-card-icon">👤</div>
            <h3 class="venta-form-card-title">Información del Cliente</h3>
        </div>

        <div class="venta-form-group">
            <label class="venta-form-label">
                Buscar Cliente <span class="venta-form-required">*</span>
            </label>
            <div class="venta-search-container">
                <input type="text" id="cliente-search" class="venta-search-input" placeholder="Buscar por nombre, email o teléfono..." autocomplete="off" required>
                <span class="venta-search-icon">🔍</span>
                <div id="cliente-search-results" class="venta-search-results"></div>
            </div>
        </div>
    </div>

    <!-- Productos Card -->
    <div class="venta-form-card fade-in-up animate-delay-2">
        <div class="venta-form-card-header">
            <div class="venta-form-card-icon">📦</div>
            <h3 class="venta-form-card-title">Productos</h3>
        </div>

        <div class="venta-form-group">
            <label class="venta-form-label">Agregar Producto</label>
            <div class="venta-search-container">
                <input type="text" id="producto-search" class="venta-search-input" placeholder="Buscar producto por nombre o código..." autocomplete="off">
                <span class="venta-search-icon">🔍</span>
                <div id="producto-search-results" class="venta-search-results"></div>
            </div>
        </div>

        <div id="productos-list" class="venta-productos-list">
            <p style="text-align:center;color:var(--gray-500);padding:2rem;">No hay productos agregados</p>
        </div>
    </div>

    <!-- Método de Pago Card -->
    <div class="venta-form-card fade-in-up animate-delay-3">
        <div class="venta-form-card-header">
            <div class="venta-form-card-icon">💳</div>
            <h3 class="venta-form-card-title">Método de Pago</h3>
        </div>

        <div class="venta-metodo-pago-grid">
            <label class="venta-metodo-pago-card">
                <input type="radio" name="metodo_pago" value="efectivo" required>
                <div class="venta-metodo-pago-content">
                    <div class="venta-metodo-pago-icon">💵</div>
                    <div class="venta-metodo-pago-label">Efectivo</div>
                </div>
            </label>

            <label class="venta-metodo-pago-card">
                <input type="radio" name="metodo_pago" value="transferencia" required>
                <div class="venta-metodo-pago-content">
                    <div class="venta-metodo-pago-icon">🏦</div>
                    <div class="venta-metodo-pago-label">Transferencia</div>
                </div>
            </label>

            <label class="venta-metodo-pago-card">
                <input type="radio" name="metodo_pago" value="tarjeta" required>
                <div class="venta-metodo-pago-content">
                    <div class="venta-metodo-pago-icon">💳</div>
                    <div class="venta-metodo-pago-label">Tarjeta</div>
                </div>
            </label>

            <label class="venta-metodo-pago-card">
                <input type="radio" name="metodo_pago" value="credito" required>
                <div class="venta-metodo-pago-content">
                    <div class="venta-metodo-pago-icon">📝</div>
                    <div class="venta-metodo-pago-label">Crédito</div>
                </div>
            </label>
        </div>
    </div>

    <!-- Detalles Adicionales Card -->
    <div class="venta-form-card fade-in-up animate-delay-3">
        <div class="venta-form-card-header">
            <div class="venta-form-card-icon">📝</div>
            <h3 class="venta-form-card-title">Detalles Adicionales</h3>
        </div>

        <div class="venta-form-grid">
            <div class="venta-form-group">
                <label class="venta-form-label">Descuento</label>
                <input type="number" id="descuento" name="descuento" class="venta-form-input" value="0" min="0" step="0.01">
            </div>

            <div class="venta-form-group">
                <label class="venta-form-label">Dirección de Entrega</label>
                <input type="text" name="direccion_entrega" class="venta-form-input" placeholder="Opcional">
            </div>

            <div class="venta-form-group">
                <label class="venta-form-label">Teléfono de Contacto</label>
                <input type="text" name="telefono_entrega" class="venta-form-input" placeholder="Opcional">
            </div>
        </div>

        <div class="venta-form-group">
            <label class="venta-form-label">Notas</label>
            <textarea name="notas" class="venta-form-textarea" placeholder="Observaciones adicionales..."></textarea>
        </div>
    </div>

    <!-- Totales -->
    <div class="venta-totales-card fade-in-up animate-delay-3">
        <div class="venta-total-row">
            <span class="venta-total-label">Subtotal:</span>
            <span class="venta-total-value" id="subtotal-display">$0.00</span>
        </div>
        <div class="venta-total-row">
            <span class="venta-total-label">Descuento:</span>
            <span class="venta-total-value" id="descuento-display">$0.00</span>
        </div>
        <div class="venta-total-row">
            <span class="venta-total-label">IVA (19%):</span>
            <span class="venta-total-value" id="iva-display">$0.00</span>
        </div>
        <div class="venta-total-row">
            <span class="venta-total-label">TOTAL:</span>
            <span class="venta-total-value" id="total-display">$0.00</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="venta-actions fade-in-up animate-delay-3">
        <a href="{{ route('vendedor.ventas.index') }}" class="venta-btn-secondary">
            ❌ Cancelar
        </a>
        <button type="submit" class="venta-btn-primary">
            ✅ Registrar Venta
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/nueva-venta-modern.js') }}?v={{ filemtime(public_path('js/vendedor/nueva-venta-modern.js')) }}"></script>
@endpush
