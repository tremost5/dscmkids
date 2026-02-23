<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::latest()->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        if ($news->cover_image) {
            Storage::disk('public')->delete($news->cover_image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}
