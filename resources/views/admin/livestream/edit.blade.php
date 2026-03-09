@extends('admin.layout')

@section('title', 'Live Streaming')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Pengaturan live streaming</h1>
            <p class="muted">Atur link YouTube dan status live untuk theater di landing page.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('landing') }}#live" target="_blank" rel="noopener">Lihat Preview</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.livestream.update') }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="field"><label>Judul Section<input type="text" name="title" value="{{ old('title', $section->title) }}" required></label></div>
            <div class="field"><label>Deskripsi Singkat<textarea name="content">{{ old('content', $section->content) }}</textarea></label></div>
            <div class="field"><label>Link YouTube Live<input type="url" name="youtube_url" value="{{ old('youtube_url', $section->meta['youtube_url'] ?? '') }}" placeholder="https://www.youtube.com/watch?v=..."></label></div>
            <label class="toggle-field"><input type="checkbox" name="is_live" value="1" {{ old('is_live', $section->meta['is_live'] ?? false) ? 'checked' : '' }}> Tampilkan status LIVE</label>
        </section>

        @if(!empty($section->meta['youtube_url']))
            <section class="detail-panel">
                <div class="section-head"><h2 class="section-title">Preview link saat ini</h2></div>
                <div class="detail-prose">{{ $section->meta['youtube_url'] }}</div>
            </section>
        @endif

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('landing') }}#live" target="_blank" rel="noopener">Preview</a>
        </div>
    </form>
</div>
@endsection
