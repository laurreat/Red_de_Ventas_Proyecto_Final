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

                <form method="POST" action="{{ route('password.email') }}">
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

                    <button type="submit" class="btn btn-login text-white">
                        <i class="bi bi-send me-2"></i>
                        Enviar Enlace de Restablecimiento
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
