/**
 * Módulo para gestión de actividad del perfil
 * Separado para mejor organización del código
 */

console.log('🚀 Cargando perfil-activity.js...');

/**
 * Función para ver actividad del usuario
 */
function verActividad() {
    console.log('📊 Abriendo modal de actividad');

    const modalElement = document.getElementById('activityModal');
    if (!modalElement) {
        console.error('❌ Modal de actividad no encontrado');
        alert('Error: No se pudo encontrar el modal de actividad. Por favor, recarga la página.');
        return;
    }

    // Verificar que las rutas estén disponibles
    if (!window.perfilRoutes || !window.perfilRoutes.activity) {
        console.error('❌ Rutas de perfil no disponibles');
        alert('Error: Configuración de rutas no disponible. Por favor, recarga la página.');
        return;
    }

    // Limpiar modal instance previo
    const existingModal = bootstrap.Modal.getInstance(modalElement);
    if (existingModal) {
        existingModal.dispose();
    }

    // Crear nueva instancia del modal
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: true,
        focus: true
    });

    // Cargar contenido inicial
    document.getElementById('activityContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3"></div>
            <h6>Cargando actividad...</h6>
            <p class="small text-muted">Por favor espera...</p>
        </div>
    `;

    // Mostrar modal
    console.log('🚀 Mostrando modal...');
    modal.show();

    // Event listener para cuando se muestre el modal
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('✅ Modal mostrado correctamente');
    }, { once: true });

    // Fetch data después de mostrar el modal
    setTimeout(() => {
        const activityRoute = window.perfilRoutes ? window.perfilRoutes.activity : '#';
        fetch(activityRoute)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarActividad(data.data);
                } else {
                    mostrarError(data.message || 'Error al cargar actividad');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error de conexión: ' + error.message);
            });
    }, 100);

    // Event listener para cerrar el modal
    modalElement.addEventListener('hidden.bs.modal', function () {
        console.log('🔄 Modal cerrado, limpiando contenido...');
        document.getElementById('activityContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3"></div>
                <h6 class="text-muted">Cargando actividad...</h6>
                <p class="small text-muted">Obteniendo tus datos más recientes</p>
            </div>
        `;
    }, { once: true });
}

/**
 * Función para mostrar actividad
 */
function mostrarActividad(data) {
    let html = '<div class="row">';

    // Resumen
    html += '<div class="col-12 mb-4">';
    html += '<h6><i class="bi bi-graph-up me-2"></i>Resumen de Actividad</h6>';
    html += '<div class="row">';
    html += `<div class="col-md-3 text-center mb-2">
                <div class="border rounded p-3">
                    <h4 class="text-primary">${data.resumen?.pedidos_como_cliente || 0}</h4>
                    <small class="text-muted">Como Cliente</small>
                </div>
             </div>`;
    html += `<div class="col-md-3 text-center mb-2">
                <div class="border rounded p-3">
                    <h4 class="text-primary">${data.resumen?.pedidos_como_vendedor || 0}</h4>
                    <small class="text-muted">Como Vendedor</small>
                </div>
             </div>`;
    html += `<div class="col-md-3 text-center mb-2">
                <div class="border rounded p-3">
                    <h4 class="text-primary">${data.resumen?.total_referidos || 0}</h4>
                    <small class="text-muted">Referidos</small>
                </div>
             </div>`;
    html += `<div class="col-md-3 text-center mb-2">
                <div class="border rounded p-3">
                    <h4 class="text-primary">${data.resumen?.accesos_ultimo_mes || 0}</h4>
                    <small class="text-muted">Accesos/Mes</small>
                </div>
             </div>`;
    html += '</div></div>';

    // Pedidos recientes
    if (data.pedidos && data.pedidos.length > 0) {
        html += '<div class="col-md-6">';
        html += '<h6><i class="bi bi-cart me-2"></i>Pedidos Recientes</h6>';
        data.pedidos.slice(0, 5).forEach(pedido => {
            const fecha = new Date(pedido.created_at).toLocaleDateString('es-CO');
            const badgeClass = pedido.estado === 'entregado' ? 'success' :
                             pedido.estado === 'cancelado' ? 'danger' : 'warning';
            html += `<div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <small><strong>${pedido.numero_pedido}</strong></small><br>
                            <span class="badge bg-${badgeClass}">${pedido.estado}</span>
                            <small class="text-muted ms-2">${pedido.tipo}</small>
                        </div>
                        <small class="text-muted">${fecha}</small>
                     </div>`;
        });
        html += '</div>';
    }

    // Referidos
    if (data.usuarios_referidos && data.usuarios_referidos.length > 0) {
        html += '<div class="col-md-6">';
        html += '<h6><i class="bi bi-people me-2"></i>Referidos Recientes</h6>';
        data.usuarios_referidos.slice(0, 5).forEach(usuario => {
            const fecha = new Date(usuario.created_at).toLocaleDateString('es-CO');
            html += `<div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <small><strong>${usuario.name} ${usuario.apellidos}</strong></small><br>
                            <span class="badge bg-info">${usuario.rol}</span>
                        </div>
                        <small class="text-muted">${fecha}</small>
                     </div>`;
        });
        html += '</div>';
    } else {
        html += '<div class="col-md-6">';
        html += '<h6><i class="bi bi-people me-2"></i>Referidos</h6>';
        html += '<p class="text-muted text-center">No tienes referidos recientes</p>';
        html += '</div>';
    }

    html += '</div>';
    document.getElementById('activityContent').innerHTML = html;
}

/**
 * Función para mostrar errores
 */
function mostrarError(mensaje) {
    document.getElementById('activityContent').innerHTML = `
        <div class="text-center py-4">
            <div class="alert alert-danger d-inline-block">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error:</strong> ${mensaje}
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary me-2" onclick="verActividad()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                </button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    `;
}

/**
 * Función para cerrar modal
 */
function cerrarModal() {
    console.log('❌ Cerrando modal de actividad...');
    const modalElement = document.getElementById('activityModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    } else {
        console.warn('⚠️ No se encontró instancia del modal');
    }
}

/**
 * Función de prueba para verificar funcionamiento
 */
function testModal() {
    console.log('🧪 Ejecutando prueba del modal...');
    verActividad();
}

/**
 * Inicializar el módulo de actividad
 */
function initializeActivityModule() {
    console.log('📊 Módulo de actividad de perfil inicializado');

    // Debug: verificar elementos disponibles
    console.log('🔍 Verificando elementos...');
    console.log('Modal:', document.getElementById('activityModal'));
    console.log('Botón actividad:', document.getElementById('ver-actividad-btn'));
    console.log('Rutas:', window.perfilRoutes);
    console.log('Bootstrap disponible:', typeof window.bootstrap !== 'undefined');

    // Vincular botón de ver actividad
    const verActividadBtn = document.getElementById('ver-actividad-btn');
    if (verActividadBtn) {
        console.log('✅ Botón de ver actividad encontrado, vinculando evento...');
        verActividadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔍 Botón de ver actividad clickeado');
            verActividad();
        });
        console.log('✅ Evento click vinculado correctamente');
    } else {
        console.warn('⚠️ Botón de ver actividad no encontrado - ID: ver-actividad-btn');
    }

    // Agregar función de prueba global para testing
    window.testModalActividad = testModal;
}

// Delegación de eventos para asegurar que funcione
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'ver-actividad-btn') {
        e.preventDefault();
        console.log('🎯 Click detectado via delegación de eventos');
        verActividad();
    }
});

// Vincular evento inmediatamente si el botón ya existe
(function() {
    console.log('🔄 Ejecutando vinculación inmediata...');
    const btn = document.getElementById('ver-actividad-btn');
    if (btn) {
        console.log('🎯 Botón encontrado inmediatamente, vinculando...');
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔍 Click detectado inmediatamente');
            verActividad();
        });
    } else {
        console.log('⏳ Botón no encontrado aún, esperando DOM...');
    }
})();

// Inicializar cuando el DOM esté listo con prioridad alta
document.addEventListener('DOMContentLoaded', function() {
    console.log('📚 DOM listo, ejecutando inicialización...');
    initializeActivityModule();
});

// También ejecutar después de que se carguen otros scripts
setTimeout(function() {
    console.log('🔄 Ejecutando inicialización de actividad con retraso...');
    initializeActivityModule();
}, 2000);

// Exponer funciones globalmente
window.verActividad = verActividad;
window.cerrarModal = cerrarModal;

console.log('✅ perfil-activity.js completamente cargado');