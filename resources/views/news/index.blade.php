<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    @if (!app()->environment('testing'))
        @vite(['resources/css/news.css', 'resources/js/news.js'])
    @endif
</head>
<body data-page="news-index">
@php
    $highlight = function (string $text, string $q): string {
        $safe = e($text);
        if ($q === '') {
            return $safe;
        }

        $pattern = '/('.preg_quote($q, '/').')/i';
        return (string) preg_replace($pattern, '<mark>$1</mark>', $safe);
    };
@endphp
<div class="news-shell">
    <section class="news-top">
        <a class="news-back" href="{{ route('landing') }}">&larr; Kembali ke Landing</a>
        <h1 class="news-title">Semua Berita DSCMKids</h1>
        <p class="news-subtitle">Cerita pelayanan, kegiatan anak, dan update terbaru gereja.</p>
        <div class="news-search-wrap">
            <form class="search-form" method="GET" action="{{ route('news.index') }}" id="searchForm">
                <input type="search" id="newsSearch" name="q" value="{{ $query }}" placeholder="Cari berita (judul / isi)..." autocomplete="off">
                <button type="submit">Cari</button>
                @if($query !== '')
                    <a class="reset-link" href="{{ route('news.index') }}">Reset</a>
                @endif
            </form>
        </div>
    </section>

    @if($query !== '')
        <div class="news-meta-query">Hasil pencarian: "{{ $query }}" ({{ $latestNews->total() }} berita)</div>
    @endif

    <div class="news-list" id="newsList">
        @forelse($latestNews as $item)
            @php
                $excerptText = $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->body), 220);
            @endphp
            <article class="news-item">
                <h2>{!! $highlight($item->title, $query) !!}</h2>
                <div class="news-item-meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                <p class="news-item-excerpt">{!! $highlight($excerptText, $query) !!}</p>
                <a class="news-item-link" href="{{ route('news.show', $item->slug) }}">Baca Selengkapnya</a>
            </article>
        @empty
            <article class="news-item">Belum ada berita yang sesuai.</article>
        @endforelse
    </div>

    <div class="news-pagination" id="paginationWrap">{{ $latestNews->links() }}</div>
</div>
</body>
</html>
