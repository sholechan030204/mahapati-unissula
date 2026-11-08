<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Jelajahi informasi kegiatan MAHAPATI Universitas Islam Sultan Agung.">
    <title>Kegiatan | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
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
                <a href="{{ route('divisions') }}">Divisi</a><a href="{{ route('activities') }}" aria-current="page">Kegiatan</a>
                <a href="{{ route('home') }}#galeri">Galeri</a><a href="{{ route('home') }}#berita">Berita</a>
                <a href="/prestasi">Prestasi</a><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="activities-hero" aria-labelledby="activities-title">
            <div class="page-container activities-hero-content">
                <p class="hero-eyebrow">Menjelajah, belajar, dan berkarya</p>
                <h1 id="activities-title">Kegiatan MAHAPATI</h1>
                <p>Ikuti perjalanan kegiatan MAHAPATI, mulai dari aktivitas lapangan hingga kegiatan pendidikan dan kepedulian terhadap alam.</p>
                <div class="activities-hero-actions">
                    <a class="button" href="#sorotan">Sorotan Kegiatan <span aria-hidden="true">↓</span></a>
                    <a class="button button-outline" href="#daftar-kegiatan">Lihat Daftar Kegiatan</a>
                </div>
            </div>
        </section>

        <section class="section activities-intro" aria-labelledby="activities-intro-title">
            <div class="page-container activities-intro-copy">
                <p class="section-kicker">Berkegiatan bersama</p>
                <h2 class="section-title" id="activities-intro-title">Setiap perjalanan menjadi pengalaman</h2>
                <p>Halaman ini menjadi tempat untuk melihat sorotan kegiatan dan informasi kegiatan MAHAPATI. Informasi kegiatan akan ditampilkan setelah diterbitkan melalui sistem.</p>
            </div>
        </section>

        <section class="section activities-feature-section" id="sorotan" aria-labelledby="feature-title">
            <div class="page-container">
                <div class="section-heading"><div><p class="section-kicker">Kegiatan pilihan</p><h2 class="section-title" id="feature-title">Sorotan Utama</h2></div></div>
                @if ($featuredActivity)
                    <article class="public-feature-activity">
                        @if ($featuredActivity->cover_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($featuredActivity->cover_image) }}" alt="Foto {{ $featuredActivity->title }}">@else<img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI di alam terbuka">@endif
                        <div class="public-feature-activity-copy"><span class="public-feature-label">SOROTAN KEGIATAN</span><span class="public-activity-status {{ $featuredActivity->status }}">{{ ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'][$featuredActivity->status] ?? $featuredActivity->status }}</span><h3>{{ $featuredActivity->title }}</h3><p>{{ \Illuminate\Support\Str::limit($featuredActivity->description, 260) }}</p><div class="public-activity-meta"><span>▣ {{ $featuredActivity->planned_at->translatedFormat('d F Y · H:i') }} WIB</span><span>⌖ {{ $featuredActivity->location }}</span></div><a class="button" href="{{ route('activities.show', $featuredActivity) }}">Lihat Detail <span aria-hidden="true">→</span></a></div>
                    </article>
                @else
                    <div class="activities-feature-empty empty-state"><span class="empty-state-icon" aria-hidden="true">▲</span><strong>Belum ada sorotan kegiatan</strong><p>Admin dapat memilih satu kegiatan sebagai sorotan halaman publik melalui menu Kegiatan.</p></div>
                @endif
            </div>
        </section>

        <section class="section activities-list-section" id="daftar-kegiatan" aria-labelledby="activity-list-title">
            <div class="page-container">
                <div class="section-heading"><div><p class="section-kicker">Catatan perjalanan MAHAPATI</p><h2 class="section-title" id="activity-list-title">Semua Kegiatan</h2></div></div>
                @if ($activities->isEmpty())
                    <div class="empty-state"><span class="empty-state-icon" aria-hidden="true">⌁</span><strong>Belum ada kegiatan yang dipublikasikan</strong><p>Daftar kegiatan akan muncul di sini setelah informasi kegiatan diterbitkan.</p></div>
                @else
                    <div class="public-activity-grid public-activity-list">
                        @foreach ($activities as $activity)
                            <a class="public-activity-card" href="{{ route('activities.show', $activity) }}">
                                @if ($activity->cover_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activity->cover_image) }}" alt="Foto {{ $activity->title }}" loading="lazy">@else<img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI di alam terbuka" loading="lazy">@endif
                                <div class="public-activity-card-copy"><span>{{ $activity->planned_at->translatedFormat('d M Y') }} · {{ $activity->location }}</span><h3>{{ $activity->title }}</h3><p>{{ \Illuminate\Support\Str::limit($activity->description, 130) }}</p><span class="public-activity-status {{ $activity->status }}">{{ ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'][$activity->status] ?? $activity->status }}</span></div>
                            </a>
                        @endforeach
                    </div>
                    <div class="public-activity-pagination">{{ $activities->links() }}</div>
                @endif
            </div>
        </section>

        <section class="join-section activities-join" aria-labelledby="activities-join-title">
            <div class="page-container join-content">
                <div><p class="section-kicker">Bersama MAHAPATI</p><h2 id="activities-join-title">Ingin menjadi bagian dari perjalanan ini?</h2><p>Lihat informasi pendaftaran calon anggota melalui halaman pendaftaran MAHAPATI.</p></div>
                <a class="button" href="{{ route('home') }}#pendaftaran">Informasi Pendaftaran <span aria-hidden="true">→</span></a>
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
                    <li><a href="{{ route('home') }}#galeri">Galeri</a></li><li><a href="{{ route('home') }}#berita">Berita</a></li>
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

