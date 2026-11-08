@extends('layouts.admin')

@section('title', 'Prestasi MAHAPATI')

@section('content')
    <div class="admin-content-heading achievement-admin-heading"><div><span class="eyebrow-label">CATATAN PENCAPAIAN</span><h1>Prestasi</h1><p>Kelola pencapaian MAHAPATI dan tinjau usulan sebelum diterbitkan ke website publik.</p></div><a class="admin-action-button" href="{{ route('admin.achievements.create') }}">＋ Tambah Prestasi</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="activity-summary-grid achievement-summary-grid" aria-label="Ringkasan prestasi">
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon green">★</span><div><strong>{{ number_format($publishedCount) }}</strong><span>Terbit</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon blue">✎</span><div><strong>{{ number_format($draftCount) }}</strong><span>Draft Admin</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon amber">◷</span><div><strong>{{ number_format($pendingCount) }}</strong><span>Menunggu Persetujuan</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon slate">↩</span><div><strong>{{ number_format($rejectedCount) }}</strong><span>Ditolak</span></div></article>
    </section>
    <section class="dashboard-card achievement-management-card">
        <div class="activity-list-toolbar"><div><h2>Daftar Prestasi</h2><p>Hanya prestasi yang diterbitkan Admin tampil di website publik.</p></div>
            <form class="activity-filters achievement-filters" method="GET" action="{{ route('admin.achievements.index') }}"><input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari prestasi atau penyelenggara" aria-label="Cari prestasi"><select class="admin-select" name="status" aria-label="Filter status"><option value="">Semua status</option><option value="published" @selected(($filters['status'] ?? '') === 'published')>Terbit</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option><option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Menunggu persetujuan</option><option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Ditolak</option></select><button class="admin-action-button" type="submit">Filter</button></form>
        </div>
        @if ($achievements->isEmpty())
            <div class="activity-empty achievement-empty"><span class="activity-empty-mark">★</span><strong>Belum ada catatan prestasi</strong><p>Tambahkan prestasi untuk menampilkan perjalanan pencapaian MAHAPATI kepada publik.</p><a class="admin-action-button" href="{{ route('admin.achievements.create') }}">Tambah Prestasi</a></div>
        @else
            <div class="table-scroll"><table class="admin-table achievement-admin-table"><thead><tr><th>Prestasi</th><th>Tingkat</th><th>Tanggal</th><th>Status</th><th>Dikirim oleh</th><th>Aksi</th></tr></thead><tbody>
                @foreach ($achievements as $achievement)
                    <tr><td><div class="achievement-admin-title-cell">@if ($achievement->image_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($achievement->image_path) }}" alt="">@else<span class="achievement-admin-thumb-placeholder">★</span>@endif<div><strong>{{ $achievement->title }}</strong><small class="table-secondary">{{ \Illuminate\Support\Str::limit($achievement->summary, 88) }}</small></div></div></td><td>{{ $achievement->level ?? '—' }}</td><td>{{ $achievement->achievement_date?->translatedFormat('d M Y') ?? '—' }}</td><td><span class="achievement-status-pill {{ $achievement->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$achievement->status] ?? $achievement->status }}</span></td><td>{{ $achievement->createdBy?->name ?? 'Admin' }}</td><td><a class="table-action-link" href="{{ route('admin.achievements.show', $achievement) }}">Detail →</a></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="admin-pagination">{{ $achievements->links() }}</div>
        @endif
    </section>
@endsection
