<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} | DSCMKids</title>
    <meta name="description" content="{{ $article->excerpt ? e($article->excerpt) : e(\Illuminate\Support\Str::limit(strip_tags($article->body), 150)) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ e($article->title) }} | DSCMKids">
    <meta property="og:description" content="{{ $article->excerpt ? e($article->excerpt) : e(\Illuminate\Support\Str::limit(strip_tags($article->body), 150)) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    @if (!app()->environment('testing'))
        @vite(['resources/css/news.css'])
    @endif
</head>
<body data-page="news-show">
<div class="news-shell">
    <section class="news-top">
        <a class="news-back" href="{{ route('news.index') }}">&larr; Kembali ke Daftar Berita</a>
        <h1 class="news-title">{{ $article->title }}</h1>
        <div class="news-subtitle">{{ optional($article->published_at)->format('d M Y H:i') ?? '-' }}</div>
    </section>

    @if($article->cover_image)
        <img class="news-cover" src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}">
    @endif
    <div class="news-content-card">{{ $article->body }}</div>

    @if($relatedNews->isNotEmpty())
        <section class="news-related">
            <h3>Berita Lainnya</h3>
            @foreach($relatedNews as $item)
                <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
            @endforeach
        </section>
    @endif
</div>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () { navigator.serviceWorker.register('/sw.js'); });
}
</script>
</body>
</html>
