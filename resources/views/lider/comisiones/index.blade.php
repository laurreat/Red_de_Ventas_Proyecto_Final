@extends('layouts.lider')

@section('title', ' - Mis Comisiones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/comisiones-modern.css') }}?v={{ filemtime(public_path('css/lider/comisiones-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="comisiones-header fade-in-up">
        <div class="comisiones-header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="comisiones-title">
                        <i class="bi bi-cash-coin"></i>
                        Mis Comisiones
                    </h1>
                    <p class="comisiones-subtitle">
                        Gestiona tus comisiones, revisa tu historial y solicita pagos de manera rápida y segura
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="comisiones-actions">
                        @if(auth()->user()->comisiones_disponibles > 0)
                            <a href="{{ route('lider.comisiones.solicitar') }}" class="comisiones-action-btn comisiones-action-btn-primary">
                                <i class="bi bi-send-fill"></i>
                                Solicitar Pago
                            </a>
                        @endif
                        <button class="comisiones-action-btn" onclick="window.print()">
                            <i class="bi bi-printer"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="comisiones-stats-grid">
        <div class="comisiones-stat-card scale-in animate-delay-1">
            <div class="comisiones-stat-icon">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['total_ganado'], 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Total Ganado</div>
            <div class="comisiones-stat-change positive">
                <i class="bi bi-arrow-up"></i>
                Histórico
            </div>
        </div>

        <div class="comisiones-stat-card scale-in animate-delay-2">
            <div class="comisiones-stat-icon success">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['disponible'], 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Disponible</div>
            <div class="comisiones-stat-change positive">
                <i class="bi bi-check-circle"></i>
                Listo para cobrar
            </div>
        </div>

        <div class="comisiones-stat-card scale-in animate-delay-3">
            <div class="comisiones-stat-icon info">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['mes_actual'], 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Mes Actual</div>
            <div class="comisiones-stat-change neutral">
                <i class="bi bi-calendar3"></i>
                {{ now()->format('F Y') }}
            </div>
        </div>

        <div class="comisiones-stat-card scale-in animate-delay-4">
            <div class="comisiones-stat-icon warning">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="comisiones-stat-value">${{ number_format($stats['promedio_mensual'], 0, ',', '.') }}</div>
            <div class="comisiones-stat-label">Promedio Mensual</div>
            <div class="comisiones-stat-change neutral">
                <i class="bi bi-bar-chart"></i>
                Últimos 6 meses
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Evolución Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="comisiones-chart-container fade-in-up">
                <h3 class="comisiones-chart-title">
                    <i class="bi bi-graph-up"></i>
                    Evolución de Comisiones
                </h3>
                <div class="comisiones-chart-wrapper">
                    <canvas id="evolucionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Breakdown por Tipo -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="comisiones-chart-container fade-in-up">
                <h3 class="comisiones-chart-title">
                    <i class="bi bi-pie-chart"></i>
                    Por Tipo
                </h3>
                <div class="comisiones-chart-wrapper">
                    <canvas id="tipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="comisiones-filter-card fade-in-up">
        <h3 class="comisiones-filter-title">
            <i class="bi bi-funnel"></i>
            Filtrar Comisiones
        </h3>
        <form method="GET" action="{{ route('lider.comisiones.index') }}" id="comisionesFilterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="comisiones-form-label">Periodo</label>
                    <select name="periodo" class="comisiones-form-control">
                        <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                        <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                        <option value="ultimo_trimestre" {{ $periodo == 'ultimo_trimestre' ? 'selected' : '' }}>Último Trimestre</option>
                        <option value="ultimo_ano" {{ $periodo == 'ultimo_ano' ? 'selected' : '' }}>Último Año</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="comisiones-form-label">Tipo de Comisión</label>
                    <select name="tipo" class="comisiones-form-control">
                        <option value="todas" {{ $tipo == 'todas' ? 'selected' : '' }}>Todas</option>
                        <option value="venta_directa" {{ $tipo == 'venta_directa' ? 'selected' : '' }}>Venta Directa</option>
                        <option value="referido" {{ $tipo == 'referido' ? 'selected' : '' }}>Referido</option>
                        <option value="liderazgo" {{ $tipo == 'liderazgo' ? 'selected' : '' }}>Liderazgo</option>
                        <option value="bono" {{ $tipo == 'bono' ? 'selected' : '' }}>Bono</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="comisiones-form-label">&nbsp;</label>
                    <div class="comisiones-btn-group">
                        <button type="submit" class="comisiones-btn comisiones-btn-primary">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('lider.comisiones.index') }}" class="comisiones-btn comisiones-btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Comisiones -->
    <div class="comisiones-table-container fade-in-up">
        <div class="comisiones-table-header">
            <h3 class="comisiones-table-title">
                <i class="bi bi-list-ul"></i>
                Historial de Comisiones
            </h3>
            <span class="badge bg-white text-dark">{{ $comisiones->total() }} registros</span>
        </div>
        <div class="table-responsive">
            <table class="comisiones-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Referido</th>
                        <th>Pedido</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comisiones as $comision)
                        <tr>
                            <td data-label="Fecha">
                                <i class="bi bi-calendar3 text-muted me-2"></i>
                                {{ $comision->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td data-label="Tipo">
                                <span class="comisiones-badge comisiones-badge-{{ str_replace('_', '-', $comision->tipo) }}">
                                    {{ ucfirst(str_replace('_', ' ', $comision->tipo)) }}
                                </span>
                            </td>
                            <td data-label="Monto">
                                <div class="comisiones-amount-display">
                                    ${{ number_format($comision->monto, 0, ',', '.') }}
                                </div>
                            </td>
                            <td data-label="Referido">
                                @if($comision->referido)
                                    <div class="comisiones-referido-info">
                                        <div class="comisiones-referido-avatar">
                                            {{ strtoupper(substr($comision->referido->name, 0, 1)) }}
                                        </div>
                                        <span class="comisiones-referido-name">{{ $comision->referido->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-label="Pedido">
                                @if($comision->pedido)
                                    <a href="#" class="text-decoration-none">
                                        <i class="bi bi-receipt"></i>
                                        #{{ str_pad($comision->pedido->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-label="Estado">
                                <span class="comisiones-badge comisiones-badge-{{ $comision->estado }}">
                                    {{ ucfirst($comision->estado) }}
                                </span>
                            </td>
                            <td class="text-center" data-label="Acciones">
                                <button class="comisiones-action-btn-icon info btn-ver-detalle"
                                        data-comision-id="{{ $comision->id }}"
                                        title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="comisiones-empty-state">
                                    <div class="comisiones-empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <div class="comisiones-empty-text">No hay comisiones disponibles</div>
                                    <div class="comisiones-empty-subtext">
                                        No se encontraron comisiones para el periodo seleccionado
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($comisiones->hasPages())
            <div class="comisiones-pagination">
                {{ $comisiones->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Top Generadores -->
    @if($topGeneradores->isNotEmpty())
        <div class="comisiones-chart-container fade-in-up">
            <h3 class="comisiones-chart-title">
                <i class="bi bi-star-fill"></i>
                Top Generadores de Comisiones (Este Mes)
            </h3>
            <div class="row">
                @foreach($topGeneradores as $index => $generador)
                    @php
                        $position = $index + 1;
                        $positionClass = $position === 1 ? 'gold' : ($position === 2 ? 'silver' : ($position === 3 ? 'bronze' : ''));
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="comisiones-top-card {{ $positionClass }}">
                            <div class="comisiones-top-position {{ $positionClass }}">
                                @if($position <= 3)
                                    <i class="bi bi-trophy-fill"></i>
                                @else
                                    {{ $position }}
                                @endif
                            </div>
                            <div class="comisiones-top-info">
                                <div class="comisiones-top-name">{{ $generador->referido->name }}</div>
                                <div class="comisiones-top-value">${{ number_format($generador->total_generado, 0, ',', '.') }}</div>
                                <div class="comisiones-top-meta">
                                    <i class="bi bi-graph-up"></i>
                                    Comisiones generadas
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Solicitudes Pendientes -->
    @if($solicitudesPendientes->isNotEmpty())
        <div class="comisiones-chart-container fade-in-up">
            <h3 class="comisiones-chart-title">
                <i class="bi bi-clock-history"></i>
                Solicitudes de Pago Pendientes
            </h3>
            <div class="row">
                @foreach($solicitudesPendientes as $solicitud)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="comisiones-solicitud-card">
                            <div class="comisiones-solicitud-header">
                                <span class="comisiones-solicitud-date">
                                    <i class="bi bi-calendar"></i>
                                    {{ $solicitud->created_at->format('d/m/Y') }}
                                </span>
                                <span class="comisiones-badge comisiones-badge-{{ $solicitud->estado }}">
                                    {{ ucfirst($solicitud->estado) }}
                                </span>
                            </div>
                            <div class="comisiones-solicitud-amount">
                                ${{ number_format($solicitud->monto, 0, ',', '.') }}
                            </div>
                            <div class="comisiones-solicitud-method">
                                <i class="bi bi-credit-card"></i>
                                {{ ucfirst($solicitud->metodo_pago) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/lider/comisiones-modern.js') }}?v={{ filemtime(public_path('js/lider/comisiones-modern.js')) }}"></script>
<script>
// Gráfico de Evolución
const evolucionCtx = document.getElementById('evolucionChart');
if(evolucionCtx) {
    new Chart(evolucionCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($evolucionComisiones->pluck('mes')) !!},
            datasets: [{
                label: 'Comisiones',
                data: {!! json_encode($evolucionComisiones->pluck('total')) !!},
                borderColor: '#722F37',
                backgroundColor: 'rgba(114, 47, 55, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: '#722F37',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: (context) => 'Comisiones: $' + context.parsed.y.toLocaleString('es-CO')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => '$' + value.toLocaleString('es-CO')
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Gráfico de Tipos
const tipoCtx = document.getElementById('tipoChart');
if(tipoCtx) {
    const breakdownData = @json($breakdownTipo);
    const labels = Object.keys(breakdownData);
    const data = Object.values(breakdownData).map(item => item.total);

    new Chart(tipoCtx, {
        type: 'doughnut',
        data: {
            labels: labels.map(label => label.replace(/_/g, ' ').toUpperCase()),
            datasets: [{
                data: data,
                backgroundColor: ['#722F37', '#10b981', '#3b82f6', '#f59e0b'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: (context) => context.label + ': $' + context.parsed.toLocaleString('es-CO')
                    }
                }
            }
        }
    });
}
</script>
@endpush
