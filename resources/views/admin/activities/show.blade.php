@extends('layouts.admin')

@section('title', 'Detail Kegiatan')

@section('content')
    @php($statusLabels = ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'])
    <div class="admin-content-heading"><div><span class="eyebrow-label">DETAIL KEGIATAN</span><h1>{{ $activity->title }}</h1><p>Informasi jadwal, lokasi, program kerja, dan anggota yang mengikuti.</p></div><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'activities']) }}">← Kembali ke Kegiatan</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif

    <section class="dashboard-card admin-card activity-detail-card">
        @if ($activity->cover_image)<img class="activity-detail-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activity->cover_image) }}" alt="Sampul {{ $activity->title }}">@endif
        <div class="activity-detail-heading"><div><span class="eyebrow-label">{{ $activity->workProgram?->title ?? 'KEGIATAN MAHAPATI' }}</span><h2>{{ $activity->title }}</h2></div><span class="activity-status-pill {{ $activity->status }}">{{ $statusLabels[$activity->status] ?? $activity->status }}</span></div>
        <div class="activity-detail-facts">
            <div><span>Jadwal rencana</span><strong>{{ $activity->planned_at->translatedFormat('l, d F Y · H:i') }} WIB</strong></div>
            <div><span>Realisasi</span><strong>{{ $activity->realized_at?->translatedFormat('l, d F Y · H:i') ?? 'Belum tercatat' }}@if ($activity->realized_at) WIB @endif</strong></div>
            <div><span>Lokasi</span><strong>{{ $activity->location }}</strong></div>
            <div><span>Jumlah peserta</span><strong>{{ $activity->participants->count() }} anggota</strong></div>
        </div>
        <div class="activity-description-block"><h3>Deskripsi kegiatan</h3><p>{{ $activity->description }}</p></div>
        @unless ($isReadOnly)
            <div class="admin-button-row">
                <a class="admin-action-button" href="{{ route($routePrefix.'.activities.edit', $activity) }}">{{ $isProposal ? 'Usulkan Edit' : 'Edit Kegiatan' }}</a>
                <form method="POST" action="{{ route($routePrefix.'.activities.destroy', $activity) }}" onsubmit="return confirm('{{ $isProposal ? 'Kirim usulan penghapusan kegiatan kepada Admin?' : 'Hapus kegiatan ini?' }}')">
                    @csrf @method('DELETE')
                    <button class="admin-delete-button" type="submit">{{ $isProposal ? 'Usulkan Hapus' : 'Hapus Kegiatan' }}</button>
                </form>
            </div>
        @endunless
    </section>

    <section class="dashboard-card admin-card participants-card">
        <div class="panel-heading"><div><h2>Anggota yang Mengikuti</h2><p class="panel-subtitle">Daftar peserta yang dicatat untuk kegiatan ini.</p></div><span class="participant-total">{{ $activity->participants->count() }} peserta</span></div>
        @if ($activity->participants->isEmpty())
            <div class="admin-empty">Belum ada anggota yang ditambahkan sebagai peserta kegiatan.</div>
        @else
            <div class="participant-directory">@foreach ($activity->participants as $member)<article class="participant-directory-item"><span class="participant-avatar">{{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}</span><span><strong>{{ $member->full_name }}</strong><small>{{ $member->alam_name ?: $member->nra }} · {{ $member->membership_type === 'anggota_istimewa' ? 'Anggota Istimewa' : 'Anggota Tetap' }}</small></span></article>@endforeach</div>
        @endif
    </section>
@endsection
