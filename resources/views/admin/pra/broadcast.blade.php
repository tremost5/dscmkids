@extends('admin.layout')

@section('title', 'Broadcast WA PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

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
    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">{{ $recipients->count() }} penerima</h2>
                <p class="section-copy">Data nomor sudah dinormalisasi ke format internasional jika memungkinkan.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Orang Tua</th><th>Anak</th><th>WA</th><th>Grup</th></tr></thead>
                <tbody>
                @forelse($recipients as $recipient)
                    <tr>
                        <td>{{ $recipient['name'] }}</td>
                        <td>{{ $recipient['child'] }}</td>
                        <td>{{ $recipient['phone'] }}</td>
                        <td>{{ $recipient['group'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Tidak ada penerima untuk filter ini.</td></tr>
                @endforelse
                </tbody>
            </table>
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

@if($message !== '')
    <section class="form-panel" style="margin-top:16px;">
        <h2 class="section-title">Payload siap integrasi</h2>
        <pre class="code-block">{{ json_encode(['filter' => $filter, 'message' => $message, 'recipients' => $recipients], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </section>
@endif
@endsection
