@extends('admin.layout')

@section('title', 'Media')

@section('content')
<h1 style="margin-top:0;">Media Upload</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.media.create') }}">Upload Media</a></div>
<table>
    <thead><tr><th>Judul</th><th>File</th><th>Ukuran</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($media as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td><a href="{{ asset('storage/'.$item->file_path) }}" target="_blank">Lihat File</a></td>
            <td>{{ $item->file_size ? number_format($item->file_size/1024, 1).' KB' : '-' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.media.show', $item) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.media.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Hapus media ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Belum ada media.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $media->links() }}</div>
@endsection
