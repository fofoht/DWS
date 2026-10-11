<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    
    }

    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

Route::resource('categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('proveedores', ProveedorController::class);

// Acciones especiales de compras
Route::post('/compras/{id}/recibir', [CompraController::class, 'recibir']) ->name('compras.recibir');
Route::post('/compras/{id}/cancelar', [CompraController::class, 'cancelar']) ->name('compras.cancelar');

Route::resource('compras', CompraController::class);

});