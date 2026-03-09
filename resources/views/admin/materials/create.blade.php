@extends('admin.layout')

@section('title', 'Tambah Materi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah materi edukatif</h1>
            <p class="muted">Susun materi pembelajaran berdasarkan kelas, level, dan isi konten.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.materials.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">Material details</h2>
                <p class="section-copy">Gunakan level untuk mengatur tingkat kesulitan materi murid.</p>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Kelas<input name="class_group" value="{{ old('class_group') }}" placeholder="PG / TKA / 1 / Semua"></label></div>
                <div class="field"><label>Level
                    <select name="level">
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
                <label class="toggle-field"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            </div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.materials.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
