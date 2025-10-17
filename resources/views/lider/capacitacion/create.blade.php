@extends('layouts.lider')

@section('title', '- Crear Capacitación')
@section('page-title', 'Nueva Capacitación')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/capacitacion-modern.css') }}?v={{ filemtime(public_path('css/lider/capacitacion-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="capacitacion-header">
        <div class="capacitacion-header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h1 class="capacitacion-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nueva Capacitación
                    </h1>
                    <p class="capacitacion-subtitle">Crea un nuevo módulo de capacitación para tu equipo</p>
                </div>
                <div class="capacitacion-actions ms-3">
                    <a href="{{ route('lider.capacitacion.index') }}" class="capacitacion-action-btn">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="capacitacion-progreso-section">
                <form action="{{ route('lider.capacitacion.store') }}" method="POST" id="capacitacionForm" onsubmit="return capacitacionManager.validarFormulario('capacitacionForm')">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-card-heading me-1"></i>
                                    Título de la Capacitación *
                                </label>
                                <input type="text" name="titulo" class="capacitacion-form-input"
                                       placeholder="Ej: Técnicas de Ventas Efectivas" required maxlength="255"
                                       value="{{ old('titulo') }}">
                                <div class="capacitacion-form-help">Nombre descriptivo del módulo</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Duración *
                                </label>
                                <input type="text" name="duracion" class="capacitacion-form-input"
                                       placeholder="Ej: 2 horas" required maxlength="100"
                                       value="{{ old('duracion') }}">
                                <div class="capacitacion-form-help">Tiempo estimado</div>
                            </div>
                        </div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-text-paragraph me-1"></i>
                            Descripción *
                        </label>
                        <textarea name="descripcion" class="capacitacion-form-textarea" rows="3"
                                  placeholder="Describe brevemente de qué trata esta capacitación..." required>{{ old('descripcion') }}</textarea>
                        <div class="capacitacion-form-help">Resumen breve del contenido</div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Contenido Completo *
                        </label>
                        <textarea name="contenido" class="capacitacion-form-textarea" rows="8"
                                  placeholder="Contenido detallado de la capacitación..." required>{{ old('contenido') }}</textarea>
                        <div class="capacitacion-form-help">Contenido completo del módulo</div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-bar-chart me-1"></i>
                                    Nivel *
                                </label>
                                <select name="nivel" class="capacitacion-form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="basico" {{ old('nivel') == 'basico' ? 'selected' : '' }}>Básico</option>
                                    <option value="intermedio" {{ old('nivel') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                    <option value="avanzado" {{ old('nivel') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-folder me-1"></i>
                                    Categoría
                                </label>
                                <input type="text" name="categoria" class="capacitacion-form-input"
                                       placeholder="Ej: Ventas, Liderazgo..." maxlength="100"
                                       value="{{ old('categoria', 'General') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-emoji-smile me-1"></i>
                                    Icono
                                </label>
                                <select name="icono" class="capacitacion-form-select">
                                    <option value="bi-book">📚 Libro (bi-book)</option>
                                    <option value="bi-graph-up">📈 Gráfico (bi-graph-up)</option>
                                    <option value="bi-chat-dots">💬 Chat (bi-chat-dots)</option>
                                    <option value="bi-people-fill">👥 Personas (bi-people-fill)</option>
                                    <option value="bi-person-badge">🎖️ Badge (bi-person-badge)</option>
                                    <option value="bi-lightbulb">💡 Idea (bi-lightbulb)</option>
                                    <option value="bi-trophy">🏆 Trofeo (bi-trophy)</option>
                                    <option value="bi-award">🏅 Premio (bi-award)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-check2-square me-1"></i>
                            Objetivos de Aprendizaje
                        </label>
                        <div id="objetivos-container">
                            <div class="objetivo-item mb-2 d-flex gap-2">
                                <input type="text" name="objetivos[]" class="capacitacion-form-input"
                                       placeholder="Objetivo 1" value="{{ old('objetivos.0') }}">
                                <button type="button" onclick="this.parentElement.remove()"
                                        class="capacitacion-action-btn-delete" style="flex-shrink:0;padding:.75rem 1rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="capacitacionManager.agregarObjetivo()"
                                class="capacitacion-action-btn-edit mt-2">
                            <i class="bi bi-plus"></i>
                            Agregar Objetivo
                        </button>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-link-45deg me-1"></i>
                            URL del Video (opcional)
                        </label>
                        <input type="url" name="video_url" class="capacitacion-form-input"
                               placeholder="https://youtube.com/..." value="{{ old('video_url') }}">
                        <div class="capacitacion-form-help">Link a YouTube, Vimeo, etc.</div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-folder2 me-1"></i>
                            Recursos Adicionales (opcional)
                        </label>
                        <div id="recursos-container"></div>
                        <button type="button" onclick="capacitacionManager.agregarRecurso()"
                                class="capacitacion-action-btn-edit">
                            <i class="bi bi-plus"></i>
                            Agregar Recurso
                        </button>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-4 pt-4" style="border-top: 2px solid var(--gray-200);">
                        <a href="{{ route('lider.capacitacion.index') }}" class="capacitacion-action-btn-edit">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="capacitacion-action-btn-view">
                            <i class="bi bi-check-circle"></i>
                            Crear Capacitación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/capacitacion-modern.js') }}?v={{ filemtime(public_path('js/lider/capacitacion-modern.js')) }}"></script>
@endpush
