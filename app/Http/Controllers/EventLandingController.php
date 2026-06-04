<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRegistrationRequest;
use App\Models\EventRegistration;
use App\Models\PaymentProof;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventLandingController extends Controller
{
    public function show(EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $stats = $eventService->stats($event);
        $registrationsByGroup = $event->registrations()
            ->with('group')
            ->latest('registered_at')
            ->latest('id')
            ->get()
            ->groupBy(fn (EventRegistration $registration) => $registration->group?->slug ?: 'unknown');

        return view('events.pra-2026', [
            'event' => $event,
            'banner' => $event->banner,
            'groups' => $event->groups,
            'stats' => $stats,
            'registrationsByGroup' => $registrationsByGroup,
        ]);
    }

    public function register(StoreEventRegistrationRequest $request, EventService $eventService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $validated = $request->validated();
        $group = $eventService->groupForClass($event, $validated['class_before']);
        $paymentMethod = $validated['payment_method'];

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'event_group_id' => $group->id,
            'full_name' => $validated['full_name'],
            'nickname' => $validated['nickname'],
            'has_allergy' => $validated['has_allergy'] === 'yes',
            'allergy_notes' => $validated['has_allergy'] === 'yes' ? ($validated['allergy_notes'] ?? null) : null,
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'class_before' => $validated['class_before'],
            'church_branch' => $validated['church_branch'],
            'parent_name' => $validated['parent_name'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'address' => $validated['address'],
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentMethod === 'transfer'
                ? EventRegistration::PAYMENT_PENDING
                : EventRegistration::PAYMENT_UNPAID,
            'attendance_status' => EventRegistration::ATTENDANCE_ABSENT,
            'registered_at' => now(),
        ]);

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('event-payment-proofs', 'public');

            PaymentProof::create([
                'event_registration_id' => $registration->id,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'verification_status' => 'pending',
            ]);
        }

        return redirect()
            ->route('events.pra-2026')
            ->with('registration_success', true);
    }
}
