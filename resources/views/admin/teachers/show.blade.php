@extends('admin.layout')

@section('title', 'Detail Guru')

@section('content')
<h1 style="margin-top:0;">{{ $teacher->name }}</h1>
@if($teacher->photo_path)
    <img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="{{ $teacher->name }}" style="width:130px;height:130px;object-fit:cover;border-radius:50%;">
@endif
<p><strong>{{ $teacher->role }}</strong> {{ $teacher->class_group ? ' - '.$teacher->class_group : '' }}</p>
<p>{{ $teacher->bio }}</p>
@if($teacher->instagram_url)
    <p><a href="{{ $teacher->instagram_url }}" target="_blank">Instagram</a></p>
@endif
<div class="actions"><a class="btn btn-secondary" href="{{ route('admin.teachers.edit', $teacher) }}">Edit</a><a class="btn btn-secondary" href="{{ route('admin.teachers.index') }}">Kembali</a></div>
@endsection
