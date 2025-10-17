@extends('layouts.lider')

@section('title', '- Editar ' . $capacitacion->titulo)
@section('page-title', 'Editar Capacitación')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider/capacitacion-modern.css') }}?v={{ filemtime(public_path('css/lider/capacitacion-modern.css')) }}">
<style>
/* Container principal */
.form-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Secciones del formulario */
.form-section {
    background: var(--white);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 2px solid transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.form-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--wine), var(--wine-light));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.form-section:hover {
    box-shadow: 0 8px 32px rgba(114, 47, 55, 0.12);
    border-color: rgba(114, 47, 55, 0.1);
    transform: translateY(-2px);
}

.form-section:hover::before {
    transform: scaleX(1);
}

/* Header de secciones */
.form-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.25rem;
    border-bottom: 2px solid var(--gray-100);
    position: relative;
}

.form-section-header::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 60px;
    height: 2px;
    background: var(--wine);
}

.form-section-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--wine), var(--wine-dark));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    box-shadow: 0 4px 12px rgba(114, 47, 55, 0.25);
}

.form-section-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    letter-spacing: -0.5px;
}

.form-section-subtitle {
    font-size: 0.9rem;
    color: var(--gray-600);
    margin: 0.25rem 0 0 0;
}

/* Mejora de inputs y textareas */
.capacitacion-form-group {
    margin-bottom: 1.75rem;
}

.capacitacion-form-label {
    display: block;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.75rem;
}

.capacitacion-form-input,
.capacitacion-form-textarea {
    width: 100%;
    padding: 0.875rem 1.125rem;
    border: 2px solid var(--gray-300);
    border-radius: 12px;
    font-size: 0.95rem;
    font-family: inherit;
    transition: all 0.2s ease;
    background: var(--white);
    color: var(--gray-900);
}

.capacitacion-form-input:focus,
.capacitacion-form-textarea:focus {
    outline: none;
    border-color: var(--wine);
    box-shadow: 0 0 0 4px rgba(114, 47, 55, 0.1);
    transform: translateY(-1px);
}

.capacitacion-form-input::placeholder,
.capacitacion-form-textarea::placeholder {
    color: var(--gray-500);
}

.capacitacion-form-textarea {
    min-height: 120px;
    resize: vertical;
    line-height: 1.6;
}

.capacitacion-form-help {
    font-size: 0.8rem;
    color: var(--gray-600);
    margin-top: 0.5rem;
    display: block;
}

/* Lista dinámica de objetivos */
.dynamic-list-item {
    background: linear-gradient(135deg, var(--gray-50), var(--white));
    border: 2px solid var(--gray-200);
    border-radius: 14px;
    padding: 1.125rem;
    margin-bottom: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideInRight 0.4s ease-out;
    position: relative;
}

.dynamic-list-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 0;
    background: var(--wine);
    border-radius: 0 4px 4px 0;
    transition: height 0.3s ease;
}

.dynamic-list-item:hover {
    border-color: var(--wine);
    background: var(--white);
    box-shadow: 0 4px 16px rgba(114, 47, 55, 0.12);
    transform: translateX(4px);
}

.dynamic-list-item:hover::before {
    height: 60%;
}

.dynamic-list-item input {
    border: none !important;
    box-shadow: none !important;
    padding: 0.75rem 1rem;
    background: transparent;
}

.dynamic-list-item input:focus {
    background: rgba(114, 47, 55, 0.02);
    border-radius: 8px;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Botón agregar */
.btn-add-item {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.08), rgba(25, 135, 84, 0.03));
    border: 2px dashed var(--success);
    color: var(--success);
    padding: 1rem 1.75rem;
    border-radius: 14px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-add-item::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--success);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-add-item i {
    position: relative;
    z-index: 1;
    font-size: 1.1rem;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-add-item span {
    position: relative;
    z-index: 1;
}

.btn-add-item:hover {
    border-style: solid;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.3);
}

.btn-add-item:hover::before {
    opacity: 1;
}

.btn-add-item:hover i {
    transform: rotate(90deg) scale(1.1);
}

.btn-add-item:hover {
    color: var(--white);
}

.btn-add-item:active {
    transform: translateY(-1px);
}

/* Botón eliminar */
.btn-remove-item {
    background: var(--white);
    border: 2px solid rgba(220, 53, 69, 0.3);
    color: var(--danger);
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    position: relative;
    overflow: hidden;
}

.btn-remove-item::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--danger);
    transform: scale(0);
    transition: transform 0.2s ease;
    border-radius: 10px;
}

.btn-remove-item i {
    position: relative;
    z-index: 1;
    transition: transform 0.2s ease;
}

.btn-remove-item:hover {
    border-color: var(--danger);
    color: var(--white);
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-remove-item:hover::before {
    transform: scale(1);
}

.btn-remove-item:hover i {
    transform: scale(1.1);
}

.btn-remove-item:active {
    transform: scale(0.95);
}

/* Selector de iconos */
.icon-picker {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 1rem;
}

.icon-option {
    padding: 1.25rem 0.75rem;
    border: 2px solid var(--gray-300);
    border-radius: 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: var(--white);
    position: relative;
    overflow: hidden;
}

.icon-option::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.08), rgba(114, 47, 55, 0.03));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.icon-option:hover {
    border-color: var(--wine);
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 6px 20px rgba(114, 47, 55, 0.18);
}

.icon-option:hover::before {
    opacity: 1;
}

.icon-option.selected {
    border-color: var(--wine);
    border-width: 3px;
    background: linear-gradient(135deg, rgba(114, 47, 55, 0.1), rgba(114, 47, 55, 0.05));
}

.icon-option.selected::after {
    content: '✓';
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    background: var(--wine);
    color: white;
    border-radius: 50%;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.icon-option i {
    font-size: 2.25rem;
    color: var(--wine);
    display: block;
    margin-bottom: 0.625rem;
    position: relative;
    z-index: 1;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.icon-option:hover i {
    transform: scale(1.15) rotate(5deg);
}

.icon-option.selected i {
    transform: scale(1.1);
}

.icon-option .icon-label {
    font-size: 0.75rem;
    color: var(--gray-700);
    font-weight: 600;
    position: relative;
    z-index: 1;
}

/* Selector de nivel */
.nivel-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.nivel-option {
    padding: 2rem 1.5rem;
    border: 3px solid var(--gray-300);
    border-radius: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: var(--white);
    position: relative;
    overflow: hidden;
}

.nivel-option::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nivel-option.basico::before {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.12), rgba(25, 135, 84, 0.05));
}

.nivel-option.intermedio::before {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.12), rgba(255, 152, 0, 0.05));
}

.nivel-option.avanzado::before {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.12), rgba(220, 53, 69, 0.05));
}

.nivel-option:hover {
    transform: translateY(-6px) scale(1.02);
}

.nivel-option:hover::before {
    opacity: 1;
}

.nivel-option.selected {
    border-width: 4px;
    transform: scale(1.02);
}

.nivel-option.selected::before {
    opacity: 1;
}

.nivel-option.basico:hover,
.nivel-option.basico.selected {
    border-color: var(--success);
    box-shadow: 0 8px 24px rgba(25, 135, 84, 0.25);
}

.nivel-option.intermedio:hover,
.nivel-option.intermedio.selected {
    border-color: #ff9800;
    box-shadow: 0 8px 24px rgba(255, 152, 0, 0.25);
}

.nivel-option.avanzado:hover,
.nivel-option.avanzado.selected {
    border-color: var(--danger);
    box-shadow: 0 8px 24px rgba(220, 53, 69, 0.25);
}

.nivel-option .nivel-icon {
    font-size: 3rem;
    margin-bottom: 0.75rem;
    position: relative;
    z-index: 1;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.nivel-option:hover .nivel-icon {
    transform: scale(1.15);
}

.nivel-option.selected .nivel-icon {
    animation: bounce 0.6s ease;
}

@keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.nivel-option .nivel-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--gray-900);
    margin-bottom: 0.375rem;
    position: relative;
    z-index: 1;
}

.nivel-option .nivel-description {
    font-size: 0.8rem;
    color: var(--gray-600);
    position: relative;
    z-index: 1;
}

/* Footer pegajoso */
.sticky-footer {
    position: sticky;
    bottom: 0;
    background: linear-gradient(to top, var(--white), rgba(255, 255, 255, 0.98));
    padding: 1.75rem 2.5rem;
    border-top: 3px solid var(--gray-100);
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.1);
    border-radius: 20px 20px 0 0;
    margin: 3rem -2.5rem -2.5rem;
    z-index: 100;
    backdrop-filter: blur(10px);
}

.sticky-footer .d-flex {
    align-items: center;
}

/* Barra de progreso superior */
.progress-indicator {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gray-200);
    z-index: 9999;
}

.progress-indicator-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--wine), var(--wine-light), var(--wine));
    background-size: 200% 100%;
    animation: gradient-flow 2s ease infinite;
    width: 0%;
    transition: width 0.2s ease;
}

@keyframes gradient-flow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Responsive */
@media (max-width: 768px) {
    .form-section {
        padding: 1.75rem;
        border-radius: 16px;
    }

    .form-section-icon {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }

    .form-section-title {
        font-size: 1.15rem;
    }

    .nivel-selector,
    .icon-picker {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .nivel-option {
        padding: 1.5rem;
    }

    .sticky-footer {
        padding: 1.25rem 1.5rem;
        margin: 2rem -1.75rem -1.75rem;
    }

    .sticky-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }

    .sticky-footer .d-flex > div {
        width: 100%;
    }

    .sticky-footer .d-flex .d-flex {
        flex-direction: row !important;
        justify-content: stretch;
    }

    .sticky-footer .d-flex .d-flex a,
    .sticky-footer .d-flex .d-flex button {
        flex: 1;
    }
}

@media (max-width: 576px) {
    .icon-picker {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush

@section('content')
<div class="progress-indicator">
    <div class="progress-indicator-bar" id="formProgress"></div>
</div>

<div class="container-fluid">
    <div class="capacitacion-header">
        <div class="capacitacion-header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h1 class="capacitacion-title">
                        <i class="bi bi-pencil-square me-2"></i>
                        Editar Capacitación
                    </h1>
                    <p class="capacitacion-subtitle">{{ $capacitacion->titulo }}</p>
                </div>
                <div class="capacitacion-actions ms-3">
                    <a href="{{ route('lider.capacitacion.show', $capacitacion->id) }}" class="capacitacion-action-btn">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-10 mx-auto">
            <form action="{{ route('lider.capacitacion.update', $capacitacion->id) }}" method="POST" id="capacitacionForm">
                @csrf
                @method('PUT')

                <!-- Información Básica -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Información Básica</h3>
                            <p class="form-section-subtitle">Datos generales de la capacitación</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="capacitacion-form-group">
                                <label class="capacitacion-form-label">
                                    <i class="bi bi-card-heading me-1"></i>
                                    Título de la Capacitación *
                                </label>
                                <input type="text" name="titulo" class="capacitacion-form-input"
                                       placeholder="Ej: Técnicas de Ventas Efectivas" required maxlength="255"
                                       value="{{ old('titulo', $capacitacion->titulo) }}">
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
                                       value="{{ old('duracion', $capacitacion->duracion) }}">
                            </div>
                        </div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-tag me-1"></i>
                            Categoría
                        </label>
                        <input type="text" name="categoria" class="capacitacion-form-input"
                               placeholder="Ej: Ventas, Liderazgo, Marketing..." maxlength="100"
                               value="{{ old('categoria', $capacitacion->categoria ?? 'General') }}">
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-text-paragraph me-1"></i>
                            Descripción *
                        </label>
                        <textarea name="descripcion" class="capacitacion-form-textarea" rows="3"
                                  placeholder="Describe brevemente de qué trata esta capacitación..." required>{{ old('descripcion', $capacitacion->descripcion) }}</textarea>
                    </div>
                </div>

                <!-- Nivel de Dificultad -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Nivel de Dificultad</h3>
                            <p class="form-section-subtitle">Selecciona el nivel apropiado</p>
                        </div>
                    </div>

                    <input type="hidden" name="nivel" id="nivelInput" value="{{ old('nivel', $capacitacion->nivel) }}" required>
                    <div class="nivel-selector">
                        <div class="nivel-option basico {{ old('nivel', $capacitacion->nivel) == 'basico' || old('nivel', $capacitacion->nivel) == 'básico' ? 'selected' : '' }}" onclick="selectNivel('basico', this)">
                            <div class="nivel-icon">🌱</div>
                            <div class="nivel-title">Básico</div>
                            <div class="nivel-description">Para principiantes</div>
                        </div>
                        <div class="nivel-option intermedio {{ old('nivel', $capacitacion->nivel) == 'intermedio' ? 'selected' : '' }}" onclick="selectNivel('intermedio', this)">
                            <div class="nivel-icon">📊</div>
                            <div class="nivel-title">Intermedio</div>
                            <div class="nivel-description">Conocimiento moderado</div>
                        </div>
                        <div class="nivel-option avanzado {{ old('nivel', $capacitacion->nivel) == 'avanzado' ? 'selected' : '' }}" onclick="selectNivel('avanzado', this)">
                            <div class="nivel-icon">🚀</div>
                            <div class="nivel-title">Avanzado</div>
                            <div class="nivel-description">Expertos</div>
                        </div>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Contenido Detallado</h3>
                            <p class="form-section-subtitle">Describe el contenido completo del módulo</p>
                        </div>
                    </div>

                    <textarea name="contenido" class="capacitacion-form-textarea" rows="10"
                              placeholder="Contenido detallado de la capacitación..." required>{{ old('contenido', $capacitacion->contenido) }}</textarea>
                </div>

                <!-- Objetivos -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Objetivos de Aprendizaje</h3>
                            <p class="form-section-subtitle">¿Qué aprenderán los participantes?</p>
                        </div>
                    </div>

                    <div id="objetivos-container">
                        @if(isset($capacitacion->objetivos) && count($capacitacion->objetivos) > 0)
                            @foreach($capacitacion->objetivos as $objetivo)
                            <div class="dynamic-list-item d-flex gap-2">
                                <input type="text" name="objetivos[]" class="capacitacion-form-input"
                                       placeholder="Objetivo de aprendizaje" value="{{ $objetivo }}">
                                <button type="button" onclick="this.parentElement.remove()" class="btn-remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="agregarObjetivo()" class="btn-add-item">
                        <i class="bi bi-plus-circle"></i>
                        Agregar Objetivo
                    </button>
                </div>

                <!-- Icono -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Icono Representativo</h3>
                            <p class="form-section-subtitle">Elige un icono para identificar el módulo</p>
                        </div>
                    </div>

                    <input type="hidden" name="icono" id="iconoInput" value="{{ old('icono', $capacitacion->icono) }}">
                    <div class="icon-picker">
                        <div class="icon-option {{ $capacitacion->icono == 'bi-book' ? 'selected' : '' }}" onclick="selectIcon('bi-book', this)">
                            <i class="bi bi-book"></i>
                            <div class="icon-label">Libro</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-graph-up' ? 'selected' : '' }}" onclick="selectIcon('bi-graph-up', this)">
                            <i class="bi bi-graph-up"></i>
                            <div class="icon-label">Gráfico</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-chat-dots' ? 'selected' : '' }}" onclick="selectIcon('bi-chat-dots', this)">
                            <i class="bi bi-chat-dots"></i>
                            <div class="icon-label">Chat</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-people-fill' ? 'selected' : '' }}" onclick="selectIcon('bi-people-fill', this)">
                            <i class="bi bi-people-fill"></i>
                            <div class="icon-label">Personas</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-lightbulb' ? 'selected' : '' }}" onclick="selectIcon('bi-lightbulb', this)">
                            <i class="bi bi-lightbulb"></i>
                            <div class="icon-label">Idea</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-trophy' ? 'selected' : '' }}" onclick="selectIcon('bi-trophy', this)">
                            <i class="bi bi-trophy"></i>
                            <div class="icon-label">Trofeo</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-award' ? 'selected' : '' }}" onclick="selectIcon('bi-award', this)">
                            <i class="bi bi-award"></i>
                            <div class="icon-label">Premio</div>
                        </div>
                        <div class="icon-option {{ $capacitacion->icono == 'bi-mortarboard' ? 'selected' : '' }}" onclick="selectIcon('bi-mortarboard', this)">
                            <i class="bi bi-mortarboard"></i>
                            <div class="icon-label">Graduación</div>
                        </div>
                    </div>
                </div>

                <!-- Multimedia -->
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-play-circle"></i>
                        </div>
                        <div>
                            <h3 class="form-section-title">Contenido Multimedia</h3>
                            <p class="form-section-subtitle">Video y recursos adicionales</p>
                        </div>
                    </div>

                    <div class="capacitacion-form-group">
                        <label class="capacitacion-form-label">
                            <i class="bi bi-link-45deg me-1"></i>
                            URL del Video (opcional)
                        </label>
                        <input type="url" name="video_url" class="capacitacion-form-input"
                               placeholder="https://youtube.com/watch?v=..."
                               value="{{ old('video_url', $capacitacion->video_url) }}">
                    </div>
                </div>

                <!-- Footer Sticky -->
                <div class="sticky-footer">
                    <div class="d-flex gap-3 justify-content-between align-items-center">
                        <div style="color: var(--gray-600); font-size: 0.9rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Todos los cambios se guardarán al actualizar
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('lider.capacitacion.show', $capacitacion->id) }}"
                               class="capacitacion-action-btn-edit">
                                <i class="bi bi-x-circle"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="capacitacion-action-btn-view">
                                <i class="bi bi-check-circle"></i>
                                Actualizar Capacitación
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectNivel(nivel, element) {
    document.querySelectorAll('.nivel-option').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('nivelInput').value = nivel;
}

function selectIcon(icon, element) {
    document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('iconoInput').value = icon;
}

function agregarObjetivo() {
    const container = document.getElementById('objetivos-container');
    const div = document.createElement('div');
    div.className = 'dynamic-list-item d-flex gap-2';
    div.innerHTML = `
        <input type="text" name="objetivos[]" class="capacitacion-form-input"
               placeholder="Objetivo de aprendizaje">
        <button type="button" onclick="this.parentElement.remove()" class="btn-remove-item">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Progress indicator
window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById('formProgress').style.width = scrolled + '%';
});
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/lider/capacitacion-modern.js') }}?v={{ filemtime(public_path('js/lider/capacitacion-modern.js')) }}"></script>
@endpush
