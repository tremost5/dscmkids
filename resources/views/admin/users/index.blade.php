@extends('admin.layout')

@section('title', 'User Management')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>User management</h1>
            <p class="muted">Kelola akun admin, editor, dan murid dengan filter, sorting, bulk action, dan export data.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.users.export', request()->query()) }}">Export CSV</a>
            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Tambah User</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total akun</div>
            <strong>{{ $summary['total'] }}</strong>
            <div class="stat-trend">Semua account yang tersimpan</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Akun aktif</div>
            <strong>{{ $summary['active'] }}</strong>
            <div class="stat-trend">{{ max(0, $summary['total'] - $summary['active']) }} akun perlu reaktivasi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Admin + editor</div>
            <strong>{{ $summary['admins'] }}</strong>
            <div class="stat-trend">Kontrol operasional platform</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Murid</div>
            <strong>{{ $summary['students'] }}</strong>
            <div class="stat-trend">User-facing account</div>
        </div>
    </div>

    <section class="surface-panel">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid-2">
            <div class="field">
                <label>Cari pengguna
                    <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nama, email, kelas">
                </label>
            </div>
            <div class="field">
                <label>Role
                    <select name="role">
                        <option value="">Semua role</option>
                        @foreach($roleOptions as $key => $item)
                            <option value="{{ $key }}" @selected($filters['role'] === $key)>{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="field">
                <label>Status
                    <select name="status">
                        <option value="">Semua status</option>
                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                    </select>
                </label>
            </div>
            <div class="field">
                <label>Sort
                    <select name="sort">
                        <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                        <option value="name" @selected($filters['sort'] === 'name')>Nama</option>
                        <option value="points" @selected($filters['sort'] === 'points')>Points</option>
                        <option value="last_login" @selected($filters['sort'] === 'last_login')>Last login</option>
                    </select>
                </label>
            </div>
            <div class="toolbar-actions full-span">
                <button class="btn btn-primary" type="submit">Apply Filters</button>
                <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="table-shell">
        <form method="POST" action="{{ route('admin.users.bulk') }}" data-loading-form>
            @csrf
            <div class="table-toolbar">
                <div>
                    <h2 class="section-title">User directory</h2>
                    <p class="section-copy">{{ $users->total() }} akun cocok dengan filter saat ini.</p>
                </div>
                <div class="toolbar-actions">
                    <select name="action" class="select-compact">
                        <option value="">Bulk action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="promote_admin">Promote to Admin</option>
                        <option value="make_editor">Make Editor</option>
                        <option value="make_student">Make Student</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button class="btn btn-secondary" type="submit">Run Bulk Action</button>
                </div>
            </div>

            <div class="table-scroller">
                <table>
                    <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" data-check-all='input[name="user_ids[]"]'></th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Kelas</th>
                        <th>Points</th>
                        <th>Last Login</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="checkbox-cell"><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"></td>
                            <td>
                                <strong>{{ $user->name }}</strong><br>
                                <span class="muted">{{ $user->email }}</span>
                            </td>
                            <td>{{ $user->roleLabel() }}</td>
                            <td>
                                <span class="status-badge {{ $user->is_active ? 'status-badge--active' : 'status-badge--inactive' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $user->class_group ?: '-' }}</td>
                            <td>{{ number_format((int) $user->points) }}</td>
                            <td>{{ optional($user->last_login_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td><a class="btn btn-secondary" href="{{ route('admin.users.edit', $user) }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">Belum ada pengguna yang cocok dengan filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </section>

    <div>{{ $users->links() }}</div>
</div>
@endsection
