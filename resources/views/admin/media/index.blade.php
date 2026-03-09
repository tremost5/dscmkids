@extends('admin.layout')

@section('title', 'Media')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Media library</h1>
            <p class="muted">Kelola aset gambar, video, dan PDF dengan tampilan yang lebih rapi untuk operasional harian.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.media.create') }}">Upload Media</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Library assets</h2>
                <p class="section-copy">{{ $media->total() }} file tersedia di media storage.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Judul</th><th>File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($media as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td><a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" rel="noopener">Lihat File</a></td>
                        <td>{{ $item->file_size ? number_format($item->file_size / 1024, 1).' KB' : '-' }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.media.show', $item) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.media.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.media.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus media ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Belum ada media.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $media->links() }}</div>
</div>
@endsection
