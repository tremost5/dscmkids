@extends('admin.layout')

@section('title', 'Testimonial')

@section('content')
<h1 style="margin-top:0;">Testimonial Orang Tua & Murid</h1>
<div class="actions"><a class="btn btn-primary" href="{{ route('admin.testimonials.create') }}">Tambah Testimonial</a></div>

<table>
    <thead><tr><th>Avatar</th><th>Nama</th><th>Keterangan</th><th>Rating</th><th>Status</th><th>Balasan</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($testimonials as $item)
        <tr>
            <td>
                @if($item->avatar_path)
                    <img src="{{ asset('storage/'.$item->avatar_path) }}" alt="avatar" style="width:58px;height:58px;object-fit:cover;border-radius:50%;">
                @else
                    -
                @endif
            </td>
            <td>{{ $item->name }}</td>
            <td>
                <strong>{{ $item->role_label ?? '-' }}</strong><br>
                <span class="muted">{{ \Illuminate\Support\Str::limit($item->message, 100) }}</span>
            </td>
            <td>{{ str_repeat('★', max(1, min(5, (int) $item->rating))) }}</td>
            <td>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            <td>{{ filled($item->admin_reply) ? 'Sudah dibalas' : 'Belum' }}</td>
            <td class="actions">
                <a class="btn btn-secondary" href="{{ route('admin.testimonials.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.testimonials.destroy', $item) }}" onsubmit="return confirm('Hapus testimonial ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="muted">Belum ada testimonial.</td></tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">{{ $testimonials->links() }}</div>
@endsection
