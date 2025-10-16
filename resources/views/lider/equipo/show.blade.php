@extends('layouts.lider')

@section('title', '- Detalles del Miembro')
@section('page-title', 'Detalles del Miembro')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/equipo-show.css') }}?v={{ filemtime(public_path('css/lider/equipo-show.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Hero --}}
    <div class="equipo-detail-header fade-in-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="equipo-detail-avatar">
                    {{ strtoupper(substr($miembro->name, 0, 1)) }}
                </div>
                <div class="equipo-detail-info">
                    <h1 class="equipo-detail-name">{{ $miembro->name }} {{ $miembro->apellidos }}</h1>
                    <div class="equipo-detail-meta">
                        <span class="equipo-detail-badge">
                            <i class="bi bi-{{ $miembro->activo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                            {{ $miembro->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span class="equipo-detail-badge">
                            <i class="bi bi-person-badge"></i>
                            {{ ucfirst($miembro->rol) }}
                        </span>
                        <span class="equipo-detail-badge">
                            <i class="bi bi-envelope"></i>
                            {{ $miembro->email }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="equipo-detail-actions">
                <button type="button" class="equipo-detail-btn equipo-detail-btn-white" onclick="asignarMeta('{{ $miembro->_id }}')">
                    <i class="bi bi-bullseye"></i>
                    Asignar Meta
                </button>
                <a href="{{ route('lider.equipo.index') }}" class="equipo-detail-btn equipo-detail-btn-outline">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="equipo-stat-card-detail fade-in-up">
                <div class="equipo-stat-icon-detail" style="background: rgba(114, 47, 55, 0.1); color: #722F37;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="equipo-stat-value-detail">${{ number_format($stats['ventas_mes'], 0) }}</div>
                <div class="equipo-stat-label-detail">Ventas del Mes</div>
                <div class="equipo-stat-trend">
                    <i class="bi bi-calendar-month"></i>
                    {{ now()->isoFormat('MMMM YYYY') }}
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="equipo-stat-card-detail fade-in-up animate-delay-1">
                <div class="equipo-stat-icon-detail" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="equipo-stat-value-detail">{{ $stats['pedidos_mes'] }}</div>
                <div class="equipo-stat-label-detail">Pedidos del Mes</div>
                <div class="equipo-stat-trend">
                    <i class="bi bi-calculator"></i>
                    Promedio: ${{ number_format($stats['ticket_promedio'], 0) }}
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="equipo-stat-card-detail fade-in-up animate-delay-2">
                <div class="equipo-stat-icon-detail" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="equipo-stat-value-detail">{{ $stats['referidos_totales'] }}</div>
                <div class="equipo-stat-label-detail">Red Total</div>
                <div class="equipo-stat-trend text-success">
                    <i class="bi bi-arrow-up-circle-fill"></i>
                    +{{ $stats['referidos_mes'] }} este mes
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 mb-3">
            <div class="equipo-stat-card-detail fade-in-up animate-delay-3">
                <div class="equipo-stat-icon-detail" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-gem"></i>
                </div>
                <div class="equipo-stat-value-detail">${{ number_format($stats['comisiones_mes'], 0) }}</div>
                <div class="equipo-stat-label-detail">Comisiones del Mes</div>
                <div class="equipo-stat-trend">
                    <i class="bi bi-cash-stack"></i>
                    Ganadas
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Columna Principal --}}
        <div class="col-lg-8">
            {{-- Información Personal --}}
            <div class="equipo-info-card fade-in-up">
                <div class="equipo-info-card-header">
                    <i class="bi bi-person"></i>
                    <h3 class="equipo-info-card-title">Información Personal</h3>
                </div>
                <div class="equipo-info-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Nombres</label>
                                <div class="equipo-info-value">{{ $miembro->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Apellidos</label>
                                <div class="equipo-info-value">{{ $miembro->apellidos }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Cédula</label>
                                <div class="equipo-info-value">{{ $miembro->cedula }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Email</label>
                                <div class="equipo-info-value">{{ $miembro->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Teléfono</label>
                                <div class="equipo-info-value">{{ $miembro->telefono ?? 'No especificado' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="equipo-info-item">
                                <label class="equipo-info-label">Ciudad</label>
                                <div class="equipo-info-value">{{ $miembro->ciudad ?? 'No especificado' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Historial de Ventas --}}
            <div class="equipo-info-card fade-in-up animate-delay-1">
                <div class="equipo-info-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h3 class="equipo-info-card-title mb-0">Historial de Ventas</h3>
                    </div>
                    <div class="equipo-chart-controls">
                        <select id="periodoVentas" class="equipo-chart-select">
                            <option value="6">Últimos 6 meses</option>
                            <option value="12">Último año</option>
                            <option value="24">Últimos 2 años</option>
                            <option value="custom">Personalizado</option>
                        </select>
                        <button type="button" class="equipo-chart-btn" onclick="exportarHistorial()" title="Exportar datos">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>

                <div id="customDateRangeContainer" class="equipo-custom-date-range" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="equipo-form-label">
                                <i class="bi bi-calendar-event"></i>
                                Fecha Inicial
                            </label>
                            <input type="month"
                                   id="fechaInicial"
                                   class="equipo-form-control-sm"
                                   value="{{ now()->subMonths(6)->format('Y-m') }}"
                                   max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="equipo-form-label">
                                <i class="bi bi-calendar-check"></i>
                                Fecha Final
                            </label>
                            <input type="month"
                                   id="fechaFinal"
                                   class="equipo-form-control-sm"
                                   value="{{ now()->format('Y-m') }}"
                                   max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button"
                                    class="equipo-btn-apply w-100"
                                    onclick="aplicarFechasPersonalizadas()">
                                <i class="bi bi-check-lg"></i>
                                Aplicar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="equipo-info-card-body">
                    <div id="loadingChart" class="equipo-chart-loading" style="display: none;">
                        <div class="spinner-border text-wine" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando datos...</p>
                    </div>

                    <div id="chartContainer">
                        <div class="equipo-chart-stats">
                            <div class="equipo-chart-stat-item">
                                <span class="equipo-chart-stat-label">Total del Período</span>
                                <span class="equipo-chart-stat-value" id="totalPeriodo">${{ number_format($historialVentas->sum('ventas'), 0) }}</span>
                            </div>
                            <div class="equipo-chart-stat-item">
                                <span class="equipo-chart-stat-label">Promedio Mensual</span>
                                <span class="equipo-chart-stat-value" id="promedioMensual">${{ number_format($historialVentas->avg('ventas'), 0) }}</span>
                            </div>
                            <div class="equipo-chart-stat-item">
                                <span class="equipo-chart-stat-label">Mejor Mes</span>
                                <span class="equipo-chart-stat-value" id="mejorMes">
                                    @php $mejor = $historialVentas->sortByDesc('ventas')->first(); @endphp
                                    {{ $mejor['mes'] ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="equipo-chart-container" id="ventasChart">
                            @foreach($historialVentas as $dato)
                                <div class="equipo-chart-bar-container">
                                    @php
                                        $maxVenta = $historialVentas->max('ventas');
                                        $altura = $maxVenta > 0 ? ($dato['ventas'] / $maxVenta * 100) : 0;
                                        // Separar mes y año
                                        $partes = explode(' ', $dato['mes']);
                                        $mes = $partes[0] ?? '';
                                        $ano = $partes[1] ?? '';
                                    @endphp
                                    <div class="equipo-chart-bar-wrapper">
                                        <div class="equipo-chart-bar"
                                             style="height: {{ $altura }}%"
                                             data-valor="${{ number_format($dato['ventas'], 0) }}"
                                             data-mes="{{ $dato['mes'] }}"
                                             data-ventas="{{ $dato['ventas'] }}">
                                            <div class="equipo-chart-value">${{ number_format($dato['ventas'] / 1000, 0) }}K</div>
                                        </div>
                                    </div>
                                    <div class="equipo-chart-label">
                                        <span class="equipo-chart-label-mes">{{ $mes }}</span>
                                        <span class="equipo-chart-label-ano">{{ $ano }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Red del Miembro --}}
            <div class="equipo-info-card fade-in-up animate-delay-2">
                <div class="equipo-info-card-header">
                    <i class="bi bi-diagram-3"></i>
                    <h3 class="equipo-info-card-title">Red de Referidos ({{ $redMiembro->count() }})</h3>
                </div>
                <div class="equipo-info-card-body">
                    @if($redMiembro->count() > 0)
                        @foreach($redMiembro->take(10) as $referido)
                            <div class="equipo-referido-item">
                                <div class="equipo-referido-avatar">
                                    {{ strtoupper(substr($referido['usuario']->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <div class="equipo-referido-name">{{ $referido['usuario']->name }} {{ $referido['usuario']->apellidos }}</div>
                                    <div class="equipo-referido-stats">
                                        <span><i class="bi bi-currency-dollar"></i> ${{ number_format($referido['ventas_mes'], 0) }}</span>
                                        <span><i class="bi bi-people"></i> {{ $referido['referidos_propios'] }} referidos</span>
                                    </div>
                                </div>
                                <span class="equipo-badge equipo-badge-{{ $referido['usuario']->activo ? 'success' : 'danger' }}">
                                    {{ $referido['usuario']->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        @endforeach
                        @if($redMiembro->count() > 10)
                            <p class="text-center text-muted mt-3 mb-0">
                                Y {{ $redMiembro->count() - 10 }} más...
                            </p>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-diagram-3 text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2 mb-0">Aún no tiene referidos directos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Lateral --}}
        <div class="col-lg-4">
            {{-- Rendimiento --}}
            <div class="equipo-info-card fade-in-up">
                <div class="equipo-info-card-header">
                    <i class="bi bi-speedometer2"></i>
                    <h3 class="equipo-info-card-title">Rendimiento</h3>
                </div>
                <div class="equipo-info-card-body">
                    @php
                        $rendimiento = 0;
                        if ($miembro->meta_mensual && $miembro->meta_mensual > 0) {
                            $rendimiento = min(($stats['ventas_mes'] / $miembro->meta_mensual) * 100, 100);
                        }
                        $progressColor = $rendimiento >= 80 ? 'success' : ($rendimiento >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="equipo-progress-ring">
                        <svg viewBox="0 0 200 200">
                            <circle cx="100" cy="100" r="90" class="equipo-progress-ring-bg"></circle>
                            <circle cx="100" cy="100" r="90" class="equipo-progress-ring-fill equipo-progress-ring-{{ $progressColor }}"
                                    style="stroke-dashoffset: {{ 565.48 - (565.48 * $rendimiento / 100) }}"></circle>
                        </svg>
                        <div class="equipo-progress-ring-text">
                            <div class="equipo-progress-ring-value">{{ number_format($rendimiento, 1) }}%</div>
                            <div class="equipo-progress-ring-label">de la meta</div>
                        </div>
                    </div>
                    @if($miembro->meta_mensual)
                        <div class="text-center mt-3">
                            <p class="mb-1"><strong>Meta Mensual:</strong> ${{ number_format($miembro->meta_mensual, 0) }}</p>
                            <p class="mb-0 text-muted">Falta: ${{ number_format(max(0, $miembro->meta_mensual - $stats['ventas_mes']), 0) }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            No tiene meta asignada
                        </div>
                    @endif
                </div>
            </div>

            {{-- Estadísticas Adicionales --}}
            <div class="equipo-info-card fade-in-up animate-delay-1">
                <div class="equipo-info-card-header">
                    <i class="bi bi-bar-chart"></i>
                    <h3 class="equipo-info-card-title">Estadísticas</h3>
                </div>
                <div class="equipo-info-card-body">
                    <div class="equipo-info-item">
                        <label class="equipo-info-label">Ventas del Año</label>
                        <div class="equipo-info-value">${{ number_format($stats['ventas_ano'], 0) }}</div>
                    </div>
                    <div class="equipo-info-item">
                        <label class="equipo-info-label">Ticket Promedio</label>
                        <div class="equipo-info-value">${{ number_format($stats['ticket_promedio'], 0) }}</div>
                    </div>
                    <div class="equipo-info-item">
                        <label class="equipo-info-label">Días Activo</label>
                        <div class="equipo-info-value">{{ $stats['dias_activo'] }} días</div>
                    </div>
                    <div class="equipo-info-item">
                        <label class="equipo-info-label">Última Venta</label>
                        <div class="equipo-info-value">
                            @if($stats['ultima_venta'])
                                {{ $stats['ultima_venta']->diffForHumans() }}
                            @else
                                Sin ventas
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actividad Reciente --}}
            <div class="equipo-info-card fade-in-up animate-delay-2">
                <div class="equipo-info-card-header">
                    <i class="bi bi-clock-history"></i>
                    <h3 class="equipo-info-card-title">Actividad Reciente</h3>
                </div>
                <div class="equipo-info-card-body">
                    @if($actividadReciente->count() > 0)
                        @foreach($actividadReciente as $actividad)
                            <div class="equipo-actividad-item">
                                <div class="equipo-actividad-icon equipo-actividad-icon-{{ $actividad['tipo'] }}">
                                    <i class="bi bi-{{ $actividad['tipo'] == 'venta' ? 'cart-check' : 'person-plus' }}"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="equipo-actividad-desc">{{ $actividad['descripcion'] }}</div>
                                    <div class="equipo-actividad-time">{{ $actividad['fecha']->diffForHumans() }}</div>
                                </div>
                                @if(isset($actividad['monto']))
                                    <div class="equipo-actividad-monto">${{ number_format($actividad['monto'], 0) }}</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2 mb-0 small">Sin actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Asignar Meta --}}
<div class="equipo-modal-backdrop" id="metaModalBackdrop"></div>
<div class="equipo-modal-container" id="metaModal" role="dialog" aria-labelledby="metaModalTitle" aria-hidden="true">
    <div class="equipo-modal-glass">
        <div class="equipo-modal-header">
            <div class="equipo-modal-icon">
                <i class="bi bi-bullseye"></i>
            </div>
            <h4 class="equipo-modal-title" id="metaModalTitle">Asignar Meta Mensual</h4>
            <p class="equipo-modal-subtitle">Define objetivos de ventas para este miembro</p>
            <button type="button" class="equipo-modal-close" onclick="cerrarModalMeta()" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="metaForm" method="POST">
            @csrf
            <div class="equipo-modal-body">
                <div class="equipo-form-field">
                    <label class="equipo-form-field-label">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Meta Mensual
                    </label>
                    <div class="equipo-input-group">
                        <span class="equipo-input-addon">$</span>
                        <input type="number"
                               class="equipo-form-control"
                               id="meta_mensual_input"
                               name="meta_mensual"
                               step="1000"
                               min="0"
                               placeholder="5000000"
                               value="{{ $miembro->meta_mensual ?? '' }}"
                               required>
                        <span class="equipo-input-badge">COP</span>
                    </div>
                    <small class="equipo-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Ingresa el monto objetivo en pesos colombianos
                    </small>
                </div>

                <div class="equipo-form-field">
                    <label class="equipo-form-field-label">
                        <i class="bi bi-calendar-event me-2"></i>
                        Período
                    </label>
                    <input type="month"
                           class="equipo-form-control"
                           id="mes_input"
                           name="mes"
                           value="{{ now()->format('Y-m') }}"
                           required>
                    <small class="equipo-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Selecciona el mes para aplicar la meta
                    </small>
                </div>

                @if($miembro->meta_mensual)
                <div class="equipo-alert-info" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); border-color: rgba(16, 185, 129, 0.2);">
                    <div class="equipo-alert-icon" style="color: var(--success);">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="equipo-alert-content">
                        <strong>Meta actual:</strong> ${{ number_format($miembro->meta_mensual, 0) }} COP
                        <br><small>Modifica el valor arriba para actualizar la meta</small>
                    </div>
                </div>
                @endif

                <div class="equipo-alert-info">
                    <div class="equipo-alert-icon">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <div class="equipo-alert-content">
                        <strong>Consejo:</strong>
                        Establece metas alcanzables basadas en el rendimiento histórico. Las metas motivantes suelen estar entre 10-20% por encima del promedio actual.
                    </div>
                </div>
            </div>

            <div class="equipo-modal-footer">
                <button type="button" class="equipo-btn-secondary" onclick="cerrarModalMeta()">
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>
                <button type="submit" class="equipo-btn-primary">
                    <i class="bi bi-check-lg"></i>
                    Asignar Meta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function asignarMeta(miembroId) {
    const modal = document.getElementById('metaModal');
    const backdrop = document.getElementById('metaModalBackdrop');
    const form = document.getElementById('metaForm');

    form.action = `/lider/equipo/${miembroId}/asignar-meta`;

    backdrop.classList.add('active');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModalMeta() {
    const modal = document.getElementById('metaModal');
    const backdrop = document.getElementById('metaModalBackdrop');

    backdrop.classList.remove('active');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('metaModal');
        if (modal && modal.classList.contains('active')) {
            cerrarModalMeta();
        }
    }
});

// Cerrar al hacer click en el backdrop
document.getElementById('metaModalBackdrop')?.addEventListener('click', cerrarModalMeta);

// Manejo del formulario con AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('metaForm');

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Deshabilitar el botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Asignando...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Actualizar los elementos de la página con los nuevos datos
                    actualizarDatosEnTiempoReal(result.data);

                    // Mostrar mensaje de éxito
                    mostrarToast('Meta asignada exitosamente', 'success');

                    // Cerrar modal
                    cerrarModalMeta();
                } else {
                    throw new Error(result.message || 'Error al asignar la meta');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('Error al asignar la meta: ' + error.message, 'error');
            } finally {
                // Restaurar el botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});

function actualizarDatosEnTiempoReal(data) {
    // Actualizar el anillo de progreso
    const progressRing = document.querySelector('.equipo-progress-ring-fill');
    const progressValue = document.querySelector('.equipo-progress-ring-value');

    if (progressRing && progressValue) {
        // Actualizar valor
        progressValue.textContent = data.rendimiento + '%';

        // Actualizar color
        progressRing.classList.remove('equipo-progress-ring-success', 'equipo-progress-ring-warning', 'equipo-progress-ring-danger');
        progressRing.classList.add('equipo-progress-ring-' + data.progress_color);

        // Animar el stroke
        progressRing.style.strokeDashoffset = data.stroke_dashoffset;
    }

    // Actualizar meta mensual en el texto
    const metaTexto = document.querySelector('.equipo-progress-ring').parentElement.querySelector('p strong');
    if (metaTexto) {
        metaTexto.nextSibling.textContent = ' ' + data.meta_mensual_formateado;
    }

    // Actualizar el texto "Falta"
    const faltaTexto = document.querySelector('.equipo-progress-ring').parentElement.querySelectorAll('p')[1];
    if (faltaTexto) {
        faltaTexto.lastChild.textContent = ' ' + data.falta_formateado;
    }

    // Actualizar el input del formulario con la nueva meta
    const metaInput = document.getElementById('meta_mensual_input');
    if (metaInput) {
        metaInput.value = data.meta_mensual;
    }

    // Añadir animación de pulso al card de rendimiento
    const rendimientoCard = document.querySelector('.equipo-progress-ring').closest('.equipo-info-card');
    if (rendimientoCard) {
        rendimientoCard.style.animation = 'none';
        setTimeout(() => {
            rendimientoCard.style.animation = 'pulse 0.5s ease-out';
        }, 10);
    }
}

function mostrarToast(mensaje, tipo = 'success') {
    // Crear toast
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${tipo}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${tipo === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
        color: white;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        animation: slideInRight 0.3s ease-out;
    `;

    const icon = tipo === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill';
    toast.innerHTML = `
        <i class="bi bi-${icon}" style="font-size: 1.25rem;"></i>
        <span>${mensaje}</span>
    `;

    document.body.appendChild(toast);

    // Remover después de 3 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Añadir estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); box-shadow: 0 10px 25px rgba(114, 47, 55, 0.2); }
    }
`;
document.head.appendChild(style);

// Gestión del historial de ventas
const miembroId = '{{ $miembro->_id }}';

// Cambio de período
document.getElementById('periodoVentas')?.addEventListener('change', function(e) {
    const periodo = e.target.value;
    const customContainer = document.getElementById('customDateRangeContainer');

    if (periodo === 'custom') {
        customContainer.style.display = 'block';
    } else {
        customContainer.style.display = 'none';
        cargarHistorialVentas(periodo);
    }
});

// Cargar historial de ventas
async function cargarHistorialVentas(meses) {
    const chartContainer = document.getElementById('chartContainer');
    const loadingChart = document.getElementById('loadingChart');

    // Mostrar loading
    chartContainer.style.opacity = '0.3';
    loadingChart.style.display = 'flex';

    try {
        const response = await fetch(`/lider/equipo/${miembroId}/historial-ventas?meses=${meses}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            actualizarGraficoVentas(data.data);
            mostrarToast('Historial actualizado correctamente', 'success');
        } else {
            throw new Error(data.message || 'Error al cargar el historial');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al cargar el historial: ' + error.message, 'error');
    } finally {
        chartContainer.style.opacity = '1';
        loadingChart.style.display = 'none';
    }
}

// Aplicar fechas personalizadas
async function aplicarFechasPersonalizadas() {
    const fechaInicial = document.getElementById('fechaInicial').value;
    const fechaFinal = document.getElementById('fechaFinal').value;

    if (!fechaInicial || !fechaFinal) {
        mostrarToast('Por favor selecciona ambas fechas', 'error');
        return;
    }

    if (fechaInicial > fechaFinal) {
        mostrarToast('La fecha inicial no puede ser mayor a la final', 'error');
        return;
    }

    const chartContainer = document.getElementById('chartContainer');
    const loadingChart = document.getElementById('loadingChart');

    chartContainer.style.opacity = '0.3';
    loadingChart.style.display = 'flex';

    try {
        const response = await fetch(`/lider/equipo/${miembroId}/historial-ventas?fecha_inicial=${fechaInicial}&fecha_final=${fechaFinal}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            actualizarGraficoVentas(data.data);
            mostrarToast('Historial personalizado cargado', 'success');
        } else {
            throw new Error(data.message || 'Error al cargar el historial');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al cargar el historial: ' + error.message, 'error');
    } finally {
        chartContainer.style.opacity = '1';
        loadingChart.style.display = 'none';
    }
}

// Actualizar gráfico con nuevos datos
function actualizarGraficoVentas(data) {
    const ventasChart = document.getElementById('ventasChart');

    // Actualizar estadísticas
    document.getElementById('totalPeriodo').textContent = '$' + formatNumber(data.total);
    document.getElementById('promedioMensual').textContent = '$' + formatNumber(data.promedio);
    document.getElementById('mejorMes').textContent = data.mejor_mes || 'N/A';

    // Generar barras
    const maxVenta = Math.max(...data.ventas.map(v => v.ventas));

    let html = '';
    data.ventas.forEach(dato => {
        const altura = maxVenta > 0 ? (dato.ventas / maxVenta * 100) : 0;
        const partes = dato.mes.split(' ');
        const mes = partes[0] || '';
        const ano = partes[1] || '';
        const valorK = Math.round(dato.ventas / 1000);

        html += `
            <div class="equipo-chart-bar-container">
                <div class="equipo-chart-bar-wrapper">
                    <div class="equipo-chart-bar"
                         style="height: ${altura}%"
                         data-valor="$${formatNumber(dato.ventas)}"
                         data-mes="${dato.mes}"
                         data-ventas="${dato.ventas}">
                        <div class="equipo-chart-value">$${valorK}K</div>
                    </div>
                </div>
                <div class="equipo-chart-label">
                    <span class="equipo-chart-label-mes">${mes}</span>
                    <span class="equipo-chart-label-ano">${ano}</span>
                </div>
            </div>
        `;
    });

    ventasChart.innerHTML = html;
}

// Exportar historial
function exportarHistorial() {
    const periodo = document.getElementById('periodoVentas').value;
    let url = `/lider/equipo/${miembroId}/exportar-historial?`;

    if (periodo === 'custom') {
        const fechaInicial = document.getElementById('fechaInicial').value;
        const fechaFinal = document.getElementById('fechaFinal').value;
        url += `fecha_inicial=${fechaInicial}&fecha_final=${fechaFinal}`;
    } else {
        url += `meses=${periodo}`;
    }

    window.location.href = url;
    mostrarToast('Descargando historial...', 'success');
}

// Formatear números
function formatNumber(num) {
    return new Intl.NumberFormat('es-CO').format(num);
}
</script>
@endpush
