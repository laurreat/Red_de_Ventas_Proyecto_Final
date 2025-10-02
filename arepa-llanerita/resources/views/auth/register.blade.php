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
                        <i class="bi bi-shop fs-1" style="color: white;"></i>
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
                            <i class="bi bi-people-fill"></i>
                            Sistema de referidos con comisiones
                        </li>
                        <li>
                            <i class="bi bi-basket-fill"></i>
                            Productos frescos y auténticos
                        </li>
                        <li>
                            <i class="bi bi-truck"></i>
                            Entrega a domicilio
                        </li>
                        <li>
                            <i class="bi bi-headset"></i>
                            Soporte personalizado
                        </li>
                        <li>
                            <i class="bi bi-star-fill"></i>
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
                    <form method="POST" action="{{ route('register') }}" novalidate
                          class="needs-register-confirmation"
                          data-confirm-message="¿Estás seguro de crear tu cuenta? Se te enviará un email de verificación."
                          id="registerForm">
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
                                <div class="form-floating position-relative">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Contraseña"
                                           required>
                                    <label for="password">Contraseña *</label>
                                    <button type="button" class="btn password-toggle" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Confirmar contraseña -->
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirmar Contraseña"
                                           required>
                                    <label for="password_confirmation">Confirmar Contraseña *</label>
                                    <button type="button" class="btn password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
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
                                Acepto los <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#terminosModal">términos y condiciones</a> y la <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacidadModal">política de privacidad</a> *
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
<script src="{{ asset('js/auth/register.js') }}"></script>
@endpush

{{-- Incluir modales de confirmación para registro --}}
@include('admin.partials.modals-users')

{{-- Modal de Términos y Condiciones --}}
<div class="modal fade" id="terminosModal" tabindex="-1" aria-labelledby="terminosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="terminosModalLabel">
                    <i class="bi bi-file-text me-2"></i>
                    Términos y Condiciones de Uso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="terms-content">
                    <p class="text-muted mb-4">
                        <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                    </p>

                    <h6 class="fw-bold mt-4">1. Aceptación de los Términos</h6>
                    <p>
                        Al registrarte en Arepa la Llanerita, aceptas cumplir con estos términos y condiciones.
                        Si no estás de acuerdo con alguno de estos términos, por favor no utilices nuestros servicios.
                    </p>

                    <h6 class="fw-bold mt-4">2. Descripción del Servicio</h6>
                    <p>
                        Arepa la Llanerita es una plataforma de comercialización de productos alimenticios tradicionales
                        colombianos que incluye:
                    </p>
                    <ul>
                        <li>Sistema de compra y venta de productos</li>
                        <li>Red de referidos multinivel (MLM)</li>
                        <li>Sistema de comisiones por ventas</li>
                        <li>Servicios de entrega a domicilio</li>
                    </ul>

                    <h6 class="fw-bold mt-4">3. Registro de Usuarios</h6>
                    <p>Para utilizar nuestros servicios, debes:</p>
                    <ul>
                        <li>Ser mayor de 18 años</li>
                        <li>Proporcionar información veraz y actualizada</li>
                        <li>Mantener la confidencialidad de tu contraseña</li>
                        <li>Notificar inmediatamente cualquier uso no autorizado de tu cuenta</li>
                    </ul>

                    <h6 class="fw-bold mt-4">4. Sistema de Referidos y Comisiones</h6>
                    <p>
                        Nuestro sistema de referidos funciona bajo las siguientes condiciones:
                    </p>
                    <ul>
                        <li>Las comisiones se calculan según las ventas realizadas</li>
                        <li>Los pagos se realizan de acuerdo a los períodos establecidos</li>
                        <li>No garantizamos ganancias específicas</li>
                        <li>El éxito depende del esfuerzo individual de cada usuario</li>
                        <li>Nos reservamos el derecho de modificar las tasas de comisión con previo aviso</li>
                    </ul>

                    <h6 class="fw-bold mt-4">5. Responsabilidades del Usuario</h6>
                    <p>Como usuario, te comprometes a:</p>
                    <ul>
                        <li>Usar la plataforma de manera legal y ética</li>
                        <li>No realizar actividades fraudulentas o engañosas</li>
                        <li>No crear múltiples cuentas falsas</li>
                        <li>Respetar los derechos de propiedad intelectual</li>
                        <li>No compartir contenido ofensivo o inapropiado</li>
                    </ul>

                    <h6 class="fw-bold mt-4">6. Productos y Precios</h6>
                    <p>
                        Los precios de nuestros productos están sujetos a cambios sin previo aviso.
                        Nos esforzamos por mantener la información de productos actualizada, pero no garantizamos
                        la disponibilidad constante de todos los productos.
                    </p>

                    <h6 class="fw-bold mt-4">7. Pedidos y Entregas</h6>
                    <ul>
                        <li>Los pedidos están sujetos a disponibilidad de stock</li>
                        <li>Los tiempos de entrega son estimados y pueden variar</li>
                        <li>No nos hacemos responsables por demoras causadas por factores externos</li>
                        <li>El cliente debe verificar el pedido al recibirlo</li>
                    </ul>

                    <h6 class="fw-bold mt-4">8. Política de Devoluciones</h6>
                    <p>
                        Por tratarse de productos alimenticios, solo aceptamos devoluciones en caso de:
                    </p>
                    <ul>
                        <li>Productos defectuosos o en mal estado</li>
                        <li>Error en el pedido por parte de la empresa</li>
                        <li>Productos que no cumplan con los estándares de calidad</li>
                    </ul>
                    <p class="text-muted">
                        Las devoluciones deben reportarse dentro de las 24 horas posteriores a la entrega.
                    </p>

                    <h6 class="fw-bold mt-4">9. Propiedad Intelectual</h6>
                    <p>
                        Todos los contenidos de la plataforma (textos, imágenes, logos, diseños) son propiedad
                        de Arepa la Llanerita y están protegidos por las leyes de propiedad intelectual.
                    </p>

                    <h6 class="fw-bold mt-4">10. Limitación de Responsabilidad</h6>
                    <p>
                        Arepa la Llanerita no será responsable por:
                    </p>
                    <ul>
                        <li>Daños indirectos o consecuentes del uso de la plataforma</li>
                        <li>Pérdida de ganancias o datos</li>
                        <li>Interrupciones del servicio por mantenimiento o causas técnicas</li>
                        <li>Acciones de terceros</li>
                    </ul>

                    <h6 class="fw-bold mt-4">11. Modificaciones del Servicio</h6>
                    <p>
                        Nos reservamos el derecho de modificar, suspender o descontinuar cualquier aspecto
                        de nuestros servicios en cualquier momento, con o sin previo aviso.
                    </p>

                    <h6 class="fw-bold mt-4">12. Suspensión y Terminación de Cuentas</h6>
                    <p>
                        Podemos suspender o cancelar tu cuenta si:
                    </p>
                    <ul>
                        <li>Violas estos términos y condiciones</li>
                        <li>Realizas actividades fraudulentas</li>
                        <li>Proporcionas información falsa</li>
                        <li>Abusas del sistema de referidos</li>
                    </ul>

                    <h6 class="fw-bold mt-4">13. Ley Aplicable</h6>
                    <p>
                        Estos términos se rigen por las leyes de la República de Colombia.
                        Cualquier disputa se resolverá en los tribunales competentes de Villavicencio, Meta.
                    </p>

                    <h6 class="fw-bold mt-4">14. Contacto</h6>
                    <p>
                        Para cualquier consulta sobre estos términos, puedes contactarnos:
                    </p>
                    <ul>
                        <li><strong>Email:</strong> contacto@arepallanerita.com</li>
                        <li><strong>Teléfono:</strong> +57 315 431 1266</li>
                        <li><strong>Dirección:</strong> Villavicencio, Meta, Colombia</li>
                    </ul>

                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Importante:</strong> Al marcar la casilla de aceptación, confirmas que has leído,
                        entendido y aceptado todos estos términos y condiciones.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="aceptarTerminos()">
                    <i class="bi bi-check-circle me-2"></i>
                    Acepto los Términos
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Política de Privacidad --}}
<div class="modal fade" id="privacidadModal" tabindex="-1" aria-labelledby="privacidadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="privacidadModalLabel">
                    <i class="bi bi-shield-check me-2"></i>
                    Política de Privacidad y Protección de Datos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="privacy-content">
                    <p class="text-muted mb-4">
                        <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                    </p>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Compromiso con tu Privacidad:</strong> En Arepa la Llanerita, la protección
                        de tus datos personales es fundamental. Esta política explica cómo recopilamos,
                        usamos y protegemos tu información.
                    </div>

                    <h6 class="fw-bold mt-4">1. Responsable del Tratamiento de Datos</h6>
                    <p>
                        <strong>Razón Social:</strong> Arepa la Llanerita<br>
                        <strong>Domicilio:</strong> Villavicencio, Meta, Colombia<br>
                        <strong>Email:</strong> privacidad@arepallanerita.com<br>
                        <strong>Teléfono:</strong> +57 315 431 1266
                    </p>

                    <h6 class="fw-bold mt-4">2. Datos Personales que Recopilamos</h6>
                    <p>Recopilamos los siguientes tipos de información:</p>

                    <h6 class="fw-semibold mt-3">2.1 Información de Registro</h6>
                    <ul>
                        <li>Nombres y apellidos</li>
                        <li>Correo electrónico</li>
                        <li>Número de teléfono</li>
                        <li>Número de cédula</li>
                        <li>Dirección completa</li>
                        <li>Ciudad y departamento</li>
                        <li>Fecha de nacimiento</li>
                    </ul>

                    <h6 class="fw-semibold mt-3">2.2 Información de Uso</h6>
                    <ul>
                        <li>Historial de pedidos</li>
                        <li>Preferencias de productos</li>
                        <li>Información de pago (encriptada)</li>
                        <li>Datos de navegación</li>
                        <li>Dirección IP</li>
                        <li>Tipo de dispositivo y navegador</li>
                    </ul>

                    <h6 class="fw-semibold mt-3">2.3 Información del Sistema de Referidos</h6>
                    <ul>
                        <li>Código de referido</li>
                        <li>Red de referidos</li>
                        <li>Historial de comisiones</li>
                        <li>Datos bancarios para pagos (encriptados)</li>
                    </ul>

                    <h6 class="fw-bold mt-4">3. Finalidad del Tratamiento de Datos</h6>
                    <p>Utilizamos tus datos personales para:</p>
                    <ul>
                        <li>Gestionar tu registro y cuenta de usuario</li>
                        <li>Procesar y entregar tus pedidos</li>
                        <li>Administrar el sistema de referidos y comisiones</li>
                        <li>Enviarte comunicaciones sobre tu cuenta y pedidos</li>
                        <li>Mejorar nuestros productos y servicios</li>
                        <li>Cumplir con obligaciones legales y fiscales</li>
                        <li>Prevenir fraudes y garantizar la seguridad</li>
                        <li>Enviar promociones y ofertas (con tu consentimiento)</li>
                    </ul>

                    <h6 class="fw-bold mt-4">4. Base Legal del Tratamiento</h6>
                    <p>Tratamos tus datos personales con base en:</p>
                    <ul>
                        <li><strong>Consentimiento:</strong> Al aceptar estos términos</li>
                        <li><strong>Ejecución del contrato:</strong> Para procesar tus pedidos</li>
                        <li><strong>Obligación legal:</strong> Para cumplir con la ley colombiana</li>
                        <li><strong>Interés legítimo:</strong> Para mejorar nuestros servicios</li>
                    </ul>

                    <h6 class="fw-bold mt-4">5. Compartir Información con Terceros</h6>
                    <p>Podemos compartir tu información con:</p>
                    <ul>
                        <li><strong>Proveedores de servicios:</strong> Empresas de entrega, procesadores de pago</li>
                        <li><strong>Autoridades:</strong> Cuando sea requerido por ley</li>
                        <li><strong>Socios comerciales:</strong> Solo con tu consentimiento explícito</li>
                    </ul>
                    <p class="text-danger">
                        <strong>Nunca vendemos tus datos personales a terceros.</strong>
                    </p>

                    <h6 class="fw-bold mt-4">6. Seguridad de los Datos</h6>
                    <p>Implementamos medidas de seguridad técnicas y organizativas:</p>
                    <ul>
                        <li>Encriptación SSL/TLS para transmisión de datos</li>
                        <li>Encriptación de contraseñas con algoritmos seguros</li>
                        <li>Acceso restringido a datos personales</li>
                        <li>Monitoreo constante de seguridad</li>
                        <li>Backups regulares de información</li>
                        <li>Auditorías de seguridad periódicas</li>
                    </ul>

                    <h6 class="fw-bold mt-4">7. Tus Derechos (Ley 1581 de 2012 - Habeas Data)</h6>
                    <p>Como titular de datos personales, tienes derecho a:</p>
                    <ul>
                        <li><strong>Acceso:</strong> Consultar tus datos personales</li>
                        <li><strong>Rectificación:</strong> Corregir datos inexactos o incompletos</li>
                        <li><strong>Actualización:</strong> Actualizar tu información</li>
                        <li><strong>Supresión:</strong> Solicitar la eliminación de tus datos</li>
                        <li><strong>Revocación:</strong> Retirar el consentimiento otorgado</li>
                        <li><strong>Oposición:</strong> Oponerte a ciertos usos de tus datos</li>
                        <li><strong>Portabilidad:</strong> Obtener una copia de tus datos</li>
                    </ul>

                    <h6 class="fw-bold mt-4">8. Cómo Ejercer tus Derechos</h6>
                    <p>Para ejercer cualquiera de tus derechos, puedes:</p>
                    <ul>
                        <li>Enviar un correo a: <strong>privacidad@arepallanerita.com</strong></li>
                        <li>Llamar al: <strong>+57 315 431 1266</strong></li>
                        <li>Acceder a tu perfil y usar la opción "Descargar mis datos"</li>
                    </ul>
                    <p>Responderemos a tu solicitud en un plazo máximo de 15 días hábiles.</p>

                    <h6 class="fw-bold mt-4">9. Conservación de Datos</h6>
                    <p>Conservamos tus datos personales:</p>
                    <ul>
                        <li>Mientras tu cuenta esté activa</li>
                        <li>Durante el tiempo necesario para cumplir con obligaciones legales</li>
                        <li>5 años después del cierre de cuenta (por obligaciones fiscales)</li>
                    </ul>

                    <h6 class="fw-bold mt-4">10. Cookies y Tecnologías Similares</h6>
                    <p>Utilizamos cookies para:</p>
                    <ul>
                        <li>Mantener tu sesión activa</li>
                        <li>Recordar tus preferencias</li>
                        <li>Analizar el uso de la plataforma</li>
                        <li>Mejorar la experiencia de usuario</li>
                    </ul>
                    <p>Puedes configurar tu navegador para rechazar cookies, aunque esto puede afectar la funcionalidad.</p>

                    <h6 class="fw-bold mt-4">11. Transferencias Internacionales</h6>
                    <p>
                        Tus datos se almacenan en servidores ubicados en Colombia. En caso de requerir
                        transferencias internacionales, garantizamos un nivel de protección adecuado.
                    </p>

                    <h6 class="fw-bold mt-4">12. Menores de Edad</h6>
                    <p>
                        Nuestros servicios están dirigidos a mayores de 18 años. No recopilamos
                        intencionalmente información de menores de edad.
                    </p>

                    <h6 class="fw-bold mt-4">13. Cambios en la Política de Privacidad</h6>
                    <p>
                        Podemos actualizar esta política periódicamente. Te notificaremos sobre cambios
                        significativos por correo electrónico o mediante aviso en la plataforma.
                    </p>

                    <h6 class="fw-bold mt-4">14. Autoridad de Protección de Datos</h6>
                    <p>
                        Si consideras que tus derechos han sido vulnerados, puedes presentar una queja ante:
                    </p>
                    <p>
                        <strong>Superintendencia de Industria y Comercio (SIC)</strong><br>
                        Delegatura para la Protección de Datos Personales<br>
                        Website: www.sic.gov.co
                    </p>

                    <h6 class="fw-bold mt-4">15. Consentimiento Informado</h6>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Al aceptar esta política, confirmas que:
                        <ul class="mb-0 mt-2">
                            <li>Has leído y comprendido esta política de privacidad</li>
                            <li>Autorizas el tratamiento de tus datos según lo establecido</li>
                            <li>Conoces tus derechos y cómo ejercerlos</li>
                            <li>Comprendes que puedes revocar tu consentimiento en cualquier momento</li>
                        </ul>
                    </div>

                    <h6 class="fw-bold mt-4">16. Contacto para Privacidad</h6>
                    <p>
                        Para consultas específicas sobre privacidad y protección de datos:
                    </p>
                    <ul>
                        <li><strong>Email:</strong> privacidad@arepallanerita.com</li>
                        <li><strong>Teléfono:</strong> +57 315 431 1266</li>
                        <li><strong>Horario:</strong> Lunes a Viernes, 8:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="aceptarPrivacidad()">
                    <i class="bi bi-shield-check me-2"></i>
                    Acepto la Política
                </button>
            </div>
        </div>
    </div>
</div>
