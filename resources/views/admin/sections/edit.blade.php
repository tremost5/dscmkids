@extends('admin.layout')

@section('title', 'Edit Section')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit section</h1>
            <p class="muted">Perbarui blok konten atau meta JSON untuk section yang ada.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="field"><label>Section Key<input name="section_key" value="{{ old('section_key', $section->section_key) }}" required></label></div>
            <div class="field"><label>Title<input name="title" value="{{ old('title', $section->title) }}"></label></div>
            <div class="field"><label>Content<textarea name="content">{{ old('content', $section->content) }}</textarea></label></div>
            <div class="field"><label>Meta JSON<textarea name="meta">{{ old('meta', $section->meta ? json_encode($section->meta, JSON_PRETTY_PRINT) : '') }}</textarea></label></div>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
