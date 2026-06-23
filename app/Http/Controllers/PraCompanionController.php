<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePraCompanionRequest;
use App\Models\EventRegistration;
use App\Models\PraCompanion;
use App\Services\EventService;
use App\Services\FonnteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PraCompanionController extends Controller
{
    private const CLASS_OPTIONS = ['PG', 'TKA', 'TKB', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public function show(EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $paymentInfo = is_array($event->payment_info) ? $event->payment_info : [];

        return view('events.pra-companion', [
            'event' => $event,
            'banner' => $event->banner,
            'paymentInfo' => $paymentInfo,
            'children' => old('children', [['class_before' => '', 'event_registration_id' => '']]),
            'classOptions' => self::CLASS_OPTIONS,
        ]);
    }

    public function store(StorePraCompanionRequest $request, EventService $eventService, FonnteService $fonnteService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $validated = $request->validated();
        $selectedRegistrationIds = collect($validated['children'])
            ->pluck('event_registration_id')
            ->map(fn ($value) => (int) $value)
            ->values();

        $availableRegistrations = $this->findRegistrationsByIds($selectedRegistrationIds);

        foreach ($validated['children'] as $index => $child) {
            $registration = $availableRegistrations->get((int) $child['event_registration_id']);

            if (!$registration || $registration->class_before !== $child['class_before']) {
                throw ValidationException::withMessages([
                    "children.$index.event_registration_id" => 'Murid yang dipilih tidak sesuai dengan kelas.',
                ]);
            }
        }

        $paymentStatus = $validated['payment_method'] === 'transfer'
            ? PraCompanion::PAYMENT_PENDING
            : PraCompanion::PAYMENT_UNPAID;

        $companion = DB::transaction(function () use ($validated, $request, $selectedRegistrationIds, $paymentStatus): PraCompanion {
            $companion = PraCompanion::create([
                'companion_name' => $validated['companion_name'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'attend_26_june' => (bool) ($validated['attend_26_june'] ?? false),
                'attend_27_june' => (bool) ($validated['attend_27_june'] ?? false),
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
            ]);

            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('pra-companion-payment-proofs', 'public');

                $companion->update([
                    'payment_proof_path' => $path,
                ]);
            }

            $companion->registrations()->sync($selectedRegistrationIds->all());

            return $companion->load('registrations');
        });

        $fonnteService->sendPraCompanionNotification($companion);

        return redirect()
            ->route('events.pra-2026.pendamping')
            ->with('registration_success', true);
    }

    public function students(Request $request): JsonResponse
    {
        $class = strtoupper(trim((string) $request->query('class', '')));

        if (!in_array($class, self::CLASS_OPTIONS, true)) {
            throw ValidationException::withMessages([
                'class' => 'The selected class is invalid.',
            ]);
        }

        $students = EventRegistration::query()
            ->select(['id', 'full_name', 'class_before'])
            ->whereRaw('UPPER(TRIM(class_before)) = ?', [$class])
            ->orderBy('class_before')
            ->orderBy('full_name')
            ->get()
            ->map(fn (EventRegistration $registration) => [
                'id' => $registration->id,
                'full_name' => $registration->full_name,
                'class_before' => $registration->class_before,
            ])
            ->values();

        return response()->json($students);
    }

    private function findRegistrationsByIds(Collection $ids): Collection
    {
        $ids = $ids->filter()->unique()->values();

        $local = EventRegistration::query()
            ->select(['id', 'class_before', 'nickname'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if ($local->isNotEmpty()) {
            return $local;
        }

        $connection = (string) config('school_data.connection', 'external');

        if (!$this->connectionHasTable($connection, 'event_registrations')) {
            return $local;
        }

        try {
            return EventRegistration::on($connection)
                ->select(['id', 'class_before', 'nickname'])
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');
        } catch (\Throwable) {
            return $local;
        }
    }

    private function connectionHasTable(string $connection, string $table): bool
    {
        try {
            return Schema::connection($connection)->hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
