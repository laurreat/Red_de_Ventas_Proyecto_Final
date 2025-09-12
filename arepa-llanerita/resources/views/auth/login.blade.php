@extends('layouts.app')

@section('title', '- Iniciar Sesión')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
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
            <div class="login-card">
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
                        <div class="form-floating">
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password"
                                   placeholder="Contraseña">
                            <label for="password">
                                <i class="bi bi-lock me-2"></i>Contraseña
                            </label>
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
                
                <!-- Usuarios de Prueba -->
                <div class="demo-users">
                    <h6 class="text-center mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Usuarios de Prueba - Demo
                    </h6>
                    
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="#" class="demo-user-btn" onclick="fillDemo('admin@arepallanerita.com', 'admin123')">
                                <i class="bi bi-person-gear"></i>
                                <strong>Admin</strong>
                                <div class="demo-user-role">Panel completo</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="#" class="demo-user-btn" onclick="fillDemo('lider@arepallanerita.com', 'lider123')">
                                <i class="bi bi-person-badge"></i>
                                <strong>Líder</strong>
                                <div class="demo-user-role">Gestión equipo</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="#" class="demo-user-btn" onclick="fillDemo('vendedor@arepallanerita.com', 'vendedor123')">
                                <i class="bi bi-person-check"></i>
                                <strong>Vendedor</strong>
                                <div class="demo-user-role">Ventas</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="demo-user-btn" onclick="fillDemo('cliente@test.com', 'cliente123')">
                                <i class="bi bi-person"></i>
                                <strong>Cliente</strong>
                                <div class="demo-user-role">Compras</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        
        // Agregar efecto visual
        showSuccessToast('¡Credenciales cargadas! Haz clic en "Iniciar Sesión"');
        
        // Animar el botón de login
        const loginBtn = document.querySelector('.btn-login');
        loginBtn.style.transform = 'scale(1.05)';
        loginBtn.style.boxShadow = '0 20px 40px rgba(255, 107, 53, 0.4)';
        
        setTimeout(() => {
            loginBtn.style.transform = 'scale(1)';
            loginBtn.style.boxShadow = '0 15px 35px rgba(255, 107, 53, 0.3)';
        }, 200);
    }
    
    // Validación del formulario
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        showLoading();
    });
    
    // Ocultar loading si hay errores
    @if($errors->any())
        hideLoading();
    @endif
    
    // Animaciones de entrada
    document.addEventListener('DOMContentLoaded', function() {
        const loginCard = document.querySelector('.login-card');
        const brandContent = document.querySelector('.brand-content');
        
        // Animación de entrada
        loginCard.style.opacity = '0';
        loginCard.style.transform = 'translateX(30px)';
        brandContent.style.opacity = '0';
        brandContent.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
            loginCard.style.transition = 'all 0.8s ease';
            brandContent.style.transition = 'all 0.8s ease';
            loginCard.style.opacity = '1';
            loginCard.style.transform = 'translateX(0)';
            brandContent.style.opacity = '1';
            brandContent.style.transform = 'translateX(0)';
        }, 100);
    });
</script>
@endpush
