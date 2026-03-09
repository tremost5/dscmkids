@extends('admin.layout')

@section('title', 'Tambah Testimonial')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah testimonial</h1>
            <p class="muted">Masukkan testimoni baru lengkap dengan rating dan avatar opsional.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.testimonials.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="field"><label>Nama Depan / Panggilan<input name="name" value="{{ old('name') }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Keterangan (contoh: Ortu Natan - Kelas 3)<input name="role_label" value="{{ old('role_label') }}"></label></div>
                <div class="field"><label>Rating (1-5)<input type="number" min="1" max="5" name="rating" value="{{ old('rating', 5) }}" required></label></div>
            </div>
            <div class="field"><label>Isi Testimonial<textarea name="message" required>{{ old('message') }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Urutan Tampil<input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></label></div>
                <div class="field"><label>Foto Kecil / Avatar<input type="file" name="avatar" accept="image/*"></label></div>
            </div>
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.testimonials.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
