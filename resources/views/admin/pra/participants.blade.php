@extends('admin.layout')

@section('title', 'Peserta PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="toolbar">
    <form method="GET" class="toolbar-actions">
        <select name="filter" class="select-compact">
            <option value="">Semua</option>
            <option value="grup-1" @selected($filter === 'grup-1')>Grup 1</option>
            <option value="grup-2" @selected($filter === 'grup-2')>Grup 2</option>
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <div class="toolbar-actions">
        <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'all']) }}">Export Excel Semua Peserta</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'group-1']) }}">Export Excel Grup 1</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'group-2']) }}">Export Excel Grup 2</a>
    </div>
</div>

<section class="table-shell">
    <div class="table-scroller">
        <table>
            <thead>
            <tr><th>Nama</th><th>Nama Panggilan</th><th>Kelas</th><th>Grup</th><th>Asal Sekolah Minggu</th><th>Metode Pembayaran</th><th>Status Pembayaran</th><th>Tanggal Daftar</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($registrations as $registration)
                <tr>
                    <td>{{ $registration->full_name }}</td>
                    <td>{{ $registration->nickname }}</td>
                    <td>{{ $registration->class_before }}</td>
                    <td>{{ $registration->group?->name }}</td>
                    <td>{{ $registration->church_branch }}</td>
                    <td>{{ ucfirst($registration->payment_method) }}</td>
                    <td>{{ $registration->paymentStatusLabel() }}</td>
                    <td>{{ optional($registration->registered_at)->format('d M Y H:i') }}</td>
                    <td><a class="btn btn-secondary" href="{{ route('admin.pra.participants.show', $registration) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty-state">Belum ada peserta.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registrations->links() }}
</section>
@endsection
