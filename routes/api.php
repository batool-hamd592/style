<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index'])
    ->name('api.products.index');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('api.products.show');