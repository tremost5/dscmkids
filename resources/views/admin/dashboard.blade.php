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
</div>

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
@endsection
