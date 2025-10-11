<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CatalogoPublicoController;

// Página principal - Welcome page renovada
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Catálogo público (ruta alternativa)
Route::get('/catalogo', [CatalogoPublicoController::class, 'index'])->name('catalogo.index');

// Ruta para ver detalles de producto público
Route::get('/catalogo/{producto}', [CatalogoPublicoController::class, 'show']);

// Ruta específica para logout y redirección al login
Route::get('/inicio', function () {
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
})->name('inicio');

// Rutas de autenticación
Auth::routes();

// Dashboard principal (requiere autenticación)
//rol de cliente
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Rutas protegidas por roles
Route::middleware(['auth', 'role'])->group(function () {
    
/**
 * RUTAS DEL CLIENTE
 * Protegidas con middleware auth y verified
 */

    Route::prefix('cliente')->name('cliente.')->middleware(['auth', 'verified'])->group(function () {
        
        // Dashboard Principal
        Route::get('/dashboard', [ClienteDashboardController::class, 'index'])
            ->name('dashboard');

        // Favoritos
        Route::post('/favoritos/agregar', [ClienteDashboardController::class, 'agregarFavorito'])
            ->name('favoritos.agregar');
        
        Route::post('/favoritos/eliminar', [ClienteDashboardController::class, 'eliminarFavorito'])
            ->name('favoritos.eliminar');

        // Perfil
        Route::post('/perfil/actualizar', [ClienteDashboardController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');

        // Pedidos
        Route::post('/pedidos/crear', [ClienteDashboardController::class, 'crearPedido'])
            ->name('pedidos.crear');
        
        Route::get('/pedidos/historial', [ClienteDashboardController::class, 'historialPedidos'])
            ->name('pedidos.historial');
        
        Route::get('/pedidos/{id}', [ClienteDashboardController::class, 'verPedido'])
            ->name('pedidos.ver');
        
        Route::post('/pedidos/{id}/cancelar', [ClienteDashboardController::class, 'cancelarPedido'])
            ->name('pedidos.cancelar');

        // Recomendaciones
        Route::get('/productos/recomendados', [ClienteDashboardController::class, 'productosRecomendados'])
            ->name('productos.recomendados');
    });

    // Rutas para Administradores
    Route::middleware(['role:administrador'])->group(function () {
        // Dashboard SPA del Administrador
        Route::get('admin/spa', function () {
            return view('dashboard.admin-spa');
        })->name('admin.spa');

        // Gestión de usuarios
        Route::resource('admin/users', UserController::class, ['as' => 'admin']);
        Route::patch('admin/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // Productos
        Route::resource('admin/productos', \App\Http\Controllers\Admin\ProductoController::class, ['as' => 'admin']);
        Route::patch('admin/productos/{producto}/toggle-status', [\App\Http\Controllers\Admin\ProductoController::class, 'toggleStatus'])->name('admin.productos.toggle-status');

        // Pedidos
        Route::resource('admin/pedidos', \App\Http\Controllers\Admin\PedidoController::class, ['as' => 'admin']);
        Route::patch('admin/pedidos/{pedido}/status', [\App\Http\Controllers\Admin\PedidoController::class, 'updateStatus'])->name('admin.pedidos.update-status');
        Route::post('admin/pedidos/search-cliente', [\App\Http\Controllers\Admin\PedidoController::class, 'searchCliente'])->name('admin.pedidos.search-cliente');
        Route::post('admin/pedidos/search-vendedor', [\App\Http\Controllers\Admin\PedidoController::class, 'searchVendedor'])->name('admin.pedidos.search-vendedor');

        // Roles y Permisos
        Route::resource('admin/roles', \App\Http\Controllers\Admin\RoleController::class, ['as' => 'admin']);
        Route::patch('admin/roles/{role}/toggle', [\App\Http\Controllers\Admin\RoleController::class, 'toggleActive'])->name('admin.roles.toggle');
        Route::get('admin/roles/permissions/list', [\App\Http\Controllers\Admin\RoleController::class, 'permissions'])->name('admin.roles.permissions');
        Route::post('admin/roles/initialize-system', [\App\Http\Controllers\Admin\RoleController::class, 'initializeSystemRoles'])->name('admin.roles.initialize');
        Route::get('admin/roles/{role}/assign-users', [\App\Http\Controllers\Admin\RoleController::class, 'assignUsers'])->name('admin.roles.assign-users');
        Route::post('admin/roles/{role}/update-users', [\App\Http\Controllers\Admin\RoleController::class, 'updateUserRoles'])->name('admin.roles.update-users');

        // Reportes
        Route::get('admin/reportes/ventas', [\App\Http\Controllers\Admin\ReporteController::class, 'ventas'])->name('admin.reportes.ventas');
        Route::get('admin/reportes/productos', [\App\Http\Controllers\Admin\ReporteController::class, 'productos'])->name('admin.reportes.productos');
        Route::get('admin/reportes/comisiones', [\App\Http\Controllers\Admin\ReporteController::class, 'comisiones'])->name('admin.reportes.comisiones');
        Route::get('admin/reportes/exportar-ventas', [\App\Http\Controllers\Admin\ReporteController::class, 'exportarVentas'])->name('admin.reportes.exportar-ventas');

        // Comisiones
        Route::get('admin/comisiones', [\App\Http\Controllers\Admin\ComisionController::class, 'index'])->name('admin.comisiones.index');
        Route::get('admin/comisiones/{id}', [\App\Http\Controllers\Admin\ComisionController::class, 'show'])->name('admin.comisiones.show');
        Route::post('admin/comisiones/calcular', [\App\Http\Controllers\Admin\ComisionController::class, 'calcular'])->name('admin.comisiones.calcular');
        Route::post('admin/comisiones/exportar', [\App\Http\Controllers\Admin\ComisionController::class, 'exportar'])->name('admin.comisiones.exportar');

        // Red de Referidos
        Route::get('admin/referidos', [\App\Http\Controllers\Admin\ReferidoController::class, 'index'])->name('admin.referidos.index');
        Route::post('admin/referidos/exportar', [\App\Http\Controllers\Admin\ReferidoController::class, 'exportar'])->name('admin.referidos.exportar');
        Route::get('admin/referidos/estadisticas', [\App\Http\Controllers\Admin\ReferidoController::class, 'estadisticas'])->name('admin.referidos.estadisticas');
        Route::get('admin/referidos/red/{id?}', [\App\Http\Controllers\Admin\ReferidoController::class, 'red'])->name('admin.referidos.red');
        Route::get('admin/referidos/{id}', [\App\Http\Controllers\Admin\ReferidoController::class, 'show'])->name('admin.referidos.show');

        // Configuración
        Route::get('admin/configuracion', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('admin.configuracion.index');
        Route::post('admin/configuracion/general', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'updateGeneral'])->name('admin.configuracion.update-general');
        Route::post('admin/configuracion/mlm', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'updateMlm'])->name('admin.configuracion.update-mlm');
        Route::post('admin/configuracion/pedidos', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'updatePedidos'])->name('admin.configuracion.update-pedidos');
        Route::post('admin/configuracion/notificaciones', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'updateNotificaciones'])->name('admin.configuracion.update-notificaciones');
        Route::post('admin/configuracion/backup', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'backup'])->name('admin.configuracion.backup');
        Route::post('admin/configuracion/limpiar-cache', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'limpiarCache'])->name('admin.configuracion.limpiar-cache');
        Route::post('admin/configuracion/limpiar-logs', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'limpiarLogs'])->name('admin.configuracion.limpiar-logs');
        Route::get('admin/configuracion/info-sistema', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'infoSistema'])->name('admin.configuracion.info-sistema');

        // Respaldos
        Route::get('admin/respaldos', [\App\Http\Controllers\Admin\RespaldoController::class, 'index'])->name('admin.respaldos.index');
        Route::post('admin/respaldos/create', [\App\Http\Controllers\Admin\RespaldoController::class, 'create'])->name('admin.respaldos.create');
        Route::get('admin/respaldos/{filename}/download', [\App\Http\Controllers\Admin\RespaldoController::class, 'download'])->name('admin.respaldos.download');
        Route::get('admin/respaldos/{filename}/view', [\App\Http\Controllers\Admin\RespaldoController::class, 'view'])->name('admin.respaldos.view');
        Route::delete('admin/respaldos/{filename}', [\App\Http\Controllers\Admin\RespaldoController::class, 'delete'])->name('admin.respaldos.delete');
        Route::post('admin/respaldos/{filename}/restore', [\App\Http\Controllers\Admin\RespaldoController::class, 'restore'])->name('admin.respaldos.restore');
        Route::post('admin/respaldos/cleanup', [\App\Http\Controllers\Admin\RespaldoController::class, 'cleanup'])->name('admin.respaldos.cleanup');

        // Logs del Sistema
        Route::get('admin/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');
        Route::get('admin/logs/{filename}', [\App\Http\Controllers\Admin\LogController::class, 'show'])->name('admin.logs.show');
        Route::get('admin/logs/{filename}/download', [\App\Http\Controllers\Admin\LogController::class, 'download'])->name('admin.logs.download');
        Route::delete('admin/logs/{filename}', [\App\Http\Controllers\Admin\LogController::class, 'delete'])->name('admin.logs.delete');
        Route::post('admin/logs/clear', [\App\Http\Controllers\Admin\LogController::class, 'clear'])->name('admin.logs.clear');
        Route::post('admin/logs/cleanup', [\App\Http\Controllers\Admin\LogController::class, 'cleanup'])->name('admin.logs.cleanup');
        Route::post('admin/logs/export', [\App\Http\Controllers\Admin\LogController::class, 'export'])->name('admin.logs.export');
        Route::get('admin/logs-stats', [\App\Http\Controllers\Admin\LogController::class, 'stats'])->name('admin.logs.stats');

        // Notificaciones (rutas específicas ANTES de las dinámicas)
        Route::get('admin/notificaciones', [\App\Http\Controllers\Admin\NotificacionController::class, 'index'])->name('admin.notificaciones.index');
        Route::get('admin/notificaciones/dropdown', [\App\Http\Controllers\Admin\NotificacionController::class, 'dropdown'])->name('admin.notificaciones.dropdown');
        Route::get('admin/notificaciones/contar-no-leidas', [\App\Http\Controllers\Admin\NotificacionController::class, 'contarNoLeidas'])->name('admin.notificaciones.contar-no-leidas');
        Route::post('admin/notificaciones/marcar-todas-leidas', [\App\Http\Controllers\Admin\NotificacionController::class, 'marcarTodasLeidas'])->name('admin.notificaciones.marcar-todas-leidas');
        Route::post('admin/notificaciones/crear-pruebas', [\App\Http\Controllers\Admin\NotificacionController::class, 'crearPruebas'])->name('admin.notificaciones.crear-pruebas');
        Route::delete('admin/notificaciones/limpiar-leidas', [\App\Http\Controllers\Admin\NotificacionController::class, 'limpiarLeidas'])->name('admin.notificaciones.limpiar-leidas');
        Route::get('admin/notificaciones/{id}', [\App\Http\Controllers\Admin\NotificacionController::class, 'show'])->name('admin.notificaciones.show');
        Route::post('admin/notificaciones/{id}/marcar-leida', [\App\Http\Controllers\Admin\NotificacionController::class, 'marcarLeida'])->name('admin.notificaciones.marcar-leida');
        Route::delete('admin/notificaciones/{id}', [\App\Http\Controllers\Admin\NotificacionController::class, 'eliminar'])->name('admin.notificaciones.eliminar');

        // Mi Perfil
        Route::get('admin/perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'index'])->name('admin.perfil.index');
        Route::post('admin/perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'update'])->name('admin.perfil.update');
        Route::post('admin/perfil/password', [\App\Http\Controllers\Admin\PerfilController::class, 'updatePassword'])->name('admin.perfil.update-password');
        Route::post('admin/perfil/notifications', [\App\Http\Controllers\Admin\PerfilController::class, 'updateNotifications'])->name('admin.perfil.update-notifications');
        Route::post('admin/perfil/privacy', [\App\Http\Controllers\Admin\PerfilController::class, 'updatePrivacy'])->name('admin.perfil.update-privacy');
        Route::delete('admin/perfil/avatar', [\App\Http\Controllers\Admin\PerfilController::class, 'deleteAvatar'])->name('admin.perfil.delete-avatar');
        Route::get('admin/perfil/download', [\App\Http\Controllers\Admin\PerfilController::class, 'downloadData'])->name('admin.perfil.download-data');
        Route::get('admin/perfil/activity', [\App\Http\Controllers\Admin\PerfilController::class, 'activity'])->name('admin.perfil.activity');

    });
    
    // Rutas para Líderes y Administradores
    Route::middleware(['role:lider,administrador'])->group(function () {
        // Dashboard del Líder
        Route::get('lider/dashboard', [\App\Http\Controllers\Lider\DashboardController::class, 'index'])->name('lider.dashboard');

        // Gestión de Equipo
        Route::get('lider/equipo', [\App\Http\Controllers\Lider\EquipoController::class, 'index'])->name('lider.equipo.index');
        Route::get('lider/equipo/{id}', [\App\Http\Controllers\Lider\EquipoController::class, 'show'])->name('lider.equipo.show');
        Route::post('lider/equipo/{id}/asignar-meta', [\App\Http\Controllers\Lider\EquipoController::class, 'asignarMeta'])->name('lider.equipo.asignar-meta');

        // Red de Referidos del Líder
        Route::get('lider/referidos', [\App\Http\Controllers\Lider\ReferidoController::class, 'index'])->name('lider.referidos.index');
        Route::get('lider/referidos/red', [\App\Http\Controllers\Lider\ReferidoController::class, 'red'])->name('lider.referidos.red');

        // Rendimiento del Equipo
        Route::get('lider/rendimiento', [\App\Http\Controllers\Lider\RendimientoController::class, 'index'])->name('lider.rendimiento.index');

        // Ventas del Equipo
        Route::get('lider/ventas', [\App\Http\Controllers\Lider\VentaController::class, 'index'])->name('lider.ventas.index');
        Route::get('lider/ventas/{id}', [\App\Http\Controllers\Lider\VentaController::class, 'show'])->name('lider.ventas.show');

        // Comisiones del Líder
        Route::get('lider/comisiones', [\App\Http\Controllers\Lider\ComisionController::class, 'index'])->name('lider.comisiones.index');
        Route::get('lider/comisiones/solicitar', [\App\Http\Controllers\Lider\ComisionController::class, 'solicitar'])->name('lider.comisiones.solicitar');
        Route::post('lider/comisiones/solicitar', [\App\Http\Controllers\Lider\ComisionController::class, 'procesarSolicitud'])->name('lider.comisiones.procesar');

        // Metas y Objetivos
        Route::get('lider/metas', [\App\Http\Controllers\Lider\MetaController::class, 'index'])->name('lider.metas.index');
        Route::post('lider/metas', [\App\Http\Controllers\Lider\MetaController::class, 'store'])->name('lider.metas.store');
        Route::put('lider/metas/{id}', [\App\Http\Controllers\Lider\MetaController::class, 'update'])->name('lider.metas.update');
        Route::post('lider/metas/asignar-equipo', [\App\Http\Controllers\Lider\MetaController::class, 'asignarMetaEquipo'])->name('lider.metas.asignar-equipo');

        // Reportes del Líder
        Route::get('lider/reportes/ventas', [\App\Http\Controllers\Lider\ReporteController::class, 'ventas'])->name('lider.reportes.ventas');
        Route::get('lider/reportes/equipo', [\App\Http\Controllers\Lider\ReporteController::class, 'equipo'])->name('lider.reportes.equipo');
        Route::post('lider/reportes/exportar', [\App\Http\Controllers\Lider\ReporteController::class, 'exportar'])->name('lider.reportes.exportar');

        // Perfil y Configuración del Líder
        Route::get('lider/perfil', [\App\Http\Controllers\Lider\PerfilController::class, 'index'])->name('lider.perfil.index');
        Route::put('lider/perfil', [\App\Http\Controllers\Lider\PerfilController::class, 'update'])->name('lider.perfil.update');
        Route::get('lider/configuracion', [\App\Http\Controllers\Lider\ConfiguracionController::class, 'index'])->name('lider.configuracion.index');
        Route::put('lider/configuracion', [\App\Http\Controllers\Lider\ConfiguracionController::class, 'update'])->name('lider.configuracion.update');

        // Capacitación del Equipo
        Route::get('lider/capacitacion', [\App\Http\Controllers\Lider\CapacitacionController::class, 'index'])->name('lider.capacitacion.index');
        Route::get('lider/capacitacion/{id}', [\App\Http\Controllers\Lider\CapacitacionController::class, 'show'])->name('lider.capacitacion.show');
        Route::post('lider/capacitacion/asignar', [\App\Http\Controllers\Lider\CapacitacionController::class, 'asignar'])->name('lider.capacitacion.asignar');
    });
    
    // Rutas para Vendedores, Líderes y Administradores
    Route::middleware(['role:vendedor,lider,administrador'])->group(function () {
        // Dashboard del Vendedor
        Route::get('vendedor/dashboard', [\App\Http\Controllers\Vendedor\DashboardController::class, 'index'])->name('vendedor.dashboard');
        Route::get('vendedor/dashboard/ventas-chart', [\App\Http\Controllers\Vendedor\DashboardController::class, 'getVentasChart'])->name('vendedor.dashboard.ventas-chart');
        Route::get('vendedor/dashboard/metricas', [\App\Http\Controllers\Vendedor\DashboardController::class, 'getMetricasRapidas'])->name('vendedor.dashboard.metricas');

        // Gestión de Pedidos
        Route::get('vendedor/pedidos', [\App\Http\Controllers\Vendedor\PedidoController::class, 'index'])->name('vendedor.pedidos.index');
        Route::get('vendedor/pedidos/crear', [\App\Http\Controllers\Vendedor\PedidoController::class, 'create'])->name('vendedor.pedidos.create');
        Route::post('vendedor/pedidos', [\App\Http\Controllers\Vendedor\PedidoController::class, 'store'])->name('vendedor.pedidos.store');
        Route::get('vendedor/pedidos/{id}', [\App\Http\Controllers\Vendedor\PedidoController::class, 'show'])->name('vendedor.pedidos.show');
        Route::get('vendedor/pedidos/{id}/editar', [\App\Http\Controllers\Vendedor\PedidoController::class, 'edit'])->name('vendedor.pedidos.edit');
        Route::put('vendedor/pedidos/{id}', [\App\Http\Controllers\Vendedor\PedidoController::class, 'update'])->name('vendedor.pedidos.update');
        Route::patch('vendedor/pedidos/{id}/estado', [\App\Http\Controllers\Vendedor\PedidoController::class, 'updateEstado'])->name('vendedor.pedidos.update-estado');
        Route::delete('vendedor/pedidos/{id}', [\App\Http\Controllers\Vendedor\PedidoController::class, 'destroy'])->name('vendedor.pedidos.destroy');
        Route::post('vendedor/pedidos/exportar', [\App\Http\Controllers\Vendedor\PedidoController::class, 'exportar'])->name('vendedor.pedidos.exportar');

        // Ventas (alias para pedidos)
        Route::get('vendedor/ventas/crear', [\App\Http\Controllers\Vendedor\PedidoController::class, 'create'])->name('vendedor.ventas.crear');
        Route::get('vendedor/ventas/historial', [\App\Http\Controllers\Vendedor\PedidoController::class, 'index'])->name('vendedor.ventas.historial');

        // Gestión de Clientes
        Route::get('vendedor/clientes', [\App\Http\Controllers\Vendedor\ClienteController::class, 'index'])->name('vendedor.clientes.index');
        Route::get('vendedor/clientes/crear', [\App\Http\Controllers\Vendedor\ClienteController::class, 'create'])->name('vendedor.clientes.crear');
        Route::post('vendedor/clientes', [\App\Http\Controllers\Vendedor\ClienteController::class, 'store'])->name('vendedor.clientes.store');
        Route::get('vendedor/clientes/{id}', [\App\Http\Controllers\Vendedor\ClienteController::class, 'show'])->name('vendedor.clientes.show');
        Route::get('vendedor/clientes/{id}/editar', [\App\Http\Controllers\Vendedor\ClienteController::class, 'edit'])->name('vendedor.clientes.edit');
        Route::put('vendedor/clientes/{id}', [\App\Http\Controllers\Vendedor\ClienteController::class, 'update'])->name('vendedor.clientes.update');
        Route::get('vendedor/clientes/seguimiento', [\App\Http\Controllers\Vendedor\ClienteController::class, 'seguimiento'])->name('vendedor.clientes.seguimiento');
        Route::get('vendedor/clientes/buscar', [\App\Http\Controllers\Vendedor\ClienteController::class, 'buscar'])->name('vendedor.clientes.buscar');
        Route::post('vendedor/clientes/exportar', [\App\Http\Controllers\Vendedor\ClienteController::class, 'exportar'])->name('vendedor.clientes.exportar');

        // Comisiones del Vendedor
        Route::get('vendedor/comisiones', [\App\Http\Controllers\Vendedor\ComisionController::class, 'index'])->name('vendedor.comisiones.index');
        Route::get('vendedor/comisiones/{id}', [\App\Http\Controllers\Vendedor\ComisionController::class, 'show'])->name('vendedor.comisiones.show');
        Route::get('vendedor/comisiones/solicitar', [\App\Http\Controllers\Vendedor\ComisionController::class, 'solicitar'])->name('vendedor.comisiones.solicitar');
        Route::post('vendedor/comisiones/solicitar', [\App\Http\Controllers\Vendedor\ComisionController::class, 'procesarSolicitud'])->name('vendedor.comisiones.procesar');
        Route::get('vendedor/comisiones/historial', [\App\Http\Controllers\Vendedor\ComisionController::class, 'historial'])->name('vendedor.comisiones.historial');
        Route::get('vendedor/comisiones/evolucion', [\App\Http\Controllers\Vendedor\ComisionController::class, 'getEvolucionComisiones'])->name('vendedor.comisiones.evolucion');
        Route::post('vendedor/comisiones/exportar', [\App\Http\Controllers\Vendedor\ComisionController::class, 'exportar'])->name('vendedor.comisiones.exportar');

        // Red de Referidos del Vendedor
        Route::get('vendedor/referidos', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'index'])->name('vendedor.referidos.index');
        Route::get('vendedor/referidos/{id}', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'show'])->name('vendedor.referidos.show');
        Route::get('vendedor/referidos/invitar', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'invitar'])->name('vendedor.referidos.invitar');
        Route::post('vendedor/referidos/invitar', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'enviarInvitacion'])->name('vendedor.referidos.enviar-invitacion');
        Route::get('vendedor/referidos/ganancias', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'ganancias'])->name('vendedor.referidos.ganancias');
        Route::get('vendedor/referidos/red', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'red'])->name('vendedor.referidos.red');
        Route::get('vendedor/referidos/enlace', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'generarEnlaceReferido'])->name('vendedor.referidos.enlace');
        Route::post('vendedor/referidos/exportar', [\App\Http\Controllers\Vendedor\ReferidoController::class, 'exportar'])->name('vendedor.referidos.exportar');

        // Metas del Vendedor
        Route::get('vendedor/metas', [\App\Http\Controllers\Vendedor\MetaController::class, 'index'])->name('vendedor.metas.index');
        Route::post('vendedor/metas/actualizar', [\App\Http\Controllers\Vendedor\MetaController::class, 'actualizar'])->name('vendedor.metas.actualizar');
        Route::get('vendedor/metas/progreso', [\App\Http\Controllers\Vendedor\MetaController::class, 'getProgreso'])->name('vendedor.metas.progreso');
        Route::get('vendedor/metas/historial', [\App\Http\Controllers\Vendedor\MetaController::class, 'getHistorial'])->name('vendedor.metas.historial');
        Route::get('vendedor/metas/sugerir', [\App\Http\Controllers\Vendedor\MetaController::class, 'sugerirMeta'])->name('vendedor.metas.sugerir');

        // Reportes del Vendedor
        Route::get('vendedor/reportes/ventas', [\App\Http\Controllers\Vendedor\ReporteController::class, 'ventas'])->name('vendedor.reportes.ventas');
        Route::get('vendedor/reportes/rendimiento', [\App\Http\Controllers\Vendedor\ReporteController::class, 'rendimiento'])->name('vendedor.reportes.rendimiento');
        Route::get('vendedor/reportes/comisiones', [\App\Http\Controllers\Vendedor\ReporteController::class, 'comisiones'])->name('vendedor.reportes.comisiones');

        // Perfil del Vendedor
        Route::get('vendedor/perfil', [\App\Http\Controllers\Vendedor\PerfilController::class, 'index'])->name('vendedor.perfil.index');
        Route::get('vendedor/perfil/editar', [\App\Http\Controllers\Vendedor\PerfilController::class, 'edit'])->name('vendedor.perfil.edit');
        Route::put('vendedor/perfil', [\App\Http\Controllers\Vendedor\PerfilController::class, 'update'])->name('vendedor.perfil.update');
        Route::put('vendedor/perfil/password', [\App\Http\Controllers\Vendedor\PerfilController::class, 'updatePassword'])->name('vendedor.perfil.update-password');
        Route::delete('vendedor/perfil/avatar', [\App\Http\Controllers\Vendedor\PerfilController::class, 'eliminarAvatar'])->name('vendedor.perfil.eliminar-avatar');
        Route::get('vendedor/perfil/exportar-datos', [\App\Http\Controllers\Vendedor\PerfilController::class, 'exportarDatos'])->name('vendedor.perfil.exportar-datos');

        // Configuración del Vendedor
        Route::get('vendedor/configuracion', [\App\Http\Controllers\Vendedor\PerfilController::class, 'configuracion'])->name('vendedor.configuracion.index');
        Route::put('vendedor/configuracion', [\App\Http\Controllers\Vendedor\PerfilController::class, 'updateConfiguracion'])->name('vendedor.configuracion.update');
    });
});

// Redireccionar /home al dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
});

// Rutas de prueba para verificar alertas (TEMPORAL)
Route::get('/test/success', function () {
    return redirect('/admin/productos')->with('success', 'Mensaje de éxito de prueba - ¡Las alertas funcionan!');
});

Route::get('/test/error', function () {
    return redirect('/admin/productos')->with('error', 'Mensaje de error de prueba - ¡Las alertas funcionan!');
});

Route::get('/test/warning', function () {
    return redirect('/admin/productos')->with('warning', 'Mensaje de advertencia de prueba - ¡Las alertas funcionan!');
});

Route::get('/test/validation', function () {
    $validator = \Validator::make([], [
        'nombre' => 'required',
        'email' => 'required|email',
        'precio' => 'required|numeric'
    ]);
    return redirect('/admin/productos')->withErrors($validator);
});
Route::get('/test-modals-pedidos', function () { return view('admin.pedidos.test-modals'); });
