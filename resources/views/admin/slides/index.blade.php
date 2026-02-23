@extends('admin.layout')

@section('title', 'Slide Header')

@section('content')
<h1 style="margin-top:0;">Slide Header Landing</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.slides.create') }}">Tambah Slide</a></div>
<table>
    <thead><tr><th>Gambar</th><th>Judul</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($slides as $slide)
        <tr>
            <td><img src="{{ asset('storage/'.$slide->image_path) }}" alt="slide" style="width:110px;height:70px;object-fit:cover;border-radius:8px;"></td>
            <td>{{ $slide->title ?? '-' }}</td>
            <td>{{ $slide->sort_order }}</td>
            <td>{{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.slides.show', $slide) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.slides.edit', $slide) }}">Edit</a>
                <form method="POST" action="{{ route('admin.slides.destroy', $slide) }}" onsubmit="return confirm('Hapus slide ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Belum ada slide.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $slides->links() }}</div>
@endsection
