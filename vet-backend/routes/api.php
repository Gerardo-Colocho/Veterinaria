<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\HistorialMedicoController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Autenticación)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestión de Usuarios y Roles
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Rutas de Citas, Mascotas e Historiales
    Route::get('/citas/disponibilidad', [CitaController::class, 'consultarDisponibilidad']);
    
    // Ruta específica para consultar los historiales de una mascota concreta desde el móvil
    Route::get('/mascotas/{id}/historial', function ($id) {
        $historiales = \App\Models\HistorialMedico::where('mascota_id', $id)->get();
        return response()->json($historiales, 200);
    });

    Route::apiResource('citas', CitaController::class);
    Route::apiResource('mascotas', MascotaController::class);
    Route::apiResource('historiales', HistorialMedicoController::class);
});