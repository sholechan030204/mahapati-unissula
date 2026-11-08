<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <main class="auth-panel">
        <a class="auth-mobile-brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span>MAHAPATI</span></a>
        <section class="auth-card" aria-labelledby="forgot-title">
            <div class="auth-card-heading">
                <p class="auth-kicker">Pemulihan akun</p>
                <h2 id="forgot-title">Lupa Password?</h2>
                <p>Masukkan email akun. Jika layanan email sudah aktif, tautan untuk membuat password baru akan dikirim ke email tersebut.</p>
            </div>
            @if (session('status'))<p class="auth-status" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="auth-status" role="alert">{{ $errors->first() }}</p>@endif
            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email">Email akun</label>
                <div class="auth-input-wrap"><span aria-hidden="true">✉</span><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="nama@email.com" required></div>
                <button class="auth-submit" type="submit">Kirim Tautan Pemulihan <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-status">Layanan email MAHAPATI belum dikonfigurasi. Untuk saat ini, hubungi Admin agar dapat membantu akses akun.</p>
            <p class="auth-back-link" style="margin-top:18px"><a href="{{ route('login') }}">← Kembali ke halaman masuk</a></p>
        </section>
        <p class="auth-copyright">© MAHAPATI UNISSULA</p>
    </main>
</body>
</html>
