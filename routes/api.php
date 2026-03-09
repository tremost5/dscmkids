<?php

use App\Http\Controllers\Api\AdminMetricsController;
use App\Http\Controllers\Api\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/dashboard', [PublicContentController::class, 'dashboard']);
    Route::get('/news', [PublicContentController::class, 'news']);
    Route::get('/announcements', [PublicContentController::class, 'announcements']);
    Route::get('/materials', [PublicContentController::class, 'materials']);

    Route::middleware(['web', 'auth', 'admin', 'permission:api.admin'])->group(function () {
        Route::get('/admin/metrics', [AdminMetricsController::class, 'dashboard']);
    });
});
