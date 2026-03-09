@extends('admin.layout')

@section('title', 'Upload Media')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Upload media</h1>
            <p class="muted">Format yang diizinkan: JPG, PNG, WEBP, GIF, MP4, dan PDF. File aktif lain diblokir untuk keamanan.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">File details</h2>
                <p class="section-copy">Gunakan nama file yang jelas agar mudah dicari kembali.</p>
            </div>
            <div class="field"><label>Judul File<input name="title" value="{{ old('title') }}" required></label></div>
            <div class="field"><label>File<input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.pdf,image/jpeg,image/png,image/webp,image/gif,video/mp4,application/pdf" required></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Upload</button>
            <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
