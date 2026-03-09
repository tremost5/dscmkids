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
        $query = trim((string) request()->query('q', ''));
        $status = trim((string) request()->query('status', ''));

        $news = News::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', '%'.$query.'%')
                        ->orWhere('slug', 'like', '%'.$query.'%')
                        ->orWhere('excerpt', 'like', '%'.$query.'%');
                });
            })
            ->when($status !== '', fn ($builder) => $builder->where('is_published', $status === 'published'))
            ->latest('published_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.news.index', compact('news', 'query', 'status'));
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

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'news_ids' => ['required', 'array', 'min:1'],
            'news_ids.*' => ['integer', 'exists:news,id'],
            'action' => ['required', Rule::in(['publish', 'unpublish', 'delete'])],
        ]);

        $query = News::query()->whereIn('id', $validated['news_ids']);

        if ($validated['action'] === 'publish') {
            $query->update([
                'is_published' => true,
                'published_at' => now(),
            ]);
        } elseif ($validated['action'] === 'unpublish') {
            $query->update(['is_published' => false]);
        } else {
            $query->get()->each(function (News $item) {
                if ($item->cover_image) {
                    Storage::disk('public')->delete($item->cover_image);
                }

                $item->delete();
            });
        }

        return redirect()->route('admin.news.index')->with('success', 'Bulk action berita berhasil diproses.');
    }

    public function export()
    {
        $query = trim((string) request()->query('q', ''));
        $status = trim((string) request()->query('status', ''));

        $news = News::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', '%'.$query.'%')
                        ->orWhere('slug', 'like', '%'.$query.'%')
                        ->orWhere('excerpt', 'like', '%'.$query.'%');
                });
            })
            ->when($status !== '', fn ($builder) => $builder->where('is_published', $status === 'published'))
            ->orderBy('title');

        return response()->streamDownload(function () use ($news) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Slug', 'Status', 'Published At']);

            $news->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->title,
                        $item->slug,
                        $item->is_published ? 'Published' : 'Draft',
                        optional($item->published_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, 'news-export-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
