<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\HistorialMedicoController;

// --- RUTAS PÚBLICAS ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS POR TOKEN (Sanctum) ---
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Mascotas
    Route::apiResource('mascotas', MascotaController::class);

    // Citas
    Route::apiResource('citas', CitaController::class);

    // Historial Médico
    Route::apiResource('historiales', HistorialMedicoController::class);
});