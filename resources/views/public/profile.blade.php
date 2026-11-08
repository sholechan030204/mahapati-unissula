<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profil MAHAPATI, Mahasiswa Pecinta Alam Teknologi Industri Universitas Islam Sultan Agung.">
    <title>Profil MAHAPATI | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
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
                <a href="{{ route('home') }}#tentang" aria-current="page">Tentang Kami</a>
                <a href="{{ route('home') }}#divisi">Divisi</a>
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
        <section class="profile-hero" aria-labelledby="profile-title">
            <div class="page-container profile-hero-content">
                <p class="hero-eyebrow">Tentang Kami</p>
                <h1 id="profile-title">Profil MAHAPATI</h1>
                <p>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</p>
                <a class="button" href="#identitas">Kenali MAHAPATI <span aria-hidden="true">↓</span></a>
            </div>
        </section>

        <section class="section profile-section" id="identitas" aria-labelledby="identity-title">
            <div class="page-container profile-story">
                <div class="profile-copy">
                    <p class="section-kicker">Identitas organisasi</p>
                    <h2 class="section-title" id="identity-title">Menjaga Alam, Membangun Karakter, Berkarya untuk Negeri</h2>
                    <p>MAHAPATI adalah organisasi mahasiswa pecinta alam di lingkungan Fakultas Teknologi Industri Universitas Islam Sultan Agung.</p>
                    <p>Melalui kegiatan di alam, pendidikan, dan kebersamaan, MAHAPATI menjadi ruang bagi mahasiswa untuk berkegiatan dan mengembangkan diri.</p>
                    <blockquote>“Bersama Alam, Berkarya untuk Negeri”</blockquote>
                </div>
                <figure class="profile-feature-photo">
                    <img src="{{ asset('images/tentang-mahapat-1.jpg') }}" alt="Anggota MAHAPATI berkumpul bersama di depan spanduk kegiatan" loading="lazy">
                    <figcaption>Kebersamaan anggota MAHAPATI dalam kegiatan organisasi</figcaption>
                </figure>
            </div>
        </section>

        <section class="profile-values-section" aria-labelledby="spirit-title">
            <div class="page-container">
                <div class="profile-section-heading">
                    <p class="section-kicker">Semangat berkegiatan</p>
                    <h2 class="section-title" id="spirit-title">Bersama Alam, Belajar dan Berkarya</h2>
                    <p>Bagian ini menggambarkan tema umum kegiatan MAHAPATI, bukan rumusan visi dan misi resmi.</p>
                </div>
                <div class="profile-values-grid">
                    <article class="profile-value-card">
                        <span aria-hidden="true">⌁</span>
                        <h3>Alam</h3>
                        <p>Mengenal dan berkegiatan di alam terbuka dengan kepedulian terhadap lingkungan.</p>
                    </article>
                    <article class="profile-value-card">
                        <span aria-hidden="true">✦</span>
                        <h3>Pendidikan</h3>
                        <p>Belajar melalui pengalaman, kegiatan organisasi, dan kebersamaan.</p>
                    </article>
                    <article class="profile-value-card">
                        <span aria-hidden="true">♧</span>
                        <h3>Pengabdian</h3>
                        <p>Mengembangkan kepedulian dan memberi manfaat bagi lingkungan serta masyarakat.</p>
                    </article>
                    <article class="profile-value-card">
                        <span aria-hidden="true">◎</span>
                        <h3>Solidaritas</h3>
                        <p>Menumbuhkan kebersamaan dan saling mendukung dalam setiap kegiatan.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section profile-section" aria-labelledby="moments-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Cerita dalam gambar</p><h2 class="section-title" id="moments-title">Kebersamaan MAHAPATI</h2></div>
                    <a class="text-link" href="{{ route('home') }}#galeri">Lihat Galeri <span aria-hidden="true">→</span></a>
                </div>
                <div class="profile-photo-grid">
                    <figure><img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI berfoto bersama di alam terbuka" loading="lazy"><figcaption>Kebersamaan di perjalanan</figcaption></figure>
                    <figure><img src="{{ asset('images/tentang-mahapat-2.jpg') }}" alt="Anggota MAHAPATI beristirahat di area perkemahan" loading="lazy"><figcaption>Belajar dan beristirahat bersama</figcaption></figure>
                    <figure><img src="{{ asset('images/tentang-mahapat-3.jpg') }}" alt="Anggota MAHAPATI berkegiatan di kawasan alam" loading="lazy"><figcaption>Kegiatan di alam terbuka</figcaption></figure>
                </div>
            </div>
        </section>

        <section class="profile-history-section" aria-labelledby="history-title">
            <div class="page-container profile-history-card">
                <div>
                    <p class="section-kicker">Riwayat organisasi</p>
                    <h2 class="section-title" id="history-title">Sejarah MAHAPATI</h2>
                    <p>Informasi sejarah resmi MAHAPATI belum dimasukkan. Bagian ini akan dilengkapi setelah naskah sejarah organisasi tersedia.</p>
                </div>
                <span class="history-mark" aria-hidden="true">M</span>
            </div>
        </section>

        <section class="join-section profile-join" aria-labelledby="profile-join-title">
            <div class="page-container join-content">
                <div>
                    <p class="section-kicker">Bersama MAHAPATI</p>
                    <h2 id="profile-join-title">Jelajahi kegiatan dan kenali perjalanan kami</h2>
                    <p>Lihat informasi kegiatan dan pendaftaran calon anggota melalui halaman website publik.</p>
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
                        <li><a href="{{ route('home') }}#divisi">Divisi</a></li><li><a href="{{ route('home') }}#kegiatan">Kegiatan</a></li>
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


