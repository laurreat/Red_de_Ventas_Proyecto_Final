@extends('layouts.app')

@section('title', '- Iniciar Sesión')

@push('styles')
<style>
    /* Diseño horizontal completo para pantallas de PC */
    .login-wrapper {
        min-height: 100vh;
        height: 100vh;
        overflow: hidden;
    }
    
    /* Panel izquierdo - Información de la empresa */
    .brand-panel {
        background: linear-gradient(135deg, var(--arepa-primary) 0%, var(--arepa-accent) 100%);
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
    }
    
    .brand-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="arepa-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23arepa-pattern)"/></svg>');
        opacity: 0.5;
    }
    
    .brand-content {
        text-align: center;
        z-index: 1;
        max-width: 400px;
    }
    
    .brand-logo {
        width: 130px;
        height: 130px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        backdrop-filter: blur(10px);
        border: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .brand-logo:hover {
        transform: scale(1.05);
    }
    
    .brand-title {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        line-height: 1.2;
    }
    
    .brand-subtitle {
        font-size: 1.1rem;
        font-weight: 400;
        opacity: 0.95;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }
    
    .brand-features {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }
    
    .brand-features li {
        padding: 0.6rem 0;
        display: flex;
        align-items: center;
        font-size: 1rem;
        opacity: 0.9;
        transition: all 0.3s ease;
    }
    
    .brand-features li:hover {
        opacity: 1;
        transform: translateX(5px);
    }
    
    .brand-features i {
        margin-right: 1rem;
        font-size: 1.2rem;
        width: 20px;
        text-align: center;
    }
    
    /* Panel derecho - Formulario de login */
    .login-panel {
        background: linear-gradient(135deg, var(--arepa-cream) 0%, #f1f1f1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        min-height: 100vh;
    }
    
    .login-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
        padding: 2.5rem;
        width: 100%;
        max-width: 480px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }
    
    .login-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-floating {
        position: relative;
    }
    
    .form-floating > .form-control {
        height: 58px;
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .form-floating > .form-control:focus {
        border-color: var(--arepa-primary);
        background: white;
        box-shadow: 0 0 0 0.2rem rgba(114, 47, 55, 0.15);
    }
    
    .form-floating > label {
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--arepa-primary);
        font-size: 0.8rem;
    }
    
    .form-check {
        padding-left: 0;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 0.8rem;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
    }
    
    .form-check-input:checked {
        background-color: var(--arepa-primary);
        border-color: var(--arepa-primary);
    }
    
    .btn-login {
        background: linear-gradient(135deg, var(--arepa-primary) 0%, var(--arepa-accent) 100%);
        border: none;
        padding: 1rem 2rem;
        font-weight: 600;
        border-radius: 14px;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        width: 100%;
        margin-bottom: 1.25rem;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(114, 47, 55, 0.3);
    }
    
    .login-links {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .login-links a {
        color: var(--arepa-primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .login-links a:hover {
        color: var(--arepa-accent);
    }
    
    .demo-users {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 18px;
        padding: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .demo-users h6 {
        color: #4a5568;
        margin-bottom: 1rem;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .demo-user-btn {
        display: block;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        color: #4a5568;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        text-align: left;
    }
    
    .demo-user-btn:hover {
        background: var(--arepa-primary);
        border-color: var(--arepa-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.2);
        text-decoration: none;
    }
    
    .demo-user-btn i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .demo-user-role {
        font-size: 0.75rem;
        opacity: 0.8;
        font-weight: 400;
        margin-top: 0.2rem;
    }
    
    /* Responsive Design */
    
    /* Pantallas grandes (Desktop) */
    @media (min-width: 1200px) {
        .brand-logo {
            width: 150px;
            height: 150px;
        }
        
        .brand-title {
            font-size: 2.5rem;
        }
        
        .brand-subtitle {
            font-size: 1.2rem;
        }
        
        .login-card {
            padding: 3rem;
            max-width: 520px;
        }
        
        .login-title {
            font-size: 2rem;
        }
    }
    
    /* Pantallas medianas (Tablets horizontales) */
    @media (max-width: 1199.98px) and (min-width: 992px) {
        .brand-logo {
            width: 120px;
            height: 120px;
        }
        
        .brand-title {
            font-size: 2rem;
        }
        
        .brand-subtitle {
            font-size: 1rem;
        }
        
        .brand-features li {
            font-size: 0.95rem;
        }
        
        .login-card {
            padding: 2.2rem;
        }
    }
    
    /* Tablets verticales */
    @media (max-width: 991.98px) and (min-width: 768px) {
        .login-wrapper {
            flex-direction: column;
            height: auto;
            min-height: 100vh;
        }
        
        .brand-panel {
            min-height: 40vh;
            padding: 2rem 1rem;
        }
        
        .brand-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 1rem;
        }
        
        .brand-title {
            font-size: 1.8rem;
        }
        
        .brand-subtitle {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        
        .brand-features {
            display: none;
        }
        
        .login-panel {
            min-height: 60vh;
            padding: 2rem 1.5rem;
        }
        
        .login-card {
            padding: 2rem;
        }
        
        .login-title {
            font-size: 1.6rem;
        }
    }
    
    /* Móviles */
    @media (max-width: 767.98px) {
        .login-wrapper {
            flex-direction: column;
            height: auto;
            min-height: 100vh;
        }
        
        .brand-panel {
            min-height: 35vh;
            padding: 1.5rem 1rem;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        
        .brand-title {
            font-size: 1.6rem;
        }
        
        .brand-subtitle {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .brand-features {
            display: none;
        }
        
        .login-panel {
            min-height: 65vh;
            padding: 1.5rem 1rem;
        }
        
        .login-card {
            padding: 1.5rem;
            border-radius: 20px;
        }
        
        .login-title {
            font-size: 1.4rem;
        }
        
        .form-floating > .form-control {
            height: 54px;
        }
        
        .demo-users {
            padding: 1.2rem;
        }
        
        .demo-user-btn {
            padding: 0.7rem 0.8rem;
            font-size: 0.9rem;
        }
    }
    
    /* Móviles pequeños */
    @media (max-width: 575.98px) {
        .brand-title {
            font-size: 1.4rem;
        }
        
        .brand-subtitle {
            font-size: 0.85rem;
        }
        
        .login-title {
            font-size: 1.3rem;
        }
        
        .form-floating > .form-control {
            height: 52px;
            font-size: 0.95rem;
        }
        
        .btn-login {
            font-size: 1rem;
            padding: 0.9rem 1.5rem;
        }
    }
</style>
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
