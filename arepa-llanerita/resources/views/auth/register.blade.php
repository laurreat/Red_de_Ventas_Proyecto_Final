@extends('layouts.app')

@section('title', '- Registro')

@push('styles')
<style>
    /* Diseño horizontal completo para pantallas de PC */
    .register-wrapper {
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
        margin-right: 12px;
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.8);
        width: 24px;
        text-align: center;
    }
    
    /* Panel derecho - Formulario de registro */
    .form-panel {
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        min-height: 100vh;
        position: relative;
    }
    
    .register-form-container {
        width: 100%;
        max-width: 500px;
        padding: 0 1rem;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .register-title {
        color: var(--arepa-primary);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .register-subtitle {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 0;
    }
    
    .form-floating {
        margin-bottom: 1.5rem;
    }
    
    .form-floating > .form-control {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-floating > .form-control:focus {
        border-color: var(--arepa-primary);
        box-shadow: 0 0 0 0.2rem rgba(114, 47, 55, 0.25);
    }
    
    .form-floating > label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .btn-register {
        background: linear-gradient(135deg, var(--arepa-primary) 0%, var(--arepa-accent) 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 12px;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }
    
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(114, 47, 55, 0.3);
        color: white;
    }
    
    .form-check {
        margin-bottom: 1.5rem;
    }
    
    .form-check-input:checked {
        background-color: var(--arepa-primary);
        border-color: var(--arepa-primary);
    }
    
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }
    
    .login-link a {
        color: var(--arepa-primary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .login-link a:hover {
        color: var(--arepa-accent);
        text-decoration: underline;
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .register-wrapper {
            height: auto;
            min-height: 100vh;
        }
        
        .brand-panel {
            min-height: 50vh;
            padding: 2rem 1rem;
        }
        
        .form-panel {
            min-height: 50vh;
            padding: 2rem 1rem;
        }
        
        .brand-title {
            font-size: 1.8rem;
        }
        
        .brand-features {
            display: none;
        }
    }
    
    @media (max-width: 575.98px) {
        .register-form-container {
            padding: 0;
        }
        
        .brand-panel {
            padding: 1.5rem;
        }
        
        .form-panel {
            padding: 1.5rem;
        }
        
        .brand-title {
            font-size: 1.6rem;
        }
        
        .brand-logo {
            width: 100px;
            height: 100px;
        }
    }
    
    .invalid-feedback {
        color: var(--arepa-danger);
        font-size: 0.875rem;
    }
    
    .form-control.is-invalid {
        border-color: var(--arepa-danger);
    }
    
    .select-custom {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23722f37' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    }
</style>
@endpush

@section('content')
<div class="register-wrapper">
    <div class="row g-0 h-100">
        <!-- Panel izquierdo - Información de la empresa -->
        <div class="col-lg-5 d-flex">
            <div class="brand-panel w-100">
                <div class="brand-content">
                    <!-- Logo -->
                    <div class="brand-logo">
                        <i class="bi bi-shop" style="font-size: 4rem; color: white;"></i>
                    </div>
                    
                    <!-- Título -->
                    <h1 class="brand-title">¡Únete a Arepa la Llanerita!</h1>
                    
                    <!-- Subtítulo -->
                    <p class="brand-subtitle">
                        Forma parte de nuestra gran familia y disfruta de los mejores sabores llaneros
                    </p>
                    
                    <!-- Lista de beneficios -->
                    <ul class="brand-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Sistema de referidos con comisiones
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Productos frescos y auténticos
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Entrega a domicilio
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Soporte personalizado
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            Precios especiales para miembros
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho - Formulario de registro -->
        <div class="col-lg-7 d-flex">
            <div class="form-panel w-100">
                <div class="register-form-container">
                    <!-- Header -->
                    <div class="register-header">
                        <h2 class="register-title">Crear Cuenta</h2>
                        <p class="register-subtitle">Completa tus datos para comenzar</p>
                    </div>
                    
                    <!-- Formulario -->
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        
                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('nombres') is-invalid @enderror" 
                                           id="nombres" 
                                           name="nombres" 
                                           value="{{ old('nombres') }}" 
                                           placeholder="Nombres"
                                           required>
                                    <label for="nombres">Nombres *</label>
                                    @error('nombres')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('apellidos') is-invalid @enderror" 
                                           id="apellidos" 
                                           name="apellidos" 
                                           value="{{ old('apellidos') }}" 
                                           placeholder="Apellidos"
                                           required>
                                    <label for="apellidos">Apellidos *</label>
                                    @error('apellidos')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="form-floating">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="nombre@ejemplo.com"
                                   required>
                            <label for="email">Correo Electrónico *</label>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono') }}" 
                                           placeholder="3001234567"
                                           required>
                                    <label for="telefono">Teléfono *</label>
                                    @error('telefono')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Documento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('documento_identidad') is-invalid @enderror" 
                                           id="documento_identidad" 
                                           name="documento_identidad" 
                                           value="{{ old('documento_identidad') }}" 
                                           placeholder="12345678"
                                           required>
                                    <label for="documento_identidad">Cédula *</label>
                                    @error('documento_identidad')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dirección -->
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="{{ old('direccion') }}" 
                                   placeholder="Calle 123 #45-67">
                            <label for="direccion">Dirección</label>
                            @error('direccion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Contraseña"
                                           required>
                                    <label for="password">Contraseña *</label>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Confirmar contraseña -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirmar Contraseña"
                                           required>
                                    <label for="password_confirmation">Confirmar Contraseña *</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Código de referido (opcional) -->
                        <div class="form-floating">
                            <input type="text" 
                                   class="form-control @error('codigo_referido') is-invalid @enderror" 
                                   id="codigo_referido" 
                                   name="codigo_referido" 
                                   value="{{ old('codigo_referido') }}" 
                                   placeholder="ARF123456">
                            <label for="codigo_referido">Código de Referido (Opcional)</label>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Si tienes un código de referido, ¡ingresalo aquí para obtener beneficios!
                            </div>
                            @error('codigo_referido')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Términos y condiciones -->
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input @error('terms') is-invalid @enderror" 
                                   id="terms" 
                                   name="terms" 
                                   required>
                            <label class="form-check-label" for="terms">
                                Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a> y la <a href="#" class="text-decoration-none">política de privacidad</a> *
                            </label>
                            @error('terms')
                                <div class="invalid-feedback">
                                    Debes aceptar los términos y condiciones
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Botón de registro -->
                        <button type="submit" class="btn btn-register">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Mi Cuenta
                        </button>
                    </form>
                    
                    <!-- Link de login -->
                    <div class="login-link">
                        <p class="mb-0">¿Ya tienes una cuenta? 
                            <a href="{{ route('login') }}">Inicia sesión aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validación en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        // Validación del formulario completo
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            // Validar que las contraseñas coincidan
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');
            
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.classList.add('is-invalid');
                showFieldError(passwordConfirm, 'Las contraseñas no coinciden');
                isValid = false;
            }
            
            // Validar términos y condiciones
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                terms.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showToast('Por favor corrige los errores en el formulario', 'error');
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Validaciones específicas
        switch(field.type) {
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Ingresa un correo electrónico válido';
                }
                break;
                
            case 'tel':
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(value.replace(/\s+/g, ''))) {
                    isValid = false;
                    errorMessage = 'Ingresa un teléfono válido (10 dígitos)';
                }
                break;
                
            case 'text':
                if (field.name === 'documento_identidad') {
                    const docRegex = /^[0-9]{6,12}$/;
                    if (!docRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Ingresa un documento válido (6-12 dígitos)';
                    }
                }
                break;
                
            case 'password':
                if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'La contraseña debe tener al menos 8 caracteres';
                }
                break;
        }
        
        // Validar campos requeridos
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Este campo es requerido';
        }
        
        // Aplicar estilos de validación
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            hideFieldError(field);
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            showFieldError(field, errorMessage);
        }
        
        return isValid;
    }
    
    function showFieldError(field, message) {
        let errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    function hideFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv && !errorDiv.hasAttribute('data-server-error')) {
            errorDiv.remove();
        }
    }
    
    // Formatear teléfono
    document.getElementById('telefono').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substr(0, 10);
        }
        e.target.value = value;
    });
    
    // Formatear documento
    document.getElementById('documento_identidad').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 12) {
            value = value.substr(0, 12);
        }
        e.target.value = value;
    });
</script>
@endpush
