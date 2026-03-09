@extends('admin.layout')

@section('title', 'Materi Edukatif')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Learning materials</h1>
            <p class="muted">Materi bertingkat untuk murid berdasarkan kelas, level, dan ayat pendukung.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.materials.create') }}">Tambah Materi</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Daftar materi</h2>
                <p class="section-copy">{{ $materials->total() }} materi tersedia untuk kurikulum DSCMKids.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Judul</th><th>Kelas</th><th>Level</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($materials as $item)
                    <tr>
                        <td><strong>{{ $item->title }}</strong><br><span class="muted">{{ $item->bible_reference ?: '-' }}</span></td>
                        <td>{{ $item->class_group ?: 'Semua' }}</td>
                        <td>{{ strtoupper($item->level) }}</td>
                        <td><span class="status-badge {{ $item->is_active ? 'status-badge--active' : 'status-badge--inactive' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.materials.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.materials.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus materi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Belum ada materi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $materials->links() }}</div>
</div>
@endsection
