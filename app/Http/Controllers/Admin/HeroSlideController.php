<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('sort_order')->latest('id')->paginate(12);

        return view('admin.slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.slides.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:5120'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('slides', 'public');

        HeroSlide::create([
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'image_path' => $path,
            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.slides.index')->with('success', 'Slide berhasil ditambahkan.');
    }

    public function show(HeroSlide $slide)
    {
        return view('admin.slides.show', compact('slide'));
    }

    public function edit(HeroSlide $slide)
    {
        return view('admin.slides.edit', compact('slide'));
    }

    public function update(Request $request, HeroSlide $slide)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slide->image_path);
            $payload['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($payload);

        return redirect()->route('admin.slides.index')->with('success', 'Slide berhasil diperbarui.');
    }

    public function destroy(HeroSlide $slide)
    {
        Storage::disk('public')->delete($slide->image_path);
        $slide->delete();

        return redirect()->route('admin.slides.index')->with('success', 'Slide berhasil dihapus.');
    }
}
