@extends('layouts.vendedor')
@section('title', 'Centro de Ayuda')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendedor/ayuda-modern.css') }}?v={{ filemtime(public_path('css/vendedor/ayuda-modern.css')) }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    {{-- Hero Search --}}
    <div class="ayuda-search-hero fade-in-up">
        <h1 style="font-size:2.5rem;font-weight:700;margin:0 0 1rem"><i class="bi bi-question-circle"></i> Centro de Ayuda</h1>
        <p style="font-size:1.125rem;opacity:.95;margin:0">¿En qué podemos ayudarte hoy?</p>
        <div class="ayuda-search-box">
            <form id="searchForm">
                <input type="text" id="searchInput" placeholder="Buscar en ayuda..." required>
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    {{-- Categorías --}}
    <div style="margin-bottom:3rem">
        <h2 style="font-size:1.5rem;font-weight:600;margin-bottom:1.5rem">
            <i class="bi bi-grid-3x3-gap"></i> Categorías de Ayuda
        </h2>
        <div class="ayuda-categories-grid">
            @foreach($categorias as $cat)
            <div class="ayuda-category-card fade-in-up animate-delay-{{ $loop->iteration }}">
                <div class="ayuda-category-icon"><i class="bi {{ $cat['icono'] }}"></i></div>
                <div class="ayuda-category-title">{{ $cat['titulo'] }}</div>
                <div class="ayuda-category-desc">{{ $cat['descripcion'] }}</div>
                <div class="ayuda-category-count">{{ $cat['articulos'] }} artículos</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Preguntas Frecuentes --}}
    <div style="margin-bottom:3rem">
        <h2 style="font-size:1.5rem;font-weight:600;margin-bottom:1.5rem">
            <i class="bi bi-patch-question"></i> Preguntas Frecuentes
        </h2>
        <div class="ayuda-faq-container scale-in">
            @foreach($preguntasFrecuentes as $faq)
            <div class="ayuda-faq-item">
                <div class="ayuda-faq-question">
                    {{ $faq['pregunta'] }}
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="ayuda-faq-answer">{{ $faq['respuesta'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tutoriales --}}
    <div style="margin-bottom:3rem">
        <h2 style="font-size:1.5rem;font-weight:600;margin-bottom:1.5rem">
            <i class="bi bi-play-circle"></i> Tutoriales en Video
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem">
            @foreach($tutoriales as $tutorial)
            <div class="ayuda-tutorial-card fade-in-up animate-delay-{{ $loop->iteration }}">
                <div class="ayuda-tutorial-thumb">
                    <div class="ayuda-tutorial-play"><i class="bi bi-play-fill"></i></div>
                </div>
                <div class="ayuda-tutorial-info">
                    <div class="ayuda-tutorial-title">{{ $tutorial['titulo'] }}</div>
                    <div class="ayuda-tutorial-meta">
                        <span><i class="bi bi-clock"></i> {{ $tutorial['duracion'] }}</span>
                        <span><i class="bi bi-eye"></i> {{ $tutorial['vistas'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Contacto --}}
    <div style="margin-bottom:3rem">
        <h2 style="font-size:1.5rem;font-weight:600;margin-bottom:1.5rem">
            <i class="bi bi-headset"></i> Contacta con Soporte
        </h2>
        <div class="ayuda-contact-grid">
            <div class="ayuda-contact-card scale-in">
                <div class="ayuda-contact-icon"><i class="bi bi-envelope"></i></div>
                <h4 style="font-weight:600;margin-bottom:.5rem">Email</h4>
                <p style="color:var(--gray-600);font-size:.875rem">{{ $contacto['email'] }}</p>
            </div>
            <div class="ayuda-contact-card scale-in animate-delay-1">
                <div class="ayuda-contact-icon"><i class="bi bi-whatsapp"></i></div>
                <h4 style="font-weight:600;margin-bottom:.5rem">WhatsApp</h4>
                <p style="color:var(--gray-600);font-size:.875rem">{{ $contacto['whatsapp'] }}</p>
            </div>
            <div class="ayuda-contact-card scale-in animate-delay-2">
                <div class="ayuda-contact-icon"><i class="bi bi-clock"></i></div>
                <h4 style="font-weight:600;margin-bottom:.5rem">Horario</h4>
                <p style="color:var(--gray-600);font-size:.875rem">{{ $contacto['horario'] }}</p>
            </div>
        </div>
    </div>

    {{-- Formulario Ticket --}}
    <div style="background:#fff;border-radius:16px;padding:2rem;box-shadow:var(--shadow)">
        <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:1.5rem">
            <i class="bi bi-ticket-perforated"></i> Enviar Ticket de Soporte
        </h3>
        <form id="ticketForm" class="ayuda-ticket-form">
            <div class="form-group">
                <label>Asunto</label>
                <input type="text" name="asunto" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" required>
                    <option value="">Selecciona...</option>
                    <option value="pedidos">Pedidos</option>
                    <option value="clientes">Clientes</option>
                    <option value="comisiones">Comisiones</option>
                    <option value="referidos">Referidos</option>
                    <option value="tecnico">Soporte Técnico</option>
                </select>
            </div>
            <div class="form-group">
                <label>Prioridad</label>
                <select name="prioridad" required>
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" required placeholder="Describe tu problema con el mayor detalle posible..."></textarea>
            </div>
            <button type="submit" class="notif-btn notif-btn-primary">
                <i class="bi bi-send"></i> Enviar Ticket
            </button>
        </form>
    </div>
</div>

<div class="notif-toast-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendedor/ayuda-modern.js') }}?v={{ filemtime(public_path('js/vendedor/ayuda-modern.js')) }}"></script>
@endpush
