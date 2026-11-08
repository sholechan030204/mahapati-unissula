@extends('layouts.admin')

@section('title', 'Detail Materi Galeri')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">DETAIL GALERI</span><h1>{{ $galleryItem->title }}</h1><p>Dokumentasi yang sudah diterbitkan pada galeri MAHAPATI.</p></div><a class="admin-secondary-button" href="{{ route($routePrefix.'.gallery.index') }}">← Kembali ke Galeri</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card admin-card gallery-detail-card">
        @if ($galleryItem->media_type === 'photo')<img class="gallery-detail-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($galleryItem->photo_path) }}" alt="{{ $galleryItem->title }}">@else<a class="gallery-detail-youtube" href="{{ $galleryItem->youtube_url }}" target="_blank" rel="noopener noreferrer"><img src="{{ $galleryItem->youtubeThumbnailUrl() ?? asset('images/hero-mahapat.jpg') }}" alt="Pratinjau video {{ $galleryItem->title }}"><span>▶ Tonton di YouTube</span></a>@endif
        <div class="gallery-detail-copy"><div class="gallery-card-meta"><span class="gallery-type-pill {{ $galleryItem->media_type }}">{{ $galleryItem->media_type === 'photo' ? 'FOTO' : 'YOUTUBE' }}</span><span>{{ $galleryItem->activity_date?->translatedFormat('d F Y') ?? 'Tanggal tidak dicatat' }}</span></div><h2>{{ $galleryItem->title }}</h2><p>{{ $galleryItem->caption ?: 'Tidak ada keterangan untuk materi ini.' }}</p><small>Ditambahkan oleh {{ $galleryItem->createdBy?->name ?? 'Admin' }}</small></div>
        @unless ($isReadOnly)
            <div class="admin-button-row gallery-detail-actions"><a class="admin-action-button" href="{{ route($routePrefix.'.gallery.edit', $galleryItem) }}">{{ $isProposal ? 'Usulkan Edit' : 'Edit Materi' }}</a><form method="POST" action="{{ route($routePrefix.'.gallery.destroy', $galleryItem) }}" onsubmit="return confirm('{{ $isProposal ? 'Kirim usulan penghapusan materi kepada Admin?' : 'Hapus materi galeri ini?' }}')">@csrf @method('DELETE')<button class="admin-delete-button" type="submit">{{ $isProposal ? 'Usulkan Hapus' : 'Hapus Materi' }}</button></form></div>
        @endunless
    </section>
@endsection
