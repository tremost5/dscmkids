<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\Announcement;
use App\Models\ArcadeGameScore;
use App\Models\DailyQuizResult;
use App\Models\HeroSlide;
use App\Models\LearningMaterial;
use App\Models\Media;
use App\Models\News;
use App\Models\NotificationBroadcast;
use App\Models\PageSection;
use App\Models\TeacherProfile;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlatformMetricsService
{
    public function dashboard(): array
    {
        $studentCount = User::query()->where('role', 'student')->count();
        $activeStudents7d = Schema::hasTable('daily_quiz_results')
            ? DailyQuizResult::query()
                ->where('quiz_date', '>=', now()->subDays(6)->toDateString())
                ->distinct('user_id')
                ->count('user_id')
            : 0;
        $retentionRate = $studentCount > 0 ? round(($activeStudents7d / $studentCount) * 100, 1) : 0;
        $testimonialCount = Schema::hasTable('testimonials') ? Testimonial::query()->count() : 0;
        $conversionRate = $studentCount > 0 ? round(($testimonialCount / $studentCount) * 100, 1) : 0;

        $hourlyQuiz = [];
        $topClasses = collect();
        if (Schema::hasTable('daily_quiz_results')) {
            $hourlyQuiz = DailyQuizResult::query()
                ->selectRaw('HOUR(updated_at) as h, COUNT(*) as total')
                ->whereDate('updated_at', now()->toDateString())
                ->groupBy(DB::raw('HOUR(updated_at)'))
                ->orderBy('h')
                ->get()
                ->map(fn ($row) => [
                    'hour' => str_pad((string) $row->h, 2, '0', STR_PAD_LEFT).':00',
                    'total' => (int) $row->total,
                ])
                ->all();

            $topClasses = DailyQuizResult::query()
                ->join('users', 'users.id', '=', 'daily_quiz_results.user_id')
                ->selectRaw('COALESCE(users.class_group, "Tanpa Kelas") as class_group, SUM(daily_quiz_results.score) as total_score, COUNT(*) as attempts')
                ->whereBetween('daily_quiz_results.quiz_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->groupBy('class_group')
                ->orderByDesc('total_score')
                ->take(8)
                ->get();
        }

        return [
            'counts' => [
                'news' => News::count(),
                'announcements' => Announcement::count(),
                'sections' => PageSection::count(),
                'media' => Media::count(),
                'slides' => HeroSlide::count(),
                'teachers' => TeacherProfile::count(),
                'materials' => Schema::hasTable('learning_materials') ? LearningMaterial::count() : 0,
                'students' => $studentCount,
                'active_admins' => User::query()->whereIn('role', ['super_admin', 'admin', 'editor'])->where('is_active', true)->count(),
                'inactive_users' => User::query()->where('is_active', false)->count(),
            ],
            'engagement' => [
                'retention_rate' => $retentionRate,
                'conversion_rate' => $conversionRate,
                'arcade_today_count' => Schema::hasTable('arcade_game_scores')
                    ? ArcadeGameScore::query()->whereDate('played_on', now()->toDateString())->count()
                    : 0,
                'inactive_students_7d' => User::query()
                    ->where('role', 'student')
                    ->where(function ($query) {
                        $query->whereNull('last_quiz_played_on')
                            ->orWhere('last_quiz_played_on', '<', now()->subDays(7)->toDateString());
                    })
                    ->count(),
            ],
            'hourly_quiz' => $hourlyQuiz,
            'top_classes' => $topClasses,
            'recent_admin_activities' => Schema::hasTable('admin_activity_logs')
                ? AdminActivityLog::query()->latest('id')->take(8)->get()
                : collect(),
            'recent_news' => News::latest()->take(5)->get(),
        ];
    }

    public function systemHealth(): array
    {
        $pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $broadcastStatuses = Schema::hasTable('notification_broadcasts')
            ? NotificationBroadcast::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all()
            : [];
        $onlineUsers = Schema::hasTable('sessions')
            ? DB::table('sessions')
                ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
                ->count()
            : 0;

        return [
            'queues' => [
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'job_batches' => Schema::hasTable('job_batches') ? DB::table('job_batches')->count() : 0,
            ],
            'broadcasts' => [
                'statuses' => $broadcastStatuses,
                'pending' => (int) ($broadcastStatuses['pending'] ?? 0),
                'processing' => (int) ($broadcastStatuses['processing'] ?? 0),
                'failed' => (int) ($broadcastStatuses['failed'] ?? 0),
            ],
            'users' => [
                'online_users_15m' => $onlineUsers,
                'active_admins' => User::query()->whereIn('role', ['super_admin', 'admin', 'editor'])->where('is_active', true)->count(),
                'students' => User::query()->where('role', 'student')->count(),
            ],
            'integrations' => [
                'whatsapp_webhook_configured' => filled(config('services.whatsapp.broadcast_webhook')),
                'school_data_cache_ttl' => (int) config('school_data.cache_ttl_seconds', 0),
            ],
        ];
    }

    public function adminDigest(): array
    {
        $dashboard = $this->dashboard();
        $system = $this->systemHealth();

        return [
            'generated_at' => now()->toDateTimeString(),
            'retention_rate' => $dashboard['engagement']['retention_rate'],
            'inactive_students_7d' => $dashboard['engagement']['inactive_students_7d'],
            'pending_jobs' => $system['queues']['pending_jobs'],
            'failed_jobs' => $system['queues']['failed_jobs'],
            'pending_broadcasts' => $system['broadcasts']['pending'],
            'processing_broadcasts' => $system['broadcasts']['processing'],
            'failed_broadcasts' => $system['broadcasts']['failed'],
        ];
    }
}
