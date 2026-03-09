@extends('admin.layout')

@section('title', 'Bank Soal Harian')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Quiz bank manager</h1>
            <p class="muted">Kelola bank soal harian, ayat hafalan, dan status kuis mingguan.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.quiz-banks.create') }}">Tambah Bank Soal</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Daily quiz banks</h2>
                <p class="section-copy">{{ $banks->total() }} bank soal tersimpan.</p>
            </div>
        </div>
        <div class="table-scroller">
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
                        <td><span class="status-badge {{ $bank->is_active ? 'status-badge--active' : 'status-badge--inactive' }}">{{ $bank->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.quiz-banks.edit', $bank) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.quiz-banks.destroy', $bank) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus bank soal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Belum ada bank soal.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $banks->links() }}</div>
</div>
@endsection
