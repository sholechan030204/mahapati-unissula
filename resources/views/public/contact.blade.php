<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hubungi MAHAPATI UNISSULA melalui kontak resmi dan formulir pesan.">
    <title>Kontak | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <script src="{{ asset('js/home.js') }}?v=2" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="navbar">
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mainNav"><span></span><span></span><span></span></button>
            <a class="brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda">
                <img class="brand-logo" src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span class="brand-copy"><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri</small></span>
            </a>
            <nav class="nav-links" id="mainNav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}">Beranda</a><a href="{{ route('profile') }}">Tentang Kami</a>
                <a href="{{ route('divisions') }}">Divisi</a><a href="{{ route('activities') }}">Kegiatan</a>
                <a href="{{ route('gallery') }}">Galeri</a><a href="{{ route('news.index') }}">Berita</a>
                <a href="{{ route('achievements.index') }}">Prestasi</a><a href="{{ route('registration') }}">Pendaftaran</a>
                <a href="{{ route('contact') }}" aria-current="page">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="contact-hero" aria-labelledby="contact-title">
            <div class="page-container contact-hero-content">
                <p class="hero-eyebrow">Kami siap terhubung</p>
                <h1 id="contact-title">Hubungi MAHAPATI</h1>
                <p>Ingin bertanya tentang organisasi, kegiatan, atau pendaftaran? Hubungi kami melalui kanal resmi MAHAPATI.</p>
                <a class="button" href="#kanal-resmi">Lihat Kontak Resmi <span aria-hidden="true">↓</span></a>
            </div>
        </section>

        <section class="section contact-channels-section" id="kanal-resmi" aria-labelledby="channels-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Tetap terhubung</p><h2 class="section-title" id="channels-title">Kontak Resmi</h2></div>
                    <p class="contact-section-intro">Gunakan kanal resmi berikut untuk menghubungi Pengurus MAHAPATI.</p>
                </div>
                <div class="contact-channel-grid">
                    <article class="contact-channel-card">
                        <span class="contact-channel-icon whatsapp-icon" aria-hidden="true">WA</span>
                        <div><h3>WhatsApp</h3><p>Untuk pertanyaan dan komunikasi langsung dengan Pengurus.</p>@if (filled(config('public_contact.whatsapp')))<a class="contact-channel-link" href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}" target="_blank" rel="noopener noreferrer">{{ config('public_contact.whatsapp') }}</a>@else<span class="contact-unavailable">Nomor WhatsApp resmi belum tersedia.</span>@endif</div>
                    </article>
                    <article class="contact-channel-card">
                        <span class="contact-channel-icon email-icon" aria-hidden="true">@</span>
                        <div><h3>Email</h3><p>Untuk pertanyaan atau pesan resmi kepada organisasi.</p><a class="contact-channel-link" href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section contact-message-section" aria-labelledby="message-title">
            <div class="page-container contact-message-layout">
                <div class="contact-message-copy">
                    <p class="section-kicker">Kirim pesan</p>
                    <h2 class="section-title" id="message-title">Ada yang ingin ditanyakan?</h2>
                    <p>Formulir ini disediakan untuk mengirim pertanyaan umum kepada MAHAPATI. Untuk sementara, pengiriman pesan belum aktif karena alamat layanan pengiriman email belum dikonfigurasi.</p>
                    <div class="contact-location-card">
                        <span class="contact-location-icon" aria-hidden="true">⌖</span>
                        <div><h3>Sekretariat MAHAPATI</h3><p>Fakultas Teknologi Industri<br>Universitas Islam Sultan Agung, Semarang</p></div>
                    </div>
                </div>
                <form class="contact-form" aria-describedby="contact-form-status">
                    <div class="contact-form-grid">
                        <label>Nama lengkap<input type="text" name="name" autocomplete="name" placeholder="Masukkan nama Anda" disabled></label>
                        <label>Email<input type="email" name="email" autocomplete="email" placeholder="nama@email.com" disabled></label>
                        <label class="contact-field-full">Subjek<input type="text" name="subject" placeholder="Topik pesan Anda" disabled></label>
                        <label class="contact-field-full">Pesan<textarea name="message" rows="5" placeholder="Tuliskan pesan atau pertanyaan Anda" disabled></textarea></label>
                    </div>
                    <button class="button contact-submit" type="button" disabled>Kirim Pesan <span aria-hidden="true">→</span></button>
                    <p class="contact-form-status" id="contact-form-status">Email tujuan pesan: {{ config('public_contact.email') }}. Formulir akan diaktifkan setelah layanan pengiriman email dikonfigurasi.</p>
                </form>
            </div>
        </section>

        <section class="section contact-social-section" aria-labelledby="social-title">
            <div class="page-container">
                <div class="contact-social-card">
                    <div><p class="section-kicker">Ikuti kegiatan kami</p><h2 class="section-title" id="social-title">Media Sosial MAHAPATI</h2><p>Ikuti akun resmi MAHAPATI melalui tautan berikut.</p></div>
                    <ul class="contact-social-list" aria-label="Media sosial resmi MAHAPATI">@foreach (config('public_contact.social_links') as $social)<li><a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"><span>{{ $social['name'] }}</span><strong>Kunjungi akun →</strong></a></li>@endforeach</ul>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="page-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a class="footer-brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</small></span></a>
                    <p class="footer-slogan">“Bersama Alam, Berkarya untuk Negeri”</p>
                </div>
                <div class="footer-column"><h3>Menu</h3><ul class="footer-links">
                    <li><a href="{{ route('home') }}">Beranda</a></li><li><a href="{{ route('profile') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('divisions') }}">Divisi</a></li><li><a href="{{ route('activities') }}">Kegiatan</a></li>
                    <li><a href="{{ route('gallery') }}">Galeri</a></li><li><a href="{{ route('news.index') }}">Berita</a></li>
                    <li><a href="{{ route('achievements.index') }}">Prestasi</a></li><li><a href="{{ route('registration') }}">Pendaftaran</a></li>
                    <li><a href="{{ route('contact') }}">Kontak</a></li>
                </ul></div>
                <div class="footer-column"><h3>Kontak Resmi</h3><ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul><p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p></div>
                <div class="footer-column"><h3>Ikuti Kami</h3><div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div></div>
            </div>
            <div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div>
        </div>
    </footer>
</body>
</html>

