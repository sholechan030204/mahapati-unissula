@extends('layouts.admin')

@section('title', 'Detail Sertifikat')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">ARSIP DOKUMEN ANGGOTA</span><h1>Detail Sertifikat</h1><p>Informasi pengajuan, tinjauan, dan berkas sertifikat.</p></div><a class="admin-secondary-button" href="{{ route('admin.certificates.index') }}">← Daftar Sertifikat</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card certificate-detail-card">
        <div class="certificate-detail-top"><div><span class="certificate-file-mark">PDF</span><div><span class="eyebrow-label">SERTIFIKAT MAHAPATI</span><h2>{{ $certificate->title }}</h2><p>{{ $certificate->certificate_number ?: 'Nomor sertifikat belum diisi' }}</p><span class="admin-badge {{ $certificate->status === 'pending' ? 'pending' : ($certificate->status === 'rejected' ? 'rejected' : '') }}">{{ ['pending' => 'Menunggu persetujuan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$certificate->status] ?? $certificate->status }}</span></div></div><div class="certificate-detail-actions"><a class="admin-action-button" href="{{ route('admin.certificates.download', $certificate) }}">↓ Periksa / Unduh PDF</a><a class="admin-secondary-button" href="{{ route('admin.certificates.edit', $certificate) }}">Edit</a></div></div>
        <div class="certificate-detail-grid"><div><span>Penerima</span><strong>{{ $certificate->member?->full_name ?? '—' }}</strong><small>NRA {{ $certificate->member?->nra ?? '—' }}</small></div><div><span>Kegiatan</span><strong>{{ $certificate->activity?->title ?? '—' }}</strong></div><div><span>Tanggal terbit pada dokumen</span><strong>{{ $certificate->issued_at?->translatedFormat('d F Y') ?? 'Belum dicatat' }}</strong></div><div><span>Diajukan oleh</span><strong>{{ $certificate->createdBy?->name ?? 'Admin' }}</strong></div>@if ($certificate->reviewed_at)<div><span>Ditinjau oleh</span><strong>{{ $certificate->reviewedBy?->name ?? 'Admin' }}</strong><small>{{ $certificate->reviewed_at->translatedFormat('d M Y, H:i') }}</small></div>@endif</div>
        @if ($certificate->notes)<div class="certificate-notes"><span>Catatan Admin</span><p>{{ $certificate->notes }}</p></div>@endif
        @if ($certificate->rejection_reason)<div class="admin-alert error" role="note"><strong>Alasan penolakan:</strong> {{ $certificate->rejection_reason }}</div>@endif
        @if ($certificate->status === 'pending')
            <div class="certificate-review-actions">
                <form method="POST" action="{{ route('admin.certificates.approve', $certificate) }}" onsubmit="return confirm('Setujui sertifikat ini? Setelah disetujui, PDF dapat diunduh oleh pemiliknya.')">
                    @csrf
                    <button class="admin-action-button" type="submit">Setujui Sertifikat</button>
                </form>
                <form method="POST" action="{{ route('admin.certificates.reject', $certificate) }}" class="admin-tool-form">
                    @csrf
                    <div class="admin-field"><label for="rejection_reason">Alasan penolakan *</label><textarea class="admin-textarea" id="rejection_reason" name="rejection_reason" maxlength="2000" required placeholder="Jelaskan alasan agar pemohon tahu apa yang perlu diperbaiki."></textarea>@error('rejection_reason')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <button class="admin-danger-button" type="submit">Tolak Pengajuan</button>
                </form>
            </div>
        @endif
        <form class="certificate-delete-form" method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm('Hapus sertifikat ini beserta file PDF-nya?')">@csrf @method('DELETE')<button class="admin-danger-button" type="submit">Hapus Sertifikat</button></form>
    </section>
@endsection
