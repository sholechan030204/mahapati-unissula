<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=2">
</head>
<body class="auth-page">
    <main class="auth-panel">
        <a class="auth-mobile-brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span>MAHAPATI</span></a>
        <section class="auth-card" aria-labelledby="reset-title">
            <div class="auth-card-heading">
                <p class="auth-kicker">Pengaturan keamanan akun</p>
                <h2 id="reset-title">Buat Password</h2>
                <p>Gunakan password minimal 12 karakter, dengan huruf besar, huruf kecil, dan angka.</p>
            </div>
            @if ($errors->any())<p class="auth-status" role="alert">{{ $errors->first() }}</p>@endif
            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label for="email">Email akun</label>
                <div class="auth-input-wrap"><span aria-hidden="true">✉</span><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $email) }}" required></div>
                <label for="password">Password baru</label>
                <div class="auth-input-wrap"><span aria-hidden="true">⌑</span><input id="password" name="password" type="password" autocomplete="new-password" required><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></div>
                <label for="password_confirmation">Ulangi password baru</label>
                <div class="auth-input-wrap"><span aria-hidden="true">⌑</span><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></div>
                <button class="auth-submit" type="submit">Simpan Password <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-back-link" style="margin-top:18px"><a href="{{ route('login') }}">← Kembali ke halaman masuk</a></p>
        </section>
        <p class="auth-copyright">© MAHAPATI UNISSULA</p>
    </main>
    <script src="{{ asset('js/auth-password-toggle.js') }}?v=1" defer></script>
</body>
</html>
