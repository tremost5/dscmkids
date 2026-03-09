@extends('admin.layout')

@section('title', 'Detail Section')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>{{ $section->section_key }}</h1>
            <p class="muted">{{ $section->title ?: 'Tanpa judul' }}</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('admin.sections.edit', $section) }}">Edit</a>
            <a class="btn btn-secondary" href="{{ route('admin.sections.index') }}">Kembali</a>
        </div>
    </div>

    <div class="detail-grid">
        <section class="detail-panel detail-copy">
            <div class="section-head"><h2 class="section-title">Content</h2></div>
            <div class="detail-prose">{{ $section->content }}</div>
        </section>

        <aside class="detail-panel detail-card">
            <div class="section-head"><h2 class="section-title">Meta</h2></div>
            @if($section->meta)
                <pre class="code-block">{{ json_encode($section->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            @else
                <p class="helper-text">Section ini belum memiliki meta JSON.</p>
            @endif
        </aside>
    </div>
</div>
@endsection
