<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Controllers\SettingsController;
use App\Modules\Settings\Controllers\EmailTemplateController;
use App\Modules\Settings\Controllers\RecaptchaController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',        [SettingsController::class, 'index'])->name('index');
        Route::post('/',       [SettingsController::class, 'update'])->name('update');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::get('/frontend', [\App\Modules\Settings\Controllers\FrontendContentController::class, 'edit'])->name('frontend');
        Route::post('/frontend', [\App\Modules\Settings\Controllers\FrontendContentController::class, 'update'])->name('frontend.update');
        Route::get('/seo',     [SettingsController::class, 'seo'])->name('seo');
        Route::get('/email',   [SettingsController::class, 'email'])->name('email');
        Route::get('/backup',  [SettingsController::class, 'backup'])->name('backup');
        Route::post('/email/test', [SettingsController::class, 'testEmail'])->name('email.test');

        // Backups (Ações)
        Route::post('/backup/run',      [\App\Modules\Settings\Controllers\BackupController::class, 'create'])->name('backup.run');
        Route::get('/backup/download',  [\App\Modules\Settings\Controllers\BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/delete', [\App\Modules\Settings\Controllers\BackupController::class, 'destroy'])->name('backup.delete');

        // reCAPTCHA
        Route::get('/recaptcha',  [RecaptchaController::class, 'index'])->name('recaptcha');
        Route::post('/recaptcha', [RecaptchaController::class, 'update'])->name('recaptcha.update');

        // Templates de e-mail
        Route::get('/email/templates',           [EmailTemplateController::class, 'index'])->name('email.templates');
        Route::post('/email/templates/preview',  [EmailTemplateController::class, 'preview'])->name('email.templates.preview');
        Route::get('/email/templates/{slug}',    [EmailTemplateController::class, 'edit'])->name('email.templates.edit');
        Route::post('/email/templates/{slug}',   [EmailTemplateController::class, 'update'])->name('email.templates.update');
    });
});
