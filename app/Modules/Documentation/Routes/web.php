<?php

use App\Modules\Documentation\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/documentacao')->name('admin.documentation.')->group(function () {
    Route::get('/', [DocumentationController::class, 'index'])->name('index');
    Route::get('/buscar', [DocumentationController::class, 'search'])->name('search');
    Route::get('/{document}', [DocumentationController::class, 'show'])->name('show');
});