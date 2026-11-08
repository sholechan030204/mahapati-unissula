<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Berita dan publikasi terbaru dari MAHAPATI Universitas Islam Sultan Agung.">
    <title>@isset($article){{ $article->title }}@else Berita dan Publikasi @endisset | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/news.css') }}">
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
                <a href="{{ route('gallery') }}">Galeri</a><a href="{{ route('news.index') }}" aria-current="page">Berita</a>
                <a href="/prestasi">Prestasi</a><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        @isset($article)
            <nav class="news-breadcrumb page-container" aria-label="Jejak halaman">
                <a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span>
                <a href="{{ route('news.index') }}">Berita dan Publikasi</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ $article->title }}</span>
            </nav>
            <article class="news-article page-container">
                <header class="news-article-header">
                    <a class="news-back-link" href="{{ route('news.index') }}">← Kembali ke Berita dan Publikasi</a>
                    <p class="section-kicker">{{ $article->category }}</p>
                    <h1>{{ $article->title }}</h1>
                    @if ($article->published_at)
                        <p class="news-article-date">{{ $article->published_at->locale('id')->translatedFormat('d F Y') }}</p>
                    @endif
                </header>
                @if ($article->cover_image)
                    <figure class="news-article-cover">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="Sampul {{ $article->title }}">
                    </figure>
                @endif
                <div class="news-article-content">
                    <p class="news-article-excerpt">{{ $article->excerpt }}</p>
                    <div class="news-article-body">{!! nl2br(e($article->body)) !!}</div>
                </div>
            </article>
        @else
            <section class="news-hero" aria-labelledby="news-title">
                <div class="page-container news-hero-content">
                    <p class="hero-eyebrow">Kabar organisasi</p>
                    <h1 id="news-title">Berita dan Publikasi</h1>
                    <p>Ikuti kabar, informasi, dan publikasi kegiatan MAHAPATI.</p>
                    <a class="button" href="#artikel">Jelajahi Berita <span aria-hidden="true">↓</span></a>
                </div>
            </section>

            <section class="section news-intro" aria-labelledby="news-intro-title">
                <div class="page-container news-intro-copy">
                    <p class="section-kicker">Kabar MAHAPATI</p>
                    <h2 class="section-title" id="news-intro-title">Cerita dan informasi dari organisasi</h2>
                    <p>Temukan berita dan publikasi tentang kegiatan serta perkembangan MAHAPATI. Setiap artikel dapat dibuka untuk membaca informasi selengkapnya.</p>
                </div>
            </section>

            <section class="section news-list-section" id="artikel" aria-labelledby="article-list-title">
                <div class="page-container">
                    <div class="section-heading">
                        <div><p class="section-kicker">Informasi terbaru</p><h2 class="section-title" id="article-list-title">Berita dan Publikasi</h2></div>
                    </div>
                    @if ($articles->count() > 0)
                        <div class="news-grid">
                            @foreach ($articles as $article)
                                <a class="news-card" href="{{ route('news.show', ['slug' => $article->slug]) }}">
                                    @if ($article->cover_image)
                                        <img class="news-card-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="Sampul {{ $article->title }}" loading="lazy">
                                    @else
                                        <span class="news-card-placeholder" aria-hidden="true">MAHAPATI</span>
                                    @endif
                                    <span class="news-card-copy">
                                        <span class="news-card-meta">{{ $article->category }}@if ($article->published_at) · {{ $article->published_at->locale('id')->translatedFormat('d F Y') }}@endif</span>
                                        <strong>{{ $article->title }}</strong>
                                        <span class="news-card-excerpt">{{ $article->excerpt }}</span>
                                        <span class="news-card-link">Baca selengkapnya <span aria-hidden="true">→</span></span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        <div class="news-pagination">{{ $articles->links() }}</div>
                    @else
                        <div class="empty-state">
                            <span class="empty-state-icon" aria-hidden="true">▤</span>
                            <strong>Belum ada berita atau publikasi</strong>
                            <p>Berita dan publikasi MAHAPATI akan muncul di sini setelah diterbitkan.</p>
                        </div>
                    @endif
                </div>
            </section>
        @endisset

        <section class="join-section news-join" aria-labelledby="news-join-title">
            <div class="page-container join-content">
                <div><p class="section-kicker">Bersama MAHAPATI</p><h2 id="news-join-title">Jelajahi kegiatan kami</h2><p>Lihat kegiatan MAHAPATI dan temukan cerita di balik setiap perjalanan.</p></div>
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


