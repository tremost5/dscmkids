@extends('admin.layout')

@section('title', 'Edit Informasi')

@section('content')
<h1 style="margin-top:0;">Edit Informasi</h1>
<form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
    @csrf
    @method('PUT')
    <div class="field"><label>Judul<input name="title" value="{{ old('title', $announcement->title) }}" required></label></div>
    <div class="field"><label>Isi<textarea name="body" required>{{ old('body', $announcement->body) }}</textarea></label></div>
    <div class="grid-2">
        <div class="field"><label>Tanggal Acara<input type="date" name="event_date" value="{{ old('event_date', optional($announcement->event_date)->format('Y-m-d')) }}"></label></div>
        <div class="field"><label>Lokasi<input name="location" value="{{ old('location', $announcement->location) }}"></label></div>
    </div>
    <div class="field"><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }} style="width:auto;"> Aktif</label></div>
    <div class="actions">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">Batal</a>
    </div>
</form>
@endsection
