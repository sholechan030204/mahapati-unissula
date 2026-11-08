<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lihat catatan prestasi MAHAPATI Universitas Islam Sultan Agung.">
    <title>@isset($achievement){{ $achievement->title }}@else Prestasi MAHAPATI @endisset | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
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
                <a href="{{ route('achievements.index') }}" aria-current="page">Prestasi</a>
                <a href="{{ route('home') }}#pendaftaran">Pendaftaran</a><a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        @isset($achievement)
            <nav class="achievement-breadcrumb page-container" aria-label="Jejak halaman">
                <a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span>
                <a href="{{ route('achievements.index') }}">Prestasi</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ $achievement->title }}</span>
            </nav>
            <article class="achievement-article page-container">
                <header class="achievement-article-header">
                    <a class="achievement-back-link" href="{{ route('achievements.index') }}">← Kembali ke Prestasi</a>
                    <p class="section-kicker">Catatan prestasi MAHAPATI</p>
                    <h1>{{ $achievement->title }}</h1>
                    <div class="achievement-meta">
                        @if ($achievement->level)<span>{{ $achievement->level }}</span>@endif
                        @if ($achievement->achievement_date)<time datetime="{{ $achievement->achievement_date->toDateString() }}">{{ $achievement->achievement_date->locale('id')->translatedFormat('d F Y') }}</time>@endif
                    </div>
                </header>
                @if ($achievement->image_path)
                    <figure class="achievement-cover"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($achievement->image_path) }}" alt="Dokumentasi prestasi {{ $achievement->title }}"></figure>
                @endif
                <div class="achievement-article-content">
                    @if ($achievement->organizer)<p class="achievement-organizer"><strong>Penyelenggara:</strong> {{ $achievement->organizer }}</p>@endif
                    <p class="achievement-summary">{{ $achievement->summary }}</p>
                    <div class="achievement-description">{!! nl2br(e($achievement->description)) !!}</div>
                </div>
            </article>
        @else
            <section class="achievements-hero" aria-labelledby="achievements-title">
                <div class="page-container achievements-hero-content">
                    <p class="hero-eyebrow">Jejak perjalanan dan pencapaian</p>
                    <h1 id="achievements-title">Prestasi MAHAPATI</h1>
                    <p>Catatan pencapaian dan kontribusi anggota MAHAPATI dalam berbagai kegiatan.</p>
                    <a class="button" href="#daftar-prestasi">Lihat Prestasi <span aria-hidden="true">↓</span></a>
                </div>
            </section>

            <section class="section achievements-intro" aria-labelledby="achievements-intro-title">
                <div class="page-container achievements-intro-copy">
                    <p class="section-kicker">Apresiasi atas usaha</p>
                    <h2 class="section-title" id="achievements-intro-title">Setiap pencapaian menyimpan cerita</h2>
                    <p>Halaman ini memuat prestasi MAHAPATI yang telah dicatat dan diterbitkan oleh organisasi.</p>
                </div>
            </section>

            <section class="section achievements-list-section" id="daftar-prestasi" aria-labelledby="achievement-list-title">
                <div class="page-container">
                    <div class="section-heading">
                        <div><p class="section-kicker">Catatan pencapaian</p><h2 class="section-title" id="achievement-list-title">Daftar Prestasi</h2></div>
                    </div>
                    @if ($achievements->isNotEmpty())
                        <div class="achievement-grid">
                            @foreach ($achievements as $achievement)
                                    <a class="achievement-card" href="{{ route('achievements.show', ['slug' => $achievement->slug]) }}">
                                    @if ($achievement->image_path)
                                        <img class="achievement-card-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($achievement->image_path) }}" alt="Dokumentasi prestasi {{ $achievement->title }}" loading="lazy">
                                    @else
                                        <span class="achievement-card-placeholder" aria-hidden="true">★</span>
                                    @endif
                                    <span class="achievement-card-copy">
                                        <span class="achievement-card-meta">{{ $achievement->level ?? 'Prestasi MAHAPATI' }}@if ($achievement->achievement_date) · {{ $achievement->achievement_date->locale('id')->translatedFormat('d F Y') }}@endif</span>
                                        <strong>{{ $achievement->title }}</strong>
                                        <span class="achievement-card-summary">{{ $achievement->summary }}</span>
                                        <span class="achievement-card-link">Lihat detail <span aria-hidden="true">→</span></span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        <div class="achievement-pagination">{{ $achievements->links() }}</div>
                    @else
                        <div class="empty-state">
                            <span class="empty-state-icon" aria-hidden="true">★</span>
                            <strong>Belum ada prestasi yang dipublikasikan</strong>
                            <p>Catatan prestasi MAHAPATI akan muncul di sini setelah data resminya tersedia dan diterbitkan.</p>
                        </div>
                    @endif
                </div>
            </section>
        @endisset

        <section class="join-section achievements-join" aria-labelledby="achievements-join-title">
            <div class="page-container join-content">
                <div><p class="section-kicker">Bersama MAHAPATI</p><h2 id="achievements-join-title">Lihat perjalanan kegiatan kami</h2><p>Jelajahi kegiatan dan pengalaman MAHAPATI di alam terbuka.</p></div>
                <a class="button" href="{{ route('activities') }}">Lihat Kegiatan <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </main>

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
                    <li><a href="{{ route('gallery') }}">Galeri</a></li><li><a href="{{ route('news.index') }}">Berita</a></li>
                    <li><a href="{{ route('achievements.index') }}">Prestasi</a></li><li><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a></li>
                </ul></div>
                <div class="footer-column"><h3>Kontak Resmi</h3><ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul><p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p></div>
                <div class="footer-column"><h3>Ikuti Kami</h3><div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div></div>
            </div>
            <div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div>
        </div>
    </footer>
</body>
</html>


