@extends('admin.layout')

@section('title', 'Detail Berita')

@section('content')
<h1 style="margin-top:0;">{{ $news->title }}</h1>
<p class="muted">Slug: {{ $news->slug }}</p>
@if($news->cover_image)
    <img src="{{ asset('storage/'.$news->cover_image) }}" alt="cover" style="max-width:360px;border-radius:12px;border:1px solid #cbd5e1;">
@endif
<p>{{ $news->excerpt }}</p>
<div style="white-space:pre-line;">{{ $news->body }}</div>
<div class="actions" style="margin-top:14px;">
    <a class="btn btn-secondary" href="{{ route('admin.news.edit', $news) }}">Edit</a>
    <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Kembali</a>
</div>
@endsection
