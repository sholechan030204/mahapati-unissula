@extends('layouts.admin')

@section('title', 'Detail Berita')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">DETAIL PUBLIKASI</span><h1>{{ $article->title }}</h1><p>Periksa isi artikel, status penerbitan, dan riwayat peninjauan.</p></div><a class="admin-secondary-button" href="{{ route('admin.news.index') }}">← Kembali ke Berita</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card admin-card news-detail-card">
        <div class="news-detail-heading"><div><span class="news-status-pill {{ $article->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu persetujuan', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$article->status] ?? $article->status }}</span><h2>{{ $article->title }}</h2><p>{{ $article->category }} · Ditulis oleh {{ $article->createdBy?->name ?? 'Admin' }}</p></div>@if ($article->published_at)<time>{{ $article->published_at->translatedFormat('d F Y, H:i') }}</time>@endif</div>
        @if ($article->cover_image)<img class="news-detail-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="Sampul {{ $article->title }}">@endif
        <p class="news-detail-excerpt">{{ $article->excerpt }}</p>
        <div class="news-detail-body">{!! nl2br(e($article->body)) !!}</div>
        @if ($article->rejection_reason)<div class="news-rejection-box"><strong>Alasan penolakan</strong><p>{{ $article->rejection_reason }}</p></div>@endif
        @if ($article->reviewedBy)<p class="news-review-meta">Ditinjau oleh {{ $article->reviewedBy->name }}{{ $article->reviewed_at ? ' · '.$article->reviewed_at->translatedFormat('d M Y, H:i') : '' }}</p>@endif
        <div class="admin-button-row news-detail-actions">
            <a class="admin-secondary-button" href="{{ route('admin.news.edit', $article) }}">Edit Artikel</a>
            @if ($article->status === 'pending')
                <form method="POST" action="{{ route('admin.news.approve', $article) }}" onsubmit="return confirm('Setujui dan terbitkan artikel ini ke website publik?')">@csrf<button class="admin-action-button" type="submit">Setujui dan Terbitkan</button></form>
            @endif
            <form method="POST" action="{{ route('admin.news.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini secara permanen?')">@csrf @method('DELETE')<button class="cash-delete-button" type="submit">Hapus Berita</button></form>
        </div>
        @if ($article->status === 'pending')
            <form class="news-reject-form" method="POST" action="{{ route('admin.news.reject', $article) }}">@csrf<div class="admin-field"><label for="rejection_reason">Alasan penolakan jika artikel tidak dapat diterbitkan *</label><textarea class="admin-textarea" id="rejection_reason" name="rejection_reason" minlength="3" maxlength="2000" required placeholder="Jelaskan hal yang perlu diperbaiki."></textarea>@error('rejection_reason')<span class="admin-error">{{ $message }}</span>@enderror</div><button class="cash-delete-button" type="submit">Tolak dengan Alasan</button></form>
        @endif
    </section>
@endsection
