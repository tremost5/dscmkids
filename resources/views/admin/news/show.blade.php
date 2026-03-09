@extends('admin.layout')

@section('title', 'Detail Berita')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $news->title }}</h1>
            <p class="muted">Slug: {{ $news->slug ?: '-' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.news.edit', $news) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            @if($news->cover_image)
                <img src="{{ asset('storage/'.$news->cover_image) }}" alt="cover" class="detail-media">
            @endif
            @if($news->excerpt)
                <div class="detail-card">
                    <div class="section-head"><h2 class="section-title">Excerpt</h2></div>
                    <div class="detail-prose">{{ $news->excerpt }}</div>
                </div>
            @endif
            <div class="section-head"><h2 class="section-title">Body</h2></div>
            <div class="detail-prose">{{ $news->body }}</div>
        </section>

        <aside class="detail-panel detail-card">
            <div class="section-head"><h2 class="section-title">Ringkasan</h2></div>
            <div class="detail-kv">
                <div class="detail-kv-item"><span>Status</span><strong>{{ $news->is_published ? 'Published' : 'Draft' }}</strong></div>
                <div class="detail-kv-item"><span>Published at</span><strong>{{ optional($news->published_at)->format('d M Y H:i') ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>Slug</span><strong>{{ $news->slug ?: '-' }}</strong></div>
            </div>
        </aside>
    </div>
</div>
@endsection
