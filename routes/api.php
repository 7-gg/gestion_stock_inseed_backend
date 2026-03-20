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
    Route::get('users/managers', [UserController::class, 'allManager']);
    Route::get('users/movements', [UserController::class, 'allMovement']);

    // Gestion des Utilisateurs
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleManagerRole']);

    // nombre total de stock
    Route::get('stocks/count', [StockController::class, 'count']);
    // total des produits
    Route::get('products/count', [ProductController::class, 'count']);
    // les produits à réapprovisionner
    Route::get('products/to-restock', [ProductController::class, 'toRestock']);

    // Ressources simples
    Route::apiResource('products/categories', ProductCategoryController::class);
    Route::apiResource('products/units', ProductUnitController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stocks', StockController::class);

    Route::scopeBindings()->group(function () {
        Route::prefix('stocks/{stock}')->group(function () {
            Route::apiResource('users', StockUserController::class)
                ->parameters([
                    'users' => 'stockUser'
                ]);
        });
    });

    // Relations spécifiques aux Stocks
    Route::prefix('stocks/{stock}')
        ->group(function () {
            Route::apiResource('products', StockProductController::class);
            Route::apiResource('movements', StockMovementController::class);
        });
});
