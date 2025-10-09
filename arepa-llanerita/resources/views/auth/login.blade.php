@extends('layouts.app')

@section('title', '- Iniciar Sesión')

@push('styles')
<!-- Preload critical resources -->
<link rel="preload" href="{{ asset('css/pages/login.css') }}?v={{ filemtime(public_path('css/pages/login.css')) }}" as="style">
<!-- Login styles with automatic cache busting -->
<link rel="stylesheet" href="{{ asset('css/pages/login.css') }}?v={{ filemtime(public_path('css/pages/login.css')) }}">
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-wrapper">
        <!-- Panel Izquierdo - Información de la Empresa -->
        <div class="col-lg-6 brand-panel">
            <div class="brand-content">
                <!-- Logo de la Empresa -->
                <div class="brand-logo">
                    <i class="bi bi-shop fs-1" style="color: white;"></i>
                </div>
                
                <!-- Información de la Empresa -->
                <h1 class="brand-title">Arepa la Llanerita</h1>
                <p class="brand-subtitle">
                    El sabor auténtico de los llanos colombianos.<br>
                    Sistema de ventas con red de referidos y comisiones.
                </p>
                
                <!-- Características Destacadas -->
                <ul class="brand-features">
                    <li>
                        <i class="bi bi-people-fill"></i>
                        Sistema de referidos inteligente
                    </li>
                    <li>
                        <i class="bi bi-cash-coin"></i>
                        Comisiones automáticas
                    </li>
                    <li>
                        <i class="bi bi-graph-up-arrow"></i>
                        Reportes en tiempo real
                    </li>
                    <li>
                        <i class="bi bi-shield-check"></i>
                        Gestión segura y eficiente
                    </li>
                    <li>
                        <i class="bi bi-heart-fill"></i>
                        Tradición y calidad llanera
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Panel Derecho - Formulario de Login -->
        <div class="col-lg-6 login-panel">
            <div class="login-content">
                <!-- Header del Login -->
                <div class="login-header">
                    <h2 class="login-title">¡Bienvenido de vuelta!</h2>
                    <p class="login-subtitle">Accede a tu cuenta para continuar</p>
                </div>

                <!-- Formulario de Login -->
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Campo Email -->
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

                    <!-- Campo Contraseña -->
                    <div class="form-group">
                        <div class="form-floating position-relative">
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="Contraseña">
                            <label for="password">
                                <i class="bi bi-lock me-2"></i>Contraseña
                            </label>
                            <button type="button" class="btn password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Recordar Sesión -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Recordar mi sesión
                        </label>
                    </div>

                    <!-- Botón de Acceso -->
                    <button type="submit" class="btn btn-login text-white">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Iniciar Sesión
                    </button>

                    <!-- Enlaces -->
                    <div class="login-links">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                <i class="bi bi-key me-1"></i>¿Olvidaste tu contraseña?
                            </a>
                        @endif

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
    // Configurar variables antes de cargar el módulo
    window.setLoginErrors && window.setLoginErrors(@json($errors->any()));
</script>
<script src="{{ asset('js/auth/login.js') }}?v={{ filemtime(public_path('js/auth/login.js')) }}" defer></script>
@endpush
