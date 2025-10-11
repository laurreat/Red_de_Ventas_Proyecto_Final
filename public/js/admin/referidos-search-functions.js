/**
 * Funciones de búsqueda y navegación para red de referidos
 * Separado para mejor organización del código
 */

/**
 * Limpiar búsqueda y mostrar red completa
 */
function clearSearch() {
    // Mostrar indicador de carga
    showLoadingIndicator('Cargando red completa');

    // Construir URL sin parámetros de búsqueda
    const url = new URL(window.location.href);
    url.searchParams.delete('cedula');
    url.searchParams.delete('search');

    // Redireccionar sin parámetros
    window.location.href = url.toString();
}

/**
 * Mostrar usuario aleatorio para demostración
 */
function showRandomUser() {
    // Mostrar indicador de carga
    showLoadingIndicator('Seleccionando usuario aleatorio');

    // Lista de cédulas de ejemplo para demostración
    const cedulasEjemplo = ['12345678', '87654321', '11111111', '22222222', '33333333'];
    const cedulaAleatoria = cedulasEjemplo[Math.floor(Math.random() * cedulasEjemplo.length)];

    document.getElementById('cedula_search').value = cedulaAleatoria;

    // Buscar el usuario aleatorio
    setTimeout(() => {
        const form = document.getElementById('searchUserForm');
        if (form) {
            form.dispatchEvent(new Event('submit'));
        }
    }, 500);
}

/**
 * Buscar red de usuario por cédula
 */
function searchUserNetwork(event) {
    event.preventDefault();
    const cedulaInput = document.getElementById('cedula_search');
    const cedula = cedulaInput.value.trim();

    // Validaciones en tiempo real
    if (!cedula) {
        showValidationError(cedulaInput, 'Por favor ingrese un número de cédula');
        return;
    }

    // Validar formato de cédula (solo números, 6-12 dígitos)
    if (!/^[0-9]{6,12}$/.test(cedula)) {
        showValidationError(cedulaInput, 'La cédula debe contener solo números (6-12 dígitos)');
        return;
    }

    // Limpiar errores de validación
    clearValidationError(cedulaInput);

    // Mostrar indicador de carga con mensaje específico
    showLoadingIndicator('Buscando usuario con cédula: ' + cedula);

    // Construir URL con parámetro de búsqueda
    const url = new URL(window.location.href);
    url.searchParams.set('cedula', cedula);

    // Redireccionar con el parámetro de búsqueda
    window.location.href = url.toString();
}

/**
 * Mostrar error de validación en campo
 */
function showValidationError(input, message) {
    // Remover errores previos
    clearValidationError(input);

    // Agregar clase de error
    input.classList.add('is-invalid');

    // Crear elemento de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    errorDiv.id = input.id + '_error';

    // Insertar después del input
    input.parentNode.insertBefore(errorDiv, input.nextSibling);

    // Focus en el input
    input.focus();
}

/**
 * Limpiar errores de validación
 */
function clearValidationError(input) {
    // Remover clase de error
    input.classList.remove('is-invalid');

    // Remover mensaje de error si existe
    const errorElement = document.getElementById(input.id + '_error');
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Mostrar indicador de carga
 */
function showLoadingIndicator(customMessage = null) {
    const indicator = document.getElementById('loading-indicator');
    if (indicator) {
        // Actualizar mensaje si se proporciona uno personalizado
        if (customMessage) {
            const messageElement = indicator.querySelector('.fw-medium');
            const submessageElement = indicator.querySelector('.text-muted');
            if (messageElement) {
                messageElement.textContent = customMessage;
            }
            if (submessageElement) {
                submessageElement.textContent = 'Procesando solicitud en tiempo real...';
            }
        }
        indicator.style.display = 'block';
    }
}

/**
 * Ocultar indicador de carga
 */
function hideLoadingIndicator() {
    const indicator = document.getElementById('loading-indicator');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

/**
 * Validación en tiempo real para campo de cédula
 */
function setupRealTimeValidation() {
    const cedulaInput = document.getElementById('cedula_search');
    if (cedulaInput) {
        cedulaInput.addEventListener('input', function() {
            const value = this.value.trim();

            // Limpiar errores previos si el campo está vacío
            if (!value) {
                clearValidationError(this);
                return;
            }

            // Validar formato en tiempo real
            if (!/^[0-9]*$/.test(value)) {
                showValidationError(this, 'Solo se permiten números');
            } else if (value.length > 12) {
                showValidationError(this, 'La cédula no puede tener más de 12 dígitos');
            } else if (value.length > 0 && value.length < 6) {
                showValidationError(this, 'La cédula debe tener al menos 6 dígitos');
            } else {
                clearValidationError(this);
            }
        });

        // Limpiar errores cuando se hace focus
        cedulaInput.addEventListener('focus', function() {
            if (this.value.trim() === '') {
                clearValidationError(this);
            }
        });
    }
}

/**
 * Ver visualización (scroll a contenedor)
 */
function verVisualizacion() {
    // Enfocar en el contenedor de visualización
    const container = document.getElementById('network-container');
    if (container) {
        container.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
}

/**
 * Exportar red completa como JSON
 */
function exportarRed() {
    // Esta función será implementada por el módulo principal
    if (window.NetworkVisualization && window.nodes && window.links) {
        // Preparar datos para exportación
        const exportData = {
            timestamp: new Date().toISOString(),
            total_nodos: window.nodes.length,
            total_enlaces: window.links.length,
            nodos: window.nodes.map(node => ({
                id: node.id,
                nombre: node.name,
                email: node.email,
                tipo: node.tipo,
                nivel: node.level + 1,
                referidos_count: node.referidos_count
            })),
            enlaces: window.links.map(link => ({
                origen: typeof link.source === 'object' ? link.source.id : link.source,
                destino: typeof link.target === 'object' ? link.target.id : link.target
            }))
        };

        // Crear y descargar archivo JSON
        const blob = new Blob([JSON.stringify(exportData, null, 2)], {
            type: 'application/json'
        });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'red-mlm-' + new Date().toISOString().split('T')[0] + '.json';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
}

// Exponer funciones globales
window.ReferidosSearch = {
    clearSearch,
    showRandomUser,
    searchUserNetwork,
    verVisualizacion,
    exportarRed,
    setupRealTimeValidation,
    showLoadingIndicator,
    hideLoadingIndicator
};