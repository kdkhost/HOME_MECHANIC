<?php

use App\Modules\Services\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Rotas administrativas dos serviços
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('services')->name('services.')->group(function () {
        // CRUD básico
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        
        // Ações AJAX
        Route::post('/reorder', [ServiceController::class, 'reorder'])->name('reorder');
        Route::patch('/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])->name('toggle-featured');
    });
});