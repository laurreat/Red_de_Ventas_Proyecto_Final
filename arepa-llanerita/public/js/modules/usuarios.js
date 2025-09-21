/**
 * Usuarios Module JavaScript
 * Funcionalidades específicas para el módulo de gestión de usuarios
 */

class UsuariosModule {
    constructor(adminCore) {
        this.adminCore = adminCore;
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.filters = {
            search: '',
            role: '',
            status: ''
        };
        this.sortBy = 'created_at';
        this.sortOrder = 'desc';

        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('Usuarios Module initialized');
    }

    setupEventListeners() {
        // Búsqueda con debounce
        const searchInput = document.getElementById('user-search');
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
        document.getElementById('user-role-filter')?.addEventListener('change', (e) => {
            this.filters.role = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        document.getElementById('user-status-filter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.currentPage = 1;
            this.loadData();
        });

        // Botón filtrar
        document.getElementById('filter-users-btn')?.addEventListener('click', () => {
            this.loadData();
        });

        // Botón limpiar filtros
        document.getElementById('clear-filters-btn')?.addEventListener('click', () => {
            this.clearFilters();
        });

        // Botón nuevo usuario
        document.getElementById('btn-nuevo-usuario')?.addEventListener('click', () => {
            this.openUserModal('create');
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

            const response = await this.adminCore.apiCall(`/api/admin/users?${params.toString()}`);

            this.renderUsersTable(response.data.users);
            this.renderPagination(response.data.pagination);
            this.updateStats(response.data.stats);

            // Guardar en cache
            this.adminCore.setCacheData('usuarios', response.data);

        } catch (error) {
            console.error('Error loading users:', error);
            this.adminCore.showError('Error al cargar usuarios');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    renderUsersTable(users) {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        if (!users || users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <div class="py-4">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            No se encontraron usuarios
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = users.map(user => `
            <tr data-user-id="${user._id}">
                <td>
                    <div class="user-info">
                        <div class="user-avatar">
                            ${this.getUserInitials(user.name)}
                        </div>
                        <div class="user-details">
                            <div class="user-name">${user.name}</div>
                            ${user.apellidos ? `<div class="user-apellidos">${user.apellidos}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <div>${user.email}</div>
                    ${user.telefono ? `<small class="text-muted">${user.telefono}</small>` : ''}
                </td>
                <td>
                    <span class="user-role-badge role-${user.rol}">${this.formatRole(user.rol)}</span>
                </td>
                <td>
                    <span class="user-status-badge ${user.activo ? 'bg-success' : 'bg-secondary'}">
                        ${user.activo ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td>
                    <div>${this.adminCore.formatDate(user.created_at)}</div>
                    <small class="text-muted">${this.adminCore.formatDateTime(user.created_at).split(' ')[1]}</small>
                </td>
                <td>
                    <div class="user-actions">
                        <button class="user-action-btn btn-view" onclick="viewUser('${user._id}')" title="Ver detalles">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="user-action-btn btn-edit" onclick="editUser('${user._id}')" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${user.rol !== 'administrador' ? `
                            <button class="user-action-btn btn-delete" onclick="deleteUser('${user._id}')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('users-pagination');
        if (!container || !pagination) return;

        const { currentPage, totalPages, totalItems } = pagination;

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '<nav aria-label="Paginación usuarios"><ul class="pagination justify-content-center">';

        // Botón anterior
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
        `;

        // Páginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            paginationHTML += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
            if (startPage > 2) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="changePage(${i})">${i}</button>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHTML += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
        }

        // Botón siguiente
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        `;

        paginationHTML += '</ul></nav>';

        // Información de paginación
        paginationHTML += `
            <div class="text-center mt-2">
                <small class="text-muted">
                    Mostrando ${((currentPage - 1) * this.itemsPerPage) + 1} - ${Math.min(currentPage * this.itemsPerPage, totalItems)} de ${totalItems} usuarios
                </small>
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    updateStats(stats) {
        if (!stats) return;

        // Actualizar estadísticas si existen contenedores
        const containers = {
            'total-users-stat': stats.total,
            'active-users-stat': stats.activos,
            'new-users-month-stat': stats.nuevos_mes,
            'admins-count-stat': stats.administradores
        };

        Object.entries(containers).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = this.adminCore.formatNumber(value);
            }
        });
    }

    getUserInitials(name) {
        return name.split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    formatRole(role) {
        const roles = {
            'administrador': 'Administrador',
            'lider': 'Líder',
            'vendedor': 'Vendedor',
            'cliente': 'Cliente'
        };
        return roles[role] || role;
    }

    changePage(page) {
        if (page < 1 || page > this.totalPages) return;
        this.currentPage = page;
        this.loadData();
    }

    clearFilters() {
        this.filters = {
            search: '',
            role: '',
            status: ''
        };

        // Limpiar campos de filtro
        document.getElementById('user-search').value = '';
        document.getElementById('user-role-filter').value = '';
        document.getElementById('user-status-filter').value = '';

        this.currentPage = 1;
        this.loadData();
    }

    async openUserModal(action, userId = null) {
        // TODO: Implementar modal de usuario
        console.log(`Opening user modal: ${action}`, userId);
        this.adminCore.showError('Modal de usuario próximamente');
    }

    async viewUser(userId) {
        try {
            this.adminCore.showLoading();

            const response = await this.adminCore.apiCall(`/api/admin/users/${userId}`);
            const user = response.data;

            // TODO: Mostrar modal o vista de detalles del usuario
            console.log('User details:', user);
            this.adminCore.showSuccess('Cargando detalles del usuario...');

        } catch (error) {
            console.error('Error loading user details:', error);
            this.adminCore.showError('Error al cargar detalles del usuario');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    async editUser(userId) {
        // TODO: Implementar edición de usuario
        console.log('Edit user:', userId);
        this.adminCore.showError('Edición de usuario próximamente');
    }

    async deleteUser(userId) {
        if (!confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            this.adminCore.showLoading();

            await this.adminCore.apiCall(`/api/admin/users/${userId}`, {
                method: 'DELETE'
            });

            this.adminCore.showSuccess('Usuario eliminado correctamente');
            this.loadData(); // Recargar tabla

        } catch (error) {
            console.error('Error deleting user:', error);
            this.adminCore.showError('Error al eliminar usuario');
        } finally {
            this.adminCore.hideLoading();
        }
    }

    async toggleUserStatus(userId, currentStatus) {
        try {
            this.adminCore.showLoading();

            await this.adminCore.apiCall(`/api/admin/users/${userId}/toggle-status`, {
                method: 'PATCH',
                body: JSON.stringify({ activo: !currentStatus })
            });

            this.adminCore.showSuccess(`Usuario ${!currentStatus ? 'activado' : 'desactivado'} correctamente`);
            this.loadData(); // Recargar tabla

        } catch (error) {
            console.error('Error toggling user status:', error);
            this.adminCore.showError('Error al cambiar estado del usuario');
        } finally {
            this.adminCore.hideLoading();
        }
    }
}

// Funciones globales para compatibilidad
window.viewUser = function(userId) {
    if (window.adminSPA && window.adminSPA.modules.users) {
        window.adminSPA.modules.users.viewUser(userId);
    }
};

window.editUser = function(userId) {
    if (window.adminSPA && window.adminSPA.modules.users) {
        window.adminSPA.modules.users.editUser(userId);
    }
};

window.deleteUser = function(userId) {
    if (window.adminSPA && window.adminSPA.modules.users) {
        window.adminSPA.modules.users.deleteUser(userId);
    }
};

window.changePage = function(page) {
    if (window.adminSPA && window.adminSPA.modules.users) {
        window.adminSPA.modules.users.changePage(page);
    }
};

window.filterUsers = function() {
    if (window.adminSPA && window.adminSPA.modules.users) {
        window.adminSPA.modules.users.loadData();
    }
};

// Exportar módulo
window.UsuariosModule = UsuariosModule;