<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Red de Ventas - Arepa la Llanerita. Sistema de gestión empresarial para red de ventas multinivel especializado en arepas tradicionales colombianas amazónicas.">
    <meta name="theme-color" content="#8B1538">
    <title><?php echo e(config('app.name', 'Red de Ventas - Arepa la Llanerita')); ?></title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">

    <!-- Styles -->
    <link href="<?php echo e(asset('css/welcome.css')); ?>?v=<?php echo e(filemtime(public_path('css/welcome.css'))); ?>" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="Arepa la Llanerita" class="logo">
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-menu" id="navMenu">
                <a href="#inicio" class="nav-link active">Inicio</a>
                <a href="#productos" class="nav-link">Productos</a>
                <a href="#nosotros" class="nav-link">Nosotros</a>
                <a href="#como-funciona" class="nav-link">¿Cómo funciona?</a>
                <a href="#contacto" class="nav-link">Contacto</a>

                <?php if(Route::has('login')): ?>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-primary">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Iniciar Sesión
                        </a>
                        <?php if(Route::has('register')): ?>
                            <a href="#" class="btn btn-primary" data-action="register">
                                <i class="bi bi-person-plus"></i>
                                Registrarse
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">
                        <i class="bi bi-star-fill"></i>
                        #1 en Ventas de Arepas Tradicionales
                    </span>
                    <h1 class="hero-title">
                        Únete a la Red de Ventas más Grande de
                        <span class="gradient-text">Arepas Amazónicas</span>
                    </h1>
                    <p class="hero-description">
                        Gana dinero mientras compartes la tradición culinaria colombiana.
                        Sistema multinivel profesional, comisiones atractivas y soporte completo.
                    </p>
                    <div class="hero-buttons">
                        <a href="#" class="btn btn-primary btn-lg" data-action="register">
                            <i class="bi bi-rocket-takeoff"></i>
                            Comenzar Ahora
                        </a>
                        <a href="#como-funciona" class="btn btn-outline btn-lg">
                            <i class="bi bi-play-circle"></i>
                            Ver cómo funciona
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <i class="bi bi-people-fill"></i>
                            <div>
                                <h3>500+</h3>
                                <p>Vendedores Activos</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-graph-up-arrow"></i>
                            <div>
                                <h3>10k+</h3>
                                <p>Pedidos Mensuales</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-currency-dollar"></i>
                            <div>
                                <h3>15%</h3>
                                <p>Comisión Promedio</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="image-card">
                        <img src="<?php echo e(asset('images/arepa-hero.jpg')); ?>" alt="Arepas Tradicionales" loading="lazy" onerror="this.src='<?php echo e(asset('images/logo.svg')); ?>'">
                        <div class="image-badge">
                            <i class="bi bi-award-fill"></i>
                            <span>100% Tradicional</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="section products-section" id="productos">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Nuestros Productos</span>
                <h2 class="section-title">Sabores Auténticos de la Amazonía</h2>
                <p class="section-description">
                    Cada arepa es elaborada con ingredientes 100% naturales y recetas tradicionales
                    transmitidas de generación en generación.
                </p>
            </div>

            <div class="products-grid">
                <div class="product-card" data-aos="fade-up">
                    <div class="product-icon">
                        <i class="bi bi-circle-fill"></i>
                    </div>
                    <h3>Arepa Tradicional</h3>
                    <p>La receta original que ha conquistado paladares por décadas. Perfecta para cualquier momento.</p>
                    <a href="#" class="product-link" data-action="view-product">
                        Ver detalles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="product-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="product-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <h3>Arepa Especial</h3>
                    <p>Con nuestros ingredientes secretos amazónicos que la hacen única e irresistible.</p>
                    <a href="#" class="product-link" data-action="view-product">
                        Ver detalles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="product-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="product-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h3>Arepa Gourmet</h3>
                    <p>Nuestra versión premium con ingredientes selectos para los paladares más exigentes.</p>
                    <a href="#" class="product-link" data-action="view-product">
                        Ver detalles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="product-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="product-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h3>Pack Familiar</h3>
                    <p>La mejor opción para compartir en familia. Variedad de sabores en un solo paquete.</p>
                    <a href="#" class="product-link" data-action="view-product">
                        Ver detalles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section about-section" id="nosotros">
        <div class="container">
            <div class="about-content">
                <div class="about-image" data-aos="fade-right">
                    <img src="<?php echo e(asset('images/about.jpg')); ?>" alt="Sobre Nosotros" loading="lazy" onerror="this.src='<?php echo e(asset('images/logo.svg')); ?>'">
                    <div class="about-badge">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Desde 1990</span>
                    </div>
                </div>
                <div class="about-text" data-aos="fade-left">
                    <span class="section-badge">Nuestra Historia</span>
                    <h2 class="section-title">Tradición y Calidad desde hace 30 Años</h2>
                    <p>
                        Somos una empresa familiar que ha preservado las recetas tradicionales de arepas
                        amazónicas por más de tres décadas. Nuestro compromiso es llevar el sabor auténtico
                        de la Amazonía colombiana a cada hogar.
                    </p>
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Ingredientes 100% Naturales</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Recetas Tradicionales Auténticas</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Control de Calidad Riguroso</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Producción Artesanal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section class="section how-section" id="como-funciona">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Sistema MLM</span>
                <h2 class="section-title">¿Cómo Funciona Nuestro Sistema?</h2>
                <p class="section-description">
                    Únete a nuestra red de vendedores y comienza a ganar comisiones por tus ventas
                    y las ventas de tu equipo.
                </p>
            </div>

            <div class="steps-container">
                <div class="step-item" data-aos="zoom-in">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <h3>Regístrate</h3>
                    <p>Crea tu cuenta gratis y obtén tu código de referido único en menos de 2 minutos.</p>
                </div>

                <div class="step-arrow" data-aos="fade-left" data-aos-delay="100">
                    <i class="bi bi-arrow-right"></i>
                </div>

                <div class="step-item" data-aos="zoom-in" data-aos-delay="100">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <h3>Comienza a Vender</h3>
                    <p>Comparte tus productos con amigos y familia. Sistema de pedidos fácil e intuitivo.</p>
                </div>

                <div class="step-arrow" data-aos="fade-left" data-aos-delay="200">
                    <i class="bi bi-arrow-right"></i>
                </div>

                <div class="step-item" data-aos="zoom-in" data-aos-delay="200">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>Construye tu Red</h3>
                    <p>Invita a otros vendedores usando tu código y gana comisiones por sus ventas.</p>
                </div>

                <div class="step-arrow" data-aos="fade-left" data-aos-delay="300">
                    <i class="bi bi-arrow-right"></i>
                </div>

                <div class="step-item" data-aos="zoom-in" data-aos-delay="300">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h3>Gana Comisiones</h3>
                    <p>Recibe hasta 15% de comisión y bonos por alcanzar metas. ¡Sin límites!</p>
                </div>
            </div>

            <div class="commission-info" data-aos="fade-up">
                <h3><i class="bi bi-trophy-fill"></i> Plan de Comisiones</h3>
                <div class="commission-grid">
                    <div class="commission-card">
                        <h4>Vendedor</h4>
                        <div class="commission-value">10%</div>
                        <p>Por tus ventas directas</p>
                    </div>
                    <div class="commission-card">
                        <h4>Líder</h4>
                        <div class="commission-value">5%</div>
                        <p>Por ventas de tu equipo</p>
                    </div>
                    <div class="commission-card highlight">
                        <h4>Bono Referido</h4>
                        <div class="commission-value">50k VES</div>
                        <p>Por cada nuevo vendedor</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials-section" id="testimonios">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Testimonios</span>
                <h2 class="section-title">Lo que Dicen Nuestros Vendedores</h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card" data-aos="fade-up">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <h4>María González</h4>
                            <p>Vendedora Líder</p>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">
                        "En 6 meses logré construir un equipo de 20 vendedores. Las comisiones son excelentes
                        y el sistema es muy fácil de usar. ¡Totalmente recomendado!"
                    </p>
                </div>

                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <h4>Carlos Ramírez</h4>
                            <p>Vendedor Destacado</p>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">
                        "Los productos son de excelente calidad y mis clientes siempre quedan satisfechos.
                        El sistema de referidos me ha permitido generar ingresos pasivos."
                    </p>
                </div>

                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <h4>Ana Martínez</h4>
                            <p>Nueva Vendedora</p>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">
                        "Empecé hace 2 meses y ya estoy viendo resultados. El soporte del equipo es increíble
                        y me ayudaron en cada paso del proceso."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section" id="contacto">
        <div class="container">
            <div class="cta-content" data-aos="zoom-in">
                <h2>¿Listo para Comenzar tu Negocio?</h2>
                <p>Únete a cientos de vendedores exitosos y comienza a generar ingresos hoy mismo.</p>
                <div class="cta-buttons">
                    <a href="#" class="btn btn-white btn-lg" data-action="register">
                        <i class="bi bi-rocket-takeoff"></i>
                        Registrarse Gratis
                    </a>
                    <a href="#" class="btn btn-outline-white btn-lg" data-action="contact">
                        <i class="bi bi-chat-dots"></i>
                        Contáctanos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="Arepa la Llanerita" class="footer-logo">
                    <p>Tradición culinaria colombiana amazónica desde 1990.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Enlaces Rápidos</h4>
                    <ul>
                        <li><a href="#productos">Productos</a></li>
                        <li><a href="#nosotros">Nosotros</a></li>
                        <li><a href="#como-funciona">Cómo Funciona</a></li>
                        <li><a href="<?php echo e(route('login')); ?>">Iniciar Sesión</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Soporte</h4>
                    <ul>
                        <li><a href="#" data-action="help">Centro de Ayuda</a></li>
                        <li><a href="#" data-action="terms">Términos y Condiciones</a></li>
                        <li><a href="#" data-action="privacy">Política de Privacidad</a></li>
                        <li><a href="#" data-action="contact">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Contacto</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Colombia, Amazonas</span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span>+57 315 431 1266</span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span>info@arepallanerita.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo e(date('Y')); ?> Arepa la Llanerita. Todos los derechos reservados.</p>
                <p class="dev-credit">Desarrollado con ❤️ por Luis Alberto Urrea Trujillo</p>
            </div>
        </div>
    </footer>

    <!-- Registration Modal -->
    <div class="modal" id="registerModal">
        <div class="modal-overlay" data-close-modal></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3>
                    <i class="bi bi-person-plus-fill"></i>
                    Registro de Nuevo Vendedor
                </h3>
                <button class="modal-close" data-close-modal aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-description">
                    ¿Estás listo para comenzar tu negocio con nosotros? Te redirigiremos a la página
                    de registro donde podrás crear tu cuenta en menos de 2 minutos.
                </p>
                <div class="modal-features">
                    <div class="modal-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Registro 100% Gratuito</span>
                    </div>
                    <div class="modal-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Código de Referido Único</span>
                    </div>
                    <div class="modal-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Acceso Inmediato al Sistema</span>
                    </div>
                    <div class="modal-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Soporte 24/7</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" data-close-modal>Cancelar</button>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-primary">
                    <i class="bi bi-rocket-takeoff"></i>
                    Ir al Registro
                </a>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-overlay" data-close-modal></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3>
                    <i class="bi bi-info-circle-fill"></i>
                    Información del Producto
                </h3>
                <button class="modal-close" data-close-modal aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-description">
                    Para ver el catálogo completo de productos y realizar pedidos, necesitas iniciar
                    sesión como vendedor o cliente registrado.
                </p>
                <div class="modal-info">
                    <i class="bi bi-lightbulb-fill"></i>
                    <p>
                        Si aún no tienes cuenta, puedes registrarte como vendedor y obtener acceso
                        completo al catálogo con precios preferenciales.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" data-close-modal>Cerrar</button>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal" id="contactModal">
        <div class="modal-overlay" data-close-modal></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3>
                    <i class="bi bi-chat-dots-fill"></i>
                    Contáctanos
                </h3>
                <button class="modal-close" data-close-modal aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-description">
                    Estamos aquí para ayudarte. Elige tu medio de contacto preferido:
                </p>
                <div class="contact-options">
                    <a href="https://wa.me/573154311266" target="_blank" class="contact-option">
                        <i class="bi bi-whatsapp"></i>
                        <div>
                            <h4>WhatsApp</h4>
                            <p>+57 315 431 1266</p>
                        </div>
                    </a>
                    <a href="tel:+573154311266" class="contact-option">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <h4>Teléfono</h4>
                            <p>+57 315 431 1266</p>
                        </div>
                    </a>
                    <a href="mailto:info@arepallanerita.com" class="contact-option">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@arepallanerita.com</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-close-modal>Cerrar</button>
            </div>
        </div>
    </div>

    <!-- General Info Modal -->
    <div class="modal" id="infoModal">
        <div class="modal-overlay" data-close-modal></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3 id="infoModalTitle">
                    <i class="bi bi-info-circle-fill"></i>
                    Información
                </h3>
                <button class="modal-close" data-close-modal aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-description" id="infoModalContent">
                    Esta sección estará disponible próximamente.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-close-modal>Entendido</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('js/welcome.js')); ?>?v=<?php echo e(filemtime(public_path('js/welcome.js'))); ?>"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/welcome.blade.php ENDPATH**/ ?>