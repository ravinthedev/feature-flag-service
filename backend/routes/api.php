<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeatureFlagController;
use App\Http\Controllers\Api\CarReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Public feature flag endpoints (for clients)
Route::prefix('feature-flags')->group(function () {
    Route::post('/{key}/evaluate', [FeatureFlagController::class, 'evaluate']);
    Route::post('/{key}/check', [FeatureFlagController::class, 'check']);
    Route::get('/active', [FeatureFlagController::class, 'getActiveFlags']);
});

// Admin-only feature flag endpoints
Route::prefix('admin/feature-flags')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', [FeatureFlagController::class, 'index']);
    Route::post('/', [FeatureFlagController::class, 'store']);
    Route::get('/{key}', [FeatureFlagController::class, 'show']);
    Route::put('/{key}', [FeatureFlagController::class, 'update']);
    Route::delete('/{key}', [FeatureFlagController::class, 'destroy']);
    Route::get('/{key}/analytics', [FeatureFlagController::class, 'analytics']);
    Route::get('/analytics/user-history', [FeatureFlagController::class, 'userHistory']);
});

// Admin-only car report endpoints
Route::prefix('admin/car-reports')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', [CarReportController::class, 'index']);
    Route::post('/', [CarReportController::class, 'store']);
    Route::get('/{id}', [CarReportController::class, 'show']);
    Route::put('/{id}', [CarReportController::class, 'update']);
    Route::delete('/{id}', [CarReportController::class, 'destroy']);
    Route::get('/status/{status}', [CarReportController::class, 'byStatus']);
});
