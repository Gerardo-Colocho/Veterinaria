<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminWebController;

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Vista de Login Web
Route::get('/login', function () {
    return view('login');
})->name('login');

// Rutas del Panel Web
Route::get('/admin', [AdminWebController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/usuarios', [AdminWebController::class, 'usuarios'])->name('admin.usuarios');

// Acciones POST del Panel Web
Route::post('/admin/citas/{id}/status', [AdminWebController::class, 'updateCitaStatus'])->name('admin.citas.status');
Route::post('/admin/historiales', [AdminWebController::class, 'storeHistorial'])->name('admin.historiales.store');