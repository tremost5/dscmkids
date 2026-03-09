@extends('admin.layout')

@section('title', 'Detail Guru')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $teacher->name }}</h1>
            <p class="muted">{{ $teacher->role ?: '-' }}{{ $teacher->class_group ? ' · '.$teacher->class_group : '' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.teachers.edit', $teacher) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.teachers.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            @if($teacher->photo_path)
                <img src="{{ route('teacher.photo', $teacher) }}" alt="{{ $teacher->name }}" class="detail-media detail-media--avatar" onerror="this.onerror=null;this.style.display='none';">
            @endif
            <div class="detail-prose">{{ $teacher->bio }}</div>
            @if($teacher->instagram_url)
                <a href="{{ $teacher->instagram_url }}" target="_blank" rel="noopener">Instagram</a>
            @endif
        </section>

        <aside class="detail-panel detail-card">
            <div class="detail-kv">
                <div class="detail-kv-item"><span>Peran</span><strong>{{ $teacher->role ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>Kelas</span><strong>{{ $teacher->class_group ?: '-' }}</strong></div>
                <div class="detail-kv-item"><span>Status</span><strong>{{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}</strong></div>
            </div>
        </aside>
    </div>
</div>
@endsection
