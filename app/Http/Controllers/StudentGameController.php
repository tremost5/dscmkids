<?php

namespace App\Http\Controllers;

use App\Models\ArcadeGameScore;
use App\Models\DailyQuizBank;
use App\Models\DailyQuizQuestion;
use App\Models\DailyQuizResult;
use App\Models\StudentRewardClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StudentGameController extends Controller
{
    public function arcade()
    {
        $arcadeLeaderboard = [];
        if (Schema::hasTable('arcade_game_scores')) {
            $arcadeLeaderboard = ArcadeGameScore::query()
                ->with('user:id,name')
                ->whereDate('played_on', now()->toDateString())
                ->orderByDesc('score')
                ->take(12)
                ->get()
                ->values()
                ->map(function (ArcadeGameScore $item, int $index) {
                    return [
                        'rank' => $index + 1,
                        'name' => (string) optional($item->user)->name,
                        'game_key' => (string) $item->game_key,
                        'score' => (int) $item->score,
                    ];
                })->all();
        }

        return view('student.arcade', [
            'arcadeLeaderboard' => $arcadeLeaderboard,
            'isStudent' => auth()->check() && auth()->user()?->role === 'student',
        ]);
    }

    public function progress(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'student', 403);

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        $weeklyQuizScore = Schema::hasTable('daily_quiz_results')
            ? (int) DailyQuizResult::query()->where('user_id', $user->id)->whereBetween('quiz_date', [$weekStart, $weekEnd])->sum('score')
            : 0;
        $weeklyQuizDays = Schema::hasTable('daily_quiz_results')
            ? (int) DailyQuizResult::query()->where('user_id', $user->id)->whereBetween('quiz_date', [$weekStart, $weekEnd])->distinct('quiz_date')->count('quiz_date')
            : 0;
        $weeklyArcadeScore = Schema::hasTable('arcade_game_scores')
            ? (int) ArcadeGameScore::query()->where('user_id', $user->id)->whereBetween('played_on', [$weekStart, $weekEnd])->sum('score')
            : 0;

        $recentQuiz = Schema::hasTable('daily_quiz_results')
            ? DailyQuizResult::query()->where('user_id', $user->id)->latest('quiz_date')->take(10)->get()
            : collect();

        return view('student.progress', [
            'user' => $user,
            'weeklyQuizScore' => $weeklyQuizScore,
            'weeklyQuizDays' => $weeklyQuizDays,
            'weeklyArcadeScore' => $weeklyArcadeScore,
            'recentQuiz' => $recentQuiz,
        ]);
    }

    public function submitDailyQuiz(Request $request): JsonResponse
    {
        if (!Schema::hasTable('daily_quiz_results')) {
            return response()->json(['message' => 'Fitur quiz belum siap. Jalankan migrate terlebih dulu.'], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Silakan login murid terlebih dulu.'], 401);
        }
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Fitur ini khusus akun murid.'], 403);
        }

        $quiz = $this->todayQuizSet();
        $validated = $request->validate([
            'quiz_key' => ['required', 'string', Rule::in([$quiz['key']])],
            'answers' => ['required', 'array'],
        ]);

        $submittedAnswers = $validated['answers'];
        $questions = $quiz['questions'];
        $totalQuestions = count($questions);
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $questionId = (string) ($question['id'] ?? '');
            $correctOption = (string) ($question['answer'] ?? '');
            $picked = (string) ($submittedAnswers[$questionId] ?? '');

            if ($questionId !== '' && $picked !== '' && $picked === $correctOption) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? (int) round(($correctAnswers / $totalQuestions) * 100) : 0;
        $today = now()->toDateString();
        $todayResult = DailyQuizResult::query()->firstOrNew([
            'user_id' => $user->id,
            'quiz_date' => $today,
        ]);

        $previousScore = $todayResult->exists ? (int) $todayResult->score : 0;
        $bestScore = max($previousScore, $score);
        $scoreDelta = max(0, $bestScore - $previousScore);

        $todayResult->fill([
            'quiz_key' => $quiz['key'],
            'score' => $bestScore,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'badge_awarded' => $this->dailyBadgeLabel($bestScore),
            'answers' => $submittedAnswers,
        ])->save();

        if ($scoreDelta > 0) {
            $user->points += $scoreDelta;
        }

        $user->streak_days = $this->calculateStreakDays($user->id);
        $user->last_quiz_played_on = $today;
        $user->save();

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $weeklyTotalScore = (int) DailyQuizResult::query()
            ->where('user_id', $user->id)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->sum('score');

        $weeklyCompletedDays = (int) DailyQuizResult::query()
            ->where('user_id', $user->id)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->distinct('quiz_date')
            ->count('quiz_date');

        return response()->json([
            'message' => 'Kuis tersimpan. Mantap, terus bertumbuh dalam firman.',
            'score' => $score,
            'best_score_today' => $bestScore,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'daily_badge' => $this->dailyBadgeLabel($bestScore),
            'weekly_badge' => $this->weeklyBadgeLabel($weeklyTotalScore),
            'weekly_total_score' => $weeklyTotalScore,
            'weekly_completed_days' => $weeklyCompletedDays,
            'points' => (int) $user->points,
            'streak_days' => (int) $user->streak_days,
            'leaderboard' => $this->dailyLeaderboard(),
        ]);
    }

    public function submitArcadeScore(Request $request): JsonResponse
    {
        if (!Schema::hasTable('arcade_game_scores')) {
            return response()->json(['message' => 'Fitur arcade belum siap. Jalankan migrate terlebih dulu.'], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Silakan login murid terlebih dulu.'], 401);
        }
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Fitur ini khusus akun murid.'], 403);
        }

        $validated = $request->validate([
            'game_key' => ['required', Rule::in(['memory_match', 'bible_guess', 'verse_builder'])],
            'score' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $today = now()->toDateString();
        $row = ArcadeGameScore::query()->firstOrNew([
            'user_id' => $user->id,
            'game_key' => $validated['game_key'],
            'played_on' => $today,
        ]);

        $oldScore = $row->exists ? (int) $row->score : 0;
        $newScore = (int) $validated['score'];
        $bestScore = max($oldScore, $newScore);
        $delta = max(0, $bestScore - $oldScore);

        $row->score = $bestScore;
        $row->save();

        if ($delta > 0) {
            $user->points += (int) round($delta * 0.4);
            $user->save();
        }

        $leaderboard = ArcadeGameScore::query()
            ->with('user:id,name')
            ->whereDate('played_on', $today)
            ->orderByDesc('score')
            ->take(12)
            ->get()
            ->values()
            ->map(function (ArcadeGameScore $item, int $index) {
                return [
                    'rank' => $index + 1,
                    'name' => (string) optional($item->user)->name,
                    'game_key' => (string) $item->game_key,
                    'score' => (int) $item->score,
                ];
            })->all();

        return response()->json([
            'message' => 'Skor arcade tersimpan.',
            'best_score' => $bestScore,
            'points' => (int) $user->points,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function claimWeeklyReward(Request $request): JsonResponse
    {
        if (!Schema::hasTable('student_reward_claims') || !Schema::hasTable('daily_quiz_results')) {
            return response()->json(['message' => 'Fitur reward belum siap. Jalankan migrate terlebih dulu.'], 422);
        }

        $user = $request->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Hanya murid yang bisa klaim reward.'], 403);
        }

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $weeklyTotalScore = (int) DailyQuizResult::query()
            ->where('user_id', $user->id)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->sum('score');

        if ($weeklyTotalScore < 240) {
            return response()->json([
                'message' => 'Skor mingguan belum cukup untuk klaim. Minimal 240 poin.',
                'weekly_total_score' => $weeklyTotalScore,
            ], 422);
        }

        $existingClaim = StudentRewardClaim::query()
            ->where('user_id', $user->id)
            ->whereDate('week_start_date', $weekStart)
            ->first();
        if ($existingClaim) {
            return response()->json(['message' => 'Reward minggu ini sudah pernah diklaim.'], 422);
        }

        $rewardPoints = $this->rewardPointsByWeeklyScore($weeklyTotalScore);
        $badgeLabel = $this->weeklyBadgeLabel($weeklyTotalScore);

        StudentRewardClaim::create([
            'user_id' => $user->id,
            'week_start_date' => $weekStart,
            'reward_points' => $rewardPoints,
            'reward_label' => $badgeLabel,
        ]);

        $user->points += $rewardPoints;
        $user->last_weekly_claimed_on = $weekStart;
        $user->save();

        return response()->json([
            'message' => 'Reward mingguan berhasil diklaim.',
            'reward_points' => $rewardPoints,
            'badge' => $badgeLabel,
            'points' => (int) $user->points,
        ]);
    }

    public function markDailyResetSeen(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Akun murid diperlukan.'], 403);
        }

        $user->last_daily_reset_seen_on = now()->toDateString();
        $user->save();

        return response()->json(['message' => 'Reset notice disembunyikan untuk hari ini.']);
    }

    private function todayQuizSet(): array
    {
        if (Schema::hasTable('daily_quiz_banks')) {
            $dayKey = strtolower(now()->englishDayOfWeek);
            $bank = DailyQuizBank::query()
                ->where('day_key', $dayKey)
                ->where('is_active', true)
                ->with(['questions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'), 'questions.options'])
                ->first();

            if ($bank && $bank->questions->isNotEmpty()) {
                return [
                    'key' => $bank->day_key,
                    'title' => $bank->title,
                    'memory_verse' => $bank->memory_verse ?: '',
                    'questions' => $bank->questions->map(function (DailyQuizQuestion $question) {
                        return [
                            'id' => (string) $question->id,
                            'question' => $question->question_text,
                            'answer' => (string) optional($question->options->firstWhere('is_correct', true))->option_text,
                            'options' => $question->options->pluck('option_text')->values()->all(),
                        ];
                    })->all(),
                ];
            }
        }

        $quizSets = config('kids_program.quiz_sets', []);
        $dayKey = strtolower(now()->englishDayOfWeek);
        $fallbackKey = array_key_first($quizSets);
        $key = array_key_exists($dayKey, $quizSets) ? $dayKey : (string) $fallbackKey;
        $set = $quizSets[$key] ?? ['questions' => []];

        return [
            'key' => $key,
            'title' => (string) ($set['title'] ?? 'Kuis Ayat Harian'),
            'memory_verse' => (string) ($set['memory_verse'] ?? ''),
            'questions' => is_array($set['questions'] ?? null) ? $set['questions'] : [],
        ];
    }

    private function calculateStreakDays(int $userId): int
    {
        $days = 0;
        $cursor = now()->startOfDay();

        while (true) {
            $exists = DailyQuizResult::query()
                ->where('user_id', $userId)
                ->whereDate('quiz_date', $cursor->toDateString())
                ->exists();

            if (!$exists) {
                break;
            }

            $days++;
            $cursor->subDay();
        }

        return $days;
    }

    private function dailyBadgeLabel(int $score): string
    {
        if ($score >= 90) {
            return 'Daily Gold';
        }
        if ($score >= 70) {
            return 'Daily Silver';
        }

        return 'Daily Bronze';
    }

    private function weeklyBadgeLabel(int $weeklyTotalScore): string
    {
        $badges = config('kids_program.weekly_badges', []);
        $active = 'Faith Starter';

        foreach ($badges as $badge) {
            $threshold = (int) ($badge['min_score'] ?? 0);
            if ($weeklyTotalScore >= $threshold) {
                $active = (string) ($badge['label'] ?? $active);
            }
        }

        return $active;
    }

    private function rewardPointsByWeeklyScore(int $weeklyTotalScore): int
    {
        if ($weeklyTotalScore >= 560) {
            return 120;
        }
        if ($weeklyTotalScore >= 420) {
            return 90;
        }
        if ($weeklyTotalScore >= 300) {
            return 70;
        }

        return 50;
    }

    private function dailyLeaderboard(): array
    {
        if (!Schema::hasTable('daily_quiz_results')) {
            return [];
        }

        $rows = DailyQuizResult::query()
            ->with('user:id,name,points')
            ->whereDate('quiz_date', now()->toDateString())
            ->orderByDesc('score')
            ->orderBy('updated_at')
            ->take(10)
            ->get();

        return $rows->values()->map(function (DailyQuizResult $item, int $index) {
            return [
                'rank' => $index + 1,
                'name' => (string) optional($item->user)->name,
                'score' => (int) $item->score,
                'points' => (int) optional($item->user)->points,
            ];
        })->all();
    }
}
