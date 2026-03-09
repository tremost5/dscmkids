@extends('admin.layout')

@section('title', 'Edit Berita')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit berita</h1>
            <p class="muted">Perbarui artikel, cover, dan jadwal publikasinya.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.news.update', $news) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">News details</h2>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title', $news->title) }}" required></label></div>
            <div class="field"><label>Slug<input name="slug" value="{{ old('slug', $news->slug) }}"></label></div>
            <div class="field"><label>Excerpt<textarea name="excerpt">{{ old('excerpt', $news->excerpt) }}</textarea></label></div>
            <div class="field"><label>Body<textarea name="body" required>{{ old('body', $news->body) }}</textarea></label></div>
            <div class="field"><label>Ganti Cover<input type="file" name="cover_image" accept="image/*"></label></div>
            <div class="grid-2">
                <div class="field"><label>Published At<input type="datetime-local" name="published_at" value="{{ old('published_at', optional($news->published_at)->format('Y-m-d\\TH:i')) }}"></label></div>
                <label class="toggle-field"><input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}> Publish</label>
            </div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
