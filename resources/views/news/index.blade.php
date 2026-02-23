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
        .search { margin: 0 0 14px; }
        .search input { width:100%; border:1px solid #d8e4f8; border-radius:12px; padding:12px; font:inherit; }
        .item { border:1px solid #dce8fa; background:#fff; border-radius:16px; padding:16px; margin-bottom:12px; }
        .item h2 { margin:0; font-size:1.2rem; }
        .meta { margin-top:6px; color:#657693; font-size:.85rem; }
        .excerpt { margin-top:10px; color:#3f4f6a; }
        .link { margin-top:10px; display:inline-block; text-decoration:none; color:#1d4ed8; font-weight:800; }
        .no-result { display:none; border:1px dashed #c8d8f2; border-radius:14px; padding:16px; background:#fff; color:#667993; }
    </style>
</head>
<body>
<div class="container">
    <a href="{{ route('landing') }}" style="text-decoration:none;color:#1d4ed8;font-weight:800;">&larr; Kembali ke Landing</a>
    <h1>Semua Berita DSCMKids</h1>

    <div class="search">
        <input type="search" id="newsSearch" placeholder="Cari berita (judul / isi)...">
    </div>

    <div id="newsList">
        @forelse($latestNews as $item)
            @php
                $searchText = strtolower(trim(($item->title ?? '').' '.($item->excerpt ?? '').' '.strip_tags($item->body ?? '')));
            @endphp
            <article class="item" data-news-item data-search-text="{{ $searchText }}">
                <h2>{{ $item->title }}</h2>
                <div class="meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                <p class="excerpt">{{ $item->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($item->body), 220) }}</p>
                <a class="link" href="{{ route('news.show', $item->slug) }}">Baca Selengkapnya</a>
            </article>
        @empty
            <article class="item">Belum ada berita.</article>
        @endforelse
    </div>

    <div class="no-result" id="noResult">Tidak ada berita yang cocok dengan kata kunci kamu.</div>

    <div id="paginationWrap">{{ $latestNews->links() }}</div>
</div>

<script>
(function () {
    const input = document.getElementById('newsSearch');
    const items = Array.from(document.querySelectorAll('[data-news-item]'));
    const noResult = document.getElementById('noResult');
    const paginationWrap = document.getElementById('paginationWrap');

    if (!input || !items.length) {
        return;
    }

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;

        items.forEach((item) => {
            const text = item.getAttribute('data-search-text') || '';
            const match = q === '' || text.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) {
                visible += 1;
            }
        });

        noResult.style.display = visible === 0 ? 'block' : 'none';
        paginationWrap.style.display = q === '' ? '' : 'none';
    });
})();
</script>
</body>
</html>
