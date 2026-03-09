@extends('admin.layout')

@section('title', 'Edit Materi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit materi edukatif</h1>
            <p class="muted">Perbarui konten materi, urutan, dan status tayang.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.materials.update', $material) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">Material details</h2>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title', $material->title) }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group', $material->class_group) }}"></label></div>
                <div class="field"><label>Level
                    <select name="level">
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
                <label class="toggle-field"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }}> Aktif</label>
            </div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.materials.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
