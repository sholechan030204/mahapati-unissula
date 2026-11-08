<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lihat foto kegiatan MAHAPATI dan temukan dokumentasi video melalui YouTube.">
    <title>Galeri | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <script src="{{ asset('js/home.js') }}?v=2" defer></script>
    <script src="{{ asset('js/gallery.js') }}" defer></script>
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
                <a href="{{ route('gallery') }}" aria-current="page">Galeri</a><a href="{{ route('home') }}#berita">Berita</a>
                <a href="/prestasi">Prestasi</a><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="gallery-hero" aria-labelledby="gallery-title">
            <div class="page-container gallery-hero-content">
                <p class="hero-eyebrow">Momen bersama alam</p>
                <h1 id="gallery-title">Galeri MAHAPATI</h1>
                <p>Dokumentasi perjalanan, kegiatan, dan kebersamaan anggota MAHAPATI.</p>
                <a class="button" href="#foto-kegiatan">Jelajahi Foto <span aria-hidden="true">↓</span></a>
            </div>
        </section>

        <section class="section gallery-intro" aria-labelledby="gallery-intro-title">
            <div class="page-container gallery-intro-copy">
                <p class="section-kicker">Cerita dalam gambar</p>
                <h2 class="section-title" id="gallery-intro-title">Momen yang kami lalui bersama</h2>
                <p>Lihat foto perjalanan dan kegiatan MAHAPATI. Pilih foto untuk membukanya dalam ukuran lebih besar.</p>
            </div>
        </section>

        <section class="section gallery-list-section" id="foto-kegiatan" aria-labelledby="photo-list-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Dokumentasi foto</p><h2 class="section-title" id="photo-list-title">Foto Kegiatan</h2></div>
                </div>
                @php($photoItems = $galleryItems->getCollection()->where('media_type', 'photo'))
                @if ($photoItems->isEmpty())
                    <div class="empty-state"><span class="empty-state-icon" aria-hidden="true">▧</span><strong>Belum ada foto yang diterbitkan</strong><p>Foto galeri akan muncul setelah Admin menerbitkannya.</p></div>
                @else
                    <div class="gallery-photo-grid gallery-dynamic-photo-grid">
                        @foreach ($photoItems as $item)
                            <button class="gallery-photo-card" type="button" data-full-src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" data-photo-alt="{{ $item->title }}" data-photo-caption="{{ $item->caption ?: $item->title }}">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" alt="{{ $item->title }}" loading="lazy">
                                <span class="gallery-photo-caption"><span>{{ $item->caption ?: $item->title }}</span><span class="gallery-photo-open">Lihat foto <span aria-hidden="true">⤢</span></span></span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="section gallery-video-section" aria-labelledby="video-title">
            <div class="page-container gallery-video-card">
                <div class="gallery-video-copy">
                    <p class="section-kicker">Dokumentasi video</p>
                    <h2 class="section-title" id="video-title">Video Galeri di YouTube</h2>
                    <p>Video dokumentasi ditautkan ke YouTube agar halaman tetap ringan dan ruang penyimpanan website tetap hemat.</p>
                </div>
                <a class="button" href="{{ config('public_contact.social_links.2.url') }}" target="_blank" rel="noopener noreferrer">Kunjungi YouTube MAHAPATI <span aria-hidden="true">↗</span></a>
            </div>
            @php($youtubeItems = $galleryItems->getCollection()->where('media_type', 'youtube'))
            <div class="page-container gallery-youtube-list">
                @if ($youtubeItems->isEmpty())
                    <div class="empty-state"><span class="empty-state-icon" aria-hidden="true">▶</span><strong>Belum ada tautan video galeri</strong><p>Video kegiatan yang sudah disetujui akan ditampilkan di sini.</p></div>
                @else
                    <div class="gallery-youtube-grid">
                        @foreach ($youtubeItems as $item)
                            <a class="gallery-youtube-card" href="{{ $item->youtube_url }}" target="_blank" rel="noopener noreferrer">
                                <span class="gallery-youtube-cover"><img src="{{ $item->youtubeThumbnailUrl() ?? asset('images/hero-mahapat.jpg') }}" alt="Pratinjau {{ $item->title }}" loading="lazy"><span class="gallery-youtube-play" aria-hidden="true">▶</span></span>
                                <span class="gallery-youtube-copy"><small>{{ $item->activity_date?->translatedFormat('d M Y') ?? 'Video MAHAPATI' }}</small><strong>{{ $item->title }}</strong><span>{{ $item->caption ?: 'Tonton dokumentasi ini di YouTube.' }}</span><b>Tonton di YouTube ↗</b></span>
                            </a>
                        @endforeach
                    </div>
                @endif
                <div class="gallery-pagination">{{ $galleryItems->links() }}</div>
            </div>
        </section>

        <section class="join-section gallery-join" aria-labelledby="gallery-join-title">
            <div class="page-container join-content">
                <div><p class="section-kicker">Bersama MAHAPATI</p><h2 id="gallery-join-title">Jelajahi kegiatan kami berikutnya</h2><p>Kenali kegiatan MAHAPATI dan ikuti informasi pendaftaran calon anggota.</p></div>
                <a class="button" href="{{ route('activities') }}">Lihat Kegiatan <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </main>

    <dialog class="gallery-lightbox" id="galleryLightbox" aria-label="Foto galeri MAHAPATI">
        <button class="gallery-lightbox-close" id="galleryLightboxClose" type="button" aria-label="Tutup foto">×</button>
        <figure>
            <img id="galleryLightboxImage" src="" alt="">
            <figcaption id="galleryLightboxCaption"></figcaption>
        </figure>
    </dialog>

    <footer class="site-footer" id="kontak">
        <div class="page-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a class="footer-brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</small></span></a>
                    <p class="footer-slogan">“Bersama Alam, Berkarya untuk Negeri”</p>
                </div>
                <div class="footer-column"><h3>Menu</h3><ul class="footer-links">
                    <li><a href="{{ route('home') }}">Beranda</a></li><li><a href="{{ route('profile') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('divisions') }}">Divisi</a></li><li><a href="{{ route('activities') }}">Kegiatan</a></li>
                    <li><a href="{{ route('gallery') }}">Galeri</a></li><li><a href="{{ route('home') }}#berita">Berita</a></li>
                    <li><a href="/prestasi">Prestasi</a></li><li><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a></li>
                </ul></div>
                <div class="footer-column"><h3>Kontak Resmi</h3><ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul><p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p></div>
                <div class="footer-column"><h3>Ikuti Kami</h3><div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div></div>
            </div>
            <div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div>
        </div>
    </footer>
</body>
</html>

