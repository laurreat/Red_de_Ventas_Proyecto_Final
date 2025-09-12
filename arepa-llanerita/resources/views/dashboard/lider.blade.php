@extends('layouts.app')

@section('title', '- Dashboard Líder')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/lider-dashboard.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">Dashboard Líder</h1>
                    <p class="text-muted mb-0">Panel de gestión de equipo y metas</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if($stats['meta_mensual'] > 0)
                    <div class="bg-primary text-white px-3 py-2 rounded">
                        <small>Meta Mensual</small>
                        <div class="fw-bold">${{ number_format($stats['meta_mensual']) }}</div>
                    </div>
                    @endif
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-calendar-check me-1"></i>
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill text-primary fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['total_equipo']) }}</div>
                    <div class="metric-label">Mi Equipo</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-success fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['ventas_equipo_mes'], 0) }}</div>
                    <div class="metric-label">Ventas del Equipo</div>
                    @if($stats['meta_mensual'] > 0)
                    <div class="progress-custom mt-2">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ min(($stats['ventas_equipo_mes'] / $stats['meta_mensual']) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <small class="text-muted">{{ number_format(min(($stats['ventas_equipo_mes'] / $stats['meta_mensual']) * 100, 100), 1) }}% de la meta</small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin text-info fs-1 mb-3"></i>
                    <div class="metric-value">${{ number_format($stats['comisiones_mes'], 0) }}</div>
                    <div class="metric-label">Comisiones del Mes</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus text-warning fs-1 mb-3"></i>
                    <div class="metric-value">{{ number_format($stats['nuevos_mes']) }}</div>
                    <div class="metric-label">Nuevos este Mes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rendimiento del Equipo -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Rendimiento del Equipo
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Reportes Detallados')">
                        Ver reportes
                    </button>
                </div>
                <div class="card-body">
                    @if($equipo->count() > 0)
                        <div class="row">
                            @foreach($equipo as $miembro)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="team-member-card card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $miembro->name }}</div>
                                                <small class="text-muted">{{ ucfirst($miembro->rol) }}</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <small>Ventas del mes</small>
                                                <strong>${{ number_format($miembro->ventas_mes_actual, 0) }}</strong>
                                            </div>
                                            @if($miembro->meta_mensual > 0)
                                            <div class="progress-custom mt-1">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: {{ min(($miembro->ventas_mes_actual / $miembro->meta_mensual) * 100, 100) }}%"></div>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format(min(($miembro->ventas_mes_actual / $miembro->meta_mensual) * 100, 100), 1) }}% de meta</small>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">Referidos</small>
                                                <div class="fw-bold">{{ $miembro->total_referidos }}</div>
                                            </div>
                                            <div>
                                                @php
                                                    $performance = $miembro->meta_mensual > 0 ? ($miembro->ventas_mes_actual / $miembro->meta_mensual) * 100 : 0;
                                                    $badgeClass = $performance >= 80 ? 'success' : ($performance >= 60 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="performance-badge bg-{{ $badgeClass }} text-white">
                                                    @if($performance >= 80) Excelente
                                                    @elseif($performance >= 60) Bueno
                                                    @else Necesita apoyo
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mb-0">No tienes miembros en tu equipo aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen y Metas -->
        <div class="col-xl-4 mb-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-target me-2"></i>
                        Progreso de Metas
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['meta_mensual'] > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Meta Individual</span>
                            <strong>${{ number_format($stats['meta_mensual']) }}</strong>
                        </div>
                        <div class="progress-custom mt-1">
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ min(($stats['ventas_personales'] / $stats['meta_mensual']) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <small class="text-muted">Vendido: ${{ number_format($stats['ventas_personales']) }}</small>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Meta del Equipo</span>
                            <strong>${{ number_format($stats['meta_equipo']) }}</strong>
                        </div>
                        <div class="progress-custom mt-1">
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $stats['meta_equipo'] > 0 ? min(($stats['ventas_equipo_mes'] / $stats['meta_equipo']) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <small class="text-muted">Vendido: ${{ number_format($stats['ventas_equipo_mes']) }}</small>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-award me-2"></i>
                        Logros Recientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center py-2">
                        <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="bi bi-star-fill small"></i>
                        </div>
                        <div>
                            <div class="fw-medium">Equipo Activo</div>
                            <small class="text-muted">{{ $stats['total_equipo'] }} miembros activos</small>
                        </div>
                    </div>
                    
                    @if($stats['comisiones_mes'] > 100000)
                    <div class="d-flex align-items-center py-2">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="bi bi-coin small"></i>
                        </div>
                        <div>
                            <div class="fw-medium">Meta de Comisiones</div>
                            <small class="text-muted">Más de $100K en comisiones</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($stats['nuevos_mes'] >= 3)
                    <div class="d-flex align-items-center py-2">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="bi bi-person-plus-fill small"></i>
                        </div>
                        <div>
                            <div class="fw-medium">Reclutador Exitoso</div>
                            <small class="text-muted">{{ $stats['nuevos_mes'] }} nuevos este mes</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($stats['total_equipo'] == 0 && $stats['nuevos_mes'] == 0)
                    <div class="text-center py-3">
                        <i class="bi bi-trophy fs-3 text-muted"></i>
                        <p class="text-muted mb-0">Construye tu equipo para desbloquear logros</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animar barras de progreso
        setTimeout(function() {
            document.querySelectorAll('.progress-bar').forEach(function(bar) {
                bar.style.transition = 'width 1s ease-in-out';
            });
        }, 100);
    });
</script>
@endpush