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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class PraEventController extends Controller
{
    private const BROADCAST_FILTERS = ['all', 'grup-1', 'grup-2', 'unpaid', 'pending_verification', 'paid'];
    private const OPTIONAL_IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
    private const GALLERY_IMAGE_RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

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
        $filter = in_array($filter, self::BROADCAST_FILTERS, true) ? $filter : 'all';
        $message = (string) $request->query('message', '');
        $recipients = $eventService->broadcastRecipients($event, $filter);
        $broadcastResult = session('broadcast_result');

        return view('admin.pra.broadcast', compact('event', 'filter', 'message', 'recipients', 'broadcastResult'));
    }

    public function sendBroadcast(Request $request, EventService $eventService, FonnteService $fonnteService): RedirectResponse
    {
        $data = $request->validate([
            'filter' => ['required', Rule::in(self::BROADCAST_FILTERS)],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $filter = (string) $data['filter'];
        $message = trim((string) $data['message']);
        $redirectPayload = ['filter' => $filter, 'message' => $message];

        if (!$fonnteService->hasToken()) {
            return redirect()
                ->route('admin.pra.broadcast', $redirectPayload)
                ->withErrors(['fonnte' => 'FONNTE_TOKEN belum diisi. Broadcast WhatsApp belum dapat dikirim.']);
        }

        $event = $eventService->pra2026();
        $recipients = $eventService->broadcastRecipients($event, $filter);

        if ($recipients->isEmpty()) {
            return redirect()
                ->route('admin.pra.broadcast', $redirectPayload)
                ->withErrors(['recipients' => 'Tidak ada penerima untuk filter ini.']);
        }

        $broadcastLog = $fonnteService->sendBroadcast($recipients, $message, $request->user()?->id, $filter);

        $result = [
            'total' => $broadcastLog->total_recipients,
            'success' => $broadcastLog->success_count,
            'failed' => $broadcastLog->failed_count,
        ];

        $redirect = redirect()
            ->route('admin.pra.broadcast', $redirectPayload)
            ->with('broadcast_result', $result);

        if ($broadcastLog->success_count > 0) {
            $message = $broadcastLog->failed_count > 0
                ? "Broadcast terkirim. {$broadcastLog->success_count} berhasil, {$broadcastLog->failed_count} gagal."
                : "Broadcast berhasil dikirim. {$broadcastLog->success_count} berhasil, 0 gagal.";

            return $redirect->with('success', $message);
        }

        return $redirect->withErrors([
            'broadcast' => "Broadcast gagal dikirim. Berhasil 0 peserta, gagal {$broadcastLog->failed_count} peserta.",
        ]);
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
            'background_image' => self::OPTIONAL_IMAGE_RULES,
        ], $this->uploadValidationMessages());

        $newBackgroundPath = null;
        $backgroundUploaded = $request->hasFile('background_image');
        if ($backgroundUploaded) {
            try {
                $newBackgroundPath = $request->file('background_image')->store('event-banners', 'public');
            } catch (Throwable) {
                return back()
                    ->withErrors(['background_image' => 'Background banner belum berhasil diupload. Coba gunakan file JPG, PNG, atau WebP maksimal 5MB.'])
                    ->withInput();
            }
        }

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
        if ($newBackgroundPath) {
            if ($banner->background_image_path) {
                Storage::disk('public')->delete($banner->background_image_path);
            }

            $bannerPayload['background_image_path'] = $newBackgroundPath;
        }

        $banner->update($bannerPayload);

        return back()->with(
            'success',
            $backgroundUploaded
                ? 'Konten PRA dan background banner berhasil diperbarui.'
                : 'Konten landing page PRA berhasil diperbarui.'
        );
    }

    public function storeGallery(Request $request, EventService $eventService): RedirectResponse
    {
        $event = $eventService->pra2026();
        $data = $request->validate([
            'group_slug' => ['required', Rule::in(['grup-1', 'grup-2'])],
            'title' => ['nullable', 'string', 'max:255'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => self::GALLERY_IMAGE_RULES,
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], $this->uploadValidationMessages());

        $storedImages = [];

        try {
            foreach ($request->file('images', []) as $image) {
                $storedImages[] = $image->store('event-galleries', 'public');
            }
        } catch (Throwable) {
            foreach ($storedImages as $storedImage) {
                Storage::disk('public')->delete($storedImage);
            }

            return back()
                ->withErrors(['images' => 'Foto lokasi belum berhasil diupload. Coba gunakan file JPG, PNG, atau WebP maksimal 5MB.'])
                ->withInput();
        }

        foreach ($storedImages as $index => $imagePath) {
            $event->galleries()->create([
                'group_slug' => $data['group_slug'],
                'title' => $data['title'] ?? null,
                'image_path' => $imagePath,
                'sort_order' => (int) ($data['sort_order'] ?? 0) + $index,
            ]);
        }

        return back()->with('success', count($storedImages).' foto lokasi berhasil diupload dan langsung tampil di galeri.');
    }

    public function deleteGallery(EventGallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Foto lokasi berhasil dihapus dari galeri.');
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
        ], [
            'video_url.url' => 'Link video harus berupa URL yang valid.',
            'video_file.max' => 'Ukuran video maksimal 10MB.',
            'video_file.mimetypes' => 'Format video harus MP4, WebM, atau QuickTime.',
        ]);

        if (!$request->hasFile('video_file') && empty($data['video_url'])) {
            return back()->withErrors(['video_url' => 'Isi link video atau upload file video.'])->withInput();
        }

        try {
            $videoPath = $request->hasFile('video_file')
                ? $request->file('video_file')->store('event-videos', 'public')
                : null;
        } catch (Throwable) {
            return back()
                ->withErrors(['video_file' => 'Video lokasi belum berhasil diupload. Coba gunakan file MP4/WebM maksimal 10MB.'])
                ->withInput();
        }

        $event->videos()->create([
            'title' => $data['title'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'video_path' => $videoPath,
            'description' => $data['description'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Video lokasi berhasil ditambahkan.');
    }

    public function deleteVideo(EventVideo $video): RedirectResponse
    {
        if ($video->video_path) {
            Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return back()->with('success', 'Video lokasi berhasil dihapus.');
    }

    public function export(Request $request, EventService $eventService): BinaryFileResponse
    {
        $event = $eventService->pra2026();
        $type = (string) $request->query('type', 'all');
        $filter = match ($type) {
            'group-1' => 'grup-1',
            'group-2' => 'grup-2',
            'payments' => null,
            default => null,
        };

        return $eventService->exportRegistrations($event, $filter, 'pra-2026-peserta-'.now()->format('Y-m-d').'.xlsx');
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

    private function uploadValidationMessages(): array
    {
        return [
            'background_image.image' => 'Background banner harus berupa gambar.',
            'background_image.mimes' => 'Background banner harus berformat JPG, JPEG, PNG, atau WebP.',
            'background_image.max' => 'Ukuran background banner maksimal 5MB.',
            'image.required' => 'Pilih foto lokasi terlebih dahulu.',
            'images.required' => 'Pilih foto lokasi terlebih dahulu.',
            'images.array' => 'Pilih satu atau beberapa foto lokasi yang valid.',
            'images.min' => 'Pilih minimal satu foto lokasi.',
            'group_slug.required' => 'Pilih grup galeri terlebih dahulu.',
            'group_slug.in' => 'Grup galeri harus Grup 1 atau Grup 2.',
            'image.image' => 'Foto lokasi harus berupa gambar.',
            'image.mimes' => 'Foto lokasi harus berformat JPG, JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran foto lokasi maksimal 5MB.',
            'images.*.image' => 'Setiap foto lokasi harus berupa gambar.',
            'images.*.mimes' => 'Setiap foto lokasi harus berformat JPG, JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran setiap foto lokasi maksimal 5MB.',
        ];
    }
}
