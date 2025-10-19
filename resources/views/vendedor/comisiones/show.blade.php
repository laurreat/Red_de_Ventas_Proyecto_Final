@extends('layouts.vendedor')

@section('title', 'Detalle de Comisión')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/comisiones-modern.css') }}?v={{ time() }}">
<style>
.comisiones-btn-reenviar {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    padding: 1rem 2rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.comisiones-btn-reenviar:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
}

/* ========== GLASSMORPHISM MODAL ========== */
.glass-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-modal-backdrop.active {
    opacity: 1;
    visibility: visible;
}

.glass-modal {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    transform: scale(0.9) translateY(20px);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.glass-modal-backdrop.active .glass-modal {
    transform: scale(1) translateY(0);
}

.glass-modal-header {
    position: relative;
    padding: 2rem;
    background: linear-gradient(135deg, 
        rgba(114, 47, 55, 0.95) 0%, 
        rgba(114, 47, 55, 0.85) 100%);
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.1) 0%,
        transparent 100%
    );
    pointer-events: none;
}

.glass-modal-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.2),
        inset 0 0 0 1px rgba(255, 255, 255, 0.3);
    animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.glass-modal-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin: 0;
    text-align: center;
    letter-spacing: -0.02em;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.glass-modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.2rem;
}

.glass-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.glass-modal-body {
    padding: 2rem;
    position: relative;
}

.glass-modal-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.glass-modal-text {
    font-size: 1.1rem;
    color: #374151;
    text-align: center;
    margin: 0;
    line-height: 1.6;
    font-weight: 500;
}

.glass-info-card {
    background: rgba(114, 47, 55, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(114, 47, 55, 0.1);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.glass-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(114, 47, 55, 0.1);
}

.glass-info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.glass-info-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.glass-info-value {
    font-size: 1.1rem;
    color: #111827;
    font-weight: 700;
}

.glass-info-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.glass-info-badge.pending {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.glass-modal-note {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: rgba(59, 130, 246, 0.1);
    padding: 1rem 1.25rem;
    border-radius: 12px;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
}

.glass-modal-note i {
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.glass-modal-footer {
    padding: 1.5rem 2rem;
    background: rgba(249, 250, 251, 0.8);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.glass-btn {
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.glass-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.glass-btn:active::before {
    width: 300px;
    height: 300px;
}

.glass-btn-primary {
    background: linear-gradient(135deg, #722f37, #8b3c44);
    color: white;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.3);
}

.glass-btn-primary:hover {
    background: linear-gradient(135deg, #8b3c44, #722f37);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(114, 47, 55, 0.4);
}

.glass-btn-secondary {
    background: rgba(107, 114, 128, 0.1);
    backdrop-filter: blur(10px);
    color: #374151;
    border: 1px solid rgba(107, 114, 128, 0.2);
}

.glass-btn-secondary:hover {
    background: rgba(107, 114, 128, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Animaciones adicionales */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.glass-modal-body > * {
    animation: slideUp 0.4s ease-out backwards;
}

.glass-modal-body > *:nth-child(1) { animation-delay: 0.1s; }
.glass-modal-body > *:nth-child(2) { animation-delay: 0.2s; }
.glass-modal-body > *:nth-child(3) { animation-delay: 0.3s; }

/* Responsive */
@media (max-width: 768px) {
    .glass-modal {
        width: 95%;
        max-width: none;
        border-radius: 20px;
    }
    
    .glass-modal-header {
        padding: 1.5rem;
    }
    
    .glass-modal-icon {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .glass-modal-title {
        font-size: 1.4rem;
    }
    
    .glass-modal-body {
        padding: 1.5rem;
    }
    
    .glass-modal-footer {
        flex-direction: column;
        padding: 1.25rem;
    }
    
    .glass-btn {
        width: 100%;
        justify-content: center;
    }
    
    .comisiones-btn-reenviar {
        width: 100%;
        justify-content: center;
    }
}

/* Smooth scrolling para el modal */
.glass-modal-body {
    overflow-y: auto;
    max-height: calc(90vh - 250px);
}

.glass-modal-body::-webkit-scrollbar {
    width: 8px;
}

.glass-modal-body::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}

.glass-modal-body::-webkit-scrollbar-thumb {
    background: rgba(114, 47, 55, 0.3);
    border-radius: 10px;
}

.glass-modal-body::-webkit-scrollbar-thumb:hover {
    background: rgba(114, 47, 55, 0.5);
}
</style>
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
    
    @if(isset($solicitudPago) && $solicitudPago && $solicitudPago->estado === 'pendiente')
        <button type="button" class="comisiones-btn-reenviar" onclick="openReenviarModal()">
            <i class="bi bi-send-fill"></i>
            Reenviar Notificación
        </button>
        
        <div class="alert alert-info mt-3" style="opacity: 0; animation: fadeInUp 0.6s ease-out 0.4s forwards;">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Solicitud en proceso:</strong> Tu solicitud de retiro por 
            <strong>${{ number_format($solicitudPago->monto, 0, ',', '.') }}</strong> 
            está pendiente de aprobación por parte de los administradores.
            @if($solicitudPago->created_at)
                <br><small>Solicitada el {{ $solicitudPago->created_at->format('d/m/Y H:i') }}</small>
            @endif
        </div>
    @endif
</div>

<!-- Modal Glassmorphism para Reenviar Notificación -->
@if(isset($solicitudPago) && $solicitudPago && $solicitudPago->estado === 'pendiente')
<div id="reenviarModal" class="glass-modal-backdrop" onclick="closeReenviarModal(event)">
    <div class="glass-modal" onclick="event.stopPropagation()">
        <div class="glass-modal-header">
            <div class="glass-modal-icon">
                <i class="bi bi-send-fill"></i>
            </div>
            <h3 class="glass-modal-title">Reenviar Notificación</h3>
            <button type="button" class="glass-modal-close" onclick="closeReenviarModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="glass-modal-body">
            <div class="glass-modal-content">
                <p class="glass-modal-text">
                    ¿Estás seguro de que deseas reenviar la notificación a los administradores?
                </p>
                <div class="glass-info-card">
                    <div class="glass-info-item">
                        <span class="glass-info-label">Monto:</span>
                        <span class="glass-info-value">${{ number_format($solicitudPago->monto, 0, ',', '.') }}</span>
                    </div>
                    <div class="glass-info-item">
                        <span class="glass-info-label">Método:</span>
                        <span class="glass-info-value">{{ ucfirst($solicitudPago->metodo_pago) }}</span>
                    </div>
                    <div class="glass-info-item">
                        <span class="glass-info-label">Estado:</span>
                        <span class="glass-info-badge pending">Pendiente</span>
                    </div>
                </div>
                <p class="glass-modal-note">
                    <i class="bi bi-lightbulb-fill"></i>
                    Esta acción enviará una nueva notificación a todos los administradores para recordarles sobre tu solicitud.
                </p>
            </div>
        </div>
        
        <div class="glass-modal-footer">
            <button type="button" class="glass-btn glass-btn-secondary" onclick="closeReenviarModal()">
                <i class="bi bi-x-circle"></i>
                Cancelar
            </button>
            <form action="{{ route('vendedor.comisiones.reenviar-notificacion', $solicitudPago->_id ?? $solicitudPago->id) }}" 
                  method="POST" 
                  style="display: inline;">
                @csrf
                <button type="submit" class="glass-btn glass-btn-primary">
                    <i class="bi bi-send-check-fill"></i>
                    Confirmar y Reenviar
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/comisiones-modern.js') }}?v={{ time() }}"></script>
<script>
function openReenviarModal() {
    const modal = document.getElementById('reenviarModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeReenviarModal(event) {
    if (event && event.target !== event.currentTarget) {
        return;
    }
    const modal = document.getElementById('reenviarModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReenviarModal();
    }
});
</script>
@endpush
