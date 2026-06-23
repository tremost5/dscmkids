@extends('admin.layout')

@section('title', 'Audit Log')

@section('content')

<div class="toolbar" style="margin-top:16px;">
    <div>
        <h2 class="section-title">Audit Log Admin</h2>
        <p class="muted">
            Riwayat aktivitas perubahan yang dilakukan admin.
        </p>
    </div>
</div>

<section class="table-shell">
    <div class="table-scroller">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Admin</th>
                    <th>Aksi</th>
                    <th>Detail</th>
                    <th>IP</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            {{ optional($log->created_at)->format('d M Y H:i:s') }}
                        </td>

                        <td>
                            {{ $log->user->name ?? 'Unknown' }}
                        </td>

                        <td>
                            {{ $log->action ?: '-' }}
                        </td>
                        
                        <td>
                            {{ $log->description ?: '-' }}
                        </td>
                        
                        <td>
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            Belum ada audit log.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $logs->links() }}
    </div>
</section>

@endsection