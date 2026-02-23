<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSCMKids | Sistem Informasi Sekolah Minggu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6fbff;
            --ink: #0b1324;
            --muted: #4f5d75;
            --brand: #0f766e;
            --brand2: #0284c7;
            --card: #ffffff;
            --line: #dbeafe;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 0%, #ccfbf1, transparent 35%),
                radial-gradient(circle at 100% 30%, #bfdbfe, transparent 40%),
                var(--bg);
        }
        .container { width: min(1120px, 92vw); margin: 0 auto; }
        .hero {
            padding: 72px 0 48px;
            position: relative;
            overflow: hidden;
        }
        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: 1.08;
            margin: 0;
            max-width: 840px;
        }
        .hero p {
            margin-top: 16px;
            color: var(--muted);
            font-size: 1.08rem;
            max-width: 740px;
        }
        .cta-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
        .btn {
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 12px;
            font-weight: 700;
        }
        .btn-main { background: linear-gradient(120deg, var(--brand), var(--brand2)); color: #fff; }
        .btn-alt { background: #e2e8f0; color: #0f172a; }
        .section { margin: 22px 0; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 16px 32px rgba(9, 24, 46, 0.06);
        }
        h2 {
            font-family: 'Sora', sans-serif;
            margin-top: 0;
            font-size: 1.5rem;
        }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .meta { color: #64748b; font-size: 14px; }
        .news-title { margin: 0 0 8px; font-size: 1.1rem; }
        .news-body { color: #334155; margin: 0; }
        .gallery img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
        }
        .footer { text-align: center; color: #64748b; padding: 26px 0 44px; }
        .fade { animation: fadeUp 0.9s ease both; }
        .fade.d2 { animation-delay: 0.15s; }
        .fade.d3 { animation-delay: 0.3s; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(22px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 900px) {
            .grid-3 { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .hero { padding-top: 54px; }
        }
    </style>
</head>
<body>
<section class="hero">
    <div class="container fade">
        <h1>{{ $sections['hero']->title ?? 'System Informasi Sekolah Minggu DSCMKids' }}</h1>
        <p>{{ $sections['hero']->content ?? 'Platform informasi terpadu untuk berita, jadwal pelayanan, galeri kegiatan, dan komunikasi orang tua.' }}</p>
        <div class="cta-row">
            <a href="#berita" class="btn btn-main">Lihat Berita</a>
            <a href="{{ route('admin.login') }}" class="btn btn-alt">Login Admin</a>
        </div>
    </div>
</section>

<section class="section container fade d2">
    <div class="card">
        <h2>{{ $sections['about']->title ?? 'Tentang DSCMKids' }}</h2>
        <p>{{ $sections['about']->content ?? 'Pelayanan sekolah minggu dengan pembinaan iman anak yang kreatif, aman, dan relevan.' }}</p>
    </div>
</section>

<section id="berita" class="section container fade d2">
    <h2>Berita Terbaru</h2>
    <div class="grid-3">
        @forelse($news as $item)
            <article class="card">
                <p class="meta">{{ optional($item->published_at)->format('d M Y') ?? '-' }}</p>
                <h3 class="news-title">{{ $item->title }}</h3>
                <p class="news-body">{{ $item->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($item->body), 120) }}</p>
            </article>
        @empty
            <article class="card">Belum ada berita tersedia.</article>
        @endforelse
    </div>
</section>

<section class="section container fade d3">
    <h2>Informasi & Jadwal</h2>
    <div class="grid-2">
        @forelse($announcements as $item)
            <article class="card">
                <h3 class="news-title">{{ $item->title }}</h3>
                <p class="meta">{{ optional($item->event_date)->format('d M Y') ?? 'Jadwal menyusul' }} {{ $item->location ? '- '.$item->location : '' }}</p>
                <p class="news-body">{{ $item->body }}</p>
            </article>
        @empty
            <article class="card">Belum ada informasi aktif.</article>
        @endforelse
    </div>
</section>

<section class="section container fade d3">
    <h2>Galeri Pelayanan</h2>
    <div class="grid-3 gallery">
        @forelse($gallery as $item)
            <div class="card">
                <img src="{{ asset('storage/'.$item->file_path) }}" alt="{{ $item->title }}">
                <p class="meta" style="margin-top:8px;">{{ $item->title }}</p>
            </div>
        @empty
            <div class="card">Belum ada media upload.</div>
        @endforelse
    </div>
</section>

<section class="section container fade d3">
    <div class="card" style="background:linear-gradient(120deg,#0f766e,#0e7490,#0284c7); color:#fff; border:0;">
        <h2 style="color:#fff;">{{ $sections['cta']->title ?? 'Ayo Bertumbuh Bersama DSCMKids' }}</h2>
        <p>{{ $sections['cta']->content ?? 'Hubungi admin untuk pendaftaran anak, info kelas, dan aktivitas pelayanan.' }}</p>
    </div>
</section>

<div class="footer">&copy; {{ date('Y') }} DSCMKids</div>
</body>
</html>
