<?php

namespace Tests\Feature;

use App\Models\EventRegistration;
use App\Models\PraCompanion;
use App\Services\EventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PraCompanionRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_companion_page_and_student_api_are_accessible(): void
    {
        $response = $this->get(route('events.pra-2026.pendamping'));

        $response->assertOk();
        $response->assertSee('Pendaftaran Pendamping PRA 2026');

        $event = app(EventService::class)->pra2026();
        $group = app(EventService::class)->groupForClass($event, 'PG');

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Samuel Tan',
            'nickname' => 'Samuel',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Bapak Samuel',
            'whatsapp_number' => '081234567890',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $apiResponse = $this->getJson(route('events.pra-2026.api.students', ['class' => 'PG']));

        $apiResponse->assertOk();
        $apiResponse->assertExactJson([
            [
                'id' => 1,
                'full_name' => 'Samuel Tan',
                'class_before' => 'PG',
            ],
        ]);

        $normalizedResponse = $this->getJson(route('events.pra-2026.api.students', ['class' => ' pg ']));

        $normalizedResponse->assertOk();
        $normalizedResponse->assertExactJson([
            [
                'id' => 1,
                'full_name' => 'Samuel Tan',
                'class_before' => 'PG',
            ],
        ]);
    }

    public function test_companion_registration_accepts_single_child(): void
    {
        $this->assertCompanionRegistrationChildrenCount(1);
    }

    public function test_companion_registration_accepts_three_children(): void
    {
        $this->assertCompanionRegistrationChildrenCount(3);
    }

    public function test_companion_registration_saves_relations_and_sends_whatsapp(): void
    {
        $this->assertCompanionRegistrationChildrenCount(2, true);
    }

    public function test_admin_can_export_companions(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        $student = EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Samuel Tan',
            'nickname' => 'Samuel',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Bapak Samuel',
            'whatsapp_number' => '081234567890',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $companion = PraCompanion::create([
            'companion_name' => 'Maria',
            'whatsapp_number' => '081299988877',
            'attend_26_june' => true,
            'attend_27_june' => false,
            'payment_method' => 'cash',
            'payment_status' => PraCompanion::PAYMENT_UNPAID,
        ]);

        $companion->registrations()->attach($student->id);

        $response = $this->actingAs($admin)->get(route('admin.pra.export-companions'));

        $response->assertDownload('pra-2026-pendamping-'.now()->format('Y-m-d').'.xlsx');
    }

    public function test_admin_can_view_companion_list(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        $student = EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Samuel Tan',
            'nickname' => 'Samuel',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Bapak Samuel',
            'whatsapp_number' => '081234567890',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $companion = PraCompanion::create([
            'companion_name' => 'Maria',
            'whatsapp_number' => '081299988877',
            'attend_26_june' => true,
            'attend_27_june' => true,
            'payment_method' => 'cash',
            'payment_status' => PraCompanion::PAYMENT_UNPAID,
        ]);
        $companion->registrations()->attach($student->id);

        $response = $this->actingAs($admin)->get(route('admin.pra.companions'));

        $response->assertOk();
        $response->assertSee('Pendamping PRA 2026');
        $response->assertSee('Maria');
        $response->assertSee('Samuel');
        $response->assertSee(route('admin.pra.participants.show', $student), false);
    }

    public function test_admin_participants_search_results_are_sorted_alphabetically(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Zara',
            'nickname' => 'Zara',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Perempuan',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Zara',
            'whatsapp_number' => '081111111111',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Alan',
            'nickname' => 'Alan',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Alan',
            'whatsapp_number' => '081111111112',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pra.participants', ['search' => 'a']));

        $response->assertOk();
        $response->assertSeeInOrder(['Alan', 'Zara']);
    }

    public function test_admin_payments_search_results_are_sorted_alphabetically(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Zara',
            'nickname' => 'Zara',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Perempuan',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Zara',
            'whatsapp_number' => '081111111111',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Alan',
            'nickname' => 'Alan',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Alan',
            'whatsapp_number' => '081111111112',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pra.payments', ['search' => 'a']));

        $response->assertOk();
        $response->assertSeeInOrder(['Alan', 'Zara']);
    }

    public function test_admin_attendance_search_results_are_sorted_alphabetically(): void
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Zara',
            'nickname' => 'Zara',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Perempuan',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Zara',
            'whatsapp_number' => '081111111111',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => 'Alan',
            'nickname' => 'Alan',
            'has_allergy' => false,
            'birth_date' => '2018-01-01',
            'gender' => 'Laki-laki',
            'class_before' => 'PG',
            'church_branch' => 'NICC',
            'parent_name' => 'Parent Alan',
            'whatsapp_number' => '081111111112',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'attendance_status' => 'absent',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pra.attendance', ['search' => 'a']));

        $response->assertOk();
        $response->assertSeeInOrder(['Alan', 'Zara']);
    }

    private function assertCompanionRegistrationChildrenCount(int $childCount, bool $assertWhatsapp = false): void
    {
        config([
            'services.fonnte.token' => 'test-token',
            'services.fonnte.endpoint' => 'https://api.fonnte.com/send',
            'services.fonnte.country_code' => '62',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $eventService = app(EventService::class);
        $event = $eventService->pra2026();
        $group = $eventService->groupForClass($event, 'PG');

        $students = collect(range(1, $childCount))->map(function (int $index) use ($event, $group) {
            return EventRegistration::create([
                'event_id' => $event->id,
                'event_group_id' => $group->id,
                'full_name' => 'Student '.$index,
                'nickname' => 'Student '.$index,
                'has_allergy' => false,
                'birth_date' => '2018-01-01',
                'gender' => 'Laki-laki',
                'class_before' => 'PG',
                'church_branch' => 'NICC',
                'parent_name' => 'Parent '.$index,
                'whatsapp_number' => '08123456789'.$index,
                'address' => 'Malang',
                'payment_method' => 'cash',
                'payment_status' => 'unpaid',
                'attendance_status' => 'absent',
                'registered_at' => now(),
            ]);
        });

        $children = $students->map(fn (EventRegistration $student) => [
            'class_before' => 'PG',
            'event_registration_id' => $student->id,
        ])->all();

        $response = $this->post(route('events.pra-2026.pendamping.store'), [
            'companion_name' => 'Maria',
            'whatsapp_number' => '081299988877',
            'children' => $children,
            'attend_26_june' => '1',
            'attend_27_june' => '1',
            'payment_method' => 'transfer',
            'payment_proof' => \Illuminate\Http\UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('events.pra-2026.pendamping'));

        $this->assertDatabaseHas('pra_companions', [
            'companion_name' => 'Maria',
            'whatsapp_number' => '081299988877',
            'payment_method' => 'transfer',
            'payment_status' => PraCompanion::PAYMENT_PENDING,
        ]);

        $companion = PraCompanion::query()->firstOrFail();
        $this->assertSame($childCount, $companion->registrations()->count());
        foreach ($students as $student) {
            $this->assertTrue($companion->registrations()->whereKey($student->id)->exists());
        }

        if ($assertWhatsapp) {
            Http::assertSentCount(1);
            Http::assertSent(function ($request) use ($childCount) {
                $message = (string) $request['message'];

                $hasAllChildren = collect(range(1, $childCount))
                    ->every(fn (int $index) => str_contains($message, '- Student '.$index));

                return $request['target'] === '6281299988877'
                    && str_contains($message, 'Pendaftaran Pendamping PRA 2026 berhasil.')
                    && str_contains($message, 'Maria')
                    && $hasAllChildren;
            });
        }
    }
}
