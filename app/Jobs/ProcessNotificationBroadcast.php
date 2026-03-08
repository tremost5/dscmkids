<?php

namespace App\Jobs;

use App\Models\NotificationBroadcast;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessNotificationBroadcast implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $broadcastId)
    {
    }

    public function handle(): void
    {
        $broadcast = NotificationBroadcast::query()->find($this->broadcastId);

        if (!$broadcast) {
            return;
        }

        $broadcast->forceFill([
            'status' => 'processing',
            'processed_count' => 0,
            'failed_count' => 0,
            'last_error' => null,
        ])->save();

        $processedCount = 0;
        $failedCount = 0;
        $lastError = null;

        try {
            if (str_contains($broadcast->channel, 'email')) {
                User::query()
                    ->where('role', 'student')
                    ->whereNotNull('email')
                    ->orderBy('id')
                    ->chunkById(100, function ($students) use ($broadcast, &$processedCount, &$failedCount, &$lastError) {
                        foreach ($students as $student) {
                            try {
                                Mail::raw($broadcast->message, function ($mail) use ($student, $broadcast) {
                                    $mail->to($student->email)->subject('[DSCMKids] '.$broadcast->title);
                                });

                                $processedCount++;
                            } catch (Throwable $exception) {
                                $failedCount++;
                                $lastError = $exception->getMessage();
                            }
                        }
                    });
            }

            if (str_contains($broadcast->channel, 'whatsapp')) {
                $webhook = trim((string) config('services.whatsapp.broadcast_webhook'));

                if ($webhook !== '') {
                    try {
                        Http::timeout(10)->retry(2, 300)->post($webhook, [
                            'title' => $broadcast->title,
                            'message' => $broadcast->message,
                            'audience' => 'students',
                        ])->throw();

                        if ($broadcast->channel === 'whatsapp') {
                            $processedCount = max($processedCount, (int) $broadcast->target_count);
                        }
                    } catch (Throwable $exception) {
                        $failedCount++;
                        $lastError = $exception->getMessage();
                    }
                }
            }

            $status = 'sent';
            if ($failedCount > 0 && $processedCount === 0) {
                $status = 'failed';
            } elseif ($failedCount > 0) {
                $status = 'partial';
            }

            $broadcast->forceFill([
                'status' => $status,
                'processed_count' => $processedCount,
                'failed_count' => $failedCount,
                'last_error' => $lastError ? mb_substr($lastError, 0, 2000) : null,
                'sent_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $broadcast->forceFill([
                'status' => 'failed',
                'failed_count' => max(1, $failedCount),
                'last_error' => mb_substr($exception->getMessage(), 0, 2000),
            ])->save();

            throw $exception;
        }
    }
}
