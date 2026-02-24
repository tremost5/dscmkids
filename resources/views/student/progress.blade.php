<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Murid | DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f8ff; color: #0f172a; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 18px; }
        .card { background: #fff; border: 1px solid #dbe5f6; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .title { margin: 0 0 8px; }
        @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <p><a href="{{ route('landing') }}">&larr; Kembali ke Landing</a></p>
    <h1>Progress Murid: {{ $user->name }}</h1>
    <div class="grid">
        <div class="card"><strong>{{ (int) $user->points }}</strong><br>Total Poin</div>
        <div class="card"><strong>{{ (int) $user->streak_days }}</strong><br>Streak Hari</div>
        <div class="card"><strong>{{ (int) $weeklyQuizDays }}/7</strong><br>Hari Quiz Minggu Ini</div>
        <div class="card"><strong>{{ (int) $weeklyQuizScore }}</strong><br>Skor Quiz Mingguan</div>
        <div class="card"><strong>{{ (int) $weeklyArcadeScore }}</strong><br>Skor Arcade Mingguan</div>
        <div class="card"><strong>{{ $user->class_group ?: '-' }}</strong><br>Kelas</div>
    </div>

    <div class="card">
        <h3 class="title">Riwayat Quiz Terbaru</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr><th style="text-align:left;">Tanggal</th><th style="text-align:left;">Skor</th><th style="text-align:left;">Badge</th></tr></thead>
            <tbody>
            @forelse($recentQuiz as $row)
                <tr>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $row->quiz_date }}</td>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $row->score }}</td>
                    <td style="padding:6px 0;border-top:1px solid #eef2ff;">{{ $row->badge_awarded }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="padding:8px 0;">Belum ada riwayat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

