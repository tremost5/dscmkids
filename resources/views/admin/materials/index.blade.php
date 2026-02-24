@extends('admin.layout')

@section('title', 'Materi Edukatif')

@section('content')
<h1 style="margin-top:0;">Materi Edukatif Bertingkat</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.materials.create') }}">Tambah Materi</a></div>

<table>
    <thead><tr><th>Judul</th><th>Kelas</th><th>Level</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($materials as $item)
        <tr>
            <td><strong>{{ $item->title }}</strong><br><span class="muted">{{ $item->bible_reference ?: '-' }}</span></td>
            <td>{{ $item->class_group ?: 'Semua' }}</td>
            <td>{{ strtoupper($item->level) }}</td>
            <td>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.materials.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.materials.destroy', $item) }}" onsubmit="return confirm('Hapus materi ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Belum ada materi.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $materials->links() }}</div>
@endsection

