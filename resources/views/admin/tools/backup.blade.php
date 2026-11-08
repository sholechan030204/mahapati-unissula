@extends('layouts.admin')

@section('title', 'BackUp & Export')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">ARSIP DAN PEMINDAHAN DATA</span><h1>BackUp &amp; Export</h1><p>Unduh cadangan data aplikasi atau ekspor data pilihan untuk dibuka di Excel.</p></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    <div class="admin-tool-grid">
        <section class="dashboard-card admin-tool-card">
            <div class="tool-section-heading"><span class="tool-icon">▣</span><div><h2>Cadangan data aplikasi</h2><p>Cadangan JSON mencakup data organisasi dan akun, tanpa data sesi, cache, atau antrean sementara.</p></div></div>
            <a class="admin-action-button" href="{{ route('admin.backup.download') }}">Unduh Cadangan JSON</a>
            <div class="admin-warning-note"><strong>Pemulihan mengganti data aplikasi saat ini.</strong><span>Sebelum melanjutkan, buat dan simpan cadangan kondisi sekarang di komputer. Gunakan hanya cadangan MAHAPATI dengan struktur yang sama. Berkas mencakup data akun (termasuk hash kata sandi), jadi simpan di tempat pribadi. Foto dan berkas unggahan tidak ikut di dalam cadangan database ini.</span></div>
            <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" class="admin-restore-form" onsubmit="return confirm('Pemulihan akan mengganti seluruh data aplikasi dengan isi berkas cadangan. Pastikan Anda sudah mengunduh cadangan kondisi saat ini. Lanjutkan?')">
                @csrf
                <label class="admin-field"><span>Pilih berkas cadangan MAHAPATI (.json)</span><input class="admin-input" type="file" name="backup_file" accept=".json,application/json" required><small>Maksimal 50 MB. Berkas diperiksa format dan kolomnya sebelum pemulihan.</small></label>
                <button class="admin-danger-button" type="submit">Pulihkan dari Cadangan</button>
            </form>
        </section>
        <section class="dashboard-card admin-tool-card">
            <div class="tool-section-heading"><span class="tool-icon gold">⇩</span><div><h2>Ekspor per modul</h2><p>File CSV menggunakan UTF-8 agar teks Indonesia terbaca di Excel.</p></div></div>
            <div class="export-list">
                @foreach ($exports as $key => $export)
                    <div class="export-row"><div><strong>{{ ['members' => 'Data Anggota', 'programs' => 'Program Kerja', 'activities' => 'Kegiatan', 'cash' => 'Transaksi Kas', 'news' => 'Berita', 'achievements' => 'Prestasi'][$key] }}</strong><small>{{ number_format($counts[$key]) }} baris data</small></div><a class="admin-secondary-button" href="{{ route('admin.backup.csv', ['dataset' => $key]) }}">Unduh CSV</a></div>
                @endforeach
            </div>
        </section>
    </div>
    <p class="admin-tool-footnote">Penyimpanan dan jadwal arsip jangka panjang tetap mengikuti kebijakan organisasi. Fitur ini mengunduh cadangan ke perangkat Admin dan tidak membuat salinan rutin tersembunyi di hosting.</p>
@endsection
