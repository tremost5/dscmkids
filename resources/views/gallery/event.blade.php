<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Galeri: {{ $eventName }} | DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Nunito', sans-serif; background: #f3f8ff; color: #16294d; }
        .container { width: min(1100px, 92vw); margin: 0 auto; padding: 24px 0 34px; }
        h1 { margin: 8px 0 8px; font-family: 'Baloo 2', sans-serif; font-size: 2.1rem; }
        .muted { color: #64748b; }
        .event-links { display: flex; gap: 8px; flex-wrap: wrap; margin: 10px 0 14px; }
        .event-links a { text-decoration: none; border: 1px solid #d4e0f4; background: #fff; color: #334155; font-weight: 700; border-radius: 999px; padding: 8px 12px; }
        .event-links a.active { background: #1d4ed8; border-color: #1d4ed8; color: #fff; }
        .stats { display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 12px; }
        .stat { border:1px solid #d8e5f8; border-radius:12px; background:#fff; padding:10px; }
        .label { font-size:.78rem; color:#65738d; text-transform:uppercase; font-weight:800; }
        .value { margin-top:4px; font-size:1.25rem; font-weight:900; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .photo { position: relative; overflow: hidden; border-radius: 16px; border: 1px solid #dce8f9; min-height: 250px; }
        .photo img { width: 100%; height: 100%; object-fit: cover; display: block; cursor: zoom-in; transition: transform .3s ease; }
        .photo:hover img { transform: scale(1.05); }
        .caption { position: absolute; left: 0; right: 0; bottom: 0; color: #fff; padding: 10px; background: linear-gradient(180deg, rgba(0,0,0,0), rgba(0,0,0,.8)); font-size: .85rem; }

        .lightbox { position: fixed; inset: 0; background: rgba(7,13,30,.92); display: none; align-items: center; justify-content: center; z-index: 99; padding: 20px; }
        .lightbox.open { display: flex; }
        .lightbox img { max-width: min(1100px, 92vw); max-height: 78vh; object-fit: contain; border-radius: 14px; border: 1px solid rgba(255,255,255,.22); }
        .lightbox-meta { margin-top: 10px; color: #dce7ff; text-align: center; font-weight: 700; }
        .lightbox-close { position: absolute; top: 16px; right: 18px; width: 38px; height: 38px; border-radius: 999px; border: 1px solid rgba(255,255,255,.4); background: rgba(255,255,255,.12); color: #fff; font-size: 1.2rem; cursor: pointer; }
        .lightbox-nav { position:absolute; top:50%; transform:translateY(-50%); width:42px; height:42px; border-radius:999px; border:1px solid rgba(255,255,255,.4); background:rgba(255,255,255,.12); color:#fff; font-size:1.3rem; cursor:pointer; }
        .lightbox-prev { left: 16px; }
        .lightbox-next { right: 16px; }

        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } .stats { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 620px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <a href="{{ route('landing') }}#gallery" style="text-decoration:none;color:#1d4ed8;font-weight:800;">&larr; Kembali ke Landing</a>
    <h1>Event: {{ $eventName }}</h1>
    <p class="muted">Kumpulan foto kegiatan untuk event ini.</p>

    <div class="event-links">
        @foreach($allEvents as $slug => $name)
            <a href="{{ route('gallery.event', ['eventSlug' => $slug]) }}" class="{{ $slug === $eventSlug ? 'active' : '' }}">{{ $name }}</a>
        @endforeach
    </div>

    <div class="stats">
        <div class="stat"><div class="label">Jumlah Foto</div><div class="value">{{ $eventStats['photo_count'] }}</div></div>
        <div class="stat"><div class="label">Tanggal Awal</div><div class="value" style="font-size:1rem;">{{ $eventStats['first_date'] ?? '-' }}</div></div>
        <div class="stat"><div class="label">Tanggal Terbaru</div><div class="value" style="font-size:1rem;">{{ $eventStats['latest_date'] ?? '-' }}</div></div>
        <div class="stat"><div class="label">Total Event</div><div class="value">{{ $eventStats['event_count'] }}</div></div>
    </div>

    <div class="grid">
        @forelse($eventItems as $photo)
            @php
                $title = $photo['title'] ?? 'Kegiatan DSCMKids';
                $date = $photo['date'] ?? null;
                $eventNameItem = $photo['event_name'] ?? $eventName;
                $pathValue = $photo['path'] ?? null;
                $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://')) ? $pathValue : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
            @endphp
            <figure class="photo">
                @if($src)
                    <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventNameItem }}{{ $date ? ' - '.$date : '' }}" data-lightbox-index="{{ $loop->index }}">
                @endif
                <figcaption class="caption"><strong>{{ $title }}</strong><br>{{ $eventNameItem }}{{ $date ? ' - '.$date : '' }}</figcaption>
            </figure>
        @empty
            <p>Tidak ada foto pada event ini.</p>
        @endforelse
    </div>
</div>

<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose" aria-label="Tutup">&times;</button>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Sebelumnya">&#10094;</button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Berikutnya">&#10095;</button>
    <div>
        <img id="lightboxImage" src="" alt="Preview">
        <div class="lightbox-meta" id="lightboxMeta"></div>
    </div>
</div>

<script>
(function () {
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
