<?php

use App\Modules\Installer\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::prefix('install')->name('installer.')->group(function () {
    Route::get('/', [InstallerController::class, 'index'])->name('index');
    Route::get('/configuracao', [InstallerController::class, 'create'])->name('create');
    Route::get('/debug', [InstallerController::class, 'createDebug'])->name('create-debug');
    Route::post('/instalar', [InstallerController::class, 'store'])->name('store');
    Route::post('/testar-banco', [InstallerController::class, 'testDatabase'])->name('test-database');
});