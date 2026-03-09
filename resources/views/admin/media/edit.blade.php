@extends('admin.layout')

@section('title', 'Edit Media')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit media</h1>
            <p class="muted">Perbarui metadata atau ganti file yang sudah diunggah.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.update', $media) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">File details</h2>
                <p class="section-copy">Format yang diizinkan: JPG, PNG, WEBP, GIF, MP4, dan PDF.</p>
            </div>
            <div class="field"><label>Judul File<input name="title" value="{{ old('title', $media->title) }}" required></label></div>
            <div class="field"><label>Ganti File (opsional)<input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.pdf,image/jpeg,image/png,image/webp,image/gif,video/mp4,application/pdf"></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
