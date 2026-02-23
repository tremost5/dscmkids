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

        $galleryItems = collect(is_iterable($gallery) ? $gallery : [])
            ->map(function ($photo) {
                if (is_array($photo)) {
                    $title = $photo['title'] ?? 'Kegiatan DSCMKids';
                    $event = $photo['event_name'] ?? $this->eventNameFromTitle($title);
                    return array_merge($photo, ['title' => $title, 'event_name' => $event]);
                }

                $title = $photo->title ?? 'Kegiatan DSCMKids';
                return [
                    'title' => $title,
                    'path' => asset('storage/'.$photo->file_path),
                    'date' => optional($photo->created_at)->format('d M Y'),
                    'event_name' => $this->eventNameFromTitle($title),
                    'external' => false,
                ];
            });

        $activeEvent = request()->query('event');
        $galleryEvents = $galleryItems
            ->pluck('event_name')
            ->filter()
            ->unique()
            ->values();

        if ($activeEvent) {
            $galleryItems = $galleryItems->where('event_name', $activeEvent)->values();
        }

        return view('landing', [
            'sections' => $sections,
            'news' => $news,
            'announcements' => $announcements,
            'gallery' => $galleryItems,
            'galleryEvents' => $galleryEvents,
            'activeEvent' => $activeEvent,
            'schoolData' => $schoolData,
            'slides' => $slides,
            'teachers' => $teachers,
        ]);
    }

    public function newsIndex()
    {
        $latestNews = News::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest('id')
            ->paginate(9);

        return view('news.index', compact('latestNews'));
    }

    public function newsShow(string $slug)
    {
        $article = News::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedNews = News::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('news.show', compact('article', 'relatedNews'));
    }

    private function eventNameFromTitle(string $title): string
    {
        $parts = preg_split('/[-:|]/', $title);
        $candidate = trim((string) ($parts[0] ?? ''));

        return $candidate !== '' ? $candidate : 'Kegiatan Umum';
    }
}
