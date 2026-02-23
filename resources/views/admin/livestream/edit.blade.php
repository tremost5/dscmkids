@extends('admin.layout')

@section('title', 'Live Streaming')

@section('content')
<h1 style="margin-top:0;">Pengaturan Live Streaming</h1>
<p class="muted">Atur link YouTube untuk layar theater di landing page.</p>

<form method="POST" action="{{ route('admin.livestream.update') }}">
    @csrf
    @method('PUT')

    <div class="field">
        <label>Judul Section
            <input type="text" name="title" value="{{ old('title', $section->title) }}" required>
        </label>
    </div>

    <div class="field">
        <label>Deskripsi Singkat
            <textarea name="content">{{ old('content', $section->content) }}</textarea>
        </label>
    </div>

    <div class="field">
        <label>Link YouTube Live
            <input type="url" name="youtube_url" value="{{ old('youtube_url', $section->meta['youtube_url'] ?? '') }}" placeholder="https://www.youtube.com/watch?v=..."></input>
        </label>
    </div>

    <div class="field">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="is_live" value="1" style="width:auto;" {{ old('is_live', $section->meta['is_live'] ?? false) ? 'checked' : '' }}>
            Tampilkan status LIVE
        </label>
    </div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('landing') }}#live" target="_blank">Lihat Preview</a>
    </div>
</form>

@if(!empty($section->meta['youtube_url']))
    <hr style="border:0;border-top:1px solid #e5e7eb;margin:16px 0;">
    <h3>Preview Link Saat Ini</h3>
    <p style="word-break:break-all;">{{ $section->meta['youtube_url'] }}</p>
@endif
@endsection
