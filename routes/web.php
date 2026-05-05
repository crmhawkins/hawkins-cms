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

// E-commerce shop routes
Route::prefix('tienda')->name('shop.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Shop\CartController::class, 'show'])->name('cart');
    Route::post('/carrito', [\App\Http\Controllers\Shop\CartController::class, 'add'])->name('cart.add');
    Route::delete('/carrito', [\App\Http\Controllers\Shop\CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/carrito', [\App\Http\Controllers\Shop\CartController::class, 'update'])->name('cart.update');
    Route::get('/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'submit'])->name('checkout.submit');
    Route::get('/success/{order}', [\App\Http\Controllers\Shop\CheckoutController::class, 'success'])->name('success');
    Route::get('/cancelar', [\App\Http\Controllers\Shop\CheckoutController::class, 'cancel'])->name('cancel');
});

// Stripe webhook (CSRF-exempt)
Route::post('/webhooks/stripe', [\App\Http\Controllers\Shop\WebhookController::class, 'stripe'])
    ->name('webhooks.stripe')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// CSP report endpoint
Route::post('csp-report', function () {
    \Log::channel('csp')->warning('CSP violation', request()->json()->all() ?? []);
    return response()->noContent();
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Public CMS page routes (must be last to avoid conflicts)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')
    ->where('slug', '[a-z0-9\-_]+');
