/**
 * Funciones específicas para gestión de productos
 * Archivo separado para mantener organización del código
 */

// Sobrescribir funciones después de que todo se cargue
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        console.log('Sobrescribiendo funciones de productos...');

        // Función para confirmar eliminación
        window.confirmDelete = function(productId) {
            console.log('Nueva confirmDelete ejecutada para:', productId);

            // Obtener información del producto
            const productRow = document.querySelector(`[data-product-id="${productId}"]`);
            let productName = 'Producto';
            let productCategory = '';
            let productImage = 'https://via.placeholder.com/50';

            if (productRow) {
                const nameElement = productRow.querySelector('.producto-nombre');
                const categoryElement = productRow.querySelector('.categoria-badge');
                const imageElement = productRow.querySelector('img');

                productName = nameElement ? nameElement.textContent.trim() : 'Producto';
                productCategory = categoryElement ? categoryElement.textContent.trim() : '';
                productImage = imageElement ? imageElement.src : 'https://via.placeholder.com/50';
            }

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
                console.log('Mostrando modal de eliminación');
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');

                // Crear backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        };

        // Función para cambiar estado
        window.toggleStatus = function(productId) {
            console.log('Nueva toggleStatus ejecutada para:', productId);

            // Obtener información del producto
            const productRow = document.querySelector(`[data-product-id="${productId}"]`);
            let isActive = false;
            let productName = 'Producto';

            if (productRow) {
                const statusBadge = productRow.querySelector('td:nth-child(5) .badge');
                const nameElement = productRow.querySelector('.producto-nombre');

                isActive = statusBadge && statusBadge.textContent.trim() === 'Activo';
                productName = nameElement ? nameElement.textContent.trim() : 'Producto';
            }

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
                statusHeader.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                statusIconContainer.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
                statusIcon.className = 'bi bi-pause-fill text-warning fs-1';
                statusTitle.textContent = '¿Deseas desactivar este producto?';
                statusMessage.textContent = 'El producto no será visible en el catálogo.';
                statusBtn.className = 'btn btn-warning';
                statusBtnIcon.className = 'bi bi-pause me-1';
                statusBtnText.textContent = 'Desactivar';
            } else {
                // Activar
                statusHeader.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                statusIconContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                statusIcon.className = 'bi bi-play-fill text-success fs-1';
                statusTitle.textContent = '¿Deseas activar este producto?';
                statusMessage.textContent = 'El producto será visible en el catálogo.';
                statusBtn.className = 'btn btn-success';
                statusBtnIcon.className = 'bi bi-play me-1';
                statusBtnText.textContent = 'Activar';
            }

            // Configurar botón de confirmación
            statusBtn.onclick = function() {
                document.getElementById(`toggle-form-${productId}`).submit();
            };

            // Mostrar modal
            const modalElement = document.getElementById('statusConfirmModal');
            if (modalElement) {
                console.log('Mostrando modal de estado');
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');

                // Crear backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        };

        // Función para cerrar modales
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

        // Event listeners para cerrar modales
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) closeModal(modal.id);
            });
        });

        // Cerrar con backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) closeModal(openModal.id);
            }
        });

        console.log('Funciones sobrescritas exitosamente');
    }, 1000); // Esperar 1 segundo
});