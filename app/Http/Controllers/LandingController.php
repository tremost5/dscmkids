<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\HeroSlide;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;
use App\Services\SchoolDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        $galleryItems = $this->collectGalleryItems($schoolData);

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
        $query = trim((string) request()->query('q', ''));

        $latestNews = News::query()
            ->where('is_published', true)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', '%'.$query.'%')
                        ->orWhere('excerpt', 'like', '%'.$query.'%')
                        ->orWhere('body', 'like', '%'.$query.'%');
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('news.index', compact('latestNews', 'query'));
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

    public function galleryEventShow(SchoolDataService $schoolDataService, string $eventSlug)
    {
        $schoolData = $schoolDataService->buildDashboardData();
        $galleryItems = $this->collectGalleryItems($schoolData);

        $eventsBySlug = $galleryItems
            ->pluck('event_name')
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($eventName) => [Str::slug((string) $eventName) => $eventName]);

        abort_if(!$eventsBySlug->has($eventSlug), 404);

        $eventName = (string) $eventsBySlug->get($eventSlug);
        $eventItems = $galleryItems->where('event_name', $eventName)->values();
        $timestamps = $eventItems
            ->pluck('date')
            ->filter()
            ->map(fn ($date) => strtotime((string) $date))
            ->filter(fn ($timestamp) => $timestamp !== false)
            ->values();

        $eventStats = [
            'photo_count' => $eventItems->count(),
            'latest_date' => $timestamps->isNotEmpty() ? date('d M Y', (int) $timestamps->max()) : null,
            'first_date' => $timestamps->isNotEmpty() ? date('d M Y', (int) $timestamps->min()) : null,
            'event_count' => $eventsBySlug->count(),
        ];

        return view('gallery.event', [
            'eventName' => $eventName,
            'eventSlug' => $eventSlug,
            'eventItems' => $eventItems,
            'allEvents' => $eventsBySlug,
            'eventStats' => $eventStats,
        ]);
    }

    private function collectGalleryItems(array $schoolData): Collection
    {
        $gallery = !empty($schoolData['gallery'])
            ? $schoolData['gallery']
            : Media::query()->latest()->take(8)->get();

        return collect(is_iterable($gallery) ? $gallery : [])
            ->map(function ($photo) {
                if (is_array($photo)) {
                    $title = $photo['title'] ?? 'Kegiatan DSCMKids';
                    $event = $photo['event_name'] ?? $this->eventNameFromTitle($title);
                    $slug = Str::slug((string) $event);

                    return array_merge($photo, [
                        'title' => $title,
                        'event_name' => $event,
                        'event_slug' => $slug,
                    ]);
                }

                $title = $photo->title ?? 'Kegiatan DSCMKids';
                $event = $this->eventNameFromTitle($title);

                return [
                    'title' => $title,
                    'path' => asset('storage/'.$photo->file_path),
                    'date' => optional($photo->created_at)->format('d M Y'),
                    'event_name' => $event,
                    'event_slug' => Str::slug($event),
                    'external' => false,
                ];
            });
    }

    private function eventNameFromTitle(string $title): string
    {
        $parts = preg_split('/[-:|]/', $title);
        $candidate = trim((string) ($parts[0] ?? ''));

        return $candidate !== '' ? $candidate : 'Kegiatan Umum';
    }
}
