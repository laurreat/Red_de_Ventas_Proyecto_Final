/**
 * MÓDULO DE NOTIFICACIONES - PÁGINA COMPLETA
 * Gestión completa de notificaciones con filtros, búsqueda y paginación
 */

class NotificacionesModule {
    constructor() {
        this.notificaciones = [];
        this.currentPage = 1;
        this.perPage = 12;
        this.totalPages = 1;
        this.filtros = {
            tipo: '',
            estado: '',
            periodo: '',
            buscar: ''
        };
        this.vistaActual = 'grid';
        this.currentNotificationId = null;
        
        this.init();
    }

    init() {
        this.cargarNotificaciones();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Event listener para el modal
        const modal = document.getElementById('notificacionModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                this.currentNotificationId = null;
            });
        }

        // Botones del header
        const btnMarcarTodas = document.getElementById('btnMarcarTodas');
        if (btnMarcarTodas) {
            btnMarcarTodas.addEventListener('click', () => this.marcarTodasComoLeidas());
        }

        const btnLimpiarLeidas = document.getElementById('btnLimpiarLeidas');
        if (btnLimpiarLeidas) {
            btnLimpiarLeidas.addEventListener('click', () => this.limpiarLeidas());
        }

        const btnLimpiarAntiguas = document.getElementById('btnLimpiarAntiguas');
        if (btnLimpiarAntiguas) {
            btnLimpiarAntiguas.addEventListener('click', () => this.limpiarAntiguas());
        }

        // Botones de vista
        const btnVistaLista = document.getElementById('btnVistaLista');
        if (btnVistaLista) {
            btnVistaLista.addEventListener('click', () => this.cambiarVista('list'));
        }

        const btnVistaGrid = document.getElementById('btnVistaGrid');
        if (btnVistaGrid) {
            btnVistaGrid.addEventListener('click', () => this.cambiarVista('grid'));
        }

        // Botón eliminar en modal
        const btnEliminar = document.getElementById('btnEliminarNotificacion');
        if (btnEliminar) {
            btnEliminar.addEventListener('click', () => {
                if (this.currentNotificationId) {
                    this.eliminarNotificacion(this.currentNotificationId);
                }
            });
        }
    }

    async cargarNotificaciones() {
        this.mostrarLoading();

        try {
            const response = await fetch(this.buildUrl(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.notificaciones = data.notificaciones;
                this.totalPages = data.pagination.last_page;
                this.currentPage = data.pagination.current_page;

                // Debug: verificar que las notificaciones tienen _id
                console.log('Notificaciones cargadas:', this.notificaciones.length);
                if (this.notificaciones.length > 0) {
                    console.log('Ejemplo de notificación:', this.notificaciones[0]);
                }

                this.actualizarEstadisticas();
                this.renderizarNotificaciones();
                this.renderizarPaginacion();
                this.ocultarLoading();
            } else {
                this.mostrarError('Error al cargar notificaciones');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarError('Error de conexión');
        }
    }

    buildUrl() {
        const params = new URLSearchParams({
            page: this.currentPage,
            per_page: this.perPage
        });

        if (this.filtros.tipo) params.append('tipo', this.filtros.tipo);
        if (this.filtros.estado !== '') params.append('leida', this.filtros.estado);

        return `/cliente/notificaciones?${params.toString()}`;
    }

    renderizarNotificaciones() {
        const container = document.getElementById('notificacionesGrid');
        const emptyState = document.getElementById('emptyState');
        
        if (!container) return;

        // Aplicar filtros adicionales (periodo y búsqueda) en el frontend
        let notificacionesFiltradas = this.aplicarFiltrosLocal(this.notificaciones);

        if (notificacionesFiltradas.length === 0) {
            container.innerHTML = '';
            emptyState.style.display = 'flex';
            return;
        }

        emptyState.style.display = 'none';
        
        container.innerHTML = notificacionesFiltradas.map(notif => 
            this.renderNotificacionCard(notif)
        ).join('');

        document.getElementById('resultadosCount').textContent = notificacionesFiltradas.length;
    }

    renderNotificacionCard(notif) {
        const iconMap = {
            'pedido': { icon: 'bi-box-seam-fill', class: 'tipo-pedido' },
            'venta': { icon: 'bi-currency-dollar', class: 'tipo-venta' },
            'comision': { icon: 'bi-star-fill', class: 'tipo-comision' },
            'usuario': { icon: 'bi-person-fill', class: 'tipo-usuario' },
            'sistema': { icon: 'bi-gear-fill', class: 'tipo-sistema' }
        };

        const iconData = iconMap[notif.tipo] || { icon: 'bi-bell-fill', class: 'tipo-sistema' };
        const timeAgo = this.getTimeAgo(notif.created_at);
        const isUnread = !notif.leida;
        
        // Obtener el ID de la notificación - puede ser _id o id
        const notifId = notif._id || notif.id;
        
        if (!notifId) {
            console.warn('Notificación sin ID:', notif);
        }

        return `
            <div class="notificacion-card ${isUnread ? 'unread' : ''}" 
                 data-id="${notifId}"
                 data-notification-click="${notifId}">
                <div class="notificacion-header">
                    <div class="notificacion-icon ${iconData.class}">
                        <i class="bi ${iconData.icon}"></i>
                    </div>
                    <div class="notificacion-content">
                        <div class="notificacion-titulo">${this.escapeHtml(notif.titulo)}</div>
                    </div>
                </div>
                <div class="notificacion-mensaje">${this.escapeHtml(notif.mensaje)}</div>
                <div class="notificacion-footer">
                    <div class="notificacion-time">
                        <i class="bi bi-clock"></i>
                        ${timeAgo}
                    </div>
                    <div class="notificacion-actions">
                        ${isUnread ? `
                            <button class="btn-icon btn-mark-read" 
                                    data-mark-read="${notifId}"
                                    title="Marcar como leída">
                                <i class="bi bi-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn-icon btn-delete" 
                                data-delete-notif="${notifId}"
                                title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    aplicarFiltrosLocal(notificaciones) {
        let filtradas = [...notificaciones];

        // Filtro por período
        if (this.filtros.periodo) {
            const ahora = new Date();
            filtradas = filtradas.filter(notif => {
                const fecha = new Date(notif.created_at);
                
                switch(this.filtros.periodo) {
                    case 'hoy':
                        return fecha.toDateString() === ahora.toDateString();
                    case 'semana':
                        const semanaAtras = new Date(ahora.getTime() - 7 * 24 * 60 * 60 * 1000);
                        return fecha >= semanaAtras;
                    case 'mes':
                        const mesAtras = new Date(ahora.getTime() - 30 * 24 * 60 * 60 * 1000);
                        return fecha >= mesAtras;
                    default:
                        return true;
                }
            });
        }

        // Filtro por búsqueda
        if (this.filtros.buscar) {
            const termino = this.filtros.buscar.toLowerCase();
            filtradas = filtradas.filter(notif => 
                notif.titulo.toLowerCase().includes(termino) ||
                notif.mensaje.toLowerCase().includes(termino)
            );
        }

        return filtradas;
    }

    async verDetalle(id) {
        const notif = this.notificaciones.find(n => (n._id || n.id) === id);
        if (!notif) {
            console.error('Notificación no encontrada con ID:', id);
            return;
        }

        this.currentNotificationId = id;

        // Cerrar cualquier instancia previa del modal
        const modalElement = document.getElementById('notificacionModal');
        const existingInstance = bootstrap.Modal.getInstance(modalElement);
        if (existingInstance) {
            existingInstance.dispose();
        }

        const iconMap = {
            'pedido': { icon: 'bi-box-seam-fill', color: '#17a2b8', bg: 'rgba(23, 162, 184, 0.2)' },
            'venta': { icon: 'bi-currency-dollar', color: '#28a745', bg: 'rgba(40, 167, 69, 0.2)' },
            'comision': { icon: 'bi-star-fill', color: '#ffc107', bg: 'rgba(255, 193, 7, 0.2)' },
            'usuario': { icon: 'bi-person-fill', color: '#722F37', bg: 'rgba(114, 47, 55, 0.2)' },
            'sistema': { icon: 'bi-gear-fill', color: '#6c757d', bg: 'rgba(108, 117, 125, 0.2)' }
        };

        const iconData = iconMap[notif.tipo] || { icon: 'bi-bell-fill', color: '#6c757d', bg: 'rgba(108, 117, 125, 0.2)' };

        // Configurar icono
        const iconWrapper = document.getElementById('modalIconWrapper');
        const icono = document.getElementById('modalIcono');
        iconWrapper.style.background = iconData.bg;
        iconWrapper.style.borderColor = iconData.color + '40';
        icono.className = `bi ${iconData.icon}`;
        icono.style.color = iconData.color;

        // Configurar título
        document.getElementById('modalTituloTexto').textContent = notif.titulo;
        
        // Badge de tipo
        const tipoBadge = document.getElementById('modalTipoBadge');
        tipoBadge.textContent = this.getTipoTexto(notif.tipo);
        tipoBadge.style.background = iconData.bg;
        tipoBadge.style.borderColor = iconData.color + '40';
        tipoBadge.style.color = iconData.color;

        // Tiempo en header
        document.getElementById('modalFechaHeader').innerHTML = `
            <i class="bi bi-clock"></i>
            ${this.getTimeAgo(notif.created_at)}
        `;

        // Contenido
        document.getElementById('modalContenido').innerHTML = `
            <p class="lead mb-0">${this.escapeHtml(notif.mensaje)}</p>
            ${notif.datos_adicionales ? this.renderDatosAdicionales(notif.datos_adicionales) : ''}
        `;

        // Meta info
        document.getElementById('modalFecha').textContent = this.formatearFecha(notif.created_at);
        document.getElementById('modalTipo').textContent = this.getTipoTexto(notif.tipo);

        const modal = new bootstrap.Modal(document.getElementById('notificacionModal'), {
            backdrop: true,
            keyboard: true
        });
        
        modal.show();
        
        // Limpiar backdrop adicional si existe después de mostrar
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 1) {
                // Mantener solo el último backdrop
                for (let i = 0; i < backdrops.length - 1; i++) {
                    backdrops[i].remove();
                }
            }
            // Asegurar z-index correcto
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '1040';
            }
        }, 100);

        // Marcar como leída si no lo está
        if (!notif.leida) {
            await this.marcarComoLeida(id, false);
        }
        
        // Limpiar cuando se cierre el modal
        document.getElementById('notificacionModal').addEventListener('hidden.bs.modal', function cleanup() {
            // Limpiar todos los backdrops
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            
            // Remover este listener para evitar múltiples ejecuciones
            this.removeEventListener('hidden.bs.modal', cleanup);
        }, { once: true });
    }

    renderDatosAdicionales(datos) {
        if (!datos || Object.keys(datos).length === 0) return '';

        let html = '<div class="mt-4"><h6><i class="bi bi-info-circle"></i> Detalles Adicionales</h6><ul class="list-unstyled">';
        
        for (const [key, value] of Object.entries(datos)) {
            if (key !== 'test' && value !== null && value !== '') {
                const keyFormatted = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `
                    <li>
                        <strong>${keyFormatted}:</strong>
                        <span>${value}</span>
                    </li>
                `;
            }
        }
        
        html += '</ul></div>';
        return html;
    }

    async marcarComoLeida(id, recargar = true) {
        try {
            const response = await fetch(`/cliente/notificaciones/${id}/marcar-leida`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                if (recargar) {
                    this.mostrarToast('Notificación marcada como leída', 'success');
                    await this.cargarNotificaciones();
                } else {
                    // Actualizar solo el estado local
                    const notif = this.notificaciones.find(n => n._id === id);
                    if (notif) notif.leida = true;
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async marcarTodasComoLeidas() {
        GlassModal.confirm({
            title: '¿Marcar todas como leídas?',
            message: 'Todas las notificaciones no leídas serán marcadas como leídas',
            icon: 'bi-check-all',
            iconColor: '#10b981',
            iconBg: 'rgba(16, 185, 129, 0.2)',
            confirmText: 'Sí, marcar todas',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-success',
            onConfirm: async () => {
                try {
                    const response = await fetch('/cliente/notificaciones/marcar-todas-leidas', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        GlassModal.success('¡Listo!', data.message || 'Todas las notificaciones han sido marcadas como leídas');
                        await this.cargarNotificaciones();
                    } else {
                        GlassModal.error('Error', data.message || 'No se pudieron marcar las notificaciones');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    GlassModal.error('Error de Conexión', 'No se pudo conectar con el servidor');
                }
            }
        });
    }

    confirmarEliminar(id) {
        // Cerrar el modal de detalles primero si está abierto
        const modalDetalles = document.getElementById('notificacionModal');
        const bsModalDetalles = bootstrap.Modal.getInstance(modalDetalles);
        if (bsModalDetalles) {
            bsModalDetalles.hide();
        }

        // Esperar a que el modal se cierre completamente antes de mostrar la confirmación
        setTimeout(() => {
            GlassModal.confirm({
                title: '¿Eliminar notificación?',
                message: 'Esta acción no se puede deshacer',
                icon: 'bi-trash',
                iconColor: '#ef4444',
                iconBg: 'rgba(239, 68, 68, 0.2)',
                confirmText: 'Sí, eliminar',
                cancelText: 'Cancelar',
                confirmClass: 'btn-glass-danger',
                onConfirm: () => {
                    this.eliminarNotificacion(id);
                }
            });
        }, 300); // Esperar 300ms para que el primer modal se cierre
    }

    async eliminarNotificacion(id) {
        try {
            const response = await fetch(`/cliente/notificaciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                GlassModal.success('Eliminado', data.message || 'Notificación eliminada correctamente');
                
                // Cerrar modal si está abierto
                const modal = bootstrap.Modal.getInstance(document.getElementById('notificacionModal'));
                if (modal) modal.hide();
                
                await this.cargarNotificaciones();
            } else {
                GlassModal.error('Error', data.message || 'No se pudo eliminar la notificación');
            }
        } catch (error) {
            console.error('Error:', error);
            GlassModal.error('Error de Conexión', 'No se pudo conectar con el servidor');
        }
    }

    async limpiarAntiguas() {
        GlassModal.confirm({
            title: '¿Limpiar notificaciones antiguas?',
            message: 'Se eliminarán todas las notificaciones de más de 30 días',
            icon: 'bi-calendar-x',
            iconColor: '#f59e0b',
            iconBg: 'rgba(245, 158, 11, 0.2)',
            confirmText: 'Sí, limpiar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-warning',
            onConfirm: async () => {
                try {
                    const response = await fetch('/cliente/notificaciones/limpiar-antiguas', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        GlassModal.success('Limpieza Completada', data.message || 'Notificaciones antiguas eliminadas');
                        await this.cargarNotificaciones();
                    } else {
                        GlassModal.error('Error', data.message || 'No se pudieron limpiar las notificaciones');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    GlassModal.error('Error de Conexión', 'No se pudo conectar con el servidor');
                }
            }
        });
    }

    async limpiarLeidas() {
        GlassModal.confirm({
            title: '¿Limpiar notificaciones leídas?',
            message: 'Se eliminarán todas las notificaciones que ya has leído',
            icon: 'bi-check2-circle',
            iconColor: '#3b82f6',
            iconBg: 'rgba(59, 130, 246, 0.2)',
            confirmText: 'Sí, limpiar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-glass-info',
            onConfirm: async () => {
                try {
                    const response = await fetch('/cliente/notificaciones/limpiar-leidas', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        GlassModal.success('Limpieza Completada', data.message || 'Notificaciones leídas eliminadas');
                        await this.cargarNotificaciones();
                    } else {
                        GlassModal.error('Error', data.message || 'No se pudieron limpiar las notificaciones');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    GlassModal.error('Error de Conexión', 'No se pudo conectar con el servidor');
                }
            }
        });
    }

    aplicarFiltros() {
        this.filtros.tipo = document.getElementById('filtroTipo').value;
        this.filtros.estado = document.getElementById('filtroEstado').value;
        this.filtros.periodo = document.getElementById('filtroPeriodo').value;
        
        this.currentPage = 1;
        this.cargarNotificaciones();
    }

    buscar() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.filtros.buscar = document.getElementById('filtroBuscar').value;
            this.renderizarNotificaciones();
        }, 300);
    }

    cambiarVista(vista) {
        this.vistaActual = vista;
        
        document.getElementById('btnVistaLista').classList.toggle('active', vista === 'list');
        document.getElementById('btnVistaGrid').classList.toggle('active', vista === 'grid');
        
        const grid = document.getElementById('notificacionesGrid');
        if (vista === 'list') {
            grid.classList.add('notificaciones-list');
            grid.classList.remove('notificaciones-grid');
        } else {
            grid.classList.add('notificaciones-grid');
            grid.classList.remove('notificaciones-list');
        }
    }

    cambiarPagina(page) {
        this.currentPage = page;
        this.cargarNotificaciones();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    renderizarPaginacion() {
        const container = document.getElementById('paginationContainer');
        if (!container || this.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<nav><ul class="pagination">';

        // Botón anterior
        html += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage - 1}">
                    <i class="bi bi-chevron-left"></i> Anterior
                </a>
            </li>
        `;

        // Páginas
        const maxPages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxPages - 1);

        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${this.totalPages}">${this.totalPages}</a></li>`;
        }

        // Botón siguiente
        html += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage + 1}">
                    Siguiente <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        html += '</ul></nav>';
        container.innerHTML = html;

        // Agregar event listeners a los links de paginación
        container.querySelectorAll('a.page-link[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.dataset.page);
                if (!isNaN(page) && page > 0 && page <= this.totalPages) {
                    this.cambiarPagina(page);
                }
            });
        });
    }

    actualizarEstadisticas() {
        const total = this.notificaciones.length;
        const noLeidas = this.notificaciones.filter(n => !n.leida).length;
        const leidas = total - noLeidas;
        const hoy = this.notificaciones.filter(n => {
            const fecha = new Date(n.created_at);
            const ahora = new Date();
            return fecha.toDateString() === ahora.toDateString();
        }).length;

        document.getElementById('totalNotificaciones').textContent = total;
        document.getElementById('noLeidasCount').textContent = noLeidas;
        document.getElementById('leidasCount').textContent = leidas;
        document.getElementById('hoyCount').textContent = hoy;
    }

    mostrarLoading() {
        document.getElementById('loadingState').style.display = 'flex';
        document.getElementById('notificacionesGrid').style.display = 'none';
    }

    ocultarLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('notificacionesGrid').style.display = 'grid';
    }

    mostrarError(mensaje) {
        this.ocultarLoading();
        this.mostrarToast(mensaje, 'error');
    }

    mostrarToast(mensaje, tipo = 'info') {
        if (typeof showSuccessToast !== 'undefined' && tipo === 'success') {
            showSuccessToast(mensaje);
        } else if (typeof showErrorToast !== 'undefined' && tipo === 'error') {
            showErrorToast(mensaje);
        } else if (typeof showInfoToast !== 'undefined') {
            showInfoToast(mensaje);
        } else {
            alert(mensaje);
        }
    }

    // Utilidades
    getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            año: 31536000,
            mes: 2592000,
            semana: 604800,
            día: 86400,
            hora: 3600,
            minuto: 60
        };

        for (const [name, secondsInInterval] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInInterval);
            if (interval >= 1) {
                return interval === 1 ? `Hace 1 ${name}` : `Hace ${interval} ${name}s`;
            }
        }

        return 'Hace un momento';
    }

    formatearFecha(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        };
        return date.toLocaleDateString('es-ES', options);
    }

    getTipoTexto(tipo) {
        const tipos = {
            'pedido': 'Pedido',
            'venta': 'Venta',
            'comision': 'Comisión',
            'usuario': 'Usuario',
            'sistema': 'Sistema'
        };
        return tipos[tipo] || tipo;
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
}

// Instancia global
let notificacionesModule;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    notificacionesModule = new NotificacionesModule();
    console.log('Módulo de notificaciones inicializado');
    
    // Hacer disponible globalmente
    window.notificacionesModule = notificacionesModule;
});
