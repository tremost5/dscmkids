<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    public function edit()
    {
        $section = PageSection::firstOrCreate(
            ['section_key' => 'parent_portal'],
            [
                'title' => 'Portal Orang Tua DSCMKids',
                'content' => 'Ringkasan mingguan untuk orang tua: fokus kelas, ayat hafalan, dan progress anak.',
                'meta' => [
                    'enabled' => false,
                    'cta_url' => '',
                    'highlights' => [
                        'Ringkasan tema mingguan per kelas',
                        'Rangkuman aktivitas quiz dan challenge anak',
                        'Saran pendampingan rohani di rumah',
                    ],
                ],
            ]
        );

        return view('admin.parent-portal.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'highlight_1' => ['nullable', 'string', 'max:255'],
            'highlight_2' => ['nullable', 'string', 'max:255'],
            'highlight_3' => ['nullable', 'string', 'max:255'],
            'enabled' => ['nullable', 'boolean'],
        ]);

        $section = PageSection::firstOrCreate(['section_key' => 'parent_portal']);
        $section->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'meta' => [
                'enabled' => $request->boolean('enabled'),
                'cta_url' => $data['cta_url'] ?? '',
                'highlights' => array_values(array_filter([
                    $data['highlight_1'] ?? '',
                    $data['highlight_2'] ?? '',
                    $data['highlight_3'] ?? '',
                ])),
            ],
        ]);

        return redirect()->route('admin.parent-portal.edit')->with('success', 'Pengaturan Parent Portal berhasil diperbarui.');
    }
}

