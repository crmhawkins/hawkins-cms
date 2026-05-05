<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Editor\FieldController;
use App\Http\Controllers\Editor\MediaController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Editor API routes — require auth + edit-content permission + no-cache
Route::prefix('edit')->name('editor.')->middleware(['web', 'auth', 'editor.cache'])->group(function () {
    Route::patch('api/field', [FieldController::class, 'update'])->name('field.update');
    Route::post('api/image', [MediaController::class, 'upload'])->name('image.upload');
    Route::delete('api/image/{id}', [MediaController::class, 'destroy'])->name('image.destroy');
});

// Contact form submission
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// CSP report endpoint
Route::post('csp-report', function () {
    \Log::channel('csp')->warning('CSP violation', request()->json()->all() ?? []);
    return response()->noContent();
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Public CMS page routes (must be last to avoid conflicts)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')
    ->where('slug', '[a-z0-9\-_]+');
