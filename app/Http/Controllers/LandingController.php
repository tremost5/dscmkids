<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DailyQuizBank;
use App\Models\DailyQuizQuestion;
use App\Models\DailyQuizResult;
use App\Models\HeroSlide;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\StudentRewardClaim;
use App\Models\Testimonial;
use App\Services\SchoolDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest('id')
            ->take(6)
            ->get();

        $schoolData = $schoolDataService->buildDashboardData();
        $galleryItems = $this->collectGalleryItems($schoolData);
        $weeklyGalleryItems = $this->collectWeeklyGalleryItems($schoolData);
        $monthlyTheme = $this->buildMonthlyTheme($sections->get('monthly_theme'));
        $dailyDevotion = $this->buildDailyDevotion($sections->get('daily_devotions'));

        $activeEvent = request()->query('event');
        $galleryEvents = $galleryItems
            ->pluck('event_name')
            ->filter()
            ->unique()
            ->values();

        if ($activeEvent) {
            $galleryItems = $galleryItems->where('event_name', $activeEvent)->values();
        }

        $liveSection = $sections->get('livestream');
        $liveTitle = $liveSection?->title ?: 'Live Streaming Ibadah Anak';
        $liveDescription = $liveSection?->content ?: 'Saksikan ibadah dan kegiatan DSCMKids secara langsung melalui YouTube.';
        $youtubeRawUrl = $liveSection?->meta['youtube_url']
            ?? $liveSection?->meta['url']
            ?? $liveSection?->content;
        $youtubeEmbedUrl = $this->youtubeEmbedUrl(is_string($youtubeRawUrl) ? $youtubeRawUrl : null);

        $liveStream = [
            'title' => $liveTitle,
            'description' => $liveDescription,
            'youtube_url' => is_string($youtubeRawUrl) ? $youtubeRawUrl : null,
            'embed_url' => $youtubeEmbedUrl,
            'is_live' => (bool) ($liveSection?->meta['is_live'] ?? false),
        ];

        $todayQuiz = $this->buildTodayQuizPayload();
        $dailyLeaderboard = $this->dailyLeaderboard();
        $weeklyLeaderboard = $this->weeklyLeaderboard();
        $miniGames = config('kids_program.mini_games', []);

        $studentProgress = null;
        $dailyResetNotice = false;
        if (auth()->check() && auth()->user()?->role === 'student') {
            $studentProgress = $this->studentProgress(auth()->id());
            $dailyResetNotice = $this->shouldShowDailyResetNotice(auth()->user());
        }

        return view('landing', [
            'sections' => $sections,
            'news' => $news,
            'announcements' => $announcements,
            'gallery' => $galleryItems,
            'galleryEvents' => $galleryEvents,
            'activeEvent' => $activeEvent,
            'schoolData' => $schoolData,
            'weeklyGallery' => $weeklyGalleryItems,
            'monthlyTheme' => $monthlyTheme,
            'dailyDevotion' => $dailyDevotion,
            'slides' => $slides,
            'teachers' => $teachers,
            'testimonials' => $testimonials,
            'liveStream' => $liveStream,
            'todayQuiz' => $todayQuiz,
            'dailyLeaderboard' => $dailyLeaderboard,
            'weeklyLeaderboard' => $weeklyLeaderboard,
            'studentProgress' => $studentProgress,
            'miniGames' => $miniGames,
            'dailyResetNotice' => $dailyResetNotice,
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

    private function collectWeeklyGalleryItems(array $schoolData): Collection
    {
        $weeklyGallery = !empty($schoolData['weekly_gallery'])
            ? $schoolData['weekly_gallery']
            : [];

        return collect(is_iterable($weeklyGallery) ? $weeklyGallery : [])
            ->map(function ($photo) {
                if (!is_array($photo)) {
                    return null;
                }

                $title = (string) ($photo['title'] ?? 'Selfie Kehadiran');
                $event = (string) ($photo['event_name'] ?? 'Selfie Absensi Minggu Ini');

                return array_merge($photo, [
                    'title' => $title,
                    'event_name' => $event,
                    'event_slug' => Str::slug($event),
                ]);
            })
            ->filter()
            ->values();
    }

    private function buildMonthlyTheme(?PageSection $section): array
    {
        $fallback = [
            'title' => 'Tema Bulanan DSCMKids',
            'subtitle' => 'Fokus Pertumbuhan Iman',
            'verse' => 'Kolose 2:7',
            'description' => 'Bulan ini kita belajar bertumbuh dalam kasih dan ketaatan kepada Tuhan melalui tindakan sederhana setiap hari.',
            'highlight' => 'Akar iman yang kuat melahirkan hidup yang berdampak.',
        ];

        return [
            'title' => (string) ($section?->title ?: $fallback['title']),
            'subtitle' => (string) (($section?->meta['subtitle'] ?? '') ?: $fallback['subtitle']),
            'verse' => (string) (($section?->meta['verse'] ?? '') ?: $fallback['verse']),
            'description' => (string) ($section?->content ?: $fallback['description']),
            'highlight' => (string) (($section?->meta['highlight'] ?? '') ?: $fallback['highlight']),
        ];
    }

    private function buildDailyDevotion(?PageSection $section = null): array
    {
        $dayKey = strtolower(now()->englishDayOfWeek);
        $devotions = (array) ($section?->meta['days'] ?? config('kids_program.daily_devotions', []));
        $fallback = [
            'title' => 'Tuhan Menyertai Setiap Hari',
            'verse' => 'Yosua 1:9',
            'message' => 'Tuhan tidak pernah meninggalkanmu. Tetap berani, setia berdoa, dan lakukan yang benar hari ini.',
            'challenge' => 'Berdoa 2 menit untuk satu temanmu hari ini.',
        ];

        $item = $devotions[$dayKey] ?? $devotions[array_key_first($devotions)] ?? $fallback;

        return [
            'section_title' => (string) ($section?->title ?: 'Renungan Harian Murid'),
            'day' => now()->locale('id')->translatedFormat('l'),
            'title' => (string) ($item['title'] ?? $fallback['title']),
            'verse' => (string) ($item['verse'] ?? $fallback['verse']),
            'message' => (string) ($item['message'] ?? $fallback['message']),
            'challenge' => (string) ($item['challenge'] ?? $fallback['challenge']),
        ];
    }

    private function youtubeEmbedUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $trimmed = trim($url);

        if ($trimmed === '') {
            return null;
        }

        if (preg_match('~youtube\.com/embed/([A-Za-z0-9_-]{6,})~i', $trimmed, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?autoplay=0&rel=0';
        }

        $parts = parse_url($trimmed);
        if (!$parts || empty($parts['host'])) {
            return null;
        }

        $host = strtolower((string) $parts['host']);
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $videoId = null;

        if (str_contains($host, 'youtu.be')) {
            $videoId = strtok($path, '/');
        } elseif (str_contains($host, 'youtube.com')) {
            if (str_starts_with($path, 'watch')) {
                parse_str((string) ($parts['query'] ?? ''), $query);
                $videoId = $query['v'] ?? null;
            } elseif (str_starts_with($path, 'shorts/')) {
                $videoId = explode('/', $path)[1] ?? null;
            } elseif (str_starts_with($path, 'live/')) {
                $videoId = explode('/', $path)[1] ?? null;
            } elseif (str_starts_with($path, 'embed/')) {
                $videoId = explode('/', $path)[1] ?? null;
            }
        }

        if (!$videoId || !preg_match('/^[A-Za-z0-9_-]{6,}$/', (string) $videoId)) {
            return null;
        }

        return 'https://www.youtube.com/embed/'.$videoId.'?autoplay=0&rel=0';
    }

    private function buildTodayQuizPayload(): array
    {
        if (Schema::hasTable('daily_quiz_banks')) {
            $dayKey = strtolower(now()->englishDayOfWeek);
            $bank = DailyQuizBank::query()
                ->where('day_key', $dayKey)
                ->where('is_active', true)
                ->with(['questions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'), 'questions.options'])
                ->first();

            if ($bank && $bank->questions->isNotEmpty()) {
                $questions = $bank->questions
                    ->map(function (DailyQuizQuestion $item) {
                        return [
                            'id' => (string) $item->id,
                            'question' => (string) $item->question_text,
                            'options' => $item->options->pluck('option_text')->values()->all(),
                        ];
                    })
                    ->filter(fn ($item) => $item['id'] !== '' && $item['question'] !== '' && !empty($item['options']))
                    ->values()
                    ->all();

                return [
                    'quiz_key' => $bank->day_key,
                    'title' => $bank->title,
                    'memory_verse' => $bank->memory_verse ?: '',
                    'questions' => $questions,
                ];
            }
        }

        $quizSets = config('kids_program.quiz_sets', []);
        $dayKey = strtolower(now()->englishDayOfWeek);
        $fallbackKey = array_key_first($quizSets);
        $activeKey = array_key_exists($dayKey, $quizSets) ? $dayKey : (string) $fallbackKey;
        $set = $quizSets[$activeKey] ?? ['questions' => []];
        $questions = collect($set['questions'] ?? [])
            ->map(function ($item) {
                return [
                    'id' => (string) ($item['id'] ?? ''),
                    'question' => (string) ($item['question'] ?? ''),
                    'options' => array_values($item['options'] ?? []),
                ];
            })
            ->filter(fn ($item) => $item['id'] !== '' && $item['question'] !== '' && !empty($item['options']))
            ->values()
            ->all();

        return [
            'quiz_key' => $activeKey,
            'title' => (string) ($set['title'] ?? 'Kuis Ayat Harian'),
            'memory_verse' => (string) ($set['memory_verse'] ?? ''),
            'questions' => $questions,
        ];
    }

    private function dailyLeaderboard(): array
    {
        if (!Schema::hasTable('daily_quiz_results')) {
            return [];
        }

        return DailyQuizResult::query()
            ->with('user:id,name,points')
            ->whereDate('quiz_date', now()->toDateString())
            ->orderByDesc('score')
            ->orderBy('updated_at')
            ->take(10)
            ->get()
            ->values()
            ->map(function (DailyQuizResult $item, int $index) {
                return [
                    'rank' => $index + 1,
                    'name' => (string) optional($item->user)->name,
                    'score' => (int) $item->score,
                    'points' => (int) optional($item->user)->points,
                ];
            })
            ->all();
    }

    private function weeklyLeaderboard(): array
    {
        if (!Schema::hasTable('daily_quiz_results')) {
            return [];
        }

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        return DailyQuizResult::query()
            ->selectRaw('user_id, SUM(score) as weekly_score')
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->groupBy('user_id')
            ->orderByDesc('weekly_score')
            ->take(10)
            ->get()
            ->map(function (DailyQuizResult $row, int $index) {
                $user = User::query()->select('name')->find($row->user_id);

                return [
                    'rank' => $index + 1,
                    'name' => (string) ($user?->name ?? 'Murid'),
                    'weekly_score' => (int) $row->weekly_score,
                ];
            })
            ->all();
    }

    private function studentProgress(int $userId): array
    {
        if (!Schema::hasTable('daily_quiz_results')) {
            return [
                'weekly_total_score' => 0,
                'weekly_completed_days' => 0,
                'weekly_completion_percent' => 0,
                'weekly_badge' => 'Faith Starter',
                'weekly_reward_claimed' => false,
                'weekly_reward_claimable' => false,
                'weekly_reward_threshold' => 240,
            ];
        }

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $weeklyTotalScore = (int) DailyQuizResult::query()
            ->where('user_id', $userId)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->sum('score');

        $weeklyCompletedDays = (int) DailyQuizResult::query()
            ->where('user_id', $userId)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->distinct('quiz_date')
            ->count('quiz_date');

        $badgeLabel = 'Faith Starter';
        foreach (config('kids_program.weekly_badges', []) as $badge) {
            $threshold = (int) ($badge['min_score'] ?? 0);
            if ($weeklyTotalScore >= $threshold) {
                $badgeLabel = (string) ($badge['label'] ?? $badgeLabel);
            }
        }

        $rewardClaimed = Schema::hasTable('student_reward_claims')
            ? StudentRewardClaim::query()->where('user_id', $userId)->whereDate('week_start_date', $weekStart)->exists()
            : false;
        $rewardThreshold = 240;

        return [
            'weekly_total_score' => $weeklyTotalScore,
            'weekly_completed_days' => $weeklyCompletedDays,
            'weekly_completion_percent' => min(100, (int) round(($weeklyCompletedDays / 7) * 100)),
            'weekly_badge' => $badgeLabel,
            'weekly_reward_claimed' => $rewardClaimed,
            'weekly_reward_claimable' => $weeklyTotalScore >= $rewardThreshold && !$rewardClaimed,
            'weekly_reward_threshold' => $rewardThreshold,
        ];
    }

    private function shouldShowDailyResetNotice(User $user): bool
    {
        if ($user->role !== 'student') {
            return false;
        }

        $lastSeen = $user->last_daily_reset_seen_on;
        $today = now()->toDateString();

        return !$lastSeen || $lastSeen->toDateString() !== $today;
    }
}
