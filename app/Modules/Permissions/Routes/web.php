<?php

use App\Modules\Permissions\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    // Gerenciamento de Permissoes
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::patch('/{permission}/toggle', [PermissionController::class, 'toggle'])->name('toggle');

        // Gerenciar permissoes de usuario
        Route::get('/user/{user}', [PermissionController::class, 'userPermissions'])->name('user');
        Route::post('/user/{user}', [PermissionController::class, 'updateUserPermissions'])->name('user.update');
    });
});
