@extends('admin.layout')

@section('title', 'Tema Bulanan & Renungan Harian')

@section('content')
<div class="page-grid">
    <div class="page-header">
        <div class="page-header-copy">
            <h1>Tema bulanan & renungan harian</h1>
            <p class="muted">Atur tampilan tema bulanan dan isi renungan harian murid dari satu form terstruktur.</p>
        </div>
        <div class="toolbar-actions">
            <a class="btn btn-secondary" href="{{ route('landing') }}#renungan" target="_blank" rel="noopener">Lihat Preview</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.spiritual.update') }}" class="form-shell" data-loading-form>
        @csrf
        @method('PUT')

        <section class="form-panel">
            <div class="section-head"><h2 class="section-title">Tema bulanan</h2></div>
            <div class="field"><label>Judul Tema<input type="text" name="monthly_title" value="{{ old('monthly_title', $monthlyTheme->title) }}" required></label></div>
            <div class="grid-2">
                <div class="field"><label>Subjudul<input type="text" name="monthly_subtitle" value="{{ old('monthly_subtitle', $monthlyTheme->meta['subtitle'] ?? '') }}" placeholder="Fokus Pertumbuhan Iman"></label></div>
                <div class="field"><label>Ayat Tema Bulanan<input type="text" name="monthly_verse" value="{{ old('monthly_verse', $monthlyTheme->meta['verse'] ?? '') }}" placeholder="Kolose 2:7"></label></div>
            </div>
            <div class="field"><label>Deskripsi Tema Bulanan<textarea name="monthly_content">{{ old('monthly_content', $monthlyTheme->content) }}</textarea></label></div>
            <div class="field"><label>Highlight/Motto Bulanan<input type="text" name="monthly_highlight" value="{{ old('monthly_highlight', $monthlyTheme->meta['highlight'] ?? '') }}" placeholder="Akar iman yang kuat melahirkan hidup yang berdampak."></label></div>
        </section>

        <section class="form-panel">
            <div class="section-head"><h2 class="section-title">Renungan harian</h2></div>
            <div class="field"><label>Judul Section Renungan<input type="text" name="devotion_section_title" value="{{ old('devotion_section_title', $dailyDevotions->title) }}" required></label></div>
            @foreach($dayLabels as $dayKey => $dayLabel)
                @php $row = $devotionRows[$dayKey] ?? ['title' => '', 'verse' => '', 'message' => '', 'challenge' => '']; @endphp
                <div class="detail-panel">
                    <div class="section-head"><h3 class="section-title">{{ $dayLabel }}</h3></div>
                    <div class="field"><label>Judul Renungan<input type="text" name="devotions[{{ $dayKey }}][title]" value="{{ old('devotions.'.$dayKey.'.title', $row['title']) }}" required></label></div>
                    <div class="field"><label>Ayat<input type="text" name="devotions[{{ $dayKey }}][verse]" value="{{ old('devotions.'.$dayKey.'.verse', $row['verse']) }}" required></label></div>
                    <div class="field"><label>Isi Renungan<textarea name="devotions[{{ $dayKey }}][message]" required>{{ old('devotions.'.$dayKey.'.message', $row['message']) }}</textarea></label></div>
                    <div class="field"><label>Misi Iman Harian<input type="text" name="devotions[{{ $dayKey }}][challenge]" value="{{ old('devotions.'.$dayKey.'.challenge', $row['challenge']) }}"></label></div>
                </div>
            @endforeach
        </section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan Semua</button>
            <a class="btn btn-secondary" href="{{ route('landing') }}#renungan" target="_blank" rel="noopener">Preview</a>
        </div>
    </form>
</div>
@endsection
