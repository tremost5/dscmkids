<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $media = Media::latest()->paginate(12);

        return view('admin.media.index', compact('media'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.media.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        Media::create([
            'title' => $data['title'],
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()?->id,
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil diupload.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $medium)
    {
        return view('admin.media.show', ['media' => $medium]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $medium)
    {
        return view('admin.media.edit', ['media' => $medium]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $medium)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:5120'],
        ]);

        $payload = [
            'title' => $data['title'],
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($medium->file_path);

            $file = $request->file('file');
            $payload['file_path'] = $file->store('media', 'public');
            $payload['mime_type'] = $file->getMimeType();
            $payload['file_size'] = $file->getSize();
        }

        $medium->update($payload);

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $medium)
    {
        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil dihapus.');
    }
}
