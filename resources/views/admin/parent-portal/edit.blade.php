@extends('admin.layout')

@section('title', 'Pengaturan Parent Portal')

@section('content')
<h1 style="margin-top:0;">Pengaturan Parent Portal</h1>
<p class="muted">Fitur ini muncul di landing hanya jika status aktif.</p>

<form method="POST" action="{{ route('admin.parent-portal.update') }}">
    @csrf
    @method('PUT')

    <div class="field">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="enabled" value="1" style="width:auto;" {{ old('enabled', $section->meta['enabled'] ?? false) ? 'checked' : '' }}>
            Aktifkan Parent Portal di website publik
        </label>
    </div>

    <div class="field"><label>Judul<input name="title" value="{{ old('title', $section->title) }}" required></label></div>
    <div class="field"><label>Deskripsi<textarea name="content">{{ old('content', $section->content) }}</textarea></label></div>
    <div class="field"><label>Link CTA Orang Tua (opsional)<input type="url" name="cta_url" value="{{ old('cta_url', $section->meta['cta_url'] ?? '') }}" placeholder="https://..."></label></div>
    <div class="grid-2">
        <div class="field"><label>Highlight 1<input name="highlight_1" value="{{ old('highlight_1', $section->meta['highlights'][0] ?? '') }}"></label></div>
        <div class="field"><label>Highlight 2<input name="highlight_2" value="{{ old('highlight_2', $section->meta['highlights'][1] ?? '') }}"></label></div>
    </div>
    <div class="field"><label>Highlight 3<input name="highlight_3" value="{{ old('highlight_3', $section->meta['highlights'][2] ?? '') }}"></label></div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('landing') }}">Lihat Situs</a>
    </div>
</form>
@endsection

