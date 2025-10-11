/**
 * Módulo para reportes de ventas
 * Separado para mejor organización del código
 */

/**
 * Función para exportar reporte de ventas a PDF
 */
function exportarReporte() {
    // Obtener los valores actuales de los filtros
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
    const vendedorId = document.querySelector('select[name="vendedor_id"]').value;

    // Mostrar mensaje de carga
    const button = document.getElementById('exportButton');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise me-1 spin"></i> Generando PDF...';
    button.disabled = true;

    // Construir URL con parámetros
    const params = new URLSearchParams();
    if (fechaInicio) params.append('fecha_inicio', fechaInicio);
    if (fechaFin) params.append('fecha_fin', fechaFin);
    if (vendedorId) params.append('vendedor_id', vendedorId);
    params.append('format', 'pdf');

    const exportRoute = window.reportesRoutes ? window.reportesRoutes.export : '#';
    const url = `${exportRoute}?${params.toString()}`;

    // Usar fetch para manejar errores correctamente
    const csrfToken = window.reportesCSRF || '';

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(error => Promise.reject(error));
        }
        return response.blob().then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;

            // Generar nombre de archivo con fecha
            const today = new Date();
            const fechaStr = today.toISOString().split('T')[0];
            link.download = `reporte_ventas_${fechaStr}.pdf`;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);

            // Mostrar mensaje de éxito
            if (typeof showSuccessToast === 'function') {
                showSuccessToast('¡Reporte PDF descargado exitosamente!');
            }
        });
    })
    .catch(error => {
        console.error('Error al exportar reporte:', error);

        // Mostrar mensaje de error
        if (typeof showErrorToast === 'function') {
            const mensaje = error.error || error.message || 'Error al generar el reporte PDF. Inténtalo nuevamente.';
            showErrorToast(mensaje);
        } else {
            alert('Error al generar el reporte PDF. Inténtalo nuevamente.');
        }
    })
    .finally(() => {
        // Restaurar botón
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

/**
 * Inicializar el módulo de reportes
 */
function initializeReportesModule() {
    // Agregar estilos para la animación de carga
    const style = document.createElement('style');
    style.textContent = `
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    console.log('✅ Módulo de reportes de ventas inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeReportesModule);

// Exponer funciones globalmente
window.exportarReporte = exportarReporte;