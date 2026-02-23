<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\HeroSlide;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;
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
            ->take(4)
            ->get();

        $announcements = Announcement::query()
            ->where('is_active', true)
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $slides = HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $teachers = TeacherProfile::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $schoolData = $schoolDataService->buildDashboardData();
        $gallery = !empty($schoolData['gallery'])
            ? $schoolData['gallery']
            : Media::query()->latest()->take(8)->get();

        return view('landing', compact('sections', 'news', 'announcements', 'gallery', 'schoolData', 'slides', 'teachers'));
    }
}
