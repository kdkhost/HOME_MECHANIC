<?php

use App\Modules\Analytics\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/visitors', [AnalyticsController::class, 'getVisitors'])->name('analytics.visitors');
    Route::get('/analytics/pages', [AnalyticsController::class, 'getPages'])->name('analytics.pages');
});
