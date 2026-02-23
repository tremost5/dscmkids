@extends('admin.layout')

@section('title', 'Detail Media')

@section('content')
<h1 style="margin-top:0;">{{ $media->title }}</h1>
<p class="muted">{{ $media->mime_type ?? '-' }} | {{ $media->file_size ? number_format($media->file_size/1024, 1).' KB' : '-' }}</p>
<p><a class="btn btn-secondary" href="{{ asset('storage/'.$media->file_path) }}" target="_blank">Buka File</a></p>
@if(str_starts_with($media->mime_type ?? '', 'image/'))
    <img src="{{ asset('storage/'.$media->file_path) }}" alt="{{ $media->title }}" style="max-width:420px;border-radius:12px;border:1px solid #cbd5e1;">
@endif
<div class="actions" style="margin-top:14px;">
    <a class="btn btn-secondary" href="{{ route('admin.media.edit', $media) }}">Edit</a>
    <a class="btn btn-secondary" href="{{ route('admin.media.index') }}">Kembali</a>
</div>
@endsection
