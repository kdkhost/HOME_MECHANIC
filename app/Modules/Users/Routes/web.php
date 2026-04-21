<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Users\Controllers\UsersController;

// Rota de verificação por link assinado (acessível sem login)
Route::get('/admin/email/verify/{id}/{hash}', [UsersController::class, 'verify'])
    ->middleware(['web', 'signed'])
    ->name('admin.verification.verify');

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('/{id}', [UsersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy');

        // Verificação de e-mail
        Route::post('/{id}/send-verification', [UsersController::class, 'sendVerification'])->name('send-verification');
        Route::post('/{id}/verify-manual', [UsersController::class, 'verifyManual'])->name('verify-manual');

        // Impersonacao (acessar conta de outro usuario)
        Route::post('/{id}/impersonate', [UsersController::class, 'impersonate'])->name('impersonate');
    });

    // Parar impersonacao (volta para conta original)
    Route::get('/stop-impersonating', [UsersController::class, 'stopImpersonating'])->name('stop-impersonating');
});