<?php

use App\Modules\Seo\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

// Rotas administrativas do SEO
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('seo')->name('seo.')->group(function () {
        // CRUD básico
        Route::get('/', [SeoController::class, 'index'])->name('index');
        Route::get('/create', [SeoController::class, 'create'])->name('create');
        Route::post('/', [SeoController::class, 'store'])->name('store');
        Route::delete('/{seoSetting}', [SeoController::class, 'destroy'])->name('destroy');
        
        // Funcionalidades especiais
        Route::post('/preview', [SeoController::class, 'preview'])->name('preview');
        Route::post('/hashtags', [SeoController::class, 'generateHashtags'])->name('hashtags');
        Route::post('/analyze', [SeoController::class, 'analyze'])->name('analyze');
    });
});