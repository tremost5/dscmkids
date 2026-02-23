@extends('admin.layout')

@section('title', 'Detail Section')

@section('content')
<h1 style="margin-top:0;">{{ $section->section_key }}</h1>
<p><strong>{{ $section->title }}</strong></p>
<div style="white-space:pre-line;">{{ $section->content }}</div>
@if($section->meta)
    <h3>Meta</h3>
    <pre>{{ json_encode($section->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
@endif
<div class="actions" style="margin-top:14px;">
    <a class="btn btn-secondary" href="{{ route('admin.sections.edit', $section) }}">Edit</a>
    <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Kembali</a>
</div>
@endsection
