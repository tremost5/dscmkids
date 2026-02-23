@extends('admin.layout')

@section('title', 'Edit Section')

@section('content')
<h1 style="margin-top:0;">Edit Section</h1>
<form method="POST" action="{{ route('admin.sections.update', $section) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Section Key<input name="section_key" value="{{ old('section_key', $section->section_key) }}" required></label></div>
    <div class="field"><label>Title<input name="title" value="{{ old('title', $section->title) }}"></label></div>
    <div class="field"><label>Content<textarea name="content">{{ old('content', $section->content) }}</textarea></label></div>
    <div class="field"><label>Meta JSON<textarea name="meta">{{ old('meta', $section->meta ? json_encode($section->meta, JSON_PRETTY_PRINT) : '') }}</textarea></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Batal</a>
    </div>
</form>
@endsection
