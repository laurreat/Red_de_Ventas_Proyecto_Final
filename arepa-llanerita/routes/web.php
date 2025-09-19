<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;

// Página principal - siempre mostrar login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Ruta específica para logout y redirección al login
Route::get('/inicio', function () {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
})->name('inicio');

// Rutas de autenticación
Auth::routes();

// Dashboard principal (requiere autenticación)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Rutas protegidas por roles
Route::middleware(['auth', 'role'])->group(function () {
    
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

        // Reportes
        Route::get('admin/reportes/ventas', [\App\Http\Controllers\Admin\ReporteController::class, 'ventas'])->name('admin.reportes.ventas');
        Route::get('admin/reportes/productos', [\App\Http\Controllers\Admin\ReporteController::class, 'productos'])->name('admin.reportes.productos');
        Route::get('admin/reportes/comisiones', [\App\Http\Controllers\Admin\ReporteController::class, 'comisiones'])->name('admin.reportes.comisiones');
        Route::post('admin/reportes/exportar-ventas', [\App\Http\Controllers\Admin\ReporteController::class, 'exportarVentas'])->name('admin.reportes.exportar-ventas');

        // Comisiones
        Route::get('admin/comisiones', [\App\Http\Controllers\Admin\ComisionController::class, 'index'])->name('admin.comisiones.index');
        Route::get('admin/comisiones/{id}', [\App\Http\Controllers\Admin\ComisionController::class, 'show'])->name('admin.comisiones.show');
        Route::post('admin/comisiones/calcular', [\App\Http\Controllers\Admin\ComisionController::class, 'calcular'])->name('admin.comisiones.calcular');
        Route::post('admin/comisiones/exportar', [\App\Http\Controllers\Admin\ComisionController::class, 'exportar'])->name('admin.comisiones.exportar');

        // Red de Referidos
        Route::get('admin/referidos', [\App\Http\Controllers\Admin\ReferidoController::class, 'index'])->name('admin.referidos.index');
        Route::get('admin/referidos/{id}', [\App\Http\Controllers\Admin\ReferidoController::class, 'show'])->name('admin.referidos.show');
        Route::get('admin/referidos/red/{id?}', [\App\Http\Controllers\Admin\ReferidoController::class, 'red'])->name('admin.referidos.red');
        Route::get('admin/referidos/estadisticas', [\App\Http\Controllers\Admin\ReferidoController::class, 'estadisticas'])->name('admin.referidos.estadisticas');

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
