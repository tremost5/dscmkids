<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Nunito',sans-serif; background:#f3f8ff; color:#142544; }
        .container { width:min(1000px,92vw); margin:0 auto; padding:24px 0 34px; }
        h1 { font-family:'Baloo 2',sans-serif; font-size:2.2rem; margin:0 0 14px; }
        .search-form { margin: 0 0 14px; display:flex; gap:8px; }
        .search-form input { flex:1; border:1px solid #d8e4f8; border-radius:12px; padding:12px; font:inherit; }
        .search-form button, .reset-link { border:1px solid #d0dcf4; border-radius:12px; padding:11px 14px; background:#fff; color:#1d4ed8; font-weight:800; text-decoration:none; cursor:pointer; }
        .item { border:1px solid #dce8fa; background:#fff; border-radius:16px; padding:16px; margin-bottom:12px; }
        .item h2 { margin:0; font-size:1.2rem; }
        .meta { margin-top:6px; color:#657693; font-size:.85rem; }
        .excerpt { margin-top:10px; color:#3f4f6a; }
        .link { margin-top:10px; display:inline-block; text-decoration:none; color:#1d4ed8; font-weight:800; }
        mark { background:#fef08a; color:#111827; padding:0 2px; border-radius:3px; }
        .muted { color:#64748b; font-weight:700; margin-bottom:12px; }
    </style>
</head>
<body>
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
<div class="container">
    <a href="{{ route('landing') }}" style="text-decoration:none;color:#1d4ed8;font-weight:800;">&larr; Kembali ke Landing</a>
    <h1>Semua Berita DSCMKids</h1>

    <form class="search-form" method="GET" action="{{ route('news.index') }}" id="searchForm">
        <input type="search" id="newsSearch" name="q" value="{{ $query }}" placeholder="Cari berita (judul / isi)..." autocomplete="off">
        <button type="submit">Cari</button>
        @if($query !== '')
            <a class="reset-link" href="{{ route('news.index') }}">Reset</a>
        @endif
    </form>

    @if($query !== '')
        <div class="muted">Hasil pencarian: "{{ $query }}" ({{ $latestNews->total() }} berita)</div>
    @endif

    <div id="newsList">
        @forelse($latestNews as $item)
            @php
                $excerptText = $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->body), 220);
            @endphp
            <article class="item">
                <h2>{!! $highlight($item->title, $query) !!}</h2>
                <div class="meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                <p class="excerpt">{!! $highlight($excerptText, $query) !!}</p>
                <a class="link" href="{{ route('news.show', $item->slug) }}">Baca Selengkapnya</a>
            </article>
        @empty
            <article class="item">Belum ada berita yang sesuai.</article>
        @endforelse
    </div>

    <div id="paginationWrap">{{ $latestNews->links() }}</div>
</div>

<script>
(function () {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('newsSearch');
    if (!form || !input) {
        return;
    }

    let timer = null;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            form.submit();
        }, 450);
    });
})();
</script>
</body>
</html>
