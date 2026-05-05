<?php

use App\Http\Controllers\Editor\FieldController;
use App\Http\Controllers\Editor\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Editor API routes — require auth + edit-content permission + no-cache
Route::prefix('edit')->name('editor.')->middleware(['web', 'auth', 'editor.cache'])->group(function () {
    Route::patch('api/field', [FieldController::class, 'update'])->name('field.update');
    Route::post('api/image', [MediaController::class, 'upload'])->name('image.upload');
    Route::delete('api/image/{id}', [MediaController::class, 'destroy'])->name('image.destroy');
});

// CSP report endpoint
Route::post('csp-report', function () {
    \Log::channel('csp')->warning('CSP violation', request()->json()->all() ?? []);
    return response()->noContent();
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
