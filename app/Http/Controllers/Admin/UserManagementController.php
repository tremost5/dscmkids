<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkUserActionRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $role = trim((string) $request->query('role', ''));
        $status = trim((string) $request->query('status', ''));
        $sort = trim((string) $request->query('sort', 'latest'));

        $users = User::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', '%'.$query.'%')
                        ->orWhere('email', 'like', '%'.$query.'%')
                        ->orWhere('class_group', 'like', '%'.$query.'%');
                });
            })
            ->when($role !== '', fn ($builder) => $builder->where('role', $role))
            ->when($status !== '', fn ($builder) => $builder->where('is_active', $status === 'active'))
            ->when($sort === 'name', fn ($builder) => $builder->orderBy('name'))
            ->when($sort === 'points', fn ($builder) => $builder->orderByDesc('points'))
            ->when($sort === 'last_login', fn ($builder) => $builder->orderByDesc('last_login_at'))
            ->when($sort === 'latest' || !in_array($sort, ['name', 'points', 'last_login'], true), fn ($builder) => $builder->latest('id'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'q' => $query,
                'role' => $role,
                'status' => $status,
                'sort' => $sort,
            ],
            'roleOptions' => config('admin_permissions.roles', []),
            'summary' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admins' => User::whereIn('role', ['super_admin', 'admin', 'editor'])->count(),
                'students' => User::where('role', 'student')->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'roleOptions' => config('admin_permissions.roles', []),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => trim($validated['name']),
            'class_group' => filled($validated['class_group'] ?? null) ? trim((string) $validated['class_group']) : null,
            'email' => mb_strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
            'password' => Hash::make($validated['password']),
            'points' => 0,
            'streak_days' => 0,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'targetUser' => $user,
            'roleOptions' => config('admin_permissions.roles', []),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $payload = [
            'name' => trim($validated['name']),
            'class_group' => filled($validated['class_group'] ?? null) ? trim((string) $validated['class_group']) : null,
            'email' => mb_strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (filled($validated['password'] ?? null)) {
            $payload['password'] = Hash::make($validated['password']);
        }

        if ($request->user()?->id === $user->id) {
            if (!$payload['is_active']) {
                return back()->withErrors(['message' => 'Kamu tidak bisa menonaktifkan akunmu sendiri.'])->withInput();
            }

            if (!in_array($payload['role'], ['super_admin', 'admin', 'editor'], true)) {
                return back()->withErrors(['message' => 'Kamu tidak bisa mencabut akses admin dari akunmu sendiri.'])->withInput();
            }
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function bulkUpdate(BulkUserActionRequest $request)
    {
        $userIds = collect($request->validated('user_ids'))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) $request->user()?->id)
            ->values();

        if ($userIds->isEmpty()) {
            return back()->withErrors(['message' => 'Pilih minimal satu akun selain akunmu sendiri.']);
        }

        $action = $request->validated('action');
        $query = User::query()->whereIn('id', $userIds->all());

        match ($action) {
            'activate' => $query->update(['is_active' => true]),
            'deactivate' => $query->update(['is_active' => false]),
            'promote_admin' => $query->update(['role' => 'admin', 'is_active' => true]),
            'make_editor' => $query->update(['role' => 'editor', 'is_active' => true]),
            'make_student' => $query->update(['role' => 'student']),
            'delete' => $query->delete(),
        };

        return redirect()->route('admin.users.index')->with('success', 'Bulk action berhasil diproses.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = trim((string) $request->query('q', ''));
        $role = trim((string) $request->query('role', ''));
        $status = trim((string) $request->query('status', ''));

        $users = User::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', '%'.$query.'%')
                        ->orWhere('email', 'like', '%'.$query.'%')
                        ->orWhere('class_group', 'like', '%'.$query.'%');
                });
            })
            ->when($role !== '', fn ($builder) => $builder->where('role', $role))
            ->when($status !== '', fn ($builder) => $builder->where('is_active', $status === 'active'))
            ->orderBy('name');

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Class Group', 'Points', 'Last Login']);

            $users->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->roleLabel(),
                        $user->is_active ? 'Active' : 'Inactive',
                        $user->class_group,
                        $user->points,
                        optional($user->last_login_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, 'users-export-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
