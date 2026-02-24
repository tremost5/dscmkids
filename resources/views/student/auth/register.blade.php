<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Murid | DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    @if (!app()->environment('testing'))
        @vite(['resources/css/student-auth.css'])
    @endif
</head>
<body data-page="student-auth-register">
<div class="student-auth-card">
    <a class="student-auth-back" href="{{ route('landing') }}">&larr; Kembali ke Landing</a>
    <h1 class="student-auth-title">Daftar Murid</h1>
    <p class="student-auth-subtitle">Buat akun untuk ikut quiz, ranking, dan mini games rohani.</p>
    <p class="student-auth-benefits">Akun ini untuk murid/pengunjung. Admin tetap login dari panel admin.</p>

    @if($errors->any())
        <div class="student-auth-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('student.register.submit') }}">
        @csrf
        <div class="student-auth-field">
            <label for="name">Nama Lengkap</label>
            <input class="student-auth-input" id="name" type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="student-auth-field">
            <label for="class_group">Kelas (opsional)</label>
            <input class="student-auth-input" id="class_group" type="text" name="class_group" value="{{ old('class_group') }}" placeholder="Contoh: TKB / Kelas 1">
            <div class="student-auth-help">Boleh dikosongkan jika belum tahu kelasnya.</div>
        </div>
        <div class="student-auth-field">
            <label for="email">Email</label>
            <input class="student-auth-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="student-auth-field">
            <label for="password">Password</label>
            <input class="student-auth-input" id="password" type="password" name="password" required>
            <div class="student-auth-help">Gunakan kombinasi huruf/angka agar lebih aman.</div>
        </div>
        <div class="student-auth-field">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input class="student-auth-input" id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <button class="student-auth-btn" type="submit">Daftar Murid</button>
    </form>

    <div class="student-auth-switch">
        Sudah punya akun? <a href="{{ route('student.login') }}">Login murid</a>
    </div>
</div>
</body>
</html>
