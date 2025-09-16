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

        // Reportes del Líder
        Route::get('lider/reportes/ventas', [\App\Http\Controllers\Lider\ReporteController::class, 'ventas'])->name('lider.reportes.ventas');
        Route::get('lider/reportes/equipo', [\App\Http\Controllers\Lider\ReporteController::class, 'equipo'])->name('lider.reportes.equipo');
        Route::post('lider/reportes/exportar', [\App\Http\Controllers\Lider\ReporteController::class, 'exportar'])->name('lider.reportes.exportar');

        // Perfil y Configuración del Líder
        Route::get('lider/perfil', [\App\Http\Controllers\Lider\PerfilController::class, 'index'])->name('lider.perfil.index');
        Route::put('lider/perfil', [\App\Http\Controllers\Lider\PerfilController::class, 'update'])->name('lider.perfil.update');
        Route::get('lider/configuracion', [\App\Http\Controllers\Lider\ConfiguracionController::class, 'index'])->name('lider.configuracion.index');
        Route::put('lider/configuracion', [\App\Http\Controllers\Lider\ConfiguracionController::class, 'update'])->name('lider.configuracion.update');
    });
    
    // Rutas para Vendedores, Líderes y Administradores
    Route::middleware(['role:vendedor,lider,administrador'])->group(function () {
        // Aquí irán las rutas de inventario y pedidos
    });
});

// Redireccionar /home al dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
});
