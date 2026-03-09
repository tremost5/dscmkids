<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlatformMetricsService;

class SystemMonitorController extends Controller
{
    public function index(PlatformMetricsService $platformMetricsService)
    {
        return view('admin.system.index', [
            'systemHealth' => $platformMetricsService->systemHealth(),
            'digest' => $platformMetricsService->adminDigest(),
        ]);
    }
}
