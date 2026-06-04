<?php

namespace Tests\Unit;

use App\Models\EventGroup;
use App\Models\EventRegistration;
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
}
