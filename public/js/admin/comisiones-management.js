/**
 * Módulo para gestión de comisiones
 * Separado para mejor organización del código
 */

/**
 * Función para calcular comisiones
 */
function calcularComisiones() {
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

    if (!fechaInicio || !fechaFin) {
        showErrorToast('Por favor selecciona un período válido');
        return;
    }

    // Mostrar loading
    const btnCalcular = document.querySelector('button[onclick="calcularComisiones()"]');
    const originalText = btnCalcular.innerHTML;
    btnCalcular.disabled = true;
    btnCalcular.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Calculando...';

    const calcularRoute = window.comisionesRoutes ? window.comisionesRoutes.calcular : '#';
    const csrfToken = window.comisionesCSRF || '';

    // Realizar petición AJAX
    fetch(calcularRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            const totalComisiones = data.total_comisiones || 0;
            const totalFormateado = typeof totalComisiones === 'number' ?
                totalComisiones.toLocaleString('es-CO') :
                parseFloat(totalComisiones).toLocaleString('es-CO');
            showSuccessToast(`Comisiones calculadas: $${totalFormateado}`);

            // Recargar la página para mostrar los nuevos datos
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorToast(data.mensaje || 'Error al calcular comisiones');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al calcular comisiones');
    })
    .finally(() => {
        btnCalcular.disabled = false;
        btnCalcular.innerHTML = originalText;
    });
}

/**
 * Función para exportar comisiones a PDF
 */
function exportarComisiones() {
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

    if (!fechaInicio || !fechaFin) {
        showErrorToast('Por favor selecciona un período válido');
        return;
    }

    const exportarRoute = window.comisionesRoutes ? window.comisionesRoutes.exportar : '#';
    const csrfToken = window.comisionesCSRF || '';

    // Crear formulario para descargar
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = exportarRoute;
    form.target = '_blank';

    // Token CSRF
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Fecha inicio
    const fechaInicioInput = document.createElement('input');
    fechaInicioInput.type = 'hidden';
    fechaInicioInput.name = 'fecha_inicio';
    fechaInicioInput.value = fechaInicio;
    form.appendChild(fechaInicioInput);

    // Fecha fin
    const fechaFinInput = document.createElement('input');
    fechaFinInput.type = 'hidden';
    fechaFinInput.name = 'fecha_fin';
    fechaFinInput.value = fechaFin;
    form.appendChild(fechaFinInput);

    // Formato
    const formatoInput = document.createElement('input');
    formatoInput.type = 'hidden';
    formatoInput.name = 'formato';
    formatoInput.value = 'pdf';
    form.appendChild(formatoInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    showSuccessToast('Generando PDF de comisiones...');
}

/**
 * Función helper para mostrar toast de éxito
 */
function showSuccessToast(message) {
    // Implementación básica, puede usar librerías como Toast de Bootstrap
    if (window.Toastify) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745",
        }).showToast();
    } else {
        console.log('SUCCESS:', message);
    }
}

/**
 * Función helper para mostrar toast de error
 */
function showErrorToast(message) {
    // Implementación básica, puede usar librerías como Toast de Bootstrap
    if (window.Toastify) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
        }).showToast();
    } else {
        console.error('ERROR:', message);
    }
}

/**
 * Inicializar el módulo de comisiones
 */
function initializeComisionesModule() {
    console.log('✅ Módulo de gestión de comisiones inicializado');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeComisionesModule);

// Exponer funciones globalmente
window.calcularComisiones = calcularComisiones;
window.exportarComisiones = exportarComisiones;