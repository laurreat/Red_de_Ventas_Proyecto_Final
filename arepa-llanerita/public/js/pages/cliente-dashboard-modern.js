/**
 * CLIENTE DASHBOARD - MODERN JAVASCRIPT
 * Version: 2.0
 * Optimized & Minified
 */

class ClienteDashboardManager {
  constructor() {
    if (ClienteDashboardManager.instance) {
      return ClienteDashboardManager.instance;
    }
    ClienteDashboardManager.instance = this;

    this.carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    this.favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.animateCards();
    this.updateCarritoCount();
    this.loadFavoritos();
  }

  setupEventListeners() {
    // ESC para cerrar modales y sidebars
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeAllModals();
        this.closeCarrito();
      }
    });

    // Prevenir scroll cuando modal está abierto
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('modal-backdrop')) {
        this.closeAllModals();
      }
    });
  }

  animateCards() {
    const cards = document.querySelectorAll('.metric-card, .order-card, .producto-card');
    cards.forEach((card, idx) => {
      card.style.opacity = '0';
      setTimeout(() => {
        card.classList.add('animate-fade-in-up');
        card.style.opacity = '1';
      }, idx * 50);
    });
  }

  // ========================================
  // CARRITO DE COMPRAS
  // ========================================
  
  agregarAlCarrito(productoId, nombre, precio, imagen = null) {
    const existente = this.carrito.find(item => item.id === productoId);
    
    if (existente) {
      existente.cantidad++;
    } else {
      this.carrito.push({
        id: productoId,
        nombre: nombre,
        precio: precio,
        imagen: imagen,
        cantidad: 1
      });
    }
    
    this.guardarCarrito();
    this.updateCarritoCount();
    this.showToast(`${nombre} agregado al carrito`, 'success');
    this.renderCarrito();
  }

  eliminarDelCarrito(productoId) {
    this.carrito = this.carrito.filter(item => item.id !== productoId);
    this.guardarCarrito();
    this.updateCarritoCount();
    this.renderCarrito();
    this.showToast('Producto eliminado del carrito', 'info');
  }

  actualizarCantidad(productoId, cantidad) {
    const item = this.carrito.find(item => item.id === productoId);
    if (item) {
      if (cantidad <= 0) {
        this.eliminarDelCarrito(productoId);
      } else {
        item.cantidad = cantidad;
        this.guardarCarrito();
        this.renderCarrito();
      }
    }
  }

  guardarCarrito() {
    localStorage.setItem('carrito', JSON.stringify(this.carrito));
  }

  updateCarritoCount() {
    const count = this.carrito.reduce((sum, item) => sum + item.cantidad, 0);
    const badges = document.querySelectorAll('.carrito-count');
    badges.forEach(badge => {
      badge.textContent = count;
      badge.style.display = count > 0 ? 'flex' : 'none';
    });
  }

  toggleCarrito() {
    const sidebar = document.getElementById('carritoSidebar');
    if (sidebar) {
      sidebar.classList.toggle('active');
      if (sidebar.classList.contains('active')) {
        this.renderCarrito();
      }
    }
  }

  closeCarrito() {
    const sidebar = document.getElementById('carritoSidebar');
    if (sidebar) {
      sidebar.classList.remove('active');
    }
  }

  renderCarrito() {
    const container = document.getElementById('carritoItems');
    const totalEl = document.getElementById('carritoTotal');
    
    if (!container) return;

    if (this.carrito.length === 0) {
      container.innerHTML = `
        <div class="text-center py-5">
          <i class="bi bi-cart-x fs-1 text-muted"></i>
          <p class="text-muted mt-3">Tu carrito está vacío</p>
        </div>
      `;
      if (totalEl) totalEl.textContent = '$0';
      return;
    }

    let total = 0;
    container.innerHTML = this.carrito.map(item => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;
      
      return `
        <div class="carrito-item">
          <div class="carrito-item-image">
            <i class="bi bi-egg-fried fs-4 text-muted"></i>
          </div>
          <div class="carrito-item-info">
            <div class="fw-bold small">${item.nombre}</div>
            <div class="text-success small fw-bold">$${this.formatPrice(item.precio)}</div>
            <div class="d-flex align-items-center gap-2 mt-2">
              <button class="btn btn-sm btn-outline-secondary" onclick="clienteDashboard.actualizarCantidad(${item.id}, ${item.cantidad - 1})">
                <i class="bi bi-dash"></i>
              </button>
              <span class="fw-bold">${item.cantidad}</span>
              <button class="btn btn-sm btn-outline-secondary" onclick="clienteDashboard.actualizarCantidad(${item.id}, ${item.cantidad + 1})">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          <button class="btn btn-sm btn-outline-danger" onclick="clienteDashboard.eliminarDelCarrito(${item.id})">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      `;
    }).join('');

    if (totalEl) {
      totalEl.textContent = `$${this.formatPrice(total)}`;
    }
  }

  // ========================================
  // FAVORITOS
  // ========================================

  toggleFavorito(productoId, nombre = '', precio = 0) {
    const idx = this.favoritos.findIndex(f => f.id === productoId);
    const btn = document.querySelector(`[onclick*="toggleFavorito(${productoId})"]`);
    
    if (idx > -1) {
      this.favoritos.splice(idx, 1);
      if (btn) {
        btn.classList.remove('active');
        btn.querySelector('i').classList.remove('bi-heart-fill');
        btn.querySelector('i').classList.add('bi-heart');
      }
      this.showToast('Eliminado de favoritos', 'info');
    } else {
      this.favoritos.push({ id: productoId, nombre, precio });
      if (btn) {
        btn.classList.add('active');
        btn.querySelector('i').classList.remove('bi-heart');
        btn.querySelector('i').classList.add('bi-heart-fill');
      }
      this.showToast('Agregado a favoritos', 'success');
    }
    
    this.guardarFavoritos();
  }

  guardarFavoritos() {
    localStorage.setItem('favoritos', JSON.stringify(this.favoritos));
  }

  loadFavoritos() {
    this.favoritos.forEach(fav => {
      const btn = document.querySelector(`[onclick*="toggleFavorito(${fav.id})"]`);
      if (btn) {
        btn.classList.add('active');
        btn.querySelector('i').classList.remove('bi-heart');
        btn.querySelector('i').classList.add('bi-heart-fill');
      }
    });
  }

  // ========================================
  // FILTROS Y BÚSQUEDA
  // ========================================

  filtrarProductos() {
    const buscar = document.getElementById('buscarProducto')?.value.toLowerCase() || '';
    const categoria = document.getElementById('filtroCategoria')?.value || '';
    
    const productos = document.querySelectorAll('.producto-item');
    let visibles = 0;

    productos.forEach(producto => {
      const nombre = producto.dataset.nombre?.toLowerCase() || '';
      const cat = producto.dataset.categoria || '';
      
      const matchBuscar = !buscar || nombre.includes(buscar);
      const matchCategoria = !categoria || cat === categoria;
      
      if (matchBuscar && matchCategoria) {
        producto.style.display = '';
        visibles++;
        producto.classList.add('animate-fade-in');
      } else {
        producto.style.display = 'none';
      }
    });

    const noResultados = document.getElementById('noResultados');
    if (noResultados) {
      noResultados.style.display = visibles === 0 ? 'block' : 'none';
    }
  }

  // ========================================
  // MODALES
  // ========================================

  createModal(type = 'primary', title, content, buttons = []) {
    const modalId = `modal-${Date.now()}`;
    const colors = {
      primary: '#722F37',
      success: '#28a745',
      warning: '#ffc107',
      danger: '#dc3545',
      info: '#17a2b8'
    };

    const buttonsHtml = buttons.map(btn => `
      <button class="btn btn-${btn.type || 'secondary'}" onclick="${btn.onclick || ''}">
        ${btn.icon ? `<i class="bi bi-${btn.icon} me-1"></i>` : ''}
        ${btn.text}
      </button>
    `).join('');

    const modalHtml = `
      <div class="modal-backdrop" id="${modalId}-backdrop"></div>
      <div class="modal-container" id="${modalId}">
        <div class="modal-content">
          <div class="modal-header" style="border-bottom-color: ${colors[type]};">
            <h5>${title}</h5>
            <button class="modal-close" onclick="clienteDashboard.closeModal('${modalId}')">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="modal-body">${content}</div>
          ${buttons.length > 0 ? `<div class="modal-footer">${buttonsHtml}</div>` : ''}
        </div>
      </div>
    `;

    const div = document.createElement('div');
    div.innerHTML = modalHtml;
    document.body.appendChild(div);
    
    setTimeout(() => document.body.style.overflow = 'hidden', 10);
    
    return modalId;
  }

  closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.getElementById(`${modalId}-backdrop`);
    
    if (modal) {
      modal.style.opacity = '0';
      modal.style.transform = 'scale(0.9)';
    }
    if (backdrop) {
      backdrop.style.opacity = '0';
    }
    
    setTimeout(() => {
      if (modal) modal.parentElement.remove();
      if (backdrop) backdrop.remove();
      document.body.style.overflow = '';
    }, 300);
  }

  closeAllModals() {
    document.querySelectorAll('.modal-container, .modal-backdrop').forEach(el => {
      el.style.opacity = '0';
      setTimeout(() => el.parentElement?.remove(), 300);
    });
    document.body.style.overflow = '';
  }

  showComingSoon(feature) {
    this.createModal('info', '🚧 Próximamente', `
      <div class="text-center py-3">
        <i class="bi bi-tools fs-1 text-info mb-3 d-block"></i>
        <p class="mb-0">La funcionalidad <strong>"${feature}"</strong> estará disponible pronto.</p>
        <p class="text-muted small mt-2">Estamos trabajando para ofrecerte la mejor experiencia.</p>
      </div>
    `, [
      { text: 'Entendido', type: 'primary', onclick: 'clienteDashboard.closeAllModals()' }
    ]);
  }

  // ========================================
  // CONFIRMACIÓN DE PEDIDO
  // ========================================

  confirmarPedido() {
    if (this.carrito.length === 0) {
      this.showToast('Tu carrito está vacío', 'warning');
      return;
    }

    const total = this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    
    const itemsHtml = this.carrito.map(item => `
      <div class="d-flex justify-content-between py-2 border-bottom">
        <div>
          <div class="fw-bold">${item.nombre}</div>
          <small class="text-muted">Cantidad: ${item.cantidad}</small>
        </div>
        <div class="text-success fw-bold">${this.formatPrice(item.precio * item.cantidad)}</div>
      </div>
    `).join('');

    this.createModal('success', '🛒 Confirmar Pedido', `
      <div>
        <h6 class="mb-3">Resumen de tu pedido:</h6>
        ${itemsHtml}
        <div class="d-flex justify-content-between py-3 border-top mt-3">
          <strong>Total a pagar:</strong>
          <strong class="text-success fs-5">${this.formatPrice(total)}</strong>
        </div>
        <div class="alert alert-info mt-3 mb-0">
          <i class="bi bi-info-circle me-2"></i>
          <small>Al confirmar, recibirás un correo con los detalles de tu pedido.</small>
        </div>
      </div>
    `, [
      { text: 'Cancelar', type: 'outline-secondary', onclick: 'clienteDashboard.closeAllModals()' },
      { text: 'Confirmar Pedido', type: 'success', icon: 'check-circle', onclick: 'clienteDashboard.procesarPedido()' }
    ]);
  }

  procesarPedido() {
    this.showLoading('Procesando tu pedido...');
    
    // Simulación de envío al servidor
    setTimeout(() => {
      this.hideLoading();
      this.closeAllModals();
      this.carrito = [];
      this.guardarCarrito();
      this.updateCarritoCount();
      this.closeCarrito();
      
      this.createModal('success', '✅ ¡Pedido Confirmado!', `
        <div class="text-center py-3">
          <i class="bi bi-check-circle fs-1 text-success mb-3 d-block"></i>
          <h5 class="mb-3">¡Tu pedido ha sido confirmado!</h5>
          <p class="text-muted">Recibirás un correo con los detalles.</p>
          <p class="text-muted mb-0">Número de pedido: <strong>#${Math.floor(Math.random() * 10000)}</strong></p>
        </div>
      `, [
        { text: 'Ver mis pedidos', type: 'primary', onclick: 'clienteDashboard.closeAllModals()' }
      ]);
    }, 2000);
  }

  // ========================================
  // COMPARTIR CÓDIGO DE REFERIDO
  // ========================================

  shareReferralCode(codigo, nombre) {
    const text = `¡Prueba las deliciosas arepas de Arepa la Llanerita! Usa mi código de referido: ${codigo} y obtén beneficios especiales.`;
    const url = window.location.origin;
    
    if (navigator.share) {
      navigator.share({
        title: 'Arepa la Llanerita - Código de Referido',
        text: text,
        url: url
      }).catch(() => {
        this.copyToClipboard(text);
      });
    } else {
      this.copyToClipboard(text);
    }
  }

  copyToClipboard(text) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        this.showToast('Código copiado al portapapeles', 'success');
      }).catch(() => {
        this.fallbackCopyTextToClipboard(text);
      });
    } else {
      this.fallbackCopyTextToClipboard(text);
    }
  }

  fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
      document.execCommand('copy');
      this.showToast('Código copiado al portapapeles', 'success');
    } catch (err) {
      this.showToast('No se pudo copiar el código', 'error');
    }
    
    document.body.removeChild(textArea);
  }

  // ========================================
  // TOAST NOTIFICATIONS
  // ========================================

  showToast(message, type = 'info') {
    const icons = {
      success: 'check-circle-fill',
      error: 'x-circle-fill',
      warning: 'exclamation-triangle-fill',
      info: 'info-circle-fill'
    };

    const container = this.getToastContainer();
    const toastId = `toast-${Date.now()}`;
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.id = toastId;
    toast.innerHTML = `
      <i class="bi bi-${icons[type]}"></i>
      <div class="toast-message">${message}</div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(20px)';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  getToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  // ========================================
  // LOADING OVERLAY
  // ========================================

  showLoading(message = 'Cargando...') {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = `
      <div class="text-center">
        <div class="loading-spinner"></div>
        <div class="text-white mt-3 fw-bold">${message}</div>
      </div>
    `;
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
  }

  hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
      overlay.style.opacity = '0';
      setTimeout(() => {
        overlay.remove();
        document.body.style.overflow = '';
      }, 300);
    }
  }

  // ========================================
  // UTILIDADES
  // ========================================

  formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(price);
  }

  debounce(func, wait) {
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
}

// ========================================
// INICIALIZACIÓN
// ========================================

let clienteDashboard;

document.addEventListener('DOMContentLoaded', () => {
  clienteDashboard = new ClienteDashboardManager();
  
  // Configurar búsqueda con debounce
  const buscarInput = document.getElementById('buscarProducto');
  if (buscarInput) {
    const debouncedFilter = clienteDashboard.debounce(() => {
      clienteDashboard.filtrarProductos();
    }, 300);
    buscarInput.addEventListener('input', debouncedFilter);
  }
  
  // Configurar filtro de categoría
  const filtroCategoria = document.getElementById('filtroCategoria');
  if (filtroCategoria) {
    filtroCategoria.addEventListener('change', () => {
      clienteDashboard.filtrarProductos();
    });
  }
});

// ========================================
// FUNCIONES GLOBALES
// ========================================

function agregarAlCarrito(id) {
  const producto = document.querySelector(`[onclick*="agregarAlCarrito(${id})"]`)?.closest('.producto-card');
  if (producto) {
    const nombre = producto.querySelector('.card-title')?.textContent || 'Producto';
    const precioText = producto.querySelector('.precio-producto .fw-bold')?.textContent || '$0';
    const precio = parseInt(precioText.replace(/\D/g, ''));
    clienteDashboard.agregarAlCarrito(id, nombre, precio);
  }
}

function toggleFavorito(id) {
  const producto = document.querySelector(`[onclick*="toggleFavorito(${id})"]`)?.closest('.producto-card');
  if (producto) {
    const nombre = producto.querySelector('.card-title')?.textContent || '';
    const precioText = producto.querySelector('.precio-producto .fw-bold')?.textContent || '$0';
    const precio = parseInt(precioText.replace(/\D/g, ''));
    clienteDashboard.toggleFavorito(id, nombre, precio);
  }
}

function filtrarProductos() {
  clienteDashboard.filtrarProductos();
}

function showComingSoon(feature) {
  clienteDashboard.showComingSoon(feature);
}

function shareReferralCode() {
  const codigo = document.querySelector('.bg-white .fw-bold.text-primary')?.textContent || '';
  const nombre = document.querySelector('.welcome-card h1')?.textContent || '';
  clienteDashboard.shareReferralCode(codigo, nombre);
}

// PWA: Registrar Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {
      console.log('Service Worker registration failed');
    });
  });
}