<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Controllers\SettingsController;
use App\Modules\Settings\Controllers\EmailTemplateController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',        [SettingsController::class, 'index'])->name('index');
        Route::post('/',       [SettingsController::class, 'update'])->name('update');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::get('/seo',     [SettingsController::class, 'seo'])->name('seo');
        Route::get('/email',   [SettingsController::class, 'email'])->name('email');
        Route::get('/backup',  [SettingsController::class, 'backup'])->name('backup');
        Route::post('/email/test', [SettingsController::class, 'testEmail'])->name('email.test');

        // Templates de e-mail
        Route::get('/email/templates',           [EmailTemplateController::class, 'index'])->name('email.templates');
        Route::post('/email/templates/preview',  [EmailTemplateController::class, 'preview'])->name('email.templates.preview');
        Route::get('/email/templates/{slug}',    [EmailTemplateController::class, 'edit'])->name('email.templates.edit');
        Route::post('/email/templates/{slug}',   [EmailTemplateController::class, 'update'])->name('email.templates.update');
    });
});
