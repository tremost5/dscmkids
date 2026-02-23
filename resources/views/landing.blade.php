<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSCMKids | Sistem Informasi Sekolah Minggu Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #f3f9ff;
            --ink: #0e1729;
            --muted: #5f6b85;
            --card: #ffffff;
            --line: #dbe5f4;
            --brand-a: #0f766e;
            --brand-b: #0284c7;
            --accent-a: #f59e0b;
            --accent-b: #ef4444;
            --radius: 18px;
        }

        * { box-sizing: border-box; }

        html, body { margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 400px at -10% -5%, #99f6e4 0%, transparent 40%),
                radial-gradient(900px 400px at 110% 10%, #bfdbfe 0%, transparent 45%),
                var(--bg);
        }

        .container { width: min(1180px, 92vw); margin: 0 auto; }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 0 8px;
        }

        .brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #b7f3e4;
            background: #ecfeff;
            color: #0f766e;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 16px;
            padding: 26px 0 18px;
        }

        .hero-main {
            background: linear-gradient(130deg, #0f766e 0%, #0369a1 45%, #1d4ed8 100%);
            color: #fff;
            border-radius: 28px;
            padding: 34px;
            position: relative;
            overflow: hidden;
            min-height: 360px;
            box-shadow: 0 24px 54px rgba(3, 24, 58, 0.25);
        }

        .hero-main::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.28), rgba(255,255,255,0));
            top: -120px;
            right: -90px;
        }

        .hero-main h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1.06;
            margin: 0;
            max-width: 700px;
        }

        .hero-main p {
            margin: 18px 0 0;
            max-width: 680px;
            color: rgba(255,255,255,0.9);
            line-height: 1.6;
            font-size: 1.05rem;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .btn {
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-weight: 700;
            border-radius: 12px;
            padding: 11px 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-white { background: #fff; color: #0f172a; }
        .btn-soft { background: rgba(255,255,255,0.16); color: #fff; border: 1px solid rgba(255,255,255,0.28); }

        .mini-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .mini-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: 0 10px 30px rgba(2, 20, 47, 0.08);
        }

        .mini-title { margin: 0; font-weight: 700; font-size: 0.93rem; color: #334155; }
        .mini-value { margin: 8px 0 0; font-size: 2rem; font-weight: 800; }
        .mini-note { margin-top: 6px; color: var(--muted); font-size: 0.9rem; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-top: 14px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: 0 10px 30px rgba(2, 20, 47, 0.08);
            transform: translateY(18px);
            opacity: 0;
            animation: rise .8s ease forwards;
        }

        .stat-card:nth-child(2) { animation-delay: .1s; }
        .stat-card:nth-child(3) { animation-delay: .2s; }
        .stat-card:nth-child(4) { animation-delay: .3s; }

        .k-label { color: #64748b; font-size: 0.86rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .k-value { font-size: clamp(1.6rem, 3vw, 2.25rem); font-weight: 800; margin-top: 6px; }

        .section { margin-top: 18px; }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: 0 12px 34px rgba(8, 28, 59, 0.08);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1.25fr 0.75fr;
            gap: 14px;
        }

        .section-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.4rem;
            margin: 0 0 12px;
        }

        .muted { color: var(--muted); }

        .list {
            display: grid;
            gap: 10px;
        }

        .list-item {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            background: linear-gradient(120deg, #ffffff, #f8fbff);
        }

        .list-item h3 {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 800;
        }

        .list-item p { margin: 8px 0 0; color: #45556f; }
        .meta { color: #64748b; font-size: 0.82rem; margin-top: 6px; }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .photo {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            min-height: 210px;
            border: 1px solid #d7e2f1;
            background: linear-gradient(145deg, #dbeafe, #f0fdfa);
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.35s ease;
        }

        .photo:hover img { transform: scale(1.05); }

        .photo-caption {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            color: #fff;
            font-size: 0.82rem;
            padding: 9px 10px;
            background: linear-gradient(180deg, rgba(3,12,30,0), rgba(3,12,30,0.85));
        }

        .programs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .program {
            padding: 16px;
            border-radius: 16px;
            color: #fff;
            min-height: 152px;
        }

        .program h3 { margin: 0; font-family: 'Space Grotesk', sans-serif; }
        .program p { margin: 8px 0 0; color: rgba(255,255,255,0.9); }
        .p-a { background: linear-gradient(135deg, #0369a1, #2563eb); }
        .p-b { background: linear-gradient(135deg, #0f766e, #14b8a6); }
        .p-c { background: linear-gradient(135deg, #be123c, #ef4444); }

        .cta {
            margin: 18px 0 30px;
            border-radius: 22px;
            padding: 24px;
            background: linear-gradient(120deg, #111827, #1e293b 35%, #0f766e 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cta h2 { margin: 0; font-family: 'Space Grotesk', sans-serif; font-size: 1.65rem; }
        .cta p { margin: 8px 0 0; color: rgba(255,255,255,0.85); max-width: 700px; }

        .footer {
            text-align: center;
            font-size: 0.86rem;
            color: #64748b;
            padding: 6px 0 26px;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1080px) {
            .hero { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .programs { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .hero-main { padding: 22px; border-radius: 20px; }
            .stat-grid { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $metrics = $schoolData['metrics'] ?? [];
    $series = $schoolData['attendance_series'] ?? [];
    $source = $schoolData['source'] ?? 'local-fallback';
    $galleryItems = is_iterable($gallery) ? collect($gallery)->all() : [];
@endphp

<div class="container">
    <nav class="nav">
        <div class="brand">DSCMKids Digital Ministry</div>
        <div class="pill">Data Source: {{ strtoupper($source) }}</div>
    </nav>

    <section class="hero">
        <div class="hero-main">
            <h1>{{ $sections['hero']->title ?? 'Landing Page Premium Sekolah Minggu DSCMKids' }}</h1>
            <p>{{ $sections['hero']->content ?? 'Dashboard pelayanan anak yang menggabungkan informasi terkini, statistik siswa dan kehadiran real-time, serta dokumentasi kegiatan dari database terintegrasi.' }}</p>
            <div class="hero-actions">
                <a class="btn btn-white" href="#analytics">Lihat Analytics</a>
                <a class="btn btn-soft" href="#gallery">Eksplor Galeri</a>
                <a class="btn btn-soft" href="{{ route('admin.login') }}">Admin Login</a>
            </div>
        </div>

        <div class="mini-grid">
            <article class="mini-card">
                <h3 class="mini-title">Total Murid Aktif</h3>
                <div class="mini-value">{{ number_format((int) ($metrics['students_total'] ?? 0)) }}</div>
                <div class="mini-note">Terverifikasi dari data sekolah minggu</div>
            </article>
            <article class="mini-card">
                <h3 class="mini-title">Kehadiran Hari Ini</h3>
                <div class="mini-value">{{ number_format((int) ($metrics['attendance_today'] ?? 0)) }}</div>
                <div class="mini-note">Rate {{ number_format((float) ($metrics['attendance_rate'] ?? 0), 1) }}%</div>
            </article>
            <article class="mini-card">
                <h3 class="mini-title">Rata-Rata Kehadiran</h3>
                <div class="mini-value">{{ number_format((float) ($metrics['weekly_average'] ?? 0), 1) }}%</div>
                <div class="mini-note">Performa 14 hari terakhir</div>
            </article>
        </div>
    </section>

    <section class="stat-grid" id="analytics">
        <article class="stat-card">
            <div class="k-label">Jumlah Siswa</div>
            <div class="k-value">{{ number_format((int) ($metrics['students_total'] ?? 0)) }}</div>
        </article>
        <article class="stat-card">
            <div class="k-label">Hadir Hari Ini</div>
            <div class="k-value">{{ number_format((int) ($metrics['attendance_today'] ?? 0)) }}</div>
        </article>
        <article class="stat-card">
            <div class="k-label">Rate Kehadiran</div>
            <div class="k-value">{{ number_format((float) ($metrics['attendance_rate'] ?? 0), 1) }}%</div>
        </article>
        <article class="stat-card">
            <div class="k-label">Kelas Aktif</div>
            <div class="k-value">{{ number_format((int) ($metrics['active_classes'] ?? 0)) }}</div>
        </article>
    </section>

    <section class="section grid-2">
        <article class="panel">
            <h2 class="section-title">Grafik Kehadiran Berwarna</h2>
            <p class="muted" style="margin-top:0;">Visual trend kehadiran siswa 14 hari terakhir.</p>
            <canvas id="attendanceChart" height="120"></canvas>
        </article>
        <article class="panel">
            <h2 class="section-title">Informasi & Jadwal</h2>
            <div class="list">
                @forelse($announcements as $item)
                    <div class="list-item">
                        <h3>{{ $item->title }}</h3>
                        <div class="meta">{{ optional($item->event_date)->format('d M Y') ?? 'Tanggal menyusul' }} {{ $item->location ? '- '.$item->location : '' }}</div>
                        <p>{{ $item->body }}</p>
                    </div>
                @empty
                    <div class="list-item">Belum ada pengumuman aktif.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="section panel">
        <h2 class="section-title">Berita Terbaru</h2>
        <div class="list">
            @forelse($news as $item)
                <div class="list-item">
                    <h3>{{ $item->title }}</h3>
                    <div class="meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                    <p>{{ $item->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($item->body), 180) }}</p>
                </div>
            @empty
                <div class="list-item">Belum ada berita tersedia.</div>
            @endforelse
        </div>
    </section>

    <section class="section panel" id="gallery">
        <h2 class="section-title">Foto Kegiatan</h2>
        <p class="muted" style="margin-top:0;">Dokumentasi pelayanan yang ditarik dari database terintegrasi.</p>
        <div class="gallery-grid">
            @forelse($galleryItems as $photo)
                @php
                    $title = is_array($photo) ? ($photo['title'] ?? 'Kegiatan DSCMKids') : $photo->title;
                    $date = is_array($photo) ? ($photo['date'] ?? null) : optional($photo->created_at)->format('d M Y');
                    $pathValue = is_array($photo) ? ($photo['path'] ?? null) : asset('storage/'.$photo->file_path);
                    $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://'))
                        ? $pathValue
                        : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
                @endphp
                <figure class="photo">
                    @if($src)
                        <img src="{{ $src }}" alt="{{ $title }}">
                    @endif
                    <figcaption class="photo-caption">
                        <strong>{{ $title }}</strong><br>
                        {{ $date ?? 'DSCMKids Event' }}
                    </figcaption>
                </figure>
            @empty
                <figure class="photo">
                    <figcaption class="photo-caption"><strong>Belum ada foto kegiatan</strong></figcaption>
                </figure>
            @endforelse
        </div>
    </section>

    <section class="section programs">
        <article class="program p-a">
            <h3>Kid Worship</h3>
            <p>Ibadah interaktif berbasis kreativitas, lagu, dan pembelajaran alkitabiah kontekstual.</p>
        </article>
        <article class="program p-b">
            <h3>Family Engagement</h3>
            <p>Kolaborasi orang tua dan guru untuk pendampingan rohani anak yang berkelanjutan.</p>
        </article>
        <article class="program p-c">
            <h3>Growth Tracking</h3>
            <p>Monitoring perkembangan, kehadiran, dan keterlibatan siswa dengan data visual real-time.</p>
        </article>
    </section>

    <section class="cta">
        <div>
            <h2>{{ $sections['cta']->title ?? 'Ayo Terhubung Dengan DSCMKids' }}</h2>
            <p>{{ $sections['cta']->content ?? 'Landing page ini dirancang sebagai pusat informasi sekolah minggu modern yang menarik, informatif, dan siap ditingkatkan ke skala besar.' }}</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.login') }}" class="btn btn-white">Kelola Konten</a>
            <a href="#analytics" class="btn btn-soft">Lihat Data</a>
        </div>
    </section>

    <footer class="footer">&copy; {{ date('Y') }} DSCMKids. Crafted for premium digital ministry experience.</footer>
</div>

<script>
    (function () {
        const labels = @json(array_column($series, 'label'));
        const values = @json(array_map(fn ($item) => (float) ($item['value'] ?? 0), $series));

        const canvas = document.getElementById('attendanceChart');
        if (!canvas || !labels.length) {
            return;
        }

        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(14, 165, 233, 0.40)');
        gradient.addColorStop(0.55, 'rgba(16, 185, 129, 0.20)');
        gradient.addColorStop(1, 'rgba(239, 68, 68, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Persentase Kehadiran',
                    data: values,
                    borderWidth: 3,
                    borderColor: '#0284c7',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#0f766e',
                    pointBorderColor: '#ffffff',
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y.toFixed(1) + '% hadir';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: 100,
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                        ticks: {
                            callback: function (v) {
                                return v + '%';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    })();
</script>
</body>
</html>
