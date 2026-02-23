<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSCMKids | Sekolah Minggu Modern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --ink:#11213f; --muted:#5b6987; --bg:#f4f9ff; --card:#fff; --line:#d9e5f6; --radius:20px; }
        * { box-sizing:border-box; }
        html, body { margin:0; }
        body { font-family:'Nunito',sans-serif; color:var(--ink); background:radial-gradient(circle at 0% 0%, #dbeafe, transparent 35%),radial-gradient(circle at 100% 10%, #cffafe, transparent 35%),var(--bg); }
        .container { width:min(1160px,92vw); margin:0 auto; }

        .top { position:relative; height:min(68vh,580px); border-radius:0 0 30px 30px; overflow:hidden; box-shadow:0 22px 44px rgba(13,27,52,.25); }
        .slide { position:absolute; inset:0; opacity:0; transform:scale(1.06); transition:opacity 1.4s ease, transform 6s ease; will-change:opacity,transform; }
        .slide.active { opacity:1; transform:scale(1); }
        .slide img { width:100%; height:100%; object-fit:cover; }
        .slide.active img { animation:kenBurns 8s ease forwards; }
        @keyframes kenBurns { from { transform:scale(1.05) translateX(0); } to { transform:scale(1.14) translateX(-1.8%); } }

        .overlay { position:absolute; inset:0; background:linear-gradient(115deg, rgba(4,9,22,.76), rgba(4,9,22,.42) 48%, rgba(4,9,22,.2)); display:flex; align-items:center; }
        .hero-content { width:min(1160px,92vw); margin:0 auto; color:#fff; transform:translateY(8px); opacity:0; transition:all .65s ease; }
        .hero-content.enter { transform:translateY(0); opacity:1; }
        .badge { display:inline-block; padding:6px 12px; border-radius:999px; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.35); font-weight:800; font-size:.78rem; }
        .hero-title { font-family:'Baloo 2',sans-serif; font-size:clamp(2rem,4.1vw,3.9rem); line-height:1.04; margin:14px 0 0; max-width:760px; }
        .hero-sub { margin-top:10px; max-width:720px; color:rgba(255,255,255,.92); font-size:1.05rem; }
        .hero-actions { margin-top:20px; display:flex; gap:10px; flex-wrap:wrap; }
        .btn { border:0; text-decoration:none; padding:11px 15px; border-radius:12px; font-weight:800; display:inline-flex; align-items:center; cursor:pointer; }
        .btn-light { background:#fff; color:#0f172a; }
        .btn-ghost { background:rgba(255,255,255,.14); color:#fff; border:1px solid rgba(255,255,255,.35); }

        .hero-nav { position:absolute; left:50%; transform:translateX(-50%); bottom:14px; display:flex; gap:8px; z-index:3; }
        .dot { width:34px; height:4px; border-radius:999px; background:rgba(255,255,255,.34); overflow:hidden; }
        .dot span { display:block; width:0; height:100%; background:#fff; }
        .dot.active span { animation:fillLine 4.6s linear forwards; }
        @keyframes fillLine { from { width:0; } to { width:100%; } }

        .stats { margin-top:-44px; position:relative; z-index:2; display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
        .stat { background:var(--card); border:1px solid var(--line); border-radius:var(--radius); padding:16px; box-shadow:0 12px 30px rgba(13,29,58,.09); }
        .s-label { color:var(--muted); font-weight:700; font-size:.83rem; text-transform:uppercase; }
        .s-value { font-size:clamp(1.45rem,3vw,2.2rem); font-weight:900; margin-top:4px; }

        .section { margin-top:16px; }
        .panel { background:var(--card); border:1px solid var(--line); border-radius:var(--radius); padding:18px; box-shadow:0 12px 30px rgba(13,29,58,.07); }
        .title { margin:0 0 10px; font-family:'Baloo 2',sans-serif; font-size:1.7rem; line-height:1.1; }
        .muted { color:var(--muted); }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .theater {
            margin-top: 16px;
            border-radius: 24px;
            border: 1px solid #1f355b;
            padding: 18px;
            background:
                radial-gradient(circle at 12% 0%, rgba(59,130,246,.2), transparent 40%),
                radial-gradient(circle at 88% 10%, rgba(20,184,166,.18), transparent 35%),
                linear-gradient(145deg, #020617, #111827 40%, #1e293b 100%);
            color: #f8fafc;
            box-shadow: 0 20px 42px rgba(2, 6, 23, .45);
        }
        .theater-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
        .live-badge {
            background:#ef4444;
            color:#fff;
            border-radius:999px;
            padding:6px 10px;
            font-size:.75rem;
            font-weight:900;
            letter-spacing:.3px;
            animation: pulseLive 1.4s ease-in-out infinite;
        }
        @keyframes pulseLive { 0% { opacity:1; } 50% { opacity:.55; } 100% { opacity:1; } }
        .theater p { color: rgba(248,250,252,.84); margin-top:6px; }
        .theater-screen {
            margin-top: 12px;
            border-radius: 18px;
            border: 1px solid rgba(148,163,184,.3);
            overflow: hidden;
            background: #020617;
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.05), 0 18px 36px rgba(0,0,0,.45);
        }
        .theater-screen::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 28px;
            background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,0));
            pointer-events: none;
            z-index: 2;
        }
        .theater-iframe { display:block; width:100%; aspect-ratio:16/9; border:0; }
        .theater-empty {
            width:100%;
            aspect-ratio:16/9;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            padding:16px;
            color:#cbd5e1;
            background: radial-gradient(circle at center, #1e293b, #020617);
            font-weight:700;
        }

        .circle-wrap { display:grid; grid-template-columns:.9fr 1.1fr; gap:12px; align-items:center; }
        .donut-box { position:relative; min-height:270px; }
        .donut-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; }
        .big-roll { font-size:clamp(2rem,4vw,2.8rem); font-weight:900; color:#0f172a; line-height:1; }
        .small-roll { color:var(--muted); font-weight:700; margin-top:6px; }

        .class-list { display:grid; gap:8px; }
        .class-row { border:1px solid #e3ebf8; border-radius:12px; padding:9px 11px; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(120deg,#fff,#f9fbff); }
        .chip { width:11px; height:11px; border-radius:999px; display:inline-block; margin-right:7px; }

        .cards { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
        .info-card { border-radius:16px; color:#fff; padding:16px; min-height:150px; }
        .info-card h3 { margin:0; font-family:'Baloo 2',sans-serif; font-size:1.25rem; }
        .info-card p { margin:8px 0 0; color:rgba(255,255,255,.92); }
        .c1 { background:linear-gradient(140deg,#0ea5e9,#2563eb); }
        .c2 { background:linear-gradient(140deg,#0d9488,#14b8a6); }
        .c3 { background:linear-gradient(140deg,#f97316,#ef4444); }

        .news-list, .ann-list { display:grid; gap:10px; }
        .item { border:1px solid #e1e9f8; border-radius:14px; padding:12px; background:linear-gradient(120deg,#fff,#f8fbff); }
        .item h3 { margin:0; font-size:1.05rem; }
        .item p { margin:7px 0 0; color:#3f4e6a; }
        .meta { color:#677791; font-size:.83rem; margin-top:6px; }

        .teacher-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
        .teacher { border:1px solid #dde7f8; border-radius:16px; padding:12px; background:#fff; }
        .teacher img { width:100%; height:170px; object-fit:cover; border-radius:12px; border:1px solid #d7e4f6; }
        .teacher h4 { margin:10px 0 0; font-size:1rem; }
        .teacher p { margin:4px 0 0; color:#52617d; font-size:.9rem; }

        .filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
        .filter-pill { border:1px solid #d1def4; background:#fff; color:#334155; font-weight:700; border-radius:999px; padding:8px 12px; text-decoration:none; }
        .filter-pill.active { background:#1d4ed8; border-color:#1d4ed8; color:#fff; }

        .gallery-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
        .photo { position:relative; overflow:hidden; border-radius:15px; min-height:210px; border:1px solid #d8e4f6; }
        .photo img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .35s ease; cursor:zoom-in; }
        .photo:hover img { transform:scale(1.05); }
        .caption { position:absolute; left:0; right:0; bottom:0; padding:9px 10px; color:#fff; background:linear-gradient(180deg,rgba(0,0,0,.02),rgba(0,0,0,.78)); font-size:.8rem; }
        .caption a { color:#fff; font-weight:800; text-decoration:none; display:inline-block; margin-top:4px; border-bottom:1px solid rgba(255,255,255,.7); }

        .lightbox {
            position: fixed;
            inset: 0;
            background: rgba(7, 13, 30, 0.92);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99;
            padding: 20px;
        }
        .lightbox.open { display:flex; }
        .lightbox img {
            max-width: min(1100px, 92vw);
            max-height: 78vh;
            object-fit: contain;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 20px 38px rgba(0,0,0,.45);
        }
        .lightbox-meta {
            margin-top: 10px;
            color: #dce7ff;
            text-align: center;
            font-weight: 700;
        }
        .lightbox-close {
            position: absolute;
            top: 16px;
            right: 18px;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.4);
            background: rgba(255,255,255,.12);
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.4);
            background: rgba(255,255,255,.12);
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
        }
        .lightbox-prev { left: 16px; }
        .lightbox-next { right: 16px; }

        .footer { text-align:center; color:#6b7a92; padding:20px 0 30px; font-size:.85rem; }

        @media (max-width:980px) {
            .stats { grid-template-columns:repeat(2,1fr); }
            .grid-2 { grid-template-columns:1fr; }
            .cards { grid-template-columns:1fr; }
            .teacher-grid, .gallery-grid { grid-template-columns:repeat(2,1fr); }
            .circle-wrap { grid-template-columns:1fr; }
        }
        @media (max-width:640px) {
            .top { height:72vh; border-radius:0 0 18px 18px; }
            .teacher-grid, .gallery-grid, .stats { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
@php
    $metrics = $schoolData['metrics'] ?? [];
    $classAttendance = $schoolData['attendance_by_class'] ?? [];
    $attendanceTotals = $schoolData['attendance_totals'] ?? ['present' => 0, 'absent' => 0];

    $slidesData = $slides->count() > 0
        ? $slides->map(fn ($item) => [
            'title' => $item->title,
            'subtitle' => $item->subtitle,
            'image' => asset('storage/'.$item->image_path),
            'button_text' => $item->button_text,
            'button_url' => $item->button_url,
        ])->values()->all()
        : [[
            'title' => $sections['hero']->title ?? 'System Informasi Sekolah Minggu DSCMKids',
            'subtitle' => $sections['hero']->content ?? 'Platform digital untuk siswa dan orang tua.',
            'image' => 'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?q=80&w=1600&auto=format&fit=crop',
            'button_text' => 'Info Kelas',
            'button_url' => '#informasi',
        ]];

    $galleryItems = is_iterable($gallery) ? collect($gallery)->all() : [];
@endphp

<header class="top" id="home">
    @foreach($slidesData as $i => $slide)
        <div class="slide {{ $i === 0 ? 'active' : '' }}" data-slide>
            <img src="{{ $slide['image'] }}" alt="slide {{ $i + 1 }}">
        </div>
    @endforeach
    <div class="overlay">
        <div class="hero-content enter" id="heroContent">
            <span class="badge">Sekolah Minggu DSCMKids</span>
            <h1 class="hero-title" id="heroTitle">{{ $slidesData[0]['title'] ?? ($sections['hero']->title ?? 'Selamat Datang') }}</h1>
            <p class="hero-sub" id="heroSubtitle">{{ $slidesData[0]['subtitle'] ?? ($sections['hero']->content ?? '') }}</p>
            <div class="hero-actions">
                <a href="#analytics" class="btn btn-light">Lihat Kehadiran</a>
                <a href="#teachers" class="btn btn-ghost">Portfolio Guru</a>
                <a href="#gallery" class="btn btn-ghost">Galeri</a>
                <a href="{{ route('news.index') }}" class="btn btn-ghost">Semua Berita</a>
                <a href="{{ route('admin.login') }}" class="btn btn-ghost">Admin</a>
            </div>
        </div>
    </div>
    <div class="hero-nav" id="heroNav">
        @foreach($slidesData as $i => $slide)
            <div class="dot {{ $i === 0 ? 'active' : '' }}" data-dot="{{ $i }}"><span></span></div>
        @endforeach
    </div>
</header>

<main class="container">
    <section class="stats">
        <article class="stat"><div class="s-label">Jumlah Siswa</div><div class="s-value">{{ number_format((int) ($metrics['students_total'] ?? 0)) }}</div></article>
        <article class="stat"><div class="s-label">Hadir Hari Ini</div><div class="s-value" id="rolledPresent">{{ number_format((int) ($metrics['attendance_today'] ?? 0)) }}</div></article>
        <article class="stat"><div class="s-label">Persentase Hadir</div><div class="s-value">{{ number_format((float) ($metrics['attendance_rate'] ?? 0), 1) }}%</div></article>
        <article class="stat"><div class="s-label">Kelas Aktif</div><div class="s-value">{{ number_format((int) ($metrics['active_classes'] ?? 0)) }}</div></article>
    </section>

    <section class="theater" id="live">
        <div class="theater-head">
            <div>
                <h2 class="title" style="color:#fff;margin-bottom:0;">{{ $liveStream['title'] ?? 'Live Streaming Ibadah Anak' }}</h2>
                <p>{{ $liveStream['description'] ?? 'Saksikan siaran langsung DSCMKids.' }}</p>
            </div>
            <span class="live-badge">LIVE STREAM</span>
        </div>
        <div class="theater-screen">
            @if(!empty($liveStream['embed_url']))
                <iframe class="theater-iframe" src="{{ $liveStream['embed_url'] }}" title="Live Streaming DSCMKids" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            @else
                <div class="theater-empty">Link YouTube live belum diatur.<br>Isi di Admin &gt; Konten (`section_key: livestream`, `meta.youtube_url`).</div>
            @endif
        </div>
    </section>

    <section class="section panel" id="analytics">
        <h2 class="title">Kehadiran Hari Ini (PG, TKA, TKB, 1-6)</h2>
        <p class="muted">Grafik bulat menampilkan komposisi kehadiran per kelas, terhubung ke database presensi.</p>
        <div class="circle-wrap">
            <div class="donut-box">
                <canvas id="attendanceDonut" height="260"></canvas>
                <div class="donut-center">
                    <div class="big-roll" id="centerRoll">0</div>
                    <div class="small-roll">Murid Hadir</div>
                </div>
            </div>
            <div class="class-list">
                @foreach($classAttendance as $index => $entry)
                    <div class="class-row">
                        <div><span class="chip" data-chip="{{ $index }}"></span>{{ $entry['class'] }}</div>
                        <strong>{{ $entry['present'] }} murid</strong>
                    </div>
                @endforeach
                <div class="class-row" style="background:#f8fafc;">
                    <div>Total Tidak Hadir</div>
                    <strong>{{ $attendanceTotals['absent'] ?? 0 }} murid</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="section grid-2" id="informasi">
        <article class="panel">
            <h2 class="title">Konten Umum Informatif & Edukatif</h2>
            <div class="cards">
                <div class="info-card c1"><h3>Kelas Kreatif Alkitab</h3><p>Anak belajar firman Tuhan lewat aktivitas visual, musik, dan permainan edukatif.</p></div>
                <div class="info-card c2"><h3>Parent Insight</h3><p>Ringkasan perkembangan rohani anak dan komunikasi rutin untuk orang tua.</p></div>
                <div class="info-card c3"><h3>Growth Journey</h3><p>Pemantauan keterlibatan, kehadiran, dan partisipasi per kelas secara berkala.</p></div>
            </div>
        </article>
        <article class="panel">
            <h2 class="title">Informasi Pelayanan</h2>
            <div class="ann-list">
                @forelse($announcements as $item)
                    <div class="item">
                        <h3>{{ $item->title }}</h3>
                        <div class="meta">{{ optional($item->event_date)->format('d M Y') ?? 'Tanggal menyusul' }} {{ $item->location ? '- '.$item->location : '' }}</div>
                        <p>{{ $item->body }}</p>
                    </div>
                @empty
                    <div class="item">Belum ada informasi.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="section panel" id="teachers">
        <h2 class="title">Portfolio Singkat Guru</h2>
        <p class="muted">Profil guru diinput dari admin panel.</p>
        <div class="teacher-grid">
            @forelse($teachers as $teacher)
                <article class="teacher">
                    @if($teacher->photo_path)
                        <img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="{{ $teacher->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1545239351-1141bd82e8a6?q=80&w=900&auto=format&fit=crop" alt="{{ $teacher->name }}">
                    @endif
                    <h4>{{ $teacher->name }}</h4>
                    <p>{{ $teacher->role ?? 'Pengajar Sekolah Minggu' }}</p>
                    <p><strong>Kelas:</strong> {{ $teacher->class_group ?? '-' }}</p>
                    @if($teacher->bio)
                        <p>{{ \Illuminate\Support\Str::limit($teacher->bio, 88) }}</p>
                    @endif
                </article>
            @empty
                <article class="teacher"><h4>Belum ada data guru</h4><p>Tambah dari admin panel.</p></article>
            @endforelse
        </div>
    </section>

    <section class="section panel">
        <h2 class="title">Berita Terbaru</h2>
        <div class="news-list">
            @forelse($news as $item)
                <article class="item">
                    <h3><a href="{{ route('news.show', $item->slug) }}" style="text-decoration:none;color:inherit;">{{ $item->title }}</a></h3>
                    <div class="meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                    <p>{{ $item->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($item->body), 160) }}</p>
                    <a href="{{ route('news.show', $item->slug) }}" style="display:inline-block;margin-top:8px;font-weight:800;color:#1d4ed8;text-decoration:none;">Baca Selengkapnya</a>
                </article>
            @empty
                <article class="item">Belum ada berita.</article>
            @endforelse
        </div>
    </section>

    <section class="section panel" id="gallery">
        <h2 class="title">Galeri Kegiatan</h2>
        <div class="filter-bar">
            <a href="{{ route('landing') }}#gallery" class="filter-pill {{ empty($activeEvent) ? 'active' : '' }}">Semua Event</a>
            @foreach($galleryEvents as $event)
                <a href="{{ route('landing', ['event' => $event]) }}#gallery" class="filter-pill {{ $activeEvent === $event ? 'active' : '' }}">{{ $event }}</a>
            @endforeach
        </div>
        <div class="gallery-grid">
            @forelse($galleryItems as $photo)
                @php
                    $title = is_array($photo) ? ($photo['title'] ?? 'Kegiatan DSCMKids') : $photo->title;
                    $date = is_array($photo) ? ($photo['date'] ?? null) : optional($photo->created_at)->format('d M Y');
                    $eventName = is_array($photo) ? ($photo['event_name'] ?? 'Kegiatan Umum') : 'Kegiatan Umum';
                    $eventSlug = is_array($photo) ? ($photo['event_slug'] ?? \Illuminate\Support\Str::slug($eventName)) : \Illuminate\Support\Str::slug($eventName);
                    $pathValue = is_array($photo) ? ($photo['path'] ?? null) : asset('storage/'.$photo->file_path);
                    $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://')) ? $pathValue : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
                @endphp
                <figure class="photo">
                    @if($src)
                        <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventName }}{{ $date ? ' - '.$date : '' }}" data-lightbox-index="{{ $loop->index }}">
                    @endif
                    <figcaption class="caption"><strong>{{ $title }}</strong><br>{{ $eventName }}{{ $date ? ' - '.$date : '' }}<br><a href="{{ route('gallery.event', ['eventSlug' => $eventSlug]) }}">Detail Event</a></figcaption>
                </figure>
            @empty
                <figure class="photo"><figcaption class="caption"><strong>Tidak ada foto untuk event ini</strong></figcaption></figure>
            @endforelse
        </div>
    </section>

    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" id="lightboxClose" aria-label="Tutup">&times;</button>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Sebelumnya">&#10094;</button>
        <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Berikutnya">&#10095;</button>
        <div>
            <img id="lightboxImage" src="" alt="Preview">
            <div class="lightbox-meta" id="lightboxMeta"></div>
        </div>
    </div>

    <footer class="footer">&copy; {{ date('Y') }} DSCMKids - Dirancang untuk anak sekolah minggu dan orang tua murid.</footer>
</main>

<script>
(function () {
    const slides = @json($slidesData);
    const slideEls = Array.from(document.querySelectorAll('[data-slide]'));
    const dots = Array.from(document.querySelectorAll('[data-dot]'));
    const titleEl = document.getElementById('heroTitle');
    const subtitleEl = document.getElementById('heroSubtitle');
    const heroContent = document.getElementById('heroContent');

    const slideDuration = 4600;
    let index = 0;

    function animateText(newTitle, newSubtitle) {
        heroContent.classList.remove('enter');
        setTimeout(() => {
            titleEl.textContent = newTitle || 'Sekolah Minggu DSCMKids';
            subtitleEl.textContent = newSubtitle || '';
            heroContent.classList.add('enter');
        }, 220);
    }

    function goToSlide(nextIndex) {
        if (nextIndex === index) {
            return;
        }

        slideEls[index].classList.remove('active');
        dots[index]?.classList.remove('active');

        index = nextIndex;

        slideEls[index].classList.add('active');
        dots[index]?.classList.add('active');
        animateText(slides[index].title, slides[index].subtitle);
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            goToSlide(i);
            resetAutoRotate();
        });
    });

    let timer;
    function startAutoRotate() {
        if (slideEls.length <= 1) {
            return;
        }

        timer = setInterval(() => {
            goToSlide((index + 1) % slideEls.length);
        }, slideDuration);
    }

    function resetAutoRotate() {
        clearInterval(timer);
        startAutoRotate();
    }

    startAutoRotate();

    const classData = @json($classAttendance);
    const presentTotal = Number(@json((int) ($attendanceTotals['present'] ?? 0)));
    const absentTotal = Number(@json((int) ($attendanceTotals['absent'] ?? 0)));

    const labels = classData.map((x) => x.class).concat(['Tidak Hadir']);
    const values = classData.map((x) => Number(x.present)).concat([absentTotal]);
    const colors = ['#2563eb','#0ea5e9','#0d9488','#14b8a6','#f59e0b','#f97316','#ec4899','#a855f7','#6366f1','#94a3b8'];

    document.querySelectorAll('[data-chip]').forEach((el) => {
        const i = Number(el.getAttribute('data-chip'));
        el.style.background = colors[i % colors.length];
    });

    const donutCanvas = document.getElementById('attendanceDonut');
    if (donutCanvas && labels.length > 0) {
        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: labels.map((_, i) => colors[i % colors.length]), borderWidth: 0, hoverOffset: 8 }],
            },
            options: { responsive: true, cutout: '68%', plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#e2e8f0' } } }
        });
    }

    const rollEl = document.getElementById('centerRoll');
    let current = 0;
    const steps = 36;
    const increment = presentTotal / steps;
    let tick = 0;
    const timerRoll = setInterval(() => {
        tick += 1;
        current += increment;
        rollEl.textContent = Math.round(current).toLocaleString('id-ID');
        if (tick >= steps) {
            rollEl.textContent = presentTotal.toLocaleString('id-ID');
            clearInterval(timerRoll);
        }
    }, 32);

    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxMeta = document.getElementById('lightboxMeta');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxItems = Array.from(document.querySelectorAll('[data-lightbox-src]'));
    let activeLightboxIndex = -1;

    function renderLightbox(index) {
        if (!lightboxItems.length) {
            return;
        }

        const safeIndex = (index + lightboxItems.length) % lightboxItems.length;
        const item = lightboxItems[safeIndex];

        activeLightboxIndex = safeIndex;
        lightboxImage.src = item.getAttribute('data-lightbox-src') || '';
        lightboxImage.alt = item.getAttribute('data-lightbox-title') || 'Gallery';
        lightboxMeta.textContent = (item.getAttribute('data-lightbox-title') || '') + ' | ' + (item.getAttribute('data-lightbox-meta') || '');
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        lightboxImage.src = '';
        lightboxMeta.textContent = '';
        document.body.style.overflow = '';
        activeLightboxIndex = -1;
    }

    lightboxItems.forEach((img, i) => {
        img.addEventListener('click', () => {
            renderLightbox(i);
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function showNext(step) {
        if (!lightbox.classList.contains('open')) {
            return;
        }

        renderLightbox(activeLightboxIndex + step);
    }

    lightboxClose.addEventListener('click', closeLightbox);
    lightboxPrev.addEventListener('click', () => showNext(-1));
    lightboxNext.addEventListener('click', () => showNext(1));
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) {
            closeLightbox();
            return;
        }

        if (event.key === 'ArrowLeft') {
            showNext(-1);
        }

        if (event.key === 'ArrowRight') {
            showNext(1);
        }
    });
})();
</script>
</body>
</html>
