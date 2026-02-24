<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Edukatif | DSCMKids</title>
    <meta name="description" content="Materi Alkitab bertingkat untuk murid sekolah minggu DSCMKids.">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f8ff; color: #0f172a; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 18px; }
        .card { background: #fff; border: 1px solid #dbe5f6; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .meta { color: #64748b; font-size: .85rem; margin-top: 4px; }
        .badge { padding: 4px 8px; border-radius: 999px; background: #e2e8f0; font-size: .75rem; font-weight: 700; }
        @media (max-width: 860px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <p><a href="{{ route('landing') }}">&larr; Kembali ke Landing</a></p>
    <h1>Materi Edukatif Bertingkat</h1>
    <form class="card" method="GET">
        <div class="grid">
            <label>Kelas<input name="class_group" value="{{ $filters['class_group'] }}"></label>
            <label>Level
                <select name="level" style="width:100%;padding:8px;">
                    <option value="">Semua</option>
                    <option value="easy" @selected($filters['level'] === 'easy')>Easy</option>
                    <option value="medium" @selected($filters['level'] === 'medium')>Medium</option>
                    <option value="hard" @selected($filters['level'] === 'hard')>Hard</option>
                </select>
            </label>
            <div style="display:flex;align-items:flex-end;"><button type="submit">Filter</button></div>
        </div>
    </form>

    @if($recommended->count() > 0)
        <div class="card">
            <h3>Rekomendasi untuk kamu</h3>
            @foreach($recommended as $item)
                <div style="padding:8px 0;border-top:1px solid #eef2ff;">
                    <strong>{{ $item->title }}</strong>
                    <span class="badge">{{ strtoupper($item->level) }}</span>
                    <div class="meta">{{ $item->class_group ?: 'Semua kelas' }} | {{ $item->bible_reference ?: '-' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if($materials instanceof \Illuminate\Pagination\LengthAwarePaginator)
        @foreach($materials as $item)
            <article class="card">
                <h3 style="margin:0;">{{ $item->title }} <span class="badge">{{ strtoupper($item->level) }}</span></h3>
                <div class="meta">{{ $item->class_group ?: 'Semua kelas' }} | {{ $item->bible_reference ?: '-' }}</div>
                @if($item->summary)<p>{{ $item->summary }}</p>@endif
                <div style="white-space:pre-line;">{{ $item->content }}</div>
            </article>
        @endforeach
        {{ $materials->links() }}
    @else
        <article class="card">Materi belum tersedia. Jalankan migrasi terlebih dulu.</article>
    @endif
</div>
</body>
</html>

