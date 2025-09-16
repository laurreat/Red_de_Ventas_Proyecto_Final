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
        Route::get('admin/productos', function () {
            return view('admin.productos.index');
        })->name('admin.productos.index');

        // Pedidos
        Route::get('admin/pedidos', function () {
            return view('admin.pedidos.index');
        })->name('admin.pedidos.index');

        // Reportes
        Route::get('admin/reportes/ventas', function () {
            return view('admin.reportes.ventas');
        })->name('admin.reportes.ventas');

        // Comisiones
        Route::get('admin/comisiones', function () {
            return view('admin.comisiones.index');
        })->name('admin.comisiones.index');

        // Red de Referidos
        Route::get('admin/referidos', function () {
            return view('admin.referidos');
        })->name('admin.referidos.index');

        // Configuración
        Route::get('admin/configuracion', function () {
            return view('admin.configuracion.index');
        })->name('admin.configuracion.index');
    });
    
    // Rutas para Líderes y Administradores
    Route::middleware(['role:lider,administrador'])->group(function () {
        // Aquí irán las rutas de reportes y gestión de equipos
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
