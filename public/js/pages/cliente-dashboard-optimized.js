/**
 * CLIENTE DASHBOARD - OPTIMIZED JAVASCRIPT
 * Version: 3.0
 * Optimizado para rendimiento < 3s
 */

class ClienteDashboardManager {
  constructor() {
    if (ClienteDashboardManager.instance) {
      return ClienteDashboardManager.instance;
    }
    ClienteDashboardManager.instance = this;

    this.carrito = this.loadFromStorage('carrito') || [];
    this.favoritos = this.loadFromStorage('favoritos') || [];
    this.init();
  }

  init() {
    this.updateCarritoCount();
    this.setupEventListeners();
    this.lazyLoadImages();
  }

  // ========================================
  // STORAGE MANAGEMENT
  // ========================================
  loadFromStorage(key) {
    try {
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      console.error(`Error loading ${key} from storage:`, e);
      return null;
    }
  }

  saveToStorage(key, data) {
    try {
      localStorage.setItem(key, JSON.stringify(data));
    } catch (e) {
      console.error(`Error saving ${key} to storage:`, e);
    }
  }

  // ========================================
  // EVENT LISTENERS
  // ========================================
  setupEventListeners() {
    // ESC para cerrar carrito
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeCarrito();
      }
    });

    // Click en overlay para cerrar
    const overlay = document.getElementById('carritoOverlay');
    if (overlay) {
      overlay.addEventListener('click', () => this.closeCarrito());
    }
  }

  // ========================================
  // LAZY LOADING DE IMÁGENES
  // ========================================
  lazyLoadImages() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.classList.add('loaded');
            imageObserver.unobserve(img);
          }
        });
      });

      images.forEach(img => imageObserver.observe(img));
    } else {
      // Fallback para navegadores antiguos
      images.forEach(img => img.classList.add('loaded'));
    }
  }

  // ========================================
  // CARRITO DE COMPRAS
  // ========================================
  agregarAlCarrito(productoId, nombre, precio, imagen = null, stock = null) {
    // Validar stock
    if (stock !== null && stock <= 0) {
      this.showToast('Producto agotado', 'warning');
      return;
    }

    const existente = this.carrito.find(item => item.id === productoId);
    
    if (existente) {
      if (stock !== null && existente.cantidad >= stock) {
        this.showToast(`Stock máximo disponible: ${stock}`, 'warning');
        return;
      }
      existente.cantidad++;
    } else {
      this.carrito.push({
        id: productoId,
        nombre: nombre,
        precio: precio,
        imagen: imagen,
        cantidad: 1,
        stock: stock
      });
    }
    
    this.saveToStorage('carrito', this.carrito);
    this.updateCarritoCount();
    this.renderCarrito();
    this.showToast(`${nombre} agregado al carrito`, 'success');
    
    // Animación del botón
    this.animateCartButton();
  }

  eliminarDelCarrito(productoId) {
    this.carrito = this.carrito.filter(item => item.id !== productoId);
    this.saveToStorage('carrito', this.carrito);
    this.updateCarritoCount();
    this.renderCarrito();
    this.showToast('Producto eliminado', 'info');
  }

  actualizarCantidad(productoId, cantidad) {
    const item = this.carrito.find(i => i.id === productoId);
    if (item) {
      if (item.stock !== null && cantidad > item.stock) {
        this.showToast(`Stock máximo: ${item.stock}`, 'warning');
        return;
      }
      if (cantidad <= 0) {
        this.eliminarDelCarrito(productoId);
        return;
      }
      item.cantidad = cantidad;
      this.saveToStorage('carrito', this.carrito);
      this.renderCarrito();
    }
  }

  vaciarCarrito() {
    GlassModal.confirm({
      title: 'Vaciar Carrito',
      message: '¿Estás seguro de que deseas vaciar todo el carrito? Esta acción no se puede deshacer.',
      icon: 'bi-trash',
      iconColor: '#dc3545',
      iconBg: 'rgba(220, 53, 69, 0.2)',
      confirmText: 'Sí, vaciar',
      cancelText: 'Cancelar',
      confirmClass: 'btn-glass-danger',
      onConfirm: () => {
        this.carrito = [];
        this.saveToStorage('carrito', this.carrito);
        this.updateCarritoCount();
        this.renderCarrito();
        this.showToast('Carrito vaciado', 'info');
      }
    });
  }

  updateCarritoCount() {
    const count = this.carrito.reduce((sum, item) => sum + item.cantidad, 0);
    const badge = document.getElementById('carritoCount');
    
    if (badge) {
      badge.textContent = count;
      badge.style.display = count > 0 ? 'flex' : 'none';
    }
  }

  renderCarrito() {
    const container = document.getElementById('carritoItems');
    const totalElement = document.getElementById('carritoTotal');
    const btnVaciar = document.getElementById('btnVaciarCarrito');
    const btnConfirmar = document.getElementById('btnConfirmarPedido');
    
    if (!container) return;

    if (this.carrito.length === 0) {
      container.innerHTML = `
        <div class="carrito-empty">
          <div class="carrito-empty-icon">
            <i class="bi bi-cart-x"></i>
          </div>
          <p class="carrito-empty-text">Tu carrito está vacío</p>
          <p class="carrito-empty-subtext">¡Agrega productos para comenzar!</p>
        </div>
      `;
      if (totalElement) totalElement.textContent = '$0';
      if (btnVaciar) btnVaciar.style.display = 'none';
      if (btnConfirmar) btnConfirmar.disabled = true;
      return;
    }

    let html = '<div class="carrito-items-list">';
    let total = 0;

    this.carrito.forEach(item => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;
      
      html += `
        <div class="carrito-item">
          <div class="carrito-item-image">
            ${item.imagen ? 
              `<img src="/storage/${item.imagen}" alt="${item.nombre}" loading="lazy">` :
              `<div class="carrito-item-placeholder"><i class="bi bi-image"></i></div>`
            }
          </div>
          <div class="carrito-item-info">
            <h4 class="carrito-item-name">${item.nombre}</h4>
            <div class="carrito-item-price">$${this.formatNumber(item.precio)}</div>
            <div class="carrito-item-controls">
              <button class="btn-quantity" onclick="clienteDashboard.actualizarCantidad('${item.id}', ${item.cantidad - 1})">
                <i class="bi bi-dash"></i>
              </button>
              <span class="carrito-item-quantity">${item.cantidad}</span>
              <button class="btn-quantity" onclick="clienteDashboard.actualizarCantidad('${item.id}', ${item.cantidad + 1})">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          <div class="carrito-item-actions">
            <div class="carrito-item-subtotal">$${this.formatNumber(subtotal)}</div>
            <button class="btn-remove-item" onclick="clienteDashboard.eliminarDelCarrito('${item.id}')">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `;
    });

    html += '</div>';
    container.innerHTML = html;
    
    if (totalElement) totalElement.textContent = `$${this.formatNumber(total)}`;
    if (btnVaciar) btnVaciar.style.display = 'flex';
    if (btnConfirmar) btnConfirmar.disabled = false;
  }

  openCarrito() {
    this.renderCarrito();
    document.getElementById('carritoLateral')?.classList.add('active');
    document.getElementById('carritoOverlay')?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  closeCarrito() {
    document.getElementById('carritoLateral')?.classList.remove('active');
    document.getElementById('carritoOverlay')?.classList.remove('active');
    document.body.style.overflow = '';
  }

  animateCartButton() {
    const btn = document.getElementById('btnCarritoFlotante');
    if (btn) {
      btn.style.animation = 'none';
      setTimeout(() => {
        btn.style.animation = 'pulse 0.5s ease';
      }, 10);
    }
  }

  confirmarPedido() {
    if (this.carrito.length === 0) {
      this.showToast('El carrito está vacío', 'warning');
      return;
    }

    // Guardar el carrito en localStorage para que se cargue en la página de crear pedido
    this.saveToStorage('carrito', this.carrito);
    
    // Mostrar mensaje de confirmación
    this.showToast('Redirigiendo a crear pedido...', 'info');
    
    // Redirigir a la página de crear pedido
    setTimeout(() => {
      window.location.href = '/cliente/pedidos/create';
    }, 500);
  }

  // ========================================
  // UTILIDADES
  // ========================================
  formatNumber(number) {
    return new Intl.NumberFormat('es-CO').format(number);
  }

  showToast(message, type = 'info') {
    // Crear toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <div class="toast-icon">
        <i class="bi bi-${this.getToastIcon(type)}"></i>
      </div>
      <div class="toast-message">${message}</div>
    `;

    // Agregar estilos si no existen
    if (!document.getElementById('toast-styles')) {
      const style = document.createElement('style');
      style.id = 'toast-styles';
      style.textContent = `
        .toast {
          position: fixed;
          top: 2rem;
          right: 2rem;
          background: white;
          padding: 1rem 1.5rem;
          border-radius: 0.5rem;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          display: flex;
          align-items: center;
          gap: 0.75rem;
          z-index: 10000;
          animation: slideIn 0.3s ease;
          max-width: 300px;
        }
        .toast-icon { font-size: 1.5rem; }
        .toast-success { border-left: 4px solid #28a745; }
        .toast-success .toast-icon { color: #28a745; }
        .toast-error { border-left: 4px solid #dc3545; }
        .toast-error .toast-icon { color: #dc3545; }
        .toast-warning { border-left: 4px solid #ffc107; }
        .toast-warning .toast-icon { color: #ffc107; }
        .toast-info { border-left: 4px solid #17a2b8; }
        .toast-info .toast-icon { color: #17a2b8; }
        @keyframes slideIn {
          from { transform: translateX(400px); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
          from { transform: translateX(0); opacity: 1; }
          to { transform: translateX(400px); opacity: 0; }
        }
      `;
      document.head.appendChild(style);
    }

    document.body.appendChild(toast);

    // Auto-remover después de 3 segundos
    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  getToastIcon(type) {
    const icons = {
      success: 'check-circle-fill',
      error: 'x-circle-fill',
      warning: 'exclamation-triangle-fill',
      info: 'info-circle-fill'
    };
    return icons[type] || icons.info;
  }
}

// Estilos adicionales para carrito items
const carritoItemsStyles = `
<style>
.carrito-items-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.carrito-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: var(--gray-50, #f8f9fa);
  border-radius: 0.5rem;
  border: 1px solid var(--gray-200, #e9ecef);
}

.carrito-item-image {
  width: 80px;
  height: 80px;
  border-radius: 0.5rem;
  overflow: hidden;
  flex-shrink: 0;
  background: var(--gray-100, #e9ecef);
}

.carrito-item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.carrito-item-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--gray-400, #ced4da);
}

.carrito-item-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.carrito-item-name {
  font-size: 0.9375rem;
  font-weight: 600;
  margin: 0;
  color: var(--gray-800, #343a40);
}

.carrito-item-price {
  font-size: 0.875rem;
  color: var(--gray-600, #6c757d);
}

.carrito-item-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-quantity {
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 0.25rem;
  background: white;
  border: 1px solid var(--gray-300, #dee2e6);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-quantity:hover {
  background: var(--primary, #722F37);
  color: white;
  border-color: var(--primary, #722F37);
}

.carrito-item-quantity {
  min-width: 2rem;
  text-align: center;
  font-weight: 600;
}

.carrito-item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
}

.carrito-item-subtotal {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--primary, #722F37);
}

.btn-remove-item {
  width: 2rem;
  height: 2rem;
  border-radius: 0.25rem;
  background: transparent;
  border: 1px solid var(--danger, #dc3545);
  color: var(--danger, #dc3545);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-remove-item:hover {
  background: var(--danger, #dc3545);
  color: white;
}
</style>
`;

// Inyectar estilos del carrito
if (!document.getElementById('carrito-items-styles')) {
  document.head.insertAdjacentHTML('beforeend', carritoItemsStyles);
}

// Exportar instancia global
window.ClienteDashboardManager = ClienteDashboardManager;
