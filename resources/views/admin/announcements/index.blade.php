@extends('admin.layout')

@section('title', 'Informasi')

@section('content')
<h1 style="margin-top:0;">Manajemen Informasi</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.announcements.create') }}">Tambah Informasi</a></div>
<table>
    <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($announcements as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td>{{ optional($item->event_date)->format('d M Y') ?? '-' }}</td>
            <td>{{ $item->location ?? '-' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.announcements.show', $item) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.announcements.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.announcements.destroy', $item) }}" onsubmit="return confirm('Hapus informasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Belum ada informasi.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $announcements->links() }}</div>
@endsection
