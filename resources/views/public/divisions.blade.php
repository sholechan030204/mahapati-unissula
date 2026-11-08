<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kenali divisi-divisi MAHAPATI, Mahasiswa Pecinta Alam Teknologi Industri Universitas Islam Sultan Agung.">
    <title>Divisi | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/divisions.css') }}">
    <script src="{{ asset('js/home.js') }}?v=2" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="navbar">
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mainNav">
                <span></span><span></span><span></span>
            </button>
            <a class="brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda">
                <img class="brand-logo" src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span class="brand-copy"><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri</small></span>
            </a>
            <nav class="nav-links" id="mainNav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('profile') }}">Tentang Kami</a>
                <a href="{{ route('divisions') }}" aria-current="page">Divisi</a>
                <a href="{{ route('home') }}#kegiatan">Kegiatan</a>
                <a href="{{ route('home') }}#galeri">Galeri</a>
                <a href="{{ route('home') }}#berita">Berita</a>
                <a href="/prestasi">Prestasi</a>
                <a href="{{ route('home') }}#pendaftaran">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="division-hero" aria-labelledby="division-title">
            <div class="page-container division-hero-content">
                <p class="hero-eyebrow">Berkarya bersama</p>
                <h1 id="division-title">Divisi MAHAPATI</h1>
                <p>Kenali bidang-bidang yang menjadi bagian dari kegiatan dan pengembangan organisasi.</p>
                <a class="button" href="#daftar-divisi">Lihat Divisi <span aria-hidden="true">↓</span></a>
            </div>
        </section>

        <section class="section division-intro" aria-labelledby="division-intro-title">
            <div class="page-container division-intro-content">
                <p class="section-kicker">Bergerak dengan tujuan</p>
                <h2 class="section-title" id="division-intro-title">Setiap divisi mendukung perjalanan MAHAPATI</h2>
                <p>Divisi menjadi bagian dari pembagian bidang kerja organisasi. Berikut ringkasan divisi yang ditampilkan pada desain acuan MAHAPATI.</p>
            </div>
        </section>

        <section class="division-list-section" id="daftar-divisi" aria-labelledby="division-list-title">
            <div class="page-container">
                <div class="section-heading">
                    <div>
                        <p class="section-kicker">Bidang organisasi</p>
                        <h2 class="section-title" id="division-list-title">Kenali Divisi Kami</h2>
                    </div>
                </div>
                <div class="division-grid">
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'pendidikan-pelatihan']) }}"><article class="division-card">
                        <div class="division-card-image">
                            <img src="{{ asset('images/tentang-mahapat-1.jpg') }}" alt="Anggota MAHAPATI mengikuti kegiatan bersama" loading="lazy">
                            <span class="division-card-number">01</span>
                        </div>
                        <div class="division-card-copy">
                            <p class="division-card-label">Pengembangan Anggota</p>
                            <h3>Pendidikan dan Pelatihan</h3>
                            <p>Mendukung pengembangan kapasitas anggota melalui kegiatan pendidikan, pelatihan, dan pembelajaran organisasi.</p>
                        </div>
                    </article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'pengembangan-teknologi']) }}"><article class="division-card">
                        <div class="division-card-image">
                            <img src="{{ asset('images/tentang-mahapat-2.jpg') }}" alt="Anggota MAHAPATI berkegiatan di area perkemahan" loading="lazy">
                            <span class="division-card-number">02</span>
                        </div>
                        <div class="division-card-copy">
                            <p class="division-card-label">Inovasi dan Teknologi</p>
                            <h3>Pengembangan Teknologi</h3>
                            <p>Mengembangkan pemanfaatan teknologi untuk mendukung kegiatan dan pengelolaan organisasi.</p>
                        </div>
                    </article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'operasional-konservasi']) }}"><article class="division-card">
                        <div class="division-card-image">
                            <img src="{{ asset('images/tentang-mahapat-3.jpg') }}" alt="Anggota MAHAPATI beraktivitas di lingkungan alam" loading="lazy">
                            <span class="division-card-number">03</span>
                        </div>
                        <div class="division-card-copy">
                            <p class="division-card-label">Kegiatan Lapangan</p>
                            <h3>Operasional dan Konservasi</h3>
                            <p>Mendukung pelaksanaan kegiatan lapangan dan kepedulian terhadap pelestarian lingkungan.</p>
                        </div>
                    </article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'logistik']) }}"><article class="division-card">
                        <div class="division-card-image">
                            <img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI dalam perjalanan di alam terbuka" loading="lazy">
                            <span class="division-card-number">04</span>
                        </div>
                        <div class="division-card-copy">
                            <p class="division-card-label">Dukungan Kegiatan</p>
                            <h3>Logistik</h3>
                            <p>Mendukung kebutuhan perlengkapan dan logistik agar kegiatan organisasi dapat berjalan terkoordinasi.</p>
                        </div>
                    </article></a>
                </div>
                <p class="division-disclaimer">Nama dan ringkasan divisi mengikuti desain acuan. Uraian tugas resmi dapat disesuaikan dengan informasi organisasi.</p>
            </div>
        </section>

        <section class="join-section division-join" aria-labelledby="division-join-title">
            <div class="page-container join-content">
                <div>
                    <p class="section-kicker">Bersama MAHAPATI</p>
                    <h2 id="division-join-title">Temukan kegiatan yang membangun kebersamaan</h2>
                    <p>Lihat kegiatan MAHAPATI dan informasi pendaftaran calon anggota.</p>
                </div>
                <a class="button" href="{{ route('home') }}#kegiatan">Jelajahi Kegiatan <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="kontak">
        <div class="page-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a class="footer-brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda">
                        <img src="{{ asset('images/logo-mahapati-new.png') }}" alt="">
                        <span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</small></span>
                    </a>
                    <p class="footer-slogan">“Bersama Alam, Berkarya untuk Negeri”</p>
                </div>
                <div class="footer-column">
                    <h3>Menu</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Beranda</a></li><li><a href="{{ route('profile') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('divisions') }}">Divisi</a></li><li><a href="{{ route('home') }}#kegiatan">Kegiatan</a></li>
                        <li><a href="{{ route('home') }}#galeri">Galeri</a></li><li><a href="{{ route('home') }}#berita">Berita</a></li>
                        <li><a href="/prestasi">Prestasi</a></li><li><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Kontak Resmi</h3>
                    <ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul>
                    <p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p>
                </div>
                <div class="footer-column">
                    <h3>Ikuti Kami</h3>
                    <div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div>
                    
                </div>
            </div>
            <div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div>
        </div>
    </footer>
</body>
</html>

