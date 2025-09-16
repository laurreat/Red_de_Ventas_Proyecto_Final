@extends('layouts.lider')

@section('title', ' - Red de Referidos')
@section('page-title', 'Red de Referidos')

@section('content')
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Gestiona y monitorea tu red de referidos</p>
        </div>
        <div>
            <a href="{{ route('lider.referidos.red') }}" class="btn btn-outline-primary">
                <i class="bi bi-diagram-3 me-1"></i>
                Ver Estructura
            </a>
        </div>
    </div>

    <!-- Cards de estadísticas de la red -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Red
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statsRed['total_referidos'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statsRed['activos'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Nivel 1
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statsRed['nivel_1'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Nivel 2
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statsRed['nivel_2'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-diagram-2 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Ventas Red
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($statsRed['ventas_totales'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Crecimiento
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $statsRed['crecimiento_mensual'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-up-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Evolución de la Red -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Evolución de la Red</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="evolucionRedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución por Niveles -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Niveles</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="nivelesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar Referidos</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lider.referidos.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search">Buscar</label>
                        <input type="text"
                               name="search"
                               id="search"
                               class="form-control"
                               placeholder="Nombre, email o cédula"
                               value="{{ $search }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="nivel">Nivel</label>
                        <select name="nivel" id="nivel" class="form-control">
                            <option value="todos" {{ $nivel == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="directos" {{ $nivel == 'directos' ? 'selected' : '' }}>Directos</option>
                            <option value="segundo" {{ $nivel == 'segundo' ? 'selected' : '' }}>Segundo Nivel</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="periodo">Periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Mes Actual</option>
                            <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Semana Actual</option>
                            <option value="trimestre_actual" {{ $periodo == 'trimestre_actual' ? 'selected' : '' }}>Trimestre</option>
                            <option value="ano_actual" {{ $periodo == 'ano_actual' ? 'selected' : '' }}>Año Actual</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ $estado == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('lider.referidos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Referidos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mi Red de Referidos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Referido</th>
                            <th>Nivel</th>
                            <th>Ventas</th>
                            <th>Sus Referidos</th>
                            <th>Rendimiento</th>
                            <th>Última Actividad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redConStats as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($item['referido']->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $item['referido']->name }}</div>
                                            <div class="text-muted small">{{ $item['referido']->email }}</div>
                                            <div class="text-muted small">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $item['fecha_ingreso']->format('d/m/Y') }}
                                                ({{ $item['dias_activo'] }} días)
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-outline-primary">
                                        Nivel {{ $item['nivel'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-success">
                                        ${{ number_format($item['ventas_periodo'], 0, ',', '.') }}
                                    </div>
                                    <div class="small text-muted">{{ $item['pedidos_periodo'] }} pedidos</div>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $item['referidos_totales'] }}</div>
                                    @if($item['referidos_periodo'] > 0)
                                        <div class="small text-success">
                                            +{{ $item['referidos_periodo'] }} este periodo
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $item['rendimiento'] >= 70 ? 'success' : ($item['rendimiento'] >= 40 ? 'warning' : 'danger') }}"
                                             style="width: {{ $item['rendimiento'] }}%"></div>
                                    </div>
                                    <div class="small text-center">{{ round($item['rendimiento']) }}%</div>
                                </td>
                                <td>
                                    @if($item['ultima_actividad'])
                                        <div class="small">
                                            {{ $item['ultima_actividad']->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-muted">Sin actividad</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['referido']->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('lider.equipo.show', $item['referido']->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                onclick="contactarReferido('{{ $item['referido']->name }}', '{{ $item['referido']->email }}')"
                                                title="Contactar">
                                            <i class="bi bi-chat-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                    No se encontraron referidos con los filtros seleccionados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    @if($topPerformers->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performers del Periodo</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($topPerformers->take(6) as $index => $performer)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-left-{{ $index < 3 ? 'success' : 'info' }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-{{ $index < 3 ? 'success' : 'info' }} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                #{{ $index + 1 }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold">{{ $performer['referido']->name }}</div>
                                            <div class="text-success font-weight-bold">
                                                ${{ number_format($performer['ventas_periodo'], 0, ',', '.') }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $performer['pedidos_periodo'] }} pedidos •
                                                {{ $performer['referidos_periodo'] }} referidos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Evolución de la Red
const evolucionCtx = document.getElementById('evolucionRedChart').getContext('2d');
new Chart(evolucionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucionRed->pluck('mes')) !!},
        datasets: [{
            label: 'Total Red',
            data: {!! json_encode($evolucionRed->pluck('total')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
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

// Gráfico de Distribución por Niveles
const nivelesCtx = document.getElementById('nivelesChart').getContext('2d');
const distribucionData = @json($distribucionNiveles);
const labels = Object.keys(distribucionData).map(key => key.replace('_', ' ').toUpperCase());
const data = Object.values(distribucionData);

new Chart(nivelesCtx, {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Función para contactar referido
function contactarReferido(nombre, email) {
    const mensaje = `¡Hola ${nombre}! Te escribo desde el sistema para...`;
    const subject = 'Seguimiento - Red de Ventas';
    const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(mensaje)}`;

    window.location.href = mailtoLink;
}
</script>
@endpush
@endsection