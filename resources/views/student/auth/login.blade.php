<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Murid | DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    @if (!app()->environment('testing'))
        @vite(['resources/css/student-auth.css'])
    @endif
</head>
<body data-page="student-auth-login">
<div class="student-auth-card">
    <a class="student-auth-back" href="{{ route('landing') }}">&larr; Kembali ke Landing</a>
    <h1 class="student-auth-title">Login Murid</h1>
    <p class="student-auth-subtitle">Masuk untuk main kuis ayat harian dan kumpulkan badge.</p>
    <p class="student-auth-benefits">Benefit akun murid: simpan skor quiz, masuk ranking harian, klaim reward mingguan.</p>

    @if($errors->any())
        <div class="student-auth-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('student.login.submit') }}">
        @csrf
        <div class="student-auth-field">
            <label for="email">Email</label>
            <input class="student-auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="student-auth-field">
            <label for="password">Password</label>
            <input class="student-auth-input" id="password" type="password" name="password" required>
            <div class="student-auth-help">Minimal 6 karakter.</div>
        </div>
        <button class="student-auth-btn" type="submit">Masuk Sekarang</button>
    </form>

    <div class="student-auth-switch">
        Belum punya akun? <a href="{{ route('student.register') }}">Daftar murid</a>
    </div>
</div>
</body>
</html>
