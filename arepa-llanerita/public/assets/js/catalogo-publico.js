/*!
 * CATÁLOGO PÚBLICO - JAVASCRIPT
 * Funcionalidades para el catálogo público de productos
 * ========================================
 */

class CatalogoPublico {
    constructor() {
        this.sidebar = null;
        this.mainWrapper = null;
        this.sidebarToggle = null;
        this.mobileSidebarToggle = null;
        this.sidebarOverlay = null;
        this.toggleIcon = null;

        this.init();
    }

    init() {
        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupElements());
        } else {
            this.setupElements();
        }
    }

    setupElements() {
        // Obtener referencias a elementos
        this.sidebar = document.getElementById('publicSidebar');
        this.mainWrapper = document.getElementById('mainWrapper');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.toggleIcon = document.getElementById('toggleIcon');

        if (!this.sidebar) {
            console.warn('Sidebar element not found');
            return;
        }

        this.bindEvents();
        this.setupCategoryNavigation();
        this.setupTooltips();
        this.loadUserPreferences();
    }

    bindEvents() {
        // Toggle sidebar desktop
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => this.toggleSidebar());
        }

        // Toggle sidebar mobile
        if (this.mobileSidebarToggle) {
            this.mobileSidebarToggle.addEventListener('click', () => this.showMobileSidebar());
        }

        // Cerrar sidebar mobile al hacer click en overlay
        if (this.sidebarOverlay) {
            this.sidebarOverlay.addEventListener('click', () => this.hideMobileSidebar());
        }

        // Manejar resize de ventana
        window.addEventListener('resize', () => this.handleResize());

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));

        // Search form handling
        this.setupSearchForm();
    }

    toggleSidebar() {
        if (!this.sidebar || !this.mainWrapper) return;

        const isCollapsed = this.sidebar.classList.contains('collapsed');

        if (isCollapsed) {
            this.expandSidebar();
        } else {
            this.collapseSidebar();
        }

        this.saveUserPreference('sidebarCollapsed', !isCollapsed);
    }

    expandSidebar() {
        this.sidebar.classList.remove('collapsed');
        this.mainWrapper.classList.remove('sidebar-collapsed');

        if (this.toggleIcon) {
            this.toggleIcon.className = 'bi bi-chevron-left';
        }

        // Trigger custom event
        this.dispatchEvent('sidebarExpanded');
    }

    collapseSidebar() {
        this.sidebar.classList.add('collapsed');
        this.mainWrapper.classList.add('sidebar-collapsed');

        if (this.toggleIcon) {
            this.toggleIcon.className = 'bi bi-chevron-right';
        }

        // Trigger custom event
        this.dispatchEvent('sidebarCollapsed');
    }

    showMobileSidebar() {
        if (!this.sidebar || !this.sidebarOverlay) return;

        this.sidebar.classList.add('show');
        this.sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';

        this.dispatchEvent('mobileSidebarShown');
    }

    hideMobileSidebar() {
        if (!this.sidebar || !this.sidebarOverlay) return;

        this.sidebar.classList.remove('show');
        this.sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';

        this.dispatchEvent('mobileSidebarHidden');
    }

    handleResize() {
        if (window.innerWidth > 768) {
            this.hideMobileSidebar();
        }
    }

    handleKeyboard(e) {
        // ESC para cerrar sidebar mobile
        if (e.key === 'Escape') {
            this.hideMobileSidebar();
        }

        // Ctrl + B para toggle sidebar desktop
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            if (window.innerWidth > 768) {
                this.toggleSidebar();
            }
        }
    }

    setupCategoryNavigation() {
        const categoryLinks = document.querySelectorAll('.nav-link[data-category], .dropdown-item[data-category]');
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        // Setup category links
        categoryLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.handleCategoryClick(e, link);
            });
        });

        // Setup dropdown toggles
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleDropdownToggle(e, toggle);
            });
        });

        // Highlight active category based on URL
        this.highlightActiveCategory();

        // Initialize dropdown states
        this.initializeDropdownStates();
    }

    handleDropdownToggle(e, toggle) {
        const dropdownMenu = toggle.nextElementSibling;
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) {
            return;
        }

        // Toggle dropdown
        if (isExpanded) {
            this.collapseDropdown(toggle, dropdownMenu);
        } else {
            this.expandDropdown(toggle, dropdownMenu);
        }
    }

    expandDropdown(toggle, dropdownMenu) {
        toggle.setAttribute('aria-expanded', 'true');
        toggle.classList.add('expanded');
        dropdownMenu.classList.add('show');

        // Save state
        this.saveDropdownState(toggle.id, true);

        this.dispatchEvent('dropdownExpanded', {
            toggleId: toggle.id,
            dropdownMenu: dropdownMenu
        });
    }

    collapseDropdown(toggle, dropdownMenu) {
        toggle.setAttribute('aria-expanded', 'false');
        toggle.classList.remove('expanded');
        dropdownMenu.classList.remove('show');

        // Save state
        this.saveDropdownState(toggle.id, false);

        this.dispatchEvent('dropdownCollapsed', {
            toggleId: toggle.id,
            dropdownMenu: dropdownMenu
        });
    }

    initializeDropdownStates() {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            const savedState = this.getDropdownState(toggle.id);
            const dropdownMenu = toggle.nextElementSibling;

            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                if (savedState === true) {
                    this.expandDropdown(toggle, dropdownMenu);
                } else if (savedState === false) {
                    this.collapseDropdown(toggle, dropdownMenu);
                } else {
                    // Default: expand if there's an active category inside
                    const hasActiveItem = dropdownMenu.querySelector('.dropdown-item.active');
                    if (hasActiveItem) {
                        this.expandDropdown(toggle, dropdownMenu);
                    }
                }
            }
        });
    }

    saveDropdownState(toggleId, isExpanded) {
        try {
            localStorage.setItem(`catalogoPublico.dropdown.${toggleId}`, isExpanded.toString());
        } catch (e) {
            console.warn('Unable to save dropdown state:', e);
        }
    }

    getDropdownState(toggleId) {
        try {
            const state = localStorage.getItem(`catalogoPublico.dropdown.${toggleId}`);
            return state === null ? null : state === 'true';
        } catch (e) {
            console.warn('Unable to get dropdown state:', e);
            return null;
        }
    }

    handleCategoryClick(e, link) {
        // Remove active class from all category links
        document.querySelectorAll('.nav-link').forEach(el => {
            el.classList.remove('active');
        });

        // Add active class to clicked link
        link.classList.add('active');

        // Close mobile sidebar if open
        if (window.innerWidth <= 768) {
            setTimeout(() => {
                this.hideMobileSidebar();
            }, 150);
        }

        // Add loading effect
        this.showLoadingState(link);
    }

    highlightActiveCategory() {
        const currentUrl = window.location.search;
        const urlParams = new URLSearchParams(currentUrl);
        const categoryId = urlParams.get('categoria');

        // Remove active class from all links and dropdown items
        document.querySelectorAll('.nav-link, .dropdown-item').forEach(el => {
            el.classList.remove('active');
        });

        if (categoryId) {
            // Highlight specific category (check both nav-links and dropdown-items)
            const categoryLink = document.querySelector(`.nav-link[data-category="${categoryId}"], .dropdown-item[data-category="${categoryId}"]`);
            if (categoryLink) {
                categoryLink.classList.add('active');

                // If it's a dropdown item, ensure the parent dropdown is expanded
                if (categoryLink.classList.contains('dropdown-item')) {
                    const dropdown = categoryLink.closest('.dropdown-menu');
                    const toggle = dropdown ? dropdown.previousElementSibling : null;

                    if (toggle && toggle.classList.contains('dropdown-toggle')) {
                        this.expandDropdown(toggle, dropdown);
                    }
                }
            }
        } else {
            // Highlight "All Products" link
            const allProductsLink = document.querySelector('.nav-link[data-category="all"]');
            if (allProductsLink) {
                allProductsLink.classList.add('active');
            }
        }
    }

    showLoadingState(element) {
        const originalText = element.innerHTML;
        const icon = element.querySelector('i');

        if (icon) {
            icon.className = 'bi bi-arrow-repeat';
            icon.style.animation = 'spin 1s linear infinite';
        }

        // Restore after delay
        setTimeout(() => {
            element.innerHTML = originalText;
        }, 1000);
    }

    setupSearchForm() {
        const searchForm = document.querySelector('#searchForm');
        const searchInput = document.querySelector('#searchInput');

        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                this.handleSearchSubmit(e);
            });
        }

        if (searchInput) {
            // Auto-focus on search when pressing '/'
            document.addEventListener('keydown', (e) => {
                if (e.key === '/' && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const activeElement = document.activeElement;
                    if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        searchInput.focus();
                    }
                }
            });

            // Clear search with ESC
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    searchInput.blur();
                }
            });
        }
    }

    handleSearchSubmit(e) {
        const searchInput = e.target.querySelector('input[name="buscar"]');
        if (searchInput && searchInput.value.trim() === '') {
            e.preventDefault();
            return;
        }

        // Show loading state
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin me-2"></i>Buscando...';
            submitBtn.disabled = true;

            // Restore button after form submits
            setTimeout(() => {
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            }, 2000);
        }
    }

    setupTooltips() {
        // Setup tooltips for collapsed sidebar
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const text = link.querySelector('.nav-text')?.textContent;
            if (text) {
                link.setAttribute('title', text);
                link.setAttribute('data-bs-placement', 'right');
            }
        });

        // Initialize Bootstrap tooltips if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltips = document.querySelectorAll('[title]');
            tooltips.forEach(el => {
                new bootstrap.Tooltip(el);
            });
        }
    }

    loadUserPreferences() {
        try {
            const collapsed = localStorage.getItem('catalogoPublico.sidebarCollapsed') === 'true';
            if (collapsed && window.innerWidth > 768) {
                this.collapseSidebar();
            }
        } catch (e) {
            console.warn('Unable to load user preferences:', e);
        }
    }

    saveUserPreference(key, value) {
        try {
            localStorage.setItem(`catalogoPublico.${key}`, value.toString());
        } catch (e) {
            console.warn('Unable to save user preference:', e);
        }
    }

    dispatchEvent(eventName, data = {}) {
        const event = new CustomEvent(eventName, {
            detail: {
                timestamp: Date.now(),
                ...data
            }
        });
        document.dispatchEvent(event);
    }

    // Public API methods
    getSidebarState() {
        return {
            collapsed: this.sidebar?.classList.contains('collapsed') || false,
            mobileVisible: this.sidebar?.classList.contains('show') || false
        };
    }

    // Static utility methods
    static showNotification(message, type = 'info', duration = 5000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 1rem; right: 1rem; z-index: 1060; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }

    static redirectToLogin(productName = '') {
        const message = productName
            ? `¿Te interesa "${productName}"? Para realizar una compra necesitas iniciar sesión. ¿Deseas continuar?`
            : 'Para realizar una compra necesitas iniciar sesión. ¿Deseas continuar?';

        if (confirm(message)) {
            const loginUrl = document.querySelector('a[href*="login"]')?.href || '/login';
            window.location.href = loginUrl;
        }
    }

    static formatPrice(price) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(price);
    }
}

// Initialize when DOM is ready
let catalogoPublicoInstance = null;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        catalogoPublicoInstance = new CatalogoPublico();
    });
} else {
    catalogoPublicoInstance = new CatalogoPublico();
}

// Export for global access
window.CatalogoPublico = CatalogoPublico;
window.catalogoPublico = catalogoPublicoInstance;

// Global helper functions
window.redirectToLogin = CatalogoPublico.redirectToLogin;
window.showNotification = CatalogoPublico.showNotification;

// Add CSS for spinning animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin { animation: spin 1s linear infinite; }
`;
document.head.appendChild(style);