@extends('admin.layout')

@section('title', 'Edit Guru')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit profil guru</h1>
            <p class="muted">Perbarui biodata, kelas pelayanan, dan media guru.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.teachers.update', $teacher) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="field"><label>Nama<input name="name" value="{{ old('name', $teacher->name) }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Peran<input name="role" value="{{ old('role', $teacher->role) }}"></label></div>
                <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group', $teacher->class_group) }}"></label></div>
            </div>
            <div class="field"><label>Bio Singkat<textarea name="bio">{{ old('bio', $teacher->bio) }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Instagram URL<input name="instagram_url" value="{{ old('instagram_url', $teacher->instagram_url) }}"></label></div>
                <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $teacher->sort_order) }}"></label></div>
            </div>
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}> Aktif</label>
            <div class="field"><label>Ganti Foto<input type="file" name="photo" accept="image/*"></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.teachers.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
