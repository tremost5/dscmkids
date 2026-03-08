@extends('admin.layout')

@section('title', 'Edit Media')

@section('content')
<h1 style="margin-top:0;">Edit Media</h1>
<p class="muted">Format yang diizinkan: JPG, PNG, WEBP, GIF, MP4, dan PDF.</p>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.update', $media) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Judul File<input name="title" value="{{ old('title', $media->title) }}" required></label></div>
    <div class="field"><label>Ganti File (opsional)<input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.pdf,image/jpeg,image/png,image/webp,image/gif,video/mp4,application/pdf"></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Batal</a>
    </div>
</form>
@endsection
