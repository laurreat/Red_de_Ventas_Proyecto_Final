/**
 * Productos Module JavaScript
 * Funcionalidades específicas para el módulo de gestión de productos
 */

class ProductosModule {
    constructor(adminCore) {
        this.adminCore = adminCore;
        this.currentPage = 1;
        this.itemsPerPage = 12;
        this.filters = {
            search: '',
            categoria: '',
            status: ''
        };
        this.sortBy = 'created_at';
        this.sortOrder = 'desc';
        this.viewMode = 'grid'; // 'grid' o 'list'

        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('Productos Module initialized');
    }

    setupEventListeners() {
        // Búsqueda con debounce
        const searchInput = document.getElementById('producto-search');
        if (searchInput) {
            searchInput.addEventListener('input',
                this.adminCore.debounce((e) => {
                    this.filters.search = e.target.value;
                    this.currentPage = 1;
                    this.loadData();
                }, 300)
            );
        }

        // Filtros
        document.getElementById('producto-categoria-filter')?.addEventListener('change', (e) => {
            this.filters.categoria = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        document.getElementById('producto-status-filter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        // Botón filtrar
        document.getElementById('filter-productos-btn')?.addEventListener('click', () => {
            this.loadData();
        });

        // Botón limpiar filtros
        document.getElementById('clear-producto-filters-btn')?.addEventListener('click', () => {
            this.clearFilters();
        });

        // Botón nuevo producto
        document.getElementById('btn-nuevo-producto')?.addEventListener('click', () => {
            this.openProductModal('create');
        });

        // Toggle vista
        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.viewMode = e.target.dataset.view;
                this.updateViewToggle();
                this.renderProducts();
            });
        });

        // Ordenamiento
        document.getElementById('producto-sort')?.addEventListener('change', (e) => {
            const [sortBy, sortOrder] = e.target.value.split(':');
            this.sortBy = sortBy;
            this.sortOrder = sortOrder;
            this.loadData();
        });
    }

    async loadData() {
        try {
            this.adminCore.showLoading();

            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                sortBy: this.sortBy,
                sortOrder: this.sortOrder,
                ...this.filters
            });

            const response = await this.adminCore.apiCall(`/api/admin/products?${params.toString()}`);

            this.products = response.data.products;
            this.renderProducts();
            this.renderPagination(response.data.pagination);
            this.updateStats(response.data.stats);

            // Guardar en cache
            this.adminCore.setCacheData('productos', response.data);

        } catch (error) {
            console.error('Error loading products:', error);
            this.adminCore.showError('Error al cargar productos');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    renderProducts() {
        const container = document.getElementById('productos-grid');
        if (!container) return;

        if (!this.products || this.products.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="productos-empty">
                        <div class="empty-icon">
                            <i class="bi bi-box"></i>
                        </div>
                        <div class="empty-title">No se encontraron productos</div>
                        <div class="empty-text">No hay productos que coincidan con los filtros seleccionados</div>
                        <button class="btn btn-primary" onclick="openProductModal('create')">
                            <i class="bi bi-plus-circle me-1"></i>
                            Crear primer producto
                        </button>
                    </div>
                </div>
            `;
            return;
        }

        if (this.viewMode === 'grid') {
            this.renderGridView();
        } else {
            this.renderListView();
        }
    }

    renderGridView() {
        const container = document.getElementById('productos-grid');

        container.innerHTML = this.products.map(product => `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="producto-card">
                    <div class="producto-image">
                        ${product.imagen ?
                            `<img src="${product.imagen}" alt="${product.nombre}" loading="lazy">` :
                            `<div class="producto-image-placeholder"><i class="bi bi-image"></i></div>`
                        }
                        <div class="producto-status-badge status-${product.activo ? 'activo' : 'inactivo'}">
                            ${product.activo ? 'Activo' : 'Inactivo'}
                        </div>
                    </div>
                    <div class="producto-content">
                        <div class="producto-categoria">${product.categoria || 'Sin categoría'}</div>
                        <h5 class="producto-titulo">${product.nombre}</h5>
                        <p class="producto-descripcion">${product.descripcion || 'Sin descripción'}</p>
                        <div class="producto-footer">
                            <div class="producto-precio">${this.adminCore.formatCurrency(product.precio)}</div>
                            <div class="producto-stock ${this.getStockClass(product.stock)}">
                                Stock: ${product.stock || 0}
                            </div>
                        </div>
                        <div class="producto-actions">
                            <button class="producto-action-btn btn-producto-view" onclick="viewProduct('${product._id}')" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                                Ver
                            </button>
                            <button class="producto-action-btn btn-producto-edit" onclick="editProduct('${product._id}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                                Editar
                            </button>
                            <button class="producto-action-btn btn-producto-delete" onclick="deleteProduct('${product._id}')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderListView() {
        const container = document.getElementById('productos-grid');

        container.innerHTML = `
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${this.products.map(product => `
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                ${product.imagen ?
                                                    `<img src="${product.imagen}" alt="${product.nombre}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">` :
                                                    `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bi bi-image text-muted"></i></div>`
                                                }
                                            </div>
                                            <div>
                                                <h6 class="mb-1">${product.nombre}</h6>
                                                <small class="text-muted">${product.descripcion || 'Sin descripción'}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="categoria-badge">${product.categoria || 'Sin categoría'}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${this.adminCore.formatCurrency(product.precio)}</span>
                                    </td>
                                    <td>
                                        <span class="producto-stock ${this.getStockClass(product.stock)}">
                                            ${product.stock || 0}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="producto-status status-${product.activo ? 'activo' : 'inactivo'}">
                                            ${product.activo ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td>${this.adminCore.formatDate(product.created_at)}</td>
                                    <td>
                                        <div class="producto-actions">
                                            <button class="producto-action-btn btn-producto-view" onclick="viewProduct('${product._id}')" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="producto-action-btn btn-producto-edit" onclick="editProduct('${product._id}')" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="producto-action-btn btn-producto-delete" onclick="deleteProduct('${product._id}')" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    renderPagination(pagination) {
        const container = document.getElementById('productos-pagination');
        if (!container || !pagination) return;

        const { currentPage, totalPages, totalItems } = pagination;

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '<nav aria-label="Paginación productos"><ul class="pagination justify-content-center">';

        // Botón anterior
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="changeProductPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
        `;

        // Páginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="changeProductPage(${i})">${i}</button>
                </li>
            `;
        }

        // Botón siguiente
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="changeProductPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        `;

        paginationHTML += '</ul></nav>';

        container.innerHTML = paginationHTML;
    }

    updateStats(stats) {
        if (!stats) return;

        const containers = {
            'total-productos-stat': stats.total,
            'productos-activos-stat': stats.activos,
            'productos-agotados-stat': stats.agotados,
            'valor-inventario-stat': this.adminCore.formatCurrency(stats.valor_inventario)
        };

        Object.entries(containers).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }

    updateViewToggle() {
        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === this.viewMode);
        });
    }

    getStockClass(stock) {
        if (stock === 0) return 'stock-agotado';
        if (stock <= 5) return 'stock-bajo';
        return 'stock-disponible';
    }

    clearFilters() {
        this.filters = {
            search: '',
            categoria: '',
            status: ''
        };

        document.getElementById('producto-search').value = '';
        document.getElementById('producto-categoria-filter').value = '';
        document.getElementById('producto-status-filter').value = '';

        this.currentPage = 1;
        this.loadData();
    }

    changeProductPage(page) {
        this.currentPage = page;
        this.loadData();
    }

    async openProductModal(action, productId = null) {
        // TODO: Implementar modal de producto
        console.log(`Opening product modal: ${action}`, productId);
        this.adminCore.showError('Modal de producto próximamente');
    }

    async viewProduct(productId) {
        try {
            this.adminCore.showLoading();

            const response = await this.adminCore.apiCall(`/api/admin/products/${productId}`);
            const product = response.data;

            console.log('Product details:', product);
            this.adminCore.showSuccess('Cargando detalles del producto...');

        } catch (error) {
            console.error('Error loading product details:', error);
            this.adminCore.showError('Error al cargar detalles del producto');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    async editProduct(productId) {
        console.log('Edit product:', productId);
        this.adminCore.showError('Edición de producto próximamente');
    }

    async deleteProduct(productId) {
        if (!confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            this.adminCore.showLoading();

            await this.adminCore.apiCall(`/api/admin/products/${productId}`, {
                method: 'DELETE'
            });

            this.adminCore.showSuccess('Producto eliminado correctamente');
            this.loadData();

        } catch (error) {
            console.error('Error deleting product:', error);
            this.adminCore.showError('Error al eliminar producto');
        } finally {
            this.adminCore.hideLoading();
        }
    }
}

// Funciones globales para compatibilidad
window.viewProduct = function(productId) {
    if (window.adminSPA && window.adminSPA.modules.products) {
        window.adminSPA.modules.products.viewProduct(productId);
    }
};

window.editProduct = function(productId) {
    if (window.adminSPA && window.adminSPA.modules.products) {
        window.adminSPA.modules.products.editProduct(productId);
    }
};

window.deleteProduct = function(productId) {
    if (window.adminSPA && window.adminSPA.modules.products) {
        window.adminSPA.modules.products.deleteProduct(productId);
    }
};

window.changeProductPage = function(page) {
    if (window.adminSPA && window.adminSPA.modules.products) {
        window.adminSPA.modules.products.changeProductPage(page);
    }
};

window.openProductModal = function(action, productId = null) {
    if (window.adminSPA && window.adminSPA.modules.products) {
        window.adminSPA.modules.products.openProductModal(action, productId);
    }
};

// Exportar módulo
window.ProductosModule = ProductosModule;