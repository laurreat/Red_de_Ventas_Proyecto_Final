@extends('layouts.app')

@section('title', '- Recuperar Contraseña')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-wrapper">
        <!-- Panel Izquierdo - Información -->
        <div class="col-lg-6 brand-panel">
            <div class="brand-content">
                <div class="brand-logo">
                    <i class="bi bi-shield-lock fs-1" style="color: white;"></i>
                </div>

                <h1 class="brand-title">Recuperar Contraseña</h1>
                <p class="brand-subtitle">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>

                <ul class="brand-features">
                    <li>
                        <i class="bi bi-shield-check"></i>
                        Proceso seguro y confiable
                    </li>
                    <li>
                        <i class="bi bi-clock"></i>
                        El enlace expira en 1 hora
                    </li>
                    <li>
                        <i class="bi bi-envelope-check"></i>
                        Verificación por correo electrónico
                    </li>
                </ul>
            </div>
        </div>

        <!-- Panel Derecho - Formulario -->
        <div class="col-lg-6 login-panel">
            <div class="login-content">
                <div class="login-header">
                    <h2 class="login-title">Recuperar Contraseña</h2>
                    <p class="login-subtitle">Ingresa tu correo electrónico</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                    @csrf

                    <div class="form-group">
                        <div class="form-floating">
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required autocomplete="email" autofocus
                                   placeholder="correo@ejemplo.com">
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>Correo Electrónico
                            </label>
                            @error('email')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login text-white" id="submitBtn">
                        <i class="bi bi-send me-2"></i>
                        <span id="btnText">Enviar Enlace de Restablecimiento</span>
                    </button>

                    <div class="login-links">
                        <a href="{{ route('login') }}">
                            <i class="bi bi-arrow-left me-1"></i>Volver al Login
                        </a>

                        @if (Route::has('register'))
                            <div class="mt-2">
                                <span class="text-muted">¿No tienes cuenta?</span>
                                <a href="{{ route('register') }}" class="fw-bold ms-1">
                                    Regístrate aquí
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const emailInput = document.getElementById('email');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Verificar conexión a internet antes de enviar
            if (!navigator.onLine) {
                e.preventDefault();
                
                // Mostrar alerta de error
                const existingAlert = document.querySelector('.alert-danger');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="bi bi-wifi-off me-2"></i>
                    <strong>Sin conexión a internet.</strong> Por favor, verifica tu conexión e intenta nuevamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const loginHeader = document.querySelector('.login-header');
                loginHeader.insertAdjacentElement('afterend', alertDiv);
                
                // Auto-cerrar alerta después de 5 segundos
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
                
                return false;
            }
            
            // Deshabilitar botón para evitar múltiples envíos
            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        });
        
        // Monitorear el estado de conexión
        window.addEventListener('online', function() {
            console.log('✅ Conexión restaurada');
        });
        
        window.addEventListener('offline', function() {
            console.log('⚠️ Sin conexión a internet');
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                btnText.textContent = 'Enviar Enlace de Restablecimiento';
            }
        });
    }
});
</script>
@endpush
