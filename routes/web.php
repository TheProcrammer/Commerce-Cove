<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Enums\RolesEnum;


Route::get('/', [ProductController::class,'home'])
    ->name('dashboard'); //
Route::get('/product/{slug}', [ProductController::class,'show']) //
    ->name('product.show');

Route::get('/d/{department:slug}', [ProductController::class, 'byDepartment'])
    ->name('product.byDepartment');

Route::get('/s/{vendor:store_name}', [VendorController::class,'profile'])
    ->name('vendor.profile');

Route::controller(CartController::class)
    ->group(function () {
    Route::get('/cart', 'index')
        ->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class,'store'])
        ->name('cart.store');
    Route::put('/cart/{product}', [CartController::class,'update'])
        ->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class,'destroy'])
        ->name('cart.destroy');
});
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])
    ->name('stripe.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::middleware(['verified'])
            ->group(function () {
        Route::post('/cart/checkout', [CartController::class, 'checkout'])
            ->name('cart.checkout');
        Route::get('/stripe/success', [StripeController::class, 'success'])
            ->name('stripe.success');
            Route::get('/stripe/cancel', [StripeController::class, 'cancel'])
            ->name('stripe.cancel');
        Route::get('/stripe/failure', [StripeController::class, 'failure'])
            ->name('stripe.failure');

        Route::post('/become-a-vendor', [VendorController::class, 'store'])
            ->name('vendor.store');

        Route::post('/stripe/connect', [StripeController::class, 'connect'])
            ->name('stripe.connect')
            ->middleware(['role' . RolesEnum::Vendor->value]);
    });
});
require __DIR__.'/auth.php';
