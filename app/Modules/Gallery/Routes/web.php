<?php

use App\Modules\Gallery\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

// Rotas administrativas da galeria
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('gallery')->name('gallery.')->group(function () {
        // Categorias
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::post('/categories', [GalleryController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [GalleryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [GalleryController::class, 'destroyCategory'])->name('categories.destroy');
        Route::post('/categories/reorder', [GalleryController::class, 'reorderCategories'])->name('categories.reorder');
        
        // Fotos
        Route::get('/photos/{category?}', [GalleryController::class, 'photos'])->name('photos');
        Route::post('/photos', [GalleryController::class, 'storePhoto'])->name('photos.store');
        Route::put('/photos/{photo}', [GalleryController::class, 'updatePhoto'])->name('photos.update');
        Route::delete('/photos/{photo}', [GalleryController::class, 'destroyPhoto'])->name('photos.destroy');
        Route::post('/photos/reorder', [GalleryController::class, 'reorderPhotos'])->name('photos.reorder');
        Route::patch('/photos/{photo}/toggle-active', [GalleryController::class, 'togglePhotoActive'])->name('photos.toggle-active');
    });
});