/**
 * Módulo para formularios de roles (create/edit)
 * Separado para mejor organización del código
 */

/**
 * Actualizar estado de toggle de categoría
 */
function updateCategoryToggle() {
    document.querySelectorAll('.category-toggle').forEach(function(categoryToggle) {
        const category = categoryToggle.dataset.category;
        const categoryCheckboxes = document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`);

        const checkedCount = Array.from(categoryCheckboxes).filter(cb => cb.checked).length;

        if (checkedCount === 0) {
            categoryToggle.checked = false;
            categoryToggle.indeterminate = false;
        } else if (checkedCount === categoryCheckboxes.length) {
            categoryToggle.checked = true;
            categoryToggle.indeterminate = false;
        } else {
            categoryToggle.checked = false;
            categoryToggle.indeterminate = true;
        }
    });
}

/**
 * Configurar toggle de categorías
 */
function setupCategoryToggles() {
    document.querySelectorAll('.category-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const category = this.dataset.category;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`);

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = toggle.checked;
            });
        });
    });
}

/**
 * Configurar checkboxes de permisos individuales
 */
function setupPermissionCheckboxes() {
    document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateCategoryToggle);
    });
}

/**
 * Inicializar el módulo de formularios de roles
 */
function initializeRoleFormsModule() {
    // Configurar toggle de categorías
    setupCategoryToggles();

    // Configurar checkboxes de permisos individuales
    setupPermissionCheckboxes();

    // Inicializar estado de toggles de categoría
    updateCategoryToggle();

    console.log('✅ Módulo de formularios de roles inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeRoleFormsModule);

// Exponer funciones globalmente
window.updateCategoryToggle = updateCategoryToggle;
window.setupCategoryToggles = setupCategoryToggles;
window.setupPermissionCheckboxes = setupPermissionCheckboxes;