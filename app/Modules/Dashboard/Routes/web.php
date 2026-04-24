<?php

use App\Modules\Dashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Rotas AJAX para dados do dashboard
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'getRecentActivityAjax'])->name('dashboard.recent-activity');
    Route::get('/dashboard/quick-stats', [DashboardController::class, 'getQuickStats'])->name('dashboard.quick-stats');
    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::post('/system/clear-all-cache', [DashboardController::class, 'clearAllCache'])->name('system.clear-cache');
    Route::post('/system/migrate', [DashboardController::class, 'runMigrations'])->name('system.migrate');

    // Notificações
    Route::get('/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/clear-all', [\App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
});