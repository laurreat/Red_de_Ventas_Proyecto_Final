<?php

use Illuminate\Support\Facades\Route;

// Rutas de prueba para verificar alertas
Route::middleware(['web'])->group(function () {

    Route::get('/test/success', function () {
        return redirect('/admin/productos')->with('success', 'Mensaje de éxito de prueba - ¡Las alertas funcionan!');
    });

    Route::get('/test/error', function () {
        return redirect('/admin/productos')->with('error', 'Mensaje de error de prueba - ¡Las alertas funcionan!');
    });

    Route::get('/test/warning', function () {
        return redirect('/admin/productos')->with('warning', 'Mensaje de advertencia de prueba - ¡Las alertas funcionan!');
    });

    Route::get('/test/info', function () {
        return redirect('/admin/productos')->with('info', 'Mensaje de información de prueba - ¡Las alertas funcionan!');
    });

    Route::get('/test/validation', function () {
        $validator = \Validator::make([], [
            'nombre' => 'required',
            'email' => 'required|email',
            'precio' => 'required|numeric'
        ]);

        return redirect('/admin/productos')->withErrors($validator);
    });
});