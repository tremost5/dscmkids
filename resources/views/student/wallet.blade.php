<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievement Wallet | DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f8ff; color: #0f172a; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 18px; }
        .card { background: #fff; border: 1px solid #dbe5f6; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .badge { border: 1px solid #dbeafe; border-radius: 12px; padding: 10px; background: #f8fbff; }
        .badge.locked { opacity: .5; }
        @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <p><a href="{{ route('landing') }}">&larr; Kembali ke Landing</a> | <a href="{{ route('student.progress') }}">Progress</a></p>
    <h1>Achievement Wallet: {{ $user->name }}</h1>
    <div class="card">
        <strong>Total Poin:</strong> {{ (int) $user->points }}<br>
        <strong>Skor Minggu Ini:</strong> {{ (int) $weeklyScore }}
    </div>

    <div class="card">
        <h3>Badge Mingguan</h3>
        <div class="grid">
            @foreach($badges as $badge)
                <div class="badge {{ $badge['unlocked'] ? '' : 'locked' }}">
                    <strong>{{ $badge['label'] }}</strong><br>
                    Min skor: {{ $badge['min_score'] }}<br>
                    Status: {{ $badge['unlocked'] ? 'Unlocked' : 'Locked' }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h3>Riwayat Klaim Reward</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr><th style="text-align:left;">Minggu Mulai</th><th style="text-align:left;">Reward</th><th style="text-align:left;">Poin</th></tr></thead>
            <tbody>
            @forelse($claims as $item)
                <tr>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $item->week_start_date }}</td>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $item->reward_label }}</td>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $item->reward_points }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="padding:8px 0;">Belum ada reward yang diklaim.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

