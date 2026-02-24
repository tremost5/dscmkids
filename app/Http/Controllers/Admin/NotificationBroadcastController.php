<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationBroadcast;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class NotificationBroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Schema::hasTable('notification_broadcasts')
            ? NotificationBroadcast::query()->latest('id')->paginate(12)
            : collect();

        return view('admin.notifications.index', compact('broadcasts'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'channel' => ['required', 'in:email,whatsapp,email_whatsapp'],
        ]);

        $students = User::query()->where('role', 'student')->whereNotNull('email')->get(['id', 'name', 'email']);
        $targetCount = 0;

        if (str_contains($data['channel'], 'email')) {
            foreach ($students as $student) {
                try {
                    Mail::raw($data['message'], function ($mail) use ($student, $data) {
                        $mail->to($student->email)->subject('[DSCMKids] '.$data['title']);
                    });
                    $targetCount++;
                } catch (\Throwable) {
                    // Keep broadcast flow alive even when one email fails.
                }
            }
        }

        if (str_contains($data['channel'], 'whatsapp')) {
            $webhook = trim((string) env('WHATSAPP_BROADCAST_WEBHOOK', ''));
            if ($webhook !== '') {
                try {
                    Http::timeout(8)->post($webhook, [
                        'title' => $data['title'],
                        'message' => $data['message'],
                        'audience' => 'students',
                    ]);
                } catch (\Throwable) {
                    // Optional channel; ignore webhook failure.
                }
            }
        }

        if (Schema::hasTable('notification_broadcasts')) {
            NotificationBroadcast::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'channel' => $data['channel'],
                'target_count' => $targetCount,
                'sent_at' => now(),
                'sent_by' => $request->user()?->id,
            ]);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Notifikasi broadcast berhasil diproses.');
    }
}

