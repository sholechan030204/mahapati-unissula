@extends('layouts.admin')

@section('title', 'Sertifikat Saya')

@section('content')
    @php($certificateRoutePrefix = auth()->user()->role === 'pengurus' ? 'pengurus' : 'member')
    <div class="admin-content-heading"><div><span class="eyebrow-label">DOKUMEN PRIBADI</span><h1>Sertifikat Saya</h1><p>Ajukan sertifikat milik Anda dan pantau hasil tinjauan Admin.</p></div>@if ($hasMemberProfile)<a class="admin-action-button" href="{{ route($certificateRoutePrefix.'.certificates.create') }}">＋ Ajukan Sertifikat Saya</a>@endif</div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @unless ($hasMemberProfile)<div class="admin-alert" role="status">Profil akun Anda belum terhubung dengan data anggota. Hubungi Admin agar sertifikat dapat ditampilkan.</div>@endunless
    <section class="certificate-mine-grid">
        @forelse ($certificates as $certificate)
            <article class="dashboard-card certificate-mine-card"><span class="certificate-file-mark">PDF</span><div class="certificate-mine-copy"><span class="eyebrow-label">{{ $certificate->issued_at?->translatedFormat('d M Y') ?? 'SERTIFIKAT MAHAPATI' }}</span><h2>{{ $certificate->title }}</h2><p>{{ $certificate->activity?->title ?? 'Kegiatan tidak tersedia' }}</p>@if ($certificate->certificate_number)<small>No. {{ $certificate->certificate_number }}</small>@endif<div><span class="admin-badge {{ $certificate->status === 'pending' ? 'pending' : ($certificate->status === 'rejected' ? 'rejected' : '') }}">{{ ['pending' => 'Menunggu persetujuan Admin', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$certificate->status] ?? $certificate->status }}</span></div>@if ($certificate->status === 'rejected' && $certificate->rejection_reason)<p class="certificate-rejection-reason"><strong>Alasan:</strong> {{ $certificate->rejection_reason }}</p>@endif</div>@if ($certificate->status === 'approved')<a class="admin-action-button" href="{{ route($certificateRoutePrefix.'.certificates.download', $certificate) }}">↓ Unduh PDF</a>@elseif ($certificate->status === 'pending')<span class="certificate-private-note">PDF tersedia setelah disetujui.</span>@else<a class="admin-action-button" href="{{ route($certificateRoutePrefix.'.certificates.create') }}">Ajukan Ulang</a>@endif</article>
        @empty
            <div class="dashboard-card certificate-empty"><span aria-hidden="true">▧</span><strong>Belum ada sertifikat atau pengajuan</strong><p>Ajukan sertifikat milik Anda. PDF baru bisa diunduh setelah Admin menyetujuinya.</p>@if ($hasMemberProfile)<a class="admin-action-button" href="{{ route($certificateRoutePrefix.'.certificates.create') }}">Ajukan Sertifikat Saya</a>@endif</div>
        @endforelse
    </section>
    @if ($certificates->hasPages())<div class="admin-pagination">{{ $certificates->links() }}</div>@endif
@endsection
