<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherProfileController extends Controller
{
    public function index()
    {
        $teachers = TeacherProfile::orderBy('sort_order')->latest('id')->paginate(12);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'class_group' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'class_group' => $data['class_group'] ?? null,
            'bio' => $data['bio'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('photo')) {
            $payload['photo_path'] = $request->file('photo')->store('teachers', 'public');
        }

        TeacherProfile::create($payload);

        return redirect()->route('admin.teachers.index')->with('success', 'Profil guru berhasil ditambahkan.');
    }

    public function show(TeacherProfile $teacher)
    {
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(TeacherProfile $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, TeacherProfile $teacher)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'class_group' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'class_group' => $data['class_group'] ?? null,
            'bio' => $data['bio'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('photo')) {
            if ($teacher->photo_path) {
                Storage::disk('public')->delete($teacher->photo_path);
            }
            $payload['photo_path'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($payload);

        return redirect()->route('admin.teachers.index')->with('success', 'Profil guru berhasil diperbarui.');
    }

    public function destroy(TeacherProfile $teacher)
    {
        if ($teacher->photo_path) {
            Storage::disk('public')->delete($teacher->photo_path);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Profil guru berhasil dihapus.');
    }
}
