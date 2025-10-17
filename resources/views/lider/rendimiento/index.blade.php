@extends('layouts.lider')

@section('title', '- Rendimiento del Equipo')
@section('page-title', 'Rendimiento del Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/rendimiento-modern.css') }}?v={{ filemtime(public_path('css/lider/rendimiento-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="rendimiento-header">
        <div class="rendimiento-header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h1 class="rendimiento-title">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Rendimiento del Equipo
                    </h1>
                    <p class="rendimiento-subtitle">
                        Análisis completo del desempeño de tu equipo de ventas
                    </p>
                </div>
                <div class="rendimiento-actions ms-3">
                    <button onclick="rendimientoManager.refreshData()" class="rendimiento-action-btn">
                        <i class="bi bi-arrow-clockwise"></i>
                        Actualizar
                    </button>
                    <button onclick="rendimientoManager.exportData('csv')" class="rendimiento-action-btn">
                        <i class="bi bi-download"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de período -->
    <div class="rendimiento-filters">
        <div class="rendimiento-filters-header">
            <i class="bi bi-funnel"></i>
            Filtros de Período
        </div>
        <div class="rendimiento-filters-content">
            <form method="GET" action="{{ route('lider.rendimiento.index') }}" class="d-flex gap-3 align-items-end flex-wrap w-100">
                <div class="rendimiento-filter-group">
                    <label class="rendimiento-filter-label">Período</label>
                    <select name="periodo" class="rendimiento-filter-select">
                        <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                        <option value="trimestre" {{ $periodo == 'trimestre' ? 'selected' : '' }}>Este Trimestre</option>
                        <option value="año" {{ $periodo == 'año' ? 'selected' : '' }}>Este Año</option>
                    </select>
                </div>
                <button type="submit" class="rendimiento-filter-btn">
                    <i class="bi bi-search"></i>
                    Aplicar Filtros
                </button>
            </form>
        </div>
    </div>

    <!-- Métricas Generales -->
    <div class="rendimiento-stats-grid">
        <div class="rendimiento-stat-card">
            <div class="rendimiento-stat-icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="rendimiento-stat-value">${{ number_format($stats['total_ventas'], 0) }}</div>
            <div class="rendimiento-stat-label">Total Ventas</div>
            <div class="rendimiento-stat-subtitle">{{ $stats['cantidad_ventas'] }} ventas realizadas</div>
        </div>

        <div class="rendimiento-stat-card success">
            <div class="rendimiento-stat-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="rendimiento-stat-value">{{ number_format($stats['porcentaje_cumplimiento'], 1) }}%</div>
            <div class="rendimiento-stat-label">Cumplimiento de Meta</div>
            <div class="rendimiento-stat-subtitle">${{ number_format($stats['meta_equipo'], 0) }} meta total</div>
        </div>

        <div class="rendimiento-stat-card info">
            <div class="rendimiento-stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="rendimiento-stat-value">{{ $stats['miembros_activos'] }}/{{ $stats['total_miembros'] }}</div>
            <div class="rendimiento-stat-label">Miembros Activos</div>
            <div class="rendimiento-stat-subtitle">{{ number_format(($stats['miembros_activos']/$stats['total_miembros'])*100, 1) }}% del equipo</div>
        </div>

        <div class="rendimiento-stat-card warning">
            <div class="rendimiento-stat-icon">
                <i class="bi bi-calculator"></i>
            </div>
            <div class="rendimiento-stat-value">${{ number_format($stats['promedio_venta'], 0) }}</div>
            <div class="rendimiento-stat-label">Promedio por Venta</div>
            <div class="rendimiento-stat-subtitle">Ticket promedio</div>
        </div>
    </div>

    <div class="row">
        <!-- Rendimiento Individual -->
        <div class="col-xl-8 mb-4">
            <div class="rendimiento-content-card">
                <div class="rendimiento-card-header">
                    <h2 class="rendimiento-card-title">
                        <i class="bi bi-person-lines-fill"></i>
                        Rendimiento Individual
                    </h2>
                    <span class="badge bg-secondary">{{ ucfirst($periodo) }}</span>
                </div>
                <div class="rendimiento-card-body">
                    @if(count($rendimientoIndividual) > 0)
                        <div class="rendimiento-members-grid">
                            @foreach($rendimientoIndividual as $index => $rendimiento)
                            <div class="rendimiento-member-card {{ $rendimiento['estado'] }}">
                                <div class="rendimiento-member-header">
                                    <div class="rendimiento-member-avatar">
                                        {{ strtoupper(substr($rendimiento['miembro']->name, 0, 2)) }}
                                    </div>
                                    <div class="rendimiento-member-info">
                                        <h3 class="rendimiento-member-name">{{ $rendimiento['miembro']->name }}</h3>
                                        <p class="rendimiento-member-role">{{ ucfirst($rendimiento['miembro']->rol) }}</p>
                                    </div>
                                    @php
                                        $badgeClass = '';
                                        $badgeText = '';
                                        switch($rendimiento['estado']) {
                                            case 'excelente':
                                                $badgeClass = 'excelente';
                                                $badgeText = 'Excelente';
                                                break;
                                            case 'bueno':
                                                $badgeClass = 'bueno';
                                                $badgeText = 'Bueno';
                                                break;
                                            case 'regular':
                                                $badgeClass = 'regular';
                                                $badgeText = 'Regular';
                                                break;
                                            default:
                                                $badgeClass = 'bajo';
                                                $badgeText = 'Bajo';
                                        }
                                    @endphp
                                    <span class="rendimiento-member-badge rendimiento-badge-{{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>

                                <div class="rendimiento-member-stats">
                                    <div class="rendimiento-member-stat">
                                        <div class="rendimiento-member-stat-value">${{ number_format($rendimiento['total_ventas'], 0) }}</div>
                                        <p class="rendimiento-member-stat-label">Ventas</p>
                                    </div>
                                    <div class="rendimiento-member-stat">
                                        <div class="rendimiento-member-stat-value">{{ $rendimiento['cantidad_ventas'] }}</div>
                                        <p class="rendimiento-member-stat-label">Cantidad</p>
                                    </div>
                                    <div class="rendimiento-member-stat">
                                        <div class="rendimiento-member-stat-value">{{ number_format($rendimiento['porcentaje_cumplimiento'], 1) }}%</div>
                                        <p class="rendimiento-member-stat-label">Meta</p>
                                    </div>
                                </div>

                                @if($rendimiento['miembro']->meta_mensual > 0)
                                <div class="rendimiento-progress-wrapper">
                                    <div class="rendimiento-progress-label">
                                        <span class="rendimiento-progress-label-text">Progreso de Meta</span>
                                        <span class="rendimiento-progress-label-value">{{ number_format($rendimiento['porcentaje_cumplimiento'], 1) }}%</span>
                                    </div>
                                    <div class="rendimiento-progress">
                                        <div class="rendimiento-progress-bar {{ $rendimiento['porcentaje_cumplimiento'] >= 90 ? 'success' : '' }}"
                                             style="width: {{ min($rendimiento['porcentaje_cumplimiento'], 100) }}%"></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rendimiento-empty-state">
                            <div class="rendimiento-empty-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <p class="rendimiento-empty-text">No hay miembros en el equipo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rankings -->
        <div class="col-xl-4 mb-4">
            <div class="rendimiento-content-card">
                <div class="rendimiento-card-header">
                    <h2 class="rendimiento-card-title">
                        <i class="bi bi-trophy"></i>
                        Top Performers
                    </h2>
                </div>
                <div class="rendimiento-card-body">
                    <!-- Top por Ventas -->
                    <div class="mb-4">
                        <div class="rendimiento-ranking-title">
                            <i class="bi bi-currency-dollar" style="color: var(--wine);"></i>
                            Top Ventas
                        </div>
                        <div class="rendimiento-ranking-list">
                            @if($rankings['top_ventas']->count() > 0)
                                @foreach($rankings['top_ventas'] as $index => $item)
                                <div class="rendimiento-ranking-item">
                                    <div class="rendimiento-ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="rendimiento-ranking-info">
                                        <div class="rendimiento-ranking-name">{{ $item->vendedor->name }}</div>
                                        <div class="rendimiento-ranking-value">${{ number_format($item->total_ventas, 0) }}</div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="rendimiento-empty-state" style="padding: 2rem 1rem;">
                                    <p class="text-muted small mb-0">Sin datos disponibles</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Top por Cantidad -->
                    <div class="mb-4">
                        <div class="rendimiento-ranking-title">
                            <i class="bi bi-graph-up" style="color: var(--info);"></i>
                            Top Cantidad
                        </div>
                        <div class="rendimiento-ranking-list">
                            @if($rankings['top_cantidad']->count() > 0)
                                @foreach($rankings['top_cantidad'] as $index => $item)
                                <div class="rendimiento-ranking-item">
                                    <div class="rendimiento-ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="rendimiento-ranking-info">
                                        <div class="rendimiento-ranking-name">{{ $item->vendedor->name }}</div>
                                        <div class="rendimiento-ranking-value">{{ $item->cantidad_ventas }} ventas</div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="rendimiento-empty-state" style="padding: 2rem 1rem;">
                                    <p class="text-muted small mb-0">Sin datos disponibles</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Top por Comisiones -->
                    <div>
                        <div class="rendimiento-ranking-title">
                            <i class="bi bi-cash-coin" style="color: var(--success);"></i>
                            Top Comisiones
                        </div>
                        <div class="rendimiento-ranking-list">
                            @if($rankings['top_comisiones']->count() > 0)
                                @foreach($rankings['top_comisiones'] as $index => $item)
                                <div class="rendimiento-ranking-item">
                                    <div class="rendimiento-ranking-position {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="rendimiento-ranking-info">
                                        <div class="rendimiento-ranking-name">{{ $item->usuario->name }}</div>
                                        <div class="rendimiento-ranking-value">${{ number_format($item->total_comisiones, 0) }}</div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="rendimiento-empty-state" style="padding: 2rem 1rem;">
                                    <p class="text-muted small mb-0">Sin datos disponibles</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evolución de Métricas -->
    <div class="row">
        <div class="col-12">
            <div class="rendimiento-content-card">
                <div class="rendimiento-card-header">
                    <h2 class="rendimiento-card-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        Evolución de Ventas (Últimos 6 Meses)
                    </h2>
                </div>
                <div class="rendimiento-card-body">
                    <div class="rendimiento-chart-container">
                        <canvas id="evolucionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pasar datos de evolución al JavaScript
    window.evolucionMetricas = @json($evolucionMetricas);
</script>
<script src="{{ asset('js/lider/rendimiento-modern.js') }}?v={{ filemtime(public_path('js/lider/rendimiento-modern.js')) }}"></script>
@endpush