@extends('admin.layout')

@section('title', 'Detail Slide')

@section('content')
<h1 style="margin-top:0;">{{ $slide->title ?? 'Tanpa Judul' }}</h1>
<img src="{{ asset('storage/'.$slide->image_path) }}" alt="slide" style="width:100%;max-width:620px;border-radius:12px;">
<p>{{ $slide->subtitle }}</p>
<p class="muted">Urutan: {{ $slide->sort_order }} | {{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</p>
<div class="actions"><a class="btn btn-secondary" href="{{ route('admin.slides.edit', $slide) }}">Edit</a><a class="btn btn-secondary" href="{{ route('admin.slides.index') }}">Kembali</a></div>
@endsection
