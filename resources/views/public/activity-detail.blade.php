<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detail kegiatan MAHAPATI Universitas Islam Sultan Agung.">
    <title>{{ $activity->title }} | Kegiatan MAHAPATI</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/activities.css') }}">
    <script src="{{ asset('js/home.js') }}?v=2" defer></script>
</head>
<body>
    <header class="site-header"><div class="navbar">
        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mainNav"><span></span><span></span><span></span></button>
        <a class="brand" href="{{ route('home') }}" aria-label="MAHAPATI, kembali ke Beranda"><img class="brand-logo" src="{{ asset('images/logo-mahapati-new.png') }}" alt="Logo MAHAPATI"><span class="brand-copy"><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri</small></span></a>
        <nav class="nav-links" id="mainNav" aria-label="Navigasi utama"><a href="{{ route('home') }}">Beranda</a><a href="{{ route('profile') }}">Tentang Kami</a><a href="{{ route('divisions') }}">Divisi</a><a href="{{ route('activities') }}" aria-current="page">Kegiatan</a><a href="{{ route('home') }}#galeri">Galeri</a><a href="{{ route('home') }}#berita">Berita</a><a href="/prestasi">Prestasi</a><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a><a href="{{ route('contact') }}">Kontak</a></nav>
        @include('public.partials.auth-navigation')
    </div></header>

    <main>
        <section class="activity-detail-hero">
            @if ($activity->cover_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activity->cover_image) }}" alt="Foto {{ $activity->title }}">@else<img src="{{ asset('images/hero-mahapat.jpg') }}" alt="Anggota MAHAPATI berkegiatan di alam terbuka">@endif
            <div class="activity-detail-hero-overlay"></div>
            <div class="page-container activity-detail-hero-copy"><a href="{{ route('activities') }}">← Semua Kegiatan</a><span>{{ $activity->workProgram?->title ?? 'KEGIATAN MAHAPATI' }}</span><h1>{{ $activity->title }}</h1></div>
        </section>
        <section class="section public-activity-detail-section"><div class="page-container public-activity-detail-grid">
            <article class="public-activity-detail-copy"><span class="public-activity-status {{ $activity->status }}">{{ ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'][$activity->status] ?? $activity->status }}</span><h2>Tentang Kegiatan</h2><p>{{ $activity->description }}</p></article>
            <aside class="public-activity-detail-facts"><h2>Informasi Kegiatan</h2><div><span>Jadwal rencana</span><strong>{{ $activity->planned_at->locale('id')->translatedFormat('l, d F Y · H:i') }} WIB</strong></div><div><span>Realisasi</span><strong>{{ $activity->realized_at?->locale('id')->translatedFormat('l, d F Y · H:i') ?? 'Belum tercatat' }}@if ($activity->realized_at) WIB @endif</strong></div><div><span>Lokasi</span><strong>{{ $activity->location }}</strong></div>@if ($activity->workProgram)<div><span>Program kerja</span><strong>{{ $activity->workProgram->title }}</strong></div>@endif</aside>
        </div></section>
    </main>

    <footer class="site-footer" id="kontak"><div class="page-container"><div class="footer-grid">
        <div class="footer-about"><a class="footer-brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mahapati-new.png') }}" alt=""><span><strong>MAHAPATI</strong><small>Mahasiswa Pecinta Alam Teknologi Industri<br>Universitas Islam Sultan Agung</small></span></a><p class="footer-slogan">“Bersama Alam, Berkarya untuk Negeri”</p></div>
        <div class="footer-column"><h3>Menu</h3><ul class="footer-links"><li><a href="{{ route('home') }}">Beranda</a></li><li><a href="{{ route('profile') }}">Tentang Kami</a></li><li><a href="{{ route('divisions') }}">Divisi</a></li><li><a href="{{ route('activities') }}">Kegiatan</a></li><li><a href="{{ route('home') }}#galeri">Galeri</a></li><li><a href="{{ route('home') }}#berita">Berita</a></li><li><a href="/prestasi">Prestasi</a></li><li><a href="{{ route('home') }}#pendaftaran">Pendaftaran</a></li></ul></div>
        <div class="footer-column"><h3>Kontak Resmi</h3><ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul></div>
        <div class="footer-column"><h3>Ikuti Kami</h3><div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div></div>
    </div><div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div></div></footer>
</body>
</html>
