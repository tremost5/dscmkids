@extends('admin.layout')

@section('title', 'Edit Informasi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Edit informasi</h1>
            <p class="muted">Perbarui isi, jadwal, dan visibilitas informasi.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">Announcement details</h2>
                <p class="section-copy">Perubahan akan langsung tercermin pada area informasi publik.</p>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title', $announcement->title) }}" required></label></div>
            <div class="field"><label>Isi<textarea name="body" required>{{ old('body', $announcement->body) }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Tanggal Acara<input type="date" name="event_date" value="{{ old('event_date', optional($announcement->event_date)->format('Y-m-d')) }}"></label></div>
                <div class="field"><label>Lokasi<input name="location" value="{{ old('location', $announcement->location) }}"></label></div>
            </div>
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}> Aktif</label>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
