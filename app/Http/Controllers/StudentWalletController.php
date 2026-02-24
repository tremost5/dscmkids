<?php

namespace App\Http\Controllers;

use App\Models\DailyQuizResult;
use App\Models\StudentRewardClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StudentWalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'student', 403);

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $weeklyScore = Schema::hasTable('daily_quiz_results')
            ? (int) DailyQuizResult::query()->where('user_id', $user->id)->whereBetween('quiz_date', [$weekStart, $weekEnd])->sum('score')
            : 0;

        $badges = collect(config('kids_program.weekly_badges', []))
            ->map(function ($badge) use ($weeklyScore) {
                $min = (int) ($badge['min_score'] ?? 0);
                return [
                    'label' => (string) ($badge['label'] ?? 'Badge'),
                    'min_score' => $min,
                    'unlocked' => $weeklyScore >= $min,
                ];
            });

        $claims = Schema::hasTable('student_reward_claims')
            ? StudentRewardClaim::query()->where('user_id', $user->id)->latest('week_start_date')->take(8)->get()
            : collect();

        return view('student.wallet', [
            'user' => $user,
            'weeklyScore' => $weeklyScore,
            'badges' => $badges,
            'claims' => $claims,
        ]);
    }
}

