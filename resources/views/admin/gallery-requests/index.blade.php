@extends('layouts.admin')

@section('title', 'Persetujuan Galeri')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">TINJAU KONTEN</span><h1>Persetujuan Kiriman Galeri</h1><p>Periksa kiriman foto dan video Pengurus atau Anggota sebelum diterbitkan.</p></div><a class="admin-secondary-button" href="{{ route('admin.gallery.index') }}">← Kembali ke Galeri</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($requests->isEmpty())
        <section class="dashboard-card activity-empty gallery-empty"><span class="activity-empty-mark">✓</span><strong>Tidak ada kiriman untuk ditinjau</strong><p>Kiriman baru dari Pengurus atau Anggota akan muncul di sini.</p></section>
    @else
        <div class="gallery-review-list">
            @foreach ($requests as $galleryChangeRequest)
                @php
                    $payload = $galleryChangeRequest->payload ?? [];
                    $actionLabels = ['create' => 'Materi baru', 'update' => 'Usulan perubahan', 'delete' => 'Usulan penghapusan'];
                    $mediaLabels = ['photo' => 'Foto', 'youtube' => 'Video YouTube'];
                @endphp
                <article class="dashboard-card gallery-review-card">
                    <div class="gallery-review-header"><div><span class="eyebrow-label">{{ $actionLabels[$galleryChangeRequest->action] ?? 'Usulan' }}</span><h2>{{ $payload['title'] ?? $galleryChangeRequest->galleryItem?->title ?? 'Materi galeri' }}</h2><p>Dikirim {{ $galleryChangeRequest->created_at->translatedFormat('d F Y, H:i') }} oleh <strong>{{ $galleryChangeRequest->proposedBy?->name ?? 'Pengguna' }}</strong></p></div><span class="activity-status-pill {{ $galleryChangeRequest->status === 'pending' ? 'planned' : ($galleryChangeRequest->status === 'approved' ? 'completed' : 'ongoing') }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$galleryChangeRequest->status] ?? $galleryChangeRequest->status }}</span></div>
                    <div class="gallery-review-content">
                        @if ($galleryChangeRequest->action === 'delete')
                            <p>Pengusul meminta menghapus materi <strong>{{ $galleryChangeRequest->galleryItem?->title ?? 'yang sudah tidak tersedia' }}</strong>.</p>
                        @else
                            @if (($payload['media_type'] ?? null) === 'photo' && ($payload['photo_path'] ?? null))<img class="gallery-review-image" src="{{ route('admin.gallery-requests.photo', $galleryChangeRequest) }}" alt="Foto usulan {{ $payload['title'] ?? '' }}">@elseif (($payload['media_type'] ?? null) === 'youtube')<a class="gallery-review-youtube" href="{{ $payload['youtube_url'] }}" target="_blank" rel="noopener noreferrer">Buka tautan YouTube yang diusulkan ↗</a>@endif
                            <dl class="approval-data-grid gallery-review-facts"><div><dt>Jenis media</dt><dd>{{ $mediaLabels[$payload['media_type'] ?? ''] ?? '—' }}</dd></div><div><dt>Tanggal kegiatan</dt><dd>{{ $payload['activity_date'] ?? 'Tidak dicatat' }}</dd></div><div><dt>Keterangan</dt><dd>{{ $payload['caption'] ?: 'Tidak ada keterangan' }}</dd></div></dl>
                        @endif
                    </div>
                    @if ($galleryChangeRequest->status === 'pending')
                        <div class="gallery-review-actions"><form method="POST" action="{{ route('admin.gallery-requests.approve', $galleryChangeRequest) }}">@csrf<button class="admin-action-button" type="submit" onclick="return confirm('Setujui kiriman ini dan terapkan perubahannya?')">Setujui dan Terbitkan</button></form><form class="approval-reject-form" method="POST" action="{{ route('admin.gallery-requests.reject', $galleryChangeRequest) }}">@csrf<label for="reason-{{ $galleryChangeRequest->id }}">Alasan penolakan (wajib jika ditolak)</label><textarea class="admin-textarea" id="reason-{{ $galleryChangeRequest->id }}" name="rejection_reason" required minlength="3" maxlength="2000" placeholder="Jelaskan alasan penolakan agar pengusul dapat memperbaiki kiriman."></textarea><button class="admin-delete-button" type="submit" onclick="return confirm('Tolak kiriman ini? Alasan penolakan akan tercatat.')">Tolak Kiriman</button></form></div>
                    @else
                        <p class="gallery-review-outcome">{{ $galleryChangeRequest->status === 'rejected' ? 'Alasan penolakan: '.($galleryChangeRequest->rejection_reason ?? '—') : 'Disetujui oleh '.($galleryChangeRequest->reviewedBy?->name ?? 'Admin') }}</p>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="admin-pagination">{{ $requests->links() }}</div>
    @endif
@endsection
