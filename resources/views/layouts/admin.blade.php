<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | MAHAPATI</title>
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('mahapati-dashboard-theme');
                document.documentElement.dataset.theme = savedTheme === 'dark' ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=21">
</head>
<body class="dashboard-body">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar" id="dashboard-sidebar" aria-label="Navigasi dashboard">
            <a class="dashboard-brand" href="{{ auth()->user()?->role === 'pengurus' ? route('pengurus.dashboard') : (auth()->user()?->role === 'anggota' ? route('member.dashboard') : route('admin.dashboard')) }}">
                <img src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span><strong>MAHAPATI</strong><small>{{ auth()->user()?->role === 'pengurus' ? 'PENGURUS' : 'SISTEM INFORMASI' }}</small></span>
            </a>
            <nav class="dashboard-nav">
                @if (auth()->user()?->role === 'pengurus')
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.dashboard') ? 'is-active' : '' }}" href="{{ route('pengurus.dashboard') }}"><span>⌂</span>Dashboard</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.activities.*', 'pengurus.work-programs.*') ? 'is-active' : '' }}" href="{{ route('pengurus.activities.index') }}"><span>▣</span>Kegiatan</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.gallery.*') ? 'is-active' : '' }}" href="{{ route('pengurus.gallery.index') }}"><span>▧</span>Galeri</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.news.*') ? 'is-active' : '' }}" href="{{ route('pengurus.news.index') }}"><span>▤</span>Berita</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.achievements.*') ? 'is-active' : '' }}" href="{{ route('pengurus.achievements.index') }}"><span>♜</span>Prestasi</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.registration.*') ? 'is-active' : '' }}" href="{{ route('pengurus.registration.index') }}"><span>▣</span>Pendaftaran</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.certificates.*') ? 'is-active' : '' }}" href="{{ route('pengurus.certificates.mine') }}"><span>▧</span>Sertifikat Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.members.*') ? 'is-active' : '' }}" href="{{ route('pengurus.members.index') }}"><span>♙</span>Daftar Anggota</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.notifications.*') ? 'is-active' : '' }}" href="{{ route('pengurus.notifications.index') }}"><span>♧</span>Notifikasi @if (($unreadDashboardNotifications ?? 0) > 0)<span class="nav-count">{{ $unreadDashboardNotifications }}</span>@endif</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('pengurus.profile.*') ? 'is-active' : '' }}" href="{{ route('pengurus.profile.edit') }}"><span>♙</span>Profil</a>
                @elseif (auth()->user()?->role === 'anggota')
                    <a class="dashboard-nav-link {{ request()->routeIs('member.dashboard') ? 'is-active' : '' }}" href="{{ route('member.dashboard') }}"><span>⌂</span>Dashboard</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.profile.*') ? 'is-active' : '' }}" href="{{ route('member.profile.edit') }}"><span>♙</span>Profil Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.domicile.*') ? 'is-active' : '' }}" href="{{ route('member.domicile.edit') }}"><span>⌖</span>Domisili Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.activities.*', 'member.work-programs.*') ? 'is-active' : '' }}" href="{{ route('member.activities.index') }}"><span>▣</span>Kegiatan Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.achievement-submissions.*') ? 'is-active' : '' }}" href="{{ route('member.achievement-submissions.index') }}"><span>♜</span>Prestasi Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.certificates.*') ? 'is-active' : '' }}" href="{{ route('member.certificates.mine') }}"><span>▧</span>Sertifikat Saya</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.gallery-submissions.*') ? 'is-active' : '' }}" href="{{ route('member.gallery-submissions.index') }}"><span>✎</span>Kirim Konten</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.notifications.*') ? 'is-active' : '' }}" href="{{ route('member.notifications.index') }}"><span>♧</span>Notifikasi @if (($unreadDashboardNotifications ?? 0) > 0)<span class="nav-count">{{ $unreadDashboardNotifications }}</span>@endif</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.account-settings.*') ? 'is-active' : '' }}" href="{{ route('member.account-settings.edit') }}"><span>⚙</span>Pengaturan Akun</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('member.account.*') ? 'is-active' : '' }}" href="{{ route('member.account.show') }}"><span>♙</span>Akun</a>
                @else
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span>⌂</span>Dashboard</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.members.*') ? 'is-active' : '' }}" href="{{ route('admin.members.index') }}"><span>♙</span>Manajemen Anggota</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.domiciles.*') ? 'is-active' : '' }}" href="{{ route('admin.domiciles.index') }}"><span>⌖</span>Domisili Anggota</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.activities.*', 'admin.work-programs.*', 'admin.activity-requests.*') ? 'is-active' : '' }}" href="{{ route('admin.activities.index') }}"><span>▣</span>Kegiatan @if (($pendingActivityRequests ?? 0) > 0)<span class="nav-count">{{ $pendingActivityRequests }}</span>@endif</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.gallery.*', 'admin.gallery-requests.*') ? 'is-active' : '' }}" href="{{ route('admin.gallery.index') }}"><span>▧</span>Galeri @if (($pendingGalleryRequests ?? 0) > 0)<span class="nav-count">{{ $pendingGalleryRequests }}</span>@endif</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.cash-transactions.*') ? 'is-active' : '' }}" href="{{ route('admin.cash-transactions.index') }}"><span>▤</span>Kas</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.news.*') ? 'is-active' : '' }}" href="{{ route('admin.news.index') }}"><span>▤</span>Berita</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.achievements.*') ? 'is-active' : '' }}" href="{{ route('admin.achievements.index') }}"><span>♜</span>Prestasi</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.registration.*') ? 'is-active' : '' }}" href="{{ route('admin.registration.index') }}"><span>▣</span>Pendaftaran</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.certificates.*') ? 'is-active' : '' }}" href="{{ route('admin.certificates.index') }}"><span>▧</span>Sertifikat</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.notifications.*', 'admin.account-requests.*') ? 'is-active' : '' }}" href="{{ route('admin.notifications.index') }}"><span>♧</span>Notifikasi @if (($unreadDashboardNotifications ?? 0) > 0)<span class="nav-count">{{ $unreadDashboardNotifications }}</span>@endif</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.audit.*') ? 'is-active' : '' }}" href="{{ route('admin.audit.index') }}"><span>▤</span>Audit Log</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.backup.*') ? 'is-active' : '' }}" href="{{ route('admin.backup.index') }}"><span>▣</span>BackUp &amp; Export</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}" href="{{ route('admin.settings.edit') }}"><span>⚙</span>Pengaturan</a>
                    <a class="dashboard-nav-link {{ request()->routeIs('admin.profile.*') ? 'is-active' : '' }}" href="{{ route('admin.profile.edit') }}"><span>♙</span>Profil</a>
                @endif
            </nav>
            <form class="sidebar-logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dashboard-nav-link" type="submit"><span>⇥</span>Keluar</button>
            </form>
        </aside>
        <div class="dashboard-main">
            <header class="dashboard-topbar">
                <button class="sidebar-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="dashboard-sidebar" data-sidebar-toggle><span></span><span></span><span></span></button>
                <form class="topbar-search" method="GET" action="{{ auth()->user()?->role === 'pengurus' ? route('pengurus.activities.index') : (auth()->user()?->role === 'anggota' ? route('member.activities.index') : route('admin.members.index')) }}">
                    <span aria-hidden="true">⌕</span><input type="search" name="q" placeholder="Cari nama, NRA, atau NIM..." value="{{ request('q') }}" aria-label="Cari anggota">
                </form>
                <a class="topbar-public-link" href="{{ route('home') }}" aria-label="Kembali ke website publik">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z"></path></svg>
                    <span>Website Publik</span>
                </a>
                <div class="topbar-user">
                    <button class="dashboard-theme-toggle" type="button" data-theme-toggle aria-label="Aktifkan mode gelap" title="Aktifkan mode gelap">
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path></svg>
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.2 15.2A8.5 8.5 0 0 1 8.8 3.8 8.6 8.6 0 1 0 20.2 15.2Z"></path></svg>
                    </button>
                    @php($notificationIndexRoute = auth()->user()?->role === 'pengurus' ? 'pengurus.notifications.index' : (auth()->user()?->role === 'anggota' ? 'member.notifications.index' : 'admin.notifications.index'))
                    <a class="topbar-notification" href="{{ route($notificationIndexRoute) }}" aria-label="Notifikasi">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                            @if (($unreadDashboardNotifications ?? 0) > 0)<i>{{ $unreadDashboardNotifications }}</i>@endif
                    </a>
                    <span class="user-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    <span class="user-identity"><strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong><small>{{ ucfirst(auth()->user()->role ?? 'anggota') }}</small></span>
                </div>
            </header>
            <main class="dashboard-content">
                @yield('content')
            </main>
        </div>
    </div>
    <div class="sidebar-backdrop" data-sidebar-close></div>
    @stack('scripts')
    <script>
        (() => {
            const shell = document.querySelector('.dashboard-shell');
            const toggle = document.querySelector('[data-sidebar-toggle]');
            const backdrop = document.querySelector('[data-sidebar-close]');
            const closeSidebar = () => {
                shell?.classList.remove('sidebar-open');
                toggle?.setAttribute('aria-expanded', 'false');
                toggle?.setAttribute('aria-label', 'Buka menu');
                toggle?.setAttribute('title', 'Buka menu');
            };
            toggle?.addEventListener('click', () => {
                const isOpen = shell.classList.toggle('sidebar-open');
                toggle.setAttribute('aria-expanded', String(isOpen));
                toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
                toggle.setAttribute('title', isOpen ? 'Tutup menu' : 'Buka menu');
            });
            backdrop?.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && shell?.classList.contains('sidebar-open')) {
                    closeSidebar();
                }
            });
            const themeToggle = document.querySelector('[data-theme-toggle]');
            const setTheme = (theme) => {
                document.documentElement.dataset.theme = theme;
                const nextThemeLabel = theme === 'dark' ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
                themeToggle?.setAttribute('aria-label', nextThemeLabel);
                themeToggle?.setAttribute('title', nextThemeLabel);
                try {
                    localStorage.setItem('mahapati-dashboard-theme', theme);
                } catch (error) {
                    // Theme still works for this page when browser storage is unavailable.
                }
            };
            themeToggle?.addEventListener('click', () => {
                setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
            });
            document.querySelectorAll('.dashboard-nav-link[aria-disabled="true"]').forEach((link) => {
                link.addEventListener('click', (event) => event.preventDefault());
            });
        })();
    </script>
</body>
</html>
