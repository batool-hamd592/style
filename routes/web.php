<?php
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
Route::get('/', [StorefrontController::class, 'home'])
    ->name('store.home');

Route::get('/shop', [StorefrontController::class, 'index'])
    ->name('store.products.index');

Route::get('/shop/{product}', [StorefrontController::class, 'show'])
    ->name('store.products.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->except('show');
            Route::resource('products', ProductController::class)
    ->except('show');
    });
Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart', [CartController::class, 'store'])
    ->name('cart.store');

Route::patch('/cart/{variant}', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('/cart/{variant}', [CartController::class, 'destroy'])
    ->name('cart.destroy');

Route::delete('/cart', [CartController::class, 'clear'])
    ->name('cart.clear');    
require __DIR__.'/auth.php';
