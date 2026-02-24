@extends('admin.layout')

@section('title', 'Tema Bulanan & Renungan Harian')

@section('content')
<h1 style="margin-top:0;">Tema Bulanan & Renungan Harian</h1>
<p class="muted">Atur tampilan tema bulanan dan konten renungan harian murid dari panel admin.</p>

<form method="POST" action="{{ route('admin.spiritual.update') }}">
    @csrf
    @method('PUT')

    <h3 style="margin-bottom:8px;">Tema Bulanan</h3>
    <div class="field">
        <label>Judul Tema
            <input type="text" name="monthly_title" value="{{ old('monthly_title', $monthlyTheme->title) }}" required>
        </label>
    </div>
    <div class="grid-2">
        <div class="field">
            <label>Subjudul
                <input type="text" name="monthly_subtitle" value="{{ old('monthly_subtitle', $monthlyTheme->meta['subtitle'] ?? '') }}" placeholder="Fokus Pertumbuhan Iman">
            </label>
        </div>
        <div class="field">
            <label>Ayat Tema Bulanan
                <input type="text" name="monthly_verse" value="{{ old('monthly_verse', $monthlyTheme->meta['verse'] ?? '') }}" placeholder="Kolose 2:7">
            </label>
        </div>
    </div>
    <div class="field">
        <label>Deskripsi Tema Bulanan
            <textarea name="monthly_content" style="min-height:100px;">{{ old('monthly_content', $monthlyTheme->content) }}</textarea>
        </label>
    </div>
    <div class="field">
        <label>Highlight/Motto Bulanan
            <input type="text" name="monthly_highlight" value="{{ old('monthly_highlight', $monthlyTheme->meta['highlight'] ?? '') }}" placeholder="Akar iman yang kuat melahirkan hidup yang berdampak.">
        </label>
    </div>

    <hr style="border:0;border-top:1px solid #e5e7eb;margin:16px 0;">

    <h3 style="margin-bottom:8px;">Renungan Harian</h3>
    <div class="field">
        <label>Judul Section Renungan
            <input type="text" name="devotion_section_title" value="{{ old('devotion_section_title', $dailyDevotions->title) }}" required>
        </label>
    </div>

    @foreach($dayLabels as $dayKey => $dayLabel)
        @php $row = $devotionRows[$dayKey] ?? ['title' => '', 'verse' => '', 'message' => '', 'challenge' => '']; @endphp
        <div style="border:1px solid #dbe4f3;border-radius:12px;padding:12px;margin-bottom:12px;background:#f8fbff;">
            <h4 style="margin:0 0 10px;">{{ $dayLabel }}</h4>
            <div class="field">
                <label>Judul Renungan
                    <input type="text" name="devotions[{{ $dayKey }}][title]" value="{{ old('devotions.'.$dayKey.'.title', $row['title']) }}" required>
                </label>
            </div>
            <div class="field">
                <label>Ayat
                    <input type="text" name="devotions[{{ $dayKey }}][verse]" value="{{ old('devotions.'.$dayKey.'.verse', $row['verse']) }}" required>
                </label>
            </div>
            <div class="field">
                <label>Isi Renungan
                    <textarea name="devotions[{{ $dayKey }}][message]" style="min-height:90px;" required>{{ old('devotions.'.$dayKey.'.message', $row['message']) }}</textarea>
                </label>
            </div>
            <div class="field" style="margin-bottom:0;">
                <label>Misi Iman Harian
                    <input type="text" name="devotions[{{ $dayKey }}][challenge]" value="{{ old('devotions.'.$dayKey.'.challenge', $row['challenge']) }}">
                </label>
            </div>
        </div>
    @endforeach

    <div class="actions">
        <button class="btn btn-primary" type="submit">Simpan Semua</button>
        <a class="btn btn-secondary" href="{{ route('landing') }}#renungan" target="_blank">Lihat Preview</a>
    </div>
</form>
@endsection

