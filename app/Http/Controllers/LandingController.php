<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;

class LandingController extends Controller
{
    public function index()
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
            ->take(4)
            ->get();

        $gallery = Media::query()->latest()->take(6)->get();

        return view('landing', compact('sections', 'news', 'announcements', 'gallery'));
    }
}
