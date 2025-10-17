@extends('layouts.lider')

@section('title', '- ' . $capacitacion->titulo)
@section('page-title', $capacitacion->titulo)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/capacitacion-modern.css') }}?v={{ filemtime(public_path('css/lider/capacitacion-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="capacitacion-header">
        <div class="capacitacion-header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h1 class="capacitacion-title">
                        <i class="bi {{ $capacitacion->icono }} me-2"></i>
                        {{ $capacitacion->titulo }}
                    </h1>
                    <p class="capacitacion-subtitle">
                        {{ $capacitacion->descripcion }}
                    </p>
                </div>
                <div class="capacitacion-actions ms-3">
                    <a href="{{ route('lider.capacitacion.edit', $capacitacion->id) }}" class="capacitacion-action-btn">
                        <i class="bi bi-pencil"></i>
                        Editar
                    </a>
                    <button onclick="capacitacionManager.confirmarEliminar('{{ $capacitacion->id }}', '{{ $capacitacion->titulo }}')"
                            class="capacitacion-action-btn" style="background: rgba(220, 53, 69, 0.2); border-color: rgba(220, 53, 69, 0.4);">
                        <i class="bi bi-trash"></i>
                        Eliminar
                    </button>
                    <a href="{{ route('lider.capacitacion.index') }}" class="capacitacion-action-btn">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="capacitacion-progreso-section">
                <h3 style="color: var(--wine); font-weight: 700; margin-bottom: 1.5rem;">
                    <i class="bi bi-info-circle me-2"></i>
                    Información de la Capacitación
                </h3>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="capacitacion-progreso-item text-center" style="padding: 1.5rem;">
                            <i class="bi bi-clock" style="font-size: 2rem; color: var(--wine);"></i>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-top: 0.5rem;">
                                {{ $capacitacion->duracion }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--gray-600);">Duración</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="capacitacion-progreso-item text-center" style="padding: 1.5rem;">
                            <i class="bi bi-bar-chart" style="font-size: 2rem; color: var(--wine);"></i>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-top: 0.5rem;">
                                {{ ucfirst($capacitacion->nivel) }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--gray-600);">Nivel</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="capacitacion-progreso-item text-center" style="padding: 1.5rem;">
                            <i class="bi bi-people" style="font-size: 2rem; color: var(--wine);"></i>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-top: 0.5rem;">
                                {{ count($progreso) }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--gray-600);">Asignados</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 style="color: var(--wine); font-weight: 600; margin-bottom: 1rem;">
                        <i class="bi bi-file-text me-2"></i>
                        Contenido
                    </h5>
                    <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 12px; line-height: 1.8;">
                        {!! nl2br(e($capacitacion->contenido)) !!}
                    </div>
                </div>

                @if(isset($capacitacion->objetivos) && count($capacitacion->objetivos) > 0)
                <div class="mb-4">
                    <h5 style="color: var(--wine); font-weight: 600; margin-bottom: 1rem;">
                        <i class="bi bi-check2-square me-2"></i>
                        Objetivos de Aprendizaje
                    </h5>
                    <ul class="capacitacion-objectives-list">
                        @foreach($capacitacion->objetivos as $objetivo)
                        <li>{{ $objetivo }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(isset($capacitacion->recursos) && count($capacitacion->recursos) > 0)
                <div class="mb-4">
                    <h5 style="color: var(--wine); font-weight: 600; margin-bottom: 1rem;">
                        <i class="bi bi-folder me-2"></i>
                        Recursos Adicionales
                    </h5>
                    <ul class="list-group">
                        @foreach($capacitacion->recursos as $recurso)
                        <li class="list-group-item d-flex align-items-center" style="border-left: 3px solid var(--wine);">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, var(--wine), var(--wine-dark)); color: white; display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 600; color: var(--gray-900);">
                                    @if(is_array($recurso))
                                        {{ $recurso['titulo'] ?? $recurso['nombre'] ?? 'Recurso' }}
                                    @else
                                        {{ $recurso }}
                                    @endif
                                </div>
                                @if(is_array($recurso) && isset($recurso['url']))
                                <a href="{{ $recurso['url'] }}" target="_blank" style="font-size: 0.85rem; color: var(--wine); text-decoration: none;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    Abrir recurso
                                </a>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($capacitacion->video_url)
                <div class="mb-4">
                    <h5 style="color: var(--wine); font-weight: 600; margin-bottom: 1rem;">
                        <i class="bi bi-camera-video me-2"></i>
                        Video de Capacitación
                    </h5>
                    <div class="capacitacion-progreso-item">
                        <a href="{{ $capacitacion->video_url }}" target="_blank" class="capacitacion-action-btn-view">
                            <i class="bi bi-play-circle"></i>
                            Ver Video
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Progreso de Vendedores -->
        <div class="col-lg-4">
            <div class="capacitacion-progreso-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: var(--wine); font-weight: 700; margin: 0;">
                        <i class="bi bi-people me-2"></i>
                        Vendedores Asignados
                    </h5>
                    <button class="capacitacion-action-btn-view" data-bs-toggle="modal" data-bs-target="#asignarModal">
                        <i class="bi bi-plus"></i>
                        Asignar
                    </button>
                </div>

                @if(count($progreso) > 0)
                    @foreach($progreso as $item)
                    <div class="capacitacion-progreso-item mb-3">
                        <div class="capacitacion-progreso-header">
                            <div class="capacitacion-progreso-avatar">
                                {{ strtoupper(substr($item['miembro']->name, 0, 2)) }}
                            </div>
                            <div class="capacitacion-progreso-info">
                                <h6 class="capacitacion-progreso-name">{{ $item['miembro']->name }}</h6>
                                <p class="capacitacion-progreso-role">{{ ucfirst($item['miembro']->rol) }}</p>
                            </div>
                            <div class="capacitacion-progreso-stats">
                                <div class="capacitacion-progreso-count">{{ $item['progreso'] }}%</div>
                                <p class="capacitacion-progreso-label">Progreso</p>
                            </div>
                        </div>

                        <div class="capacitacion-progress-bar">
                            <div class="capacitacion-progress-fill" style="width: {{ $item['progreso'] }}%"></div>
                        </div>

                        <div class="capacitacion-progress-text">
                            @if($item['completado'])
                                <span style="color: var(--success); font-weight: 600;">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Completado
                                </span>
                                @if($item['fecha_completado'])
                                <span>{{ \Carbon\Carbon::parse($item['fecha_completado'])->diffForHumans() }}</span>
                                @endif
                            @else
                                <span>
                                    @if($item['fecha_inicio'])
                                        Iniciado {{ \Carbon\Carbon::parse($item['fecha_inicio'])->diffForHumans() }}
                                    @else
                                        Asignado {{ \Carbon\Carbon::parse($item['fecha_asignacion'])->diffForHumans() }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4" style="color: var(--gray-500);">
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                        <p style="margin-top: 1rem; font-weight: 600;">No hay vendedores asignados</p>
                        <p style="font-size: 0.9rem;">Asigna esta capacitación a tu equipo</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar -->
<div class="modal fade" id="asignarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--wine), var(--wine-dark)); color: white;">
                <h5 class="modal-title">Asignar Capacitación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lider.capacitacion.asignar') }}" method="POST">
                @csrf
                <input type="hidden" name="modulo_id" value="{{ $capacitacion->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Asignar a:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="todos" onclick="capacitacionManager.toggleTodos(this)">
                            <label class="form-check-label fw-bold" for="todos">Seleccionar Todos</label>
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
@endpush
