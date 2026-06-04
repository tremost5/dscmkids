@extends('admin.layout')

@section('title', 'PRA 2026 Dashboard')

@section('content')
@include('admin.pra.partials.nav')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Peserta</div><strong>{{ $stats['total'] }}</strong><div class="stat-trend">Semua pendaftaran</div></div>
    <div class="stat-card"><div class="stat-label">Peserta Grup 1</div><strong>{{ $stats['group_1'] }}</strong><div class="stat-trend">PG-TKB, 1-4</div></div>
    <div class="stat-card"><div class="stat-label">Peserta Grup 2</div><strong>{{ $stats['group_2'] }}</strong><div class="stat-trend">Kelas 5-9</div></div>
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><strong>{{ $stats['unpaid'] }}</strong><div class="stat-trend">Cash atau belum konfirmasi</div></div>
    <div class="stat-card"><div class="stat-label">Menunggu Verifikasi</div><strong>{{ $stats['pending'] }}</strong><div class="stat-trend">Transfer masuk</div></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><strong>{{ $stats['paid'] }}</strong><div class="stat-trend">Sudah diverifikasi</div></div>
</div>

<section class="table-shell" style="margin-top:16px;">
    <div class="table-toolbar">
        <div>
            <h2 class="section-title">Peserta terbaru</h2>
            <p class="section-copy">Pendaftaran terakhir yang masuk ke PRA 2026.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'all']) }}">Export Semua Peserta</a>
            <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'group-1']) }}">Export Grup 1</a>
            <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'group-2']) }}">Export Grup 2</a>
            <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'payments']) }}">Export Pembayaran</a>
        </div>
    </div>
    <div class="table-scroller">
        <table>
            <thead><tr><th>Nama</th><th>Kelas</th><th>Grup</th><th>Pembayaran</th><th>Tanggal Daftar</th></tr></thead>
            <tbody>
            @forelse($recentRegistrations as $registration)
                <tr>
                    <td><strong>{{ $registration->full_name }}</strong><br><span class="muted">{{ $registration->nickname }}</span></td>
                    <td>{{ $registration->class_before }}</td>
                    <td>{{ $registration->group?->name }}</td>
                    <td><span class="status-badge status-badge--{{ $registration->payment_status === 'paid' ? 'healthy' : ($registration->payment_status === 'pending_verification' ? 'warning' : 'draft') }}">{{ $registration->paymentStatusLabel() }}</span></td>
                    <td>{{ optional($registration->registered_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-state">Belum ada peserta.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
