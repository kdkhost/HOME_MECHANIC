<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Modules\Frontend\Controllers\FrontendController;
use App\Http\Controllers\UploadController;

// Rota raiz — verifica instalação e redireciona
Route::get('/', function () {
    $installedFile = storage_path('installed');
    if (!File::exists($installedFile)) {
        return redirect('/install');
    }
    return app(FrontendController::class)->home();
})->name('home');

// Rotas do frontend
Route::get('/servicos',      [FrontendController::class, 'services'])->name('services');
Route::get('/galeria',       [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/blog',          [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}',   [FrontendController::class, 'blogPost'])->name('blog.post');
Route::get('/contato',       [FrontendController::class, 'contact'])->name('contact');
Route::post('/contato',      [FrontendController::class, 'sendContact'])->name('contact.send');

// Upload FilePond (admin)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/upload/filepond', [UploadController::class, 'filepond'])->name('upload.filepond');
    Route::delete('/upload/filepond', [UploadController::class, 'revert'])->name('upload.filepond.revert');
});
