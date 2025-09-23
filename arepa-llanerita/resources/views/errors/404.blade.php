@extends('layouts.app')

@section('title', '- Página no encontrada')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center">
                <!-- Logo o icono -->
                <div class="mb-4">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" style="height: 60px;" class="mb-3">
                </div>

                <!-- Error 404 -->
                <div class="display-1 fw-bold text-primary mb-3">404</div>

                <h2 class="h4 mb-3">Página no encontrada</h2>

                <p class="text-muted mb-4">
                    Lo sentimos, la página que buscas no existe o ha sido movida.
                </p>

                <!-- Sugerencias -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-house fs-4 text-primary mb-2"></i>
                                <p class="small mb-0">Ir al inicio</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-arrow-left fs-4 text-primary mb-2"></i>
                                <p class="small mb-0">Volver atrás</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2 d-md-block">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Iniciar Sesión
                        </a>
                    @endauth

                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </button>
                </div>

                <!-- Contacto -->
                <div class="mt-4 pt-4 border-top">
                    <p class="small text-muted">
                        ¿Necesitas ayuda?
                        <a href="mailto:soporte@arepallanerita.com" class="text-decoration-none">
                            Contacta soporte
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.3s ease;
}
</style>
@endsection