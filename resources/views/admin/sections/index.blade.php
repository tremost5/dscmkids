@extends('admin.layout')

@section('title', 'Konten Section')

@section('content')
<h1 style="margin-top:0;">Konten Landing Page</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.sections.create') }}">Tambah Section</a></div>
<table>
    <thead><tr><th>Key</th><th>Title</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($sections as $item)
        <tr>
            <td>{{ $item->section_key }}</td>
            <td>{{ $item->title }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.sections.show', $item) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.sections.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.sections.destroy', $item) }}" onsubmit="return confirm('Hapus section ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="muted">Belum ada section.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $sections->links() }}</div>
@endsection
