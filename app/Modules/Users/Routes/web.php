<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Users\Controllers\UsersController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('/{id}', [UsersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy');
    });
});