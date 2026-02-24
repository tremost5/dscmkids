<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Galeri: {{ $eventName }} | DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    @if (!app()->environment('testing'))
        @vite(['resources/css/gallery-event.css', 'resources/js/gallery-event.js'])
    @endif
</head>
<body data-page="gallery-event">
<div class="gallery-shell">
    <section class="gallery-hero">
        <a class="gallery-back" href="{{ route('landing') }}#gallery">&larr; Kembali ke Landing</a>
        <h1 class="gallery-title">Event: {{ $eventName }}</h1>
        <p class="gallery-muted">Kumpulan foto kegiatan untuk event ini.</p>

        <div class="event-links">
            @foreach($allEvents as $slug => $name)
                <a href="{{ route('gallery.event', ['eventSlug' => $slug]) }}" class="{{ $slug === $eventSlug ? 'active' : '' }}">{{ $name }}</a>
            @endforeach
        </div>
    </section>

    <div class="gallery-stats">
        <div class="gallery-stat"><div class="gallery-label">Jumlah Foto</div><div class="gallery-value">{{ $eventStats['photo_count'] }}</div></div>
        <div class="gallery-stat"><div class="gallery-label">Tanggal Awal</div><div class="gallery-value small">{{ $eventStats['first_date'] ?? '-' }}</div></div>
        <div class="gallery-stat"><div class="gallery-label">Tanggal Terbaru</div><div class="gallery-value small">{{ $eventStats['latest_date'] ?? '-' }}</div></div>
        <div class="gallery-stat"><div class="gallery-label">Total Event</div><div class="gallery-value">{{ $eventStats['event_count'] }}</div></div>
    </div>

    <div class="gallery-grid">
        @forelse($eventItems as $photo)
            @php
                $title = $photo['title'] ?? 'Kegiatan DSCMKids';
                $date = $photo['date'] ?? null;
                $eventNameItem = $photo['event_name'] ?? $eventName;
                $pathValue = $photo['path'] ?? null;
                $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://')) ? $pathValue : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
            @endphp
            <figure class="gallery-photo">
                @if($src)
                    <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventNameItem }}{{ $date ? ' - '.$date : '' }}" data-lightbox-index="{{ $loop->index }}">
                @endif
                <figcaption class="gallery-caption"><strong>{{ $title }}</strong><br>{{ $eventNameItem }}{{ $date ? ' - '.$date : '' }}</figcaption>
            </figure>
        @empty
            <p class="gallery-empty">Tidak ada foto pada event ini.</p>
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

</body>
</html>
