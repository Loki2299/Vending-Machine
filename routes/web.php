<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductsController;
use App\Support\AttributeRouteRegistrar;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductsController::class, 'show'])->name('products.show');

Route::middleware('auth')->group(function (): void {
    Route::get('/products/{product}/buy', [ProductsController::class, 'purchaseForm'])->name('products.purchase');
    AttributeRouteRegistrar::register(ProductsController::class);
});

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/admin/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/admin/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/admin/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('/admin/products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/admin/products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
});
