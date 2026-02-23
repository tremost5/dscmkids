<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} | DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Nunito',sans-serif; background:#f3f8ff; color:#142544; }
        .container { width:min(980px,92vw); margin:0 auto; padding:24px 0 34px; }
        h1 { font-family:'Baloo 2',sans-serif; font-size:2.2rem; margin:10px 0 0; }
        .meta { margin-top:8px; color:#64748b; }
        .cover { margin-top:14px; border-radius:16px; width:100%; max-height:460px; object-fit:cover; border:1px solid #dbe7f8; }
        .content { margin-top:16px; border:1px solid #dde8fa; background:#fff; border-radius:16px; padding:18px; white-space:pre-line; line-height:1.7; }
        .related { margin-top:16px; border:1px solid #dde8fa; background:#fff; border-radius:16px; padding:14px; }
        .related h3 { margin-top:0; font-family:'Baloo 2',sans-serif; }
        .related a { display:block; padding:8px 0; text-decoration:none; color:#1d4ed8; font-weight:800; }
    </style>
</head>
<body>
<div class="container">
    <a href="{{ route('news.index') }}" style="text-decoration:none;color:#1d4ed8;font-weight:800;">&larr; Kembali ke Daftar Berita</a>
    <h1>{{ $article->title }}</h1>
    <div class="meta">{{ optional($article->published_at)->format('d M Y H:i') ?? '-' }}</div>
    @if($article->cover_image)
        <img class="cover" src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}">
    @endif
    <div class="content">{{ $article->body }}</div>

    @if($relatedNews->isNotEmpty())
        <section class="related">
            <h3>Berita Lainnya</h3>
            @foreach($relatedNews as $item)
                <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
            @endforeach
        </section>
    @endif
</div>
</body>
</html>
