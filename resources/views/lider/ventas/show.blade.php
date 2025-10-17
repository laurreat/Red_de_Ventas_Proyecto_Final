@extends('layouts.lider')

@section('title', ' - Detalle de Venta')
@section('page-title', 'Detalle de Venta #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/ventas-modern.css') }}?v={{ filemtime(public_path('css/lider/ventas-modern.css')) }}">
<style>
.detalle-section{background:var(--white);border-radius:20px;padding:2rem;box-shadow:var(--shadow);border:1px solid rgba(114,47,55,0.1);margin-bottom:2rem}.detalle-section-title{font-size:1.25rem;font-weight:700;color:var(--wine);margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem}.detalle-section-title i{font-size:1.5rem}.detalle-info-row{display:flex;justify-content:space-between;align-items:center;padding:1rem 0;border-bottom:1px solid rgba(0,0,0,0.05)}.detalle-info-row:last-child{border-bottom:none}.detalle-info-label{font-size:0.938rem;font-weight:600;color:var(--muted)}.detalle-info-value{font-size:1rem;font-weight:600;color:var(--dark)}.producto-detalle-card{background:var(--light);border-radius:12px;padding:1.25rem;margin-bottom:1rem;border:1px solid rgba(0,0,0,0.05);transition:var(--transition)}.producto-detalle-card:hover{transform:translateX(4px);box-shadow:var(--shadow)}.timeline{position:relative;padding-left:2rem}.timeline::before{content:'';position:absolute;left:8px;top:0;bottom:0;width:2px;background:linear-gradient(180deg,var(--wine),rgba(114,47,55,0.2))}.timeline-item{position:relative;padding-bottom:2rem}.timeline-item:last-child{padding-bottom:0}.timeline-item::before{content:'';position:absolute;left:-1.45rem;top:0;width:18px;height:18px;border-radius:50%;background:var(--wine);border:3px solid var(--white);box-shadow:0 0 0 2px var(--wine)}.timeline-content{background:var(--white);border-radius:12px;padding:1.25rem;box-shadow:var(--shadow);border-left:3px solid var(--wine)}.timeline-header{display:flex;justify-content:space-between;align-items:start;margin-bottom:0.75rem}.timeline-title{font-size:1rem;font-weight:700;color:var(--dark)}.timeline-date{font-size:0.813rem;color:var(--muted)}.timeline-description{font-size:0.938rem;color:var(--dark);line-height:1.6}.rentabilidad-card{background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(16,185,129,0.05));border-radius:16px;padding:1.75rem;border:2px solid rgba(16,185,129,0.2)}.rentabilidad-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;margin-top:1.5rem}.rentabilidad-item{text-align:center}.rentabilidad-value{font-size:2rem;font-weight:800;color:var(--success);margin-bottom:0.25rem}.rentabilidad-label{font-size:0.875rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px}@media (max-width:768px){.detalle-section{padding:1.5rem;border-radius:16px}.detalle-info-row{flex-direction:column;align-items:flex-start;gap:0.5rem}.rentabilidad-grid{grid-template-columns:1fr;gap:1rem}.timeline{padding-left:1.5rem}}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header con Info Principal -->
    <div class="ventas-header fade-in-up">
        <div class="ventas-header-content">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="ventas-title">
                        <i class="bi bi-receipt-cutoff"></i>
                        Venta #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                    </h1>
                    <p class="ventas-subtitle mb-0">
                        Realizada el {{ $venta->created_at->format('d/m/Y') }} a las {{ $venta->created_at->format('H:i') }}
                    </p>
                </div>
                <div class="ventas-actions">
                    <a href="{{ route('lider.ventas.index') }}" class="ventas-action-btn">
                        <i class="bi bi-arrow-left"></i>
                        Volver al Listado
                    </a>
                    <button class="ventas-action-btn" onclick="window.print()">
                        <i class="bi bi-printer"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8 mb-4">
            <!-- Detalles de la Venta -->
            <div class="detalle-section fade-in-up animate-delay-1">
                <h3 class="detalle-section-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Información de la Venta
                </h3>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Estado</span>
                    <span class="ventas-badge ventas-badge-{{ $venta->estado }}">
                        <i class="bi bi-{{ $venta->estado == 'completado' ? 'check-circle' : ($venta->estado == 'pendiente' ? 'clock' : ($venta->estado == 'cancelado' ? 'x-circle' : 'info-circle')) }}"></i>
                        {{ ucfirst($venta->estado) }}
                    </span>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Vendedor</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="ventas-vendor-avatar" style="width:32px;height:32px;font-size:0.938rem">
                            {{ strtoupper(substr($venta->vendedor->name ?? 'V', 0, 1)) }}
                        </div>
                        <span class="detalle-info-value">{{ $venta->vendedor->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Cliente</span>
                    <div>
                        <div class="detalle-info-value">{{ $venta->cliente->name ?? 'Cliente' }}</div>
                        <div style="font-size:0.813rem;color:var(--muted)">{{ $venta->cliente->email ?? 'Sin email' }}</div>
                    </div>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Subtotal</span>
                    <span class="detalle-info-value">${{ number_format($venta->subtotal ?? 0, 0, ',', '.') }}</span>
                </div>

                @if(isset($venta->descuento) && $venta->descuento > 0)
                <div class="detalle-info-row">
                    <span class="detalle-info-label">Descuento</span>
                    <span class="detalle-info-value" style="color:var(--danger)">
                        -${{ number_format($venta->descuento, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                @if(isset($venta->impuestos) && $venta->impuestos > 0)
                <div class="detalle-info-row">
                    <span class="detalle-info-label">Impuestos</span>
                    <span class="detalle-info-value">${{ number_format($venta->impuestos, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="detalle-info-row" style="background:rgba(114,47,55,0.05);border-radius:12px;padding:1.25rem;margin-top:1rem">
                    <span class="detalle-info-label" style="font-size:1.125rem;color:var(--wine)">Total</span>
                    <span style="font-size:1.75rem;font-weight:800;color:var(--wine)">
                        ${{ number_format($venta->total_final, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Productos -->
            <div class="detalle-section fade-in-up animate-delay-2">
                <h3 class="detalle-section-title">
                    <i class="bi bi-box-seam-fill"></i>
                    Productos ({{ count($venta->detalles) }})
                </h3>

                @foreach($venta->detalles as $detalle)
                <div class="producto-detalle-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="flex-grow-1">
                            <div style="font-weight:700;font-size:1.063rem;color:var(--dark);margin-bottom:0.5rem">
                                {{ $detalle['producto_data']['nombre'] ?? 'Producto' }}
                            </div>
                            <div style="font-size:0.875rem;color:var(--muted)">
                                Cantidad: {{ $detalle['cantidad'] }} x ${{ number_format($detalle['precio_unitario'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="text-align:right">
                            <div class="ventas-amount" style="font-size:1.25rem">
                                ${{ number_format($detalle['subtotal'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Rentabilidad -->
            @if(isset($rentabilidad))
            <div class="rentabilidad-card fade-in-up animate-delay-3">
                <h3 class="detalle-section-title" style="margin-bottom:0">
                    <i class="bi bi-graph-up-arrow"></i>
                    Análisis de Rentabilidad
                </h3>
                <div class="rentabilidad-grid">
                    <div class="rentabilidad-item">
                        <div class="rentabilidad-value">${{ number_format($rentabilidad['costo_total'] ?? 0, 0, ',', '.') }}</div>
                        <div class="rentabilidad-label">Costo Total</div>
                    </div>
                    <div class="rentabilidad-item">
                        <div class="rentabilidad-value">${{ number_format($rentabilidad['utilidad'] ?? 0, 0, ',', '.') }}</div>
                        <div class="rentabilidad-label">Utilidad</div>
                    </div>
                    <div class="rentabilidad-item">
                        <div class="rentabilidad-value">{{ number_format($rentabilidad['margen'] ?? 0, 1) }}%</div>
                        <div class="rentabilidad-label">Margen</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información Adicional -->
        <div class="col-lg-4 mb-4">
            <!-- Métricas del Vendedor -->
            @if(isset($metricsVendedor))
            <div class="detalle-section fade-in-up animate-delay-1">
                <h3 class="detalle-section-title">
                    <i class="bi bi-person-fill-gear"></i>
                    Métricas del Vendedor
                </h3>

                <div style="text-align:center;padding:1rem 0">
                    <div class="ventas-vendor-avatar" style="width:80px;height:80px;font-size:2rem;margin:0 auto 1rem">
                        {{ strtoupper(substr($venta->vendedor->name ?? 'V', 0, 1)) }}
                    </div>
                    <div style="font-weight:700;font-size:1.25rem;color:var(--dark);margin-bottom:0.25rem">
                        {{ $venta->vendedor->name ?? 'N/A' }}
                    </div>
                    <div style="font-size:0.938rem;color:var(--muted)">
                        {{ ucfirst($venta->vendedor->rol ?? 'vendedor') }}
                    </div>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Ventas este Mes</span>
                    <span class="detalle-info-value">${{ number_format($metricsVendedor['ventas_mes'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Pedidos este Mes</span>
                    <span class="detalle-info-value">{{ $metricsVendedor['pedidos_mes'] ?? 0 }}</span>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Ticket Promedio</span>
                    <span class="detalle-info-value">${{ number_format($metricsVendedor['ticket_promedio_mes'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Referidos Totales</span>
                    <span class="detalle-info-value">{{ $metricsVendedor['referidos_totales'] ?? 0 }}</span>
                </div>

                <div class="detalle-info-row">
                    <span class="detalle-info-label">Días Activo</span>
                    <span class="detalle-info-value">{{ $metricsVendedor['dias_activo'] ?? 0 }} días</span>
                </div>
            </div>
            @endif

            <!-- Comisiones -->
            @if(isset($comisionesGeneradas) && $comisionesGeneradas->isNotEmpty())
            <div class="detalle-section fade-in-up animate-delay-2">
                <h3 class="detalle-section-title">
                    <i class="bi bi-currency-dollar"></i>
                    Comisiones Generadas
                </h3>

                @foreach($comisionesGeneradas as $comision)
                <div class="producto-detalle-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-weight:600;color:var(--dark);margin-bottom:0.25rem">
                                {{ $comision->user->name ?? 'Usuario' }}
                            </div>
                            <div style="font-size:0.813rem;color:var(--muted)">
                                {{ ucfirst($comision->tipo ?? 'comisión') }} • {{ $comision->porcentaje ?? 0 }}%
                            </div>
                        </div>
                        <div class="ventas-amount" style="font-size:1.125rem">
                            ${{ number_format($comision->monto ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Historial de Estados -->
            @if(isset($historialEstados) && $historialEstados->isNotEmpty())
            <div class="detalle-section fade-in-up animate-delay-3">
                <h3 class="detalle-section-title">
                    <i class="bi bi-clock-history"></i>
                    Historial de Estados
                </h3>

                <div class="timeline">
                    @foreach($historialEstados as $historial)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <div class="timeline-title">
                                    <span class="ventas-badge ventas-badge-{{ $historial['estado'] }}">
                                        {{ ucfirst($historial['estado']) }}
                                    </span>
                                </div>
                                <div class="timeline-date">
                                    {{ \Carbon\Carbon::parse($historial['fecha'])->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="timeline-description">
                                {{ $historial['observaciones'] ?? 'Sin observaciones' }}
                            </div>
                            <div style="font-size:0.813rem;color:var(--muted);margin-top:0.5rem">
                                <i class="bi bi-person"></i>
                                {{ $historial['usuario'] ?? 'Sistema' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/ventas-modern.js') }}?v={{ filemtime(public_path('js/lider/ventas-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para las secciones
    const sections = document.querySelectorAll('.detalle-section');
    sections.forEach((section, i) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        setTimeout(() => {
            section.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, i * 100);
    });
});
</script>
@endpush
