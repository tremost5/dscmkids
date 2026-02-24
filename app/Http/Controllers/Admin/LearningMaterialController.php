<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LearningMaterialController extends Controller
{
    public function index()
    {
        $materials = LearningMaterial::query()
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15);

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        LearningMaterial::create($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.materials.index')->with('success', 'Materi edukatif berhasil ditambahkan.');
    }

    public function edit(LearningMaterial $material)
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, LearningMaterial $material)
    {
        $data = $this->validated($request);
        $material->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.materials.index')->with('success', 'Materi edukatif berhasil diperbarui.');
    }

    public function destroy(LearningMaterial $material)
    {
        $material->delete();

        return redirect()->route('admin.materials.index')->with('success', 'Materi edukatif berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'class_group' => ['nullable', 'string', 'max:100'],
            'level' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'bible_reference' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}

