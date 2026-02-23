@extends('admin.layout')

@section('title', 'Tambah Guru')

@section('content')
<h1 style="margin-top:0;">Tambah Profil Guru</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.teachers.store') }}">
    @csrf
    <div class="field"><label>Nama<input name="name" value="{{ old('name') }}" required></label></div>
    <div class="grid-2">
        <div class="field"><label>Peran<input name="role" value="{{ old('role') }}"></label></div>
        <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group') }}" placeholder="PG / TKA / 1 / dst"></label></div>
    </div>
    <div class="field"><label>Bio Singkat<textarea name="bio">{{ old('bio') }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Instagram URL<input name="instagram_url" value="{{ old('instagram_url') }}"></label></div>
        <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></label></div>
    </div>
    <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" checked style="width:auto;"> Aktif</label></div>
    <div class="field"><label>Foto Guru<input type="file" name="photo" accept="image/*"></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.teachers.index') }}">Batal</a>
    </div>
</form>
@endsection
