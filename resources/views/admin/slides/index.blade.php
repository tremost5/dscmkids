@extends('admin.layout')

@section('title', 'Slide Header')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Hero slides</h1>
            <p class="muted">Kelola slide utama landing page beserta urutan dan status tampilnya.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.slides.create') }}">Tambah Slide</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Slide inventory</h2>
                <p class="section-copy">{{ $slides->total() }} slide terdaftar.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Gambar</th><th>Judul</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($slides as $slide)
                    <tr>
                        <td><img src="{{ asset('storage/'.$slide->image_path) }}" alt="slide" class="thumb"></td>
                        <td>{{ $slide->title ?? '-' }}</td>
                        <td>{{ $slide->sort_order }}</td>
                        <td><span class="status-badge {{ $slide->is_active ? 'status-badge--active' : 'status-badge--inactive' }}">{{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.slides.show', $slide) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.slides.edit', $slide) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.slides.destroy', $slide) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus slide ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Belum ada slide.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $slides->links() }}</div>
</div>
@endsection
