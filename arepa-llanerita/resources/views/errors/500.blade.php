@extends('layouts.app')

@section('title', '- Error del servidor')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center">
                <!-- Logo -->
                <div class="mb-4">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" style="height: 60px;" class="mb-3">
                </div>

                <!-- Error 500 -->
                <div class="display-1 fw-bold text-danger mb-3">500</div>

                <h2 class="h4 mb-3">Error interno del servidor</h2>

                <p class="text-muted mb-4">
                    Lo sentimos, algo salió mal en nuestros servidores. Nuestro equipo ha sido notificado automáticamente.
                </p>

                <!-- Estado del sistema -->
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Estado:</strong> Estamos trabajando para resolver este problema lo antes posible.
                </div>

                <!-- Sugerencias -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title">¿Qué puedes hacer?</h6>
                        <ul class="list-unstyled mb-0 text-start">
                            <li class="mb-2">
                                <i class="bi bi-arrow-clockwise text-primary me-2"></i>
                                Recargar la página en unos minutos
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-arrow-left text-primary me-2"></i>
                                Volver a la página anterior
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-house text-primary me-2"></i>
                                Ir a la página principal
                            </li>
                            <li>
                                <i class="bi bi-envelope text-primary me-2"></i>
                                Contactar soporte si el problema persiste
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2 d-md-block">
                    <button onclick="location.reload()" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Recargar página
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house me-1"></i>
                            Página principal
                        </a>
                    @endauth
                </div>

                <!-- Información de contacto -->
                <div class="mt-4 pt-4 border-top">
                    <p class="small text-muted mb-2">
                        <strong>Código de error:</strong> {{ uniqid('ERR-') }}
                    </p>
                    <p class="small text-muted">
                        Si necesitas ayuda inmediata:
                        <a href="mailto:soporte@arepallanerita.com" class="text-decoration-none">
                            soporte@arepallanerita.com
                        </a>
                        o
                        <a href="tel:+58424000000" class="text-decoration-none">
                            +58 424 000 0000
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.alert {
    border-left: 4px solid #ffc107;
}

.card {
    transition: all 0.3s ease;
}

.btn {
    transition: all 0.3s ease;
}
</style>
@endsection