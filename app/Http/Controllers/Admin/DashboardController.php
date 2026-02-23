<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'newsCount' => News::count(),
            'announcementCount' => Announcement::count(),
            'sectionCount' => PageSection::count(),
            'mediaCount' => Media::count(),
            'recentNews' => News::latest()->take(5)->get(),
        ]);
    }
}
