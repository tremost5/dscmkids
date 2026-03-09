@extends('admin.layout')

@section('title', 'Tambah Guru')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah profil guru</h1>
            <p class="muted">Masukkan biodata, kelas pelayanan, dan foto guru.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.teachers.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
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
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            <div class="field"><label>Foto Guru<input type="file" name="photo" accept="image/*"></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.teachers.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
