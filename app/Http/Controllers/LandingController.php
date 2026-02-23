<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Services\SchoolDataService;

class LandingController extends Controller
{
    public function index(SchoolDataService $schoolDataService)
    {
        $sections = PageSection::query()->get()->keyBy('section_key');

        $news = News::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest('id')
            ->take(3)
            ->get();

        $announcements = Announcement::query()
            ->where('is_active', true)
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $schoolData = $schoolDataService->buildDashboardData();
        $gallery = !empty($schoolData['gallery'])
            ? $schoolData['gallery']
            : Media::query()->latest()->take(8)->get();

        return view('landing', compact('sections', 'news', 'announcements', 'gallery', 'schoolData'));
    }
}
