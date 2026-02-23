@extends('admin.layout')

@section('title', 'Tambah Berita')

@section('content')
<h1 style="margin-top:0;">Tambah Berita</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.news.store') }}">
    @csrf
    <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
    <div class="field"><label>Slug (opsional)<input name="slug" value="{{ old('slug') }}"></label></div>
    <div class="field"><label>Excerpt<textarea name="excerpt">{{ old('excerpt') }}</textarea></label></div>
    <div class="field"><label>Body<textarea name="body" required>{{ old('body') }}</textarea></label></div>
    <div class="field"><label>Cover Image<input type="file" name="cover_image" accept="image/*"></label></div>
    <div class="grid-2">
        <div class="field"><label>Published At<input type="datetime-local" name="published_at" value="{{ old('published_at') }}"></label></div>
        <div class="field"><label style="display:flex;align-items:center;gap:8px;margin-top:30px;"><input type="checkbox" name="is_published" value="1" checked style="width:auto;"> Publish</label></div>
    </div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Batal</a>
    </div>
</form>
@endsection
