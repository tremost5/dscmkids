<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\HeroSlide;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'newsCount' => News::count(),
            'announcementCount' => Announcement::count(),
            'sectionCount' => PageSection::count(),
            'mediaCount' => Media::count(),
            'slideCount' => HeroSlide::count(),
            'teacherCount' => TeacherProfile::count(),
            'recentNews' => News::latest()->take(5)->get(),
        ]);
    }
}
