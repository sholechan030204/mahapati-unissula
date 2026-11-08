@extends('layouts.admin')

@section('title', 'Prestasi Saya')

@section('content')
    @php($createRoute = $routePrefix === 'pengurus' ? route('pengurus.achievements.create') : route('member.achievement-submissions.create'))
    <div class="admin-content-heading"><div><span class="eyebrow-label">CATATAN PENCAPAIAN</span><h1>Prestasi</h1><p>Usulan Anda diperiksa Admin sebelum ditampilkan di halaman publik.</p></div><a class="admin-action-button" href="{{ $createRoute }}">＋ Kirim Usulan</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card achievement-submission-card"><div class="activity-list-toolbar"><div><h2>Usulan Saya</h2><p>Pantau status usulan prestasi yang telah Anda kirim.</p></div></div>
        @if ($achievements->isEmpty())<div class="activity-empty achievement-empty"><span class="activity-empty-mark">★</span><strong>Belum ada usulan prestasi</strong><p>Kirim catatan pencapaian MAHAPATI untuk ditinjau Admin.</p><a class="admin-action-button" href="{{ $createRoute }}">Kirim Usulan Prestasi</a></div>
        @else<div class="achievement-submission-list">@foreach ($achievements as $achievement)<article class="achievement-submission-row"><div><span class="achievement-status-pill {{ $achievement->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$achievement->status] ?? $achievement->status }}</span><h3>{{ $achievement->title }}</h3><p>{{ $achievement->level ?? 'Tingkat belum dicatat' }} · Dikirim {{ $achievement->created_at->translatedFormat('d M Y, H:i') }}</p>@if ($achievement->rejection_reason)<p class="achievement-rejection-note"><strong>Alasan Admin:</strong> {{ $achievement->rejection_reason }}</p>@endif</div>@if ($achievement->status === 'published')<a class="table-action-link" href="{{ route('achievements.show', $achievement->slug) }}" target="_blank" rel="noopener">Lihat publikasi →</a>@endif</article>@endforeach</div><div class="admin-pagination">{{ $achievements->links() }}</div>@endif
    </section>
@endsection
