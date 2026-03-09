@extends('admin.layout')

@section('title', 'Edit Slide')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit slide</h1>
            <p class="muted">Perbarui hero content, urutan, dan gambar slide.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.slides.update', $slide) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="field"><label>Judul<input name="title" value="{{ old('title', $slide->title) }}"></label></div>
            <div class="field"><label>Subjudul<textarea name="subtitle">{{ old('subtitle', $slide->subtitle) }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Teks Tombol<input name="button_text" value="{{ old('button_text', $slide->button_text) }}"></label></div>
                <div class="field"><label>URL Tombol<input name="button_url" value="{{ old('button_url', $slide->button_url) }}"></label></div>
            </div>
            <div class="grid-2">
                <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $slide->sort_order) }}"></label></div>
                <label class="toggle-field"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $slide->is_active) ? 'checked' : '' }}> Aktif</label>
            </div>
            <div class="field"><label>Ganti Gambar<input type="file" name="image" accept="image/*"></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.slides.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
