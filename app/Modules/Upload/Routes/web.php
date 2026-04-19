<?php

use App\Modules\Upload\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/upload')->name('admin.upload.')->middleware(['auth'])->group(function () {
    // Upload de arquivos
    Route::post('/', [UploadController::class, 'store'])->name('store');
    Route::post('/multiple', [UploadController::class, 'storeMultiple'])->name('store-multiple');
    
    // Gerenciamento de uploads
    Route::get('/', [UploadController::class, 'index'])->name('index');
    Route::get('/config', [UploadController::class, 'getConfig'])->name('config');
    Route::get('/statistics', [UploadController::class, 'statistics'])->name('statistics');
    Route::get('/load', [UploadController::class, 'load'])->name('load');
    
    // Operações em uploads específicos
    Route::get('/{uuid}', [UploadController::class, 'show'])->name('show');
    Route::delete('/{uuid}', [UploadController::class, 'destroy'])->name('destroy');
    Route::post('/{uuid}/attach', [UploadController::class, 'attach'])->name('attach');
});