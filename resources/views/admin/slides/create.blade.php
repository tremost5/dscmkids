@extends('admin.layout')

@section('title', 'Tambah Slide')

@section('content')
<h1 style="margin-top:0;">Tambah Slide</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.slides.store') }}">
    @csrf
    <div class="field"><label>Judul<input name="title" value="{{ old('title') }}"></label></div>
    <div class="field"><label>Subjudul<textarea name="subtitle">{{ old('subtitle') }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Teks Tombol<input name="button_text" value="{{ old('button_text') }}"></label></div>
        <div class="field"><label>URL Tombol<input name="button_url" value="{{ old('button_url') }}"></label></div>
    </div>
    <div class="grid-2">
        <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></label></div>
        <div class="field"><label style="display:flex;align-items:center;gap:8px;margin-top:30px;"><input type="checkbox" name="is_active" value="1" checked style="width:auto;"> Aktif</label></div>
    </div>
    <div class="field"><label>Gambar Slide<input type="file" name="image" accept="image/*" required></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.slides.index') }}">Batal</a>
    </div>
</form>
@endsection
