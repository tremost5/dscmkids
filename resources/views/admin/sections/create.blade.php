@extends('admin.layout')

@section('title', 'Tambah Section')

@section('content')
<h1 style="margin-top:0;">Tambah Section</h1>
<form method="POST" action="{{ route('admin.sections.store') }}">
    @csrf
    <div class="field"><label>Section Key<input name="section_key" value="{{ old('section_key') }}" required></label></div>
    <div class="field"><label>Title<input name="title" value="{{ old('title') }}"></label></div>
    <div class="field"><label>Content<textarea name="content">{{ old('content') }}</textarea></label></div>
    <div class="field"><label>Meta JSON<textarea name="meta" placeholder='{"button_text":"Daftar"}'>{{ old('meta') }}</textarea></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Batal</a>
    </div>
</form>
@endsection
