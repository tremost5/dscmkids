@extends('admin.layout')

@section('title', 'Detail Media')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $media->title }}</h1>
            <p class="muted">{{ $media->mime_type ?? '-' }} · {{ $media->file_size ? number_format($media->file_size / 1024, 1).' KB' : '-' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ asset('storage/'.$media->file_path) }}" target="_blank" rel="noopener">Buka File</a>
            <a class="btn btn-secondary" href="{{ route('admin.media.edit', $media) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            <div class="section-head">
                <h2 class="section-title">Preview</h2>
            </div>
            @if(str_starts_with($media->mime_type ?? '', 'image/'))
                <img src="{{ asset('storage/'.$media->file_path) }}" alt="{{ $media->title }}" class="detail-media">
            @else
                <p class="helper-text">Preview inline hanya tersedia untuk file gambar. Gunakan tombol "Buka File" untuk melihat file asli.</p>
            @endif
        </section>

        <aside class="detail-panel detail-card">
            <div class="section-head">
                <h2 class="section-title">Metadata</h2>
            </div>
            <div class="detail-kv">
                <div class="detail-kv-item"><span>Mime type</span><strong>{{ $media->mime_type ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>Ukuran</span><strong>{{ $media->file_size ? number_format($media->file_size / 1024, 1).' KB' : '-' }}</strong></div>
                <div class="detail-kv-item"><span>Path</span><strong>{{ $media->file_path }}</strong></div>
            </div>
        </aside>
    </div>
</div>
@endsection
