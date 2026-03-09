@extends('admin.layout')

@section('title', 'Berita')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>News command center</h1>
            <p class="muted">Kelola berita dengan search, status filter, bulk publish, dan export dari satu tabel responsif.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.news.export', request()->query()) }}">Export CSV</a>
            <a class="btn btn-primary" href="{{ route('admin.news.create') }}">Tambah Berita</a>
        </div>
    </div>

    <section class="surface-panel">
        <form method="GET" action="{{ route('admin.news.index') }}" class="grid-2">
            <div class="field">
                <label>Cari berita
                    <input type="search" name="q" value="{{ $query }}" placeholder="Judul, slug, excerpt">
                </label>
            </div>
            <div class="field">
                <label>Status
                    <select name="status">
                        <option value="">Semua status</option>
                        <option value="published" @selected($status === 'published')>Published</option>
                        <option value="draft" @selected($status === 'draft')>Draft</option>
                    </select>
                </label>
            </div>
            <div class="toolbar-actions full-span">
                <button class="btn btn-primary" type="submit">Apply Filters</button>
                <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <form method="POST" action="{{ route('admin.news.bulk') }}" id="newsBulkForm" data-loading-form>
        @csrf
    </form>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">News inventory</h2>
                <p class="section-copy">{{ $news->total() }} berita cocok dengan filter saat ini.</p>
            </div>
            <div class="toolbar-actions">
                <select name="action" form="newsBulkForm" class="select-compact">
                    <option value="">Bulk action</option>
                    <option value="publish">Publish</option>
                    <option value="unpublish">Unpublish</option>
                    <option value="delete">Delete</option>
                </select>
                <button class="btn btn-secondary" type="submit" form="newsBulkForm">Run Bulk Action</button>
            </div>
        </div>

        <div class="table-scroller">
            <table>
                <thead>
                <tr>
                    <th class="checkbox-cell"><input type="checkbox" data-check-all='input[name="news_ids[]"]'></th>
                    <th>Judul</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($news as $item)
                    <tr>
                        <td class="checkbox-cell"><input type="checkbox" name="news_ids[]" value="{{ $item->id }}" form="newsBulkForm"></td>
                        <td>
                            <strong>{{ $item->title }}</strong><br>
                            <span class="muted">{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->body, 88) }}</span>
                        </td>
                        <td>{{ $item->slug }}</td>
                        <td>
                            <span class="status-badge {{ $item->is_published ? 'status-badge--published' : 'status-badge--draft' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ optional($item->published_at)->format('d M Y H:i') ?: '-' }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.news.show', $item) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.news.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.news.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Belum ada berita yang cocok dengan filter.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $news->links() }}</div>
</div>
@endsection
