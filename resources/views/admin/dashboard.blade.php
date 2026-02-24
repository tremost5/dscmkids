@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
<h1 style="margin-top:0;">Dashboard</h1>
<p class="muted">Ringkasan konten website DSCMKids.</p>

<div class="grid-2" style="margin:12px 0 16px;">
    <div class="card"><strong>{{ $newsCount }}</strong><br><span class="muted">Total Berita</span></div>
    <div class="card"><strong>{{ $announcementCount }}</strong><br><span class="muted">Total Informasi</span></div>
    <div class="card"><strong>{{ $sectionCount }}</strong><br><span class="muted">Konten Section</span></div>
    <div class="card"><strong>{{ $mediaCount }}</strong><br><span class="muted">File Media</span></div>
    <div class="card"><strong>{{ $slideCount }}</strong><br><span class="muted">Slide Header</span></div>
    <div class="card"><strong>{{ $teacherCount }}</strong><br><span class="muted">Portfolio Guru</span></div>
    <div class="card"><strong>{{ $materialCount }}</strong><br><span class="muted">Materi Edukatif</span></div>
    <div class="card"><strong>{{ $studentCount }}</strong><br><span class="muted">Akun Murid</span></div>
    <div class="card"><strong>{{ number_format($retentionRate, 1) }}%</strong><br><span class="muted">Retention 7 Hari</span></div>
    <div class="card"><strong>{{ number_format($conversionRate, 1) }}%</strong><br><span class="muted">Conversion Testimoni/Murid</span></div>
    <div class="card"><strong>{{ $arcadeTodayCount }}</strong><br><span class="muted">Aktivitas Arcade Hari Ini</span></div>
</div>

<h3>Aktivitas Quiz Hari Ini per Jam</h3>
<table>
    <thead><tr><th>Jam</th><th>Total Aktivitas</th></tr></thead>
    <tbody>
    @forelse($hourlyQuiz as $row)
        <tr><td>{{ $row['hour'] }}</td><td>{{ $row['total'] }}</td></tr>
    @empty
        <tr><td colspan="2" class="muted">Belum ada aktivitas quiz hari ini.</td></tr>
    @endforelse
    </tbody>
</table>

<h3 style="margin-top:16px;">Engagement Kelas Minggu Ini</h3>
<table>
    <thead><tr><th>Kelas</th><th>Total Skor</th><th>Attempt</th></tr></thead>
    <tbody>
    @forelse($topClasses as $row)
        <tr><td>{{ $row->class_group }}</td><td>{{ (int) $row->total_score }}</td><td>{{ (int) $row->attempts }}</td></tr>
    @empty
        <tr><td colspan="3" class="muted">Belum ada data engagement kelas.</td></tr>
    @endforelse
    </tbody>
</table>

<h3>Berita Terbaru</h3>
<table>
    <thead>
    <tr>
        <th>Judul</th>
        <th>Status</th>
        <th>Tanggal</th>
    </tr>
    </thead>
    <tbody>
    @forelse($recentNews as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td>{{ $item->is_published ? 'Published' : 'Draft' }}</td>
            <td>{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="muted">Belum ada berita.</td></tr>
    @endforelse
    </tbody>
</table>

<h3 style="margin-top:16px;">Audit Admin Terbaru</h3>
<table>
    <thead><tr><th>Waktu</th><th>Method</th><th>Path</th><th>IP</th></tr></thead>
    <tbody>
    @forelse($recentAdminActivities as $log)
        <tr>
            <td>{{ optional($log->created_at)->format('d M H:i') }}</td>
            <td>{{ $log->method }}</td>
            <td>{{ $log->path }}</td>
            <td>{{ $log->ip_address ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Audit log belum tersedia (jalankan migrasi).</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
