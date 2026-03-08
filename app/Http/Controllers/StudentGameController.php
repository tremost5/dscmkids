<?php

namespace App\Http\Controllers;

use App\Models\ArcadeGameScore;
use App\Models\DailyQuizBank;
use App\Models\DailyQuizQuestion;
use App\Models\DailyQuizResult;
use App\Models\StudentRewardClaim;
use App\Models\User;
use App\Services\StudentProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
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
                ->where('played_on', now()->toDateString())
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

    public function progress(Request $request, StudentProgressService $studentProgressService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'student', 403);

        [$weekStart, $weekEnd] = $studentProgressService->weekDateRange();
        $weeklySummary = $studentProgressService->weeklySummary($user);

        $weeklyQuizScore = (int) $weeklySummary['weekly_total_score'];
        $weeklyQuizDays = (int) $weeklySummary['weekly_completed_days'];
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

    public function submitDailyQuiz(Request $request, StudentProgressService $studentProgressService): JsonResponse
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
        [$bestScore, $lockedUser] = DB::transaction(function () use (
            $user,
            $today,
            $quiz,
            $score,
            $correctAnswers,
            $totalQuestions,
            $submittedAnswers,
            $studentProgressService
        ) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $todayResult = DailyQuizResult::query()
                ->where('user_id', $lockedUser->id)
                ->where('quiz_date', $today)
                ->lockForUpdate()
                ->first();

            $previousScore = $todayResult ? (int) $todayResult->score : 0;
            $bestScore = max($previousScore, $score);
            $scoreDelta = max(0, $bestScore - $previousScore);

            $todayResult ??= new DailyQuizResult([
                'user_id' => $lockedUser->id,
                'quiz_date' => $today,
            ]);

            $todayResult->fill([
                'quiz_key' => $quiz['key'],
                'score' => $bestScore,
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'badge_awarded' => $studentProgressService->dailyBadgeLabel($bestScore),
                'answers' => $submittedAnswers,
            ])->save();

            if ($scoreDelta > 0) {
                $lockedUser->points += $scoreDelta;
            }

            $lockedUser->streak_days = $studentProgressService->calculateStreakDays($lockedUser->id);
            $lockedUser->last_quiz_played_on = $today;
            $lockedUser->save();

            return [$bestScore, $lockedUser->fresh()];
        });

        $weeklySummary = $studentProgressService->weeklySummary($lockedUser);

        return response()->json([
            'message' => 'Kuis tersimpan. Mantap, terus bertumbuh dalam firman.',
            'score' => $score,
            'best_score_today' => $bestScore,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'daily_badge' => $studentProgressService->dailyBadgeLabel($bestScore),
            'weekly_badge' => $weeklySummary['weekly_badge'],
            'weekly_total_score' => $weeklySummary['weekly_total_score'],
            'weekly_completed_days' => $weeklySummary['weekly_completed_days'],
            'points' => (int) $lockedUser->points,
            'streak_days' => (int) $lockedUser->streak_days,
            'leaderboard' => $studentProgressService->dailyLeaderboard(),
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
        [$bestScore, $lockedUser] = DB::transaction(function () use ($user, $validated, $today) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $row = ArcadeGameScore::query()
                ->where('user_id', $lockedUser->id)
                ->where('game_key', $validated['game_key'])
                ->where('played_on', $today)
                ->lockForUpdate()
                ->first();

            $oldScore = $row ? (int) $row->score : 0;
            $newScore = (int) $validated['score'];
            $bestScore = max($oldScore, $newScore);
            $delta = max(0, $bestScore - $oldScore);

            $row ??= new ArcadeGameScore([
                'user_id' => $lockedUser->id,
                'game_key' => $validated['game_key'],
                'played_on' => $today,
            ]);

            $row->score = $bestScore;
            $row->save();

            if ($delta > 0) {
                $lockedUser->points += (int) round($delta * 0.4);
                $lockedUser->save();
            }

            return [$bestScore, $lockedUser->fresh()];
        });

        $leaderboard = ArcadeGameScore::query()
            ->with('user:id,name')
            ->where('played_on', $today)
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
            'points' => (int) $lockedUser->points,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function claimWeeklyReward(Request $request, StudentProgressService $studentProgressService): JsonResponse
    {
        if (!$studentProgressService->hasRewardClaims() || !$studentProgressService->hasDailyQuizResults()) {
            return response()->json(['message' => 'Fitur reward belum siap. Jalankan migrate terlebih dulu.'], 422);
        }

        $user = $request->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Hanya murid yang bisa klaim reward.'], 403);
        }

        [$weekStart] = $studentProgressService->weekDateRange();
        $weeklySummary = $studentProgressService->weeklySummary($user);
        $weeklyTotalScore = (int) $weeklySummary['weekly_total_score'];

        if ($weeklyTotalScore < StudentProgressService::WEEKLY_REWARD_THRESHOLD) {
            return response()->json([
                'message' => 'Skor mingguan belum cukup untuk klaim. Minimal '.StudentProgressService::WEEKLY_REWARD_THRESHOLD.' poin.',
                'weekly_total_score' => $weeklyTotalScore,
            ], 422);
        }

        [$rewardPoints, $badgeLabel, $lockedUser] = DB::transaction(function () use ($user, $weekStart, $weeklyTotalScore, $studentProgressService) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $existingClaim = StudentRewardClaim::query()
                ->where('user_id', $lockedUser->id)
                ->where('week_start_date', $weekStart)
                ->lockForUpdate()
                ->first();

            if ($existingClaim) {
                throw new HttpResponseException(
                    response()->json(['message' => 'Reward minggu ini sudah pernah diklaim.'], 422)
                );
            }

            $rewardPoints = $studentProgressService->rewardPointsByWeeklyScore($weeklyTotalScore);
            $badgeLabel = $studentProgressService->weeklyBadgeLabel($weeklyTotalScore);

            StudentRewardClaim::create([
                'user_id' => $lockedUser->id,
                'week_start_date' => $weekStart,
                'reward_points' => $rewardPoints,
                'reward_label' => $badgeLabel,
            ]);

            $lockedUser->points += $rewardPoints;
            $lockedUser->last_weekly_claimed_on = $weekStart;
            $lockedUser->save();

            return [$rewardPoints, $badgeLabel, $lockedUser->fresh()];
        });

        return response()->json([
            'message' => 'Reward mingguan berhasil diklaim.',
            'reward_points' => $rewardPoints,
            'badge' => $badgeLabel,
            'points' => (int) $lockedUser->points,
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
}
