@extends('admin.layout')

@section('title', 'Edit Materi')

@section('content')
<h1 style="margin-top:0;">Edit Materi Edukatif</h1>
<form method="POST" action="{{ route('admin.materials.update', $material) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Judul<input name="title" value="{{ old('title', $material->title) }}" required></label></div>
    <div class="grid-2">
        <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group', $material->class_group) }}"></label></div>
        <div class="field"><label>Level
            <select name="level" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;margin-top:6px;">
                <option value="easy" @selected(old('level', $material->level) === 'easy')>Easy</option>
                <option value="medium" @selected(old('level', $material->level) === 'medium')>Medium</option>
                <option value="hard" @selected(old('level', $material->level) === 'hard')>Hard</option>
            </select>
        </label></div>
    </div>
    <div class="field"><label>Ayat Alkitab<input name="bible_reference" value="{{ old('bible_reference', $material->bible_reference) }}"></label></div>
    <div class="field"><label>Ringkasan<textarea name="summary">{{ old('summary', $material->summary) }}</textarea></label></div>
    <div class="field"><label>Konten Materi<textarea name="content" required>{{ old('content', $material->content) }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Urutan<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $material->sort_order) }}"></label></div>
        <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }} style="width:auto;"> Aktif</label></div>
    </div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.materials.index') }}">Batal</a>
    </div>
</form>
@endsection

