@extends('layouts.vendedor')

@section('title', ' - Detalles del Referido')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="page-header-modern fade-in">
        <div class="page-header-content">
            <div class="page-header-left">
                <div class="page-icon-wrapper">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h1 class="page-title">Detalles del Referido</h1>
                    <p class="page-subtitle">
                        {{ $referido->name }} {{ $referido->apellidos }}
                    </p>
                </div>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('vendedor.referidos.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i>
                    Volver a Referidos
                </a>
            </div>
        </div>
    </div>

    {{-- Información del Referido --}}
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="referidos-card fade-in-up animate-delay-1">
                <div class="referidos-card-header">
                    <h3 class="referidos-card-title">
                        <i class="fas fa-user"></i>
                        Información Personal
                    </h3>
                </div>
                <div class="referidos-card-body">
                    <div class="referidos-info-item">
                        <div class="referidos-info-label">Nombre Completo</div>
                        <div class="referidos-info-value">{{ $referido->name }} {{ $referido->apellidos }}</div>
                    </div>
                    <div class="referidos-info-item">
                        <div class="referidos-info-label">Email</div>
                        <div class="referidos-info-value">{{ $referido->email }}</div>
                    </div>
                    <div class="referidos-info-item">
                        <div class="referidos-info-label">Teléfono</div>
                        <div class="referidos-info-value">{{ $referido->telefono ?? 'No registrado' }}</div>
                    </div>
                    <div class="referidos-info-item">
                        <div class="referidos-info-label">Fecha de Registro</div>
                        <div class="referidos-info-value">{{ $referido->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="referidos-info-item">
                        <div class="referidos-info-label">Días Activo</div>
                        <div class="referidos-info-value">{{ $referido->created_at->diffInDays(now()) }} días</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="referidos-card fade-in-up animate-delay-2">
                <div class="referidos-card-header">
                    <h3 class="referidos-card-title">
                        <i class="fas fa-chart-line"></i>
                        Estadísticas de Ventas
                    </h3>
                </div>
                <div class="referidos-card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="referidos-info-item text-center">
                                <div class="referidos-info-label">Total Ventas</div>
                                <div class="referidos-info-value" style="color: var(--wine); font-size: 1.75rem;">
                                    ${{ number_format($referido->total_ventas ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="referidos-info-item text-center">
                                <div class="referidos-info-label">Total Pedidos</div>
                                <div class="referidos-info-value" style="color: var(--info); font-size: 1.75rem;">
                                    {{ $referido->pedidos_vendedor_count ?? 0 }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="referidos-info-item text-center">
                                <div class="referidos-info-label">Promedio Venta</div>
                                <div class="referidos-info-value" style="color: var(--success); font-size: 1.75rem;">
                                    ${{ $referido->pedidos_vendedor_count > 0 ? number_format(($referido->total_ventas ?? 0) / $referido->pedidos_vendedor_count, 0, ',', '.') : '0' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="referidos-info-item text-center">
                                <div class="referidos-info-label">Sub-Referidos</div>
                                <div class="referidos-info-value" style="color: var(--warning); font-size: 1.75rem;">
                                    {{ $referido->referidos_count ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico de Ventas por Mes --}}
    <div class="referidos-content fade-in-up animate-delay-3">
        <h2 class="referidos-section-title">
            <i class="fas fa-chart-bar"></i>
            Evolución de Ventas (Últimos 6 Meses)
        </h2>
        <div class="referidos-chart-container">
            <canvas id="ventasChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    {{-- Sub-Referidos (Nivel 2) --}}
    @if($subReferidos->count() > 0)
    <div class="referidos-content fade-in-up animate-delay-4">
        <h2 class="referidos-section-title">
            <i class="fas fa-users"></i>
            Sus Referidos (Nivel 2)
        </h2>
        <div class="row">
            @foreach($subReferidos as $subReferido)
            <div class="col-md-4 mb-3">
                <div class="referidos-card">
                    <div class="referidos-card-header">
                        <div class="referidos-card-title">
                            <i class="fas fa-user"></i>
                            {{ $subReferido->name }}
                        </div>
                        <span class="referidos-badge referidos-badge-nivel-2">
                            <i class="fas fa-star"></i> Nivel 2
                        </span>
                    </div>
                    <div class="referidos-card-body">
                        <div class="referidos-info-item">
                            <div class="referidos-info-label">Email</div>
                            <div class="referidos-info-value">{{ $subReferido->email }}</div>
                        </div>
                        <div class="referidos-info-item">
                            <div class="referidos-info-label">Pedidos</div>
                            <div class="referidos-info-value">{{ $subReferido->pedidos_vendedor_count ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Comisiones Generadas --}}
    @if(isset($comisionesGeneradas) && $comisionesGeneradas->count() > 0)
    <div class="referidos-content fade-in-up animate-delay-5">
        <h2 class="referidos-section-title">
            <i class="fas fa-money-bill-wave"></i>
            Comisiones Generadas por este Referido
        </h2>
        <div class="referidos-table-container">
            <table class="referidos-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Venta</th>
                        <th>Monto Comisión</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comisionesGeneradas as $comision)
                    <tr>
                        <td>{{ $comision->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="referidos-badge referidos-badge-nivel-1">
                                {{ ucfirst($comision->tipo_comision) }}
                            </span>
                        </td>
                        <td>${{ number_format($comision->monto_venta ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <strong style="color: var(--success); font-size: 1.1rem;">
                                ${{ number_format($comision->monto_comision, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <span class="referidos-badge referidos-badge-{{ $comision->estado == 'pagada' ? 'activo' : 'productivo' }}">
                                {{ ucfirst($comision->estado) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="referidos-pagination mt-3">
            {{ $comisionesGeneradas->links() }}
        </div>
    </div>
    @endif
</div>

<div id="loading-overlay" class="referidos-loading-overlay">
    <div>
        <div class="referidos-loading-spinner"></div>
        <div class="referidos-loading-text">Cargando...</div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/referidos-modern.css') }}?v={{ filemtime(public_path('css/vendedor/referidos-modern.css')) }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/vendedor/referidos-modern.js') }}?v={{ filemtime(public_path('js/vendedor/referidos-modern.js')) }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de ventas por mes
    const ventasData = @json($ventasPorMes);
    const ctx = document.getElementById('ventasChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ventasData.map(item => item.mes),
            datasets: [{
                label: 'Ventas',
                data: ventasData.map(item => item.ventas),
                backgroundColor: 'rgba(114, 47, 55, 0.8)',
                borderColor: 'rgba(114, 47, 55, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: $' + context.parsed.y.toLocaleString('es-CO');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-CO');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
