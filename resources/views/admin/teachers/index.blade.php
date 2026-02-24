@extends('admin.layout')

@section('title', 'Portfolio Guru')

@section('content')
<h1 style="margin-top:0;">Portfolio Guru</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.teachers.create') }}">Tambah Guru</a></div>
<table>
    <thead><tr><th>Foto</th><th>Nama</th><th>Peran</th><th>Kelas</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($teachers as $teacher)
        <tr>
            <td>
                @if($teacher->photo_path)
                    <img src="{{ route('teacher.photo', $teacher) }}" alt="guru" style="width:70px;height:70px;object-fit:cover;border-radius:50%;" onerror="this.onerror=null;this.style.display='none';">
                @else
                    -
                @endif
            </td>
            <td>{{ $teacher->name }}</td>
            <td>{{ $teacher->role ?? '-' }}</td>
            <td>{{ $teacher->class_group ?? '-' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.teachers.show', $teacher) }}">Detail</a>
                <a class="btn btn-secondary" href="{{ route('admin.teachers.edit', $teacher) }}">Edit</a>
                <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Hapus profil guru ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Belum ada profil guru.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:12px;">{{ $teachers->links() }}</div>
@endsection
