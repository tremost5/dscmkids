@extends('admin.layout')

@section('title', 'Berita')

@section('content')
<h1 style="margin-top:0;">Manajemen Berita</h1>
<div class="actions">
    <a class="btn btn-primary" href="{{ route('admin.news.create') }}">Tambah Berita</a>
</div>
<table>
    <thead><tr><th>Judul</th><th>Slug</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($news as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td>{{ $item->slug }}</td>
            <td>{{ $item->is_published ? 'Published' : 'Draft' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.news.show', $item) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.news.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.news.destroy', $item) }}" onsubmit="return confirm('Hapus berita ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Belum ada berita.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $news->links() }}</div>
@endsection
