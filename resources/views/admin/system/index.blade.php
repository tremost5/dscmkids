@extends('admin.layout')

@section('title', 'System Monitor')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>System monitor</h1>
            <p class="muted">Pantau queue, broadcast, integrasi, dan utilisasi user dari satu tampilan operasional yang ringkas.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Pending jobs</div>
            <strong>{{ $systemHealth['queues']['pending_jobs'] }}</strong>
            <div class="stat-trend">Backlog queue aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Failed jobs</div>
            <strong>{{ $systemHealth['queues']['failed_jobs'] }}</strong>
            <div class="stat-trend">Perlu retry atau investigasi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Users online 15m</div>
            <strong>{{ $systemHealth['users']['online_users_15m'] }}</strong>
            <div class="stat-trend">Aktivitas live terbaru</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending broadcasts</div>
            <strong>{{ $systemHealth['broadcasts']['pending'] }}</strong>
            <div class="stat-trend">{{ $systemHealth['broadcasts']['processing'] }} sedang diproses</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="stack">
            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Queue & broadcast status</h2>
                        <p class="section-copy">Ringkasan pipeline background job dan status delivery notification.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead><tr><th>Metric</th><th>Value</th></tr></thead>
                        <tbody>
                        <tr><td>Pending Jobs</td><td>{{ $systemHealth['queues']['pending_jobs'] }}</td></tr>
                        <tr><td>Failed Jobs</td><td>{{ $systemHealth['queues']['failed_jobs'] }}</td></tr>
                        <tr><td>Job Batches</td><td>{{ $systemHealth['queues']['job_batches'] }}</td></tr>
                        <tr><td>Pending Broadcasts</td><td>{{ $systemHealth['broadcasts']['pending'] }}</td></tr>
                        <tr><td>Processing Broadcasts</td><td>{{ $systemHealth['broadcasts']['processing'] }}</td></tr>
                        <tr><td>Failed Broadcasts</td><td>{{ $systemHealth['broadcasts']['failed'] }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="table-shell">
                <div class="table-toolbar">
                    <div>
                        <h2 class="section-title">Digest snapshot</h2>
                        <p class="section-copy">Ringkasan otomatis yang dipakai untuk daily admin digest.</p>
                    </div>
                </div>
                <div class="table-scroller">
                    <table>
                        <thead><tr><th>Item</th><th>Value</th></tr></thead>
                        <tbody>
                        <tr><td>Generated At</td><td>{{ $digest['generated_at'] }}</td></tr>
                        <tr><td>Retention Rate</td><td>{{ $digest['retention_rate'] }}%</td></tr>
                        <tr><td>Inactive Students 7d</td><td>{{ $digest['inactive_students_7d'] }}</td></tr>
                        <tr><td>Pending Jobs</td><td>{{ $digest['pending_jobs'] }}</td></tr>
                        <tr><td>Failed Jobs</td><td>{{ $digest['failed_jobs'] }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="surface-panel surface-panel--accent">
                <div class="section-head">
                    <h2 class="section-title">Integration health</h2>
                    <p class="section-copy">Validasi cepat untuk koneksi dan konfigurasi kritikal.</p>
                </div>
                <div class="metric-row">
                    <span>WhatsApp Broadcast Webhook</span>
                    <span class="status-badge {{ $systemHealth['integrations']['whatsapp_webhook_configured'] ? 'status-badge--healthy' : 'status-badge--warning' }}">
                        {{ $systemHealth['integrations']['whatsapp_webhook_configured'] ? 'Configured' : 'Missing' }}
                    </span>
                </div>
                <div class="metric-row">
                    <span>School Data Cache TTL</span>
                    <span class="metric-value">{{ $systemHealth['integrations']['school_data_cache_ttl'] }} seconds</span>
                </div>
                <div class="metric-row">
                    <span>Queue backlog</span>
                    <span class="status-badge {{ $systemHealth['queues']['pending_jobs'] > 10 ? 'status-badge--warning' : 'status-badge--healthy' }}">
                        {{ $systemHealth['queues']['pending_jobs'] > 10 ? 'High' : 'Normal' }}
                    </span>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
