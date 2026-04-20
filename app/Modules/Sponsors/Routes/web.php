<?php

use App\Modules\Sponsors\Controllers\SponsorController;
use Illuminate\Support\Facades\Route;

// Rotas administrativas dos patrocinadores
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('sponsors')->name('sponsors.')->group(function () {
        // CRUD basico
        Route::get('/', [SponsorController::class, 'index'])->name('index');
        Route::post('/', [SponsorController::class, 'store'])->name('store');
        Route::get('/{sponsor}', [SponsorController::class, 'show'])->name('show');
        Route::put('/{sponsor}', [SponsorController::class, 'update'])->name('update');
        Route::delete('/{sponsor}', [SponsorController::class, 'destroy'])->name('destroy');

        // Acoes AJAX
        Route::post('/reorder', [SponsorController::class, 'reorder'])->name('reorder');
        Route::patch('/{sponsor}/toggle-active', [SponsorController::class, 'toggleActive'])->name('toggle-active');
    });
});
