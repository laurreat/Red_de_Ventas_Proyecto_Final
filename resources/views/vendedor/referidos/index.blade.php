@extends('layouts.vendedor')

@section('title', ' - Mi Red de Referidos')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="page-header-modern fade-in">
        <div class="page-header-content">
            <div class="page-header-left">
                <div class="page-icon-wrapper">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <h1 class="page-title">Mi Red de Referidos</h1>
                    <p class="page-subtitle">
                        <i class="bi bi-graph-up me-2"></i>
                        Gestiona tu red de ventas y maximiza tus comisiones
                    </p>
                </div>
            </div>
            <div class="page-header-actions">
                <button class="btn btn-primary" id="generate-link-btn">
                    <i class="bi bi-share-fill"></i>
                    <span>Compartir Enlace</span>
                </button>
                <a href="{{ route('vendedor.referidos.red') }}" class="btn btn-outline-primary">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Ver Árbol</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards Mejoradas --}}
    <div class="row g-4 mb-4 fade-in-up animate-delay-1">
        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #722F37, #8b3c44);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-arrow-up-right text-success"></i>
                        </div>
                    </div>
                    <div class="stats-value">{{ $stats['referidos_directos'] }}</div>
                    <div class="stats-label">Referidos Directos</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-graph-up text-primary"></i>
                        </div>
                    </div>
                    <div class="stats-value">{{ $stats['total_red'] }}</div>
                    <div class="stats-label">Total en Red</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                    </div>
                    <div class="stats-value">{{ $stats['referidos_activos'] }}</div>
                    <div class="stats-label">Activos Este Mes</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                    </div>
                    <div class="stats-value">{{ $stats['nuevos_mes'] }}</div>
                    <div class="stats-label">Nuevos Este Mes</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-bag-fill text-warning"></i>
                        </div>
                    </div>
                    <div class="stats-value">${{ number_format($stats['ventas_red'], 0, ',', '.') }}</div>
                    <div class="stats-label">Ventas de la Red</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="stats-trend">
                            <i class="bi bi-trophy-fill text-info"></i>
                        </div>
                    </div>
                    <div class="stats-value">${{ number_format($stats['comisiones_referidos'], 0, ',', '.') }}</div>
                    <div class="stats-label">Comisiones Generadas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="card fade-in-up animate-delay-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>
                Mis Referidos Directos
            </h5>
            <button class="btn btn-sm btn-outline-primary" id="export-referidos-btn">
                <i class="bi bi-download"></i>
                <span>Exportar</span>
            </button>
        </div>
        <div class="card-body">
            @if($referidos->count() > 0)
                {{-- Tabla de Referidos --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>
                                    <i class="bi bi-person-circle me-2"></i>
                                    Referido
                                </th>
                                <th>
                                    <i class="bi bi-layers me-2"></i>
                                    Nivel
                                </th>
                                <th>
                                    <i class="bi bi-calendar3 me-2"></i>
                                    Registro
                                </th>
                                <th>
                                    <i class="bi bi-cash-stack me-2"></i>
                                    Ventas
                                </th>
                                <th>
                                    <i class="bi bi-diagram-3 me-2"></i>
                                    Sub-Red
                                </th>
                                <th>
                                    <i class="bi bi-activity me-2"></i>
                                    Estado
                                </th>
                                <th>
                                    <i class="bi bi-gear me-2"></i>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referidos as $index => $referido)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            {{ strtoupper(substr($referido->name, 0, 1)) }}{{ strtoupper(substr($referido->apellidos ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $referido->name }} {{ $referido->apellidos }}</div>
                                            <small class="text-muted">{{ $referido->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-star-fill"></i>
                                        Nivel 1
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $referido->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $referido->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold text-success">
                                            ${{ number_format($referido->total_ventas ?? 0, 0, ',', '.') }}
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-bag-fill"></i>
                                            {{ $referido->pedidos_vendedor_count ?? 0 }} pedidos
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="fw-semibold">{{ $referido->referidos_count ?? 0 }}</div>
                                        <small class="text-muted">referidos</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $esActivo = $referido->activo ?? true;
                                    @endphp
                                    <span class="badge {{ $esActivo ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="bi bi-{{ $esActivo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                        {{ $esActivo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('vendedor.referidos.show', $referido->id) }}" 
                                           class="btn btn-outline-primary"
                                           title="Ver detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary"
                                                onclick="referidosManager.sendMessage('{{ $referido->id }}', '{{ $referido->name }} {{ $referido->apellidos }}')"
                                                title="Enviar mensaje">
                                            <i class="bi bi-chat-dots-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Estado Vacío --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-people display-1 text-muted"></i>
                    </div>
                    <h3 class="mb-3">Aún No Tienes Referidos</h3>
                    <p class="text-muted mb-4">
                        Comienza a construir tu red de ventas invitando personas a unirse.
                        Cada referido es una oportunidad para generar ingresos pasivos.
                    </p>
                    <button class="btn btn-primary" id="generate-link-empty-btn">
                        <i class="bi bi-share-fill"></i>
                        Compartir mi Enlace de Referido
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(isset($topReferidos) && $topReferidos->count() > 0)
    {{-- Top Referidos --}}
    <div class="card fade-in-up animate-delay-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-trophy-fill me-2"></i>
                Top Referidos por Ventas
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($topReferidos as $index => $top)
                <div class="col-md-4">
                    <div class="card h-100 border-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'bronze') }}">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'bronze') }} fs-5">
                                    <i class="bi bi-award-fill"></i>
                                    #{{ $index + 1 }}
                                </span>
                            </div>
                            <div class="avatar-lg mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($top->name, 0, 1)) }}{{ strtoupper(substr($top->apellidos ?? '', 0, 1)) }}
                            </div>
                            <h5 class="mb-2">{{ $top->name }} {{ $top->apellidos }}</h5>
                            <div class="fs-4 fw-bold text-success mb-2">
                                ${{ number_format($top->total_ventas ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-bag-check-fill"></i>
                                {{ $top->pedidos_vendedor_count ?? 0 }} pedidos
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<input type="hidden" id="codigo-referido" value="{{ auth()->user()->codigo_referido }}">
@endsection

@push('styles')
<style>
    .page-header-modern {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .page-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .page-icon-wrapper {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #722F37, #8b3c44);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: #2c2c2c;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
    }

    .page-header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .stats-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stats-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 0.25rem;
    }

    .stats-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .stats-trend i {
        font-size: 1.25rem;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #722F37, #8b3c44);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .avatar-lg {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #722F37, #8b3c44);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2rem;
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
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

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
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

    .bg-bronze {
        background: linear-gradient(135deg, #cd7f32, #b8732d) !important;
    }

    .border-bronze {
        border-color: #cd7f32 !important;
        border-width: 2px !important;
    }

    @media (max-width: 991.98px) {
        .page-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header-actions {
            width: 100%;
        }

        .page-header-actions .btn {
            flex: 1;
        }
    }
</style>
@endpush

@push('styles')
<style>
    /* Glassmorphism Modal Styles */
    .glass-modal {
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
    }

    .glass-modal .modal-header {
        background: linear-gradient(135deg, rgba(114, 47, 55, 0.95) 0%, rgba(139, 60, 68, 0.9) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
    }

    .glass-modal .modal-body {
        padding: 2rem;
    }

    .glass-modal .modal-footer {
        background: rgba(249, 250, 251, 0.5);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 0 0 20px 20px;
        padding: 1.25rem 2rem;
    }

    .glass-input-group {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(114, 47, 55, 0.1);
        transition: all 0.3s ease;
    }

    .glass-input-group:hover {
        border-color: rgba(114, 47, 55, 0.3);
        box-shadow: 0 4px 12px rgba(114, 47, 55, 0.1);
    }

    .glass-input-group input,
    .glass-input-group textarea {
        background: transparent;
        border: none;
        padding: 0.875rem 1rem;
    }

    .glass-input-group input:focus,
    .glass-input-group textarea:focus {
        outline: none;
        box-shadow: none;
    }

    .glass-share-btn {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }

    .glass-share-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border-color: currentColor;
    }

    .glass-share-btn i {
        font-size: 1.75rem;
    }

    .glass-code-box {
        background: linear-gradient(135deg, rgba(114, 47, 55, 0.1) 0%, rgba(139, 60, 68, 0.05) 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(114, 47, 55, 0.2);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .glass-code-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #722F37, #8b3c44, #722F37);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .glass-copy-btn {
        background: linear-gradient(135deg, #722F37, #8b3c44);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .glass-copy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(114, 47, 55, 0.3);
    }

    .glass-alert {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 1rem;
        border-left: 4px solid #3b82f6;
    }

    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(-50px);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }
</style>
@endpush

@push('scripts')
<script>
// Crear instancia global del manager
window.referidosManager = {
    generateReferralLink: function() {
        const codigoReferido = document.getElementById('codigo-referido')?.value;
        if (!codigoReferido) {
            this.showToast('Error', 'No se pudo obtener el código de referido', 'error');
            return;
        }
        
        const baseUrl = window.location.origin;
        const enlace = `${baseUrl}/register?ref=${codigoReferido}`;
        
        // Crear modal glassmorphism para mostrar enlace
        const modalHtml = `
            <div class="modal fade" id="shareModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content glass-modal border-0">
                        <div class="modal-header text-white border-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-share-fill me-2"></i>
                                Compartir Enlace de Referido
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="glass-code-box">
                                <label class="form-label fw-bold text-dark mb-3">
                                    <i class="bi bi-qr-code me-2"></i>
                                    Tu Código de Referido
                                </label>
                                <div class="glass-input-group mb-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg text-center fw-bold fs-3" 
                                               value="${codigoReferido}" readonly style="letter-spacing: 2px;">
                                        <button class="btn glass-copy-btn" onclick="referidosManager.copyCode('${codigoReferido}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-3">
                                    <i class="bi bi-link-45deg me-2"></i>
                                    Enlace para Compartir
                                </label>
                                <div class="glass-input-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="${enlace}" readonly id="enlace-input">
                                        <button class="btn glass-copy-btn" onclick="referidosManager.copyLink('${enlace}')">
                                            <i class="bi bi-clipboard-check"></i> Copiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="share-options">
                                <label class="form-label fw-bold text-dark mb-3">
                                    <i class="bi bi-send me-2"></i>
                                    Compartir en Redes Sociales
                                </label>
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <button class="glass-share-btn w-100 text-success" onclick="referidosManager.shareWhatsApp('${enlace}', '${codigoReferido}')">
                                            <i class="bi bi-whatsapp"></i>
                                            <span style="font-size: 0.85rem;">WhatsApp</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="glass-share-btn w-100 text-primary" onclick="referidosManager.shareEmail('${enlace}', '${codigoReferido}')">
                                            <i class="bi bi-envelope"></i>
                                            <span style="font-size: 0.85rem;">Email</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="glass-share-btn w-100 text-info" onclick="referidosManager.shareFacebook('${enlace}')">
                                            <i class="bi bi-facebook"></i>
                                            <span style="font-size: 0.85rem;">Facebook</span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="glass-share-btn w-100 text-dark" onclick="referidosManager.shareTwitter('${enlace}', '${codigoReferido}')">
                                            <i class="bi bi-twitter"></i>
                                            <span style="font-size: 0.85rem;">Twitter</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior si existe
        const oldModal = document.getElementById('shareModal');
        if (oldModal) oldModal.remove();
        
        // Agregar nuevo modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('shareModal'));
        modal.show();
    },
    
    copyCode: function(codigo) {
        this.copyToClipboard(codigo, '¡Código copiado!');
    },
    
    copyLink: function(enlace) {
        this.copyToClipboard(enlace, '¡Enlace copiado al portapapeles!');
    },
    
    copyToClipboard: function(text, message = '¡Copiado!') {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('Éxito', message, 'success');
            }).catch(() => {
                this.fallbackCopy(text, message);
            });
        } else {
            this.fallbackCopy(text, message);
        }
    },
    
    fallbackCopy: function(text, message) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            this.showToast('Éxito', message, 'success');
        } catch (err) {
            alert('Enlace: ' + text);
        }
        document.body.removeChild(textarea);
    },
    
    shareWhatsApp: function(enlace, codigo) {
        const texto = `¡Únete a nuestra red de ventas! 🚀\n\nUsa mi código: ${codigo}\nRegistrarte aquí: ${enlace}`;
        window.open(`https://wa.me/?text=${encodeURIComponent(texto)}`, '_blank');
    },
    
    shareEmail: function(enlace, codigo) {
        const asunto = 'Invitación a Red de Ventas - Arepa la Llanerita';
        const cuerpo = `¡Hola!\n\nQuiero invitarte a unirte a nuestra red de ventas.\n\nUsa mi código de referido: ${codigo}\n\nRegístrate aquí: ${enlace}\n\n¡Espero verte pronto!`;
        window.location.href = `mailto:?subject=${encodeURIComponent(asunto)}&body=${encodeURIComponent(cuerpo)}`;
    },
    
    shareFacebook: function(enlace) {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(enlace)}`, '_blank');
    },
    
    shareTwitter: function(enlace, codigo) {
        const texto = `¡Únete a nuestra red de ventas! Código: ${codigo}`;
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(texto)}&url=${encodeURIComponent(enlace)}`, '_blank');
    },
    
    sendMessage: function(referidoId, referidoNombre) {
        const modalHtml = `
            <div class="modal fade" id="messageModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content glass-modal border-0">
                        <div class="modal-header text-white border-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-chat-dots-fill me-2"></i>
                                Enviar Mensaje a ${referidoNombre}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="messageForm">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">
                                        <i class="bi bi-tag me-2"></i>Asunto
                                    </label>
                                    <div class="glass-input-group">
                                        <input type="text" class="form-control" id="messageSubject" 
                                               placeholder="Escribe el asunto del mensaje" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">
                                        <i class="bi bi-chat-text me-2"></i>Mensaje
                                    </label>
                                    <div class="glass-input-group">
                                        <textarea class="form-control" id="messageBody" rows="5" 
                                                  placeholder="Escribe tu mensaje aquí..." required></textarea>
                                    </div>
                                </div>
                                <div class="glass-alert">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle me-3 mt-1" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <strong>Información:</strong><br>
                                            El mensaje será enviado como notificación al usuario y también a su correo electrónico.
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                            <button type="button" class="btn glass-copy-btn" onclick="referidosManager.submitMessage('${referidoId}')">
                                <i class="bi bi-send me-2"></i>Enviar Mensaje
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const oldModal = document.getElementById('messageModal');
        if (oldModal) oldModal.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
    },
    
    submitMessage: function(referidoId) {
        const subject = document.getElementById('messageSubject').value;
        const body = document.getElementById('messageBody').value;
        
        if (!subject || !body) {
            this.showToast('Error', 'Por favor completa todos los campos', 'error');
            return;
        }
        
        // Mostrar loading
        const submitBtn = event.target;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enviando...';
        
        // Enviar mensaje mediante AJAX
        fetch('/vendedor/referidos/enviar-mensaje', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                referido_id: referidoId,
                asunto: subject,
                mensaje: body
            })
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('messageModal')).hide();
                this.showToast('¡Mensaje Enviado!', data.message || 'Tu mensaje ha sido enviado correctamente', 'success');
                
                // Limpiar formulario
                document.getElementById('messageSubject').value = '';
                document.getElementById('messageBody').value = '';
            } else {
                this.showToast('Error', data.message || 'No se pudo enviar el mensaje', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            this.showToast('Error', 'Ocurrió un error al enviar el mensaje', 'error');
        });
    },
    
    shareWhatsApp: function(enlace, codigo) {
        const texto = `¡Únete a nuestra red de ventas! 🚀\n\nUsa mi código: ${codigo}\nRegístrate aquí: ${enlace}`;
        window.open(`https://wa.me/?text=${encodeURIComponent(texto)}`, '_blank');
    },
    
    shareEmail: function(enlace, codigo) {
        const asunto = 'Invitación a Red de Ventas - Arepa la Llanerita';
        const cuerpo = `¡Hola!\n\nQuiero invitarte a unirte a nuestra red de ventas.\n\nUsa mi código de referido: ${codigo}\n\nRegístrate aquí: ${enlace}\n\n¡Espero verte pronto!`;
        window.location.href = `mailto:?subject=${encodeURIComponent(asunto)}&body=${encodeURIComponent(cuerpo)}`;
    },
    
    shareFacebook: function(enlace) {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(enlace)}`, '_blank');
    },
    
    shareTwitter: function(enlace, codigo) {
        const texto = `¡Únete a nuestra red de ventas! Código: ${codigo}`;
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(texto)}&url=${encodeURIComponent(enlace)}`, '_blank');
    },
    
    exportarReferidos: function() {
        this.showToast('Exportando...', 'Generando archivo de exportación', 'info');
        
        // Redirigir a la ruta de exportación
        setTimeout(() => {
            window.location.href = '/vendedor/referidos/exportar';
        }, 500);
    },
    
    showToast: function(title, message, type = 'info') {
        // Usar Livewire toast si está disponible
        if (window.Livewire) {
            Livewire.dispatch('show-toast', { 
                type: type, 
                message: title + ': ' + message 
            });
        } else {
            // Fallback a alert
            alert(title + '\n' + message);
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const generateLinkBtn = document.getElementById('generate-link-btn');
    const generateLinkEmptyBtn = document.getElementById('generate-link-empty-btn');
    const exportBtn = document.getElementById('export-referidos-btn');
    
    if (generateLinkBtn) {
        generateLinkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            referidosManager.generateReferralLink();
        });
    }
    
    if (generateLinkEmptyBtn) {
        generateLinkEmptyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            referidosManager.generateReferralLink();
        });
    }
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            referidosManager.exportarReferidos();
        });
    }
});
</script>
@endpush
