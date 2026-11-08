@extends('layouts.admin')

@section('title', 'Detail Prestasi')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">DETAIL PENCAPAIAN</span><h1>{{ $achievement->title }}</h1><p>Periksa detail, status publikasi, dan riwayat peninjauan prestasi.</p></div><a class="admin-secondary-button" href="{{ route('admin.achievements.index') }}">← Kembali ke Prestasi</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card admin-card achievement-detail-card">
        <div class="achievement-detail-heading"><div><span class="achievement-status-pill {{ $achievement->status }}">{{ ['draft' => 'Draft', 'pending' => 'Menunggu persetujuan', 'published' => 'Terbit', 'rejected' => 'Ditolak'][$achievement->status] ?? $achievement->status }}</span><h2>{{ $achievement->title }}</h2><p>{{ $achievement->level ?? 'Tingkat tidak dicatat' }}@if ($achievement->achievement_date) · {{ $achievement->achievement_date->translatedFormat('d F Y') }}@endif</p></div>@if ($achievement->published_at)<time>Diterbitkan {{ $achievement->published_at->translatedFormat('d M Y, H:i') }}</time>@endif</div>
        @if ($achievement->image_path)<img class="achievement-detail-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($achievement->image_path) }}" alt="Dokumentasi {{ $achievement->title }}">@endif
        @if ($achievement->organizer)<p class="achievement-detail-organizer"><strong>Penyelenggara:</strong> {{ $achievement->organizer }}</p>@endif
        <p class="achievement-detail-summary">{{ $achievement->summary }}</p><div class="achievement-detail-description">{!! nl2br(e($achievement->description)) !!}</div>
        @if ($achievement->rejection_reason)<div class="achievement-rejection-box"><strong>Alasan penolakan</strong><p>{{ $achievement->rejection_reason }}</p></div>@endif
        @if ($achievement->reviewedBy)<p class="achievement-review-meta">Ditinjau oleh {{ $achievement->reviewedBy->name }}{{ $achievement->reviewed_at ? ' · '.$achievement->reviewed_at->translatedFormat('d M Y, H:i') : '' }}</p>@endif
        <div class="admin-button-row achievement-detail-actions"><a class="admin-secondary-button" href="{{ route('admin.achievements.edit', $achievement) }}">Edit Prestasi</a>@if ($achievement->status === 'pending')<form method="POST" action="{{ route('admin.achievements.approve', $achievement) }}" onsubmit="return confirm('Setujui dan terbitkan prestasi ini ke website publik?')">@csrf<button class="admin-action-button" type="submit">Setujui dan Terbitkan</button></form>@endif<form method="POST" action="{{ route('admin.achievements.destroy', $achievement) }}" onsubmit="return confirm('Hapus catatan prestasi ini secara permanen?')">@csrf @method('DELETE')<button class="cash-delete-button" type="submit">Hapus Prestasi</button></form></div>
        @if ($achievement->status === 'pending')<form class="achievement-reject-form" method="POST" action="{{ route('admin.achievements.reject', $achievement) }}">@csrf<div class="admin-field"><label for="rejection_reason">Alasan penolakan *</label><textarea class="admin-textarea" id="rejection_reason" name="rejection_reason" minlength="3" maxlength="2000" required placeholder="Jelaskan alasan penolakan dengan jelas."></textarea>@error('rejection_reason')<span class="admin-error">{{ $message }}</span>@enderror</div><button class="cash-delete-button" type="submit">Tolak dengan Alasan</button></form>@endif
    </section>
@endsection
