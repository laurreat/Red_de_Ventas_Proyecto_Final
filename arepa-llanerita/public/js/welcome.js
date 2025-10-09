/**
 * ============================================
 * WELCOME PAGE JAVASCRIPT - AREPA LA LLANERITA
 * Sistema de interacciones y optimizaciones
 * ============================================
 */

(function() {
    'use strict';

    // ============================================
    // CONSTANTES Y CONFIGURACIÓN
    // ============================================
    const CONFIG = {
        navbarScrollThreshold: 50,
        observerThreshold: 0.1,
        lazyLoadMargin: '50px',
        smoothScrollDuration: 800
    };

    // ============================================
    // INICIALIZACIÓN AL CARGAR DOM
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initNavbar();
        initModals();
        initSmoothScroll();
        initAnimations();
        initLazyLoading();
        initActiveLinks();
    });

    // ============================================
    // NAVBAR
    // ============================================
    function initNavbar() {
        const navbar = document.querySelector('.navbar');
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');
        const navLinks = document.querySelectorAll('.nav-link');

        // Scroll effect
        let lastScroll = 0;
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            // Add scrolled class
            if (currentScroll > CONFIG.navbarScrollThreshold) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        }, { passive: true });

        // Mobile toggle
        if (navToggle) {
            navToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                navToggle.classList.toggle('active');

                // Animate hamburger
                const spans = navToggle.querySelectorAll('span');
                if (navMenu.classList.contains('active')) {
                    spans[0].style.transform = 'rotate(45deg) translateY(8px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translateY(-8px)';
                } else {
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '';
                    spans[2].style.transform = '';
                }
            });
        }

        // Close menu on link click (mobile)
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navMenu.classList.remove('active');
                    navToggle.classList.remove('active');

                    const spans = navToggle.querySelectorAll('span');
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '';
                    spans[2].style.transform = '';
                }
            });
        });

        // Close menu on outside click
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }

    // ============================================
    // MODALES
    // ============================================
    function initModals() {
        const modals = {
            register: document.getElementById('registerModal'),
            product: document.getElementById('productModal'),
            contact: document.getElementById('contactModal'),
            info: document.getElementById('infoModal')
        };

        // Abrir modales
        document.addEventListener('click', function(e) {
            const action = e.target.closest('[data-action]');
            if (!action) return;

            e.preventDefault();
            const actionType = action.dataset.action;

            switch(actionType) {
                case 'register':
                    openModal(modals.register);
                    break;
                case 'view-product':
                    openModal(modals.product);
                    break;
                case 'contact':
                    openModal(modals.contact);
                    break;
                case 'help':
                    showInfoModal('Centro de Ayuda', 'Nuestro equipo de soporte está disponible 24/7 para ayudarte. Puedes contactarnos por WhatsApp, teléfono o email.');
                    break;
                case 'terms':
                    showInfoModal('Términos y Condiciones', 'Los términos y condiciones completos estarán disponibles próximamente. Para más información, contáctanos.');
                    break;
                case 'privacy':
                    showInfoModal('Política de Privacidad', 'Tu privacidad es importante para nosotros. La política completa estará disponible próximamente.');
                    break;
            }
        });

        // Cerrar modales
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-close-modal]')) {
                const modal = e.target.closest('.modal');
                if (modal) closeModal(modal);
            }
        });

        // Cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) closeModal(activeModal);
            }
        });

        // Prevenir scroll del body cuando modal está activo
        const modalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const modal = mutation.target;
                    if (modal.classList.contains('active')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                }
            });
        });

        Object.values(modals).forEach(modal => {
            if (modal) modalObserver.observe(modal, { attributes: true });
        });
    }

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('active');

        // Trigger animation
        requestAnimationFrame(() => {
            const container = modal.querySelector('.modal-container');
            if (container) {
                container.style.animation = 'slideUp 0.3s ease-out';
            }
        });
    }

    function closeModal(modal) {
        if (!modal) return;

        const container = modal.querySelector('.modal-container');
        if (container) {
            container.style.animation = 'slideDown 0.3s ease-out';
        }

        setTimeout(() => {
            modal.classList.remove('active');
        }, 250);
    }

    function showInfoModal(title, content) {
        const modal = document.getElementById('infoModal');
        const titleElement = document.getElementById('infoModalTitle');
        const contentElement = document.getElementById('infoModalContent');

        if (titleElement) {
            titleElement.innerHTML = `<i class="bi bi-info-circle-fill"></i> ${title}`;
        }
        if (contentElement) {
            contentElement.textContent = content;
        }

        openModal(modal);
    }

    // ============================================
    // SMOOTH SCROLL
    // ============================================
    function initSmoothScroll() {
        const navLinks = document.querySelectorAll('.nav-link[href^="#"]');

        navLinks.forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Ignorar links que solo son "#"
                if (href === '#' || href.startsWith('#modal')) return;

                e.preventDefault();

                const target = document.querySelector(href);
                if (target) {
                    // Remover active de todos los links
                    navLinks.forEach(link => link.classList.remove('active'));

                    // Agregar active al link clickeado
                    this.classList.add('active');

                    const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - navbarHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Smooth scroll para otros enlaces con hash
        document.querySelectorAll('a[href^="#"]:not(.nav-link)').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                if (href === '#' || href.startsWith('#modal')) return;

                e.preventDefault();

                const target = document.querySelector(href);
                if (target) {
                    const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - navbarHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ============================================
    // ANIMACIONES AL SCROLL
    // ============================================
    function initAnimations() {
        // Crear observer para animaciones
        const observer = new IntersectionObserver(
            function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('aos-animate');

                        // Opcional: dejar de observar después de animar
                        // observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: CONFIG.observerThreshold,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        // Observar todos los elementos con data-aos
        document.querySelectorAll('[data-aos]').forEach(element => {
            observer.observe(element);
        });
    }

    // ============================================
    // LAZY LOADING DE IMÁGENES
    // ============================================
    function initLazyLoading() {
        const images = document.querySelectorAll('img[loading="lazy"]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(
                function(entries, observer) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;

                            // Cargar imagen
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }

                            // Agregar clase cuando carga
                            img.addEventListener('load', function() {
                                img.classList.add('loaded');
                            });

                            // Dejar de observar
                            observer.unobserve(img);
                        }
                    });
                },
                {
                    rootMargin: CONFIG.lazyLoadMargin
                }
            );

            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback para navegadores sin IntersectionObserver
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                img.classList.add('loaded');
            });
        }
    }

    // ============================================
    // ACTIVE NAVIGATION LINKS
    // ============================================
    function initActiveLinks() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');

        function updateActiveLink() {
            const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 80;
            const scrollPosition = window.scrollY + navbarHeight + 50;

            // Array para almacenar secciones visibles
            let currentSection = null;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                const sectionBottom = sectionTop + sectionHeight;

                // Detectar si estamos en esta sección
                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    currentSection = sectionId;
                }
            });

            // Si estamos cerca del final de la página, activar la última sección
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                const lastSection = sections[sections.length - 1];
                currentSection = lastSection?.getAttribute('id');
            }

            // Actualizar clase active en los enlaces
            navLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (href === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', throttle(updateActiveLink, 100), { passive: true });
        updateActiveLink(); // Initial call
    }

    // ============================================
    // PERFORMANCE OPTIMIZATIONS
    // ============================================

    // Debounce function para eventos que se disparan frecuentemente
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Throttle function para scroll events
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // ============================================
    // PRELOAD CRITICAL RESOURCES
    // ============================================
    function preloadCriticalResources() {
        // Precargar fuentes críticas
        const fontPreload = document.createElement('link');
        fontPreload.rel = 'preload';
        fontPreload.as = 'font';
        fontPreload.type = 'font/woff2';
        fontPreload.crossOrigin = 'anonymous';
        document.head.appendChild(fontPreload);
    }

    // ============================================
    // ERROR HANDLING PARA IMÁGENES
    // ============================================
    document.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG') {
            // Ya manejado con onerror en el HTML
            console.warn('Error loading image:', e.target.src);
        }
    }, true);

    // ============================================
    // ANIMACIÓN ADICIONAL PARA SLIDEDOWN
    // ============================================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }
    `;
    document.head.appendChild(style);

    // ============================================
    // ANALYTICS Y TRACKING (OPCIONAL)
    // ============================================
    function trackEvent(category, action, label) {
        // Implementar tracking aquí si es necesario
        console.log('Event:', { category, action, label });
    }

    // Track clicks en botones importantes
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-primary');
        if (btn) {
            trackEvent('Button', 'Click', btn.textContent.trim());
        }
    });

    // ============================================
    // EXPOSE UTILITIES (opcional)
    // ============================================
    window.WelcomePage = {
        openModal,
        closeModal,
        showInfoModal,
        debounce,
        throttle
    };

    // ============================================
    // CONSOLE WELCOME MESSAGE
    // ============================================
    console.log('%c🍷 Bienvenido a Arepa la Llanerita 🍷', 'color: #8B1538; font-size: 20px; font-weight: bold;');
    console.log('%cSistema de Red de Ventas MLM', 'color: #4A4A4A; font-size: 14px;');
    console.log('%cDesarrollado por Luis Alberto Urrea Trujillo', 'color: #666; font-size: 12px;');

})();
