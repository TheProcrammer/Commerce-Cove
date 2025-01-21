<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ProductController::class,'home'])->name('dashboard'); //
Route::get('/product/{slug}', [ProductController::class,'show']) //
    ->name('product.show');

Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class,'store'])
        ->name('cart.store');
    Route::put('/cart/{product}', [CartController::class,'update'])
        ->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class,'destroy'])
        ->name('cart.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
