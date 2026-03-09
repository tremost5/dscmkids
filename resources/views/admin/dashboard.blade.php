@extends('admin.layout')

@section('title', 'Dashboard Admin')

@php
    $alertCount = (int) ($systemHealth['queues']['failed_jobs'] > 0)
        + (int) ($systemHealth['broadcasts']['failed'] > 0)
        + (int) ($systemHealth['broadcasts']['pending'] > 0);
@endphp

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Operational dashboard</h1>
            <p class="muted">Ringkasan performa konten, murid, queue, dan aktivitas admin dalam satu workspace modern.</p>
        </div>
        <div class="toolbar-actions">
            @if(auth()->user()?->hasPermission('users.manage'))
                <a class="btn btn-primary" href="{{ route('admin.users.index') }}">Kelola Users</a>
            @endif
            @if(auth()->user()?->hasPermission('monitoring.view'))
                <a class="btn btn-secondary" href="{{ route('admin.system.index') }}">System Monitor</a>
            @endif
            @if(auth()->user()?->hasPermission('api.admin'))
                <a class="btn btn-secondary" href="{{ url('/api/v1/admin/metrics') }}" target="_blank" rel="noopener">Open Admin API</a>
            @endif
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total berita</div>
            <strong>{{ $newsCount }}</strong>
            <div class="stat-trend">{{ $recentNews->count() }} item terbaru siap dipantau</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Aktivitas arcade hari ini</div>
            <strong>{{ $arcadeTodayCount }}</strong>
            <div class="stat-trend">{{ number_format($retentionRate, 1) }}% retention 7 hari</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Admin aktif</div>
            <strong>{{ $activeAdminCount }}</strong>
            <div class="stat-trend">{{ $inactiveUserCount }} akun nonaktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending queue jobs</div>
            <strong>{{ $systemHealth['queues']['pending_jobs'] }}</strong>
            <div class="stat-trend">{{ $systemHealth['queues']['failed_jobs'] }} gagal, {{ $alertCount }} alert aktif</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="stack">
            <section class="surface-panel surface-panel--tinted">
                <div class="section-head">
                    <h2 class="section-title">Platform inventory</h2>
                    <p class="section-copy">Konten dan data inti yang saat ini menopang website.</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Informasi</div>
                        <strong>{{ $announcementCount }}</strong>
                        <div class="stat-trend">Announcement items aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Section konten</div>
                        <strong>{{ $sectionCount }}</strong>
                        <div class="stat-trend">Landing + static content blocks</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Media assets</div>
                        <strong>{{ $mediaCount }}</strong>
                        <div class="stat-trend">Gallery, hero, attachment</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Slides & guru</div>
                        <strong>{{ $slideCount + $teacherCount }}</strong>
                        <div class="stat-trend">{{ $slideCount }} slide, {{ $teacherCount }} guru</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Materi edukatif</div>
                        <strong>{{ $materialCount }}</strong>
                        <div class="stat-trend">{{ $studentCount }} akun murid terdaftar</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Conversion</div>
                        <strong>{{ number_format($conversionRate, 1) }}%</strong>
                        <div class="stat-trend">{{ $inactiveStudents7d }} murid belum aktif 7 hari</div>
                    </div>
                </div>
            </section>

            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Quiz activity by hour</h2>
                        <p class="section-copy">Deteksi jam sibuk penggunaan arcade dan kuis harian.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead>
                        <tr><th>Jam</th><th>Total Aktivitas</th></tr>
                        </thead>
                        <tbody>
                        @forelse($hourlyQuiz as $row)
                            <tr><td>{{ $row['hour'] }}</td><td>{{ $row['total'] }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">Belum ada aktivitas quiz hari ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Class engagement</h2>
                        <p class="section-copy">Performa skor dan percobaan per kelas minggu ini.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead>
                        <tr><th>Kelas</th><th>Total Skor</th><th>Attempt</th></tr>
                        </thead>
                        <tbody>
                        @forelse($topClasses as $row)
                            <tr><td>{{ $row->class_group }}</td><td>{{ (int) $row->total_score }}</td><td>{{ (int) $row->attempts }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">Belum ada data engagement kelas.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="surface-panel surface-panel--accent">
                <div class="section-head">
                    <h2 class="section-title">System alerts</h2>
                    <p class="section-copy">Sinyal kesehatan platform yang perlu tindakan cepat.</p>
                </div>
                <div class="metric-row">
                    <span>Queue failed jobs</span>
                    <span class="status-badge {{ $systemHealth['queues']['failed_jobs'] > 0 ? 'status-badge--warning' : 'status-badge--healthy' }}">
                        {{ $systemHealth['queues']['failed_jobs'] > 0 ? 'Warning' : 'Healthy' }} · {{ $systemHealth['queues']['failed_jobs'] }}
                    </span>
                </div>
                <div class="metric-row">
                    <span>Broadcast pending</span>
                    <span class="status-badge {{ $systemHealth['broadcasts']['pending'] > 0 ? 'status-badge--warning' : 'status-badge--healthy' }}">
                        {{ $systemHealth['broadcasts']['pending'] > 0 ? 'Watch' : 'Healthy' }} · {{ $systemHealth['broadcasts']['pending'] }}
                    </span>
                </div>
                <div class="metric-row">
                    <span>Broadcast failed</span>
                    <span class="status-badge {{ $systemHealth['broadcasts']['failed'] > 0 ? 'status-badge--warning' : 'status-badge--healthy' }}">
                        {{ $systemHealth['broadcasts']['failed'] > 0 ? 'Warning' : 'Healthy' }} · {{ $systemHealth['broadcasts']['failed'] }}
                    </span>
                </div>
                <div class="metric-row">
                    <span>Users online 15m</span>
                    <span class="metric-value">{{ $systemHealth['users']['online_users_15m'] }}</span>
                </div>
            </section>

            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Berita terbaru</h2>
                        <p class="section-copy">Konten terakhir yang masuk ke pipeline publikasi.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead>
                        <tr><th>Judul</th><th>Status</th><th>Tanggal</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recentNews as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="status-badge {{ $item->is_published ? 'status-badge--published' : 'status-badge--draft' }}">
                                        {{ $item->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">Belum ada berita.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Audit admin terbaru</h2>
                        <p class="section-copy">Jejak aktivitas request admin paling akhir.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead>
                        <tr><th>Waktu</th><th>Method</th><th>Path</th><th>IP</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recentAdminActivities as $log)
                            <tr>
                                <td>{{ optional($log->created_at)->format('d M H:i') }}</td>
                                <td>{{ $log->method }}</td>
                                <td>{{ $log->path }}</td>
                                <td>{{ $log->ip_address ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">Audit log belum tersedia.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
