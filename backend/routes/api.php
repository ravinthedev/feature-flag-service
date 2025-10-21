<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeatureFlagController;
use App\Http\Controllers\Api\CarReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::prefix('feature-flags')->group(function () {
    Route::post('/{key}/evaluate', [FeatureFlagController::class, 'evaluate']);
    Route::post('/{key}/check', [FeatureFlagController::class, 'check']);
});

Route::prefix('admin/feature-flags')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', [FeatureFlagController::class, 'index']);
    Route::post('/', [FeatureFlagController::class, 'store']);
    Route::get('/{key}', [FeatureFlagController::class, 'show']);
    Route::put('/{key}', [FeatureFlagController::class, 'update']);
    Route::delete('/{key}', [FeatureFlagController::class, 'destroy']);
    Route::get('/{key}/analytics', [FeatureFlagController::class, 'analytics']);
    Route::get('/analytics/user-history', [FeatureFlagController::class, 'userHistory']);
});

Route::prefix('car-reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CarReportController::class, 'index']);
    Route::post('/', [CarReportController::class, 'store']);
    Route::get('/{id}', [CarReportController::class, 'show']);
    Route::put('/{id}', [CarReportController::class, 'update']);
});

Route::prefix('admin/car-reports')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', [CarReportController::class, 'index']);
    Route::get('/{id}', [CarReportController::class, 'show']);
    Route::put('/{id}', [CarReportController::class, 'update']);
    Route::delete('/{id}', [CarReportController::class, 'destroy']);
    Route::get('/status/{status}', [CarReportController::class, 'byStatus']);
});
