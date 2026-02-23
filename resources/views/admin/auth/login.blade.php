<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin DSCMKids</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(140deg, #0f766e 0%, #0e7490 55%, #1d4ed8 100%);
            font-family: 'Manrope', sans-serif;
            color: #0f172a;
        }
        .card {
            width: min(420px, 92vw);
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 46px rgba(0, 0, 0, 0.22);
            padding: 22px;
        }
        h1 { margin-top: 0; margin-bottom: 6px; }
        p { margin-top: 0; color: #475569; }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input {
            width: 100%;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px;
            font: inherit;
        }
        button {
            width: 100%;
            margin-top: 16px;
            background: #0f766e;
            color: #fff;
            border: 0;
            border-radius: 10px;
            padding: 10px;
            font-weight: 800;
            cursor: pointer;
        }
        .hint { margin-top: 10px; font-size: 13px; color: #64748b; }
        .error { margin-top: 10px; background: #fee2e2; color: #7f1d1d; padding: 10px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Login Admin</h1>
        <p>DSCMKids Content Management</p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button type="submit">Masuk</button>
        </form>

        <div class="hint">Default: admin@dscmkids.org / password123</div>
    </div>
</body>
</html>
