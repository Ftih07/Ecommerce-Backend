<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductImageController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('users', UserController::class);
Route::apiResource('stores', StoreController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('product-images', ProductImageController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('carts', CartController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('orders', OrderController::class);

// Custom routes for user carts and orders
Route::get('users/{user_id}/carts', [CartController::class, 'getUserCarts']);
Route::get('users/{user_id}/orders', [OrderController::class, 'getUserOrders']);

// Custom route for category products
Route::get('categories/{id}/products', [CategoryController::class, 'products']);
