<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSCMKids | Sekolah Minggu Modern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @if (!app()->environment('testing'))
        @vite(['resources/css/landing.css', 'resources/js/landing.js'])
    @endif
</head>
<body>
@php
    $metrics = $schoolData['metrics'] ?? [];
    $classAttendance = $schoolData['attendance_by_class'] ?? [];
    $attendanceTotals = $schoolData['attendance_totals'] ?? ['present' => 0, 'absent' => 0];

    $slidesData = $slides->count() > 0
        ? $slides->map(fn ($item) => [
            'title' => $item->title,
            'subtitle' => $item->subtitle,
            'image' => asset('storage/'.$item->image_path),
            'button_text' => $item->button_text,
            'button_url' => $item->button_url,
        ])->values()->all()
        : [[
            'title' => $sections['hero']->title ?? 'System Informasi Sekolah Minggu DSCMKids',
            'subtitle' => $sections['hero']->content ?? 'Platform digital untuk siswa dan orang tua.',
            'image' => 'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?q=80&w=1600&auto=format&fit=crop',
            'button_text' => 'Info Kelas',
            'button_url' => '#informasi',
        ]];

    $galleryItems = is_iterable($gallery) ? collect($gallery)->all() : [];
    $weeklyGalleryItems = is_iterable($weeklyGallery ?? null) ? collect($weeklyGallery)->all() : [];
    $themeMonthly = is_array($monthlyTheme ?? null) ? $monthlyTheme : [
        'title' => 'Tema Bulanan DSCMKids',
        'subtitle' => 'Fokus Pertumbuhan Iman',
        'verse' => 'Kolose 2:7',
        'description' => 'Bulan ini kita belajar bertumbuh dalam kasih dan ketaatan kepada Tuhan melalui tindakan sederhana setiap hari.',
        'highlight' => 'Akar iman yang kuat melahirkan hidup yang berdampak.',
    ];
    $devotion = is_array($dailyDevotion ?? null) ? $dailyDevotion : [
        'section_title' => 'Renungan Harian Murid',
        'day' => now()->locale('id')->translatedFormat('l'),
        'title' => 'Tuhan Menyertai Setiap Hari',
        'verse' => 'Yosua 1:9',
        'message' => 'Tuhan tidak pernah meninggalkanmu. Tetap berani, setia berdoa, dan lakukan yang benar hari ini.',
        'challenge' => 'Berdoa 2 menit untuk satu temanmu hari ini.',
    ];

    $readingPlans = [
        ['day' => 'Senin', 'title' => 'Tuhan Selalu Menolong', 'reference' => 'Mazmur 121:1-2', 'point' => 'Saat takut, ingat Tuhan menjaga kamu.'],
        ['day' => 'Selasa', 'title' => 'Yesus Sahabatku', 'reference' => 'Yohanes 15:12-13', 'point' => 'Kasih itu terlihat lewat tindakan baik setiap hari.'],
        ['day' => 'Rabu', 'title' => 'Belajar Taat', 'reference' => 'Efesus 6:1-3', 'point' => 'Taat pada orang tua menyenangkan hati Tuhan.'],
        ['day' => 'Kamis', 'title' => 'Hati yang Bersyukur', 'reference' => '1 Tesalonika 5:18', 'point' => 'Ucap syukur di hal kecil maupun besar.'],
        ['day' => 'Jumat', 'title' => 'Berani Berbuat Benar', 'reference' => 'Yosua 1:9', 'point' => 'Tetap benar walau tidak mudah.'],
        ['day' => 'Sabtu', 'title' => 'Mengampuni Teman', 'reference' => 'Kolose 3:13', 'point' => 'Mengampuni membuat hati damai.'],
        ['day' => 'Minggu', 'title' => 'Bersukacita di Rumah Tuhan', 'reference' => 'Mazmur 122:1', 'point' => 'Ibadah bersama adalah sukacita.'],
    ];

    $memoryVerses = [
        ['text' => 'Kasihilah seorang akan yang lain.', 'reference' => 'Yohanes 13:34'],
        ['text' => 'Segala perkara dapat kutanggung di dalam Dia.', 'reference' => 'Filipi 4:13'],
        ['text' => 'Tuhan adalah gembalaku, takkan kekurangan aku.', 'reference' => 'Mazmur 23:1'],
        ['text' => 'Bersukacitalah senantiasa di dalam Tuhan.', 'reference' => 'Filipi 4:4'],
    ];

    $challengeItems = [
        'Doakan orang tua sebelum tidur',
        'Baca Alkitab minimal 10 menit',
        'Lakukan 1 kebaikan tanpa disuruh',
        'Hafalkan 1 ayat minggu ini',
    ];

    $currentDayName = now()->locale('id')->translatedFormat('l');
    $todayReading = collect($readingPlans)->firstWhere('day', $currentDayName) ?? $readingPlans[0];
    $memoryVerse = $memoryVerses[array_rand($memoryVerses)];
@endphp

<header class="top" id="home">
    @foreach($slidesData as $i => $slide)
        <div class="slide {{ $i === 0 ? 'active' : '' }}" data-slide>
            <img src="{{ $slide['image'] }}" alt="slide {{ $i + 1 }}">
        </div>
    @endforeach
    <div class="overlay">
        <div class="hero-content enter" id="heroContent">
            <span class="badge">Sekolah Minggu DSCMKids</span>
            <h1 class="hero-title" id="heroTitle">{{ $slidesData[0]['title'] ?? ($sections['hero']->title ?? 'Selamat Datang') }}</h1>
            <p class="hero-sub" id="heroSubtitle">{{ $slidesData[0]['subtitle'] ?? ($sections['hero']->content ?? '') }}</p>
            <div class="hero-actions">
                <a href="#kids-zone" class="btn btn-light">Zona Murid</a>
                <a href="#analytics" class="btn btn-light">Lihat Kehadiran</a>
                <a href="#teachers" class="btn btn-ghost">Portfolio Guru</a>
                <a href="#gallery" class="btn btn-ghost">Galeri</a>
                <a href="{{ route('news.index') }}" class="btn btn-ghost">Semua Berita</a>
                @auth
                    @if(auth()->user()->role === 'student')
                        <form method="POST" action="{{ route('student.logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-ghost">Logout Murid ({{ auth()->user()->name }})</button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('student.login') }}" class="btn btn-ghost">Login Murid</a>
                    <a href="{{ route('student.register') }}" class="btn btn-ghost">Daftar Murid</a>
                @endauth
                <a href="{{ route('student.arcade') }}" class="btn btn-ghost">Arcade Murid</a>
                <a href="{{ route('admin.login') }}" class="btn btn-ghost">Admin</a>
            </div>
        </div>
    </div>
    <div class="hero-nav" id="heroNav">
        @foreach($slidesData as $i => $slide)
            <div class="dot {{ $i === 0 ? 'active' : '' }}" data-dot="{{ $i }}"><span></span></div>
        @endforeach
    </div>
</header>

<nav class="quick-nav">
    <a href="#monthly-theme">Tema Bulanan</a>
    <a href="#renungan">Renungan</a>
    <a href="#kids-zone">Zona Murid</a>
    <a href="#quiz-zone">Quiz & Ranking</a>
    <a href="#weekly-gallery">Galeri Minggu Ini</a>
    <a href="#analytics">Analytics</a>
    <a href="#informasi">Informasi</a>
    <a href="#teachers">Guru</a>
    <a href="#gallery">Galeri</a>
    <a href="#live">Live</a>
</nav>

<main class="container">
    @if($dailyResetNotice)
        <section class="section panel reveal" id="dailyResetNotice">
            <strong>Daily Quest Reset</strong>
            <p class="muted" style="margin:6px 0 0;">Quest harian sudah direset. Kerjakan quiz hari ini dan main arcade untuk naik ranking.</p>
            <div class="hero-actions" style="margin-top:10px;">
                <button type="button" class="btn btn-light" id="dismissResetNotice">Siap, lanjut main</button>
            </div>
        </section>
    @endif

    @if(session('success'))
        <section class="section panel reveal">
            <strong>{{ session('success') }}</strong>
        </section>
    @endif

    <section class="stats reveal">
        <article class="stat"><div class="s-label">Jumlah Siswa</div><div class="s-value" data-counter="{{ (int) ($metrics['students_total'] ?? 0) }}">{{ number_format((int) ($metrics['students_total'] ?? 0)) }}</div></article>
        <article class="stat"><div class="s-label">Hadir Hari Ini</div><div class="s-value" id="rolledPresent" data-counter="{{ (int) ($metrics['attendance_today'] ?? 0) }}">{{ number_format((int) ($metrics['attendance_today'] ?? 0)) }}</div></article>
        <article class="stat"><div class="s-label">Persentase Hadir</div><div class="s-value" data-counter="{{ (int) round((float) ($metrics['attendance_rate'] ?? 0)) }}" data-suffix="%">{{ number_format((float) ($metrics['attendance_rate'] ?? 0), 1) }}%</div></article>
        <article class="stat"><div class="s-label">Kelas Aktif</div><div class="s-value" data-counter="{{ (int) ($metrics['active_classes'] ?? 0) }}">{{ number_format((int) ($metrics['active_classes'] ?? 0)) }}</div></article>
    </section>

    <section class="section panel reveal monthly-theme-panel" id="monthly-theme">
        <p class="theme-kicker">{{ $themeMonthly['subtitle'] }}</p>
        <h2 class="title">{{ $themeMonthly['title'] }}</h2>
        <p class="muted">{{ $themeMonthly['description'] }}</p>
        <div class="theme-meta-row">
            <div class="theme-verse">Ayat Tema: <strong>{{ $themeMonthly['verse'] }}</strong></div>
            <div class="theme-highlight">{{ $themeMonthly['highlight'] }}</div>
        </div>
    </section>

    <section class="section panel reveal devotion-panel" id="renungan">
        <h2 class="title">{{ $devotion['section_title'] }}</h2>
        <p class="muted">Hari ini <strong>{{ $devotion['day'] }}</strong> | <strong>{{ $devotion['verse'] }}</strong></p>
        <div class="devotion-grid">
            <article class="devotion-card">
                <h3>{{ $devotion['title'] }}</h3>
                <p>{{ $devotion['message'] }}</p>
            </article>
            <article class="devotion-card devotion-challenge">
                <h3>Misi Iman Hari Ini</h3>
                <p>{{ $devotion['challenge'] }}</p>
                <div class="verse-pill">Doa singkat: "Tuhan Yesus, tolong aku jadi pelaku firman-Mu hari ini. Amin."</div>
            </article>
        </div>
    </section>

    <section class="theater reveal" id="live">
        <div class="theater-head">
            <div>
                <h2 class="title">{{ $liveStream['title'] ?? 'Live Streaming Ibadah Anak' }}</h2>
                <p>{{ $liveStream['description'] ?? 'Saksikan siaran langsung DSCMKids.' }}</p>
            </div>
            <span class="live-badge {{ !($liveStream['is_live'] ?? false) ? 'offline' : '' }}">{{ ($liveStream['is_live'] ?? false) ? 'LIVE NOW' : 'OFFLINE' }}</span>
        </div>
        <div class="theater-screen">
            @if(!empty($liveStream['embed_url']))
                <iframe class="theater-iframe" src="{{ $liveStream['embed_url'] }}" title="Live Streaming DSCMKids" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            @else
                <div class="theater-empty">Link YouTube live belum diatur.<br>Isi di Admin &gt; Konten (`section_key: livestream`, `meta.youtube_url`).</div>
            @endif
        </div>
    </section>

    <section class="section panel reveal" id="analytics">
        <h2 class="title">Kehadiran Hari Ini (PG, TKA, TKB, 1-6)</h2>
        <p class="muted">Grafik bulat menampilkan komposisi kehadiran per kelas, terhubung ke database presensi.</p>
        <div class="circle-wrap">
            <div class="donut-box">
                <canvas id="attendanceDonut" height="260"></canvas>
                <div class="donut-center">
                    <div class="big-roll" id="centerRoll">0</div>
                    <div class="small-roll">Murid Hadir</div>
                </div>
            </div>
            <div class="class-list">
                @foreach($classAttendance as $index => $entry)
                    <div class="class-row">
                        <div><span class="chip" data-chip="{{ $index }}"></span>{{ $entry['class'] }}</div>
                        <strong>{{ $entry['present'] }} murid</strong>
                    </div>
                @endforeach
                <div class="class-row total-row">
                    <div>Total Tidak Hadir</div>
                    <strong>{{ $attendanceTotals['absent'] ?? 0 }} murid</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="section panel reveal" id="weekly-gallery">
        <h2 class="title">Galeri Minggu Ini: Selfie Presensi</h2>
        <p class="muted">Otomatis dari database presensi minggu berjalan (Senin-Minggu).</p>
        <div class="gallery-grid">
            @forelse($weeklyGalleryItems as $photo)
                @php
                    $title = is_array($photo) ? ($photo['title'] ?? 'Selfie Kehadiran') : 'Selfie Kehadiran';
                    $date = is_array($photo) ? ($photo['date'] ?? null) : null;
                    $eventName = is_array($photo) ? ($photo['event_name'] ?? 'Selfie Absensi Minggu Ini') : 'Selfie Absensi Minggu Ini';
                    $pathValue = is_array($photo) ? ($photo['path'] ?? null) : null;
                    $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://')) ? $pathValue : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
                @endphp
                <figure class="photo">
                    @if($src)
                        <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventName }}{{ $date ? ' - '.$date : '' }}" data-lightbox-index="{{ $loop->index }}">
                    @endif
                    <figcaption class="caption"><strong>{{ $title }}</strong><br>{{ $eventName }}{{ $date ? ' - '.$date : '' }}</figcaption>
                </figure>
            @empty
                <figure class="photo"><figcaption class="caption"><strong>Belum ada selfie presensi minggu ini</strong><br>Pastikan kolom selfie dan tanggal presensi sudah termapping di `.env`.</figcaption></figure>
            @endforelse
        </div>
    </section>

    <section class="section panel kids-zone reveal" id="kids-zone">
        <h2 class="title">Zona Murid: Bacaan Alkitab Minggu Ini</h2>
        <p class="kids-subtitle">Hari ini <strong>{{ $todayReading['day'] }}</strong> - {{ $todayReading['title'] }} ({{ $todayReading['reference'] }}).</p>
        <div class="kids-grid">
            <article class="kids-card">
                <h3 class="title kids-mini-title">Rencana Bacaan 7 Hari</h3>
                <div class="verse-pill">Ayat Hafalan: "{{ $memoryVerse['text'] }}" - <strong>{{ $memoryVerse['reference'] }}</strong></div>
                <div class="reading-list">
                    @foreach($readingPlans as $plan)
                        <div class="reading-item">
                            <div>
                                <div class="reading-day">{{ $plan['day'] }}</div>
                                <div class="reading-ref">{{ $plan['reference'] }}</div>
                            </div>
                            <div class="muted">{{ $plan['point'] }}</div>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="kids-card">
                <h3 class="title kids-mini-title">Badge Progress Mingguan</h3>
                @if(auth()->check() && auth()->user()->role === 'student')
                    <p class="muted">
                        Badge aktif: <strong>{{ $studentProgress['weekly_badge'] ?? 'Faith Starter' }}</strong>.
                        Hari aktif minggu ini: {{ $studentProgress['weekly_completed_days'] ?? 0 }}/7.
                    </p>
                    <div class="challenge-progress"><span style="width: {{ (int) ($studentProgress['weekly_completion_percent'] ?? 0) }}%"></span></div>
                    <div class="challenge-score">{{ (int) ($studentProgress['weekly_total_score'] ?? 0) }} poin minggu ini</div>
                    @if($studentProgress['weekly_reward_claimed'] ?? false)
                        <div class="quiz-result" style="color:#15803d;">Reward minggu ini sudah diklaim.</div>
                    @elseif($studentProgress['weekly_reward_claimable'] ?? false)
                        <div class="hero-actions" style="margin-top:10px;">
                            <button class="btn btn-light" type="button" id="claimWeeklyRewardBtn">Klaim Reward Mingguan</button>
                        </div>
                        <div class="quiz-result" id="claimRewardResult"></div>
                    @else
                        <div class="quiz-result">
                            Kumpulkan minimal {{ (int) ($studentProgress['weekly_reward_threshold'] ?? 240) }} poin/minggu untuk klaim reward.
                        </div>
                    @endif
                @else
                    <p class="muted">
                        Login murid dulu untuk menyimpan skor quiz, naik badge mingguan, dan masuk ranking harian.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('student.login') }}" class="btn btn-light">Login Murid</a>
                        <a href="{{ route('student.register') }}" class="btn btn-ghost">Daftar Murid</a>
                    </div>
                @endif

                <h3 class="title kids-mini-title" style="margin-top:14px;">Tantangan Iman Mingguan</h3>
                <p class="muted">Checklist ini tersimpan di perangkat kamu. Yuk capai 4/4!</p>
                <div class="challenge-list" id="challengeList">
                    @foreach($challengeItems as $index => $task)
                        <label class="challenge-item">
                            <input type="checkbox" data-challenge-index="{{ $index }}">
                            <span>{{ $task }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="challenge-progress"><span id="challengeProgressBar"></span></div>
                <div class="challenge-score" id="challengeScore">0/4 Tantangan selesai</div>
            </article>
        </div>
    </section>

    <section class="section panel reveal" id="quiz-zone">
        <h2 class="title">Zona Murid v2: Kuis Ayat Harian & Ranking</h2>
        <p class="muted">{{ $todayQuiz['title'] ?? 'Kuis Ayat Harian' }} | Ayat minggu ini: <strong>{{ $todayQuiz['memory_verse'] ?? '-' }}</strong></p>
        <div class="kids-grid">
            <article class="kids-card">
                <h3 class="title kids-mini-title">Kuis Hari Ini</h3>
                @if(auth()->check() && auth()->user()->role === 'student')
                    <form id="dailyQuizForm">
                        <input type="hidden" name="quiz_key" value="{{ $todayQuiz['quiz_key'] ?? '' }}">
                        <div class="quiz-list">
                            @foreach(($todayQuiz['questions'] ?? []) as $question)
                                <div class="quiz-item">
                                    <div class="quiz-question">{{ $loop->iteration }}. {{ $question['question'] }}</div>
                                    <div class="quiz-options">
                                        @foreach($question['options'] as $option)
                                            <label class="quiz-option">
                                                <input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option }}">
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-light" type="submit">Kirim Jawaban</button>
                        <div class="quiz-result" id="quizResultBox"></div>
                    </form>
                @else
                    <p class="muted">Login sebagai murid untuk mengerjakan kuis dan simpan skor harian.</p>
                    <div class="hero-actions">
                        <a href="{{ route('student.login') }}" class="btn btn-light">Login Murid</a>
                        <a href="{{ route('student.register') }}" class="btn btn-ghost">Daftar Murid</a>
                    </div>
                @endif
            </article>
            <article class="kids-card">
                <h3 class="title kids-mini-title">Ranking Harian</h3>
                <div id="dailyLeaderboardList" class="leaderboard-list">
                    @forelse($dailyLeaderboard as $item)
                        <div class="leaderboard-row">
                            <strong>#{{ $item['rank'] }} {{ $item['name'] }}</strong>
                            <span>{{ $item['score'] }} pts</span>
                        </div>
                    @empty
                        <div class="leaderboard-row">Belum ada skor hari ini.</div>
                    @endforelse
                </div>

                <h3 class="title kids-mini-title" style="margin-top:14px;">Top Mingguan</h3>
                <div class="leaderboard-list">
                    @forelse($weeklyLeaderboard as $item)
                        <div class="leaderboard-row">
                            <strong>#{{ $item['rank'] }} {{ $item['name'] }}</strong>
                            <span>{{ $item['weekly_score'] }} pts</span>
                        </div>
                    @empty
                        <div class="leaderboard-row">Belum ada skor minggu ini.</div>
                    @endforelse
                </div>
            </article>
        </div>
        <div class="games-grid">
            @foreach($miniGames as $game)
                <article class="game-card">
                    <h4>{{ $game['title'] }}</h4>
                    <p>{{ $game['description'] }}</p>
                    <a class="btn btn-light" href="{{ route('student.arcade') }}">Mainkan di Arcade</a>
                </article>
            @endforeach
        </div>
        <div class="game-output">Buka halaman Arcade Murid untuk gameplay penuh + penyimpanan skor.</div>
    </section>

    <section class="section grid-2 reveal" id="informasi">
        <article class="panel">
            <h2 class="title">Konten Umum Informatif & Edukatif</h2>
            <div class="cards">
                <div class="info-card c1"><h3>Kelas Kreatif Alkitab</h3><p>Anak belajar firman Tuhan lewat aktivitas visual, musik, dan permainan edukatif.</p></div>
                <div class="info-card c2"><h3>Parent Insight</h3><p>Ringkasan perkembangan rohani anak dan komunikasi rutin untuk orang tua.</p></div>
                <div class="info-card c3"><h3>Growth Journey</h3><p>Pemantauan keterlibatan, kehadiran, dan partisipasi per kelas secara berkala.</p></div>
            </div>
        </article>
        <article class="panel">
            <h2 class="title">Informasi Pelayanan</h2>
            <div class="ann-list">
                @forelse($announcements as $item)
                    <div class="item">
                        <h3>{{ $item->title }}</h3>
                        <div class="meta">{{ optional($item->event_date)->format('d M Y') ?? 'Tanggal menyusul' }} {{ $item->location ? '- '.$item->location : '' }}</div>
                        <p>{{ $item->body }}</p>
                    </div>
                @empty
                    <div class="item">Belum ada informasi.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="section panel reveal" id="teachers">
        <h2 class="title">Portfolio Singkat Guru</h2>
        <p class="muted">Profil guru diinput dari admin panel.</p>
        <div class="teacher-grid">
            @forelse($teachers as $teacher)
                <article class="teacher">
                    @if($teacher->photo_path)
                        <img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="{{ $teacher->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1545239351-1141bd82e8a6?q=80&w=900&auto=format&fit=crop" alt="{{ $teacher->name }}">
                    @endif
                    <h4>{{ $teacher->name }}</h4>
                    <p>{{ $teacher->role ?? 'Pengajar Sekolah Minggu' }}</p>
                    <p><strong>Kelas:</strong> {{ $teacher->class_group ?? '-' }}</p>
                    @if($teacher->bio)
                        <p>{{ \Illuminate\Support\Str::limit($teacher->bio, 88) }}</p>
                    @endif
                </article>
            @empty
                <article class="teacher"><h4>Belum ada data guru</h4><p>Tambah dari admin panel.</p></article>
            @endforelse
        </div>
    </section>

    <section class="section panel reveal">
        <h2 class="title">Berita Terbaru</h2>
        <div class="news-list">
            @forelse($news as $item)
                <article class="item">
                    <h3><a href="{{ route('news.show', $item->slug) }}" class="news-link-title">{{ $item->title }}</a></h3>
                    <div class="meta">{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</div>
                    <p>{{ $item->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($item->body), 160) }}</p>
                    <a href="{{ route('news.show', $item->slug) }}" class="news-link-more">Baca Selengkapnya</a>
                </article>
            @empty
                <article class="item">Belum ada berita.</article>
            @endforelse
        </div>
    </section>

    <section class="section panel reveal" id="gallery">
        <h2 class="title">Galeri Kegiatan</h2>
        <div class="filter-bar">
            <a href="{{ route('landing') }}#gallery" class="filter-pill {{ empty($activeEvent) ? 'active' : '' }}">Semua Event</a>
            @foreach($galleryEvents as $event)
                <a href="{{ route('landing', ['event' => $event]) }}#gallery" class="filter-pill {{ $activeEvent === $event ? 'active' : '' }}">{{ $event }}</a>
            @endforeach
        </div>
        <div class="gallery-grid">
            @forelse($galleryItems as $photo)
                @php
                    $title = is_array($photo) ? ($photo['title'] ?? 'Kegiatan DSCMKids') : $photo->title;
                    $date = is_array($photo) ? ($photo['date'] ?? null) : optional($photo->created_at)->format('d M Y');
                    $eventName = is_array($photo) ? ($photo['event_name'] ?? 'Kegiatan Umum') : 'Kegiatan Umum';
                    $eventSlug = is_array($photo) ? ($photo['event_slug'] ?? \Illuminate\Support\Str::slug($eventName)) : \Illuminate\Support\Str::slug($eventName);
                    $pathValue = is_array($photo) ? ($photo['path'] ?? null) : asset('storage/'.$photo->file_path);
                    $src = is_string($pathValue) && (str_starts_with($pathValue, 'http://') || str_starts_with($pathValue, 'https://')) ? $pathValue : (is_string($pathValue) ? asset(ltrim($pathValue, '/')) : null);
                @endphp
                <figure class="photo">
                    @if($src)
                        <img src="{{ $src }}" alt="{{ $title }}" data-lightbox-src="{{ $src }}" data-lightbox-title="{{ $title }}" data-lightbox-meta="{{ $eventName }}{{ $date ? ' - '.$date : '' }}" data-lightbox-index="{{ $loop->index }}">
                    @endif
                    <figcaption class="caption"><strong>{{ $title }}</strong><br>{{ $eventName }}{{ $date ? ' - '.$date : '' }}<br><a href="{{ route('gallery.event', ['eventSlug' => $eventSlug]) }}">Detail Event</a></figcaption>
                </figure>
            @empty
                <figure class="photo"><figcaption class="caption"><strong>Tidak ada foto untuk event ini</strong></figcaption></figure>
            @endforelse
        </div>
    </section>

    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" id="lightboxClose" aria-label="Tutup">&times;</button>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Sebelumnya">&#10094;</button>
        <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Berikutnya">&#10095;</button>
        <div>
            <img id="lightboxImage" src="" alt="Preview">
            <div class="lightbox-meta" id="lightboxMeta"></div>
        </div>
    </div>

    <footer class="footer">&copy; {{ date('Y') }} DSCMKids - Dirancang untuk anak sekolah minggu dan orang tua murid.</footer>
</main>

<script>
window.DSCM_LANDING_DATA = {
    slides: @json($slidesData),
    classData: @json($classAttendance),
    presentTotal: @json((int) ($attendanceTotals['present'] ?? 0)),
    absentTotal: @json((int) ($attendanceTotals['absent'] ?? 0)),
    csrfToken: @json(csrf_token()),
    quizSubmitUrl: @json(route('student.quiz.submit')),
    rewardClaimUrl: @json(route('student.reward.claim')),
    resetSeenUrl: @json(route('student.reset.seen')),
    isStudentLoggedIn: @json(auth()->check() && auth()->user()->role === 'student'),
};
</script>
</body>
</html>

