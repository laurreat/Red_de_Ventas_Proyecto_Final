/**
 * Pedidos Cliente Manager - Versión 3.0 Mejorada
 * Optimizado, con mejor manejo de errores y UX mejorada
 */

class PedidosClienteManager {
  constructor() {
    this.modals = {};
    this.toastCount = 0;
    this.cart = new Map();
    this.init();
  }

  init() {
    this.initEventListeners();
    this.animateCards();
    this.checkPWAStatus();
    this.initCartFromLocalStorage();
  }

  initEventListeners() {
    // Eventos de navegación
    document.addEventListener('click', (e) => {
      // Ver pedido
      if (e.target.closest('[data-action="view-order"]')) {
        e.preventDefault();
        const btn = e.target.closest('[data-action="view-order"]');
        this.viewOrder(btn.dataset.orderId);
      }
      
      // Cancelar pedido
      else if (e.target.closest('[data-action="cancel-order"]')) {
        e.preventDefault();
        const btn = e.target.closest('[data-action="cancel-order"]');
        this.showCancelModal(btn.dataset.orderId);
      }
      
      // Repetir pedido
      else if (e.target.closest('[data-action="repeat-order"]')) {
        e.preventDefault();
        const btn = e.target.closest('[data-action="repeat-order"]');
        this.repeatOrder(btn.dataset.orderId);
      }
      
      // Aplicar filtros
      else if (e.target.closest('[data-action="filter"]')) {
        e.preventDefault();
        this.applyFilters();
      }
      
      // Limpiar filtros
      else if (e.target.closest('[data-action="clear-filters"]')) {
        e.preventDefault();
        this.clearFilters();
      }
    });

    // Cerrar modales
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        this.closeModal(btn.dataset.closeModal);
      });
    });

    // ESC para cerrar modales
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeAllModals();
      }
    });

    // Prevenir múltiples envíos del formulario
    const form = document.getElementById('crearPedidoForm');
    if (form) {
      form.addEventListener('submit', (e) => {
        if (form.classList.contains('submitting')) {
          e.preventDefault();
          return false;
        }
        
        if (this.cart.size === 0) {
          e.preventDefault();
          this.showToast('warning', 'Carrito vacío', 'Debes seleccionar al menos un producto');
          return false;
        }
        
        form.classList.add('submitting');
        this.showLoading('Procesando tu pedido...');
      });
    }
  }

  animateCards() {
    const cards = document.querySelectorAll('.pedido-card, .pedido-stat-card, .pedidos-form-section');
    cards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add('fade-in-up');
      }, index * 80);
    });
  }

  /**
   * Inicializar carrito desde localStorage
   */
  initCartFromLocalStorage() {
    try {
      const carritoLS = JSON.parse(localStorage.getItem('carrito')) || [];
      
      if (carritoLS.length > 0 && document.getElementById('crearPedidoForm')) {
        console.log('🛒 Cargando carrito desde localStorage:', carritoLS.length, 'items');
        
        let cargados = 0;
        carritoLS.forEach(item => {
          const checkbox = document.querySelector(`input[type="checkbox"][value="${item.id}"]`);
          
          if (checkbox && !checkbox.disabled) {
            checkbox.checked = true;
            const container = checkbox.closest('.pedidos-product-checkbox');
            
            if (container) {
              container.classList.add('selected');
              
              const qtyDiv = container.querySelector('.pedidos-product-quantity');
              if (qtyDiv) {
                qtyDiv.style.display = 'flex';
                const cantidadInput = qtyDiv.querySelector('.cantidad-input');
                if (cantidadInput) {
                  cantidadInput.value = item.cantidad || 1;
                }
              }
              
              // Agregar al carrito del manager
              const productoItem = container.closest('.producto-item');
              if (productoItem) {
                const nombre = productoItem.dataset.nombre || item.nombre || 'Producto';
                const precio = parseFloat(container.dataset.precio) || item.precio || 0;
                
                this.cart.set(item.id, {
                  nombre: nombre,
                  precio: precio,
                  cantidad: item.cantidad || 1
                });
              }
              
              cargados++;
            }
          }
        });
        
        if (cargados > 0) {
          this.updateCart();
          this.showToast('success', 'Carrito cargado', `${cargados} producto(s) cargado(s)`);
          // Limpiar localStorage
          localStorage.removeItem('carrito');
        }
      }
    } catch (error) {
      console.error('Error al cargar carrito desde localStorage:', error);
    }
  }

  /**
   * Actualizar vista del carrito
   */
  updateCart() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const submitBtn = document.getElementById('submitBtn');
    const productosSeleccionados = document.getElementById('productosSeleccionados');
    
    if (!cartItems || !cartTotal || !submitBtn) return;
    
    if (this.cart.size === 0) {
      cartItems.innerHTML = `
        <div class="text-center py-5 text-white-50">
          <i class="bi bi-cart-x fs-1 mb-3 d-block"></i>
          <p class="mb-0">No has seleccionado productos</p>
        </div>
      `;
      cartTotal.textContent = '$0';
      submitBtn.disabled = true;
      
      if (productosSeleccionados) {
        productosSeleccionados.textContent = '0 seleccionados';
      }
      return;
    }
    
    let total = 0;
    let html = '';
    
    this.cart.forEach((item, productoId) => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;
      
      html += `
        <div class="pedidos-cart-item">
          <div class="flex-grow-1">
            <div class="fw-semibold text-capitalize mb-1">${this.escapeHtml(item.nombre)}</div>
            <small class="opacity-75">${item.cantidad} x $${this.formatNumber(item.precio)}</small>
          </div>
          <div class="fw-bold text-end">$${this.formatNumber(subtotal)}</div>
        </div>
      `;
    });
    
    cartItems.innerHTML = html;
    cartTotal.textContent = '$' + this.formatNumber(total);
    submitBtn.disabled = false;
    
    if (productosSeleccionados) {
      productosSeleccionados.textContent = `${this.cart.size} seleccionados`;
    }
  }

  /**
   * Ver detalles de un pedido
   */
  viewOrder(orderId) {
    this.showLoading('Cargando pedido...');
    window.location.href = `/cliente/pedidos/${orderId}`;
  }

  /**
   * Mostrar modal de cancelación
   */
  showCancelModal(orderId) {
    GlassModal.confirm({
      title: 'Cancelar Pedido',
      message: '¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.',
      icon: 'bi-exclamation-triangle-fill',
      iconColor: '#dc3545',
      iconBg: 'rgba(220, 53, 69, 0.2)',
      confirmText: 'Sí, cancelar pedido',
      cancelText: 'No, mantener',
      confirmClass: 'btn-glass-danger',
      onConfirm: () => {
        this.confirmCancel(orderId);
      }
    });
  }

  /**
   * Confirmar cancelación de pedido
   */
  confirmCancel(orderId) {
    this.showLoading('Cancelando pedido...');
    
    fetch(`/cliente/pedidos/${orderId}/cancelar`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        motivo: 'Cancelado por el cliente'
      })
    })
    .then(res => res.json())
    .then(data => {
      this.hideLoading();
      
      if (data.success) {
        GlassModal.success(
          'Pedido Cancelado',
          'El pedido ha sido cancelado exitosamente',
          () => {
            setTimeout(() => location.reload(), 500);
          }
        );
      } else {
        GlassModal.error(
          'Error al Cancelar',
          data.message || 'No se pudo cancelar el pedido. Por favor, intenta nuevamente.'
        );
      }
    })
    .catch(err => {
      this.hideLoading();
      console.error('Error al cancelar pedido:', err);
      GlassModal.error(
        'Error de Conexión',
        'Ocurrió un error al cancelar el pedido. Verifica tu conexión e intenta nuevamente.'
      );
    });
  }

  /**
   * Repetir un pedido anterior
   */
  repeatOrder(orderId) {
    GlassModal.confirm({
      title: 'Repetir Pedido',
      message: '¿Deseas cargar los productos de este pedido para realizar uno nuevo?',
      icon: 'bi-arrow-repeat',
      iconColor: '#3b82f6',
      iconBg: 'rgba(59, 130, 246, 0.2)',
      confirmText: 'Sí, repetir pedido',
      cancelText: 'Cancelar',
      confirmClass: 'btn-glass-info',
      onConfirm: () => {
        this.showLoading('Cargando productos...');
        
        fetch(`/cliente/pedidos/${orderId}`)
          .then(res => res.json())
          .then(data => {
            this.hideLoading();
            
            if (data.pedido && data.pedido.detalles) {
              // Guardar en localStorage para cargar en crear pedido
              const productos = data.pedido.detalles.map(d => ({
                id: d.producto_id,
                nombre: d.producto_nombre || 'Producto',
                precio: d.precio_unitario || 0,
                cantidad: d.cantidad || 1
              }));
              
              localStorage.setItem('carrito', JSON.stringify(productos));
              
              GlassModal.success(
                'Productos Cargados',
                'Los productos han sido cargados en tu carrito. Redirigiendo...',
                () => {
                  setTimeout(() => {
                    window.location.href = '/cliente/pedidos/create';
                  }, 500);
                }
              );
            } else {
              GlassModal.error(
                'Error',
                'No se pudo cargar los productos del pedido. Por favor, intenta nuevamente.'
              );
            }
          })
          .catch(err => {
            this.hideLoading();
            console.error('Error al repetir pedido:', err);
            GlassModal.error(
              'Error de Conexión',
              'Ocurrió un error al cargar el pedido. Verifica tu conexión e intenta nuevamente.'
            );
          });
      }
    });
  }

  /**
   * Aplicar filtros en listado de pedidos
   */
  applyFilters() {
    const estado = document.getElementById('filter-estado')?.value || '';
    const fecha = document.getElementById('filter-fecha')?.value || '';
    const busqueda = document.getElementById('filter-busqueda')?.value || '';
    
    const url = new URL(window.location.href);
    
    if (estado) url.searchParams.set('estado', estado);
    else url.searchParams.delete('estado');
    
    if (fecha) url.searchParams.set('fecha', fecha);
    else url.searchParams.delete('fecha');
    
    if (busqueda) url.searchParams.set('busqueda', busqueda);
    else url.searchParams.delete('busqueda');
    
    this.showLoading('Aplicando filtros...');
    window.location.href = url.toString();
  }

  /**
   * Limpiar todos los filtros
   */
  clearFilters() {
    const url = new URL(window.location.href);
    url.searchParams.delete('estado');
    url.searchParams.delete('fecha');
    url.searchParams.delete('busqueda');
    
    this.showLoading('Limpiando filtros...');
    window.location.href = url.toString();
  }

  /**
   * Crear modal dinámico
   */
  createModal({ id, type = 'primary', icon = 'ℹ️', title, body, footer }) {
    if (this.modals[id]) return this.modals[id];
    
    // Crear backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'pedido-modal-backdrop';
    backdrop.id = `${id}-backdrop`;
    backdrop.addEventListener('click', () => this.closeModal(id));
    
    // Crear modal
    const modal = document.createElement('div');
    modal.className = 'pedido-modal';
    modal.id = id;
    modal.innerHTML = `
      <div class="pedido-modal-content">
        <div class="pedido-modal-header">
          <div class="pedido-modal-title">
            <div class="pedido-modal-icon pedido-modal-icon-${type}">${icon}</div>
            <span>${title}</span>
          </div>
          <button class="pedido-modal-close" data-close-modal="${id}">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="pedido-modal-body">${body}</div>
        ${footer ? `<div class="pedido-modal-footer">${footer}</div>` : ''}
      </div>
    `;
    
    document.body.appendChild(backdrop);
    document.body.appendChild(modal);
    
    // Agregar listener al botón de cerrar
    modal.querySelector('[data-close-modal]').addEventListener('click', () => this.closeModal(id));
    
    this.modals[id] = { backdrop, modal };
    return this.modals[id];
  }

  /**
   * Mostrar modal
   */
  showModal(id) {
    const modalObj = this.modals[id];
    if (!modalObj) return;
    
    requestAnimationFrame(() => {
      modalObj.backdrop.classList.add('active');
      modalObj.modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }

  /**
   * Cerrar modal
   */
  closeModal(id) {
    const modalObj = this.modals[id];
    if (!modalObj) return;
    
    modalObj.backdrop.classList.remove('active');
    modalObj.modal.classList.remove('active');
    document.body.style.overflow = '';
    
    setTimeout(() => {
      modalObj.backdrop.remove();
      modalObj.modal.remove();
      delete this.modals[id];
    }, 300);
  }

  /**
   * Cerrar todos los modales
   */
  closeAllModals() {
    Object.keys(this.modals).forEach(id => this.closeModal(id));
  }

  /**
   * Mostrar notificación toast
   */
  showToast(type, title, message) {
    const toastId = `toast-${++this.toastCount}`;
    
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    
    const iconMap = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.id = toastId;
    toast.innerHTML = `
      <div class="toast-icon">${iconMap[type] || 'ℹ'}</div>
      <div class="toast-content">
        <div class="toast-title">${this.escapeHtml(title)}</div>
        <div class="toast-message">${this.escapeHtml(message)}</div>
      </div>
      <button class="toast-close" onclick="pedidosManager.closeToast('${toastId}')">
        <i class="bi bi-x-lg"></i>
      </button>
    `;
    
    container.appendChild(toast);
    
    requestAnimationFrame(() => toast.classList.add('show'));
    
    // Auto cerrar después de 5 segundos
    setTimeout(() => this.closeToast(toastId), 5000);
  }

  /**
   * Cerrar toast
   */
  closeToast(id) {
    const toast = document.getElementById(id);
    if (!toast) return;
    
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }

  /**
   * Mostrar overlay de carga
   */
  showLoading(text = 'Cargando...') {
    let overlay = document.querySelector('.pedidos-loading-overlay');
    
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'pedidos-loading-overlay';
      overlay.innerHTML = `
        <div class="pedidos-loading-content">
          <div class="pedidos-loading-spinner"></div>
          <div class="pedidos-loading-text">${text}</div>
        </div>
      `;
      document.body.appendChild(overlay);
    } else {
      const textEl = overlay.querySelector('.pedidos-loading-text');
      if (textEl) textEl.textContent = text;
    }
    
    requestAnimationFrame(() => overlay.classList.add('active'));
  }

  /**
   * Ocultar overlay de carga
   */
  hideLoading() {
    const overlay = document.querySelector('.pedidos-loading-overlay');
    if (!overlay) return;
    
    overlay.classList.remove('active');
    setTimeout(() => {
      if (!overlay.classList.contains('active')) {
        overlay.remove();
      }
    }, 300);
  }

  /**
   * Verificar estado de PWA
   */
  checkPWAStatus() {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.getRegistration()
        .then(registration => {
          if (registration) {
            console.log('✅ PWA activo - Service Worker registrado');
          }
        })
        .catch(err => {
          console.log('ℹ️ PWA no disponible:', err);
        });
    }
  }

  /**
   * Formatear número como moneda colombiana
   */
  formatNumber(num) {
    return new Intl.NumberFormat('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(num);
  }

  /**
   * Formatear moneda completa
   */
  formatCurrency(amount) {
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(amount);
  }

  /**
   * Formatear fecha
   */
  formatDate(date) {
    return new Intl.DateTimeFormat('es-CO', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date(date));
  }

  /**
   * Escapar HTML para prevenir XSS
   */
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Agregar producto al carrito (público)
   */
  addToCart(productoId, nombre, precio, cantidad = 1) {
    this.cart.set(productoId, {
      nombre: nombre,
      precio: precio,
      cantidad: cantidad
    });
    
    this.updateCart();
    this.showToast('success', 'Producto agregado', `${nombre} agregado al carrito`);
  }

  /**
   * Eliminar producto del carrito (público)
   */
  removeFromCart(productoId) {
    if (this.cart.has(productoId)) {
      const producto = this.cart.get(productoId);
      this.cart.delete(productoId);
      this.updateCart();
      
      // Desmarcar checkbox si existe
      const checkbox = document.querySelector(`input[type="checkbox"][value="${productoId}"]`);
      if (checkbox) {
        checkbox.checked = false;
        const container = checkbox.closest('.pedidos-product-checkbox');
        if (container) {
          container.classList.remove('selected');
          const qtyDiv = container.querySelector('.pedidos-product-quantity');
          if (qtyDiv) qtyDiv.style.display = 'none';
        }
      }
      
      this.showToast('info', 'Producto eliminado', `${producto.nombre} eliminado del carrito`);
    }
  }

  /**
   * Actualizar cantidad de producto
   */
  updateQuantity(productoId, cantidad) {
    if (this.cart.has(productoId)) {
      const item = this.cart.get(productoId);
      item.cantidad = Math.max(1, parseInt(cantidad));
      this.cart.set(productoId, item);
      this.updateCart();
    }
  }

  /**
   * Vaciar carrito
   */
  clearCart() {
    this.cart.clear();
    this.updateCart();
    
    // Desmarcar todos los checkboxes
    document.querySelectorAll('.producto-checkbox:checked').forEach(checkbox => {
      checkbox.checked = false;
      const container = checkbox.closest('.pedidos-product-checkbox');
      if (container) {
        container.classList.remove('selected');
        const qtyDiv = container.querySelector('.pedidos-product-quantity');
        if (qtyDiv) qtyDiv.style.display = 'none';
      }
    });
    
    this.showToast('info', 'Carrito vaciado', 'Todos los productos han sido eliminados');
  }

  /**
   * Obtener total del carrito
   */
  getCartTotal() {
    let total = 0;
    this.cart.forEach(item => {
      total += item.precio * item.cantidad;
    });
    return total;
  }

  /**
   * Obtener cantidad de items en el carrito
   */
  getCartItemCount() {
    return this.cart.size;
  }

  /**
   * Confirmar pedido (redireccionar a checkout si es necesario)
   */
  confirmOrder() {
    if (this.cart.size === 0) {
      this.showToast('warning', 'Carrito vacío', 'Debes seleccionar al menos un producto');
      return false;
    }
    
    const form = document.getElementById('crearPedidoForm');
    if (form) {
      // Validar campos requeridos
      const direccion = document.getElementById('direccion_entrega');
      const telefono = document.getElementById('telefono_entrega');
      const metodoPago = document.getElementById('metodo_pago');
      
      let isValid = true;
      
      if (!direccion || !direccion.value.trim()) {
        this.showToast('warning', 'Campo requerido', 'La dirección de entrega es obligatoria');
        direccion?.focus();
        isValid = false;
      } else if (!telefono || !telefono.value.trim()) {
        this.showToast('warning', 'Campo requerido', 'El teléfono de contacto es obligatorio');
        telefono?.focus();
        isValid = false;
      } else if (!metodoPago || !metodoPago.value) {
        this.showToast('warning', 'Campo requerido', 'Debes seleccionar un método de pago');
        metodoPago?.focus();
        isValid = false;
      }
      
      if (isValid) {
        this.showLoading('Procesando tu pedido...');
        form.submit();
      }
      
      return isValid;
    }
    
    return true;
  }
}

// ========================================
// INSTANCIA GLOBAL Y EXPORTS
// ========================================

// Crear instancia global al cargar el DOM
let pedidosManager;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    pedidosManager = new PedidosClienteManager();
    window.pedidosManager = pedidosManager;
    console.log('✅ PedidosClienteManager inicializado');
  });
} else {
  pedidosManager = new PedidosClienteManager();
  window.pedidosManager = pedidosManager;
  console.log('✅ PedidosClienteManager inicializado');
}

// Export para módulos ES6 (opcional)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = PedidosClienteManager;
}