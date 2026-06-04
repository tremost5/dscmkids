<?php

namespace App\Services;

use App\Models\BroadcastLog;
use App\Models\EventRegistration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    public function sendPraRegistrationNotifications(EventRegistration $registration): void
    {
        $registration->loadMissing('group');

        $this->sendParentConfirmation($registration);
        $this->sendCommitteeNotification($registration);
    }

    public function sendParentConfirmation(EventRegistration $registration): bool
    {
        $registration->loadMissing('group');

        return $this->sendMessage(
            $registration->whatsapp_number,
            $this->parentConfirmationMessage($registration),
            ['type' => 'pra_parent_confirmation', 'registration_id' => $registration->id]
        );
    }

    public function sendCommitteeNotification(EventRegistration $registration): bool
    {
        $numbers = $this->committeeNumbers();

        if ($numbers === []) {
            Log::warning('PRA committee WhatsApp notification skipped: no committee numbers configured.', [
                'registration_id' => $registration->id,
            ]);

            return false;
        }

        return $this->sendMessage(
            implode(',', $numbers),
            $this->committeeNotificationMessage($registration),
            ['type' => 'pra_committee_notification', 'registration_id' => $registration->id]
        );
    }

    public function sendMessage(string $target, string $message, array $context = []): bool
    {
        return $this->sendMessageWithResult($target, $message, $context)['success'];
    }

    public function sendBroadcast(Collection $recipients, string $message, ?int $adminId, string $filter): BroadcastLog
    {
        $broadcastLog = BroadcastLog::create([
            'admin_id' => $adminId,
            'filter' => $filter,
            'message' => $message,
            'total_recipients' => $recipients->count(),
            'success_count' => 0,
            'failed_count' => 0,
        ]);

        $successCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $phone = (string) ($recipient['phone'] ?? '');
            $result = $this->sendMessageWithResult($phone, $message, [
                'type' => 'pra_broadcast',
                'broadcast_log_id' => $broadcastLog->id,
                'recipient_name' => $recipient['name'] ?? null,
            ]);

            if ($result['success']) {
                $successCount++;
            } else {
                $failedCount++;
            }

            $broadcastLog->recipients()->create([
                'phone' => (string) ($result['target'] ?: $phone),
                'recipient_name' => (string) ($recipient['name'] ?? ''),
                'status' => $result['success'] ? 'success' : 'failed',
                'response' => $result['response'],
            ]);
        }

        $broadcastLog->update([
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ]);

        return $broadcastLog->refresh();
    }

    public function hasToken(): bool
    {
        return trim((string) config('services.fonnte.token')) !== '';
    }

    public function sendMessageWithResult(string $target, string $message, array $context = []): array
    {
        $token = trim((string) config('services.fonnte.token'));
        $endpoint = trim((string) config('services.fonnte.endpoint', 'https://api.fonnte.com/send'));
        $target = $this->normalizeTargets($target);

        if ($token === '' || $endpoint === '' || $target === '') {
            $response = 'Configuration or target is missing.';
            Log::warning('Fonnte WhatsApp message skipped: configuration or target is missing.', $context + [
                'has_token' => $token !== '',
                'has_endpoint' => $endpoint !== '',
                'has_target' => $target !== '',
            ]);

            return [
                'success' => false,
                'target' => $target,
                'status' => null,
                'response' => $response,
            ];
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(10)
                ->retry(1, 300)
                ->post($endpoint, [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => (string) config('services.fonnte.country_code', '62'),
                ]);

            if (!$response->successful() || $response->json('status') === false) {
                $responseBody = mb_substr($response->body(), 0, 2000);
                Log::warning('Fonnte WhatsApp message failed.', $context + [
                    'target' => $target,
                    'status' => $response->status(),
                    'response' => $responseBody,
                ]);

                return [
                    'success' => false,
                    'target' => $target,
                    'status' => $response->status(),
                    'response' => $responseBody,
                ];
            }

            Log::info('Fonnte WhatsApp message sent.', $context + ['target' => $target]);

            return [
                'success' => true,
                'target' => $target,
                'status' => $response->status(),
                'response' => mb_substr($response->body(), 0, 2000),
            ];
        } catch (Throwable $exception) {
            Log::warning('Fonnte WhatsApp message exception.', $context + [
                'target' => $target,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'target' => $target,
                'status' => null,
                'response' => $exception->getMessage(),
            ];
        }
    }

    private function parentConfirmationMessage(EventRegistration $registration): string
    {
        return <<<MESSAGE
Shalom,

Pendaftaran PRA 2026 atas nama:

{$registration->full_name}
({$registration->nickname})

Kelas {$registration->class_before}

telah berhasil kami terima.

Terima kasih telah mendaftarkan putra/putri Anda pada Pekan Rohani Anak 2026.

Informasi selanjutnya mengenai kelompok, perlengkapan yang perlu dibawa, jadwal keberangkatan, dan informasi kegiatan akan kami sampaikan melalui WhatsApp ini.

Tuhan Yesus memberkati.

Panitia PRA 2026
DSCM Kids
MESSAGE;
    }

    private function committeeNotificationMessage(EventRegistration $registration): string
    {
        $paymentMethod = $registration->payment_method === 'cash' ? 'Tunai' : 'Transfer';
        $groupName = $registration->group?->name ?: '-';

        return <<<MESSAGE
🔔 PENDAFTARAN PRA BARU

Nama: {$registration->full_name}
Nama Panggilan: {$registration->nickname}
Kelas: {$registration->class_before}
Jenis Kelamin: {$registration->gender}
Sekolah Minggu: {$registration->church_branch}
Orang Tua/Wali: {$registration->parent_name}
No WA: {$registration->whatsapp_number}
Metode Pembayaran: {$paymentMethod}
Grup PRA: {$groupName}

Silakan cek Dashboard Admin PRA 2026.
MESSAGE;
    }

    private function committeeNumbers(): array
    {
        return collect(config('services.pra.committee_numbers', []))
            ->map(fn ($number) => $this->normalizeTargets((string) $number))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeTargets(string $targets): string
    {
        return collect(explode(',', $targets))
            ->map(function (string $target) {
                $target = trim($target);

                if (str_contains($target, '@g.us')) {
                    return $target;
                }

                $digits = preg_replace('/\D+/', '', $target) ?: '';

                if (str_starts_with($digits, '0')) {
                    return '62'.substr($digits, 1);
                }

                if (str_starts_with($digits, '8')) {
                    return '62'.$digits;
                }

                return $digits;
            })
            ->filter()
            ->implode(',');
    }
}
