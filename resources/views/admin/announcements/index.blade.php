@extends('admin.layout')

@section('title', 'Informasi')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Announcement center</h1>
            <p class="muted">Kelola informasi kegiatan, tanggal acara, dan lokasi dari satu tabel yang lebih rapi dan mobile-friendly.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.announcements.create') }}">Tambah Informasi</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Daftar informasi</h2>
                <p class="section-copy">{{ $announcements->total() }} item aktif dan arsip.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($announcements as $item)
                    <tr>
                        <td><strong>{{ $item->title }}</strong><br><span class="muted">{{ \Illuminate\Support\Str::limit($item->body, 90) }}</span></td>
                        <td>{{ optional($item->event_date)->format('d M Y') ?? '-' }}</td>
                        <td>{{ $item->location ?? '-' }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.announcements.show', $item) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.announcements.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus informasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Belum ada informasi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $announcements->links() }}</div>
</div>
@endsection
