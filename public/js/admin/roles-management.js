/**
 * Módulo para gestión de roles
 * Separado para mejor organización del código
 */

/**
 * Cambiar estado de un rol (activar/desactivar)
 */
function toggleStatus(roleId) {
    if (confirm('¿Estás seguro de cambiar el estado de este rol?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/roles/${roleId}/toggle`;
        form.submit();
    }
}

/**
 * Eliminar un rol
 */
function deleteRole(roleId) {
    if (confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/roles/${roleId}`;
        form.submit();
    }
}

/**
 * Inicializar el módulo de roles
 */
function initializeRolesModule() {
    console.log('✅ Módulo de gestión de roles inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeRolesModule);

// Exponer funciones globalmente
window.toggleStatus = toggleStatus;
window.deleteRole = deleteRole;