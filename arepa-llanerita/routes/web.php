<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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
        // Aquí irán las rutas específicas de admin
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
