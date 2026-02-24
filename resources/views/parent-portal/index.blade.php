<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section?->title ?: 'Portal Orang Tua DSCMKids' }}</title>
    <meta name="description" content="Portal orang tua DSCMKids untuk ringkasan progres dan fokus pembelajaran anak.">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f7ff; color: #0f172a; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 18px; }
        .card { background: #fff; border: 1px solid #dbe5f6; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .pill { display: inline-block; background: #eaf2ff; color: #1e40af; border-radius: 999px; padding: 6px 10px; font-weight: 700; }
        @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <p><a href="{{ route('landing') }}">&larr; Kembali ke Landing</a></p>
    <div class="card">
        <h1 style="margin-top:0;">{{ $section?->title ?: 'Portal Orang Tua DSCMKids' }}</h1>
        <p>{{ $section?->content ?: 'Ringkasan mingguan untuk orang tua.' }}</p>
        @if(!empty($section?->meta['cta_url']))
            <p><a href="{{ $section->meta['cta_url'] }}" target="_blank" rel="noopener">Buka Form/Resource Orang Tua</a></p>
        @endif
    </div>

    <div class="grid">
        <div class="card"><span class="pill">Murid Terdaftar</span><h2>{{ (int) $studentCount }}</h2></div>
        <div class="card"><span class="pill">Aktif Quiz Hari Ini</span><h2>{{ (int) $activeToday }}</h2></div>
    </div>

    <div class="card">
        <h3>Fokus Pendampingan Minggu Ini</h3>
        <ul>
            @forelse($highlights as $item)
                <li>{{ $item }}</li>
            @empty
                <li>Belum ada highlight. Atur di Admin &gt; Parent Portal.</li>
            @endforelse
        </ul>
    </div>

    <div class="card">
        <h3>Informasi Terbaru</h3>
        @forelse($announcements as $item)
            <div style="padding:8px 0;border-top:1px solid #eef2ff;">
                <strong>{{ $item->title }}</strong>
                <div style="font-size:.9rem;color:#475569;">{{ optional($item->event_date)->format('d M Y') ?: '-' }}</div>
                <div>{{ $item->body }}</div>
            </div>
        @empty
            <p>Belum ada informasi terbaru.</p>
        @endforelse
    </div>
</div>
</body>
</html>

