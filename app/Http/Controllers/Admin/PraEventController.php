<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventGallery;
use App\Models\EventRegistration;
use App\Models\EventVideo;
use App\Services\EventService;
use App\Services\FonnteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PraEventController extends Controller
{
    public function dashboard(EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $stats = $eventService->stats($event);
        $recentRegistrations = $eventService->registrationsQuery($event)->take(8)->get();

        return view('admin.pra.dashboard', compact('event', 'stats', 'recentRegistrations'));
    }

    public function participants(Request $request, EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $filter = $request->query('filter');
        $registrations = $eventService->registrationsQuery($event, is_string($filter) ? $filter : null)
            ->paginate(20)
            ->withQueryString();
        $stats = $eventService->stats($event);

        return view('admin.pra.participants', compact('event', 'registrations', 'filter', 'stats'));
    }

    public function groupOne(EventService $eventService): View
    {
        request()->merge(['filter' => 'grup-1']);

        return $this->participants(request(), $eventService);
    }

    public function groupTwo(EventService $eventService): View
    {
        request()->merge(['filter' => 'grup-2']);

        return $this->participants(request(), $eventService);
    }

    public function participantDetail(EventRegistration $registration, EventService $eventService): View
    {
        $event = $eventService->pra2026();
        abort_unless((int) $registration->event_id === (int) $event->id, 404);

        return view('admin.pra.participant-detail', [
            'event' => $event,
            'registration' => $registration->load(['group', 'paymentProof']),
        ]);
    }

    public function resendWhatsappConfirmation(EventRegistration $registration, EventService $eventService, FonnteService $fonnteService): RedirectResponse
    {
        $event = $eventService->pra2026();
        abort_unless((int) $registration->event_id === (int) $event->id, 404);

        $sent = $fonnteService->sendParentConfirmation($registration->load('group'));

        if ($sent) {
            return back()->with('success', 'WhatsApp confirmation berhasil dikirim ulang.');
        }

        return back()->withErrors([
            'whatsapp' => 'WhatsApp confirmation belum berhasil dikirim. Cek konfigurasi Fonnte atau log aplikasi.',
        ]);
    }

    public function payments(Request $request, EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $filter = $request->query('filter');
        $registrations = $eventService->registrationsQuery($event, is_string($filter) ? $filter : null)
            ->whereIn('payment_method', ['cash', 'transfer'])
            ->paginate(20)
            ->withQueryString();
        $stats = $eventService->stats($event);

        return view('admin.pra.payments', compact('event', 'registrations', 'filter', 'stats'));
    }

    public function updatePayment(Request $request, EventRegistration $registration): RedirectResponse
    {
        $data = $request->validate([
            'payment_status' => ['required', Rule::in([
                EventRegistration::PAYMENT_UNPAID,
                EventRegistration::PAYMENT_PENDING,
                EventRegistration::PAYMENT_PAID,
            ])],
        ]);

        $registration->update(['payment_status' => $data['payment_status']]);

        if ($registration->paymentProof) {
            $registration->paymentProof->update([
                'verification_status' => $data['payment_status'] === EventRegistration::PAYMENT_PAID ? 'verified' : 'pending',
                'verified_by' => $data['payment_status'] === EventRegistration::PAYMENT_PAID ? $request->user()?->id : null,
                'verified_at' => $data['payment_status'] === EventRegistration::PAYMENT_PAID ? now() : null,
            ]);
        }

        return back()->with('success', 'Status pembayaran diperbarui.');
    }

    public function attendance(EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $stats = $eventService->stats($event);
        $registrations = $eventService->registrationsQuery($event)->paginate(30);

        return view('admin.pra.attendance', compact('event', 'stats', 'registrations'));
    }

    public function updateAttendance(Request $request, EventRegistration $registration): RedirectResponse
    {
        $data = $request->validate([
            'attendance_status' => ['required', Rule::in([
                EventRegistration::ATTENDANCE_PRESENT,
                EventRegistration::ATTENDANCE_ABSENT,
            ])],
        ]);

        $registration->update(['attendance_status' => $data['attendance_status']]);

        return back()->with('success', 'Status kehadiran diperbarui.');
    }

    public function broadcast(Request $request, EventService $eventService): View
    {
        $event = $eventService->pra2026();
        $filter = (string) $request->query('filter', 'all');
        $message = (string) $request->query('message', '');
        $recipients = $eventService->broadcastRecipients($event, $filter);

        return view('admin.pra.broadcast', compact('event', 'filter', 'message', 'recipients'));
    }

    public function content(EventService $eventService): View
    {
        $event = $eventService->pra2026();

        return view('admin.pra.content', [
            'event' => $event->load(['banner', 'galleries', 'videos']),
            'banner' => $event->banner,
        ]);
    }

    public function updateContent(Request $request, EventService $eventService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'location_description' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'schedule' => ['nullable', 'string'],
            'payment_contacts' => ['nullable', 'string'],
            'cash_note' => ['nullable', 'string'],
            'transfer_note' => ['nullable', 'string'],
            'banner_title' => ['required', 'string', 'max:255'],
            'banner_subtitle' => ['nullable', 'string', 'max:255'],
            'splash_title' => ['nullable', 'string', 'max:255'],
            'splash_subtitle' => ['nullable', 'string', 'max:255'],
            'background_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $event->update([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'location_name' => $data['location_name'] ?? null,
            'location_description' => $data['location_description'] ?? null,
            'benefits' => $this->lines($data['benefits'] ?? ''),
            'schedule' => $this->scheduleLines($data['schedule'] ?? ''),
            'payment_info' => [
                'cash_note' => $data['cash_note'] ?? '',
                'transfer_note' => $data['transfer_note'] ?? '',
                'contacts' => $this->contactLines($data['payment_contacts'] ?? ''),
            ],
        ]);

        $bannerPayload = [
            'title' => $data['banner_title'],
            'subtitle' => $data['banner_subtitle'] ?? null,
            'splash_title' => $data['splash_title'] ?? null,
            'splash_subtitle' => $data['splash_subtitle'] ?? null,
            'is_active' => true,
        ];

        $banner = $event->banner ?: $event->banners()->create($bannerPayload);
        if ($request->hasFile('background_image')) {
            if ($banner->background_image_path) {
                Storage::disk('public')->delete($banner->background_image_path);
            }

            $bannerPayload['background_image_path'] = $request->file('background_image')->store('event-banners', 'public');
        }

        $banner->update($bannerPayload);

        return back()->with('success', 'Konten landing page PRA diperbarui.');
    }

    public function storeGallery(Request $request, EventService $eventService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $event->galleries()->create([
            'title' => $data['title'] ?? null,
            'image_path' => $request->file('image')->store('event-galleries', 'public'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Foto lokasi ditambahkan.');
    }

    public function deleteGallery(EventGallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Foto lokasi dihapus.');
    }

    public function storeVideo(Request $request, EventService $eventService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'video_file' => ['nullable', 'file', 'max:10240', 'mimetypes:video/mp4,video/webm,video/quicktime'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!$request->hasFile('video_file') && empty($data['video_url'])) {
            return back()->withErrors(['video_url' => 'Isi link video atau upload file video.'])->withInput();
        }

        $event->videos()->create([
            'title' => $data['title'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'video_path' => $request->hasFile('video_file') ? $request->file('video_file')->store('event-videos', 'public') : null,
            'description' => $data['description'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Video lokasi ditambahkan.');
    }

    public function deleteVideo(EventVideo $video): RedirectResponse
    {
        if ($video->video_path) {
            Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return back()->with('success', 'Video lokasi dihapus.');
    }

    public function export(Request $request, EventService $eventService): StreamedResponse
    {
        $event = $eventService->pra2026();
        $type = (string) $request->query('type', 'all');
        $filter = match ($type) {
            'group-1' => 'grup-1',
            'group-2' => 'grup-2',
            'payments' => null,
            default => null,
        };

        return $eventService->exportRegistrations($event, $filter, 'pra-2026-'.$type.'.csv');
    }

    private function lines(string $value): array
    {
        return collect(preg_split('/\R/', $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    private function scheduleLines(string $value): array
    {
        return collect($this->lines($value))
            ->map(function (string $line) {
                [$time, $title, $description] = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');

                return compact('time', 'title', 'description');
            })
            ->values()
            ->all();
    }

    private function contactLines(string $value): array
    {
        return collect($this->lines($value))
            ->map(function (string $line) {
                [$name, $phone] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

                return compact('name', 'phone');
            })
            ->filter(fn (array $contact) => $contact['name'] !== '' && $contact['phone'] !== '')
            ->values()
            ->all();
    }
}
