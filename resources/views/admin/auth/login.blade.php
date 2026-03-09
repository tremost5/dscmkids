<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin DSCMKids</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            background:
                radial-gradient(circle at 0% 0%, rgba(20, 184, 166, .28), transparent 34%),
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, .24), transparent 30%),
                linear-gradient(145deg, #061325, #102a43 42%, #1d4ed8 100%);
            font-family: 'Manrope', sans-serif;
            color: #0f172a;
        }
        .login-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 420px);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 28px;
            background: rgba(255,255,255,.92);
            box-shadow: 0 30px 80px rgba(2, 8, 23, .34);
        }
        .login-aside {
            padding: 38px;
            background:
                radial-gradient(circle at top right, rgba(20,184,166,.22), transparent 34%),
                linear-gradient(155deg, #0f172a, #102a43 45%, #1d4ed8 100%);
            color: #f8fafc;
        }
        .login-kicker {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .login-aside h1 {
            margin: 16px 0 10px;
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1.02;
        }
        .login-aside p {
            margin: 0;
            color: rgba(226, 232, 240, .84);
            line-height: 1.7;
        }
        .login-bullets {
            margin-top: 24px;
            display: grid;
            gap: 12px;
        }
        .login-bullets div {
            padding: 14px 16px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 18px;
            background: rgba(255,255,255,.08);
            font-weight: 700;
        }
        .login-card {
            padding: 34px 28px;
        }
        .login-card h2 {
            margin: 0 0 6px;
            font-size: 1.6rem;
        }
        .login-card p {
            margin: 0 0 18px;
            color: #64748b;
        }
        label {
            display: block;
            margin-top: 14px;
            font-weight: 800;
        }
        input {
            width: 100%;
            margin-top: 8px;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            background: #f8fbff;
            font: inherit;
        }
        input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(96,165,250,.16);
            outline: none;
        }
        button {
            width: 100%;
            margin-top: 18px;
            border: 0;
            border-radius: 14px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #0f766e, #1d4ed8);
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }
        .hint {
            margin-top: 14px;
            color: #64748b;
            font-size: .86rem;
        }
        .error {
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fee2e2;
            color: #7f1d1d;
            font-weight: 700;
        }
        @media (max-width: 860px) {
            .login-shell {
                grid-template-columns: 1fr;
            }
            .login-aside,
            .login-card {
                padding: 26px 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <aside class="login-aside">
            <span class="login-kicker">DSCMKids Console</span>
            <h1>Modern admin access for content and operations.</h1>
            <p>Kelola dashboard, user management, monitoring, berita, materi, dan broadcast dari satu workspace yang lebih aman dan responsif.</p>
            <div class="login-bullets">
                <div>Role-aware admin workspace</div>
                <div>Queued notifications and monitoring</div>
                <div>Mobile-ready navigation and faster workflows</div>
            </div>
        </aside>

        <section class="login-card">
            <h2>Login Admin</h2>
            <p>Masuk ke DSCMKids Content Management.</p>

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
        </section>
    </div>
</body>
</html>
