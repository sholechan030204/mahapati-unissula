<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Informasi persyaratan dan alur pendaftaran calon anggota MAHAPATI.">
    <title>Pendaftaran | MAHAPATI UNISSULA</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/registration.css') }}">
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
                <a href="/prestasi">Prestasi</a><a href="{{ route('registration') }}" aria-current="page">Pendaftaran</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </nav>
            @include('public.partials.auth-navigation')
        </div>
    </header>

    <main>
        <section class="registration-hero" aria-labelledby="registration-title">
            <div class="page-container registration-hero-content">
                <p class="hero-eyebrow">Mulai perjalanan bersama MAHAPATI</p>
                <h1 id="registration-title">Pendaftaran Calon Anggota</h1>
                <p>Kenali persyaratan dan tahapan penerimaan calon anggota MAHAPATI Fakultas Teknologi Industri UNISSULA.</p>
                <div class="registration-hero-actions">
                    <a class="button" href="{{ ($registration['is_open'] ?? false) ? '#formulir-awal' : '#registration-status-title' }}">{{ ($registration['is_open'] ?? false) ? 'Isi Formulir Awal' : 'Lihat Status Pendaftaran' }} <span aria-hidden="true">↓</span></a>
                    <a class="registration-secondary-button" href="#alur-pendaftaran">Lihat Alur Pendaftaran</a>
                </div>
            </div>
        </section>

        <section class="section registration-status-section" aria-labelledby="registration-status-title">
            <div class="page-container registration-status-card">
                <div class="registration-status-icon" aria-hidden="true">i</div>
                <div class="registration-status-content">
                    <p class="section-kicker">Informasi pendaftaran</p>
                    @if (($registration['is_open'] ?? false) === true)
                        <h2 id="registration-status-title">Pendaftaran sedang dibuka</h2>
                    @else
                        <h2 id="registration-status-title">Pendaftaran sedang ditutup</h2>
                    @endif
                    <p>Status pendaftaran dikelola secara manual oleh Admin dan Pengurus. Periksa jadwal seleksi berikut sebelum mengisi formulir.</p>
                    <dl class="registration-schedule">
                        <div><dt>Pendaftaran</dt><dd>{{ !empty($registration['opens_at']) ? \Illuminate\Support\Carbon::parse($registration['opens_at'])->locale('id')->translatedFormat('d F Y') : 'Belum ditetapkan' }} – {{ !empty($registration['closes_at']) ? \Illuminate\Support\Carbon::parse($registration['closes_at'])->locale('id')->translatedFormat('d F Y') : 'Belum ditetapkan' }}</dd></div>
                        <div><dt>Wawancara</dt><dd>{{ !empty($registration['interview_starts_at']) ? \Illuminate\Support\Carbon::parse($registration['interview_starts_at'])->locale('id')->translatedFormat('d F') : 'Belum ditetapkan' }} – {{ !empty($registration['interview_ends_at']) ? \Illuminate\Support\Carbon::parse($registration['interview_ends_at'])->locale('id')->translatedFormat('d F Y') : 'Belum ditetapkan' }}</dd></div>
                        @if (!empty($registration['announcement_at']))<div><dt>Pengumuman</dt><dd>{{ \Illuminate\Support\Carbon::parse($registration['announcement_at'])->locale('id')->translatedFormat('d F Y') }}</dd></div>@endif
                        @if (!empty($registration['quota']))
                            <div><dt>Kuota</dt><dd>{{ $registration['quota'] }} calon</dd></div>
                        @endif
                        @if (isset($registration['fee']) && $registration['fee'] !== null && $registration['fee'] !== '')
                            <div><dt>Biaya</dt><dd>{{ $registration['fee'] === 0 ? 'Tidak dipungut biaya' : 'Rp ' . number_format((int) $registration['fee'], 0, ',', '.') }}</dd></div>
                        @endif
                    </dl>
                    <p class="registration-channel-note">Pengumuman resmi dan informasi lanjutan disampaikan oleh Pengurus melalui kanal MAHAPATI dan WhatsApp.</p>
                </div>
                <span class="registration-availability {{ ($registration['is_open'] ?? false) === true ? 'is-open' : '' }}">{{ ($registration['is_open'] ?? false) === true ? 'Dibuka' : 'Ditutup' }}</span>
            </div>
        </section>

        @if (!empty($registration['pamphlet_path']))
            <section class="section registration-pamphlet-section" aria-label="Pamflet Open Recruitment MAHAPATI">
                <div class="page-container"><figure class="registration-pamphlet"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($registration['pamphlet_path']) }}" alt="Pamflet Open Recruitment MAHAPATI 2026" loading="lazy"></figure></div>
            </section>
        @endif

        <section class="section registration-requirements-section" aria-labelledby="requirements-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Sebelum mendaftar</p><h2 class="section-title" id="requirements-title">Persyaratan Calon Anggota</h2></div>
                </div>
                <div class="registration-requirement-card">
                    <span class="registration-card-icon" aria-hidden="true">✓</span>
                    <div><h3>Mahasiswa aktif FTI UNISSULA semester 1–3</h3><p>Pendaftaran calon anggota MAHAPATI tahun 2026 ditujukan kepada mahasiswa aktif Fakultas Teknologi Industri Universitas Islam Sultan Agung semester 1 sampai 3.</p></div>
                </div>
            </div>
        </section>

        <section class="section registration-documents-section" aria-labelledby="documents-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Siapkan berkas</p><h2 class="section-title" id="documents-title">Dokumen yang Diperlukan</h2></div>
                </div>
                <div class="registration-documents-grid">
                    <article class="registration-document-card">
                        <span class="registration-document-number">01</span>
                        <h3>Surat Keterangan Kesehatan</h3>
                        <p>Calon anggota memperoleh surat keterangan kesehatan dari Puskesmas atau rumah sakit, lalu menyerahkannya langsung kepada Pengurus di BSKEM.</p>
                    </article>
                    <article class="registration-document-card">
                        <span class="registration-document-number">02</span>
                        <h3>Surat Persetujuan Orang Tua</h3>
                        <p>Format surat dibagikan oleh Pengurus melalui grup WhatsApp. Surat yang sudah diisi diserahkan langsung kepada Pengurus di BSKEM.</p>
                    </article>
                </div>
                <p class="registration-document-note">Dokumen kesehatan dan persetujuan orang tua tidak diunggah melalui website.</p>
            </div>
        </section>

        <section class="section registration-process-section" id="alur-pendaftaran" aria-labelledby="process-title">
            <div class="page-container">
                <div class="section-heading">
                    <div><p class="section-kicker">Tahapan penerimaan</p><h2 class="section-title" id="process-title">Alur Pendaftaran</h2></div>
                </div>
                <ol class="registration-timeline">
                    <li class="registration-step"><span class="registration-step-number">1</span><div><h3>Isi formulir awal</h3><p>Calon anggota mengisi formulir awal di website saat pendaftaran dibuka. Pengurus menyampaikan informasi lanjutan melalui WhatsApp.</p></div></li>
                    <li class="registration-step"><span class="registration-step-number">2</span><div><h3>Terima informasi dan format surat</h3><p>Pengurus menyampaikan informasi tahapan melalui grup WhatsApp, termasuk format surat persetujuan orang tua.</p></div></li>
                    <li class="registration-step"><span class="registration-step-number">3</span><div><h3>Serahkan dokumen secara langsung</h3><p>Calon anggota menyerahkan surat keterangan kesehatan dan surat persetujuan orang tua kepada Pengurus di BSKEM. Tidak ada tes tertulis atau tes keterampilan; tahap seleksi yang ditetapkan adalah wawancara.</p></div></li>
                    <li class="registration-step"><span class="registration-step-number">4</span><div><h3>Ikuti wawancara dan terima keputusan</h3><p>Setelah berkas terpenuhi, calon anggota mengikuti wawancara. Keputusan diterima atau tidak disampaikan melalui grup WhatsApp setelah wawancara.</p></div></li>
                    <li class="registration-step"><span class="registration-step-number">5</span><div><h3>Pengurus melaporkan calon yang diterima kepada Admin</h3><p>Admin mencatat calon yang diterima. Calon kemudian mengikuti tahapan pendidikan dan pelatihan yang menjadi tanggung jawab Pengurus.</p></div></li>
                    <li class="registration-step"><span class="registration-step-number">6</span><div><h3>Nama alam dan penetapan keanggotaan</h3><p>Setelah diklat, calon memperoleh nama alam, tetapi belum berstatus anggota tetap dan belum mendapat NRA. Penetapan keanggotaan dan NRA dilakukan Admin setelah tahapan pelantikan sesuai ketentuan organisasi.</p></div></li>
                </ol>
            </div>
        </section>

        <section class="section registration-followup-section" aria-labelledby="followup-title">
            <div class="page-container registration-followup-card">
                <p class="section-kicker">Berkas belum lengkap?</p>
                <h2 class="section-title" id="followup-title">Pengurus akan menghubungi calon anggota</h2>
                <p>Calon diberi waktu 24 jam untuk melengkapi atau memperbaiki berkas. Pengurus dapat memberi satu kali perpanjangan 24 jam dan tetap melakukan tindak lanjut selama belum memasuki hari H diklat. Jika berkas masih belum lengkap pada hari H diklat, calon tidak dapat diterima pada tahap tersebut.</p>
            </div>
        </section>

        <section class="section registration-form-section" id="formulir-awal" aria-labelledby="form-title">
            <div class="page-container">
                <div class="registration-form-heading">
                    <div><p class="section-kicker">Langkah pertama</p><h2 class="section-title" id="form-title">Formulir Awal Pendaftaran</h2><p>Formulir ini adalah pendaftaran calon anggota, terpisah dari pengajuan akun untuk anggota yang sudah tercatat.</p></div>
                    <span class="registration-availability {{ ($registration['is_open'] ?? false) ? 'is-open' : '' }}">{{ ($registration['is_open'] ?? false) ? 'Pendaftaran dibuka' : 'Pendaftaran ditutup' }}</span>
                </div>
                <div class="registration-form-card">
                    <p class="registration-form-explanation">Data awal yang diminta hanya untuk menghubungi dan memverifikasi calon. Surat kesehatan dan persetujuan orang tua tetap diserahkan langsung; tidak ada unggah berkas di formulir ini.</p>
                    @if (session('registrationSubmitted'))
                        <div class="registration-success" role="status"><strong>Formulir awal berhasil dikirim.</strong><p>Pengurus akan menghubungi Anda untuk informasi selanjutnya. Surat kesehatan dan persetujuan orang tua tetap diserahkan langsung.</p>@if (session('whatsappGroupLink'))<a class="button" href="{{ session('whatsappGroupLink') }}" target="_blank" rel="noopener noreferrer">Gabung Grup WhatsApp Calon Anggota →</a>@else<p>Tautan grup WhatsApp akan diberikan Pengurus melalui nomor yang Anda cantumkan.</p>@endif</div>
                    @endif
                    @if ($errors->any())<div class="registration-form-errors" role="alert">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="{{ route('registration.store') }}" aria-describedby="registration-form-status">
                        @csrf
                        <fieldset @disabled(!($registration['is_open'] ?? false))>
                            <legend>Data calon anggota</legend>
                            <div class="registration-fields-grid">
                                <label>Nama lengkap<input type="text" name="full_name" value="{{ old('full_name') }}" autocomplete="name" maxlength="180" placeholder="Masukkan nama lengkap" required>@error('full_name')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>NIM<input type="text" name="student_number" value="{{ old('student_number') }}" autocomplete="off" maxlength="40" placeholder="Masukkan NIM" required>@error('student_number')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>Program studi<input type="text" name="study_program" value="{{ old('study_program') }}" maxlength="150" placeholder="Masukkan program studi" required>@error('study_program')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>Angkatan<input type="text" name="cohort" value="{{ old('cohort') }}" inputmode="numeric" maxlength="4" pattern="[0-9]{4}" placeholder="Contoh: 2025" required>@error('cohort')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>Semester saat ini<select name="semester" required><option value="">Pilih semester</option>@foreach ([1, 2, 3] as $semester)<option value="{{ $semester }}" @selected((string) old('semester') === (string) $semester)>Semester {{ $semester }}</option>@endforeach</select>@error('semester')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>Email aktif<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190" placeholder="nama@email.com" required>@error('email')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                                <label>Nomor WhatsApp<input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" autocomplete="tel" maxlength="30" placeholder="08xxxxxxxxxx" required>@error('whatsapp')<small class="registration-input-error">{{ $message }}</small>@enderror</label>
                            </div>
                            <label class="registration-consent"><input type="checkbox" name="terms_accepted" value="1" @checked(old('terms_accepted')) required><span>Saya menyatakan data yang dikirim benar dan mengizinkan Pengurus MAHAPATI menghubungi saya terkait proses pendaftaran.</span></label>
                            @error('terms_accepted')<small class="registration-input-error">{{ $message }}</small>@enderror
                            <label class="registration-consent"><input type="checkbox" name="eligibility_confirmed" value="1" @checked(old('eligibility_confirmed')) required><span>Saya mahasiswa aktif FTI UNISSULA semester 1–3.</span></label>
                            @error('eligibility_confirmed')<small class="registration-input-error">{{ $message }}</small>@enderror
                            <button class="button registration-submit" type="submit">Kirim Formulir Awal</button>
                        </fieldset>
                    </form>
                    <p class="registration-form-status" id="registration-form-status">{{ ($registration['is_open'] ?? false) ? 'Isi data dengan benar. Setelah dikirim, Pengurus akan memberi informasi lanjutan melalui WhatsApp.' : 'Formulir sedang ditutup. Pantau kanal resmi MAHAPATI untuk jadwal penerimaan berikutnya.' }}</p>
                    <p class="registration-optional-note">Kuota dan biaya pendaftaran bersifat opsional; jika diberlakukan, Pengurus akan mengumumkannya.</p>
                </div>
            </div>
        </section>

        <section class="join-section registration-join" aria-labelledby="registration-join-title">
            <div class="page-container join-content">
                <div><p class="section-kicker">Bersama MAHAPATI</p><h2 id="registration-join-title">Siapkan diri untuk memulai perjalanan</h2><p>Pantau kanal resmi MAHAPATI untuk mengetahui jadwal dan informasi penerimaan calon anggota.</p></div>
                <a class="button" href="{{ route('contact') }}">Hubungi MAHAPATI <span aria-hidden="true">→</span></a>
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
                    <li><a href="/prestasi">Prestasi</a></li><li><a href="{{ route('registration') }}">Pendaftaran</a></li>
                </ul></div>
                <div class="footer-column"><h3>Kontak Resmi</h3><ul class="footer-contact"><li>@if (filled(config('public_contact.whatsapp')))<a href="https://wa.me/{{ config('public_contact.whatsapp_link_number') }}">WhatsApp: {{ config('public_contact.whatsapp') }}</a>@else<span>WhatsApp organisasi: nomor belum tersedia</span>@endif</li><li><a href="mailto:{{ config('public_contact.email') }}">{{ config('public_contact.email') }}</a></li><li><span>Fakultas Teknologi Industri, UNISSULA</span></li></ul><p class="footer-note">Kontak resmi dapat digunakan untuk menghubungi Pengurus MAHAPATI.</p></div>
                <div class="footer-column"><h3>Ikuti Kami</h3><div class="social-list" aria-label="Media sosial MAHAPATI">@foreach (config('public_contact.social_links') as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>@endforeach</div></div>
            </div>
            <div class="footer-bottom"><span>© MAHAPATI UNISSULA</span><span>Menjaga Alam · Mengembangkan Potensi · Berkarya untuk Negeri</span></div>
        </div>
    </footer>
</body>
</html>


