<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;

class SpiritualContentController extends Controller
{
    public function edit()
    {
        $monthlyTheme = PageSection::firstOrCreate(
            ['section_key' => 'monthly_theme'],
            [
                'title' => 'Tema Bulanan DSCMKids',
                'content' => 'Bulan ini kita belajar bertumbuh dalam kasih dan ketaatan kepada Tuhan melalui tindakan sederhana setiap hari.',
                'meta' => [
                    'subtitle' => 'Fokus Pertumbuhan Iman',
                    'verse' => 'Kolose 2:7',
                    'highlight' => 'Akar iman yang kuat melahirkan hidup yang berdampak.',
                ],
            ]
        );

        $dailyDevotions = PageSection::firstOrCreate(
            ['section_key' => 'daily_devotions'],
            [
                'title' => 'Renungan Harian Murid',
                'meta' => [
                    'days' => config('kids_program.daily_devotions', []),
                ],
            ]
        );

        return view('admin.spiritual.edit', [
            'monthlyTheme' => $monthlyTheme,
            'dailyDevotions' => $dailyDevotions,
            'dayLabels' => $this->dayLabels(),
            'devotionRows' => $this->buildDevotionRows((array) ($dailyDevotions->meta['days'] ?? [])),
        ]);
    }

    public function update(Request $request)
    {
        $dayKeys = array_keys($this->dayLabels());

        $rules = [
            'monthly_title' => ['required', 'string', 'max:255'],
            'monthly_subtitle' => ['nullable', 'string', 'max:255'],
            'monthly_verse' => ['nullable', 'string', 'max:255'],
            'monthly_content' => ['nullable', 'string'],
            'monthly_highlight' => ['nullable', 'string', 'max:255'],
            'devotion_section_title' => ['required', 'string', 'max:255'],
        ];

        foreach ($dayKeys as $dayKey) {
            $rules['devotions.'.$dayKey.'.title'] = ['required', 'string', 'max:255'];
            $rules['devotions.'.$dayKey.'.verse'] = ['required', 'string', 'max:255'];
            $rules['devotions.'.$dayKey.'.message'] = ['required', 'string'];
            $rules['devotions.'.$dayKey.'.challenge'] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules);

        $monthlyTheme = PageSection::firstOrCreate(['section_key' => 'monthly_theme']);
        $monthlyTheme->update([
            'title' => $data['monthly_title'],
            'content' => $data['monthly_content'] ?? null,
            'meta' => [
                'subtitle' => $data['monthly_subtitle'] ?? '',
                'verse' => $data['monthly_verse'] ?? '',
                'highlight' => $data['monthly_highlight'] ?? '',
            ],
        ]);

        $cleanDevotions = [];
        foreach ($dayKeys as $dayKey) {
            $row = $data['devotions'][$dayKey] ?? [];
            $cleanDevotions[$dayKey] = [
                'title' => trim((string) ($row['title'] ?? '')),
                'verse' => trim((string) ($row['verse'] ?? '')),
                'message' => trim((string) ($row['message'] ?? '')),
                'challenge' => trim((string) ($row['challenge'] ?? '')),
            ];
        }

        $dailyDevotions = PageSection::firstOrCreate(['section_key' => 'daily_devotions']);
        $dailyDevotions->update([
            'title' => $data['devotion_section_title'],
            'content' => null,
            'meta' => [
                'days' => $cleanDevotions,
            ],
        ]);

        return redirect()->route('admin.spiritual.edit')->with('success', 'Tema bulanan dan renungan harian berhasil diperbarui.');
    }

    private function dayLabels(): array
    {
        return [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
    }

    private function buildDevotionRows(array $dbDevotions): array
    {
        $labels = $this->dayLabels();
        $defaults = (array) config('kids_program.daily_devotions', []);
        $rows = [];

        foreach (array_keys($labels) as $dayKey) {
            $source = (array) ($dbDevotions[$dayKey] ?? $defaults[$dayKey] ?? []);
            $rows[$dayKey] = [
                'title' => (string) ($source['title'] ?? ''),
                'verse' => (string) ($source['verse'] ?? ''),
                'message' => (string) ($source['message'] ?? ''),
                'challenge' => (string) ($source['challenge'] ?? ''),
            ];
        }

        return $rows;
    }
}

