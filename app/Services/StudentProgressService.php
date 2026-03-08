<?php

namespace App\Services;

use App\Models\DailyQuizResult;
use App\Models\StudentRewardClaim;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Schema;

class StudentProgressService
{
    public const WEEKLY_REWARD_THRESHOLD = 240;

    public function weeklySummary(User|int $user): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (!$this->hasDailyQuizResults()) {
            return $this->emptyWeeklySummary();
        }

        [$weekStart, $weekEnd] = $this->weekDateRange();

        $weeklyTotalScore = (int) DailyQuizResult::query()
            ->where('user_id', $userId)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->sum('score');

        $weeklyCompletedDays = (int) DailyQuizResult::query()
            ->where('user_id', $userId)
            ->whereBetween('quiz_date', [$weekStart, $weekEnd])
            ->distinct('quiz_date')
            ->count('quiz_date');

        $rewardClaimed = $this->hasRewardClaims()
            ? StudentRewardClaim::query()
                ->where('user_id', $userId)
                ->where('week_start_date', $weekStart)
                ->exists()
            : false;

        return [
            'weekly_total_score' => $weeklyTotalScore,
            'weekly_completed_days' => $weeklyCompletedDays,
            'weekly_completion_percent' => min(100, (int) round(($weeklyCompletedDays / 7) * 100)),
            'weekly_badge' => $this->weeklyBadgeLabel($weeklyTotalScore),
            'weekly_reward_claimed' => $rewardClaimed,
            'weekly_reward_claimable' => $weeklyTotalScore >= self::WEEKLY_REWARD_THRESHOLD && !$rewardClaimed,
            'weekly_reward_threshold' => self::WEEKLY_REWARD_THRESHOLD,
        ];
    }

    public function dailyLeaderboard(): array
    {
        if (!$this->hasDailyQuizResults()) {
            return [];
        }

        return DailyQuizResult::query()
            ->join('users', 'users.id', '=', 'daily_quiz_results.user_id')
            ->where('quiz_date', now()->toDateString())
            ->orderByDesc('score')
            ->orderBy('daily_quiz_results.updated_at')
            ->take(10)
            ->get([
                'daily_quiz_results.score',
                'users.name',
                'users.points',
            ])
            ->values()
            ->map(function ($item, int $index) {
                return [
                    'rank' => $index + 1,
                    'name' => (string) $item->name,
                    'score' => (int) $item->score,
                    'points' => (int) $item->points,
                ];
            })
            ->all();
    }

    public function weeklyLeaderboard(): array
    {
        if (!$this->hasDailyQuizResults()) {
            return [];
        }

        [$weekStart, $weekEnd] = $this->weekDateRange();

        return DailyQuizResult::query()
            ->join('users', 'users.id', '=', 'daily_quiz_results.user_id')
            ->selectRaw('daily_quiz_results.user_id, users.name, SUM(daily_quiz_results.score) as weekly_score')
            ->whereBetween('daily_quiz_results.quiz_date', [$weekStart, $weekEnd])
            ->groupBy('daily_quiz_results.user_id', 'users.name')
            ->orderByDesc('weekly_score')
            ->take(10)
            ->get()
            ->values()
            ->map(function ($item, int $index) {
                return [
                    'rank' => $index + 1,
                    'name' => (string) $item->name,
                    'weekly_score' => (int) $item->weekly_score,
                ];
            })
            ->all();
    }

    public function calculateStreakDays(int $userId): int
    {
        if (!$this->hasDailyQuizResults()) {
            return 0;
        }

        $playedDates = DailyQuizResult::query()
            ->where('user_id', $userId)
            ->where('quiz_date', '<=', now()->toDateString())
            ->orderByDesc('quiz_date')
            ->pluck('quiz_date')
            ->map(fn ($value) => CarbonImmutable::parse($value)->toDateString())
            ->flip();

        $days = 0;
        $cursor = CarbonImmutable::today();

        while ($playedDates->has($cursor->toDateString())) {
            $days++;
            $cursor = $cursor->subDay();
        }

        return $days;
    }

    public function dailyBadgeLabel(int $score): string
    {
        if ($score >= 90) {
            return 'Daily Gold';
        }

        if ($score >= 70) {
            return 'Daily Silver';
        }

        return 'Daily Bronze';
    }

    public function weeklyBadgeLabel(int $weeklyTotalScore): string
    {
        $active = 'Faith Starter';

        foreach (config('kids_program.weekly_badges', []) as $badge) {
            $threshold = (int) ($badge['min_score'] ?? 0);

            if ($weeklyTotalScore >= $threshold) {
                $active = (string) ($badge['label'] ?? $active);
            }
        }

        return $active;
    }

    public function rewardPointsByWeeklyScore(int $weeklyTotalScore): int
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

    public function hasDailyQuizResults(): bool
    {
        return Schema::hasTable('daily_quiz_results');
    }

    public function hasRewardClaims(): bool
    {
        return Schema::hasTable('student_reward_claims');
    }

    public function weekDateRange(): array
    {
        return [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString(),
        ];
    }

    private function emptyWeeklySummary(): array
    {
        return [
            'weekly_total_score' => 0,
            'weekly_completed_days' => 0,
            'weekly_completion_percent' => 0,
            'weekly_badge' => 'Faith Starter',
            'weekly_reward_claimed' => false,
            'weekly_reward_claimable' => false,
            'weekly_reward_threshold' => self::WEEKLY_REWARD_THRESHOLD,
        ];
    }
}
