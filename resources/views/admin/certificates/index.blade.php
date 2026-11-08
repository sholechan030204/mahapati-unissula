@extends('layouts.admin')

@section('title', 'Sertifikat')

@section('content')
    <div class="admin-content-heading certificate-heading">
        <div><span class="eyebrow-label">ARSIP DOKUMEN ANGGOTA</span><h1>Sertifikat</h1><p>Kelola sertifikat dan tinjau pengajuan sertifikat dari Pengurus serta Anggota.</p></div>
        <a class="admin-action-button" href="{{ route('admin.certificates.create') }}">＋ Tambah Sertifikat</a>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card certificate-card">
        <div class="activity-list-toolbar">
            <div><h2>Daftar Sertifikat</h2><p>{{ $pendingCount }} pengajuan menunggu tinjauan. PDF tetap privat dan hanya dapat diakses Admin atau pemilik sertifikat yang telah disetujui.</p></div>
            <form class="activity-filters" method="GET" action="{{ route('admin.certificates.index') }}">
                <input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari sertifikat, anggota, NRA" aria-label="Cari sertifikat">
                <select class="admin-select" name="status" aria-label="Filter status sertifikat"><option value="">Semua status</option><option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Menunggu</option><option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Disetujui</option><option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Ditolak</option></select>
                <button class="admin-action-button" type="submit">Cari</button>
                @if (($filters['q'] ?? '') !== '' || ($filters['status'] ?? '') !== '')<a class="admin-secondary-button" href="{{ route('admin.certificates.index') }}">Reset</a>@endif
            </form>
        </div>
        @if ($certificates->isEmpty())
            <div class="certificate-empty"><span aria-hidden="true">▧</span><strong>Belum ada sertifikat</strong><p>Sertifikat atau pengajuan yang cocok dengan filter akan muncul di sini.</p><a class="admin-action-button" href="{{ route('admin.certificates.create') }}">Tambah Sertifikat</a></div>
        @else
            <div class="table-scroll"><table class="admin-table certificate-table"><thead><tr><th>Sertifikat</th><th>Anggota</th><th>Kegiatan</th><th>Status</th><th>Tanggal Terbit</th><th>Aksi</th></tr></thead><tbody>
                @foreach ($certificates as $certificate)
                    <tr><td><strong>{{ $certificate->title }}</strong><small class="table-secondary">{{ $certificate->certificate_number ?: 'Nomor sertifikat belum diisi' }}</small></td><td>{{ $certificate->member?->full_name ?? 'Anggota tidak tersedia' }}<small class="table-secondary">NRA: {{ $certificate->member?->nra ?? '—' }}</small></td><td>{{ $certificate->activity?->title ?? 'Kegiatan tidak tersedia' }}</td><td><span class="admin-badge {{ $certificate->status === 'pending' ? 'pending' : ($certificate->status === 'rejected' ? 'rejected' : '') }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$certificate->status] ?? $certificate->status }}</span></td><td>{{ $certificate->issued_at?->translatedFormat('d M Y') ?? '—' }}</td><td><a class="table-action-link" href="{{ route('admin.certificates.show', $certificate) }}">Detail →</a></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="admin-pagination">{{ $certificates->links() }}</div>
        @endif
    </section>
@endsection
