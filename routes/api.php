<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'getMe']);

        Route::middleware(['isAdmin'])->group(function () {
            Route::get('/admin-only', function () {
                return response()->json(['message' => 'Welcome, Admin']);
            });
        });

        Route::middleware(['isUser'])->group(function () {
            Route::get('/user-only', function () {
                return response()->json(['message' => 'Welcome, User']);
            });
        });
    });
});

// user routes
Route::prefix('profile')->middleware(['auth:sanctum'])->group(function () {
    Route::put('/info', [UserController::class, 'update']);
    Route::put('/setting', [UserController::class, 'updateSetting']);
    Route::get('/info', [UserController::class, 'index']);
});

// categorys
Route::prefix('category')->group(function () {
    Route::post('/', [CategoryController::class, 'store']);
    Route::post('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::get('/', [CategoryController::class, 'index']);
});

// products
Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'find']);
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});


// carts
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{productId}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/remove/{cartId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
});
