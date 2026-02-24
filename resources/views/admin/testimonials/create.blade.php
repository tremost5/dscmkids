@extends('admin.layout')

@section('title', 'Tambah Testimonial')

@section('content')
<h1 style="margin-top:0;">Tambah Testimonial</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.testimonials.store') }}">
    @csrf
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
    <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" checked style="width:auto;"> Aktif</label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.testimonials.index') }}">Batal</a>
    </div>
</form>
@endsection

