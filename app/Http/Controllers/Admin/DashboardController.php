<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlatformMetricsService;

class DashboardController extends Controller
{
    public function index(PlatformMetricsService $platformMetricsService)
    {
        $dashboard = $platformMetricsService->dashboard();
        $system = $platformMetricsService->systemHealth();

        return view('admin.dashboard', [
            'newsCount' => $dashboard['counts']['news'],
            'announcementCount' => $dashboard['counts']['announcements'],
            'sectionCount' => $dashboard['counts']['sections'],
            'mediaCount' => $dashboard['counts']['media'],
            'slideCount' => $dashboard['counts']['slides'],
            'teacherCount' => $dashboard['counts']['teachers'],
            'materialCount' => $dashboard['counts']['materials'],
            'studentCount' => $dashboard['counts']['students'],
            'activeAdminCount' => $dashboard['counts']['active_admins'],
            'inactiveUserCount' => $dashboard['counts']['inactive_users'],
            'retentionRate' => $dashboard['engagement']['retention_rate'],
            'conversionRate' => $dashboard['engagement']['conversion_rate'],
            'inactiveStudents7d' => $dashboard['engagement']['inactive_students_7d'],
            'hourlyQuiz' => $dashboard['hourly_quiz'],
            'topClasses' => $dashboard['top_classes'],
            'recentAdminActivities' => $dashboard['recent_admin_activities'],
            'arcadeTodayCount' => $dashboard['engagement']['arcade_today_count'],
            'recentNews' => $dashboard['recent_news'],
            'systemHealth' => $system,
        ]);
    }
}
