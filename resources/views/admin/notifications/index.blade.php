@extends('admin.layout')

@section('title', 'Broadcast Notifikasi')

@section('content')
<h1 style="margin-top:0;">Broadcast Notifikasi</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.notifications.create') }}">Kirim Broadcast</a></div>

@if($broadcasts instanceof \Illuminate\Support\Collection)
    <p class="muted">Tabel broadcast belum tersedia. Jalankan migrasi.</p>
@else
<p class="muted">Status broadcast diproses lewat queue. Pastikan worker aktif jika `QUEUE_CONNECTION` bukan `sync`.</p>
<table>
    <thead><tr><th>Judul</th><th>Channel</th><th>Status</th><th>Target</th><th>Diproses</th><th>Waktu</th></tr></thead>
    <tbody>
    @forelse($broadcasts as $row)
        <tr>
            <td><strong>{{ $row->title }}</strong><br><span class="muted">{{ \Illuminate\Support\Str::limit($row->message, 120) }}</span></td>
            <td>{{ strtoupper($row->channel) }}</td>
            <td>{{ strtoupper($row->status ?? 'pending') }} @if($row->last_error)<br><span class="muted">{{ \Illuminate\Support\Str::limit($row->last_error, 80) }}</span>@endif</td>
            <td>{{ $row->target_count }}</td>
            <td>{{ $row->processed_count ?? 0 }} sukses / {{ $row->failed_count ?? 0 }} gagal</td>
            <td>{{ optional($row->sent_at)->format('d M Y H:i') ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="6" class="muted">Belum ada broadcast.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $broadcasts->links() }}</div>
@endif
@endsection
