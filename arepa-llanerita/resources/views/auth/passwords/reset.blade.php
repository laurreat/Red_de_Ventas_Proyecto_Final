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
<script src="{{ asset('js/auth/reset-password.js') }}"></script>
@endpush