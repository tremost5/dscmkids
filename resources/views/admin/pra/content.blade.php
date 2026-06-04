@extends('admin.layout')

@section('title', 'Konten Landing Page PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

@php
    $paymentInfo = is_array($event->payment_info) ? $event->payment_info : [];
    $benefitsText = implode("\n", $event->benefits ?: []);
    $scheduleText = collect($event->schedule ?: [])->map(fn ($item) => ($item['time'] ?? '').' | '.($item['title'] ?? '').' | '.($item['description'] ?? ''))->implode("\n");
    $contactsText = collect($paymentInfo['contacts'] ?? [])->map(fn ($item) => ($item['name'] ?? '').' | '.($item['phone'] ?? ''))->implode("\n");
@endphp

<form method="POST" action="{{ route('admin.pra.content.update') }}" enctype="multipart/form-data" class="form-shell">
    @csrf
    @method('PUT')
    <section class="form-panel">
        <div class="section-head"><h2 class="section-title">Banner Event</h2><p class="section-copy">Judul, subjudul, splash popup, dan background banner.</p></div>
        <div class="grid-2">
            <label>Judul Event<input name="banner_title" value="{{ old('banner_title', $banner?->title ?: $event->title) }}" required></label>
            <label>Subjudul Event<input name="banner_subtitle" value="{{ old('banner_subtitle', $banner?->subtitle ?: $event->subtitle) }}"></label>
            <label>Splash Title<input name="splash_title" value="{{ old('splash_title', $banner?->splash_title ?: 'DAFTAR PRA 2026') }}"></label>
            <label>Splash Subtitle<input name="splash_subtitle" value="{{ old('splash_subtitle', $banner?->splash_subtitle) }}"></label>
            <label>Background Banner<input type="file" name="background_image" accept="image/*"></label>
        </div>
    </section>

    <section class="form-panel">
        <div class="section-head"><h2 class="section-title">Konten Landing Page</h2><p class="section-copy">Tentang PRA, lokasi, benefit, jadwal, dan pembayaran tanpa edit kode.</p></div>
        <div class="grid-2">
            <label>Nama Event<input name="title" value="{{ old('title', $event->title) }}" required></label>
            <label>Subjudul<input name="subtitle" value="{{ old('subtitle', $event->subtitle) }}"></label>
        </div>
        <div class="field"><label>Tentang PRA<textarea name="description">{{ old('description', $event->description) }}</textarea></label></div>
        <div class="grid-2">
            <label>Nama Lokasi<input name="location_name" value="{{ old('location_name', $event->location_name) }}"></label>
            <label>Benefit, satu item per baris<textarea name="benefits">{{ old('benefits', $benefitsText) }}</textarea></label>
        </div>
        <div class="field"><label>Deskripsi Lokasi<textarea name="location_description">{{ old('location_description', $event->location_description) }}</textarea></label></div>
        <div class="field"><label>Jadwal, format: Waktu | Judul | Deskripsi<textarea name="schedule">{{ old('schedule', $scheduleText) }}</textarea></label></div>
        <div class="grid-2">
            <label>Catatan Transfer<textarea name="transfer_note">{{ old('transfer_note', $paymentInfo['transfer_note'] ?? '') }}</textarea></label>
            <label>Catatan Cash<textarea name="cash_note">{{ old('cash_note', $paymentInfo['cash_note'] ?? '') }}</textarea></label>
        </div>
        <div class="field"><label>Kontak Pembayaran, format: Nama | Nomor<textarea name="payment_contacts">{{ old('payment_contacts', $contactsText) }}</textarea></label></div>
        <button class="btn btn-primary" type="submit">Simpan Konten</button>
    </section>
</form>

<div class="grid-2" style="margin-top:16px;">
    <section class="form-panel">
        <h2 class="section-title">Galeri Foto Lokasi</h2>
        <form method="POST" action="{{ route('admin.pra.galleries.store') }}" enctype="multipart/form-data" class="form-shell">
            @csrf
            <label>Judul Foto<input name="title"></label>
            <label>Foto Lokasi<input type="file" name="image" accept="image/*" required></label>
            <label>Urutan<input type="number" name="sort_order" value="0" min="0"></label>
            <button class="btn btn-primary" type="submit">Tambah Foto</button>
        </form>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Foto</th><th>Judul</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($event->galleries as $photo)
                    <tr>
                        <td><img class="thumb" src="{{ asset('storage/'.$photo->image_path) }}" alt="{{ $photo->title }}"></td>
                        <td>{{ $photo->title ?: '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.pra.galleries.destroy', $photo) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">Belum ada foto.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="form-panel">
        <h2 class="section-title">Video Lokasi</h2>
        <form method="POST" action="{{ route('admin.pra.videos.store') }}" enctype="multipart/form-data" class="form-shell">
            @csrf
            <label>Judul Video<input name="title"></label>
            <label>Link Video<input name="video_url" placeholder="https://..."></label>
            <label>Upload Video<input type="file" name="video_file" accept="video/*"></label>
            <label>Deskripsi<textarea name="description"></textarea></label>
            <label>Urutan<input type="number" name="sort_order" value="0" min="0"></label>
            <button class="btn btn-primary" type="submit">Tambah Video</button>
        </form>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Judul</th><th>Sumber</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($event->videos as $video)
                    <tr>
                        <td>{{ $video->title ?: '-' }}</td>
                        <td>{{ $video->video_url ?: ($video->video_path ? 'Upload file' : '-') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.pra.videos.destroy', $video) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">Belum ada video.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
