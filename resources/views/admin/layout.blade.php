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
        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @endif
</head>
<body class="admin-body">
@php($currentRoute = request()->route()?->getName())
<div class="admin-app" id="adminApp">
    <div class="admin-sidebar-backdrop" id="adminSidebarBackdrop"></div>
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-head">
            <div>
                <div class="admin-brand">DSCMKids Console</div>
                <div class="admin-brand-subtitle">Modern operations workspace</div>
            </div>
            <button class="admin-icon-btn admin-desktop-only" type="button" id="adminSidebarCollapse" aria-label="Collapse sidebar">||</button>
        </div>

        <div class="admin-sidebar-section">
            <div class="admin-sidebar-label">Core</div>
            <nav class="admin-sidebar-nav">
                @if(auth()->user()?->hasPermission('dashboard.view'))
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link @if($currentRoute === 'admin.dashboard') active @endif" data-close-sidebar-link>Dashboard</a>
                @endif
                @if(auth()->user()?->hasPermission('users.manage'))
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.users.')) active @endif" data-close-sidebar-link>Users</a>
                @endif
                @if(auth()->user()?->hasPermission('monitoring.view'))
                    <a href="{{ route('admin.system.index') }}" class="admin-nav-link @if($currentRoute === 'admin.system.index') active @endif" data-close-sidebar-link>System Monitor</a>
                @endif
            </nav>
        </div>

        @if(auth()->user()?->hasPermission('content.manage'))
            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">Content</div>
                <nav class="admin-sidebar-nav">
                    <a href="{{ route('admin.news.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.news.')) active @endif" data-close-sidebar-link>Berita</a>
                    <a href="{{ route('admin.announcements.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.announcements.')) active @endif" data-close-sidebar-link>Informasi</a>
                    <a href="{{ route('admin.quiz-banks.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.quiz-banks.')) active @endif" data-close-sidebar-link>Bank Soal</a>
                    <a href="{{ route('admin.sections.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.sections.')) active @endif" data-close-sidebar-link>Konten</a>
                    <a href="{{ route('admin.media.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.media.')) active @endif" data-close-sidebar-link>Media</a>
                    <a href="{{ route('admin.materials.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.materials.')) active @endif" data-close-sidebar-link>Materi</a>
                    <a href="{{ route('admin.slides.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.slides.')) active @endif" data-close-sidebar-link>Slide</a>
                    <a href="{{ route('admin.teachers.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.teachers.')) active @endif" data-close-sidebar-link>Guru</a>
                    <a href="{{ route('admin.testimonials.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.testimonials.')) active @endif" data-close-sidebar-link>Testimoni</a>
                    <a href="{{ route('admin.livestream.edit') }}" class="admin-nav-link @if($currentRoute === 'admin.livestream.edit') active @endif" data-close-sidebar-link>Live</a>
                    <a href="{{ route('admin.spiritual.edit') }}" class="admin-nav-link @if($currentRoute === 'admin.spiritual.edit') active @endif" data-close-sidebar-link>Tema & Renungan</a>
                    <a href="{{ route('admin.parent-portal.edit') }}" class="admin-nav-link @if($currentRoute === 'admin.parent-portal.edit') active @endif" data-close-sidebar-link>Parent Portal</a>
                </nav>
            </div>
        @endif

        @if(auth()->user()?->hasPermission('notifications.manage'))
            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">Automation</div>
                <nav class="admin-sidebar-nav">
                    <a href="{{ route('admin.notifications.index') }}" class="admin-nav-link @if(\Illuminate\Support\Str::startsWith((string) $currentRoute, 'admin.notifications.')) active @endif" data-close-sidebar-link>Notifikasi</a>
                </nav>
            </div>
        @endif

        <div class="admin-sidebar-footer">
            <a href="{{ route('landing') }}" target="_blank" rel="noopener" class="btn btn-secondary">Open Site</a>
            <form action="{{ route('admin.logout') }}" method="POST" data-loading-form>
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </aside>

    <section class="admin-stage">
        <header class="admin-topbar">
            <div class="admin-topbar-group">
                <button class="admin-icon-btn" type="button" id="adminSidebarToggle" aria-label="Open menu">Menu</button>
                <div class="admin-page-meta">
                    <div class="admin-page-kicker">Platform Workspace</div>
                    <div class="admin-page-title">@yield('title', 'Admin DSCMKids')</div>
                </div>
            </div>
            <div class="admin-topbar-group admin-topbar-group--end">
                <div class="admin-user-chip">
                    <strong>{{ auth()->user()?->name }}</strong>
                    <span>{{ auth()->user()?->roleLabel() }}</span>
                </div>
                <a href="{{ route('landing') }}" target="_blank" rel="noopener" class="btn btn-secondary admin-mobile-only-inline">Open Site</a>
            </div>
        </header>

        <main class="admin-shell">
            <div class="admin-toast-stack" id="adminToastStack">
                @if(session('success'))
                    <div class="admin-toast admin-toast--success" data-toast>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="admin-toast-close" data-toast-close aria-label="Close">x</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="admin-toast admin-toast--error" data-toast>
                        <div>{{ $errors->first() }}</div>
                        <button type="button" class="admin-toast-close" data-toast-close aria-label="Close">x</button>
                    </div>
                @endif
            </div>

            <section class="card admin-card">
                @yield('content')
            </section>

            <footer class="admin-footer">
                <span>DSCMKids Admin Console</span>
                <span>{{ now()->format('d M Y H:i') }} server time</span>
            </footer>
        </main>
    </section>
</div>

<div class="admin-loading-layer" id="adminLoadingLayer" aria-hidden="true">
    <div class="admin-loading-card">
        <div class="admin-loading-spinner"></div>
        <div>Processing request...</div>
    </div>
</div>
</body>
</html>
