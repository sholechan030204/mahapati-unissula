@extends('layouts.admin')

@section('title', 'Kirim Konten Galeri')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">KONTEN MAHAPATI</span><h1>Kirim Konten</h1><p>Kirim berita, prestasi, atau materi galeri. Admin akan memeriksa sebelum konten muncul di website publik.</p></div><div class="admin-button-row"><a class="admin-secondary-button" href="{{ route('member.news-submissions.create') }}">＋ Kirim Berita</a><a class="admin-secondary-button" href="{{ route('member.achievement-submissions.create') }}">＋ Kirim Prestasi</a><a class="admin-action-button" href="{{ route('member.gallery-submissions.create') }}">＋ Kirim Materi Galeri</a></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card news-submission-card">
        <div class="activity-list-toolbar"><div><h2>Kiriman Berita Saya</h2><p>Artikel baru tampil ke publik setelah disetujui Admin.</p></div><a class="table-action-link" href="{{ route('member.news-submissions.create') }}">Kirim berita →</a></div>
        @if ($articles->isEmpty())
            <div class="news-submission-compact-empty">Belum ada berita yang Anda kirim.</div>
        @else
            <div class="news-submission-list">
                @foreach ($articles as $article)
                    <article class="news-submission-row"><div><span class="news-status-pill {{ $article->status }}">{{ ['pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak', 'draft' => 'Draft'][$article->status] ?? $article->status }}</span><h3>{{ $article->title }}</h3><p>{{ $article->category }} · Dikirim {{ $article->created_at->translatedFormat('d M Y, H:i') }}</p>@if ($article->rejection_reason)<p class="news-rejection-note"><strong>Alasan Admin:</strong> {{ $article->rejection_reason }}</p>@endif</div>@if ($article->status === 'published')<a class="table-action-link" href="{{ route('news.show', $article->slug) }}" target="_blank" rel="noopener">Lihat publikasi →</a>@endif</article>
                @endforeach
            </div>
            <div class="admin-pagination">{{ $articles->links() }}</div>
        @endif
    </section>
    <section class="dashboard-card achievement-submission-card">
        <div class="activity-list-toolbar"><div><h2>Kiriman Prestasi Saya</h2><p>Prestasi hanya tampil di website publik setelah disetujui Admin.</p></div><a class="table-action-link" href="{{ route('member.achievement-submissions.index') }}">Lihat semua →</a></div>
        @if ($achievements->isEmpty())
            <div class="news-submission-compact-empty">Belum ada prestasi yang Anda kirim.</div>
        @else
            <div class="achievement-submission-list">
                @foreach ($achievements as $achievement)
                    <article class="achievement-submission-row"><div><span class="achievement-status-pill {{ $achievement->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$achievement->status] ?? $achievement->status }}</span><h3>{{ $achievement->title }}</h3><p>{{ $achievement->level ?? 'Tingkat belum dicatat' }} · Dikirim {{ $achievement->created_at->translatedFormat('d M Y, H:i') }}</p>@if ($achievement->rejection_reason)<p class="achievement-rejection-note"><strong>Alasan Admin:</strong> {{ $achievement->rejection_reason }}</p>@endif</div>@if ($achievement->status === 'published')<a class="table-action-link" href="{{ route('achievements.show', $achievement->slug) }}" target="_blank" rel="noopener">Lihat publikasi →</a>@endif</article>
                @endforeach
            </div>
            <div class="admin-pagination">{{ $achievements->links() }}</div>
        @endif
    </section>
    <section class="dashboard-card gallery-submissions-card">
        <div class="activity-list-toolbar"><div><h2>Kiriman Saya</h2><p>Lihat status kiriman yang sudah Anda ajukan.</p></div></div>
        @if ($submissions->isEmpty())
            <div class="activity-empty gallery-empty"><span class="activity-empty-mark">▧</span><strong>Belum ada konten yang dikirim</strong><p>Gunakan tombol di atas untuk mengirim dokumentasi foto atau tautan YouTube.</p><a class="admin-action-button" href="{{ route('member.gallery-submissions.create') }}">Kirim Konten</a></div>
        @else
            <div class="gallery-submission-list">
                @foreach ($submissions as $submission)
                    @php($payload = $submission->payload ?? [])
                    <article class="gallery-submission-row"><span class="gallery-type-pill {{ ($payload['media_type'] ?? 'photo') }}">{{ ($payload['media_type'] ?? null) === 'youtube' ? 'YOUTUBE' : 'FOTO' }}</span><div><h3>{{ $payload['title'] ?? 'Kiriman galeri' }}</h3><p>{{ $payload['caption'] ?? 'Tidak ada keterangan.' }}</p><small>Dikirim {{ $submission->created_at->translatedFormat('d M Y, H:i') }}</small>@if ($submission->rejection_reason)<p class="gallery-rejection-reason"><strong>Alasan Admin:</strong> {{ $submission->rejection_reason }}</p>@endif</div><span class="activity-status-pill {{ $submission->status === 'pending' ? 'planned' : ($submission->status === 'approved' ? 'completed' : 'ongoing') }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$submission->status] ?? $submission->status }}</span></article>
                @endforeach
            </div>
            <div class="admin-pagination">{{ $submissions->links() }}</div>
        @endif
    </section>
@endsection
