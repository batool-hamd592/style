<?php
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\WishlistController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Store\OrderController as CustomerOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
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
    Route::get('/orders', [AdminOrderController::class, 'index'])
    ->name('orders.index');

Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
    ->name('orders.show');

Route::patch(
    '/orders/{order}/status',
    [AdminOrderController::class, 'updateStatus']
)->name('orders.status');
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
    Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get(
        '/checkout/success/{order}',
        [CheckoutController::class, 'success']
    )->name('checkout.success');
    Route::get('/orders', [CustomerOrderController::class, 'index'])
    ->name('orders.index');

Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])
    ->name('orders.show');
});
Route::get('/wishlist', [WishlistController::class, 'index'])
    ->name('wishlist.index');

Route::post(
    '/wishlist/{product}/toggle',
    [WishlistController::class, 'toggle']
)->name('wishlist.toggle');

Route::delete(
    '/wishlist/{product}',
    [WishlistController::class, 'destroy']
)->name('wishlist.destroy');

Route::delete('/wishlist', [WishlistController::class, 'clear'])
    ->name('wishlist.clear');
require __DIR__.'/auth.php';
