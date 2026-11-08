@extends('layouts.admin')

@section('title', 'Berita dan Publikasi')

@section('content')
    <div class="admin-content-heading news-admin-heading">
        <div><span class="eyebrow-label">PUBLIKASI MAHAPATI</span><h1>Berita dan Publikasi</h1><p>Kelola artikel yang diterbitkan ke website publik dan tinjau kiriman dari Pengurus serta Anggota.</p></div>
        <a class="admin-action-button" href="{{ route('admin.news.create') }}">＋ Tambah Berita</a>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif

    <section class="activity-summary-grid news-summary-grid" aria-label="Ringkasan berita">
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon green">▤</span><div><strong>{{ number_format($publishedCount) }}</strong><span>Terbit</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon blue">✎</span><div><strong>{{ number_format($draftCount) }}</strong><span>Draft Admin</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon amber">◷</span><div><strong>{{ number_format($pendingCount) }}</strong><span>Menunggu Persetujuan</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon slate">↩</span><div><strong>{{ number_format($rejectedCount) }}</strong><span>Ditolak</span></div></article>
    </section>

    <section class="dashboard-card news-management-card">
        <div class="activity-list-toolbar">
            <div><h2>Daftar Artikel</h2><p>Konten kiriman hanya tampil di website publik setelah disetujui Admin.</p></div>
            <form class="activity-filters news-filters" method="GET" action="{{ route('admin.news.index') }}">
                <input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari judul atau kategori" aria-label="Cari berita">
                <select class="admin-select" name="status" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="published" @selected(($filters['status'] ?? '') === 'published')>Terbit</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Menunggu persetujuan</option>
                    <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Ditolak</option>
                </select>
                <button class="admin-action-button" type="submit">Filter</button>
                @if (($filters['q'] ?? '') !== '' || ($filters['status'] ?? '') !== '')<a class="admin-secondary-button" href="{{ route('admin.news.index') }}">Reset</a>@endif
            </form>
        </div>
        @if ($articles->isEmpty())
            <div class="activity-empty news-empty"><span class="activity-empty-mark">▤</span><strong>Belum ada artikel</strong><p>Tambahkan berita atau publikasi pertama MAHAPATI. Foto sampul boleh disertakan atau dikosongkan.</p><a class="admin-action-button" href="{{ route('admin.news.create') }}">Tambah Berita</a></div>
        @else
            <div class="table-scroll"><table class="admin-table news-admin-table">
                <thead><tr><th>Artikel</th><th>Kategori</th><th>Status</th><th>Dikirim oleh</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr>
                            <td><div class="news-admin-title-cell">@if ($article->cover_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="">@else<span class="news-admin-thumb-placeholder">▤</span>@endif<div><strong>{{ $article->title }}</strong><small class="table-secondary">{{ \Illuminate\Support\Str::limit($article->excerpt, 90) }}</small></div></div></td>
                            <td>{{ $article->category }}</td>
                            <td><span class="news-status-pill {{ $article->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$article->status] ?? $article->status }}</span></td>
                            <td>{{ $article->createdBy?->name ?? 'Admin' }}</td>
                            <td>{{ ($article->published_at ?? $article->created_at)->translatedFormat('d M Y') }}</td>
                            <td><a class="table-action-link" href="{{ route('admin.news.show', $article) }}">Detail →</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
            <div class="admin-pagination">{{ $articles->links() }}</div>
        @endif
    </section>
@endsection
