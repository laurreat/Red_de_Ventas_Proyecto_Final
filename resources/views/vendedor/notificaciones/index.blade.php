@extends('layouts.vendedor')

@section('title', 'Notificaciones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/notificaciones-modern.css') }}?v={{ filemtime(public_path('css/vendedor/notificaciones-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- Header Hero --}}
    <div class="notif-header fade-in-up">
        <div class="notif-header-content">
            <div class="notif-header-info">
                <h1>
                    <i class="bi bi-bell"></i>
                    Notificaciones
                </h1>
                <p>Centro de notificaciones y alertas importantes</p>
            </div>
            <div class="notif-header-actions">
                <button onclick="notificacionesManager.marcarTodasLeidas()" class="notif-action-btn notif-action-btn-secondary" id="markAllReadBtn">
                    <i class="bi bi-check-all"></i> Marcar todas leídas
                </button>
                <button onclick="notificacionesManager.limpiarLeidas()" class="notif-action-btn notif-action-btn-secondary" id="clearReadBtn">
                    <i class="bi bi-trash"></i> Limpiar leídas
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="notif-stats-container">
        <div class="notif-stat-card stat-info fade-in-up animate-delay-1">
            <div class="notif-stat-header">
                <div class="notif-stat-icon">
                    <i class="bi bi-inbox"></i>
                </div>
            </div>
            <div class="notif-stat-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="notif-stat-label">Total de Notificaciones</div>
        </div>

        <div class="notif-stat-card stat-warning fade-in-up animate-delay-2">
            <div class="notif-stat-header">
                <div class="notif-stat-icon">
                    <i class="bi bi-bell-fill"></i>
                </div>
            </div>
            <div class="notif-stat-value">{{ $stats['no_leidas'] ?? 0 }}</div>
            <div class="notif-stat-label">No Leídas</div>
        </div>

        <div class="notif-stat-card stat-success fade-in-up animate-delay-3">
            <div class="notif-stat-header">
                <div class="notif-stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="notif-stat-value">{{ $stats['leidas'] ?? 0 }}</div>
            <div class="notif-stat-label">Leídas</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="notif-filters scale-in">
        <form id="filterForm" method="GET" action="{{ route('vendedor.notificaciones.index') }}">
            <div class="notif-filters-row">
                <div class="notif-filter-group">
                    <label class="notif-filter-label">
                        <i class="bi bi-funnel"></i> Estado
                    </label>
                    <select name="filter" class="notif-filter-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Todas</option>
                        <option value="unread" {{ $filter === 'unread' ? 'selected' : '' }}>No leídas</option>
                        <option value="read" {{ $filter === 'read' ? 'selected' : '' }}>Leídas</option>
                    </select>
                </div>

                <div class="notif-filter-group">
                    <label class="notif-filter-label">
                        <i class="bi bi-tag"></i> Tipo
                    </label>
                    <select name="tipo" class="notif-filter-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipoOption)
                            <option value="{{ $tipoOption }}" {{ $tipo === $tipoOption ? 'selected' : '' }}>
                                {{ ucfirst($tipoOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="notif-filter-group" style="flex:0;min-width:auto">
                    <label class="notif-filter-label" style="opacity:0">Aplicar</label>
                    <button type="submit" class="notif-filter-btn">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Lista de Notificaciones --}}
    <div class="notif-list-container fade-in-up animate-delay-4">
        <div class="notif-list-header">
            <h3>
                <i class="bi bi-list-ul"></i>
                Mis Notificaciones
                @if($filter === 'unread')
                    <span class="notif-badge notif-badge-warning" style="margin-left:1rem">No leídas</span>
                @elseif($filter === 'read')
                    <span class="notif-badge notif-badge-success" style="margin-left:1rem">Leídas</span>
                @endif
            </h3>
            <span>{{ $notificaciones->total() }} notificaciones</span>
        </div>

        <div class="notif-list">
            @forelse($notificaciones as $notificacion)
                <div class="notif-item {{ !$notificacion->leida ? 'unread' : '' }}" data-id="{{ $notificacion->_id }}">
                    <div class="notif-icon-wrapper type-{{ $notificacion->tipo ?? 'sistema' }}">
                        @php
                            $icons = [
                                'pedido' => 'bi-cart-check-fill',
                                'venta' => 'bi-currency-dollar',
                                'comision' => 'bi-cash-coin',
                                'pago' => 'bi-wallet2',
                                'sistema' => 'bi-gear-fill',
                                'alerta' => 'bi-exclamation-triangle-fill',
                                'urgente' => 'bi-exclamation-circle-fill',
                                'importante' => 'bi-star-fill',
                                'mensaje' => 'bi-chat-dots-fill'
                            ];
                            $icon = $icons[$notificacion->tipo ?? 'sistema'] ?? 'bi-bell-fill';
                        @endphp
                        <i class="bi {{ $icon }}"></i>
                    </div>

                    <div class="notif-content">
                        <div class="notif-title">
                            {{ $notificacion->titulo }}
                            @if(!$notificacion->leida)
                                <span class="notif-badge notif-badge-warning">Nueva</span>
                            @endif
                        </div>
                        <div class="notif-message">{{ Str::limit($notificacion->mensaje, 120) }}</div>
                        <div class="notif-meta">
                            <span class="notif-meta-item">
                                <i class="bi bi-clock"></i>
                                {{ $notificacion->created_at->diffForHumans() }}
                            </span>
                            <span class="notif-meta-item">
                                <i class="bi bi-tag"></i>
                                {{ ucfirst($notificacion->tipo ?? 'sistema') }}
                            </span>
                        </div>
                    </div>

                    <div class="notif-actions">
                        @if(!$notificacion->leida)
                            <button class="notif-action-icon" data-action="mark-read" title="Marcar como leída">
                                <i class="bi bi-check2"></i>
                            </button>
                        @endif
                        <button class="notif-action-icon" data-action="delete" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="notif-empty">
                    <i class="bi bi-inbox"></i>
                    <h4>No hay notificaciones</h4>
                    <p>No tienes notificaciones en este momento</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if($notificaciones->hasPages())
            <div class="notif-pagination">
                {{ $notificaciones->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Toast Container --}}
<div class="notif-toast-container"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        notificacionesManager.showToast('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        notificacionesManager.showToast('{{ session('error') }}', 'danger');
    });
</script>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/notificaciones-modern.js') }}?v={{ filemtime(public_path('js/vendedor/notificaciones-modern.js')) }}"></script>
@endpush
