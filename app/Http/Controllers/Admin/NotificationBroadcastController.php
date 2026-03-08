<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessNotificationBroadcast;
use App\Models\NotificationBroadcast;
use App\Models\User;
use Illuminate\Http\Request;
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

        if (!Schema::hasTable('notification_broadcasts')) {
            return redirect()
                ->route('admin.notifications.index')
                ->withErrors(['message' => 'Tabel broadcast belum tersedia. Jalankan migrasi terlebih dulu.']);
        }

        $targetCount = str_contains($data['channel'], 'email')
            ? User::query()->where('role', 'student')->whereNotNull('email')->count()
            : User::query()->where('role', 'student')->count();

        $broadcast = NotificationBroadcast::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'channel' => $data['channel'],
            'status' => 'pending',
            'target_count' => $targetCount,
            'processed_count' => 0,
            'failed_count' => 0,
            'sent_by' => $request->user()?->id,
        ]);

        ProcessNotificationBroadcast::dispatch($broadcast->id);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Broadcast dijadwalkan. Proses pengiriman berjalan di background queue.');
    }
}
