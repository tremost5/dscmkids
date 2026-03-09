@extends('admin.layout')

@section('title', 'Portfolio Guru')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Teacher profiles</h1>
            <p class="muted">Kelola profil pengajar, peran, kelas, dan foto untuk area publik.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.teachers.create') }}">Tambah Guru</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Teacher directory</h2>
                <p class="section-copy">{{ $teachers->total() }} profil guru tercatat.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Foto</th><th>Nama</th><th>Peran</th><th>Kelas</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td>
                            @if($teacher->photo_path)
                                <img src="{{ route('teacher.photo', $teacher) }}" alt="guru" class="thumb thumb--avatar" onerror="this.onerror=null;this.style.display='none';">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $teacher->name }}</td>
                        <td>{{ $teacher->role ?? '-' }}</td>
                        <td>{{ $teacher->class_group ?? '-' }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.teachers.show', $teacher) }}">Detail</a>
                                <a class="btn btn-secondary" href="{{ route('admin.teachers.edit', $teacher) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus profil guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Belum ada profil guru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $teachers->links() }}</div>
</div>
@endsection
