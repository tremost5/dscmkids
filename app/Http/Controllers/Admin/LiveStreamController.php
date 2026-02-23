<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function edit()
    {
        $section = PageSection::firstOrCreate(
            ['section_key' => 'livestream'],
            [
                'title' => 'Live Streaming Ibadah Anak',
                'content' => 'Saksikan ibadah dan kegiatan DSCMKids secara langsung melalui YouTube.',
                'meta' => ['youtube_url' => ''],
            ]
        );

        return view('admin.livestream.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'is_live' => ['nullable', 'boolean'],
        ]);

        $section = PageSection::firstOrCreate(['section_key' => 'livestream']);

        $section->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'meta' => [
                'youtube_url' => $data['youtube_url'] ?? '',
                'is_live' => $request->boolean('is_live'),
            ],
        ]);

        return redirect()->route('admin.livestream.edit')->with('success', 'Pengaturan live streaming berhasil disimpan.');
    }
}
