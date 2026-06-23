<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventBanner;
use App\Models\EventGroup;
use App\Models\EventRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventService
{
    public const PRA_2026_SLUG = 'pra-2026';

    public const GROUP_ONE_CLASSES = ['PG', 'TKA', 'TKB', '1', '2', '3', '4'];
    public const GROUP_TWO_CLASSES = ['5', '6', '7', '8', '9'];

    public function pra2026(): Event
    {
        $event = Event::query()->firstOrCreate(
            ['slug' => self::PRA_2026_SLUG],
            $this->defaultPraEventPayload()
        );

        $this->ensurePraDefaults($event);
        $this->repairRegistrationGroups($event);

        return $event->load(['groups', 'banner', 'galleries', 'videos']);
    }

    public function groupForClass(Event $event, string $classBefore): EventGroup
    {
        $targetSlug = in_array($classBefore, self::GROUP_ONE_CLASSES, true) ? 'grup-1' : 'grup-2';

        return $event->groups()
            ->where('slug', $targetSlug)
            ->firstOrFail();
    }

    public function stats(Event $event): array
    {
        $base = $event->registrations();

        return [
            'total' => (clone $base)->count(),
            'group_1' => (clone $base)->whereHas('group', fn (Builder $query) => $query->where('slug', 'grup-1'))->count(),
            'group_2' => (clone $base)->whereHas('group', fn (Builder $query) => $query->where('slug', 'grup-2'))->count(),
            'unpaid' => (clone $base)->where('payment_status', EventRegistration::PAYMENT_UNPAID)->count(),
            'pending' => (clone $base)->where('payment_status', EventRegistration::PAYMENT_PENDING)->count(),
            'paid' => (clone $base)->where('payment_status', EventRegistration::PAYMENT_PAID)->count(),
            'present' => (clone $base)->where('attendance_status', EventRegistration::ATTENDANCE_PRESENT)->count(),
            'absent' => (clone $base)->where('attendance_status', EventRegistration::ATTENDANCE_ABSENT)->count(),
        ];
    }

    public function registrationsQuery(Event $event, ?string $filter = null)
    {
        return $event->registrations()
            ->with(['group', 'paymentProof'])
            ->when($filter === 'grup-1' || $filter === 'grup-2', fn (Builder $query) => $query->whereHas('group', fn (Builder $group) => $group->where('slug', $filter)))
            ->when(in_array($filter, ['unpaid', 'pending_verification', 'paid'], true), fn (Builder $query) => $query->where('payment_status', $filter))
            ->latest('registered_at')
            ->latest('id');
    }

    public function broadcastRecipients(Event $event, string $filter): Collection
    {
        return $this->registrationsQuery($event, $filter === 'all' ? null : $filter)
            ->get()
            ->map(fn (EventRegistration $registration) => [
                'name' => $registration->parent_name,
                'child' => $registration->nickname,
                'phone' => $this->normalizeWhatsapp($registration->whatsapp_number),
                'group' => $registration->group?->name,
                'payment_status' => $registration->paymentStatusLabel(),
            ])
            ->filter(fn (array $recipient) => $recipient['phone'] !== '')
            ->values();
    }

    public function exportRegistrations(Event $event, ?string $filter, string $filename): BinaryFileResponse
    {
        $headers = [
    'No',
    'Nama Anak',
    'Nama Panggilan',
    'Jenis Kelamin',
    'Kelas',
    'Grup',
    'Sekolah Minggu',
    'Nama Orang Tua',
    'WhatsApp',
    'Alergi',
    'Catatan Alergi',
    'Metode Pembayaran',
    'Status Pembayaran',
    'Catatan Pembayaran',
    'Kehadiran',
    'Tanggal Daftar',
];

        $rows = $this->registrationsQuery($event, $filter)
            ->get()
            ->map(fn (EventRegistration $registration, int $index) => [
    $index + 1,
    $registration->full_name,
    $registration->nickname,
    $registration->gender,
    $registration->class_before,
    $registration->group?->name ?: '-',
    $registration->church_branch,
    $registration->parent_name,
    $registration->whatsapp_number,

    $registration->has_allergy ? 'YA' : 'Tidak',
    $registration->allergy_notes ?: '-',

    $registration->payment_method === 'cash'
        ? 'Tunai'
        : 'Transfer',

    $registration->paymentStatusLabel(),

    $registration->payment_notes ?: '-',

    $registration->attendanceStatusLabel(),

    optional($registration->registered_at)
        ->format('d M Y H:i') ?: '-',
])
            ->all();

        return app(SimpleXlsxExporter::class)->download($headers, $rows, $filename);
    }

    public function normalizeWhatsapp(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?: '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public function defaultPaymentContacts(): array
    {
        return [
            ['name' => 'Kak Santi', 'phone' => '081334001127'],
            ['name' => 'Kak Arie', 'phone' => '081334977979'],
            ['name' => 'Kak Wenny', 'phone' => '085233107075'],
        ];
    }

    private function ensurePraDefaults(Event $event): void
    {
        $event->groups()->updateOrCreate(
            ['slug' => 'grup-1'],
            [
                'name' => 'GRUP 1',
                'starts_on' => '2026-06-26',
                'ends_on' => '2026-06-27',
                'class_levels' => self::GROUP_ONE_CLASSES,
                'sort_order' => 1,
            ]
        );

        $event->groups()->updateOrCreate(
            ['slug' => 'grup-2'],
            [
                'name' => 'GRUP 2',
                'starts_on' => '2026-07-03',
                'ends_on' => '2026-07-04',
                'class_levels' => self::GROUP_TWO_CLASSES,
                'sort_order' => 2,
            ]
        );

        EventBanner::query()->firstOrCreate(
            ['event_id' => $event->id],
            [
                'title' => 'PEKAN ROHANI ANAK 2026',
                'subtitle' => 'Petualangan Iman yang Tak Terlupakan',
                'splash_title' => 'DAFTAR PRA 2026',
                'splash_subtitle' => 'Tempat terbatas untuk setiap grup. Daftarkan anak hari ini.',
                'is_active' => true,
            ]
        );
    }

    private function repairRegistrationGroups(Event $event): void
    {
        $groupsBySlug = $event->groups()->get()->keyBy('slug');

        if (!$groupsBySlug->has('grup-1') || !$groupsBySlug->has('grup-2')) {
            return;
        }

        $event->registrations()
            ->whereDoesntHave('group')
            ->get()
            ->each(function (EventRegistration $registration) use ($groupsBySlug): void {
                $targetSlug = in_array($registration->class_before, self::GROUP_ONE_CLASSES, true) ? 'grup-1' : 'grup-2';
                $registration->forceFill([
                    'event_group_id' => $groupsBySlug[$targetSlug]->id,
                ])->save();
            });
    }

    private function defaultPraEventPayload(): array
    {
        return [
            'title' => 'PEKAN ROHANI ANAK 2026',
            'subtitle' => 'Petualangan Iman yang Tak Terlupakan',
            'description' => 'PRA 2026 adalah pengalaman rohani anak yang dirancang hangat, seru, dan bermakna melalui firman Tuhan, games, pujian, kelompok kecil, dan kebersamaan lintas kelas.',
            'location_name' => 'DSCMKids Camp Location',
            'location_description' => 'Area kegiatan yang nyaman untuk ibadah anak, aktivitas kelompok, permainan, dan momen kebersamaan yang aman bagi setiap peserta.',
            'benefits' => [
                'Belajar Firman Tuhan',
                'Menambah Teman Baru',
                'Aktivitas Menarik',
                'Games Seru',
                'Pengalaman Rohani',
            ],
            'schedule' => [
                ['time' => 'Hari 1 - 08.00', 'title' => 'Registrasi & Opening Celebration', 'description' => 'Anak disambut panitia dan masuk ke kelompok.'],
                ['time' => 'Hari 1 - 10.00', 'title' => 'Firman Tuhan & Small Group', 'description' => 'Belajar tema iman dengan aktivitas sesuai usia.'],
                ['time' => 'Hari 1 - 14.00', 'title' => 'Games & Team Challenge', 'description' => 'Permainan seru yang membangun kerja sama.'],
                ['time' => 'Hari 2 - 09.00', 'title' => 'Worship, Reflection & Closing', 'description' => 'Pujian, doa, dan penutup bersama.'],
            ],
            'payment_info' => [
                'transfer_note' => 'Upload bukti pembayaran setelah transfer. Status akan menjadi Menunggu Verifikasi.',
                'cash_note' => 'Silahkan melakukan pembayaran dan konfirmasikan ke bagian informasi pembayaran berikut:',
                'contacts' => $this->defaultPaymentContacts(),
            ],
            'is_active' => true,
        ];
    }
}
