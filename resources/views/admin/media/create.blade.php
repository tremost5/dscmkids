@extends('admin.layout')

@section('title', 'Upload Media')

@section('content')
<h1 style="margin-top:0;">Upload Media</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.store') }}">
    @csrf
    <div class="field"><label>Judul File<input name="title" value="{{ old('title') }}" required></label></div>
    <div class="field"><label>File<input type="file" name="file" required></label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Upload</button>
        <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Batal</a>
    </div>
</form>
@endsection
