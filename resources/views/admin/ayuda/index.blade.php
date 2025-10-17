@extends('layouts.admin')

@section('title', '- Centro de Ayuda')
@section('page-title', 'Centro de Ayuda')

@push('styles')
<style>
    .help-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8b3c44 100%);
        padding: 3rem 2rem;
        border-radius: 16px;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
    }

    .help-search {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .help-search input {
        border-radius: 50px;
        padding: 1rem 3rem 1rem 3.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .help-search i {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .category-card {
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        cursor: pointer;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .faq-item {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s;
    }

    .faq-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(114, 47, 55, 0.1);
    }

    .contact-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e5e7eb;
        text-align: center;
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(114, 47, 55, 0.1);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="help-hero">
        <h1 class="mb-3">¿Cómo podemos ayudarte?</h1>
        <p class="mb-4">Busca respuestas o explora nuestras categorías de ayuda</p>

        <div class="help-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchHelp" placeholder="Busca tutoriales, preguntas frecuentes...">
        </div>
    </div>

    <!-- Categorías -->
    <div class="mb-5">
        <h4 class="mb-4">Explora por categoría</h4>
        <div class="row g-3">
            @foreach($categorias as $cat)
            <div class="col-md-3">
                <div class="card category-card">
                    <div class="category-icon" style="background: rgba(114, 47, 55, 0.1); color: var(--primary-color);">
                        <i class="bi {{ $cat['icono'] }}"></i>
                    </div>
                    <h6 class="fw-bold">{{ $cat['titulo'] }}</h6>
                    <p class="text-muted small mb-2">{{ $cat['descripcion'] }}</p>
                    <span class="badge bg-secondary">{{ $cat['articulos'] }} artículos</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        <!-- Preguntas Frecuentes -->
        <div class="col-lg-8 mb-4">
            <h4 class="mb-4">Preguntas Frecuentes</h4>
            @foreach($preguntasFrecuentes as $faq)
            <div class="faq-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-2">{{ $faq['pregunta'] }}</h6>
                        <p class="text-muted mb-0">{{ $faq['respuesta'] }}</p>
                    </div>
                    <span class="badge bg-primary">{{ $faq['categoria'] }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Contacto y Soporte -->
        <div class="col-lg-4">
            <h4 class="mb-4">¿Necesitas más ayuda?</h4>

            <div class="contact-card mb-3">
                <div class="contact-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <h6 class="fw-bold mb-2">Email</h6>
                <p class="text-muted small mb-2">{{ $contacto['email'] }}</p>
                <button class="btn btn-sm btn-primary" onclick="abrirTicket()">Enviar ticket</button>
            </div>

            <div class="contact-card mb-3">
                <div class="contact-icon">
                    <i class="bi bi-telephone"></i>
                </div>
                <h6 class="fw-bold mb-2">Teléfono</h6>
                <p class="text-muted small mb-2">{{ $contacto['telefono'] }}</p>
                <p class="text-muted small">{{ $contacto['horario'] }}</p>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <h6 class="fw-bold mb-2">WhatsApp</h6>
                <p class="text-muted small mb-2">{{ $contacto['whatsapp'] }}</p>
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $contacto['whatsapp']) }}"
                   class="btn btn-sm btn-success" target="_blank">
                    Abrir chat
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ticket -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar Ticket de Soporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="ticketForm">
                    <div class="mb-3">
                        <label class="form-label">Asunto</label>
                        <input type="text" class="form-control" name="asunto" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" required>
                            @foreach($categorias as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['titulo'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridad</label>
                        <select class="form-select" name="prioridad" required>
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarTicket()">Enviar Ticket</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abrirTicket() {
    new bootstrap.Modal(document.getElementById('ticketModal')).show();
}

function enviarTicket() {
    const form = document.getElementById('ticketForm');
    const formData = new FormData(form);

    fetch('{{ route("admin.ayuda.ticket") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('ticketModal')).hide();
            form.reset();
        }
    });
}
</script>
@endpush
