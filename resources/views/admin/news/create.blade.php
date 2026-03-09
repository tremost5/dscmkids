@extends('admin.layout')

@section('title', 'Tambah Berita')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Buat berita baru</h1>
            <p class="muted">Siapkan artikel publik lengkap dengan cover, excerpt, dan jadwal publish.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.news.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">News details</h2>
                <p class="section-copy">Judul, excerpt, dan body akan tampil di halaman berita publik.</p>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
            <div class="field"><label>Slug (opsional)<input name="slug" value="{{ old('slug') }}"></label></div>
            <div class="field"><label>Excerpt<textarea name="excerpt">{{ old('excerpt') }}</textarea></label></div>
            <div class="field"><label>Body<textarea name="body" required>{{ old('body') }}</textarea></label></div>
            <div class="field"><label>Cover Image<input type="file" name="cover_image" accept="image/*"></label></div>
            <div class="grid-2">
                <div class="field"><label>Published At<input type="datetime-local" name="published_at" value="{{ old('published_at') }}"></label></div>
                <label class="toggle-field"><input type="checkbox" name="is_published" value="1" checked> Publish</label>
            </div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
