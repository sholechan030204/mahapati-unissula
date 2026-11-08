<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $division['description'] }}">
    <title>{{ $division['name'] }} | Divisi MAHAPATI</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/division-detail.css') }}">
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
        <nav class="division-breadcrumb page-container" aria-label="Jejak halaman">
            <a href="{{ route('home') }}">Beranda</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('divisions') }}">Divisi</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $division['name'] }}</span>
        </nav>

        <section class="division-detail-hero" aria-labelledby="division-detail-title">
            <div class="page-container division-detail-hero-grid">
                <div class="division-detail-heading">
                    <p class="section-kicker">{{ $division['category'] }}</p>
                    <h1 id="division-detail-title">{{ $division['name'] }}</h1>
                    <a class="button" href="#penjelasan-divisi">Baca Penjelasan <span aria-hidden="true">↓</span></a>
                </div>
                <figure class="division-detail-image">
                    <img src="{{ asset($division['image']) }}" alt="{{ $division['image_alt'] }}">
                </figure>
            </div>
        </section>

        <section class="section division-narrative-section" id="penjelasan-divisi" aria-labelledby="what-is-title">
            <div class="page-container division-narrative">
                <p class="section-kicker">Mengenal divisi</p>
                <h2 class="section-title" id="what-is-title">Apa itu Divisi {{ $division['name'] }}?</h2>
                <p>{{ $division['description'] }}</p>
                <p>{{ $division['work'] }}</p>
            </div>
        </section>

        <section class="division-purpose-section" aria-labelledby="function-title">
            <div class="page-container division-purpose-copy">
                <p class="section-kicker">Peran dalam organisasi</p>
                <h2 class="section-title" id="function-title">Fungsi Divisi {{ $division['name'] }}</h2>
                <p>{{ $division['function'] }}</p>
                <h3>Manfaat bagi anggota</h3>
                <p>{{ $division['benefit'] }}</p>
            </div>
        </section>

        <section class="division-detail-cta">
            <div class="page-container division-detail-cta-content">
                <div>
                    <p class="section-kicker">Kembali mengenal MAHAPATI</p>
                    <h2>Jelajahi divisi lainnya</h2>
                    <p>Setiap divisi mendukung kegiatan dan perjalanan organisasi.</p>
                </div>
                <a class="button" href="{{ route('divisions') }}">Lihat Semua Divisi <span aria-hidden="true">→</span></a>
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


