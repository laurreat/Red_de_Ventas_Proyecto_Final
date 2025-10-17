@extends('layouts.lider')

@section('title', '- Centro de Ayuda')
@section('page-title', 'Centro de Ayuda')

@push('styles')
<style>
    .help-hero {
        background: linear-gradient(135deg, #722F37 0%, #5a252a 100%);
        padding: 3rem 2rem;
        border-radius: 16px;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(114, 47, 55, 0.3);
    }

    .help-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
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
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-size: 1rem;
    }

    .help-search i {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 1.25rem;
    }

    .category-card {
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        cursor: pointer;
        height: 100%;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(114, 47, 55, 0.15);
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
        background: rgba(114, 47, 55, 0.1);
        color: #722F37;
    }

    .category-card h6 {
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
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
        border-color: #722F37;
        box-shadow: 0 4px 12px rgba(114, 47, 55, 0.1);
    }

    .faq-item h6 {
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.75rem;
    }

    .contact-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e5e7eb;
        text-align: center;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }

    .contact-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(114, 47, 55, 0.1);
        color: #722F37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1rem;
    }

    .contact-card h6 {
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 1.5rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 500;
    }

    .btn-primary {
        background-color: #722F37;
        border-color: #722F37;
    }

    .btn-primary:hover {
        background-color: #5a252a;
        border-color: #5a252a;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="help-hero">
        <h1>¿Cómo podemos ayudarte?</h1>
        <p class="mb-4">Busca respuestas o explora nuestras categorías de ayuda</p>

        <div class="help-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchHelp" placeholder="Busca tutoriales, preguntas frecuentes...">
        </div>
    </div>

    <!-- Categorías -->
    <div class="mb-5">
        <h4 class="section-title">Explora por categoría</h4>
        <div class="row g-3">
            @foreach($categorias as $cat)
            <div class="col-md-3 col-sm-6">
                <div class="card category-card">
                    <div class="category-icon">
                        <i class="bi {{ $cat['icono'] }}"></i>
                    </div>
                    <h6>{{ $cat['titulo'] }}</h6>
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
            <h4 class="section-title">Preguntas Frecuentes</h4>
            @foreach($preguntasFrecuentes as $faq)
            <div class="faq-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6>{{ $faq['pregunta'] }}</h6>
                        <p class="text-muted mb-0">{{ $faq['respuesta'] }}</p>
                    </div>
                    <span class="badge" style="background-color: #722F37;">{{ $faq['categoria'] }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Contacto y Soporte -->
        <div class="col-lg-4">
            <h4 class="section-title">¿Necesitas más ayuda?</h4>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <h6>Email</h6>
                <p class="text-muted small mb-2">{{ $contacto['email'] }}</p>
                <button class="btn btn-sm btn-primary" onclick="abrirTicket()">Enviar ticket</button>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-telephone"></i>
                </div>
                <h6>Teléfono</h6>
                <p class="text-muted small mb-2">{{ $contacto['telefono'] }}</p>
                <p class="text-muted small mb-0">{{ $contacto['horario'] }}</p>
            </div>

            <div class="contact-card">
                <div class="contact-icon">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <h6>WhatsApp</h6>
                <p class="text-muted small mb-2">{{ $contacto['whatsapp'] }}</p>
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $contacto['whatsapp']) }}"
                   class="btn btn-sm btn-success" target="_blank">
                    <i class="bi bi-whatsapp me-1"></i>
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

    fetch('{{ route("lider.ayuda.ticket") }}', {
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
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el ticket. Por favor intenta de nuevo.');
    });
}

// Búsqueda en tiempo real (opcional)
const searchInput = document.getElementById('searchHelp');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;

        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                fetch(`{{ route("lider.ayuda.buscar") }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Resultados:', data.resultados);
                        // Aquí puedes mostrar los resultados en un dropdown
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        }
    });
}
</script>
@endpush
