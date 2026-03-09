@extends('admin.layout')

@section('title', 'Tambah Slide')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah slide</h1>
            <p class="muted">Buat hero slide baru untuk landing page, lengkap dengan CTA dan urutan tampil.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.slides.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="field"><label>Judul<input name="title" value="{{ old('title') }}"></label></div>
            <div class="field"><label>Subjudul<textarea name="subtitle">{{ old('subtitle') }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Teks Tombol<input name="button_text" value="{{ old('button_text') }}"></label></div>
                <div class="field"><label>URL Tombol<input name="button_url" value="{{ old('button_url') }}"></label></div>
            </div>
            <div class="grid-2">
                <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></label></div>
                <label class="toggle-field"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            </div>
            <div class="field"><label>Gambar Slide<input type="file" name="image" accept="image/*" required></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.slides.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
