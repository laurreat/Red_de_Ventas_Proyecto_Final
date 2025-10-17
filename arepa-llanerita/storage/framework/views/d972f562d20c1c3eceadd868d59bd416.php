<?php $__env->startSection('title', '- Iniciar Sesión'); ?>

<?php $__env->startPush('styles'); ?>
<!-- Preload critical resources -->
<link rel="preload" href="<?php echo e(asset('css/pages/login.css')); ?>?v=<?php echo e(filemtime(public_path('css/pages/login.css'))); ?>" as="style">
<!-- Login styles with automatic cache busting -->
<link rel="stylesheet" href="<?php echo e(asset('css/pages/login.css')); ?>?v=<?php echo e(filemtime(public_path('css/pages/login.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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
                <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm">
                    <?php echo csrf_field(); ?>

                    <!-- Campo Email -->
                    <div class="form-group">
                        <div class="form-floating">
                            <input id="email" type="email"
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   name="email" value="<?php echo e(old('email')); ?>"
                                   required autocomplete="email" autofocus
                                   placeholder="correo@ejemplo.com">
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>Correo Electrónico
                            </label>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <strong><?php echo e($message); ?></strong>
                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="form-group">
                        <div class="form-floating position-relative">
                            <input id="password" type="password"
                                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   name="password" required autocomplete="current-password"
                                   placeholder="Contraseña">
                            <label for="password">
                                <i class="bi bi-lock me-2"></i>Contraseña
                            </label>
                            <button type="button" class="btn password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <strong><?php echo e($message); ?></strong>
                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Recordar Sesión -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="remember" id="remember"
                               <?php echo e(old('remember') ? 'checked' : ''); ?>>
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
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>">
                                <i class="bi bi-key me-1"></i>¿Olvidaste tu contraseña?
                            </a>
                        <?php endif; ?>

                        <?php if(Route::has('register')): ?>
                            <div class="mt-2">
                                <span class="text-muted">¿No tienes cuenta?</span>
                                <a href="<?php echo e(route('register')); ?>" class="fw-bold ms-1">
                                    Regístrate aquí
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Configurar variables antes de cargar el módulo
    window.setLoginErrors && window.setLoginErrors(<?php echo json_encode($errors->any(), 15, 512) ?>);
</script>
<script src="<?php echo e(asset('js/auth/login.js')); ?>?v=<?php echo e(filemtime(public_path('js/auth/login.js'))); ?>" defer></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/auth/login.blade.php ENDPATH**/ ?>