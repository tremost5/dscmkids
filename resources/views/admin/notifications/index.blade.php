@extends('admin.layout')

@section('title', 'Broadcast Notifikasi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Broadcast hub</h1>
            <p class="muted">Pantau status email dan WhatsApp broadcast yang diproses lewat queue background.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.notifications.create') }}">Kirim Broadcast</a>
        </div>
    </div>

    @if($broadcasts instanceof \Illuminate\Support\Collection)
        <section class="surface-panel">
            <p class="muted">Tabel broadcast belum tersedia. Jalankan migrasi.</p>
        </section>
    @else
        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h2 class="section-title">Queue-backed delivery</h2>
                    <p class="section-copy">Pastikan worker aktif jika `QUEUE_CONNECTION` bukan `sync`.</p>
                </div>
            </div>
            <div class="table-scroller">
                <table>
                    <thead><tr><th>Judul</th><th>Channel</th><th>Status</th><th>Target</th><th>Diproses</th><th>Waktu</th></tr></thead>
                    <tbody>
                    @forelse($broadcasts as $row)
                        <tr>
                            <td><strong>{{ $row->title }}</strong><br><span class="muted">{{ \Illuminate\Support\Str::limit($row->message, 120) }}</span></td>
                            <td>{{ strtoupper($row->channel) }}</td>
                            <td>
                                <span class="status-badge {{ ($row->status ?? 'pending') === 'failed' ? 'status-badge--warning' : 'status-badge--published' }}">
                                    {{ strtoupper($row->status ?? 'pending') }}
                                </span>
                                @if($row->last_error)
                                    <br><span class="muted">{{ \Illuminate\Support\Str::limit($row->last_error, 80) }}</span>
                                @endif
                            </td>
                            <td>{{ $row->target_count }}</td>
                            <td>{{ $row->processed_count ?? 0 }} sukses / {{ $row->failed_count ?? 0 }} gagal</td>
                            <td>{{ optional($row->sent_at)->format('d M Y H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">Belum ada broadcast.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $broadcasts->links() }}</div>
    @endif
</div>
@endsection
