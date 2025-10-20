@extends('layouts.cliente')

@section('title', '- Detalle de Referido')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cliente/referidos-modern.css') }}?v={{ filemtime(public_path('css/cliente/referidos-modern.css')) }}">
@endpush

@section('content')
<div class="referidos-container">
    
    {{-- Breadcrumb --}}
    <nav style="margin-bottom: 1.5rem;">
        <a href="{{ route('cliente.referidos.index') }}" style="color: var(--wine); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-arrow-left"></i>
            Volver a Mis Referidos
        </a>
    </nav>

    {{-- Header del Referido --}}
    <div class="referidos-header fade-in-up">
        <div class="referidos-header-content">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; border: 3px solid rgba(255,255,255,0.3);">
                    {{ strtoupper(substr($referido->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="referidos-title" style="margin-bottom: 0.5rem;">
                        {{ $referido->name }} {{ $referido->apellidos ?? '' }}
                    </h1>
                    <p class="referidos-subtitle" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <span><i class="bi bi-envelope-fill"></i> {{ $referido->email }}</span>
                        @if($referido->telefono)
                        <span><i class="bi bi-phone-fill"></i> {{ $referido->telefono }}</span>
                        @endif
                        <span><i class="bi bi-calendar-check"></i> Registro: {{ $referido->created_at->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas del Referido --}}
    <div class="referidos-stats-grid">
        <div class="stat-card stat-card-primary animate-delay-1 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <span class="stat-badge">Pedidos</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_pedidos'] }}</div>
                <div class="stat-label">Total de Pedidos</div>
                <div class="stat-detail">
                    <i class="bi bi-cart-fill"></i>
                    <span>Realizados desde el registro</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-success animate-delay-2 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <span class="stat-badge stat-badge-success">Entregados</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['pedidos_entregados'] }}</div>
                <div class="stat-label">Pedidos Completados</div>
                <div class="stat-detail stat-detail-success">
                    <i class="bi bi-arrow-up"></i>
                    <span>Exitosos</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-warning animate-delay-3 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="stat-badge stat-badge-warning">Total Comprado</span>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['total_comprado'], 0, ',', '.') }}</div>
                <div class="stat-label">Monto Total de Compras</div>
                <div class="stat-detail">
                    <i class="bi bi-cash-stack"></i>
                    <span>Suma de todos los pedidos</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-info animate-delay-3 fade-in-up">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="bi bi-gift-fill"></i>
                </div>
                <span class="stat-badge">Comisión</span>
            </div>
            <div class="stat-content">
                @php
                    $miComision = \App\Models\Comision::where('user_id', auth()->id())
                        ->where('referido_id', $referido->_id)
                        ->sum('monto');
                @endphp
                <div class="stat-value">${{ number_format($miComision, 0, ',', '.') }}</div>
                <div class="stat-label">Tu Ganancia</div>
                <div class="stat-detail">
                    <i class="bi bi-percent"></i>
                    <span>Por este referido</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pedidos Recientes --}}
    <div class="referidos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-clock-history"></i>
                Pedidos Recientes
            </h2>
            <span class="section-badge">
                <i class="bi bi-list"></i>
                {{ $pedidos_recientes->count() }} {{ $pedidos_recientes->count() === 1 ? 'pedido' : 'pedidos' }}
            </span>
        </div>

        @if($pedidos_recientes->count() > 0)
        <div class="referidos-table-container">
            <table class="referidos-table">
                <thead>
                    <tr>
                        <th>Pedido #</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos_recientes as $pedido)
                    <tr>
                        <td>
                            <strong style="color: var(--wine);">
                                #{{ str_pad($pedido->numero_pedido ?? $pedido->_id, 6, '0', STR_PAD_LEFT) }}
                            </strong>
                        </td>
                        <td>
                            <span class="referido-date">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            @php
                                $totalProductos = is_array($pedido->productos) ? count($pedido->productos) : 0;
                            @endphp
                            <span>{{ $totalProductos }} {{ $totalProductos === 1 ? 'producto' : 'productos' }}</span>
                        </td>
                        <td>
                            <strong style="font-size: 1.05rem;">${{ number_format($pedido->total_final ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            @php
                                $estadoBadgeClass = 'badge-pending';
                                $estadoTexto = ucfirst(str_replace('_', ' ', $pedido->estado ?? 'pendiente'));
                                $estadoIcon = 'hourglass-split';
                                
                                switch($pedido->estado ?? 'pendiente') {
                                    case 'entregado':
                                        $estadoBadgeClass = 'badge-active';
                                        $estadoIcon = 'check-circle-fill';
                                        break;
                                    case 'cancelado':
                                        $estadoBadgeClass = 'badge-inactive';
                                        $estadoIcon = 'x-circle';
                                        break;
                                    case 'confirmado':
                                    case 'en_preparacion':
                                    case 'enviado':
                                        $estadoBadgeClass = 'badge-pending';
                                        $estadoIcon = 'clock';
                                        break;
                                }
                            @endphp
                            <span class="referido-badge {{ $estadoBadgeClass }}">
                                <i class="bi bi-{{ $estadoIcon }}"></i> {{ $estadoTexto }}
                            </span>
                        </td>
                        <td>
                            @php
                                $comisionPedido = \App\Models\Comision::where('user_id', auth()->id())
                                    ->where('pedido_id', $pedido->_id)
                                    ->where('referido_id', $referido->_id)
                                    ->first();
                            @endphp
                            @if($comisionPedido)
                                <strong style="color: var(--success); font-size: 1.05rem;">
                                    +${{ number_format($comisionPedido->monto, 0, ',', '.') }}
                                </strong>
                            @else
                                <span style="color: var(--gray-400);">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-bag-x"></i>
            </div>
            <h3 class="empty-title">No hay pedidos registrados</h3>
            <p class="empty-description">
                Este referido aún no ha realizado ninguna compra.
            </p>
        </div>
        @endif
    </div>

    {{-- Información Adicional --}}
    <div style="background: #fff; border-radius: 18px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 2rem;">
        <h3 style="margin: 0 0 1.5rem; font-size: 1.35rem; color: var(--gray-900); display: flex; align-items: center; gap: 0.75rem;">
            <i class="bi bi-info-circle-fill" style="color: var(--wine);"></i>
            Información del Referido
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-person-badge"></i> NOMBRE COMPLETO
                </div>
                <div style="font-size: 1.05rem; color: var(--gray-900); font-weight: 500;">
                    {{ $referido->name }} {{ $referido->apellidos ?? '' }}
                </div>
            </div>

            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-envelope"></i> EMAIL
                </div>
                <div style="font-size: 1.05rem; color: var(--gray-900); font-weight: 500;">
                    {{ $referido->email }}
                </div>
            </div>

            @if($referido->telefono)
            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-phone"></i> TELÉFONO
                </div>
                <div style="font-size: 1.05rem; color: var(--gray-900); font-weight: 500;">
                    {{ $referido->telefono }}
                </div>
            </div>
            @endif

            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-calendar-check"></i> FECHA DE REGISTRO
                </div>
                <div style="font-size: 1.05rem; color: var(--gray-900); font-weight: 500;">
                    {{ $referido->created_at->format('d/m/Y H:i') }}
                    <small style="color: var(--gray-500); font-size: 0.85rem; display: block; margin-top: 0.25rem;">
                        Hace {{ $referido->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>

            @if($referido->ciudad)
            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-geo-alt"></i> UBICACIÓN
                </div>
                <div style="font-size: 1.05rem; color: var(--gray-900); font-weight: 500;">
                    {{ $referido->ciudad }}{{ $referido->departamento ? ', ' . $referido->departamento : '' }}
                </div>
            </div>
            @endif

            <div style="padding: 1rem; background: var(--gray-50); border-radius: 12px;">
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="bi bi-activity"></i> ESTADO
                </div>
                <div style="font-size: 1.05rem;">
                    @if($referido->activo ?? true)
                        <span class="referido-badge badge-active">
                            <i class="bi bi-check-circle-fill"></i> Activo
                        </span>
                    @else
                        <span class="referido-badge badge-inactive">
                            <i class="bi bi-x-circle"></i> Inactivo
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cliente/referidos-modern.js') }}?v={{ filemtime(public_path('js/cliente/referidos-modern.js')) }}"></script>
@endpush
