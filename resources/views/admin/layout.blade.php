<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin DSCMKids')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f7fb;
            --panel: #ffffff;
            --ink: #1f2937;
            --muted: #5f6778;
            --line: #d7deea;
            --brand: #0f766e;
            --brand-dark: #0a4f4a;
            --danger: #b91c1c;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: radial-gradient(circle at 10% 10%, #ecfeff, #f8fafc 35%, #eef2ff 100%);
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
        }
        .container { max-width: 1120px; margin: 0 auto; padding: 20px; }
        .topbar {
            background: linear-gradient(120deg, #0f766e, #155e75);
            color: #fff;
            border-radius: 18px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        .brand { font-weight: 800; letter-spacing: 0.2px; }
        .nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .nav a, .nav button {
            text-decoration: none;
            border: 0;
            background: rgba(255,255,255,0.14);
            color: #fff;
            border-radius: 10px;
            padding: 8px 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 28px rgba(12, 35, 64, 0.05);
        }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
        .btn {
            border: 0;
            border-radius: 10px;
            padding: 8px 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #111827; }
        .btn-danger { background: var(--danger); color: #fff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
        th { font-size: 13px; color: var(--muted); }
        input, textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px;
            font: inherit;
            margin-top: 6px;
        }
        textarea { min-height: 120px; resize: vertical; }
        label { font-weight: 700; font-size: 14px; }
        .field { margin-bottom: 12px; }
        .muted { color: var(--muted); }
        .flash {
            margin-bottom: 14px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 10px 12px;
            border-radius: 10px;
        }
        .error {
            margin-bottom: 14px;
            background: #fee2e2;
            color: #7f1d1d;
            border: 1px solid #fca5a5;
            padding: 10px 12px;
            border-radius: 10px;
        }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <div class="brand">Admin DSCMKids</div>
        <div class="nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.news.index') }}">Berita</a>
            <a href="{{ route('admin.announcements.index') }}">Informasi</a>
            <a href="{{ route('admin.sections.index') }}">Konten</a>
            <a href="{{ route('admin.media.index') }}">Media</a>
            <a href="{{ route('admin.slides.index') }}">Slide Header</a>
            <a href="{{ route('admin.teachers.index') }}">Portfolio Guru</a>
            <a href="{{ route('landing') }}" target="_blank">Lihat Situs</a>
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        @yield('content')
    </div>
</div>
</body>
</html>
