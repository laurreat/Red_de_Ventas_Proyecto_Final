@extends('layouts.cliente')

@section('title', '- Ayuda y Soporte')
@section('header-title', 'Ayuda y Soporte')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #722F37 0%, #5a252a 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .ayuda-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Hero Section */
    .ayuda-hero {
        background: var(--primary-gradient);
        border-radius: 24px;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(114, 47, 55, 0.3);
    }

    .ayuda-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 0;
    }

    .ayuda-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto 2rem;
        line-height: 1.6;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 3rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .hero-stat {
        text-align: center;
    }

    .hero-stat-value {
        font-size: 2rem;
        font-weight: 800;
        display: block;
        margin-bottom: 0.25rem;
    }

    .hero-stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Search Section */
    .search-section {
        margin-bottom: 3rem;
    }

    .search-box {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 1.25rem 4rem 1.25rem 3.5rem;
        border: 3px solid #e5e7eb;
        border-radius: 16px;
        font-size: 1.125rem;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .search-input:focus {
        outline: none;
        border-color: #722F37;
        box-shadow: 0 8px 30px rgba(114, 47, 55, 0.15);
    }

    .search-icon {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
        color: #722F37;
    }

    .search-btn {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary-gradient);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-btn:hover {
        transform: translateY(-50%) scale(1.05);
        box-shadow: 0 4px 15px rgba(114, 47, 55, 0.3);
    }

    /* Quick Links */
    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .quick-link-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .quick-link-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary-gradient);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .quick-link-card:hover {
        transform: translateY(-8px);
        border-color: #722F37;
        box-shadow: 0 12px 35px rgba(114, 47, 55, 0.2);
    }

    .quick-link-card:hover::before {
        transform: scaleY(1);
    }

    .quick-link-icon {
        width: 64px;
        height: 64px;
        background: var(--primary-gradient);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 15px rgba(114, 47, 55, 0.25);
    }

    .quick-link-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .quick-link-desc {
        font-size: 0.9375rem;
        color: #6b7280;
        line-height: 1.6;
    }

    /* FAQ Section */
    .faq-section {
        margin-bottom: 3rem;
    }

    .section-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: #6b7280;
    }

    .faq-categories {
        display: grid;
        gap: 2rem;
    }

    .faq-category {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .faq-category-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #722F37;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .faq-category-icon {
        width: 48px;
        height: 48px;
        background: var(--primary-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .accordion-item {
        border: none;
        margin-bottom: 1rem;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .accordion-button {
        background: white;
        color: #1f2937;
        font-weight: 600;
        padding: 1.25rem 1.5rem;
        border: none;
        font-size: 1.0625rem;
    }

    .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, rgba(114, 47, 55, 0.08) 0%, rgba(114, 47, 55, 0.04) 100%);
        color: #722F37;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 3px rgba(114, 47, 55, 0.15);
        border-color: #722F37;
    }

    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23722F37'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .accordion-body {
        padding: 1.5rem;
        background: #f9fafb;
        color: #4b5563;
        line-height: 1.7;
    }

    /* Contact Section */
    .contact-section {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 3rem;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .contact-card {
        text-align: center;
        padding: 2rem;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 16px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        border-color: #722F37;
        box-shadow: 0 8px 25px rgba(114, 47, 55, 0.15);
        transform: translateY(-4px);
    }

    .contact-icon {
        width: 72px;
        height: 72px;
        background: var(--primary-gradient);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1.5rem;
        box-shadow: 0 6px 20px rgba(114, 47, 55, 0.25);
    }

    .contact-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .contact-value {
        font-size: 1.0625rem;
        color: #722F37;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .contact-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--primary-gradient);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .contact-action:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(114, 47, 55, 0.3);
        color: white;
    }

    /* Support Ticket Form */
    .ticket-form {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #722F37;
        box-shadow: 0 0 0 3px rgba(114, 47, 55, 0.1);
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .btn-submit {
        background: var(--primary-gradient);
        border: none;
        padding: 1rem 3rem;
        border-radius: 12px;
        color: white;
        font-weight: 700;
        font-size: 1.0625rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(114, 47, 55, 0.25);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(114, 47, 55, 0.35);
    }

    /* Tutorials Section */
    .tutorials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .tutorial-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        text-decoration: none;
        display: block;
    }

    .tutorial-card:hover {
        transform: translateY(-8px);
        border-color: #722F37;
        box-shadow: 0 12px 35px rgba(114, 47, 55, 0.2);
    }

    .tutorial-icon {
        width: 56px;
        height: 56px;
        background: var(--primary-gradient);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
    }

    .tutorial-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .tutorial-desc {
        font-size: 0.9375rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .tutorial-link {
        color: #722F37;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9375rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 1.75rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .hero-stats {
            gap: 1.5rem;
        }

        .search-input {
            padding: 1rem 3rem 1rem 2.5rem;
            font-size: 1rem;
        }

        .search-btn {
            position: static;
            transform: none;
            width: 100%;
            margin-top: 0.5rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .contact-section, .ticket-form {
            padding: 1.5rem;
        }

        .ayuda-hero {
            padding: 2rem 1.5rem;
        }
    }

    /* Alerts */
    .alert {
        border-radius: 12px;
        border: none;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
        border-left: 4px solid #10b981;
        color: #065f46;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }
</style>
@endpush

@section('content')
<div class="ayuda-container">
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <div class="d-flex align-items-start">
            <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="ayuda-hero">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="bi bi-question-circle-fill"></i>
                Como podemos ayudarte
            </h1>
            <p class="hero-subtitle">
                Encuentra respuestas rapidas a tus preguntas o ponte en contacto con nuestro equipo de soporte
            </p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-value"><i class="bi bi-clock-history"></i> 24/7</span>
                    <span class="hero-stat-label">Soporte disponible</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-value"><i class="bi bi-lightning-charge-fill"></i> menor a 1h</span>
                    <span class="hero-stat-label">Tiempo de respuesta</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-value"><i class="bi bi-star-fill"></i> 4.9/5</span>
                    <span class="hero-stat-label">Satisfaccion</span>
                </div>
            </div>
        </div>
    </div>

    <div class="search-section">
        <div class="search-box">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Busca tu pregunta aqui...">
                <button class="search-btn" onclick="searchFAQ()">
                    <i class="bi bi-search"></i>
                    Buscar
                </button>
            </div>
        </div>
    </div>

    <div class="quick-links">
        <a href="#faqs" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="bi bi-patch-question-fill"></i>
            </div>
            <h3 class="quick-link-title">Preguntas Frecuentes</h3>
            <p class="quick-link-desc">Encuentra respuestas a las dudas mas comunes</p>
        </a>

        <a href="#contact" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="bi bi-headset"></i>
            </div>
            <h3 class="quick-link-title">Contactar Soporte</h3>
            <p class="quick-link-desc">Habla directamente con nuestro equipo</p>
        </a>

        <a href="#tutorials" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="bi bi-play-circle-fill"></i>
            </div>
            <h3 class="quick-link-title">Tutoriales</h3>
            <p class="quick-link-desc">Aprende a usar todas las funciones</p>
        </a>

        <a href="#ticket" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
            <h3 class="quick-link-title">Enviar Ticket</h3>
            <p class="quick-link-desc">Reporta un problema o consulta especifica</p>
        </a>
    </div>

    <div class="faq-section" id="faqs">
        <div class="section-header">
            <h2 class="section-title">Preguntas Frecuentes</h2>
            <p class="section-subtitle">Las respuestas a las preguntas mas comunes de nuestros clientes</p>
        </div>

        <div class="faq-categories">
            @foreach($faqs as $index => $categoria)
            <div class="faq-category">
                <h3 class="faq-category-title">
                    <div class="faq-category-icon">
                        @if($categoria['categoria'] == 'Pedidos')
                            <i class="bi bi-box-seam-fill"></i>
                        @elseif($categoria['categoria'] == 'Pagos')
                            <i class="bi bi-credit-card-fill"></i>
                        @elseif($categoria['categoria'] == 'Cuenta')
                            <i class="bi bi-person-circle"></i>
                        @else
                            <i class="bi bi-cart-check-fill"></i>
                        @endif
                    </div>
                    {{ $categoria['categoria'] }}
                </h3>

                <div class="accordion" id="accordion{{ $index }}">
                    @foreach($categoria['preguntas'] as $idx => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}{{ $idx }}">
                                {{ $faq['pregunta'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}{{ $idx }}" class="accordion-collapse collapse {{ $idx == 0 ? 'show' : '' }}" data-bs-parent="#accordion{{ $index }}">
                            <div class="accordion-body">
                                {{ $faq['respuesta'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="faq-section" id="tutorials">
        <div class="section-header">
            <h2 class="section-title">Tutoriales y Guias</h2>
            <p class="section-subtitle">Aprende a sacar el maximo provecho de nuestra plataforma</p>
        </div>

        <div class="tutorials-grid">
            @foreach($tutoriales as $tutorial)
            <a href="{{ $tutorial['url'] }}" class="tutorial-card">
                <div class="tutorial-icon">
                    <i class="{{ $tutorial['icono'] }}"></i>
                </div>
                <h3 class="tutorial-title">{{ $tutorial['titulo'] }}</h3>
                <p class="tutorial-desc">{{ $tutorial['descripcion'] }}</p>
                <span class="tutorial-link">
                    Ver tutorial
                    <i class="bi bi-arrow-right"></i>
                </span>
            </a>
            @endforeach
        </div>
    </div>

    <div class="contact-section" id="contact">
        <div class="section-header">
            <h2 class="section-title">Contactanos Directamente</h2>
            <p class="section-subtitle">Estamos disponibles para ayudarte por multiples canales</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <h3 class="contact-title">Email</h3>
                <p class="contact-value">{{ $contacto['email'] }}</p>
                <a href="mailto:{{ $contacto['email'] }}" class="contact-action">
                    <i class="bi bi-send-fill"></i>
                    Enviar email
                </a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <h3 class="contact-title">Telefono</h3>
                <p class="contact-value">{{ $contacto['telefono'] }}</p>
                <a href="tel:{{ str_replace(' ', '', $contacto['telefono']) }}" class="contact-action">
                    <i class="bi bi-telephone-fill"></i>
                    Llamar ahora
                </a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <h3 class="contact-title">WhatsApp</h3>
                <p class="contact-value">{{ $contacto['whatsapp'] }}</p>
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $contacto['whatsapp']) }}" target="_blank" class="contact-action">
                    <i class="bi bi-whatsapp"></i>
                    Abrir chat
                </a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <h3 class="contact-title">Horario</h3>
                <p class="contact-value" style="font-size: 0.9375rem; line-height: 1.6;">{{ $contacto['horario'] }}</p>
            </div>
        </div>
    </div>

    <div class="ticket-form" id="ticket">
        <div class="section-header">
            <h2 class="section-title">Enviar Ticket de Soporte</h2>
            <p class="section-subtitle">No encontraste lo que buscabas? Envianos tu consulta</p>
        </div>

        <form action="{{ route('cliente.ayuda.enviar-ticket') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="asunto" class="form-label">Asunto *</label>
                    <input type="text" class="form-control" id="asunto" name="asunto" value="{{ old('asunto') }}" required placeholder="Ej: Problema con mi pedido">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="categoria" class="form-label">Categoria *</label>
                    <select class="form-select" id="categoria" name="categoria" required>
                        <option value="">Selecciona una categoria</option>
                        <option value="pedido" {{ old('categoria') == 'pedido' ? 'selected' : '' }}>Pedidos</option>
                        <option value="pago" {{ old('categoria') == 'pago' ? 'selected' : '' }}>Pagos</option>
                        <option value="producto" {{ old('categoria') == 'producto' ? 'selected' : '' }}>Productos</option>
                        <option value="cuenta" {{ old('categoria') == 'cuenta' ? 'selected' : '' }}>Mi Cuenta</option>
                        <option value="otro" {{ old('categoria') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="pedido_id" class="form-label">ID del Pedido (opcional)</label>
                    <input type="text" class="form-control" id="pedido_id" name="pedido_id" value="{{ old('pedido_id') }}" placeholder="Si tu consulta es sobre un pedido especifico">
                </div>

                <div class="col-md-12 mb-4">
                    <label for="mensaje" class="form-label">Mensaje *</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" required placeholder="Describe tu consulta o problema en detalle...">{{ old('mensaje') }}</textarea>
                    <small class="text-muted">Minimo 10 caracteres</small>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send-fill"></i>
                        Enviar Consulta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function searchFAQ() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        if (!searchTerm) {
            document.querySelectorAll('.accordion-item').forEach(item => {
                item.style.display = 'block';
            });
            return;
        }

        document.querySelectorAll('.accordion-item').forEach(item => {
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                const collapse = item.querySelector('.accordion-collapse');
                if (collapse && !collapse.classList.contains('show')) {
                    const button = item.querySelector('.accordion-button');
                    button.click();
                }
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById('faqs').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchFAQ();
        }
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
@endpush
@endsection
