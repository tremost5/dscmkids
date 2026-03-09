@extends('admin.layout')

@section('title', 'Detail Informasi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $announcement->title }}</h1>
            <p class="muted">{{ optional($announcement->event_date)->format('d M Y') ?? '-' }}{{ $announcement->location ? ' · '.$announcement->location : '' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.announcements.edit', $announcement) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.announcements.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            <div class="section-head">
                <h2 class="section-title">Isi informasi</h2>
            </div>
            <div class="detail-prose">{{ $announcement->body }}</div>
        </section>

        <aside class="detail-panel detail-card">
            <div class="section-head">
                <h2 class="section-title">Ringkasan</h2>
            </div>
            <div class="detail-kv">
                <div class="detail-kv-item"><span>Status</span><strong>{{ $announcement->is_active ? 'Aktif' : 'Nonaktif' }}</strong></div>
                <div class="detail-kv-item"><span>Tanggal</span><strong>{{ optional($announcement->event_date)->format('d M Y') ?? '-' }}</strong></div>
                <div class="detail-kv-item"><span>Lokasi</span><strong>{{ $announcement->location ?: '-' }}</strong></div>
            </div>
        </aside>
    </div>
</div>
@endsection
