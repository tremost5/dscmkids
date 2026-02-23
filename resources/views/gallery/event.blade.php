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

        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
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
                    <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventNameItem }}{{ $date ? ' - '.$date : '' }}">
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

    function closeLightbox() {
        lightbox.classList.remove('open');
        lightboxImage.src = '';
        lightboxMeta.textContent = '';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-lightbox-src]').forEach((img) => {
        img.addEventListener('click', () => {
            lightboxImage.src = img.getAttribute('data-lightbox-src') || '';
            lightboxImage.alt = img.getAttribute('data-lightbox-title') || 'Gallery';
            lightboxMeta.textContent = (img.getAttribute('data-lightbox-title') || '') + ' | ' + (img.getAttribute('data-lightbox-meta') || '');
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    lightboxClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) {
            closeLightbox();
        }
    });
})();
</script>
</body>
</html>
