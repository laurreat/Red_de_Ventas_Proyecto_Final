@extends('layouts.admin')

@section('title', '- Red de Referidos - ' . $usuario->name)
@section('page-title', 'Red de Referidos')

@section('content')
<div class="container-fluid">
    <!-- Header del usuario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person-circle text-white fs-2"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold" style="color: black;">{{ $usuario->name }}</h4>
                                <p class="text-muted mb-1" style="color: black;">{{ $usuario->email }}</p>
                                <span class="badge bg-{{ $usuario->rol == 'lider' ? 'warning' : 'info' }}">
                                    {{ ucfirst($usuario->rol) }}
                                </span>
                                @if($usuario->codigo_referido)
                                    <span class="badge bg-secondary ms-2">Código: {{ $usuario->codigo_referido }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.referidos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Volver a Red de Referidos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del usuario -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 50px; height: 50px; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="bi bi-people fs-3 text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-success" style="color: black !important;">{{ $statsUsuario['referidos_directos'] }}</h4>
                    <p class="text-muted mb-0 small" style="color: black;">Referidos Directos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 50px; height: 50px; background-color: rgba(114, 47, 55, 0.1);">
                        <i class="bi bi-diagram-3 fs-3" style="color: var(--primary-color);"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $statsUsuario['referidos_totales'] }}</h4>
                    <p class="text-muted mb-0 small" style="color: black;">Red Total</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 50px; height: 50px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="bi bi-currency-dollar fs-3 text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-warning" style="color: black !important;">${{ number_format($statsUsuario['ventas_referidos'], 0) }}</h4>
                    <p class="text-muted mb-0 small" style="color: black;">Ventas Red</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-cash-coin fs-3 text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-danger" style="color: black !important;">${{ number_format($statsUsuario['comisiones_referidos'], 0) }}</h4>
                    <p class="text-muted mb-0 small" style="color: black;">Comisiones</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Red Jerárquica -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Estructura de Red (Nivel {{ $statsUsuario['nivel_en_red'] }})
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($redCompleta) > 0)
                        <div class="red-jerarquica">
                            @include('admin.referidos.partials.red-node', ['red' => $redCompleta, 'nivel' => 1])
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mb-0" style="color: black;">Este usuario aún no tiene referidos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-clock-history me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    @if($actividadReciente->count() > 0)
                        @foreach($actividadReciente as $actividad)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-person-plus text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium" style="color: black;">{{ $actividad->name }}</div>
                                <small class="text-muted" style="color: black;">{{ $actividad->email }}</small>
                                <div>
                                    <small class="text-muted" style="color: black;">
                                        Se unió {{ $actividad->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-{{ $actividad->rol == 'lider' ? 'warning' : 'info' }}">
                                    {{ ucfirst($actividad->rol) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-clock fs-1 text-muted"></i>
                            <p class="text-muted mb-0" style="color: black;">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold" style="color: var(--primary-color);">
                        <i class="bi bi-info-circle me-2"></i>
                        Información Adicional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong style="color: black;">Fecha de Registro:</strong>
                               <span style="color: black;">{{ $statsUsuario['fecha_registro']->format('d/m/Y H:i') }}</span></p>
                            <p><strong style="color: black;">Código de Referido:</strong>
                               <span style="color: black;">{{ $usuario->codigo_referido ?? 'No asignado' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong style="color: black;">Nivel en la Red:</strong>
                               <span style="color: black;">Nivel {{ $statsUsuario['nivel_en_red'] }}</span></p>
                            <p><strong style="color: black;">Estado:</strong>
                               <span class="badge bg-success">Activo</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Estilos específicos movidos a: public/css/admin/referidos-show.css --}}
<link rel="stylesheet" href="{{ asset('css/admin/referidos-show.css') }}">
@endpush