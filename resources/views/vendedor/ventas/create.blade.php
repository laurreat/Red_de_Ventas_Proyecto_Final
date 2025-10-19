@extends('layouts.vendedor')

@section('title', 'Nueva Venta')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/nueva-venta-modern.css') }}?v={{ filemtime(public_path('css/vendedor/nueva-venta-modern.css')) }}">
<link rel="stylesheet" href="{{ asset('css/vendedor/pedidos-create-enhanced.css') }}?v={{ filemtime(public_path('css/vendedor/pedidos-create-enhanced.css')) }}">
<link rel="stylesheet" href="{{ asset('css/admin/pedidos-modern.css') }}?v={{ filemtime(public_path('css/admin/pedidos-modern.css')) }}">
<style>
/* Mejoras visuales premium para Nueva Venta */
.venta-header {
    background: linear-gradient(135deg, #722F37 0%, #5a252a 100%);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(114, 47, 55, 0.25);
    color: #fff;
    position: relative;
    overflow: hidden;
}

.venta-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent 70%);
    border-radius: 50%;
}

.venta-header-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.venta-header-subtitle {
    font-size: 1rem;
    opacity: 0.95;
    margin: 0;
}

.venta-form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 2px solid rgba(114, 47, 55, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.venta-form-card:hover {
    border-color: rgba(114, 47, 55, 0.3);
    box-shadow: 0 8px 20px rgba(114, 47, 55, 0.15);
}

.venta-form-card-header {
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.05), rgba(92, 37, 43, 0.02));
    border-bottom: 2px solid rgba(114, 47, 55, 0.1);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.venta-form-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #722F37, #5a252a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.venta-form-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.venta-form-group {
    padding: 1.5rem;
}

.venta-form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.95rem;
}

.venta-form-required {
    color: #ef4444;
    font-weight: 700;
}

.venta-search-input,
.venta-input {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(114, 47, 55, 0.15);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    width: 100%;
}

.venta-search-input:focus,
.venta-input:focus {
    background: #fff;
    border-color: #722F37;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.2);
    transform: translateY(-2px);
    outline: none;
}

.venta-search-container {
    position: relative;
}

.venta-search-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    opacity: 0.5;
    pointer-events: none;
}

.venta-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid rgba(114, 47, 55, 0.15);
    border-radius: 12px;
    margin-top: 0.5rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
    display: none;
}

.venta-search-results.active {
    display: block;
}

.venta-search-result-item {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(114, 47, 55, 0.08);
    cursor: pointer;
    transition: all 0.2s ease;
}

.venta-search-result-item:hover {
    background: rgba(114, 47, 55, 0.05);
    padding-left: 1.5rem;
}

.venta-search-result-item:last-child {
    border-bottom: none;
}

.producto-item {
    background: white;
    border: 2px solid rgba(114, 47, 55, 0.1);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    animation: fadeInUp 0.4s ease-out;
}

.producto-item:hover {
    border-color: #722F37;
    transform: translateX(8px);
    box-shadow: 0 8px 24px rgba(114, 47, 55, 0.15);
}

.venta-btn-primary {
    background: linear-gradient(135deg, #722F37, #5a252a);
    border: none;
    color: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(114, 47, 55, 0.35);
    cursor: pointer;
}

.venta-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(114, 47, 55, 0.45);
}

.venta-btn-secondary {
    background: white;
    border: 2px solid #d1d5db;
    color: #374151;
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.venta-btn-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    transform: translateY(-2px);
}

.resumen-card {
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.05), rgba(92, 37, 43, 0.02));
    border: 2px solid rgba(114, 47, 55, 0.15);
    border-radius: 16px;
    padding: 1.75rem;
    position: sticky;
    top: 20px;
    box-shadow: 0 8px 24px rgba(114, 47, 55, 0.1);
}

.resumen-total {
    background: linear-gradient(135deg, #722F37, #5a252a);
    border-radius: 12px;
    padding: 1.25rem;
    margin-top: 1rem;
    color: white;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

.animate-delay-1 {
    animation-delay: 0.1s;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animate-delay-2 {
    animation-delay: 0.2s;
    opacity: 0;
    animation-fill-mode: forwards;
}

.animate-delay-3 {
    animation-delay: 0.3s;
    opacity: 0;
    animation-fill-mode: forwards;
}

@media (max-width: 768px) {
    .resumen-card {
        position: static;
        margin-top: 2rem;
    }

    .venta-header {
        padding: 1.5rem;
    }

    .venta-header-title {
        font-size: 1.5rem;
    }
}
</style>
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
