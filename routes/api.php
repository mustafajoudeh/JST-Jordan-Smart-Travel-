<?php

use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminDashboardExportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\PlatformSettingController;

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('/user', fn(\Illuminate\Http\Request $request) => new UserResource($request->user()));

    Route::middleware('role:system_admin')
        ->get('/admin/dashboard', [AdminDashboardController::class, 'index']);

    Route::middleware('role:system_admin')
        ->get('/admin/dashboard/export', [AdminDashboardExportController::class, 'export']);

    Route::prefix('admin')->group(function () {
        Route::middleware('role:system_admin,content_admin')->group(function () {
            Route::apiResource('governorates', GovernorateController::class);
            Route::apiResource('categories', CategoryController::class);
        });

        Route::middleware('role:system_admin')
            ->apiResource('platform-settings', PlatformSettingController::class)
            ->parameters(['platform-settings' => 'setting']);
    });
});

require __DIR__ . '/auth.php';
