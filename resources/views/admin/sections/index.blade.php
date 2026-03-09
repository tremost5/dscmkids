@extends('admin.layout')

@section('title', 'Konten Section')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Landing sections</h1>
            <p class="muted">Kelola blok konten statis dan konfigurasi meta untuk landing page.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.sections.create') }}">Tambah Section</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Section registry</h2>
                <p class="section-copy">{{ $sections->total() }} section tercatat.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Key</th><th>Title</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($sections as $item)
                    <tr>
                        <td>{{ $item->section_key }}</td>
                        <td>{{ $item->title }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.sections.show', $item) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.sections.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.sections.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus section ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">Belum ada section.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $sections->links() }}</div>
</div>
@endsection
