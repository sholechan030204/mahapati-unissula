@extends('layouts.admin')

@section('title', 'Berita Saya')

@section('content')
    @php($createRoute = $routePrefix === 'pengurus' ? route('pengurus.news.create') : route('member.news-submissions.create'))
    <div class="admin-content-heading"><div><span class="eyebrow-label">PUBLIKASI MAHAPATI</span><h1>Berita dan Publikasi</h1><p>Kiriman Anda akan diperiksa Admin sebelum tampil di website publik.</p></div><a class="admin-action-button" href="{{ $createRoute }}">＋ Kirim Berita</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card news-submission-card">
        <div class="activity-list-toolbar"><div><h2>Kiriman Saya</h2><p>Status artikel yang sudah Anda kirimkan.</p></div></div>
        @if ($articles->isEmpty())
            <div class="activity-empty news-empty"><span class="activity-empty-mark">▤</span><strong>Belum ada berita yang dikirim</strong><p>Tulis berita atau publikasi MAHAPATI untuk ditinjau Admin.</p><a class="admin-action-button" href="{{ $createRoute }}">Kirim Berita</a></div>
        @else
            <div class="news-submission-list">
                @foreach ($articles as $article)
                    <article class="news-submission-row"><div><span class="news-status-pill {{ $article->status }}">{{ ['pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak', 'draft' => 'Draft'][$article->status] ?? $article->status }}</span><h3>{{ $article->title }}</h3><p>{{ $article->category }} · Dikirim {{ $article->created_at->translatedFormat('d M Y, H:i') }}</p>@if ($article->rejection_reason)<p class="news-rejection-note"><strong>Alasan Admin:</strong> {{ $article->rejection_reason }}</p>@endif</div>@if ($article->status === 'published')<a class="table-action-link" href="{{ route('news.show', $article->slug) }}" target="_blank" rel="noopener">Lihat publikasi →</a>@endif</article>
                @endforeach
            </div>
            <div class="admin-pagination">{{ $articles->links() }}</div>
        @endif
    </section>
@endsection
