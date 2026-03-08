<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin DSCMKids')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">

    @if (!app()->environment('testing'))
        @vite('resources/css/admin.css')
    @endif
</head>
<body>
<div class="admin-shell">
    <header class="admin-header">
        <div class="topbar">
            <div class="brand-wrap">
                <div class="brand">Admin DSCMKids</div>
                <div class="brand-subtitle">Kontrol konten, aktivitas murid, dan operasional situs dari satu panel.</div>
            </div>
            <nav class="nav" aria-label="Admin navigation">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.news.index') }}">Berita</a>
                <a href="{{ route('admin.announcements.index') }}">Informasi</a>
                <a href="{{ route('admin.quiz-banks.index') }}">Bank Soal</a>
                <a href="{{ route('admin.sections.index') }}">Konten</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
                <a href="{{ route('admin.materials.index') }}">Materi</a>
                <a href="{{ route('admin.slides.index') }}">Slide</a>
                <a href="{{ route('admin.teachers.index') }}">Guru</a>
                <a href="{{ route('admin.testimonials.index') }}">Testimoni</a>
                <a href="{{ route('admin.notifications.index') }}">Notifikasi</a>
                <a href="{{ route('admin.livestream.edit') }}">Live</a>
                <a href="{{ route('admin.spiritual.edit') }}">Tema & Renungan</a>
                <a href="{{ route('admin.parent-portal.edit') }}">Parent Portal</a>
                <a href="{{ route('landing') }}" target="_blank" rel="noopener">Lihat Situs</a>
                <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <section class="card admin-card">
            @yield('content')
        </section>
    </main>

    <footer class="admin-footer">
        <span>DSCMKids Admin Console</span>
        <span>{{ now()->format('d M Y H:i') }} server time</span>
    </footer>
</div>
</body>
</html>
