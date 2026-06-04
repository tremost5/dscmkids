@extends('admin.layout')

@section('title', 'Pembayaran PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><strong>{{ $stats['unpaid'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Menunggu Verifikasi</div><strong>{{ $stats['pending'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><strong>{{ $stats['paid'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Total Peserta</div><strong>{{ $stats['total'] }}</strong></div>
</div>

<div class="toolbar" style="margin-top:16px;">
    <form method="GET" class="toolbar-actions">
        <select name="filter" class="select-compact">
            <option value="">Semua</option>
            <option value="unpaid" @selected($filter === 'unpaid')>Belum Bayar</option>
            <option value="pending_verification" @selected($filter === 'pending_verification')>Menunggu Verifikasi</option>
            <option value="paid" @selected($filter === 'paid')>Lunas</option>
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'payments']) }}">Export Pembayaran</a>
</div>

<section class="table-shell">
    <div class="table-scroller">
        <table>
            <thead><tr><th>Nama</th><th>Grup</th><th>Metode</th><th>Bukti</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($registrations as $registration)
                <tr>
                    <td><strong>{{ $registration->full_name }}</strong><br><span class="muted">{{ $registration->parent_name }} - {{ $registration->whatsapp_number }}</span></td>
                    <td>{{ $registration->group?->name }}</td>
                    <td>{{ ucfirst($registration->payment_method) }}</td>
                    <td>
                        @if($registration->paymentProof)
                            <a href="{{ asset('storage/'.$registration->paymentProof->file_path) }}" target="_blank" rel="noopener">Lihat bukti</a>
                        @else
                            <span class="muted">Tidak ada upload</span>
                        @endif
                    </td>
                    <td>{{ $registration->paymentStatusLabel() }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.pra.payments.update', $registration) }}" class="inline-form">
                            @csrf
                            @method('PATCH')
                            <select name="payment_status">
                                <option value="unpaid" @selected($registration->payment_status === 'unpaid')>Belum Bayar</option>
                                <option value="pending_verification" @selected($registration->payment_status === 'pending_verification')>Menunggu Verifikasi</option>
                                <option value="paid" @selected($registration->payment_status === 'paid')>Lunas</option>
                            </select>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-state">Belum ada data pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registrations->links() }}
</section>
@endsection
