@php($currentRoute = request()->route()?->getName())
<div class="toolbar">
    <div>
        <h1>PRA 2026</h1>
        <p class="muted">Dashboard event, peserta, pembayaran, kehadiran, broadcast WA, dan konten landing page.</p>
    </div>
    <div class="toolbar-actions">
        <a class="btn btn-secondary @if($currentRoute === 'admin.pra.dashboard') active @endif" href="{{ route('admin.pra.dashboard') }}">Dashboard</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.participants') }}">Peserta</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.group-one') }}">Grup 1</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.group-two') }}">Grup 2</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.payments') }}">Pembayaran</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.attendance') }}">Kehadiran</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.companions') }}">Pendamping</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.broadcast') }}">Broadcast WA</a>
        <a class="btn btn-secondary" href="{{ route('admin.pra.export-companions') }}">Export Pendamping</a>
        <a class="btn btn-primary" href="{{ route('admin.pra.content') }}">Konten Landing Page</a>
        <a class="btn btn-secondary" href="{{ route('events.pra-2026') }}" target="_blank" rel="noopener">Lihat Halaman</a>
    </div>
</div>
