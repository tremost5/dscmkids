@extends('admin.layout')

@section('title', 'Bank Soal Harian')

@section('content')
<h1 style="margin-top:0;">Bank Soal Harian</h1>
<p class="muted">Kelola soal kuis ayat harian per hari langsung dari admin panel.</p>

<div class="actions">
    <a class="btn btn-primary" href="{{ route('admin.quiz-banks.create') }}">Tambah Bank Soal</a>
</div>

<table>
    <thead>
    <tr>
        <th>Hari</th>
        <th>Judul</th>
        <th>Ayat Hafalan</th>
        <th>Jumlah Soal</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($banks as $bank)
        <tr>
            <td>{{ ucfirst($bank->day_key) }}</td>
            <td>{{ $bank->title }}</td>
            <td>{{ $bank->memory_verse ?: '-' }}</td>
            <td>{{ $bank->questions_count }}</td>
            <td>{{ $bank->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.quiz-banks.edit', $bank) }}">Edit</a>
                <form method="POST" action="{{ route('admin.quiz-banks.destroy', $bank) }}" onsubmit="return confirm('Hapus bank soal ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="muted">Belum ada bank soal.</td></tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">{{ $banks->links() }}</div>
@endsection

