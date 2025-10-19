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
    this.sincronizarFavoritosConDOM(); // Sincronizar con favoritos renderizados
    this.loadFavoritos();
    this.actualizarContadorFavoritos(); // Actualizar contador al inicio
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
  
  agregarAlCarrito(productoId, nombre, precio, imagen = null, stock = null) {
    // Validar si hay stock disponible
    if (stock !== null && stock <= 0) {
      this.showToast('Producto agotado', 'warning');
      return;
    }

    const existente = this.carrito.find(item => item.id === productoId);
    
    if (existente) {
      // Validar que no se exceda el stock
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

  vaciarCarrito() {
    if (this.carrito.length === 0) return;
    
    this.createModal('warning', '⚠️ Vaciar Carrito', `
      <div class="text-center py-3">
        <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3 d-block"></i>
        <p class="mb-0">¿Estás seguro de que deseas vaciar tu carrito?</p>
        <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
      </div>
    `, [
      { text: 'Cancelar', type: 'outline-secondary', onclick: 'clienteDashboard.closeAllModals()' },
      { text: 'Vaciar Carrito', type: 'danger', icon: 'trash', onclick: 'clienteDashboard.confirmarVaciarCarrito()' }
    ]);
  }

  confirmarVaciarCarrito() {
    this.carrito = [];
    this.guardarCarrito();
    this.updateCarritoCount();
    this.renderCarrito();
    this.closeAllModals();
    this.showToast('Carrito vaciado', 'info');
  }

  actualizarCantidad(productoId, cantidad) {
    const item = this.carrito.find(item => item.id === productoId);
    if (item) {
      if (cantidad <= 0) {
        this.eliminarDelCarrito(productoId);
      } else {
        // Validar stock si está disponible
        if (item.stock !== null && cantidad > item.stock) {
          this.showToast(`Stock máximo disponible: ${item.stock}`, 'warning');
          return;
        }
        item.cantidad = cantidad;
        this.guardarCarrito();
        this.updateCarritoCount();
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
      badge.style.display = count > 0 ? 'inline-block' : 'none';
    });
  }

  toggleCarrito() {
    const sidebar = document.getElementById('carritoSidebar');
    const backdrop = document.getElementById('carritoBackdrop');
    if (sidebar) {
      sidebar.classList.toggle('active');
      if (backdrop) {
        backdrop.classList.toggle('active');
      }
      if (sidebar.classList.contains('active')) {
        this.renderCarrito();
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  }

  closeCarrito() {
    const sidebar = document.getElementById('carritoSidebar');
    const backdrop = document.getElementById('carritoBackdrop');
    if (sidebar) {
      sidebar.classList.remove('active');
    }
    if (backdrop) {
      backdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  }

  renderCarrito() {
    const container = document.getElementById('carritoItems');
    const totalEl = document.getElementById('carritoTotal');
    const btnVaciar = document.getElementById('btnVaciarCarrito');
    const btnConfirmar = document.getElementById('btnConfirmarPedido');
    
    if (!container) return;

    if (this.carrito.length === 0) {
      container.innerHTML = `
        <div class="text-center py-5">
          <i class="bi bi-cart-x fs-1 text-muted"></i>
          <p class="text-muted mt-3">Tu carrito está vacío</p>
        </div>
      `;
      if (totalEl) totalEl.textContent = '$0';
      if (btnVaciar) btnVaciar.style.display = 'none';
      if (btnConfirmar) btnConfirmar.disabled = true;
      return;
    }

    // Mostrar botón de vaciar cuando hay items
    if (btnVaciar) btnVaciar.style.display = 'block';
    if (btnConfirmar) btnConfirmar.disabled = false;

    let total = 0;
    container.innerHTML = this.carrito.map(item => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;
      const stockDisponible = item.stock !== null && item.stock !== undefined;
      const sinStock = stockDisponible && item.cantidad >= item.stock;
      const stockBajo = stockDisponible && item.stock <= 5 && item.cantidad < item.stock;
      
      return `
        <div class="carrito-item ${sinStock ? 'border-warning' : ''}">
          <div class="carrito-item-image">
            ${item.imagen ? 
              `<img src="/storage/${item.imagen}" alt="${item.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">` :
              `<i class="bi bi-egg-fried fs-4 text-muted"></i>`
            }
          </div>
          <div class="carrito-item-info">
            <div class="fw-bold small">${item.nombre}</div>
            <div class="text-success small">$${this.formatPrice(item.precio)} c/u</div>
            ${stockDisponible ? `
              <div class="small ${stockBajo ? 'text-warning' : 'text-muted'}">
                <i class="bi bi-box-seam"></i> Stock: ${item.stock}
              </div>
            ` : ''}
            ${sinStock ? `
              <div class="text-danger small fw-bold">
                <i class="bi bi-exclamation-triangle"></i> Máximo alcanzado
              </div>
            ` : ''}
            ${stockBajo && !sinStock ? `
              <div class="text-warning small">
                <i class="bi bi-exclamation-circle"></i> Pocas unidades
              </div>
            ` : ''}
            <div class="text-muted small fw-bold mt-1">Subtotal: $${this.formatPrice(subtotal)}</div>
            <div class="d-flex align-items-center gap-2 mt-2">
              <button class="btn btn-sm btn-outline-secondary" 
                      onclick="clienteDashboard.actualizarCantidad('${item.id}', ${item.cantidad - 1})" 
                      title="Disminuir cantidad">
                <i class="bi bi-dash"></i>
              </button>
              <span class="fw-bold px-2 small" style="min-width: 30px; text-align: center;">${item.cantidad}</span>
              <button class="btn btn-sm btn-outline-secondary ${sinStock ? 'disabled' : ''}" 
                      onclick="clienteDashboard.actualizarCantidad('${item.id}', ${item.cantidad + 1})" 
                      title="${sinStock ? 'Stock máximo alcanzado' : 'Aumentar cantidad'}"
                      ${sinStock ? 'disabled' : ''}>
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          <button class="btn btn-sm btn-outline-danger" 
                  onclick="clienteDashboard.eliminarDelCarrito('${item.id}')" 
                  title="Eliminar del carrito">
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

  toggleFavorito(productoId, nombre = '', precio = 0, imagen = null, stock = null) {
    const idx = this.favoritos.findIndex(f => f.id === productoId);
    const btn = document.querySelector(`[onclick*="toggleFavorito('${productoId}')"]`);
    
    if (idx > -1) {
      this.favoritos.splice(idx, 1);
      if (btn) {
        btn.classList.remove('active');
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('bi-heart-fill');
          icon.classList.add('bi-heart');
        }
      }
      this.showToast('Eliminado de favoritos', 'info');
      
      // Sincronizar con backend
      this.syncFavoritoBackend(productoId, 'eliminar');
      
      // Actualizar sidebar de favoritos
      this.actualizarSidebarFavoritos();
    } else {
      this.favoritos.push({ id: productoId, nombre, precio, imagen, stock });
      if (btn) {
        btn.classList.add('active');
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('bi-heart');
          icon.classList.add('bi-heart-fill');
        }
      }
      this.showToast('Agregado a favoritos', 'success');
      
      // Sincronizar con backend
      this.syncFavoritoBackend(productoId, 'agregar');
      
      // Actualizar sidebar de favoritos
      this.actualizarSidebarFavoritos();
    }
    
    this.guardarFavoritos();
  }

  /**
   * Actualizar el sidebar de favoritos dinámicamente
   */
  actualizarSidebarFavoritos() {
    // Buscar el contenedor correcto - puede tener alerts de debug
    let container = document.querySelector('.card-header:has(.bi-heart-fill)')?.parentElement?.querySelector('.card-body:last-of-type');
    
    // Si no lo encuentra, buscar de forma alternativa
    if (!container) {
      const card = document.querySelector('.card-header:has(.bi-heart-fill)')?.closest('.card');
      if (card) {
        const cardBodies = card.querySelectorAll('.card-body');
        container = cardBodies[cardBodies.length - 1]; // Último card-body
      }
    }
    
    if (!container) {
      console.warn('No se encontró el contenedor de favoritos');
      return;
    }

    // Actualizar contador en el header
    this.actualizarContadorFavoritos();

    if (this.favoritos.length === 0) {
      container.innerHTML = `
        <div class="text-center py-3">
          <i class="bi bi-heart fs-3 text-muted"></i>
          <p class="text-muted mb-2">No tienes productos favoritos</p>
          <button class="btn btn-sm btn-primary" onclick="document.getElementById('buscarProducto').scrollIntoView({behavior:'smooth'})">
            Explorar productos
          </button>
        </div>
      `;
      return;
    }

    // Mostrar solo los primeros 3
    const favoritosVisible = this.favoritos.slice(0, 3);
    const totalFavoritos = this.favoritos.length;

    let html = '';
    favoritosVisible.forEach((fav, index) => {
      const stockColor = fav.stock <= 0 ? 'text-danger' : (fav.stock <= 5 ? 'text-warning' : 'text-muted');
      const stockText = fav.stock !== null && fav.stock !== undefined ? `
        <small class="d-block ${stockColor}">
          <i class="bi bi-box-seam"></i> Stock: ${fav.stock}
        </small>
      ` : '';
      
      const borderClass = index < favoritosVisible.length - 1 ? 'border-bottom' : '';

      html += `
        <div class="d-flex align-items-center py-2 ${borderClass}" data-favorito-id="${fav.id}">
          <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-heart-fill"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-medium">${fav.nombre}</div>
            <small class="text-muted">$${this.formatPrice(fav.precio)}</small>
            ${stockText}
          </div>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-primary ${fav.stock <= 0 ? 'disabled' : ''}" 
                    onclick="agregarAlCarritoFromFavorito('${fav.id}')"
                    data-producto-id="${fav.id}"
                    data-nombre="${fav.nombre}"
                    data-precio="${fav.precio}"
                    data-imagen="${fav.imagen || ''}"
                    data-stock="${fav.stock || 0}"
                    ${fav.stock <= 0 ? 'disabled' : ''}
                    title="${fav.stock <= 0 ? 'Producto agotado' : 'Agregar al carrito'}">
              <i class="bi bi-cart-plus"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
                    onclick="eliminarFavorito('${fav.id}')"
                    title="Quitar de favoritos">
              <i class="bi bi-heart-fill"></i>
            </button>
          </div>
        </div>
      `;
    });

    // Agregar botón "Ver todos" si hay más de 3
    if (totalFavoritos > 3) {
      html += `
        <div class="text-center mt-2">
          <button class="btn btn-sm btn-outline-primary" onclick="clienteDashboard.mostrarTodosFavoritos()">
            Ver todos (${totalFavoritos})
          </button>
        </div>
      `;
    }

    container.innerHTML = html;
  }

  /**
   * Actualizar contador de favoritos en el header
   */
  actualizarContadorFavoritos() {
    const totalFavoritos = this.favoritos.length;
    
    // Actualizar badge en el sidebar
    const header = document.querySelector('.card-header:has(.bi-heart-fill) h6');
    
    if (header) {
      // Buscar si ya existe un badge de contador
      let badge = header.querySelector('.badge');
      
      if (totalFavoritos > 0) {
        if (!badge) {
          // Crear badge si no existe
          badge = document.createElement('span');
          badge.className = 'badge bg-primary ms-2';
          header.appendChild(badge);
        }
        badge.textContent = totalFavoritos;
        badge.style.display = 'inline-block';
      } else {
        // Ocultar badge si no hay favoritos
        if (badge) {
          badge.style.display = 'none';
        }
      }
    }
    
    // Actualizar contador en las métricas superiores
    const metricCounter = document.getElementById('contadorFavoritosMetric');
    if (metricCounter) {
      metricCounter.textContent = totalFavoritos;
    }
  }

  /**
   * Mostrar modal con todos los favoritos
   */
  mostrarTodosFavoritos() {
    if (this.favoritos.length === 0) {
      this.showToast('No tienes productos favoritos', 'info');
      return;
    }

    // Cerrar cualquier modal abierto antes de abrir este
    this.closeAllModals();
    
    // Esperar un poco para que se cierre el modal anterior
    setTimeout(() => {
      let favoritosHtml = '';
      
      this.favoritos.forEach((fav) => {
        const stockColor = fav.stock <= 0 ? 'danger' : (fav.stock <= 5 ? 'warning' : 'secondary');
        const stockText = fav.stock !== null && fav.stock !== undefined ? `
          <span class="badge bg-${stockColor} me-2">
            <i class="bi bi-box-seam"></i> Stock: ${fav.stock}
          </span>
        ` : '';

        favoritosHtml += `
          <div class="d-flex align-items-center p-3 border-bottom hover-bg-light" data-favorito-modal-id="${fav.id}">
            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="bi bi-heart-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-bold">${fav.nombre}</div>
              <div class="text-success fw-bold mt-1">$${this.formatPrice(fav.precio)}</div>
              <div class="mt-1">
                ${stockText}
              </div>
            </div>
            <div class="d-flex flex-column gap-2">
              <button class="btn btn-sm btn-primary ${fav.stock <= 0 ? 'disabled' : ''}" 
                      onclick="event.stopPropagation(); clienteDashboard.agregarAlCarritoDesdeModal('${fav.id}')"
                      ${fav.stock <= 0 ? 'disabled' : ''}
                      title="${fav.stock <= 0 ? 'Producto agotado' : 'Agregar al carrito'}">
                <i class="bi bi-cart-plus me-1"></i> Agregar
              </button>
              <button class="btn btn-sm btn-outline-danger" 
                      onclick="event.stopPropagation(); clienteDashboard.eliminarFavoritoDesdeModal('${fav.id}')"
                      title="Quitar de favoritos">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </div>
          </div>
        `;
      });

      this.createModal('info', `❤️ Mis Favoritos (${this.favoritos.length})`, `
        <div class="favoritos-list" style="max-height: 500px; overflow-y: auto;">
          ${favoritosHtml}
        </div>
        <div class="mt-3 p-3 bg-light rounded">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <strong>Total de productos favoritos:</strong>
              <span class="badge bg-primary ms-2">${this.favoritos.length}</span>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="clienteDashboard.confirmarVaciarFavoritos()">
              <i class="bi bi-trash me-1"></i> Vaciar todos
            </button>
          </div>
        </div>
      `, [
        { text: 'Cerrar', type: 'secondary', onclick: 'clienteDashboard.closeAllModals()' }
      ]);
    }, 100);
  }

  /**
   * Agregar al carrito desde el modal de favoritos
   */
  agregarAlCarritoDesdeModal(productoId) {
    const fav = this.favoritos.find(f => f.id === productoId);
    if (fav) {
      this.agregarAlCarrito(productoId, fav.nombre, fav.precio, fav.imagen, fav.stock);
      
      // Actualizar el botón en el modal para dar feedback
      const btnModal = document.querySelector(`[onclick*="agregarAlCarritoDesdeModal('${productoId}')"]`);
      if (btnModal) {
        const originalText = btnModal.innerHTML;
        btnModal.innerHTML = '<i class="bi bi-check-circle me-1"></i> Agregado';
        btnModal.classList.add('btn-success');
        btnModal.classList.remove('btn-primary');
        
        // Restaurar después de 2 segundos
        setTimeout(() => {
          btnModal.innerHTML = originalText;
          btnModal.classList.remove('btn-success');
          btnModal.classList.add('btn-primary');
        }, 2000);
      }
    }
  }

  /**
   * Eliminar favorito desde el modal
   */
  eliminarFavoritoDesdeModal(productoId) {
    this.eliminarFavoritoDirecto(productoId);
    
    // Actualizar el modal
    const element = document.querySelector(`[data-favorito-modal-id="${productoId}"]`);
    if (element) {
      element.style.opacity = '0';
      element.style.transform = 'translateX(-20px)';
      setTimeout(() => {
        element.remove();
        
        // Si no quedan favoritos, cerrar modal y actualizar sidebar
        if (this.favoritos.length === 0) {
          this.closeAllModals();
          this.actualizarSidebarFavoritos();
        } else {
          // Actualizar contador en el título del modal
          const modalTitle = document.querySelector('.modal-container h5');
          if (modalTitle) {
            modalTitle.textContent = `❤️ Mis Favoritos (${this.favoritos.length})`;
          }
          
          // Actualizar badge de total
          const totalBadge = document.querySelector('.favoritos-list').nextElementSibling.querySelector('.badge');
          if (totalBadge) {
            totalBadge.textContent = this.favoritos.length;
          }
        }
      }, 300);
    }
  }

  /**
   * Confirmar vaciar todos los favoritos
   */
  confirmarVaciarFavoritos() {
    this.createModal('warning', '⚠️ Vaciar Favoritos', `
      <div class="text-center py-3">
        <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3 d-block"></i>
        <p class="mb-0">¿Estás seguro de que deseas eliminar TODOS tus productos favoritos?</p>
        <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
      </div>
    `, [
      { text: 'Cancelar', type: 'outline-secondary', onclick: 'clienteDashboard.mostrarTodosFavoritos()' },
      { text: 'Vaciar Todo', type: 'danger', icon: 'trash', onclick: 'clienteDashboard.ejecutarVaciarFavoritos()' }
    ]);
  }

  /**
   * Ejecutar vaciar todos los favoritos
   */
  ejecutarVaciarFavoritos() {
    // Guardar IDs para sincronizar con backend
    const favoritosIds = [...this.favoritos.map(f => f.id)];
    
    // Vaciar array
    this.favoritos = [];
    this.guardarFavoritos();
    
    // Sincronizar con backend (eliminar cada uno)
    favoritosIds.forEach(id => {
      this.syncFavoritoBackend(id, 'eliminar');
    });
    
    // Actualizar todos los botones de corazón en el catálogo
    document.querySelectorAll('.btn-favorito.active').forEach(btn => {
      btn.classList.remove('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
      }
    });
    
    // Actualizar sidebar
    this.actualizarSidebarFavoritos();
    
    // Cerrar modales
    this.closeAllModals();
    
    this.showToast('Todos los favoritos han sido eliminados', 'info');
  }

  /**
   * Sincronizar favorito con el backend
   */
  syncFavoritoBackend(productoId, action) {
    const url = action === 'agregar' ? '/cliente/favoritos/agregar' : '/cliente/favoritos/eliminar';
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!token) {
      console.warn('No se encontró token CSRF, favorito no sincronizado con BD');
      return;
    }

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        producto_id: productoId
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        console.log('Favorito sincronizado con BD:', data.message);
      } else {
        console.warn('Error al sincronizar favorito:', data.message);
      }
    })
    .catch(error => {
      console.error('Error de red al sincronizar favorito:', error);
    });
  }

  guardarFavoritos() {
    localStorage.setItem('favoritos', JSON.stringify(this.favoritos));
  }

  loadFavoritos() {
    this.favoritos.forEach(fav => {
      const btn = document.querySelector(`[onclick*="toggleFavorito('${fav.id}')"]`);
      if (btn) {
        btn.classList.add('active');
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('bi-heart');
          icon.classList.add('bi-heart-fill');
        }
      }
    });
  }

  /**
   * Sincronizar favoritos con los elementos del DOM ya renderizados
   * Esto se ejecuta al cargar la página cuando ya hay favoritos en la BD
   */
  sincronizarFavoritosConDOM() {
    // Buscar todos los elementos de favoritos ya renderizados en el sidebar
    const favoritosEnDOM = document.querySelectorAll('[data-favorito-id]');
    
    if (favoritosEnDOM.length > 0) {
      console.log('Sincronizando favoritos con DOM...', favoritosEnDOM.length);
      
      favoritosEnDOM.forEach(elemento => {
        const id = elemento.dataset.favoritoId;
        
        // Obtener datos del producto desde los atributos del botón de agregar al carrito
        const btnCarrito = elemento.querySelector('[data-producto-id]');
        if (btnCarrito) {
          const nombre = btnCarrito.dataset.nombre || 'Producto';
          const precio = parseFloat(btnCarrito.dataset.precio) || 0;
          const imagen = btnCarrito.dataset.imagen || null;
          const stock = btnCarrito.dataset.stock ? parseInt(btnCarrito.dataset.stock) : null;
          
          // Verificar si ya está en favoritos localStorage
          const existe = this.favoritos.find(f => f.id === id);
          if (!existe) {
            // Agregar a localStorage
            this.favoritos.push({
              id: id,
              nombre: nombre,
              precio: precio,
              imagen: imagen,
              stock: stock
            });
          } else {
            // Actualizar datos si ya existe (por si cambió precio o stock)
            existe.nombre = nombre;
            existe.precio = precio;
            existe.imagen = imagen;
            existe.stock = stock;
          }
        }
      });
      
      // Guardar favoritos sincronizados
      this.guardarFavoritos();
      
      // Marcar botones de corazón en el catálogo
      this.favoritos.forEach(fav => {
        const btn = document.querySelector(`[onclick*="toggleFavorito('${fav.id}')"]`);
        if (btn) {
          btn.classList.add('active');
          const icon = btn.querySelector('i');
          if (icon) {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
          }
        }
      });
      
      console.log('Favoritos sincronizados:', this.favoritos.length);
    }
  }

  eliminarFavoritoDirecto(productoId) {
    const idx = this.favoritos.findIndex(f => f.id === productoId);
    if (idx > -1) {
      const nombre = this.favoritos[idx].nombre;
      this.favoritos.splice(idx, 1);
      this.guardarFavoritos();
      
      // Sincronizar con backend
      this.syncFavoritoBackend(productoId, 'eliminar');
      
      // Actualizar botón en el catálogo si existe
      const btn = document.querySelector(`[onclick*="toggleFavorito('${productoId}')"]`);
      if (btn) {
        btn.classList.remove('active');
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('bi-heart-fill');
          icon.classList.add('bi-heart');
        }
      }
      
      // Remover de la lista de favoritos en el sidebar
      const favoritoElement = document.querySelector(`[data-favorito-id="${productoId}"]`);
      if (favoritoElement) {
        favoritoElement.style.opacity = '0';
        favoritoElement.style.transform = 'translateX(-20px)';
        setTimeout(() => {
          favoritoElement.remove();
          
          // Si no quedan favoritos, mostrar mensaje
          const favoritosContainer = document.querySelector('.card-body');
          if (favoritosContainer && !favoritosContainer.querySelector('[data-favorito-id]')) {
            favoritosContainer.innerHTML = `
              <div class="text-center py-3">
                <i class="bi bi-heart fs-3 text-muted"></i>
                <p class="text-muted mb-2">No tienes productos favoritos</p>
                <button class="btn btn-sm btn-primary" onclick="document.getElementById('buscarProducto').scrollIntoView({behavior:'smooth'})">
                  Explorar productos
                </button>
              </div>
            `;
          }
        }, 300);
      }
      
      this.showToast(`${nombre} eliminado de favoritos`, 'info');
    }
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

    // Validar que todos los productos tengan stock suficiente
    let hayProblemasStock = false;
    const itemsHtml = this.carrito.map(item => {
      const stockDisponible = item.stock !== null && item.stock !== undefined;
      const sinStock = stockDisponible && item.cantidad > item.stock;
      if (sinStock) hayProblemasStock = true;

      return `
        <div class="d-flex justify-content-between py-2 border-bottom ${sinStock ? 'bg-danger bg-opacity-10' : ''}">
          <div>
            <div class="fw-bold">${item.nombre}</div>
            <small class="text-muted">Cantidad: ${item.cantidad}</small>
            ${stockDisponible ? `<small class="text-muted d-block">Stock disponible: ${item.stock}</small>` : ''}
            ${sinStock ? `<small class="text-danger d-block"><i class="bi bi-exclamation-triangle"></i> Supera el stock disponible</small>` : ''}
          </div>
          <div class="text-success fw-bold">$${this.formatPrice(item.precio * item.cantidad)}</div>
        </div>
      `;
    }).join('');

    const total = this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

    this.createModal('success', '🛒 Confirmar Pedido', `
      <div>
        <h6 class="mb-3">Resumen de tu pedido:</h6>
        ${hayProblemasStock ? `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Atención:</strong> Algunos productos superan el stock disponible. Por favor ajusta las cantidades.
          </div>
        ` : ''}
        ${itemsHtml}
        <div class="d-flex justify-content-between py-3 border-top mt-3">
          <strong>Total a pagar:</strong>
          <strong class="text-success fs-5">$${this.formatPrice(total)}</strong>
        </div>
        <div class="alert alert-info mt-3 mb-0">
          <i class="bi bi-info-circle me-2"></i>
          <small>${hayProblemasStock ? 'Ajusta las cantidades antes de continuar.' : 'Serás redirigido a completar la información de entrega.'}</small>
        </div>
      </div>
    `, [
      { text: 'Cancelar', type: 'outline-secondary', onclick: 'clienteDashboard.closeAllModals()' },
      { text: hayProblemasStock ? 'Ajustar Cantidades' : 'Continuar', type: hayProblemasStock ? 'warning' : 'success', icon: hayProblemasStock ? 'pencil' : 'arrow-right-circle', onclick: hayProblemasStock ? 'clienteDashboard.closeAllModals()' : 'clienteDashboard.irACrearPedido()' }
    ]);
  }

  irACrearPedido() {
    // Guardar carrito en localStorage para usarlo en la página de crear pedido
    this.guardarCarrito();
    this.closeAllModals();
    this.showLoading('Preparando tu pedido...');
    
    // Redirigir a la página de crear pedido
    setTimeout(() => {
      window.location.href = '/cliente/pedidos/create';
    }, 500);
  }

  procesarPedido() {
    // Método legacy - ahora redirige a crear pedido
    this.irACrearPedido();
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
  const productoCard = document.querySelector(`[data-producto-id="${id}"]`);
  
  if (productoCard) {
    const btn = productoCard.querySelector('.btn-primary');
    const nombre = btn.dataset.nombre || 'Producto';
    const precio = parseFloat(btn.dataset.precio) || 0;
    const imagen = btn.dataset.imagen || null;
    const stock = btn.dataset.stock ? parseInt(btn.dataset.stock) : null;
    
    clienteDashboard.agregarAlCarrito(id, nombre, precio, imagen, stock);
  }
}

function toggleFavorito(id) {
  const producto = document.querySelector(`[onclick*="toggleFavorito('${id}')"]`)?.closest('.producto-card');
  if (producto) {
    const nombre = producto.querySelector('.card-title')?.textContent || '';
    const precioText = producto.querySelector('.precio-producto .fw-bold')?.textContent || '$0';
    const precio = parseInt(precioText.replace(/\D/g, ''));
    
    // Obtener imagen y stock del botón de agregar al carrito
    const btnCarrito = producto.querySelector(`[data-producto-id="${id}"]`);
    const imagen = btnCarrito?.dataset.imagen || null;
    const stock = btnCarrito?.dataset.stock ? parseInt(btnCarrito.dataset.stock) : null;
    
    clienteDashboard.toggleFavorito(id, nombre, precio, imagen, stock);
  }
}

function eliminarFavorito(id) {
  clienteDashboard.eliminarFavoritoDirecto(id);
}

function agregarAlCarritoFromFavorito(id) {
  const btn = document.querySelector(`[onclick*="agregarAlCarritoFromFavorito('${id}')"]`);
  if (btn) {
    const nombre = btn.dataset.nombre || 'Producto';
    const precio = parseFloat(btn.dataset.precio) || 0;
    const imagen = btn.dataset.imagen || null;
    const stock = btn.dataset.stock ? parseInt(btn.dataset.stock) : null;
    
    clienteDashboard.agregarAlCarrito(id, nombre, precio, imagen, stock);
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

function mostrarEditarPerfil() {
  const user = {
    nombre: document.querySelector('[class*="fw-medium"]')?.textContent || '',
    email: '{{ auth()->user()->email }}',
    telefono: '{{ auth()->user()->telefono ?? "" }}',
    direccion: '{{ auth()->user()->direccion ?? "" }}',
    ciudad: '{{ auth()->user()->ciudad ?? "" }}'
  };

  const modalContent = `
    <form id="formEditarPerfil" onsubmit="return false;">
      <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="tel" class="form-control" id="editTelefono" value="${user.telefono}" placeholder="+57 300 123 4567">
      </div>
      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" class="form-control" id="editDireccion" value="${user.direccion}" placeholder="Calle 123 # 45-67">
      </div>
      <div class="mb-3">
        <label class="form-label">Ciudad</label>
        <input type="text" class="form-control" id="editCiudad" value="${user.ciudad}" placeholder="Bogotá">
      </div>
      <div class="alert alert-info small mb-0">
        <i class="bi bi-info-circle me-2"></i>
        Para cambiar tu nombre o email, contacta con soporte.
      </div>
    </form>
  `;

  clienteDashboard.createModal('primary', '✏️ Actualizar Mi Información', modalContent, [
    { text: 'Cancelar', type: 'outline-secondary', onclick: 'clienteDashboard.closeAllModals()' },
    { text: 'Guardar Cambios', type: 'primary', icon: 'check-circle', onclick: 'guardarCambiosPerfil()' }
  ]);
}

function guardarCambiosPerfil() {
  const telefono = document.getElementById('editTelefono')?.value || '';
  const direccion = document.getElementById('editDireccion')?.value || '';
  const ciudad = document.getElementById('editCiudad')?.value || '';

  clienteDashboard.showLoading('Guardando cambios...');

  fetch('/cliente/perfil/actualizar', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({
      telefono: telefono,
      direccion: direccion,
      ciudad: ciudad
    })
  })
  .then(response => response.json())
  .then(data => {
    clienteDashboard.hideLoading();
    clienteDashboard.closeAllModals();
    
    if (data.success) {
      clienteDashboard.showToast('Información actualizada correctamente', 'success');
      
      // Recargar página para mostrar cambios
      setTimeout(() => {
        window.location.reload();
      }, 1500);
    } else {
      clienteDashboard.showToast(data.message || 'Error al actualizar', 'error');
    }
  })
  .catch(error => {
    clienteDashboard.hideLoading();
    clienteDashboard.showToast('Error al conectar con el servidor', 'error');
  });
}

// PWA: Registrar Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {
      console.log('Service Worker registration failed');
    });
  });
}

/**
 * PARCHE PARA SISTEMA DE FAVORITOS
 * Agregar este código al final del archivo cliente-dashboard-modern.js
 * o reemplazar las funciones existentes
 */

// SOBRESCRIBIR método toggleFavorito
ClienteDashboardManager.prototype.toggleFavorito = function(productoId, nombre = '', precio = 0, imagen = null, stock = null) {
  const idx = this.favoritos.findIndex(f => f.id === productoId);
  const btn = document.querySelector(`[onclick*="toggleFavorito('${productoId}')"]`);
  
  if (idx > -1) {
    // ELIMINAR de favoritos
    this.favoritos.splice(idx, 1);
    if (btn) {
      btn.classList.remove('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
      }
    }
    this.showToast('Eliminado de favoritos', 'info');
    this.syncFavoritoBackend(productoId, 'eliminar');
  } else {
    // AGREGAR a favoritos
    this.favoritos.push({ id: productoId, nombre, precio, imagen, stock });
    if (btn) {
      btn.classList.add('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
      }
    }
    this.showToast('Agregado a favoritos', 'success');
    this.syncFavoritoBackend(productoId, 'agregar');
  }
  
  // CRÍTICO: Actualizar TODO después de cada cambio
  this.guardarFavoritos();
  this.actualizarContadorFavoritos();
  this.actualizarSidebarFavoritos();
};

// SOBRESCRIBIR método actualizarContadorFavoritos
ClienteDashboardManager.prototype.actualizarContadorFavoritos = function() {
  const totalFavoritos = this.favoritos.length;
  
  // 1. Actualizar badge en el sidebar
  const header = document.querySelector('.card-header:has(.bi-heart-fill) h6');
  if (header) {
    let badge = header.querySelector('.badge');
    
    if (totalFavoritos > 0) {
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'badge bg-primary ms-2';
        header.appendChild(badge);
      }
      badge.textContent = totalFavoritos;
      badge.style.display = 'inline-block';
      badge.classList.add('pulse');
      setTimeout(() => badge.classList.remove('pulse'), 1000);
    } else {
      if (badge) {
        badge.style.display = 'none';
      }
    }
  }
  
  // 2. Actualizar contador en las métricas superiores
  const metricCounter = document.getElementById('contadorFavoritosMetric');
  if (metricCounter) {
    metricCounter.textContent = totalFavoritos;
    // Agregar animación de actualización
    metricCounter.classList.add('animate-bounce');
    setTimeout(() => {
      metricCounter.classList.remove('animate-bounce');
    }, 500);
  }

  // 3. Actualizar TODOS los contadores de favoritos en la página
  document.querySelectorAll('[data-favoritos-count]').forEach(counter => {
    counter.textContent = totalFavoritos;
  });

  console.log('✅ Contador actualizado:', totalFavoritos);
};

// SOBRESCRIBIR método eliminarFavoritoDirecto
ClienteDashboardManager.prototype.eliminarFavoritoDirecto = function(productoId) {
  const idx = this.favoritos.findIndex(f => f.id === productoId);
  if (idx > -1) {
    const nombre = this.favoritos[idx].nombre;
    this.favoritos.splice(idx, 1);
    this.guardarFavoritos();
    
    // Sincronizar con backend
    this.syncFavoritoBackend(productoId, 'eliminar');
    
    // Actualizar botón en el catálogo si existe
    const btn = document.querySelector(`[onclick*="toggleFavorito('${productoId}')"]`);
    if (btn) {
      btn.classList.remove('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
      }
    }
    
    // CRÍTICO: Actualizar contador y sidebar
    this.actualizarContadorFavoritos();
    this.actualizarSidebarFavoritos();
    
    // Remover elemento con animación
    const favoritoElement = document.querySelector(`[data-favorito-id="${productoId}"]`);
    if (favoritoElement) {
      favoritoElement.style.transition = 'all 0.3s ease';
      favoritoElement.style.opacity = '0';
      favoritoElement.style.transform = 'translateX(-20px)';
      setTimeout(() => {
        favoritoElement.remove();
        if (this.favoritos.length === 0) {
          this.actualizarSidebarFavoritos();
        }
      }, 300);
    }
    
    this.showToast(`${nombre} eliminado de favoritos`, 'info');
  }
};

// SOBRESCRIBIR método eliminarFavoritoDesdeModal
ClienteDashboardManager.prototype.eliminarFavoritoDesdeModal = function(productoId) {
  this.eliminarFavoritoDirecto(productoId);
  
  const element = document.querySelector(`[data-favorito-modal-id="${productoId}"]`);
  if (element) {
    element.style.transition = 'all 0.3s ease';
    element.style.opacity = '0';
    element.style.transform = 'translateX(-20px)';
    setTimeout(() => {
      element.remove();
      
      if (this.favoritos.length === 0) {
        this.closeAllModals();
        this.actualizarSidebarFavoritos();
        this.actualizarContadorFavoritos();
      } else {
        const modalTitle = document.querySelector('.modal-container h5');
        if (modalTitle) {
          modalTitle.textContent = `❤️ Mis Favoritos (${this.favoritos.length})`;
        }
        
        const totalBadge = document.querySelector('.favoritos-list')?.nextElementSibling?.querySelector('.badge');
        if (totalBadge) {
          totalBadge.textContent = this.favoritos.length;
        }
        
        this.actualizarContadorFavoritos();
      }
    }, 300);
  }
};

// SOBRESCRIBIR método ejecutarVaciarFavoritos
ClienteDashboardManager.prototype.ejecutarVaciarFavoritos = function() {
  const favoritosIds = [...this.favoritos.map(f => f.id)];
  
  this.favoritos = [];
  this.guardarFavoritos();
  
  favoritosIds.forEach(id => {
    this.syncFavoritoBackend(id, 'eliminar');
  });
  
  document.querySelectorAll('.btn-favorito.active').forEach(btn => {
    btn.classList.remove('active');
    const icon = btn.querySelector('i');
    if (icon) {
      icon.classList.remove('bi-heart-fill');
      icon.classList.add('bi-heart');
    }
  });
  
  // CRÍTICO: Actualizar contador y sidebar
  this.actualizarContadorFavoritos();
  this.actualizarSidebarFavoritos();
  
  this.closeAllModals();
  this.showToast('Todos los favoritos han sido eliminados', 'info');
};

// SOBRESCRIBIR método init para asegurar sincronización al cargar
ClienteDashboardManager.prototype.init = function() {
  this.setupEventListeners();
  this.animateCards();
  this.updateCarritoCount();
  this.sincronizarFavoritosConDOM();
  this.loadFavoritos();
  this.actualizarContadorFavoritos(); // Actualizar contador al inicio
  
  // Verificar y actualizar sidebar después de un pequeño delay
  setTimeout(() => {
    this.actualizarSidebarFavoritos();
    this.actualizarContadorFavoritos();
    console.log('✅ Sistema de favoritos inicializado:', this.favoritos.length);
  }, 100);
};

// Agregar método de sincronización periódica (cada 30 segundos)
setInterval(() => {
  if (window.clienteDashboard) {
    const favoritosActuales = window.clienteDashboard.favoritos.length;
    const contadorMostrado = document.getElementById('contadorFavoritosMetric')?.textContent;
    
    if (favoritosActuales != contadorMostrado) {
      console.warn('⚠️ Desincronización detectada. Corrigiendo...');
      window.clienteDashboard.actualizarContadorFavoritos();
      window.clienteDashboard.actualizarSidebarFavoritos();
    }
  }
}, 30000);

// ========================================
// CORRECCIÓN: AGREGAR AL CARRITO DESDE FAVORITOS
// ========================================

// FUNCIÓN GLOBAL MEJORADA: agregarAlCarritoFromFavorito
window.agregarAlCarritoFromFavorito = function(id) {
  console.log('🛒 Intentando agregar al carrito desde favoritos:', id);
  
  if (!window.clienteDashboard) {
    console.error('❌ ClienteDashboard no disponible');
    return;
  }
  
  // Buscar en favoritos del manager
  const fav = window.clienteDashboard.favoritos.find(f => f.id === id);
  
  if (fav) {
    console.log('✅ Producto encontrado en favoritos:', fav);
    window.clienteDashboard.agregarAlCarrito(id, fav.nombre, fav.precio, fav.imagen, fav.stock);
    
    // Feedback visual en el botón
    const btn = document.querySelector(`[onclick*="agregarAlCarritoFromFavorito('${id}')"]`);
    if (btn && !btn.disabled) {
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> ¡Agregado!';
      btn.classList.add('btn-success');
      btn.classList.remove('btn-outline-primary');
      btn.disabled = true;
      
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-primary');
        btn.disabled = false;
      }, 2000);
    }
  } else {
    console.error('❌ Producto no encontrado en favoritos:', id);
    
    // Intentar obtener datos del DOM como fallback
    const btnDOM = document.querySelector(`[data-producto-id="${id}"]`);
    if (btnDOM) {
      const nombre = btnDOM.dataset.nombre || 'Producto';
      const precio = parseFloat(btnDOM.dataset.precio) || 0;
      const imagen = btnDOM.dataset.imagen || null;
      const stock = btnDOM.dataset.stock ? parseInt(btnDOM.dataset.stock) : null;
      
      console.log('⚠️ Usando datos del DOM como fallback');
      window.clienteDashboard.agregarAlCarrito(id, nombre, precio, imagen, stock);
    } else {
      window.clienteDashboard.showToast('Error: Producto no encontrado', 'error');
    }
  }
};

// SOBRESCRIBIR: agregarAlCarritoDesdeModal
ClienteDashboardManager.prototype.agregarAlCarritoDesdeModal = function(productoId) {
  console.log('🛒 Agregando al carrito desde modal:', productoId);
  
  const fav = this.favoritos.find(f => f.id === productoId);
  
  if (!fav) {
    console.error('❌ Producto no encontrado en favoritos');
    this.showToast('Error: Producto no encontrado', 'error');
    return;
  }
  
  // Verificar stock
  if (fav.stock !== null && fav.stock <= 0) {
    this.showToast('Producto sin stock disponible', 'warning');
    return;
  }
  
  this.agregarAlCarrito(productoId, fav.nombre, fav.precio, fav.imagen, fav.stock);
  
  // Feedback visual mejorado
  const btnModal = document.querySelector(`[onclick*="agregarAlCarritoDesdeModal('${productoId}')"]`);
  if (btnModal && !btnModal.disabled) {
    const originalText = btnModal.innerHTML;
    btnModal.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ¡Agregado!';
    btnModal.classList.remove('btn-primary');
    btnModal.classList.add('btn-success');
    btnModal.disabled = true;
    
    // Restaurar después de 2 segundos
    setTimeout(() => {
      btnModal.innerHTML = originalText;
      btnModal.classList.add('btn-primary');
      btnModal.classList.remove('btn-success');
      btnModal.disabled = fav.stock <= 0;
    }, 2000);
  }
};

// MEJORAR: actualizarSidebarFavoritos para asegurar data-attributes correctos
ClienteDashboardManager.prototype.actualizarSidebarFavoritos = function() {
  let container = document.querySelector('.card-header:has(.bi-heart-fill)')?.parentElement?.querySelector('.card-body:last-of-type');
  
  if (!container) {
    const card = document.querySelector('.card-header:has(.bi-heart-fill)')?.closest('.card');
    if (card) {
      const cardBodies = card.querySelectorAll('.card-body');
      container = cardBodies[cardBodies.length - 1];
    }
  }
  
  if (!container) {
    console.warn('⚠️ No se encontró el contenedor de favoritos');
    return;
  }

  this.actualizarContadorFavoritos();

  if (this.favoritos.length === 0) {
    container.innerHTML = `
      <div class="text-center py-3">
        <i class="bi bi-heart fs-3 text-muted"></i>
        <p class="text-muted mb-2">No tienes productos favoritos</p>
        <button class="btn btn-sm btn-primary" onclick="document.getElementById('buscarProducto')?.scrollIntoView({behavior:'smooth'})">
          Explorar productos
        </button>
      </div>
    `;
    return;
  }

  const favoritosVisible = this.favoritos.slice(0, 3);
  const totalFavoritos = this.favoritos.length;

  let html = '';
  favoritosVisible.forEach((fav, index) => {
    const stockColor = fav.stock <= 0 ? 'text-danger' : (fav.stock <= 5 ? 'text-warning' : 'text-muted');
    const stockText = fav.stock !== null && fav.stock !== undefined ? `
      <small class="d-block ${stockColor}">
        <i class="bi bi-box-seam"></i> Stock: ${fav.stock}
      </small>
    ` : '';
    
    const borderClass = index < favoritosVisible.length - 1 ? 'border-bottom' : '';
    const estaAgotado = fav.stock !== null && fav.stock <= 0;

    html += `
      <div class="d-flex align-items-center py-2 ${borderClass}" data-favorito-id="${fav.id}">
        <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
          <i class="bi bi-heart-fill"></i>
        </div>
        <div class="flex-grow-1">
          <div class="fw-medium">${fav.nombre}</div>
          <small class="text-muted">${this.formatPrice(fav.precio)}</small>
          ${stockText}
        </div>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-outline-primary ${estaAgotado ? 'disabled' : ''}" 
                  onclick="agregarAlCarritoFromFavorito('${fav.id}')"
                  data-producto-id="${fav.id}"
                  data-nombre="${this.escapeHtml(fav.nombre)}"
                  data-precio="${fav.precio}"
                  data-imagen="${fav.imagen || ''}"
                  data-stock="${fav.stock || 0}"
                  ${estaAgotado ? 'disabled' : ''}
                  title="${estaAgotado ? 'Producto agotado' : 'Agregar al carrito'}">
            <i class="bi bi-cart-plus"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" 
                  onclick="eliminarFavorito('${fav.id}')"
                  title="Quitar de favoritos">
            <i class="bi bi-heart-fill"></i>
          </button>
        </div>
      </div>
    `;
  });

  if (totalFavoritos > 3) {
    html += `
      <div class="text-center mt-2">
        <button class="btn btn-sm btn-outline-primary" onclick="clienteDashboard.mostrarTodosFavoritos()">
          Ver todos (${totalFavoritos})
        </button>
      </div>
    `;
  }

  container.innerHTML = html;
  console.log('✅ Sidebar de favoritos actualizado');
};

// NUEVO: Método auxiliar para escapar HTML
ClienteDashboardManager.prototype.escapeHtml = function(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
};

// MEJORAR: mostrarTodosFavoritos con data-attributes correctos
ClienteDashboardManager.prototype.mostrarTodosFavoritos = function() {
  if (this.favoritos.length === 0) {
    this.showToast('No tienes productos favoritos', 'info');
    return;
  }

  this.closeAllModals();
  
  setTimeout(() => {
    let favoritosHtml = '';
    
    this.favoritos.forEach((fav) => {
      const stockColor = fav.stock <= 0 ? 'danger' : (fav.stock <= 5 ? 'warning' : 'secondary');
      const stockText = fav.stock !== null && fav.stock !== undefined ? `
        <span class="badge bg-${stockColor} me-2">
          <i class="bi bi-box-seam"></i> Stock: ${fav.stock}
        </span>
      ` : '';
      
      const estaAgotado = fav.stock !== null && fav.stock <= 0;

      favoritosHtml += `
        <div class="d-flex align-items-center p-3 border-bottom hover-bg-light" 
             data-favorito-modal-id="${fav.id}"
             data-producto-nombre="${this.escapeHtml(fav.nombre)}"
             data-producto-precio="${fav.precio}"
             data-producto-imagen="${fav.imagen || ''}"
             data-producto-stock="${fav.stock || 0}">
          <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-heart-fill fs-5"></i>
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold">${fav.nombre}</div>
            <div class="text-success fw-bold mt-1">${this.formatPrice(fav.precio)}</div>
            <div class="mt-1">
              ${stockText}
            </div>
          </div>
          <div class="d-flex flex-column gap-2">
            <button class="btn btn-sm btn-primary ${estaAgotado ? 'disabled' : ''}" 
                    onclick="event.stopPropagation(); clienteDashboard.agregarAlCarritoDesdeModal('${fav.id}')"
                    data-modal-producto-id="${fav.id}"
                    ${estaAgotado ? 'disabled' : ''}
                    title="${estaAgotado ? 'Producto agotado' : 'Agregar al carrito'}">
              <i class="bi bi-cart-plus me-1"></i> Agregar
            </button>
            <button class="btn btn-sm btn-outline-danger" 
                    onclick="event.stopPropagation(); clienteDashboard.eliminarFavoritoDesdeModal('${fav.id}')"
                    title="Quitar de favoritos">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </div>
        </div>
      `;
    });

    this.createModal('info', `❤️ Mis Favoritos (${this.favoritos.length})`, `
      <div class="favoritos-list" style="max-height: 500px; overflow-y: auto;">
        ${favoritosHtml}
      </div>
      <div class="mt-3 p-3 bg-light rounded">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <strong>Total de productos favoritos:</strong>
            <span class="badge bg-primary ms-2">${this.favoritos.length}</span>
          </div>
          <button class="btn btn-sm btn-outline-danger" onclick="clienteDashboard.confirmarVaciarFavoritos()">
            <i class="bi bi-trash me-1"></i> Vaciar todos
          </button>
        </div>
      </div>
    `, [
      { text: 'Cerrar', type: 'secondary', onclick: 'clienteDashboard.closeAllModals()' }
    ]);
    
    console.log('✅ Modal de favoritos mostrado con', this.favoritos.length, 'productos');
  }, 100);
};

console.log('✅ Parche del sistema de favoritos aplicado correctamente');
console.log('✅ Corrección de agregar al carrito desde favoritos aplicada');