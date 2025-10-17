@extends('layouts.vendedor')

@section('title', ' - Mensajes de mi Líder')
@section('page-title', 'Mensajes de mi Líder')

@push('styles')
<style>
.mensajes-container {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(114,47,55,0.1);
}

.mensaje-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
}

.mensaje-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #722F37;
}

.mensaje-card.no-leido {
    background: linear-gradient(135deg, #fff9f0, #ffffff);
    border-left: 4px solid #f59e0b;
}

.mensaje-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.mensaje-tipo {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
}

.tipo-motivacion {
    background: #eff6ff;
    color: #1e40af;
}

.tipo-felicitacion {
    background: #f0fdf4;
    color: #15803d;
}

.tipo-recomendacion {
    background: #fef3c7;
    color: #b45309;
}

.tipo-alerta {
    background: #fee2e2;
    color: #991b1b;
}

.tipo-otro {
    background: #f3f4f6;
    color: #374151;
}

.mensaje-body {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-size: 1rem;
    line-height: 1.6;
    color: #1f2937;
}

.mensaje-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    text-align: center;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
    color: white;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.badge-no-leido {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mensajes-container fade-in-up mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2" style="color: #722F37; font-weight: 700;">
                    <i class="bi bi-envelope-open me-2"></i>
                    Mensajes de mi Líder
                </h1>
                <p class="text-muted mb-0">
                    Mensajes, indicaciones y feedback de tu líder para ayudarte a mejorar
                </p>
            </div>
            @if($mensajesNoLeidos > 0)
                <div class="badge bg-warning text-dark" style="font-size: 1.25rem; padding: 0.75rem 1.5rem;">
                    {{ $mensajesNoLeidos }} nuevos
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid fade-in-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #722F37, #5a252d);">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Mensajes</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="bi bi-envelope-exclamation"></i>
            </div>
            <div class="stat-value">{{ $stats['no_leidos'] }}</div>
            <div class="stat-label">No Leídos</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="bi bi-envelope-check"></i>
            </div>
            <div class="stat-value">{{ $stats['leidos'] }}</div>
            <div class="stat-label">Leídos</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="bi bi-hand-thumbs-up-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['por_tipo']['motivacion'] }}</div>
            <div class="stat-label">Motivación</div>
        </div>
    </div>

    <!-- Mensajes -->
    <div class="mensajes-container fade-in-up">
        <h3 class="mb-4" style="color: #722F37; font-weight: 600;">
            <i class="bi bi-chat-left-text me-2"></i>
            Todos los Mensajes
        </h3>

        @forelse($mensajes as $mensaje)
            <div class="mensaje-card {{ !$mensaje->leido ? 'no-leido' : '' }}" data-mensaje-id="{{ $mensaje->id }}">
                @if(!$mensaje->leido)
                    <span class="badge-no-leido">Nuevo</span>
                @endif

                <div class="mensaje-header">
                    <div>
                        <span class="mensaje-tipo tipo-{{ $mensaje->tipo_mensaje }}">
                            @switch($mensaje->tipo_mensaje)
                                @case('motivacion')
                                    💪 Motivación
                                    @break
                                @case('felicitacion')
                                    🎉 Felicitación
                                    @break
                                @case('recomendacion')
                                    💡 Recomendación
                                    @break
                                @case('alerta')
                                    ⚠️ Alerta
                                    @break
                                @default
                                    📝 Mensaje
                            @endswitch
                        </span>
                    </div>
                    <div class="text-end">
                        <div style="font-weight: 600; color: #722F37;">
                            <i class="bi bi-person-badge me-1"></i>
                            {{ $mensaje->lider->name }}
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            {{ $mensaje->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                <div class="mensaje-body">
                    {{ $mensaje->mensaje }}
                </div>

                <div class="mensaje-footer">
                    <div>
                        @if($mensaje->leido)
                            <span class="text-success">
                                <i class="bi bi-check2-circle me-1"></i>
                                Leído {{ $mensaje->fecha_lectura->diffForHumans() }}
                            </span>
                        @else
                            <button class="btn btn-sm btn-outline-primary" onclick="marcarComoLeido('{{ $mensaje->id }}')">
                                <i class="bi bi-check2 me-1"></i>Marcar como leído
                            </button>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted">
                            {{ $mensaje->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4 style="color: #6b7280; font-weight: 600;">No tienes mensajes</h4>
                <p class="text-muted">
                    Aquí aparecerán los mensajes que tu líder te envíe
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function marcarComoLeido(mensajeId) {
    fetch(`/vendedor/mensajes/${mensajeId}/marcar-leido`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la tarjeta del mensaje
            const card = document.querySelector(`[data-mensaje-id="${mensajeId}"]`);
            card.classList.remove('no-leido');

            // Remover badge de "Nuevo"
            const badge = card.querySelector('.badge-no-leido');
            if (badge) badge.remove();

            // Actualizar footer
            const footer = card.querySelector('.mensaje-footer > div:first-child');
            footer.innerHTML = `
                <span class="text-success">
                    <i class="bi bi-check2-circle me-1"></i>
                    Leído hace un momento
                </span>
            `;

            // Recargar página para actualizar contadores
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo marcar el mensaje como leído.',
            confirmButtonColor: '#722F37'
        });
    });
}

// Marcar automáticamente como leído al hacer scroll
const observerOptions = {
    root: null,
    threshold: 0.8,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const card = entry.target;
            if (card.classList.contains('no-leido')) {
                const mensajeId = card.dataset.mensajeId;
                setTimeout(() => {
                    marcarComoLeido(mensajeId);
                }, 2000); // Marcar como leído después de 2 segundos de visualización
            }
        }
    });
}, observerOptions);

// Observar todas las tarjetas de mensajes no leídos
document.querySelectorAll('.mensaje-card.no-leido').forEach(card => {
    observer.observe(card);
});
</script>
@endpush
