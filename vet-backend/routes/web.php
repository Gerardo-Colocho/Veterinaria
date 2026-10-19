<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminWebController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [AdminWebController::class, 'index']);
Route::post('/admin/citas/{id}/status', [AdminWebController::class, 'updateCitaStatus'])->name('admin.citas.status');
Route::post('/admin/historiales', [AdminWebController::class, 'storeHistorial'])->name('admin.historiales.store');