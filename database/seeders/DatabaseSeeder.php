<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\DailyQuizBank;
use App\Models\DailyQuizOption;
use App\Models\DailyQuizQuestion;
use App\Models\News;
use App\Models\PageSection;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dscmkids.org'],
            [
                'name' => 'Admin DSCMKids',
                'role' => 'admin',
                'password' => 'password123',
            ]
        );

        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'System Informasi Sekolah Minggu DSCMKids',
                'content' => 'Membantu orang tua, guru, dan pengurus melayani anak dengan informasi terpusat: jadwal, berita, kegiatan, dan materi.',
            ],
            [
                'section_key' => 'about',
                'title' => 'Tentang DSCMKids',
                'content' => 'DSCMKids adalah pelayanan sekolah minggu yang berfokus pada pertumbuhan iman anak melalui pembelajaran kreatif, komunitas hangat, dan pendampingan konsisten.',
            ],
            [
                'section_key' => 'cta',
                'title' => 'Ayo Terlibat!',
                'content' => 'Hubungi admin sekolah minggu untuk pendaftaran anak, informasi kelas, atau kolaborasi pelayanan.',
            ],
            [
                'section_key' => 'livestream',
                'title' => 'Live Streaming Ibadah Anak',
                'content' => 'Saksikan ibadah dan kegiatan DSCMKids secara langsung melalui YouTube.',
                'meta' => [
                    'youtube_url' => '',
                ],
            ],
        ];

        foreach ($sections as $section) {
            PageSection::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }

        News::updateOrCreate(
            ['slug' => 'ibadah-anak-minggu-ceria'],
            [
                'title' => 'Ibadah Anak Minggu Ceria',
                'excerpt' => 'Ibadah anak minggu ini mengangkat tema kasih dan keberanian.',
                'body' => 'Anak-anak akan belajar melalui pujian, permainan, dan renungan interaktif sesuai kelompok usia.',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        );

        Announcement::updateOrCreate(
            ['title' => 'Jadwal Pelayanan Guru Minggu Ini'],
            [
                'body' => 'Mohon semua guru hadir 30 menit sebelum ibadah dimulai untuk briefing singkat.',
                'event_date' => now()->addDays(3)->toDateString(),
                'location' => 'Gedung Utama',
                'is_active' => true,
            ]
        );

        TeacherProfile::updateOrCreate(
            ['name' => 'Kak Rina'],
            [
                'role' => 'Koordinator Sekolah Minggu',
                'class_group' => 'PG - TKA',
                'bio' => 'Melayani anak-anak dengan pendekatan kreatif dan penuh kasih.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        TeacherProfile::updateOrCreate(
            ['name' => 'Kak Daniel'],
            [
                'role' => 'Pengajar',
                'class_group' => 'TKB - 2',
                'bio' => 'Fokus pada pengembangan karakter dan disiplin rohani anak.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        TeacherProfile::updateOrCreate(
            ['name' => 'Kak Lydia'],
            [
                'role' => 'Pengajar',
                'class_group' => '3 - 6',
                'bio' => 'Mendorong anak mengenal firman Tuhan lewat dialog interaktif.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $this->seedDailyQuizBanks();

    }

    private function seedDailyQuizBanks(): void
    {
        if (!Schema::hasTable('daily_quiz_banks')) {
            return;
        }

        $quizSets = config('kids_program.quiz_sets', []);
        foreach ($quizSets as $dayKey => $set) {
            $bank = DailyQuizBank::updateOrCreate(
                ['day_key' => (string) $dayKey],
                [
                    'title' => (string) ($set['title'] ?? 'Kuis Harian'),
                    'memory_verse' => (string) ($set['memory_verse'] ?? ''),
                    'is_active' => true,
                ]
            );

            $bank->questions()->delete();

            foreach (($set['questions'] ?? []) as $qIndex => $questionRow) {
                $question = DailyQuizQuestion::create([
                    'daily_quiz_bank_id' => $bank->id,
                    'question_text' => (string) ($questionRow['question'] ?? ''),
                    'sort_order' => $qIndex + 1,
                    'is_active' => true,
                ]);

                foreach (($questionRow['options'] ?? []) as $oIndex => $optionText) {
                    $isCorrect = ((string) $optionText) === ((string) ($questionRow['answer'] ?? ''));
                    DailyQuizOption::create([
                        'daily_quiz_question_id' => $question->id,
                        'option_text' => (string) $optionText,
                        'is_correct' => $isCorrect,
                        'sort_order' => $oIndex + 1,
                    ]);
                }
            }
        }
    }
}
