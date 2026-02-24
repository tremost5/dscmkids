@extends('admin.layout')

@section('title', 'Broadcast Notifikasi')

@section('content')
<h1 style="margin-top:0;">Broadcast Notifikasi</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.notifications.create') }}">Kirim Broadcast</a></div>

@if($broadcasts instanceof \Illuminate\Support\Collection)
    <p class="muted">Tabel broadcast belum tersedia. Jalankan migrasi.</p>
@else
<table>
    <thead><tr><th>Judul</th><th>Channel</th><th>Target</th><th>Terkirim</th></tr></thead>
    <tbody>
    @forelse($broadcasts as $row)
        <tr>
            <td><strong>{{ $row->title }}</strong><br><span class="muted">{{ \Illuminate\Support\Str::limit($row->message, 120) }}</span></td>
            <td>{{ strtoupper($row->channel) }}</td>
            <td>{{ $row->target_count }}</td>
            <td>{{ optional($row->sent_at)->format('d M Y H:i') ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Belum ada broadcast.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $broadcasts->links() }}</div>
@endif
@endsection

