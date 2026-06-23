@extends('admin.layout')

@section('title', 'Kehadiran PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Peserta</div><strong>{{ $stats['total'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Sudah Hadir</div><strong>{{ $stats['present'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Belum Hadir</div><strong>{{ $stats['absent'] }}</strong></div>
</div>

<div class="toolbar" style="margin-top:16px;">
    <form method="GET" class="toolbar-actions">
        <input
            type="text"
            name="search"
            class="input-compact"
            placeholder="Cari nama, ortu, WA, kelas..."
            value="{{ $search ?? '' }}"
            style="min-width:280px;">

        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
</div>

<section class="table-shell" style="margin-top:16px;">
    <div class="table-scroller">
        <table>
            <thead><tr><th>Nama</th><th>Kelas</th><th>Grup</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($registrations as $registration)
                <tr>
                    <td><strong>{{ $registration->full_name }}</strong><br><span class="muted">{{ $registration->nickname }}</span></td>
                    <td>{{ $registration->class_before }}</td>
                    <td>{{ $registration->group?->name }}</td>
                    <td>{{ $registration->attendanceStatusLabel() }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.pra.attendance.update', $registration) }}" class="toolbar-actions">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-primary" name="attendance_status" value="present" type="submit">Tandai Hadir</button>
                            <button class="btn btn-secondary" name="attendance_status" value="absent" type="submit">Belum Hadir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-state">Belum ada peserta.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registrations->links() }}
</section>
<script>
let attendanceSearchTimer;

document
    .querySelector('input[name="search"]')
    ?.addEventListener('keyup', function () {
        clearTimeout(attendanceSearchTimer);

        attendanceSearchTimer = setTimeout(() => {
            this.form?.submit();
        }, 400);
    });
</script>
@endsection
