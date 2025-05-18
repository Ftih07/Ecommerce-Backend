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
use App\Http\Controllers\API\AuthController;

// Authentication Routes - Temporarily disabled rate limiting for testing
// Route::middleware(['auth.rate_limit'])->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
// });

// Protected auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/check', [AuthController::class, 'check']);
    Route::get('auth/roles', [AuthController::class, 'roles']);
});

// Public routes - products, categories, stores can be browsed without authentication
Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('product-images', ProductImageController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Customer resources - accessible by customers and admins
    Route::middleware([App\Http\Middleware\CheckRole::class.':customer,admin'])->group(function() {
        Route::apiResource('carts', CartController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('payments', PaymentController::class);
    });

    // Admin only resources
    Route::middleware([App\Http\Middleware\CheckRole::class.':admin'])->group(function() {
        // User roles management endpoint - admin only
        Route::put('users/{id}/roles', [UserController::class, 'updateRoles']);
    });

    // Admin and seller resources
    Route::middleware([App\Http\Middleware\CheckRole::class.':admin,seller'])->group(function() {
        Route::apiResource('users', UserController::class);

        // Protected resource management (create, update, delete)
        Route::apiResource('stores', StoreController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('product-images', ProductImageController::class)->except(['index', 'show']);
        Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);

        // Protected user-specific endpoints
        Route::get('users/{user_id}/carts', [CartController::class, 'getUserCarts']);
        Route::get('users/{user_id}/orders', [OrderController::class, 'getUserOrders']);
        Route::get('users/search/name/{name}', [UserController::class, 'searchByName']);
        Route::get('users/search/email/{email}', [UserController::class, 'searchByEmail']);
        Route::get('users/{id}/with-reviews', [UserController::class, 'getUserWithReviews']);
        Route::get('users/{id}/with-carts', [UserController::class, 'getUserWithCarts']);
    });
});

// Custom route for category products
Route::get('categories/{id}/products', [CategoryController::class, 'products']);

// Custom routes for stores
Route::get('stores/{store_id}/products', [ProductController::class, 'getStoreProducts']);
Route::get('stores/with-products/{id}', [StoreController::class, 'getStoreWithProducts']);
Route::get('stores/search', [StoreController::class, 'search']);
Route::get('stores/city/{city}', [StoreController::class, 'getByCity']);

// Custom route for product images
Route::get('products/{product_id}/images', [ProductImageController::class, 'getProductImages']);

// Custom route for product reviews and user reviews
Route::get('products/{product_id}/reviews', [ReviewController::class, 'getProductReviews']);
Route::get('users/{user_id}/reviews', [ReviewController::class, 'getUserReviews']);
