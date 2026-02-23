@extends('admin.layout')

@section('title', 'Edit Berita')

@section('content')
<h1 style="margin-top:0;">Edit Berita</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.news.update', $news) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Judul<input name="title" value="{{ old('title', $news->title) }}" required></label></div>
    <div class="field"><label>Slug<input name="slug" value="{{ old('slug', $news->slug) }}"></label></div>
    <div class="field"><label>Excerpt<textarea name="excerpt">{{ old('excerpt', $news->excerpt) }}</textarea></label></div>
    <div class="field"><label>Body<textarea name="body" required>{{ old('body', $news->body) }}</textarea></label></div>
    <div class="field"><label>Ganti Cover<input type="file" name="cover_image" accept="image/*"></label></div>
    <div class="grid-2">
        <div class="field"><label>Published At<input type="datetime-local" name="published_at" value="{{ old('published_at', optional($news->published_at)->format('Y-m-d\\TH:i')) }}"></label></div>
        <div class="field"><label style="display:flex;align-items:center;gap:8px;margin-top:30px;"><input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }} style="width:auto;"> Publish</label></div>
    </div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Batal</a>
    </div>
</form>
@endsection
