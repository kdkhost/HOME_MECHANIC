<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Testimonials\Controllers\TestimonialController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('testimonials')->name('testimonials.')->group(function () {
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::post('/', [TestimonialController::class, 'store'])->name('store');
        Route::put('/{testimonial}', [TestimonialController::class, 'update'])->name('update');
        Route::delete('/{testimonial}', [TestimonialController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [TestimonialController::class, 'reorder'])->name('reorder');
        Route::patch('/{testimonial}/toggle', [TestimonialController::class, 'toggleActive'])->name('toggle');
    });
});
