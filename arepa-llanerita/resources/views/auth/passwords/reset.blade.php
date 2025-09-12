@extends('layouts.app')

@section('title', '- Restablecer Contraseña')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/reset-password.css') }}">
@endpush

@section('content')
<div class="reset-container">
    <div class="reset-main-card">
        <!-- Header Elegante -->
        <div class="reset-header">
            <div class="reset-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1 class="reset-title">Nueva Contraseña</h1>
            <p class="reset-subtitle">
                Crea una contraseña segura para proteger tu cuenta en Arepa la Llanerita
            </p>
        </div>

        <!-- Formulario Moderno -->
        <form method="POST" action="{{ route('password.update') }}" class="reset-form" id="resetForm">
            @csrf
            
            <!-- Token Oculto -->
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email Field -->
            <div class="form-group-modern">
                <input 
                    id="email" 
                    type="email" 
                    class="form-input-modern @error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ $email ?? old('email') }}" 
                    required 
                    autocomplete="email" 
                    readonly
                    placeholder=" ">
                <label for="email" class="form-label-modern">
                    <i class="bi bi-envelope me-2"></i>Correo Electrónico
                </label>
                @error('email')
                    <div class="error-message">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <!-- Password Field -->
            <div class="form-group-modern">
                <input 
                    id="password" 
                    type="password" 
                    class="form-input-modern @error('password') is-invalid @enderror" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder=" ">
                <label for="password" class="form-label-modern">
                    <i class="bi bi-lock me-2"></i>Nueva Contraseña
                </label>
                @error('password')
                    <div class="error-message">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
                
                <!-- Password Strength Indicator -->
                <div class="password-strength-container" id="strengthContainer" style="display: none;">
                    <div class="password-strength-title">
                        <i class="bi bi-shield-check"></i>
                        Fortaleza de la contraseña
                    </div>
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="strengthMeter"></div>
                    </div>
                    <ul class="password-requirements">
                        <li class="requirement-item" id="length-req">
                            <i class="bi bi-x"></i>
                            <span>Mínimo 8 caracteres</span>
                        </li>
                        <li class="requirement-item" id="lowercase-req">
                            <i class="bi bi-x"></i>
                            <span>Una minúscula</span>
                        </li>
                        <li class="requirement-item" id="uppercase-req">
                            <i class="bi bi-x"></i>
                            <span>Una mayúscula</span>
                        </li>
                        <li class="requirement-item" id="number-req">
                            <i class="bi bi-x"></i>
                            <span>Un número</span>
                        </li>
                        <li class="requirement-item" id="special-req">
                            <i class="bi bi-x"></i>
                            <span>Un carácter especial</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Confirm Password Field -->
            <div class="form-group-modern">
                <input 
                    id="password-confirm" 
                    type="password" 
                    class="form-input-modern" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder=" ">
                <label for="password-confirm" class="form-label-modern">
                    <i class="bi bi-lock-fill me-2"></i>Confirmar Nueva Contraseña
                </label>
                <div class="error-message" id="password-match-error" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i>
                    Las contraseñas no coinciden
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn-reset-modern" id="submitBtn" disabled>
                <i class="bi bi-shield-check me-2"></i>
                Restablecer Contraseña
            </button>
        </form>

        <!-- Back Link -->
        <div class="back-link-modern">
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left"></i>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password-confirm');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('resetForm');
        const strengthContainer = document.getElementById('strengthContainer');
        const strengthMeter = document.getElementById('strengthMeter');
        
        // Requisitos de contraseña
        const requirements = {
            length: { element: document.getElementById('length-req'), regex: /.{8,}/, weight: 20 },
            lowercase: { element: document.getElementById('lowercase-req'), regex: /[a-z]/, weight: 20 },
            uppercase: { element: document.getElementById('uppercase-req'), regex: /[A-Z]/, weight: 20 },
            number: { element: document.getElementById('number-req'), regex: /[0-9]/, weight: 20 },
            special: { element: document.getElementById('special-req'), regex: /[^A-Za-z0-9]/, weight: 20 }
        };
        
        // Mostrar/ocultar indicador de fortaleza
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length > 0) {
                strengthContainer.style.display = 'block';
                validatePassword(password);
            } else {
                strengthContainer.style.display = 'none';
                resetRequirements();
            }
            
            updateSubmitButton();
        });
        
        // Validar coincidencia de contraseñas
        confirmInput.addEventListener('input', function() {
            validatePasswordMatch();
            updateSubmitButton();
        });
        
        function validatePassword(password) {
            let strengthScore = 0;
            let allValid = true;
            
            // Verificar cada requisito
            for (let [key, req] of Object.entries(requirements)) {
                const isValid = req.regex.test(password);
                
                if (isValid) {
                    req.element.classList.add('valid');
                    req.element.querySelector('i').className = 'bi bi-check';
                    strengthScore += req.weight;
                } else {
                    req.element.classList.remove('valid');
                    req.element.querySelector('i').className = 'bi bi-x';
                    allValid = false;
                }
            }
            
            // Actualizar barra de fortaleza
            strengthMeter.style.width = strengthScore + '%';
            
            return allValid;
        }
        
        function resetRequirements() {
            for (let req of Object.values(requirements)) {
                req.element.classList.remove('valid');
                req.element.querySelector('i').className = 'bi bi-x';
            }
            strengthMeter.style.width = '0%';
        }
        
        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            const errorDiv = document.getElementById('password-match-error');
            
            if (confirm && password !== confirm) {
                confirmInput.classList.add('is-invalid');
                confirmInput.classList.remove('is-valid');
                errorDiv.style.display = 'flex';
                return false;
            } else if (confirm && password === confirm) {
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
                errorDiv.style.display = 'none';
                return true;
            } else {
                confirmInput.classList.remove('is-invalid', 'is-valid');
                errorDiv.style.display = 'none';
                return false;
            }
        }
        
        function updateSubmitButton() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            // Verificar que todos los requisitos se cumplan
            const allRequirementsMet = validatePassword(password);
            
            // Verificar que las contraseñas coincidan
            const passwordsMatch = password && confirm && password === confirm;
            
            // Habilitar botón si todo está correcto
            if (allRequirementsMet && passwordsMatch) {
                submitBtn.disabled = false;
                passwordInput.classList.add('is-valid');
            } else {
                submitBtn.disabled = true;
                passwordInput.classList.remove('is-valid');
            }
        }
        
        // Efectos de focus mejorados
        const inputs = document.querySelectorAll('.form-input-modern');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
        
        // Validación del formulario
        form.addEventListener('submit', function(e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                showToast && showToast('Por favor completa todos los requisitos de la contraseña', 'error');
                return;
            }
            
            showLoading && showLoading();
        });
        
        // Ocultar loading si hay errores
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                hideLoading && hideLoading();
            });
        @endif
        
        // Animación de entrada escalonada
        const formGroups = document.querySelectorAll('.form-group-modern');
        formGroups.forEach((group, index) => {
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                group.style.transition = 'all 0.4s ease';
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, (index + 1) * 100);
        });
        
        // Animación del botón
        setTimeout(() => {
            submitBtn.style.opacity = '0';
            submitBtn.style.transform = 'translateY(20px)';
            submitBtn.style.transition = 'all 0.4s ease';
            
            setTimeout(() => {
                submitBtn.style.opacity = '1';
                submitBtn.style.transform = 'translateY(0)';
            }, 50);
        }, (formGroups.length + 1) * 100);
        
        // Efectos de hover en tiempo real
        submitBtn.addEventListener('mouseenter', function() {
            if (!this.disabled) {
                this.style.transform = 'translateY(-3px) scale(1.02)';
            }
        });
        
        submitBtn.addEventListener('mouseleave', function() {
            if (!this.disabled) {
                this.style.transform = 'translateY(0) scale(1)';
            }
        });
    });
</script>
@endpush