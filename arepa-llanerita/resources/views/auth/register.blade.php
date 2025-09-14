@extends('layouts.app')

@section('title', '- Registro')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/register.css') }}">
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
                <!-- Header -->
                <div class="register-header">
                    <h2 class="register-title">Crear Cuenta</h2>
                    <p class="register-subtitle">Completa tus datos para comenzar</p>
                </div>

                <div class="register-form-container">
                    <!-- Formulario -->
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        
                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Nombres"
                                           required>
                                    <label for="name">Nombres *</label>
                                    @error('name')
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
                                           class="form-control @error('cedula') is-invalid @enderror" 
                                           id="cedula" 
                                           name="cedula" 
                                           value="{{ old('cedula') }}" 
                                           placeholder="12345678"
                                           required>
                                    <label for="cedula">Cédula *</label>
                                    @error('cedula')
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
                            <!-- Ciudad -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control @error('ciudad') is-invalid @enderror" 
                                           id="ciudad" 
                                           name="ciudad" 
                                           value="{{ old('ciudad') }}" 
                                           placeholder="Villavicencio"
                                           required>
                                    <label for="ciudad">Ciudad *</label>
                                    @error('ciudad')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Departamento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-control @error('departamento') is-invalid @enderror" 
                                            id="departamento" 
                                            name="departamento" 
                                            required>
                                        <option value="">Selecciona departamento</option>
                                        <option value="Meta" {{ old('departamento') == 'Meta' ? 'selected' : '' }}>Meta</option>
                                        <option value="Cundinamarca" {{ old('departamento') == 'Cundinamarca' ? 'selected' : '' }}>Cundinamarca</option>
                                        <option value="Boyacá" {{ old('departamento') == 'Boyacá' ? 'selected' : '' }}>Boyacá</option>
                                        <option value="Casanare" {{ old('departamento') == 'Casanare' ? 'selected' : '' }}>Casanare</option>
                                        <option value="Arauca" {{ old('departamento') == 'Arauca' ? 'selected' : '' }}>Arauca</option>
                                        <option value="Otro" {{ old('departamento') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    <label for="departamento">Departamento *</label>
                                    @error('departamento')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fecha de nacimiento -->
                        <div class="form-floating">
                            <input type="date" 
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                   id="fecha_nacimiento" 
                                   name="fecha_nacimiento" 
                                   value="{{ old('fecha_nacimiento') }}"
                                   max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                   required>
                            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Debes ser mayor de 18 años para registrarte
                            </div>
                            @error('fecha_nacimiento')
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
                                   class="form-control @error('codigo_referido_usado') is-invalid @enderror" 
                                   id="codigo_referido_usado" 
                                   name="codigo_referido_usado" 
                                   value="{{ old('codigo_referido_usado') }}" 
                                   placeholder="REF1234">
                            <label for="codigo_referido_usado">Código de Referido (Opcional)</label>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Si tienes un código de referido, ¡ingresalo aquí para obtener beneficios!
                            </div>
                            @error('codigo_referido_usado')
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
                </div>

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
                if (field.name === 'cedula') {
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
    document.getElementById('cedula').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 12) {
            value = value.substr(0, 12);
        }
        e.target.value = value;
    });
</script>
@endpush
