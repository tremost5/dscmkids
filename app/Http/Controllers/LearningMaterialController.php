<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LearningMaterialController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('learning_materials')) {
            return view('materials.index', [
                'materials' => collect(),
                'recommended' => collect(),
                'filters' => ['class_group' => '', 'level' => ''],
            ]);
        }

        $classGroup = trim((string) $request->query('class_group', ''));
        $level = trim((string) $request->query('level', ''));

        $query = LearningMaterial::query()->where('is_active', true);
        if ($classGroup !== '') {
            $query->where('class_group', $classGroup);
        }
        if ($level !== '') {
            $query->where('level', $level);
        }

        $materials = $query->orderBy('sort_order')->latest('id')->paginate(12)->withQueryString();
        $recommended = $this->recommendedMaterials($request->user(), $classGroup)->take(3);

        return view('materials.index', [
            'materials' => $materials,
            'recommended' => $recommended,
            'filters' => ['class_group' => $classGroup, 'level' => $level],
        ]);
    }

    private function recommendedMaterials($user, string $classGroup): Collection
    {
        $targetClass = $classGroup !== '' ? $classGroup : trim((string) ($user?->class_group ?? ''));

        return LearningMaterial::query()
            ->where('is_active', true)
            ->when($targetClass !== '', fn ($q) => $q->where('class_group', $targetClass))
            ->orderBy('sort_order')
            ->latest('id')
            ->get();
    }
}

