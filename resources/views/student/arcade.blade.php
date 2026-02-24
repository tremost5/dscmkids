<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade Murid | DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    @if (!app()->environment('testing'))
        @vite(['resources/css/student-arcade.css', 'resources/js/student-arcade.js'])
    @endif
</head>
<body data-page="student-arcade">
<main class="arcade-shell">
    <section class="arcade-hero">
        <a href="{{ route('landing') }}" class="arcade-back">&larr; Kembali ke Landing</a>
        <h1>Arcade Murid DSCMKids</h1>
        <p>Main 3 game Alkitab, kumpulkan skor harian, dan naikkan poin akunmu.</p>
        <div class="arcade-actions">
            @if(!$isStudent)
                <a href="{{ route('student.login') }}" class="arcade-btn">Login Murid</a>
                <a href="{{ route('student.register') }}" class="arcade-btn arcade-btn-ghost">Daftar Murid</a>
            @else
                <span class="arcade-chip">Skor tersimpan ke akun kamu</span>
            @endif
        </div>
    </section>

    <section class="arcade-grid">
        <article class="arcade-card">
            <h2>1) Memory Match</h2>
            <p>Cocokkan pasangan kartu ayat secepat mungkin.</p>
            <div id="memoryBoard" class="memory-board"></div>
            <div class="arcade-row">
                <button class="arcade-btn" type="button" id="memoryStart">Mulai Game</button>
                <button class="arcade-btn arcade-btn-ghost" type="button" id="memorySubmit">Simpan Skor</button>
            </div>
            <div class="game-status" id="memoryStatus"></div>
        </article>

        <article class="arcade-card">
            <h2>2) Tebak Tokoh Alkitab</h2>
            <p>Jawab 5 pertanyaan pilihan ganda.</p>
            <div id="guessGame" class="guess-box"></div>
            <div class="arcade-row">
                <button class="arcade-btn" type="button" id="guessStart">Mulai Quiz</button>
                <button class="arcade-btn arcade-btn-ghost" type="button" id="guessSubmit">Simpan Skor</button>
            </div>
            <div class="game-status" id="guessStatus"></div>
        </article>

        <article class="arcade-card">
            <h2>3) Susun Ayat</h2>
            <p>Klik potongan kata sesuai urutan ayat yang benar.</p>
            <div id="verseBuilder" class="verse-builder"></div>
            <div class="arcade-row">
                <button class="arcade-btn" type="button" id="verseStart">Mulai Susun</button>
                <button class="arcade-btn arcade-btn-ghost" type="button" id="verseSubmit">Simpan Skor</button>
            </div>
            <div class="game-status" id="verseStatus"></div>
        </article>
    </section>

    <section class="leaderboard-card">
        <h2>Leaderboard Arcade Hari Ini</h2>
        <div id="arcadeLeaderboardList" class="leaderboard-list">
            @forelse($arcadeLeaderboard as $row)
                <div class="leaderboard-row">
                    <strong>#{{ $row['rank'] }} {{ $row['name'] }}</strong>
                    <span>{{ $row['game_key'] }} - {{ $row['score'] }} pts</span>
                </div>
            @empty
                <div class="leaderboard-row">Belum ada skor arcade hari ini.</div>
            @endforelse
        </div>
        <div class="game-status" id="arcadeFeedback"></div>
    </section>
</main>

<script>
window.DSCM_ARCADE_DATA = {
    isStudent: @json($isStudent),
    csrfToken: @json(csrf_token()),
    submitUrl: @json(route('student.arcade.score')),
};
</script>
</body>
</html>

