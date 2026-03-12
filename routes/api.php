<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;

// ── PUBLIC ROUTES ─────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/pin-login', [AuthController::class, 'pinLogin']);
});

// ── PROTECTED ROUTES ──────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    Route::get('/sales/today', [SaleController::class, 'today']);
    Route::apiResource('sales', SaleController::class)
        ->only(['index', 'store', 'show']);

    Route::middleware('role:owner|manager')->group(function () {

        Route::prefix('dashboard')->group(function () {
            Route::get('/summary', [DashboardController::class, 'summary']);
            Route::get('/chart', [DashboardController::class, 'chart']);
            Route::get('/top-products', [DashboardController::class, 'topProducts']);
            Route::get('/stock-alerts', [DashboardController::class, 'stockAlerts']);
        });

        Route::get('/reports/daily', [ReportController::class, 'daily']);
    });
});