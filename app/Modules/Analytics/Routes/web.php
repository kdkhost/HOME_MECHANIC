<?php

use App\Modules\Analytics\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Heartbeat publico (frontend) - tracking de duracao
Route::post('/analytics/heartbeat', [AnalyticsController::class, 'heartbeat'])->name('analytics.heartbeat');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/visitors', [AnalyticsController::class, 'getVisitors'])->name('analytics.visitors');
    Route::get('/analytics/pages', [AnalyticsController::class, 'getPages'])->name('analytics.pages');
    Route::get('/analytics/chart', [AnalyticsController::class, 'chartData'])->name('analytics.chart');
    Route::get('/analytics/online', [AnalyticsController::class, 'onlineVisitors'])->name('analytics.online');
    Route::get('/analytics/report', [AnalyticsController::class, 'report'])->name('analytics.report');
    Route::post('/analytics/cleanup', [AnalyticsController::class, 'cleanup'])->name('analytics.cleanup');
});
