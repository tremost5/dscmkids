@extends('admin.layout')

@section('title', 'Tambah Informasi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Buat informasi baru</h1>
            <p class="muted">Masukkan announcement dengan tanggal acara, lokasi, dan status tampil.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.announcements.store') }}" class="form-shell" data-loading-form>
        @csrf
        <section class="form-panel">
            <div class="section-head">
                <h2 class="section-title">Announcement details</h2>
                <p class="section-copy">Teks ini akan muncul pada modul informasi publik.</p>
            </div>
            <div class="field"><label>Judul<input name="title" value="{{ old('title') }}" required></label></div>
            <div class="field"><label>Isi<textarea name="body" required>{{ old('body') }}</textarea></label></div>
            <div class="grid-2">
                <div class="field"><label>Tanggal Acara<input type="date" name="event_date" value="{{ old('event_date') }}"></label></div>
                <div class="field"><label>Lokasi<input name="location" value="{{ old('location') }}"></label></div>
            </div>
            <label class="toggle-field"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
