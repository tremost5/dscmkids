<?php

namespace Tests\Unit;

use App\Models\EventGroup;
use App\Models\EventRegistration;
use App\Models\PraCompanion;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteServiceTest extends TestCase
{
    public function test_it_sends_parent_and_committee_pra_registration_notifications(): void
    {
        config([
            'services.fonnte.token' => 'test-token',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
            'services.fonnte.country_code' => '62',
            'services.pra.committee_numbers' => ['081334001127', '081334977979'],
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $registration = new EventRegistration([
            'full_name' => 'Samuel Tan',
            'nickname' => 'Samuel',
            'gender' => 'Laki-laki',
            'class_before' => '3',
            'church_branch' => 'NICC',
            'parent_name' => 'Bapak Samuel',
            'whatsapp_number' => '081234567890',
            'payment_method' => 'transfer',
        ]);
        $registration->id = 25;
        $registration->setRelation('group', new EventGroup(['name' => 'GRUP 1']));

        app(FonnteService::class)->sendPraRegistrationNotifications($registration);

        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request['target'] === '6281234567890'
            && str_contains((string) $request['message'], 'Pendaftaran PRA 2026 atas nama:')
            && str_contains((string) $request['message'], 'Samuel Tan')
            && str_contains((string) $request['message'], '(Samuel)'));

        Http::assertSent(fn ($request) => $request['target'] === '6281334001127,6281334977979'
            && str_contains((string) $request['message'], 'PENDAFTARAN PRA BARU')
            && str_contains((string) $request['message'], 'Metode Pembayaran: Transfer')
            && str_contains((string) $request['message'], 'Grup PRA: GRUP 1'));
    }

    public function test_it_sends_pra_companion_notifications(): void
    {
        config([
            'services.fonnte.token' => 'test-token',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
            'services.fonnte.country_code' => '62',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $companion = new PraCompanion([
            'companion_name' => 'Maria',
            'whatsapp_number' => '081299988877',
            'attend_26_june' => true,
            'attend_27_june' => false,
            'payment_method' => 'transfer',
            'payment_status' => PraCompanion::PAYMENT_PENDING,
        ]);
        $companion->id = 9;
        $companion->setRelation('registrations', collect([
            new EventRegistration(['nickname' => 'Samuel']),
            new EventRegistration(['nickname' => 'Jason']),
        ]));

        app(FonnteService::class)->sendPraCompanionNotification($companion);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['target'] === '6281299988877'
            && str_contains((string) $request['message'], 'Pendaftaran Pendamping PRA 2026 berhasil.')
            && str_contains((string) $request['message'], '- Samuel')
            && str_contains((string) $request['message'], '- Jason')
            && str_contains((string) $request['message'], '✓ 26 Juni 2026')
            && str_contains((string) $request['message'], 'Menunggu Verifikasi'));
    }
}
