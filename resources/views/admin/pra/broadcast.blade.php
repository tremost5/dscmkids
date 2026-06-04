@extends('admin.layout')

@section('title', 'Broadcast WA PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

@php
    $groupCount = $recipients->pluck('group')->filter()->unique()->count();
    $estimateSeconds = max(1, $recipients->count());
    $estimateText = $estimateSeconds < 60
        ? 'sekitar '.$estimateSeconds.' detik'
        : 'sekitar '.ceil($estimateSeconds / 60).' menit';
@endphp

<div class="grid-2">
    <section class="form-panel">
        <div class="section-head">
            <h2 class="section-title">Siapkan broadcast</h2>
            <p class="section-copy">Pilih penerima, tulis pesan, preview daftar nomor, lalu kirim melalui Fonnte.</p>
        </div>

        <form method="GET" id="broadcastPreviewForm">
            <div class="field">
                <label>Filter Peserta
                    <select name="filter" data-broadcast-filter-source>
                        <option value="all" @selected($filter === 'all')>Semua Peserta</option>
                        <option value="grup-1" @selected($filter === 'grup-1')>Grup 1</option>
                        <option value="grup-2" @selected($filter === 'grup-2')>Grup 2</option>
                        <option value="unpaid" @selected($filter === 'unpaid')>Belum Bayar</option>
                        <option value="pending_verification" @selected($filter === 'pending_verification')>Menunggu Verifikasi</option>
                        <option value="paid" @selected($filter === 'paid')>Lunas</option>
                    </select>
                </label>
            </div>
            <div class="field">
                <label>Pesan
                    <textarea name="message" placeholder="Shalom Bapak/Ibu, berikut informasi PRA 2026..." data-broadcast-message-source>{{ $message }}</textarea>
                </label>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.pra.broadcast.send') }}" id="broadcastSendForm" data-loading-form data-broadcast-send-form data-broadcast-count="{{ $recipients->count() }}">
            @csrf
            <input type="hidden" name="filter" value="{{ $filter }}" data-broadcast-filter-field>
            <input type="hidden" name="message" value="{{ $message }}" data-broadcast-message-field>
        </form>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit" form="broadcastPreviewForm">Preview Penerima</button>
            <button class="btn btn-success" type="submit" form="broadcastSendForm">Kirim Broadcast WA</button>
        </div>
    </section>
    <section class="table-shell broadcast-preview-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Preview Penerima</h2>
                <p class="section-copy">Nomor sudah dinormalisasi ke format internasional.</p>
            </div>
        </div>

        <div class="broadcast-summary-grid">
            <div class="broadcast-summary-card"><span>Total Penerima</span><strong>{{ $recipients->count() }}</strong></div>
            <div class="broadcast-summary-card"><span>Grup Terlibat</span><strong>{{ $groupCount }}</strong></div>
            <div class="broadcast-summary-card"><span>Estimasi Kirim</span><strong>{{ $estimateText }}</strong></div>
        </div>

        <div class="broadcast-recipient-list">
            @forelse($recipients as $recipient)
                <article class="broadcast-recipient-card">
                    <span class="broadcast-recipient-check">✓</span>
                    <div>
                        <strong>{{ $recipient['name'] }}</strong>
                        <span>{{ $recipient['child'] }}</span>
                        <code>{{ $recipient['phone'] }}</code>
                        <em>{{ $recipient['group'] ?: 'Tanpa grup' }}</em>
                    </div>
                </article>
            @empty
                <div class="empty-state">Tidak ada penerima untuk filter ini.</div>
            @endforelse
        </div>
    </section>
</div>

@if(is_array($broadcastResult))
    <section class="form-panel" style="margin-top:16px;">
        <div class="section-head">
            <h2 class="section-title">Ringkasan Broadcast</h2>
            <p class="section-copy">Hasil pengiriman terakhir untuk filter aktif.</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-label">Total Penerima</div><strong>{{ $broadcastResult['total'] ?? 0 }}</strong></div>
            <div class="stat-card"><div class="stat-label">Berhasil</div><strong>{{ $broadcastResult['success'] ?? 0 }}</strong></div>
            <div class="stat-card"><div class="stat-label">Gagal</div><strong>{{ $broadcastResult['failed'] ?? 0 }}</strong></div>
        </div>
    </section>
@endif

<div class="admin-modal" data-broadcast-confirm-modal hidden>
    <div class="admin-modal-card">
        <h2>Kirim Broadcast WA?</h2>
        <p>Kirim pesan ini ke <strong data-broadcast-confirm-count>{{ $recipients->count() }}</strong> penerima?</p>
        <div class="form-actions">
            <button class="btn btn-secondary" type="button" data-broadcast-cancel>Batal</button>
            <button class="btn btn-success" type="button" data-broadcast-confirm>Kirim Sekarang</button>
        </div>
    </div>
</div>
@endsection
