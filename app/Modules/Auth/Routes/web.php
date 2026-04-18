<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Redirecionar /admin para /admin/login
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

// Rotas de autenticação (sem middleware auth)
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit')
        ->middleware('throttle:5,10'); // 5 tentativas por 10 minutos
    
    // Logout (precisa estar autenticado)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')
        ->middleware('auth');
    
    // Verificações AJAX
    Route::get('/auth/check', [AuthController::class, 'checkAuth'])->name('auth.check');
    Route::post('/auth/renew-session', [AuthController::class, 'renewSession'])->name('auth.renew-session')
        ->middleware('auth');
    Route::get('/auth/rate-limit-info', [AuthController::class, 'getRateLimitInfo'])->name('auth.rate-limit-info');
});