@extends('admin.layout')

@section('title', 'Detail Informasi')

@section('content')
<h1 style="margin-top:0;">{{ $announcement->title }}</h1>
<p class="muted">{{ optional($announcement->event_date)->format('d M Y') ?? '-' }} {{ $announcement->location ? '- '.$announcement->location : '' }}</p>
<div style="white-space:pre-line;">{{ $announcement->body }}</div>
<div class="actions" style="margin-top:14px;">
    <a class="btn btn-secondary" href="{{ route('admin.announcements.edit', $announcement) }}">Edit</a>
    <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">Kembali</a>
</div>
@endsection
