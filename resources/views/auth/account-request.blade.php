<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ajukan akun untuk anggota yang telah tercatat sebagai anggota MAHAPATI.">
    <title>Pengajuan Akun Anggota | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <main class="auth-layout">
        <section class="auth-visual auth-visual-request" aria-label="Identitas MAHAPATI">
            <a class="auth-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam<br>Teknologi Industri</small></span>
            </a>
            <div class="auth-visual-copy">
                <p class="auth-eyebrow">Khusus anggota yang sudah tercatat</p>
                <h1>Pengajuan Akun</h1>
                <p class="auth-tagline">Satu langkah untuk terhubung dengan sistem informasi MAHAPATI.</p>
                <div class="request-process-list">
                    <p><span>01</span> Isi data identitas dan email yang akan digunakan untuk masuk.</p>
                    <p><span>02</span> Admin memeriksa pengajuan dan kecocokan data keanggotaan.</p>
                    <p><span>03</span> Admin menentukan role akun. Pemohon tidak memilih role sendiri.</p>
                </div>
            </div>
            <p class="auth-visual-footer">“Bersama Alam, Berkarya untuk Negeri”</p>
        </section>

        <section class="auth-panel auth-panel-request" aria-labelledby="request-title">
            <a class="auth-mobile-brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span>MAHAPATI</span></a>
            <div class="auth-card auth-card-request">
                <a class="auth-back-link auth-top-back" href="{{ route('login') }}">← Kembali ke halaman masuk</a>
                <div class="auth-card-heading">
                    <p class="auth-kicker">Bukan formulir pendaftaran calon anggota</p>
                    <h2 id="request-title">Ajukan Akun Anggota</h2>
                    <p>Gunakan formulir ini jika Anda sudah menjadi anggota MAHAPATI tetapi belum memiliki akun sistem.</p>
                </div>

                <form class="auth-form account-request-form" method="POST" action="{{ route('account-request.store') }}" aria-describedby="request-status">
                    @csrf
                    <label for="member-name">Nama lengkap</label>
                    <div class="auth-input-wrap"><span aria-hidden="true">♙</span><input id="member-name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" placeholder="Sesuai data keanggotaan" required></div>
                    @error('name')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <div class="auth-two-columns">
                        <div><label for="member-nra">NRA</label><div class="auth-input-wrap"><span aria-hidden="true">#</span><input id="member-nra" name="nra" type="text" value="{{ old('nra') }}" placeholder="Nomor registrasi"></div></div>
                        <div><label for="member-nim">NIM</label><div class="auth-input-wrap"><span aria-hidden="true">#</span><input id="member-nim" name="nim" type="text" inputmode="numeric" value="{{ old('nim') }}" placeholder="Nomor mahasiswa"></div></div>
                    </div>
                    @error('nra')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    @error('nim')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <label for="request-email">Email aktif untuk nama login</label>
                    <div class="auth-input-wrap"><span aria-hidden="true">✉</span><input id="request-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="nama@email.com" required></div>
                    @error('email')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <label for="member-whatsapp">Nomor WhatsApp</label>
                    <div class="auth-input-wrap"><span aria-hidden="true">WA</span><input id="member-whatsapp" name="whatsapp" type="tel" autocomplete="tel" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required></div>
                    @error('whatsapp')<p class="auth-status" role="alert">{{ $message }}</p>@enderror
                    <button class="auth-submit" type="submit">Kirim Pengajuan <span aria-hidden="true">→</span></button>
                </form>

                @if (session('status'))<p class="auth-status" id="request-status" role="status">{{ session('status') }}</p>@endif
                <div class="auth-account-note"><span class="auth-note-icon" aria-hidden="true">i</span><p>Jangan memilih role atau membuat password di formulir ini. Admin memverifikasi data dan menentukan role akun. Pengajuan ini hanya untuk anggota yang sudah tercatat, bukan calon anggota baru.</p></div>
                <p class="auth-login-prompt">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
            </div>
            <p class="auth-copyright">© MAHAPATI UNISSULA</p>
        </section>
    </main>
</body>
</html>

