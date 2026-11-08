@extends('layouts.admin')

@section('title', 'Galeri MAHAPATI')

@section('content')
    @php($routePrefix = $isAdmin ? 'admin' : 'pengurus')
    <div class="admin-content-heading gallery-admin-heading">
        <div><span class="eyebrow-label">DOKUMENTASI MAHAPATI</span><h1>Galeri</h1><p>Kelola foto kegiatan dan tautan video YouTube yang sudah disetujui.</p></div>
        <div class="admin-button-row">
            @if ($isAdmin)<a class="admin-secondary-button" href="{{ route('admin.gallery-requests.index') }}">Persetujuan Kiriman @if ($pendingRequestCount > 0)<span class="button-count">{{ $pendingRequestCount }}</span>@endif</a>@endif
            @if ($isPengurus)<a class="admin-secondary-button" href="{{ route('pengurus.gallery.create') }}">＋ Usulkan Materi</a>@else<a class="admin-action-button" href="{{ route('admin.gallery.create') }}">＋ Tambah Materi</a>@endif
        </div>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif

    <section class="activity-summary-grid gallery-summary-grid" aria-label="Ringkasan galeri">
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon blue">▧</span><div><strong>{{ number_format($photoCount) }}</strong><span>Foto Terbit</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon gold">▶</span><div><strong>{{ number_format($videoCount) }}</strong><span>Video YouTube</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon amber">◷</span><div><strong>{{ number_format($pendingRequestCount) }}</strong><span>{{ $isAdmin ? 'Menunggu Persetujuan' : 'Usulan Saya Menunggu' }}</span></div></article>
    </section>

    <section class="dashboard-card gallery-management-card">
        <div class="activity-list-toolbar">
            <div><h2>Materi Galeri</h2><p>Hanya materi yang diterbitkan Admin atau telah disetujui yang tampil di website publik.</p></div>
            <form class="activity-filters gallery-filters" method="GET" action="{{ route($routePrefix.'.gallery.index') }}">
                <input class="admin-input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul atau keterangan" aria-label="Cari materi galeri">
                <select class="admin-select" name="type" aria-label="Filter tipe materi"><option value="">Semua media</option><option value="photo" @selected(request('type') === 'photo')>Foto</option><option value="youtube" @selected(request('type') === 'youtube')>YouTube</option></select>
                <button class="admin-action-button" type="submit">Filter</button>
            </form>
        </div>
        @if ($items->isEmpty())
            <div class="activity-empty gallery-empty"><span class="activity-empty-mark">▧</span><strong>Belum ada materi galeri</strong><p>Tambahkan foto dokumentasi atau tautan YouTube. Materi yang disetujui akan muncul di halaman Galeri publik.</p>@if ($isAdmin)<a class="admin-action-button" href="{{ route('admin.gallery.create') }}">Tambah Materi Galeri</a>@else<a class="admin-action-button" href="{{ route('pengurus.gallery.create') }}">Usulkan Materi</a>@endif</div>
        @else
            <div class="gallery-admin-grid">
                @foreach ($items as $item)
                    <article class="gallery-admin-card">
                        <a class="gallery-admin-cover" href="{{ route($routePrefix.'.gallery.show', $item) }}">
                            @if ($item->media_type === 'photo')<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" alt="{{ $item->title }}" loading="lazy">@else<img src="{{ $item->youtubeThumbnailUrl() ?? asset('images/hero-mahapat.jpg') }}" alt="Pratinjau video {{ $item->title }}" loading="lazy"><span class="gallery-play-mark" aria-hidden="true">▶</span>@endif
                        </a>
                        <div class="gallery-admin-card-copy"><div class="gallery-card-meta"><span class="gallery-type-pill {{ $item->media_type }}">{{ $item->media_type === 'photo' ? 'FOTO' : 'YOUTUBE' }}</span><span>{{ $item->activity_date?->translatedFormat('d M Y') ?? 'Tanggal tidak dicatat' }}</span></div><h3><a href="{{ route($routePrefix.'.gallery.show', $item) }}">{{ $item->title }}</a></h3><p>{{ \Illuminate\Support\Str::limit($item->caption, 105) }}</p><small>Ditambahkan oleh {{ $item->createdBy?->name ?? 'Admin' }}</small><a class="table-action-link" href="{{ route($routePrefix.'.gallery.show', $item) }}">Lihat detail →</a></div>
                    </article>
                @endforeach
            </div>
            <div class="admin-pagination">{{ $items->links() }}</div>
        @endif
    </section>
    @if ($isPengurus && $myRequests->isNotEmpty())
        <section class="dashboard-card gallery-submissions-card gallery-manager-submissions">
            <div class="activity-list-toolbar"><div><h2>Status Usulan Saya</h2><p>Admin akan meninjau setiap usulan sebelum menerapkan perubahannya.</p></div></div>
            <div class="gallery-submission-list">
                @foreach ($myRequests as $submission)
                    @php($payload = $submission->payload ?? [])
                    <article class="gallery-submission-row"><span class="gallery-type-pill {{ ($payload['media_type'] ?? 'photo') }}">{{ ($payload['media_type'] ?? null) === 'youtube' ? 'YOUTUBE' : 'GALERI' }}</span><div><h3>{{ $payload['title'] ?? $submission->galleryItem?->title ?? 'Usulan penghapusan' }}</h3><p>Dikirim {{ $submission->created_at->translatedFormat('d M Y, H:i') }}</p>@if ($submission->rejection_reason)<p class="gallery-rejection-reason"><strong>Alasan Admin:</strong> {{ $submission->rejection_reason }}</p>@endif</div><span class="activity-status-pill {{ $submission->status === 'pending' ? 'planned' : ($submission->status === 'approved' ? 'completed' : 'ongoing') }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$submission->status] ?? $submission->status }}</span></article>
                @endforeach
            </div>
        </section>
    @endif
    @if ($isPengurus)<p class="activity-permission-note"><span>ⓘ</span> Usulan Pengurus baru diterbitkan setelah Admin menyetujui. Video tetap ditonton melalui YouTube agar penyimpanan website tidak cepat penuh.</p>@endif
@endsection
