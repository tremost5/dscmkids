@extends('admin.layout')

@section('title', 'Tambah Section')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tambah section</h1>
            <p class="muted">Buat blok konten baru untuk landing page atau konfigurasi sistem.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.sections.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="field"><label>Section Key<input name="section_key" value="{{ old('section_key') }}" required></label></div>
            <div class="field"><label>Title<input name="title" value="{{ old('title') }}"></label></div>
            <div class="field"><label>Content<textarea name="content">{{ old('content') }}</textarea></label></div>
            <div class="field"><label>Meta JSON<textarea name="meta" placeholder='{"button_text":"Daftar"}'>{{ old('meta') }}</textarea></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
