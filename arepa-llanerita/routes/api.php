<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin API Routes
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
    Route::get('/users', [AdminApiController::class, 'users']);
    Route::get('/products', [AdminApiController::class, 'products']);
    Route::get('/orders', [AdminApiController::class, 'orders']);
    Route::get('/commissions', [AdminApiController::class, 'commissions']);
    Route::get('/referrals', [AdminApiController::class, 'referrals']);
    Route::get('/reports', [AdminApiController::class, 'reports']);
    Route::get('/config', [AdminApiController::class, 'config']);
    Route::post('/config', [AdminApiController::class, 'updateConfig']);
    Route::get('/backups', [AdminApiController::class, 'backups']);
    Route::post('/backups', [AdminApiController::class, 'createBackup']);
    Route::get('/logs', [AdminApiController::class, 'logs']);
    Route::delete('/logs', [AdminApiController::class, 'clearLogs']);
    Route::get('/profile', [AdminApiController::class, 'profile']);
    Route::post('/profile', [AdminApiController::class, 'updateProfile']);
    Route::post('/profile/password', [AdminApiController::class, 'updatePassword']);
    Route::post('/cache/clear', [AdminApiController::class, 'clearCache']);
});