@extends('layouts.admin')

@section('title', 'Ajukan Sertifikat Saya')

@section('content')
    @php($certificateRoutePrefix = auth()->user()->role === 'pengurus' ? 'pengurus' : 'member')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">DOKUMEN PRIBADI</span><h1>Ajukan Sertifikat Saya</h1><p>Unggah sertifikat milik Anda untuk diperiksa Admin. Sertifikat belum tersedia untuk diunduh sebelum disetujui.</p></div>
        <a class="admin-secondary-button" href="{{ route($certificateRoutePrefix.'.certificates.mine') }}">← Kembali ke Sertifikat Saya</a>
    </div>
    @if ($errors->any())<div class="admin-alert error" role="alert">Periksa kembali isian yang ditandai. {{ $errors->first() }}</div>@endif
    <div class="admin-alert" role="note">PDF disimpan secara privat. Hanya Anda dan Admin yang dapat mengakses sertifikat ini. Sertifikat tidak ditampilkan pada website publik.</div>
    <form class="dashboard-card certificate-form" method="POST" enctype="multipart/form-data" action="{{ route($certificateRoutePrefix.'.certificates.store') }}">
        @csrf
        <div class="certificate-form-grid">
            <div class="admin-field certificate-notes-field"><label for="title">Nama sertifikat *</label><input class="admin-input" id="title" name="title" value="{{ old('title') }}" maxlength="180" required placeholder="Contoh: Sertifikat Pelatihan Pertolongan Pertama">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="certificate_number">Nomor sertifikat</label><input class="admin-input" id="certificate_number" name="certificate_number" value="{{ old('certificate_number') }}" maxlength="100" placeholder="Opsional">@error('certificate_number')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="activity_id">Kegiatan terkait *</label><select class="admin-select" id="activity_id" name="activity_id" required><option value="">Pilih kegiatan</option>@foreach ($activities as $activity)<option value="{{ $activity->id }}" @selected((string) old('activity_id') === (string) $activity->id)>{{ $activity->title }}{{ $activity->planned_at ? ' — '.$activity->planned_at->translatedFormat('d M Y') : '' }}</option>@endforeach</select><small>Pilih kegiatan MAHAPATI yang terkait dengan sertifikat.</small>@error('activity_id')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="issued_at">Tanggal terbit pada sertifikat *</label><input class="admin-input" type="date" id="issued_at" name="issued_at" value="{{ old('issued_at', today()->format('Y-m-d')) }}" required>@error('issued_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field certificate-upload-field certificate-notes-field"><label for="pdf">Berkas sertifikat PDF *</label><input class="admin-input" type="file" id="pdf" name="pdf" accept="application/pdf,.pdf" required><small>PDF saja, maksimal 5 MB.</small>@error('pdf')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field certificate-notes-field"><label for="notes">Catatan untuk Admin (opsional)</label><textarea class="admin-textarea" id="notes" name="notes" rows="4" maxlength="3000" placeholder="Informasi tambahan untuk membantu pemeriksaan.">{{ old('notes') }}</textarea>@error('notes')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div>
        <div class="admin-button-row certificate-actions"><button class="admin-action-button" type="submit">Kirim untuk Persetujuan</button><a class="admin-secondary-button" href="{{ route($certificateRoutePrefix.'.certificates.mine') }}">Batal</a></div>
    </form>
@endsection
