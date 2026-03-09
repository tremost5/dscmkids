@extends('admin.layout')

@section('title', 'Detail Slide')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $slide->title ?? 'Tanpa Judul' }}</h1>
            <p class="muted">Urutan: {{ $slide->sort_order }} · {{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.slides.edit', $slide) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.slides.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            <img src="{{ asset('storage/'.$slide->image_path) }}" alt="slide" class="detail-media">
            <div class="detail-prose">{{ $slide->subtitle }}</div>
        </section>

        <aside class="detail-panel detail-card">
            <div class="detail-kv">
                <div class="detail-kv-item"><span>Teks Tombol</span><strong>{{ $slide->button_text ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>URL Tombol</span><strong>{{ $slide->button_url ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>Status</span><strong>{{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</strong></div>
            </div>
        </aside>
    </div>
</div>
@endsection
