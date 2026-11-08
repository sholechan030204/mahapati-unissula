<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website resmi MAHAPATI, Mahasiswa Pecinta Alam Teknologi Industri Universitas Islam Sultan Agung.">
    <title>Beranda | MAHAPATI UNISSULA</title>
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
            <a class="brand" href="/" aria-label="MAHAPATI, kembali ke Beranda">
                <img class="brand-logo" src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI">
                <span class="brand-copy">
                    <strong>MAHAPATI</strong>
                    <small>Mahasiswa Pecinta Alam Teknologi Industri</small>
                </span>
            </a>
            <nav class="nav-links" id="mainNav" aria-label="Navigasi utama">
                <a href="/">Beranda</a>
                <a href="#tentang">Tentang Kami</a>
                <a href="#divisi">Divisi</a>
                <a href="#kegiatan">Kegiatan</a>
                <a href="#galeri">Galeri</a>
                <a href="#berita">Berita</a>
                <a href="/prestasi">Prestasi</a>
                <a href="#pendaftaran">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="hero" id="beranda" aria-labelledby="hero-title">
            <div class="hero-content">
                <p class="hero-eyebrow">Mahasiswa Pecinta Alam Teknologi Industri</p>
                <h1 id="hero-title">MAHAPATI</h1>
                <h2>Universitas Islam Sultan Agung</h2>
                <p class="hero-slogan"><em>“Bersama Alam, Berkarya untuk Negeri”</em></p>
                <div class="hero-actions">
                    <a class="button" href="#kegiatan">Jelajahi Kegiatan <span aria-hidden="true">→</span></a>
                    <a class="button button-outline" href="#pendaftaran">Bergabung Bersama Kami</a>
                </div>
            </div>
            <a class="scroll-hint" href="#tentang">Gulir untuk menjelajah <span aria-hidden="true">↓</span></a>
        </section>

        <section class="section" id="tentang" aria-labelledby="about-title">
            <div class="page-container about-section">
                <div class="about-copy">
                    <p class="section-kicker">Tentang Kami</p>
                    <h2 class="section-title" id="about-title">Menjaga Alam,<br>Membangun Karakter,<br>Berkarya untuk Negeri</h2>
                    <p>
                        MAHAPATI adalah organisasi mahasiswa pecinta alam di lingkungan
                        Fakultas Teknologi Industri Universitas Islam Sultan Agung. Kami
                        berkegiatan di alam sekaligus belajar, mengembangkan diri, dan
                        membangun kepedulian terhadap lingkungan serta masyarakat.
                    </p>
                    <a class="button" href="/profil">Selengkapnya <span aria-hidden="true">→</span></a>
                </div>
                <div class="about-gallery" aria-label="Foto kegiatan MAHAPATI">
                    <figure class="about-photo about-photo-main">
                        <img src="{{ asset('images/tentang-mahapat-1.jpg') }}" alt="Anggota MAHAPATI berkumpul di depan spanduk kegiatan" loading="lazy">
                        <figcaption class="photo-caption">Alam · Pendidikan · Pengabdian · Solidaritas</figcaption>
                    </figure>
                    <figure class="about-photo">
                        <img src="{{ asset('images/tentang-mahapat-2.jpg') }}" alt="Anggota MAHAPATI berkemah di kawasan pegunungan" loading="lazy">
                    </figure>
                    <figure class="about-photo">
                        <img src="{{ asset('images/tentang-mahapat-3.jpg') }}" alt="Anggota MAHAPATI berkegiatan bersama di alam terbuka" loading="lazy">
                    </figure>
                </div>
            </div>
        </section>

        <section class="stats-section" aria-labelledby="stats-title">
            <div class="page-container stats-layout">
                <div class="stats-intro">
                    <p class="section-kicker">MAHAPATI dalam angka</p>
                    <h2 id="stats-title">Angka yang Menggambarkan Perjalanan Kami</h2>
                    <p>Ringkasan ini akan diisi dari data organisasi setelah modul terkait tersedia.</p>
                </div>
                <div class="stats-grid" aria-label="Ringkasan data organisasi">
                    <div class="stat-item"><span class="stat-symbol" aria-hidden="true">♟</span><span class="stat-value">—</span><span class="stat-label">Anggota Aktif</span></div>
                    <div class="stat-item"><span class="stat-symbol" aria-hidden="true">▲</span><span class="stat-value">—</span><span class="stat-label">Kegiatan Terlaksana</span></div>
                    <div class="stat-item"><span class="stat-symbol" aria-hidden="true">★</span><span class="stat-value">—</span><span class="stat-label">Prestasi</span></div>
                    <div class="stat-item"><span class="stat-symbol" aria-hidden="true">⌂</span><span class="stat-value">—</span><span class="stat-label">Divisi</span></div>
                </div>
            </div>
        </section>

        <section class="section content-section" id="kegiatan" aria-labelledby="activities-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Jelajahi aktivitas kami</p><h2 class="section-title" id="activities-title">Kegiatan Terbaru</h2></div>
                    <a class="text-link" href="/kegiatan">Lihat Semua <span aria-hidden="true">→</span></a>
                </div>
                @if ($latestActivities->isEmpty())
                    <div class="empty-state"><span class="empty-state-icon" aria-hidden="true">⌁</span><strong>Belum ada kegiatan yang dipublikasikan</strong><p>Kegiatan terbaru akan muncul di sini setelah dikelola dan diterbitkan.</p></div>
                @else
                    <div class="public-activity-grid">
                        @foreach ($latestActivities as $activity)
                            <a class="public-activity-card" href="{{ route('activities.show', $activity) }}">
                                @if ($activity->cover_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activity->cover_image) }}" alt="Foto {{ $activity->title }}" loading="lazy">@else<img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI di alam terbuka" loading="lazy">@endif
                                <div class="public-activity-card-copy"><span>{{ $activity->planned_at->translatedFormat('d M Y') }} · {{ $activity->location }}</span><h3>{{ $activity->title }}</h3><p>{{ \Illuminate\Support\Str::limit($activity->description, 110) }}</p></div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="section content-section alternate" id="divisi" aria-labelledby="divisions-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Berkarya bersama</p><h2 class="section-title" id="divisions-title">Divisi MAHAPATI</h2></div>
                    <a class="text-link" href="/divisi">Lihat Semua Divisi <span aria-hidden="true">→</span></a>
                </div>
                <div class="division-grid division-preview-grid">
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'pendidikan-pelatihan']) }}"><article class="division-card"><div class="division-card-image"><img src="{{ asset('images/tentang-mahapat-1.jpg') }}" alt="Anggota MAHAPATI berkegiatan bersama" loading="lazy"></div><div class="division-card-copy"><p class="division-card-label">Pengembangan Anggota</p><h3>Pendidikan dan Pelatihan</h3><p>Mendukung pengembangan kapasitas anggota melalui pendidikan, pelatihan, dan pembinaan.</p></div></article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'pengembangan-teknologi']) }}"><article class="division-card"><div class="division-card-image"><img src="{{ asset('images/tentang-mahapat-2.jpg') }}" alt="Anggota MAHAPATI berkegiatan di area perkemahan" loading="lazy"></div><div class="division-card-copy"><p class="division-card-label">Inovasi dan Teknologi</p><h3>Pengembangan Teknologi</h3><p>Mengembangkan pemanfaatan teknologi untuk mendukung kegiatan dan pengelolaan organisasi.</p></div></article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'operasional-konservasi']) }}"><article class="division-card"><div class="division-card-image"><img src="{{ asset('images/tentang-mahapat-3.jpg') }}" alt="Anggota MAHAPATI berkegiatan di lingkungan alam" loading="lazy"></div><div class="division-card-copy"><p class="division-card-label">Kegiatan Lapangan</p><h3>Operasional dan Konservasi</h3><p>Mendukung kegiatan lapangan dan kepedulian terhadap pelestarian lingkungan.</p></div></article></a>
                    <a class="division-card-link" href="{{ route('divisions.show', ['slug' => 'logistik']) }}"><article class="division-card"><div class="division-card-image"><img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI dalam perjalanan di alam terbuka" loading="lazy"></div><div class="division-card-copy"><p class="division-card-label">Dukungan Kegiatan</p><h3>Logistik</h3><p>Mendukung kebutuhan perlengkapan agar kegiatan berjalan efektif dan terkoordinasi.</p></div></article></a>
                </div>
            </div>
        </section>

        <section class="section content-section" id="galeri" aria-labelledby="gallery-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Momen bersama alam</p><h2 class="section-title" id="gallery-title">Galeri MAHAPATI</h2></div>
                    <a class="text-link" href="/galeri">Lihat Semua Galeri <span aria-hidden="true">→</span></a>
                </div>
                @if ($galleryPreview->isEmpty())
                    <div class="empty-state"><span class="empty-state-icon" aria-hidden="true">▧</span><strong>Dokumentasi galeri akan hadir di sini</strong><p>Foto dan video akan ditampilkan setelah diterbitkan melalui sistem.</p></div>
                @else
                    <div class="gallery-grid">
                        @foreach ($galleryPreview as $item)
                            <a class="gallery-item" href="{{ route('gallery') }}" aria-label="Lihat galeri: {{ $item->title }}">
                                @if ($item->media_type === 'photo')<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" alt="{{ $item->title }}" loading="lazy">@else<img src="{{ $item->youtubeThumbnailUrl() ?? asset('images/hero-mahapat.jpg') }}" alt="Pratinjau video {{ $item->title }}" loading="lazy"><span class="home-gallery-play" aria-hidden="true">▶</span>@endif
                                <span class="home-gallery-caption">{{ $item->title }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="section content-section alternate" id="berita" aria-labelledby="news-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Kabar organisasi</p><h2 class="section-title" id="news-title">Berita Terbaru</h2></div>
                    <a class="text-link" href="/berita">Lihat Semua Berita <span aria-hidden="true">→</span></a>
                </div>
                @if ($latestArticles->isNotEmpty())
                    <div class="home-news-grid">
                        @foreach ($latestArticles as $article)
                            <a class="home-news-card" href="{{ route('news.show', $article->slug) }}">
                                @if ($article->cover_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="Sampul {{ $article->title }}" loading="lazy">
                                @else
                                    <span class="home-news-placeholder" aria-hidden="true">MAHAPATI</span>
                                @endif
                                <span class="home-news-copy"><small>{{ $article->category }} · {{ $article->published_at?->locale('id')->translatedFormat('d F Y') }}</small><strong>{{ $article->title }}</strong><span>{{ \Illuminate\Support\Str::limit($article->excerpt, 120) }}</span><b>Baca selengkapnya →</b></span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span class="empty-state-icon" aria-hidden="true">▤</span>
                        <strong>Belum ada berita yang dipublikasikan</strong>
                        <p>Berita dan publikasi akan muncul di sini setelah disetujui dan diterbitkan.</p>
                    </div>
                @endif
            </div>
        </section>

        @if (!empty($registration['pamphlet_path']))
            <section class="recruitment-banner-section" aria-labelledby="recruitment-banner-title">
                <div class="page-container recruitment-banner">
                    <div class="recruitment-banner-copy"><p class="section-kicker">{{ ($registration['is_open'] ?? false) ? 'Pendaftaran sedang dibuka' : 'Informasi Open Recruitment' }}</p><h2 id="recruitment-banner-title">Open Recruitment MAHAPATI 2026</h2><p>Pendaftaran {{ !empty($registration['opens_at']) ? \Illuminate\Support\Carbon::parse($registration['opens_at'])->locale('id')->translatedFormat('d F') : '—' }}–{{ !empty($registration['closes_at']) ? \Illuminate\Support\Carbon::parse($registration['closes_at'])->locale('id')->translatedFormat('d F Y') : '—' }} · Wawancara {{ !empty($registration['interview_starts_at']) ? \Illuminate\Support\Carbon::parse($registration['interview_starts_at'])->locale('id')->translatedFormat('d') : '—' }}–{{ !empty($registration['interview_ends_at']) ? \Illuminate\Support\Carbon::parse($registration['interview_ends_at'])->locale('id')->translatedFormat('d F Y') : '—' }} · Pengumuman {{ !empty($registration['announcement_at']) ? \Illuminate\Support\Carbon::parse($registration['announcement_at'])->locale('id')->translatedFormat('d F Y') : '—' }}</p><a class="button" href="{{ route('registration') }}">Lihat Info &amp; Daftar <span aria-hidden="true">→</span></a></div>
                    <a class="recruitment-banner-image" href="{{ route('registration') }}" aria-label="Buka halaman Pendaftaran MAHAPATI"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($registration['pamphlet_path']) }}" alt="Pamflet Open Recruitment MAHAPATI 2026" loading="lazy"></a>
                </div>
            </section>
        @endif

        <section class="join-section" id="pendaftaran" aria-labelledby="join-title">
            <div class="page-container join-content">
                <div>
                    <p class="section-kicker">Bersama MAHAPATI</p>
                    <h2 id="join-title">Mari Bergabung dan Jadilah Bagian dari Perjalanan Ini</h2>
                    <p>Kenali kegiatan dan informasi pendaftaran calon anggota melalui kanal resmi MAHAPATI.</p>
                </div>
                <a class="button" href="/pendaftaran">Informasi Pendaftaran <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="kontak">
        <div class="page-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a class="footer-brand" href="/" aria-label="MAHAPATI, kembali ke Beranda">
                        <img src="{{ asset('images/logo-mahapati-new.png') }}" alt="">
                        <span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</small></span>
                    </a>
                    <p class="footer-slogan">“Bersama Alam, Berkarya untuk Negeri”</p>
                </div>
                <div class="footer-column">
                    <h3>Menu</h3>
                    <ul class="footer-links">
                        <li><a href="/">Beranda</a></li><li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="/divisi">Divisi</a></li><li><a href="/kegiatan">Kegiatan</a></li>
                        <li><a href="/galeri">Galeri</a></li><li><a href="/berita">Berita</a></li>
                        <li><a href="/prestasi">Prestasi</a></li><li><a href="/pendaftaran">Pendaftaran</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Kontak Resmi</h3>
                    <ul class="footer-contact">
                        <li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li>
                        <li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li>
                        <li><span>Fakultas Teknologi Industri, UNISSULA</span></li>
                    </ul>
                    <p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p>
                </div>
                <div class="footer-column">
                    <h3>Ikuti Kami</h3>
                    <div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div>
                    
                </div>
            </div>
            <div class="footer-bottom">
                <span>© MAHAPATI UNISSULA</span>
                <span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span>
            </div>
        </div>
    </footer>
</body>
</html>




