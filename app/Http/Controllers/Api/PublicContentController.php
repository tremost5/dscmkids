<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\LearningMaterial;
use App\Models\News;
use App\Services\SchoolDataService;
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function dashboard(SchoolDataService $schoolDataService)
    {
        return response()->json([
            'data' => $schoolDataService->buildDashboardData(),
        ]);
    }

    public function news(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $news = News::query()
            ->where('is_published', true)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', '%'.$query.'%')
                        ->orWhere('excerpt', 'like', '%'.$query.'%')
                        ->orWhere('body', 'like', '%'.$query.'%');
                });
            })
            ->latest('published_at')
            ->paginate(12);

        return response()->json($news);
    }

    public function announcements()
    {
        return response()->json([
            'data' => Announcement::query()
                ->where('is_active', true)
                ->orderBy('event_date')
                ->take(20)
                ->get(),
        ]);
    }

    public function materials(Request $request)
    {
        $classGroup = trim((string) $request->query('class_group', ''));
        $level = trim((string) $request->query('level', ''));

        $materials = LearningMaterial::query()
            ->where('is_active', true)
            ->when($classGroup !== '', fn ($builder) => $builder->where('class_group', $classGroup))
            ->when($level !== '', fn ($builder) => $builder->where('level', $level))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12);

        return response()->json($materials);
    }
}
