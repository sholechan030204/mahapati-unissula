<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke sistem informasi MAHAPATI UNISSULA.">
    <title>Masuk | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=2">
</head>
<body class="auth-page">
    <main class="auth-layout">
        <section class="auth-visual auth-visual-login" aria-label="Identitas MAHAPATI">
            <a class="auth-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam<br>Teknologi Industri</small></span>
            </a>
            <div class="auth-visual-copy">
                <p class="auth-eyebrow">Universitas Islam Sultan Agung</p>
                <h1>MAHAPATI</h1>
                <p class="auth-tagline">“Bersama Alam,<br>Berkarya untuk Negeri”</p>
                <div class="auth-values">
                    <div><span aria-hidden="true">✦</span><strong>Lestari Alam</strong><small>Menjaga keseimbangan alam untuk masa depan.</small></div>
                    <div><span aria-hidden="true">◎</span><strong>Solidaritas</strong><small>Tumbuh dan bergerak bersama.</small></div>
                    <div><span aria-hidden="true">▲</span><strong>Berkarya</strong><small>Mengembangkan potensi dan memberi manfaat.</small></div>
                </div>
            </div>
            <p class="auth-visual-footer">Universitas Islam Sultan Agung · Semarang</p>
        </section>

        <section class="auth-panel" aria-labelledby="login-title">
            <a class="auth-mobile-brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span>MAHAPATI</span></a>
            <div class="auth-card">
                <div class="auth-card-heading">
                    <p class="auth-kicker">Sistem Informasi MAHAPATI</p>
                    <h2 id="login-title">Selamat Datang</h2>
                    <p>Masuk menggunakan email yang terdaftar untuk mengakses dashboard.</p>
                </div>

                <form class="auth-form" method="POST" action="{{ route('login.store') }}" aria-describedby="login-status">
                    @csrf
                    <label for="login-email">Email</label>
                    <div class="auth-input-wrap"><span aria-hidden="true">✉</span><input id="login-email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" placeholder="Masukkan email Anda" required></div>
                    @error('email')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <label for="login-password">Password</label>
                    <div class="auth-input-wrap"><span aria-hidden="true">⌑</span><input id="login-password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password Anda" required><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></div>
                    @error('password')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <div class="auth-form-options"><label class="auth-checkbox"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Ingat saya</span></label><a href="{{ route('password.request') }}">Lupa password?</a></div>
                    <button class="auth-submit" type="submit">Masuk <span aria-hidden="true">→</span></button>
                </form>

                @if (session('status'))<p class="auth-status" id="login-status" role="status">{{ session('status') }}</p>@endif

                <div class="auth-divider"><span>atau</span></div>
                <div class="auth-account-note">
                    <span class="auth-note-icon" aria-hidden="true">♙</span>
                    <p>Akun hanya untuk Admin, Pengurus, dan anggota MAHAPATI yang sudah tercatat. Belum memiliki akun? Ajukan akun anggota untuk ditinjau Admin.</p>
                </div>
                <a class="auth-request-link" href="{{ route('account-request.create') }}">Ajukan Akun Anggota</a>
                <a class="auth-back-link" href="{{ route('home') }}">← Kembali ke website MAHAPATI</a>
            </div>
            <p class="auth-copyright">© MAHAPATI UNISSULA</p>
        </section>
    </main>
    <script src="{{ asset('js/auth-password-toggle.js') }}?v=1" defer></script>
</body>
</html>


