@extends('layouts.cliente')

@section('title', '- Mis Referidos')
@section('header-title', 'Mis Referidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cliente/referidos-modern.css') }}?v={{ filemtime(public_path('css/cliente/referidos-modern.css')) }}">
@endpush

@section('content')
<div class="referidos-container">
    
    {{-- Header --}}
    <div class="referidos-header fade-in-up">
        <div class="referidos-header-content">
            <h1 class="referidos-title">
                <i class="bi bi-people-fill"></i>
                Mis Referidos
            </h1>
            <p class="referidos-subtitle">Comparte tu link y gana comisiones por cada referido que realice una compra</p>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="referidos-stats-grid">
        <div class="stat-card stat-card-primary animate-delay-1 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="stat-badge">Total</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_referidos'] }}</div>
                <div class="stat-label">Referidos Totales</div>
                <div class="stat-detail">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $stats['referidos_activos'] }} activos</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-success animate-delay-2 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <span class="stat-badge stat-badge-success">Este mes</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['referidos_mes'] }}</div>
                <div class="stat-label">Nuevos Referidos</div>
                <div class="stat-detail stat-detail-success">
                    <i class="bi bi-arrow-up"></i>
                    <span>{{ \Carbon\Carbon::now()->format('F Y') }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-warning animate-delay-3 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="stat-badge stat-badge-warning">Ganadas</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['total_comisiones'], 0, ',', '.') }}</div>
                <div class="stat-label">Comisiones Totales</div>
                <div class="stat-detail">
                    <i class="bi bi-cash-stack"></i>
                    <span>${{ number_format($stats['comisiones_mes'], 0, ',', '.') }} este mes</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-info animate-delay-3 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <span class="stat-badge">Pendientes</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['comisiones_pendientes'], 0, ',', '.') }}</div>
                <div class="stat-label">Por Cobrar</div>
                <div class="stat-detail">
                    <i class="bi bi-clock-history"></i>
                    <span>En proceso de pago</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Link de Referido --}}
    <div class="link-card">
        <div class="link-card-header">
            <div class="link-card-icon">
                <i class="bi bi-link-45deg"></i>
            </div>
            <div class="link-card-title">
                <h3>Tu Link de Referido</h3>
                <p>Comparte este link con tus amigos y familiares</p>
            </div>
        </div>

        <div class="link-input-group">
            <input type="text" id="linkReferido" class="link-input" value="{{ $link_referido }}" readonly>
            <button id="copyLinkBtn" class="btn-copy">
                <i class="bi bi-clipboard"></i>
                Copiar Link
            </button>
        </div>

        <div class="link-actions">
            <button id="shareWhatsAppBtn" class="btn-share btn-whatsapp">
                <i class="bi bi-whatsapp"></i>
                Compartir en WhatsApp
            </button>
            <button id="shareFacebookBtn" class="btn-share btn-facebook">
                <i class="bi bi-facebook"></i>
                Compartir en Facebook
            </button>
            <button id="shareTwitterBtn" class="btn-share btn-twitter">
                <i class="bi bi-twitter"></i>
                Compartir en Twitter
            </button>
        </div>

        {{-- Botón para Transferir Red --}}
        @if($referidos->count() > 0)
            @php
                // Verificar si el usuario tiene un referidor y si ese referidor es vendedor/líder
                $tieneReferidorVendedor = false;
                if(auth()->user()->referido_por) {
                    $referidor = \App\Models\User::find(auth()->user()->referido_por);
                    if($referidor && in_array($referidor->rol, ['vendedor', 'lider'])) {
                        $tieneReferidorVendedor = true;
                    }
                }
            @endphp
            
            @if(!$tieneReferidorVendedor)
            <div class="mt-3 pt-3 border-top">
                <a href="{{ route('cliente.transferencia-red.index') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-diagram-3"></i>
                    Transferir mi red a un Vendedor/Líder
                </a>
                <small class="d-block text-center text-muted mt-2">
                    ¿Quieres que un vendedor o líder gestione tu red? Transfiere tus {{ $referidos->count() }} referidos.
                </small>
            </div>
            @else
            <div class="mt-3 pt-3 border-top">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i>
                    Tu red ya está gestionada por <strong>{{ $referidor->nombreCompleto() }}</strong> ({{ ucfirst($referidor->rol) }}).
                </div>
            </div>
            @endif
        @endif
    </div>

    {{-- Lista de Referidos --}}
    <div class="referidos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-list-ul"></i>
                Tus Referidos
            </h2>
            <span class="section-badge">
                <i class="bi bi-people"></i>
                {{ $referidos->count() }} {{ $referidos->count() === 1 ? 'referido' : 'referidos' }}
            </span>
        </div>

        @if($referidos->count() > 0)
        <div class="referidos-table-container">
            <table class="referidos-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Pedidos</th>
                        <th>Comisión</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referidos as $referido)
                    <tr>
                        <td>
                            <div class="referido-user">
                                <div class="referido-avatar">
                                    {{ strtoupper(substr($referido->name, 0, 2)) }}
                                </div>
                                <div class="referido-info">
                                    <div class="referido-name">{{ $referido->name }}</div>
                                    <div class="referido-email">{{ $referido->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($referido->activo ?? true)
                                <span class="referido-badge badge-active">
                                    <i class="bi bi-check-circle-fill"></i> Activo
                                </span>
                            @else
                                <span class="referido-badge badge-inactive">
                                    <i class="bi bi-x-circle"></i> Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $pedidosCount = \App\Models\Pedido::where('user_id', $referido->_id)->count();
                                $pedidosTotal = \App\Models\Pedido::where('user_id', $referido->_id)
                                    ->whereIn('estado', ['confirmado', 'en_preparacion', 'enviado', 'entregado'])
                                    ->sum('total_final');
                                $pedidosTotal = to_float($pedidosTotal);
                            @endphp
                            <div class="referido-stats">
                                <div class="referido-stat">
                                    <div class="referido-stat-value">{{ $pedidosCount }}</div>
                                    <div class="referido-stat-label">Pedidos</div>
                                </div>
                                <div class="referido-stat">
                                    <div class="referido-stat-value">${{ number_format($pedidosTotal, 0, ',', '.') }}</div>
                                    <div class="referido-stat-label">Total</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $comisionReferido = \App\Models\Comision::where('user_id', auth()->id())
                                    ->where('referido_id', $referido->_id)
                                    ->sum('monto');
                            @endphp
                            <strong style="color: var(--success); font-size: 1.05rem;">
                                ${{ number_format($comisionReferido, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <span class="referido-date">
                                {{ $referido->created_at->format('d/m/Y') }}
                            </span>
                            <br>
                            <small style="color: var(--gray-500);">
                                Hace {{ $referido->created_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            <button onclick="verDetalle('{{ $referido->_id }}')" class="btn-ver-detalle">
                                <i class="bi bi-eye-fill"></i>
                                Ver Detalle
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-people"></i>
            </div>
            <h3 class="empty-title">Aún no tienes referidos</h3>
            <p class="empty-description">
                Comparte tu link de referido con tus amigos y familiares para comenzar a ganar comisiones.
                Por cada compra que realicen, recibirás una comisión automáticamente.
            </p>
            <button onclick="document.getElementById('copyLinkBtn').click()" class="btn-primary">
                <i class="bi bi-share-fill"></i>
                Compartir Mi Link
            </button>
        </div>
        @endif
    </div>

</div>

{{-- Notificaciones --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.createElement('div');
        notification.className = 'copied-notification';
        notification.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>';
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/cliente/referidos-modern.js') }}?v={{ filemtime(public_path('js/cliente/referidos-modern.js')) }}"></script>
@endpush
