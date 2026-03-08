<?php

namespace App\Http\Controllers;

use App\Services\StudentProgressService;
use Illuminate\Http\Request;

class StudentWalletController extends Controller
{
    public function index(Request $request, StudentProgressService $studentProgressService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'student', 403);

        $summary = $studentProgressService->weeklySummary($user);
        $weeklyScore = (int) $summary['weekly_total_score'];

        $badges = collect(config('kids_program.weekly_badges', []))
            ->map(function ($badge) use ($weeklyScore) {
                $min = (int) ($badge['min_score'] ?? 0);
                return [
                    'label' => (string) ($badge['label'] ?? 'Badge'),
                    'min_score' => $min,
                    'unlocked' => $weeklyScore >= $min,
                ];
            });

        $claims = $studentProgressService->hasRewardClaims()
            ? $user->rewardClaims()->latest('week_start_date')->take(8)->get()
            : collect();

        return view('student.wallet', [
            'user' => $user,
            'weeklyScore' => $weeklyScore,
            'badges' => $badges,
            'claims' => $claims,
        ]);
    }
}
