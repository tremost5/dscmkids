<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlatformMetricsService;

class AdminMetricsController extends Controller
{
    public function dashboard(PlatformMetricsService $platformMetricsService)
    {
        return response()->json([
            'dashboard' => $platformMetricsService->dashboard(),
            'system' => $platformMetricsService->systemHealth(),
        ]);
    }
}
