@extends('admin.layout')

@section('title', 'Pendamping PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Pendamping</div><strong>{{ $stats['total'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><strong>{{ $stats['unpaid'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Menunggu Verifikasi</div><strong>{{ $stats['pending'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><strong>{{ $stats['paid'] }}</strong></div>
</div>

<div class="toolbar" style="margin-top:16px;">
    <form method="GET" class="toolbar-actions">
        <input
            type="text"
            name="search"
            class="input-compact"
            placeholder="Cari pendamping, WA, murid..."
            value="{{ $search }}"
            style="min-width:280px;">

        <select name="filter" class="select-compact">
            <option value="all" @selected($filter === 'all')>Semua</option>
            <option value="unpaid" @selected($filter === 'unpaid')>Belum Bayar</option>
            <option value="pending_verification" @selected($filter === 'pending_verification')>Menunggu Verifikasi</option>
            <option value="paid" @selected($filter === 'paid')>Lunas</option>
        </select>

        <button class="btn btn-primary" type="submit">Filter</button>
    </form>

    <div class="toolbar-actions">
        <a class="btn btn-secondary" href="{{ route('admin.pra.export-companions') }}">Export Pendamping</a>
        <a class="btn btn-secondary" href="{{ route('events.pra-2026.pendamping') }}" target="_blank" rel="noopener">Lihat Form</a>
    </div>
</div>

<section class="table-shell">
    <div class="table-scroller">
        <table>
            <thead>
            <tr>
                <th>Nama Pendamping</th>
                <th>WhatsApp</th>
                <th>Murid</th>
                <th>Tanggal Kehadiran</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($companions as $companion)
                <tr>
                    <td><strong>{{ $companion->companion_name }}</strong></td>
                    <td>{{ $companion->whatsapp_number }}</td>
                    <td>
                        <div class="toolbar-actions" style="flex-wrap:wrap; gap:8px;">
                            @forelse($companion->registrations as $student)
                                <a class="btn btn-secondary" href="{{ route('admin.pra.participants.show', $student) }}">
                                    {{ $student->nickname }}
                                </a>
                            @empty
                                -
                            @endforelse
                        </div>
                    </td>
                    <td>{{ $companion->attendanceDatesLabel() }}</td>
                    <td>{{ $companion->paymentMethodLabel() }}</td>
                    <td>{{ $companion->paymentStatusLabel() }}</td>
                    <td>{{ optional($companion->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-state">Belum ada pendamping.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $companions->links() }}
</section>

<script>
let companionSearchTimer;

document
    .querySelector('input[name="search"]')
    ?.addEventListener('keyup', function () {
        clearTimeout(companionSearchTimer);

        companionSearchTimer = setTimeout(() => {
            this.form?.submit();
        }, 400);
    });
</script>
@endsection
