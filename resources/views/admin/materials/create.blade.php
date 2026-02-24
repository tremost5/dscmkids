@extends('admin.layout')

@section('title', 'Tambah Materi')

@section('content')
<h1 style="margin-top:0;">Tambah Materi Edukatif</h1>
<form method="POST" action="{{ route('admin.materials.store') }}">
    @csrf
    <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
    <div class="grid-2">
        <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group') }}" placeholder="PG / TKA / 1 / Semua"></label></div>
        <div class="field"><label>Level
            <select name="level" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;margin-top:6px;">
                <option value="easy" @selected(old('level') === 'easy')>Easy</option>
                <option value="medium" @selected(old('level') === 'medium')>Medium</option>
                <option value="hard" @selected(old('level') === 'hard')>Hard</option>
            </select>
        </label></div>
    </div>
    <div class="field"><label>Ayat Alkitab<input name="bible_reference" value="{{ old('bible_reference') }}"></label></div>
    <div class="field"><label>Ringkasan<textarea name="summary">{{ old('summary') }}</textarea></label></div>
    <div class="field"><label>Konten Materi<textarea name="content" required>{{ old('content') }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></label></div>
        <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" checked style="width:auto;"> Aktif</label></div>
    </div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.materials.index') }}">Batal</a>
    </div>
</form>
@endsection

