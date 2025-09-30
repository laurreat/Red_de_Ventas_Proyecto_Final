/**
 * Módulo para gestión de descarga de datos del perfil
 * Separado para mejor organización del código
 */

/**
 * Función para descargar datos del usuario
 */
function descargarDatos() {
    const btn = document.getElementById('descargar-datos-btn');
    if (!btn) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generando...';
    btn.disabled = true;

    // Crear link de descarga usando la ruta global
    const downloadRoute = window.perfilRoutes ? window.perfilRoutes.downloadData : '#';
    const a = document.createElement('a');
    a.href = downloadRoute;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    // Feedback visual
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-check me-1"></i>¡Descargado!';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }, 2000);
    }, 1000);
}

/**
 * Inicializar el módulo de descarga
 */
function initializeDownloadModule() {
    const descargarBtn = document.getElementById('descargar-datos-btn');

    if (descargarBtn) {
        descargarBtn.addEventListener('click', descargarDatos);
        console.log('✅ Módulo de descarga inicializado');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeDownloadModule);

// Exponer funciones globalmente
window.descargarDatos = descargarDatos;