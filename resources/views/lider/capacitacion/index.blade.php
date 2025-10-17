@extends('layouts.lider')

@section('title', '- Capacitación del Equipo')
@section('page-title', 'Capacitación del Equipo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/capacitacion-modern.css') }}?v={{ filemtime(public_path('css/lider/capacitacion-modern.css')) }}">
<style>
/* Sección de filtros */
.capacitacion-filters-section {
    margin-bottom: 2rem;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-pills {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-pill {
    padding: 0.625rem 1.25rem;
    border: 2px solid var(--gray-300);
    border-radius: 50px;
    background: var(--white);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-pill:hover {
    border-color: var(--wine);
    background: rgba(114, 47, 55, 0.05);
    transform: translateY(-2px);
}

.filter-pill.active {
    background: linear-gradient(135deg, var(--wine), var(--wine-dark));
    color: var(--white);
    border-color: var(--wine);
}

.filter-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.filter-pill.active .filter-count {
    background: rgba(255, 255, 255, 0.3);
}

.nivel-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.nivel-dot.basico {
    background: var(--success);
}

.nivel-dot.intermedio {
    background: #ff9800;
}

.nivel-dot.avanzado {
    background: var(--danger);
}

.toggle-btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(13, 202, 240, 0.05));
    border: 2px solid var(--info);
    border-radius: 12px;
    color: var(--info);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-btn:hover {
    background: var(--info);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
}

/* Ruta de aprendizaje */
.learning-path-container {
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.03), rgba(114, 47, 55, 0.01));
    border: 2px solid var(--gray-200);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    animation: slideDown 0.4s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.learning-path-header {
    text-align: center;
    margin-bottom: 2rem;
}

.learning-path-header i {
    font-size: 3rem;
    color: var(--wine);
    margin-bottom: 1rem;
}

.learning-path-header h4 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

.learning-path-header p {
    color: var(--gray-600);
    margin: 0.5rem 0 0 0;
}

.learning-path-track {
    position: relative;
    padding-left: 2rem;
}

.learning-path-track::before {
    content: '';
    position: absolute;
    left: 23px;
    top: 30px;
    bottom: 30px;
    width: 3px;
    background: linear-gradient(to bottom, var(--success), #ff9800, var(--danger));
    border-radius: 3px;
}

.path-step {
    position: relative;
    margin-bottom: 2rem;
    display: flex;
    gap: 1.5rem;
    animation: fadeInUp 0.5s ease-out backwards;
}

.path-step:nth-child(1) { animation-delay: 0.1s; }
.path-step:nth-child(2) { animation-delay: 0.2s; }
.path-step:nth-child(3) { animation-delay: 0.3s; }
.path-step:nth-child(4) { animation-delay: 0.4s; }
.path-step:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-number {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--white);
    border: 3px solid var(--wine);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.25rem;
    color: var(--wine);
    flex-shrink: 0;
    z-index: 1;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.2);
}

.step-content {
    flex: 1;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.step-content:hover {
    border-color: var(--wine);
    box-shadow: 0 4px 16px rgba(114, 47, 55, 0.15);
    transform: translateX(5px);
}

.step-nivel {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
}

.step-nivel.basico {
    background: rgba(25, 135, 84, 0.1);
    color: var(--success);
}

.step-nivel.intermedio {
    background: rgba(255, 152, 0, 0.1);
    color: #ff9800;
}

.step-nivel.avanzado {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger);
}

.step-content h5 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.step-content p {
    color: var(--gray-600);
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
}

.step-link {
    color: var(--wine);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: gap 0.3s ease;
}

.step-link:hover {
    gap: 0.75rem;
}

/* Ocultar módulos filtrados */
.capacitacion-module-card.hidden {
    display: none;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Hero -->
    <div class="capacitacion-header">
        <div class="capacitacion-header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h1 class="capacitacion-title">
                        <i class="bi bi-mortarboard me-2"></i>
                        Capacitación del Equipo
                    </h1>
                    <p class="capacitacion-subtitle">
                        Gestiona y crea contenido de capacitación para tu equipo de ventas
                    </p>
                </div>
                <div class="capacitacion-actions ms-3">
                    <a href="{{ route('lider.capacitacion.create') }}" class="capacitacion-action-btn">
                        <i class="bi bi-plus-circle"></i>
                        Nueva Capacitación
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="capacitacion-stats-grid">
        <div class="capacitacion-stat-card">
            <div class="capacitacion-stat-icon">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="capacitacion-stat-value">{{ $stats['total_modulos'] }}</div>
            <div class="capacitacion-stat-label">Módulos Creados</div>
        </div>

        <div class="capacitacion-stat-card" style="border-left-color: var(--info);">
            <div class="capacitacion-stat-icon" style="background: linear-gradient(135deg, var(--info), #3dd5f3);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="capacitacion-stat-value">{{ $stats['total_miembros'] }}</div>
            <div class="capacitacion-stat-label">Miembros del Equipo</div>
        </div>

        <div class="capacitacion-stat-card" style="border-left-color: var(--success);">
            <div class="capacitacion-stat-icon" style="background: linear-gradient(135deg, var(--success), #157347);">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="capacitacion-stat-value">{{ $stats['total_asignaciones'] }}</div>
            <div class="capacitacion-stat-label">Asignaciones Totales</div>
        </div>

        <div class="capacitacion-stat-card" style="border-left-color: var(--warning);">
            <div class="capacitacion-stat-icon" style="background: linear-gradient(135deg, var(--warning), #ffca2c);">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="capacitacion-stat-value">
                @if($stats['total_miembros'] > 0 && $capacitaciones->count() > 0)
                    {{ number_format(($stats['total_asignaciones'] / ($stats['total_miembros'] * $capacitaciones->count())) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div class="capacitacion-stat-label">Progreso General</div>
        </div>
    </div>

    <!-- Filtros y Ruta de Aprendizaje -->
    @if($capacitaciones->count() > 0)
    <div class="capacitacion-filters-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div class="filter-group">
                <label class="filter-label">
                    <i class="bi bi-funnel"></i>
                    Filtrar por nivel:
                </label>
                <div class="filter-pills">
                    <button class="filter-pill active" data-nivel="todos" onclick="filtrarPorNivel('todos')">
                        Todos
                        <span class="filter-count">{{ $capacitaciones->count() }}</span>
                    </button>
                    @php
                        $basicos = $capacitaciones->filter(function($c) {
                            $nivel = strtolower($c->nivel ?? '');
                            return $nivel === 'basico' || $nivel === 'básico';
                        })->count();
                        $intermedios = $capacitaciones->filter(function($c) {
                            return strtolower($c->nivel ?? '') === 'intermedio';
                        })->count();
                        $avanzados = $capacitaciones->filter(function($c) {
                            return strtolower($c->nivel ?? '') === 'avanzado';
                        })->count();
                    @endphp
                    <button class="filter-pill" data-nivel="basico" onclick="filtrarPorNivel('basico')">
                        <span class="nivel-dot basico"></span>
                        Básico
                        <span class="filter-count">{{ $basicos }}</span>
                    </button>
                    <button class="filter-pill" data-nivel="intermedio" onclick="filtrarPorNivel('intermedio')">
                        <span class="nivel-dot intermedio"></span>
                        Intermedio
                        <span class="filter-count">{{ $intermedios }}</span>
                    </button>
                    <button class="filter-pill" data-nivel="avanzado" onclick="filtrarPorNivel('avanzado')">
                        <span class="nivel-dot avanzado"></span>
                        Avanzado
                        <span class="filter-count">{{ $avanzados }}</span>
                    </button>
                </div>
            </div>

            <div class="learning-path-toggle">
                <button class="toggle-btn" id="pathToggle" onclick="toggleLearningPath()">
                    <i class="bi bi-diagram-3"></i>
                    Ver Ruta de Aprendizaje
                </button>
            </div>
        </div>

        <!-- Ruta de Aprendizaje Recomendada -->
        <div class="learning-path-container" id="learningPath" style="display: none;">
            <div class="learning-path-header">
                <i class="bi bi-signpost-2"></i>
                <h4>Ruta de Aprendizaje Recomendada</h4>
                <p>Sigue esta secuencia para un aprendizaje óptimo</p>
            </div>

            <div class="learning-path-track">
                @php
                    $basicosPath = $capacitaciones->filter(function($c) {
                        $nivel = strtolower($c->nivel ?? '');
                        return $nivel === 'basico' || $nivel === 'básico';
                    })->sortBy('orden');

                    $intermediosPath = $capacitaciones->filter(function($c) {
                        return strtolower($c->nivel ?? '') === 'intermedio';
                    })->sortBy('orden');

                    $avanzadosPath = $capacitaciones->filter(function($c) {
                        return strtolower($c->nivel ?? '') === 'avanzado';
                    })->sortBy('orden');

                    $sequence = collect([]);
                    $sequence = $sequence->merge($basicosPath)->merge($intermediosPath)->merge($avanzadosPath);
                    $step = 1;
                @endphp

                @if($sequence->count() > 0)
                    @foreach($sequence as $cap)
                    <div class="path-step" data-nivel="{{ strtolower($cap->nivel ?? '') }}">
                        <div class="step-number">{{ $step++ }}</div>
                        <div class="step-content">
                            @php
                                $nivelNormalizado = strtolower($cap->nivel ?? '');
                                if ($nivelNormalizado === 'básico') {
                                    $nivelNormalizado = 'basico';
                                }
                            @endphp
                            <div class="step-nivel {{ $nivelNormalizado }}">{{ ucfirst($cap->nivel) }}</div>
                            <h5>{{ $cap->titulo }}</h5>
                            <p><i class="bi bi-clock"></i> {{ $cap->duracion }}</p>
                            <a href="{{ route('lider.capacitacion.show', $cap->id) }}" class="step-link">
                                Ver módulo <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 2rem; color: var(--gray-600);">
                        <i class="bi bi-info-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>No hay módulos disponibles para crear una ruta de aprendizaje</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Módulos de Capacitación -->
    <div class="capacitacion-modules-grid" id="modulesGrid">
            @foreach($capacitaciones as $capacitacion)
            <div class="capacitacion-module-card @if(isset($capacitacion->asignaciones) && count($capacitacion->asignaciones) > 0) completado @endif"
                 data-module-id="{{ $capacitacion->id }}"
                 data-nivel="{{ strtolower($capacitacion->nivel) }}">
                <!-- Ribbon de Nivel -->
                <div class="capacitacion-ribbon capacitacion-ribbon-{{ strtolower($capacitacion->nivel) }}">
                    <span>{{ ucfirst($capacitacion->nivel) }}</span>
                </div>

                <!-- Header del Módulo -->
                <div class="capacitacion-module-header">
                    <div class="capacitacion-module-icon">
                        <i class="bi {{ $capacitacion->icono }}"></i>
                    </div>
                </div>

                <!-- Título y Meta Info -->
                <div class="capacitacion-module-content">
                    <h3 class="capacitacion-module-title">{{ $capacitacion->titulo }}</h3>

                    <div class="capacitacion-module-meta">
                        <span class="capacitacion-meta-item">
                            <i class="bi bi-clock-fill"></i>
                            {{ $capacitacion->duracion }}
                        </span>
                        <span class="capacitacion-meta-item">
                            <i class="bi bi-tag-fill"></i>
                            {{ $capacitacion->categoria ?? 'General' }}
                        </span>
                    </div>
                </div>

                <!-- Descripción -->
                <p class="capacitacion-module-description">
                    {{ Str::limit($capacitacion->descripcion, 120) }}
                </p>

                <!-- Objetivos (primeros 3) -->
                @if(isset($capacitacion->objetivos) && count($capacitacion->objetivos) > 0)
                <div class="capacitacion-module-objectives">
                    <h6>Objetivos de Aprendizaje:</h6>
                    <ul class="capacitacion-objectives-list">
                        @foreach(array_slice($capacitacion->objetivos, 0, 3) as $objetivo)
                        <li>{{ $objetivo }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Footer con Acciones -->
                <div class="capacitacion-module-footer">
                    <div class="capacitacion-footer-info">
                        <small class="text-muted">
                            @if(isset($capacitacion->asignaciones))
                                <i class="bi bi-people"></i> {{ count($capacitacion->asignaciones) }} asignaciones
                            @else
                                <i class="bi bi-info-circle"></i> Sin asignar
                            @endif
                        </small>
                        <small class="text-muted ms-2">
                            <i class="bi bi-tag"></i> {{ $capacitacion->categoria ?? 'General' }}
                        </small>
                    </div>
                    <div class="capacitacion-module-actions">
                        <a href="{{ route('lider.capacitacion.show', $capacitacion->id) }}"
                           class="capacitacion-btn-icon"
                           title="Ver detalles">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('lider.capacitacion.edit', $capacitacion->id) }}"
                           class="capacitacion-btn-icon capacitacion-btn-edit"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button"
                                data-action="delete"
                                data-module-id="{{ $capacitacion->id }}"
                                data-module-title="{{ $capacitacion->titulo }}"
                                class="capacitacion-btn-icon capacitacion-btn-delete"
                                title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
    </div>
    @endif

    @if($capacitaciones->count() == 0)
        <div class="capacitacion-empty-state">
            <div class="capacitacion-empty-icon">
                <i class="bi bi-book"></i>
            </div>
            <p class="capacitacion-empty-text">No hay módulos de capacitación</p>
            <p class="capacitacion-empty-subtext">Crea tu primera capacitación para empezar</p>
            <a href="{{ route('lider.capacitacion.create') }}" class="capacitacion-action-btn-view mt-3">
                <i class="bi bi-plus-circle"></i>
                Crear Capacitación
            </a>
        </div>
    @endif

    <!-- Progreso del Equipo -->
    @if(count($progresoEquipo) > 0)
    <div class="capacitacion-progreso-section mt-4">
        <h3 class="mb-4" style="color: var(--wine); font-weight: 700;">
            <i class="bi bi-bar-chart me-2"></i>
            Progreso del Equipo
        </h3>
        @foreach($progresoEquipo as $progreso)
        <div class="capacitacion-progreso-item">
            <div class="capacitacion-progreso-header">
                <div class="capacitacion-progreso-avatar">
                    {{ strtoupper(substr($progreso['miembro']->name, 0, 2)) }}
                </div>
                <div class="capacitacion-progreso-info">
                    <h4 class="capacitacion-progreso-name">{{ $progreso['miembro']->name }}</h4>
                    <p class="capacitacion-progreso-role">{{ ucfirst($progreso['miembro']->rol) }}</p>
                </div>
                <div class="capacitacion-progreso-stats">
                    <div class="capacitacion-progreso-count">
                        {{ $progreso['modulos_completados'] }}/{{ $progreso['total_modulos'] }}
                    </div>
                    <p class="capacitacion-progreso-label">Módulos</p>
                </div>
            </div>

            <div class="capacitacion-progress-bar">
                <div class="capacitacion-progress-fill"
                     style="width: {{ $progreso['total_modulos'] > 0 ? ($progreso['modulos_completados'] / $progreso['total_modulos']) * 100 : 0 }}%">
                </div>
            </div>

            <div class="capacitacion-progress-text">
                <span>
                    <i class="bi bi-book me-1"></i>
                    Último: <strong>{{ $progreso['ultimo_modulo'] }}</strong>
                </span>
                <span>
                    <i class="bi bi-clock me-1"></i>
                    {{ $progreso['fecha_ultimo']->diffForHumans() }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Modal para Asignar Módulo -->
<div class="modal fade" id="asignarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--wine), var(--wine-dark)); color: white;">
                <h5 class="modal-title">Asignar Capacitación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lider.capacitacion.asignar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Módulo</label>
                        <select name="modulo_id" class="form-select" required>
                            @foreach($capacitaciones as $cap)
                            <option value="{{ $cap->id }}">{{ $cap->titulo }} ({{ $cap->duracion }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Asignar a:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="todos" onclick="capacitacionManager.toggleTodos(this)">
                            <label class="form-check-label fw-bold" for="todos">
                                Seleccionar Todos
                            </label>
                        </div>
                        <hr>
                        @foreach($equipo as $miembro)
                        <div class="form-check">
                            <input class="form-check-input miembro-check" type="checkbox"
                                   name="miembro_ids[]" value="{{ $miembro->id }}"
                                   id="miembro_{{ $miembro->id }}">
                            <label class="form-check-label" for="miembro_{{ $miembro->id }}">
                                {{ $miembro->name }} - <small class="text-muted">{{ ucfirst($miembro->rol) }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="capacitacion-action-btn-view">
                        <i class="bi bi-check-circle"></i>
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/capacitacion-modern.js') }}?v={{ filemtime(public_path('js/lider/capacitacion-modern.js')) }}"></script>
<script>
function filtrarPorNivel(nivel) {
    // Actualizar pills activos
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.classList.remove('active');
    });
    document.querySelector(`[data-nivel="${nivel}"]`).classList.add('active');

    // Filtrar tarjetas
    const cards = document.querySelectorAll('.capacitacion-module-card');
    cards.forEach(card => {
        const cardNivel = card.getAttribute('data-nivel');
        if (nivel === 'todos' || cardNivel === nivel || (nivel === 'basico' && cardNivel === 'básico')) {
            card.classList.remove('hidden');
            card.style.animation = 'fadeInUp 0.5s ease-out';
        } else {
            card.classList.add('hidden');
        }
    });
}

function toggleLearningPath() {
    const pathContainer = document.getElementById('learningPath');
    const toggleBtn = document.getElementById('pathToggle');

    if (pathContainer.style.display === 'none') {
        pathContainer.style.display = 'block';
        toggleBtn.innerHTML = '<i class="bi bi-x-circle"></i> Ocultar Ruta de Aprendizaje';
    } else {
        pathContainer.style.display = 'none';
        toggleBtn.innerHTML = '<i class="bi bi-diagram-3"></i> Ver Ruta de Aprendizaje';
    }
}
</script>
@endpush
