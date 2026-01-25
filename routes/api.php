<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductUnitController;
use App\Http\Controllers\Api\StockProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\StockUserController;

// Authentification
Route::prefix('auth')->group(function () {
    Route::post('request-code', [AuthController::class, 'requestLoginCode']);
    Route::post('verify-code', [AuthController::class, 'verifyLoginCode']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    // Gestion des Utilisateurs
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleManagerRole']);

    // Ressources simples
    Route::apiResource('products/categories', ProductCategoryController::class);
    Route::apiResource('products/units', ProductUnitController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stocks', StockController::class);

    // Relations spécifiques aux Stocks
    Route::prefix('stocks/{stock}')->group(function () {
        Route::get('users', [StockUserController::class, 'index']);
        Route::get('products', [StockProductController::class, 'index']);
        Route::get('movements', [StockMovementController::class, 'index']);
        Route::get('products/{product}/movements', [StockMovementController::class, 'productMovements']);
    });
});
