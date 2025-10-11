/**
 * Funciones de exportación para red de referidos
 * Separado para mejor organización del código
 */

/**
 * Exportar red a PDF usando el servidor
 */
function exportarRedPDF() {
    const cedula = document.querySelector('input[name="cedula"]') ?
        document.querySelector('input[name="cedula"]').value : '';

    // Crear formulario para enviar datos al servidor
    const form = document.createElement('form');
    form.method = 'POST';
    // La acción será configurada desde la vista principal
    form.action = window.exportRoutes ? window.exportRoutes.pdf : '#';
    form.target = '_blank';

    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }

    // Formato
    const formatoInput = document.createElement('input');
    formatoInput.type = 'hidden';
    formatoInput.name = 'formato';
    formatoInput.value = 'pdf';
    form.appendChild(formatoInput);

    // Cédula si está presente
    if (cedula) {
        const cedulaInput = document.createElement('input');
        cedulaInput.type = 'hidden';
        cedulaInput.name = 'cedula';
        cedulaInput.value = cedula;
        form.appendChild(cedulaInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    console.log('Generando PDF de red de referidos...');
}

/**
 * Exportar red a CSV usando el servidor
 */
function exportarRedCSV() {
    const cedula = document.querySelector('input[name="cedula"]') ?
        document.querySelector('input[name="cedula"]').value : '';

    // Crear formulario para enviar datos al servidor
    const form = document.createElement('form');
    form.method = 'POST';
    // La acción será configurada desde la vista principal
    form.action = window.exportRoutes ? window.exportRoutes.csv : '#';
    form.target = '_blank';

    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }

    // Formato
    const formatoInput = document.createElement('input');
    formatoInput.type = 'hidden';
    formatoInput.name = 'formato';
    formatoInput.value = 'csv';
    form.appendChild(formatoInput);

    // Cédula si está presente
    if (cedula) {
        const cedulaInput = document.createElement('input');
        cedulaInput.type = 'hidden';
        cedulaInput.name = 'cedula';
        cedulaInput.value = cedula;
        form.appendChild(cedulaInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    console.log('Generando CSV de red de referidos...');
}

/**
 * Descargar datos completos de la red
 */
function downloadNetworkData() {
    console.log('Descargando datos de la red...');

    // Verificar que los datos estén disponibles
    if (!window.nodes || !window.links) {
        console.error('Datos de red no disponibles');
        return;
    }

    // Preparar datos completos para exportación
    const networkAnalysis = {
        timestamp: new Date().toISOString(),
        usuario_seleccionado: window.usuarioSeleccionado || null,
        metricas: {
            total_nodos: window.nodes.length,
            total_conexiones: window.links.length,
            niveles_maximos: Math.max(...window.nodes.map(n => n.level || 0)) + 1,
            promedio_referidos: window.nodes.length > 0 ?
                (window.nodes.reduce((sum, n) => sum + (n.referidos_count || 0), 0) / window.nodes.length).toFixed(2) : 0
        },
        distribucion_por_tipo: {
            lideres: window.nodes.filter(n => n.tipo === 'lider').length,
            vendedores: window.nodes.filter(n => n.tipo === 'vendedor').length
        },
        distribucion_por_nivel: {},
        nodos: window.nodes.map(node => ({
            id: node.id,
            nombre: node.name,
            email: node.email,
            cedula: node.cedula || 'N/A',
            tipo: node.tipo,
            nivel: node.level + 1,
            referidos_count: node.referidos_count,
            parent_id: node.parentId
        })),
        conexiones: window.links.map(link => ({
            origen: typeof link.source === 'object' ? link.source.id : link.source,
            destino: typeof link.target === 'object' ? link.target.id : link.target
        }))
    };

    // Calcular distribución por nivel
    window.nodes.forEach(node => {
        const nivel = node.level + 1;
        networkAnalysis.distribucion_por_nivel[nivel] =
            (networkAnalysis.distribucion_por_nivel[nivel] || 0) + 1;
    });

    // Crear y descargar archivo JSON
    const blob = new Blob([JSON.stringify(networkAnalysis, null, 2)], {
        type: 'application/json'
    });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;

    const filename = window.usuarioSeleccionado ?
        'red-mlm-' + window.usuarioSeleccionado.cedula + '-' + new Date().toISOString().split('T')[0] + '.json' :
        'red-mlm-completa-' + new Date().toISOString().split('T')[0] + '.json';

    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    // Mostrar mensaje de éxito
    console.log('Datos descargados como:', filename);
}

// Exponer funciones globales
window.ReferidosExport = {
    exportarRedPDF,
    exportarRedCSV,
    downloadNetworkData
};