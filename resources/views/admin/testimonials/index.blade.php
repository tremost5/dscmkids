@extends('admin.layout')

@section('title', 'Testimonial')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Testimonials</h1>
            <p class="muted">Kelola testimonial orang tua dan murid, termasuk rating, avatar, dan balasan admin.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-primary" href="{{ route('admin.testimonials.create') }}">Tambah Testimonial</a>
        </div>
    </div>

    <section class="table-shell">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Testimonial feed</h2>
                <p class="section-copy">{{ $testimonials->total() }} testimonial tersimpan.</p>
            </div>
        </div>
        <div class="table-scroller">
            <table>
                <thead><tr><th>Avatar</th><th>Nama</th><th>Keterangan</th><th>Rating</th><th>Status</th><th>Balasan</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($testimonials as $item)
                    <tr>
                        <td>
                            @if($item->avatar_path)
                                <img src="{{ asset('storage/'.$item->avatar_path) }}" alt="avatar" class="thumb thumb--sm">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <strong>{{ $item->role_label ?? '-' }}</strong><br>
                            <span class="muted">{{ \Illuminate\Support\Str::limit($item->message, 100) }}</span>
                        </td>
                        <td><span class="rating-stars">{{ str_repeat('*', max(1, min(5, (int) $item->rating))) }}</span></td>
                        <td><span class="status-badge {{ $item->is_active ? 'status-badge--active' : 'status-badge--inactive' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>{{ filled($item->admin_reply) ? 'Sudah dibalas' : 'Belum' }}</td>
                        <td>
                            <div class="toolbar-actions">
                                <a class="btn btn-secondary" href="{{ route('admin.testimonials.edit', $item) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $item) }}" class="inline-form" data-loading-form onsubmit="return confirm('Hapus testimonial ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">Belum ada testimonial.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div>{{ $testimonials->links() }}</div>
</div>
@endsection
