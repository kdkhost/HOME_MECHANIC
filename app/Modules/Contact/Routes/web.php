<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Contact\Controllers\ContactController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reply',    [ContactController::class, 'reply'])->name('reply');
        Route::patch('/{id}/read',    [ContactController::class, 'markRead'])->name('read');
    });
});