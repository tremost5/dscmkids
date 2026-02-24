<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\HeroSlide;
use App\Models\LearningMaterial;
use App\Models\Media;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\DailyQuizResult;
use App\Models\ArcadeGameScore;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $studentCount = User::query()->where('role', 'student')->count();
        $activeStudents7d = Schema::hasTable('daily_quiz_results')
            ? DailyQuizResult::query()->where('quiz_date', '>=', now()->subDays(6)->toDateString())->distinct('user_id')->count('user_id')
            : 0;
        $retentionRate = $studentCount > 0 ? round(($activeStudents7d / $studentCount) * 100, 1) : 0;

        $testimonialCount = Schema::hasTable('testimonials') ? Testimonial::query()->count() : 0;
        $conversionRate = $studentCount > 0 ? round(($testimonialCount / $studentCount) * 100, 1) : 0;

        $hourlyQuiz = [];
        if (Schema::hasTable('daily_quiz_results')) {
            $hourlyQuiz = DailyQuizResult::query()
                ->selectRaw('HOUR(updated_at) as h, COUNT(*) as total')
                ->whereDate('updated_at', now()->toDateString())
                ->groupBy(DB::raw('HOUR(updated_at)'))
                ->orderBy('h')
                ->get()
                ->map(fn ($row) => ['hour' => str_pad((string) $row->h, 2, '0', STR_PAD_LEFT).':00', 'total' => (int) $row->total])
                ->all();
        }

        $topClasses = [];
        if (Schema::hasTable('daily_quiz_results')) {
            $topClasses = DailyQuizResult::query()
                ->join('users', 'users.id', '=', 'daily_quiz_results.user_id')
                ->selectRaw('COALESCE(users.class_group, "Tanpa Kelas") as class_group, SUM(daily_quiz_results.score) as total_score, COUNT(*) as attempts')
                ->whereBetween('daily_quiz_results.quiz_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->groupBy('class_group')
                ->orderByDesc('total_score')
                ->take(8)
                ->get();
        }

        $recentAdminActivities = Schema::hasTable('admin_activity_logs')
            ? AdminActivityLog::query()->latest('id')->take(8)->get()
            : collect();

        return view('admin.dashboard', [
            'newsCount' => News::count(),
            'announcementCount' => Announcement::count(),
            'sectionCount' => PageSection::count(),
            'mediaCount' => Media::count(),
            'slideCount' => HeroSlide::count(),
            'teacherCount' => TeacherProfile::count(),
            'materialCount' => Schema::hasTable('learning_materials') ? LearningMaterial::count() : 0,
            'studentCount' => $studentCount,
            'retentionRate' => $retentionRate,
            'conversionRate' => $conversionRate,
            'hourlyQuiz' => $hourlyQuiz,
            'topClasses' => $topClasses,
            'recentAdminActivities' => $recentAdminActivities,
            'arcadeTodayCount' => Schema::hasTable('arcade_game_scores')
                ? ArcadeGameScore::query()->whereDate('played_on', now()->toDateString())->count()
                : 0,
            'recentNews' => News::latest()->take(5)->get(),
        ]);
    }
}
