@extends('admin.layout')

@section('title', 'Pengaturan Parent Portal')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Pengaturan parent portal</h1>
            <p class="muted">Fitur ini muncul di landing hanya jika status aktif.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('landing') }}" target="_blank" rel="noopener">Lihat Situs</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.parent-portal.update') }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <label class="toggle-field"><input type="checkbox" name="enabled" value="1" {{ old('enabled', $section->meta['enabled'] ?? false) ? 'checked' : '' }}> Aktifkan Parent Portal di website publik</label>
            <div class="field"><label>Judul<input name="title" value="{{ old('title', $section->title) }}" required></label></div>
            <div class="field"><label>Deskripsi<textarea name="content">{{ old('content', $section->content) }}</textarea></label></div>
            <div class="field"><label>Link CTA Orang Tua (opsional)<input type="url" name="cta_url" value="{{ old('cta_url', $section->meta['cta_url'] ?? '') }}" placeholder="https://..."></label></div>
            <div class="grid-2">
                <div class="field"><label>Highlight 1<input name="highlight_1" value="{{ old('highlight_1', $section->meta['highlights'][0] ?? '') }}"></label></div>
                <div class="field"><label>Highlight 2<input name="highlight_2" value="{{ old('highlight_2', $section->meta['highlights'][1] ?? '') }}"></label></div>
            </div>
            <div class="field"><label>Highlight 3<input name="highlight_3" value="{{ old('highlight_3', $section->meta['highlights'][2] ?? '') }}"></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('landing') }}" target="_blank" rel="noopener">Lihat Situs</a>
        </div>
    </form>
</div>
@endsection
