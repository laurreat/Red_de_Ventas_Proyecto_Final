@extends('layouts.lider')

@section('title', ' - Estructura de la Red')
@section('page-title', 'Estructura de la Red')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Visualiza la estructura completa de tu red de referidos</p>
        </div>
        <div>
            <a href="{{ route('lider.referidos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Volver a Referidos
            </a>
        </div>
    </div>

    <!-- Métricas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Volumen Total Red
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metricas['volumen_red'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Comisiones Generadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metricas['comisiones_generadas'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-coin fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tasa de Actividad
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $metricas['tasa_actividad'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-activity fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Profundidad Máxima
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $metricas['profundidad_maxima'] }} niveles
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-layers fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas por Nivel -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Estadísticas por Nivel</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nivel</th>
                            <th>Total Referidos</th>
                            <th>Activos</th>
                            <th>Ventas del Mes</th>
                            <th>Promedio por Referido</th>
                            <th>% Actividad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estadisticasNivel as $nivel => $stats)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">Nivel {{ $nivel }}</span>
                                </td>
                                <td class="font-weight-bold">{{ $stats['total'] }}</td>
                                <td>
                                    <span class="text-success font-weight-bold">{{ $stats['activos'] }}</span>
                                </td>
                                <td class="text-success">
                                    ${{ number_format($stats['ventas_mes'], 0, ',', '.') }}
                                </td>
                                <td>
                                    ${{ number_format($stats['promedio_ventas'], 0, ',', '.') }}
                                </td>
                                <td>
                                    @php
                                        $porcentajeActividad = $stats['total'] > 0 ? ($stats['activos'] / $stats['total']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $porcentajeActividad >= 70 ? 'success' : ($porcentajeActividad >= 40 ? 'warning' : 'danger') }}"
                                             style="width: {{ $porcentajeActividad }}%">
                                            {{ round($porcentajeActividad) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estructura Visual de la Red -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Estructura Visual de la Red</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="expandirTodo()">
                    <i class="bi bi-arrows-expand me-1"></i>Expandir Todo
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="contraerTodo()">
                    <i class="bi bi-arrows-collapse me-1"></i>Contraer Todo
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Nodo Principal (Líder) -->
            <div class="network-container">
                <div class="network-node leader-node">
                    <div class="node-avatar bg-primary">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="node-info">
                        <div class="node-name">{{ auth()->user()->name }}</div>
                        <div class="node-title">LÍDER</div>
                        <div class="node-stats">
                            <small class="text-muted">Red Total: {{ count($redCompleta) }}</small>
                        </div>
                    </div>
                </div>

                <!-- Estructura de Niveles -->
                @if(!empty($redCompleta))
                    <div class="network-levels">
                        @foreach($redCompleta as $miembro)
                            <div class="network-level" data-nivel="{{ $miembro['nivel'] }}">
                                <div class="network-node referido-node" data-user-id="{{ $miembro['usuario']->id }}">
                                    <div class="node-connector"></div>
                                    <div class="node-content">
                                        <div class="node-avatar bg-{{ $miembro['usuario']->activo ? 'success' : 'secondary' }}">
                                            {{ strtoupper(substr($miembro['usuario']->name, 0, 1)) }}
                                        </div>
                                        <div class="node-info">
                                            <div class="node-name">{{ $miembro['usuario']->name }}</div>
                                            <div class="node-title">Nivel {{ $miembro['nivel'] }}</div>
                                            <div class="node-stats">
                                                <div class="stat-item">
                                                    <small class="text-success">
                                                        ${{ number_format($miembro['stats']['ventas_periodo'], 0, ',', '.') }}
                                                    </small>
                                                </div>
                                                <div class="stat-item">
                                                    <small class="text-info">
                                                        {{ $miembro['stats']['referidos_totales'] }} refs
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        @if($miembro['usuario']->activo)
                                            <div class="node-status active">
                                                <i class="bi bi-check-circle"></i>
                                            </div>
                                        @else
                                            <div class="node-status inactive">
                                                <i class="bi bi-x-circle"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Botón para expandir sub-red -->
                                    @if(count($miembro['hijos']) > 0)
                                        <button class="expand-btn" onclick="toggleSubRed({{ $miembro['usuario']->id }})">
                                            <i class="bi bi-plus-circle"></i>
                                            <span class="expand-count">{{ count($miembro['hijos']) }}</span>
                                        </button>

                                        <!-- Sub-red (inicialmente oculta) -->
                                        <div class="sub-network" id="subred-{{ $miembro['usuario']->id }}" style="display: none;">
                                            @foreach($miembro['hijos'] as $hijo)
                                                <div class="network-node sub-referido-node">
                                                    <div class="node-connector sub-connector"></div>
                                                    <div class="node-content">
                                                        <div class="node-avatar bg-{{ $hijo['usuario']->activo ? 'info' : 'secondary' }}">
                                                            {{ strtoupper(substr($hijo['usuario']->name, 0, 1)) }}
                                                        </div>
                                                        <div class="node-info">
                                                            <div class="node-name">{{ $hijo['usuario']->name }}</div>
                                                            <div class="node-title">Nivel {{ $hijo['nivel'] }}</div>
                                                            <div class="node-stats">
                                                                <small class="text-success">
                                                                    ${{ number_format($hijo['stats']['ventas_periodo'], 0, ',', '.') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-person-plus fs-1 d-block mb-3"></i>
                        <h5>Tu red está esperando crecer</h5>
                        <p>Comienza invitando a tus primeros referidos para ver la estructura de tu red aquí.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Análisis de Rendimiento -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rendimiento por Nivel</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="rendimientoNivelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Crecimiento de la Red</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="crecimientoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.network-container {
    position: relative;
    padding: 20px;
}

.network-node {
    display: flex;
    align-items: center;
    margin: 15px 0;
    position: relative;
}

.leader-node {
    justify-content: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.leader-node .node-avatar {
    width: 80px;
    height: 80px;
    font-size: 2rem;
    margin-right: 20px;
}

.referido-node {
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    padding: 15px;
    margin-left: 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.referido-node:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.sub-referido-node {
    background: #f8f9fc;
    border: 1px solid #d1d3e2;
    border-radius: 8px;
    padding: 10px;
    margin-left: 30px;
    margin-top: 10px;
}

.node-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
    color: white;
}

.node-info {
    flex-grow: 1;
}

.node-name {
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 2px;
}

.node-title {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.node-stats {
    display: flex;
    gap: 10px;
}

.node-status {
    margin-left: 10px;
    font-size: 1.2rem;
}

.node-status.active {
    color: #28a745;
}

.node-status.inactive {
    color: #6c757d;
}

.expand-btn {
    position: absolute;
    right: -15px;
    top: 50%;
    transform: translateY(-50%);
    background: #4e73df;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.expand-btn:hover {
    background: #2e59d9;
    transform: translateY(-50%) scale(1.1);
}

.expand-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.node-connector {
    position: absolute;
    left: -25px;
    top: 50%;
    width: 25px;
    height: 2px;
    background: #d1d3e2;
}

.sub-connector {
    background: #b3b3cc;
    left: -15px;
    width: 15px;
}

.network-levels {
    margin-top: 20px;
}

.sub-network {
    margin-top: 15px;
    padding-left: 20px;
    border-left: 2px dashed #d1d3e2;
}

@media (max-width: 768px) {
    .referido-node {
        margin-left: 20px;
        padding: 10px;
    }

    .node-avatar {
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }

    .leader-node .node-avatar {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Función para expandir/contraer sub-red
function toggleSubRed(userId) {
    const subRed = document.getElementById(`subred-${userId}`);
    const expandBtn = subRed.previousElementSibling;
    const icon = expandBtn.querySelector('i');

    if (subRed.style.display === 'none') {
        subRed.style.display = 'block';
        icon.className = 'bi bi-dash-circle';
    } else {
        subRed.style.display = 'none';
        icon.className = 'bi bi-plus-circle';
    }
}

// Función para expandir todo
function expandirTodo() {
    const subRedes = document.querySelectorAll('.sub-network');
    const expandBtns = document.querySelectorAll('.expand-btn i');

    subRedes.forEach(subRed => {
        subRed.style.display = 'block';
    });

    expandBtns.forEach(icon => {
        icon.className = 'bi bi-dash-circle';
    });
}

// Función para contraer todo
function contraerTodo() {
    const subRedes = document.querySelectorAll('.sub-network');
    const expandBtns = document.querySelectorAll('.expand-btn i');

    subRedes.forEach(subRed => {
        subRed.style.display = 'none';
    });

    expandBtns.forEach(icon => {
        icon.className = 'bi bi-plus-circle';
    });
}

// Gráfico de Rendimiento por Nivel
const rendimientoCtx = document.getElementById('rendimientoNivelChart').getContext('2d');
const estadisticas = @json($estadisticasNivel);
const niveles = Object.keys(estadisticas);
const ventasNivel = niveles.map(nivel => estadisticas[nivel].ventas_mes);

new Chart(rendimientoCtx, {
    type: 'bar',
    data: {
        labels: niveles.map(n => `Nivel ${n}`),
        datasets: [{
            label: 'Ventas del Mes',
            data: ventasNivel,
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(246, 194, 62, 0.8)',
                'rgba(231, 74, 59, 0.8)'
            ],
            borderColor: [
                'rgba(78, 115, 223, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(54, 185, 204, 1)',
                'rgba(246, 194, 62, 1)',
                'rgba(231, 74, 59, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Gráfico de Crecimiento (simulado)
const crecimientoCtx = document.getElementById('crecimientoChart').getContext('2d');
const mesesPasados = [];
const crecimientoData = [];

for (let i = 5; i >= 0; i--) {
    const fecha = new Date();
    fecha.setMonth(fecha.getMonth() - i);
    mesesPasados.push(fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' }));
    crecimientoData.push(Math.floor(Math.random() * 50) + (5 - i) * 10);
}

new Chart(crecimientoCtx, {
    type: 'line',
    data: {
        labels: mesesPasados,
        datasets: [{
            label: 'Nuevos Referidos',
            data: crecimientoData,
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection