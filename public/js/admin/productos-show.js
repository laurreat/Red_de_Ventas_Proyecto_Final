/**
 * Productos Show
 * Funcionalidad para vista de detalles del producto
 */

// Funciones específicas para la vista de detalles del producto
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        console.log('Inicializando funciones para vista show del producto...');

        // Función para confirmar eliminación
        if (!window.confirmDelete) {
            window.confirmDelete = function(productId, productData) {
                console.log('Show confirmDelete ejecutada para:', productId);

                // Obtener información del producto
                const productName = productData.nombre;
                const productCategory = productData.categoria;
                const productImage = productData.imagen;

                // Actualizar modal
                const nameEl = document.getElementById('deleteProductName');
                const categoryEl = document.getElementById('deleteProductCategory');
                const imageEl = document.getElementById('deleteProductImage');

                if (nameEl) nameEl.textContent = productName;
                if (categoryEl) categoryEl.textContent = productCategory;
                if (imageEl) {
                    imageEl.src = productImage;
                    imageEl.alt = productName;
                }

                // Configurar botón de confirmación
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                if (confirmBtn) {
                    confirmBtn.onclick = function() {
                        document.getElementById(`delete-form-${productId}`).submit();
                    };
                }

                // Mostrar modal
                const modalElement = document.getElementById('deleteConfirmModal');
                if (modalElement) {
                    console.log('Mostrando modal de eliminación en vista show');
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    document.body.classList.add('modal-open');

                    // Crear backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            };
        }

        // Función para cambiar estado
        if (!window.toggleStatus) {
            window.toggleStatus = function(productId, productData) {
                console.log('Show toggleStatus ejecutada para:', productId);

                const isActive = productData.activo;

                // Configurar modal dinámicamente
                const statusHeader = document.getElementById('statusModalHeader');
                const statusIcon = document.getElementById('statusIcon');
                const statusIconContainer = document.getElementById('statusIconContainer');
                const statusTitle = document.getElementById('statusTitle');
                const statusMessage = document.getElementById('statusMessage');
                const statusBtn = document.getElementById('confirmStatusBtn');
                const statusBtnText = document.getElementById('statusBtnText');
                const statusBtnIcon = document.getElementById('statusBtnIcon');

                if (isActive) {
                    // Desactivar
                    if (statusHeader) statusHeader.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                    if (statusIconContainer) statusIconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
                    if (statusIcon) statusIcon.className = 'bi bi-pause-fill text-warning fs-1';
                    if (statusTitle) statusTitle.textContent = '¿Deseas desactivar este producto?';
                    if (statusMessage) statusMessage.textContent = 'El producto no será visible en el catálogo y no estará disponible para venta.';
                    if (statusBtn) statusBtn.className = 'btn btn-warning';
                    if (statusBtnIcon) statusBtnIcon.className = 'bi bi-pause me-1';
                    if (statusBtnText) statusBtnText.textContent = 'Desactivar Producto';
                } else {
                    // Activar
                    if (statusHeader) statusHeader.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                    if (statusIconContainer) statusIconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                    if (statusIcon) statusIcon.className = 'bi bi-play-fill text-success fs-1';
                    if (statusTitle) statusTitle.textContent = '¿Deseas activar este producto?';
                    if (statusMessage) statusMessage.textContent = 'El producto será visible en el catálogo y estará disponible para venta.';
                    if (statusBtn) statusBtn.className = 'btn btn-success';
                    if (statusBtnIcon) statusBtnIcon.className = 'bi bi-play me-1';
                    if (statusBtnText) statusBtnText.textContent = 'Activar Producto';
                }

                // Configurar botón de confirmación
                if (statusBtn) {
                    statusBtn.onclick = function() {
                        document.getElementById(`toggle-form-${productId}`).submit();
                    };
                }

                // Mostrar modal
                const modalElement = document.getElementById('statusConfirmModal');
                if (modalElement) {
                    console.log('Mostrando modal de estado en vista show');
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    document.body.classList.add('modal-open');

                    // Crear backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            };
        }

        // Función para cerrar modales
        if (!window.closeModal) {
            window.closeModal = function(modalId) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');

                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            };
        }

        // Event listeners para cerrar modales
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) window.closeModal(modal.id);
            });
        });

        // Cerrar con backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) window.closeModal(openModal.id);
            }
        });

        console.log('Funciones inicializadas para vista show');
    }, 1000);
});
