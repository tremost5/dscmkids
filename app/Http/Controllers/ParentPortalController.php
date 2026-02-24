<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ParentPortalController extends Controller
{
    public function index()
    {
        $section = PageSection::query()->where('section_key', 'parent_portal')->first();
        $enabled = (bool) ($section?->meta['enabled'] ?? false);

        abort_unless($enabled, 404);

        $highlights = collect($section?->meta['highlights'] ?? [])
            ->filter(fn ($v) => is_string($v) && trim($v) !== '')
            ->values();

        $announcements = Announcement::query()
            ->where('is_active', true)
            ->orderBy('event_date')
            ->take(5)
            ->get();

        $studentCount = User::query()->where('role', 'student')->count();
        $activeToday = Schema::hasTable('daily_quiz_results')
            ? \App\Models\DailyQuizResult::query()->whereDate('quiz_date', now()->toDateString())->distinct('user_id')->count('user_id')
            : 0;

        return view('parent-portal.index', [
            'section' => $section,
            'highlights' => $highlights,
            'announcements' => $announcements,
            'studentCount' => $studentCount,
            'activeToday' => $activeToday,
        ]);
    }
}

