<?php

use App\Models\User;
use App\Services\PlatformMetricsService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('platform:daily-admin-digest', function () {
    $metrics = app(PlatformMetricsService::class)->adminDigest();
    $admins = User::query()
        ->whereIn('role', ['super_admin', 'admin'])
        ->where('is_active', true)
        ->whereNotNull('email')
        ->get(['email', 'name']);

    if ($admins->isEmpty()) {
        $this->warn('No active admin recipients found.');
        return self::SUCCESS;
    }

    $lines = [
        'Daily platform digest generated at '.$metrics['generated_at'],
        'Retention rate: '.$metrics['retention_rate'].'%',
        'Inactive students 7d: '.$metrics['inactive_students_7d'],
        'Pending jobs: '.$metrics['pending_jobs'],
        'Failed jobs: '.$metrics['failed_jobs'],
        'Pending broadcasts: '.$metrics['pending_broadcasts'],
        'Processing broadcasts: '.$metrics['processing_broadcasts'],
        'Failed broadcasts: '.$metrics['failed_broadcasts'],
    ];

    foreach ($admins as $admin) {
        Mail::raw(implode(PHP_EOL, $lines), function ($mail) use ($admin) {
            $mail->to($admin->email)->subject('[DSCMKids] Daily Admin Digest');
        });
    }

    $this->info('Daily admin digest sent to '.$admins->count().' admin(s).');

    return self::SUCCESS;
})->purpose('Send a daily operational digest to active admins');

Artisan::command('platform:remind-inactive-students', function () {
    $students = User::query()
        ->where('role', 'student')
        ->where('is_active', true)
        ->whereNotNull('email')
        ->where(function ($query) {
            $query->whereNull('last_quiz_played_on')
                ->orWhere('last_quiz_played_on', '<', now()->subDays(7)->toDateString());
        })
        ->get(['email', 'name']);

    foreach ($students as $student) {
        Mail::raw(
            "Halo {$student->name}, jangan lupa kembali ke DSCMKids hari ini untuk quiz, progress, dan reward mingguan.",
            function ($mail) use ($student) {
                $mail->to($student->email)->subject('[DSCMKids] Reminder Aktivitas Mingguan');
            }
        );
    }

    $this->info('Inactive student reminders sent: '.$students->count());

    return self::SUCCESS;
})->purpose('Send reminders to inactive students');

Artisan::command('platform:cleanup-old-activity', function () {
    if (!Schema::hasTable('admin_activity_logs')) {
        $this->warn('admin_activity_logs table not found.');
        return self::SUCCESS;
    }

    $deleted = DB::table('admin_activity_logs')
        ->where('created_at', '<', now()->subDays(90))
        ->delete();

    $this->info('Old admin activities deleted: '.$deleted);

    return self::SUCCESS;
})->purpose('Prune old admin activity logs');

Schedule::command('platform:daily-admin-digest')->dailyAt('07:00');
Schedule::command('platform:remind-inactive-students')->dailyAt('08:00');
Schedule::command('platform:cleanup-old-activity')->dailyAt('02:00');
