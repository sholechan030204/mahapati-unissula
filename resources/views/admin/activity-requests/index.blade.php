@extends('layouts.admin')

@section('title', 'Persetujuan Kegiatan')

@section('content')
    @php
        $actionLabels = ['create' => 'Tambah', 'update' => 'Ubah', 'delete' => 'Hapus'];
        $statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        $fieldLabels = ['title' => 'Nama', 'description' => 'Deskripsi', 'planned_start_date' => 'Mulai rencana', 'planned_end_date' => 'Selesai rencana', 'actual_start_date' => 'Mulai realisasi', 'actual_end_date' => 'Selesai realisasi', 'location' => 'Lokasi', 'planned_at' => 'Jadwal rencana', 'realized_at' => 'Realisasi', 'status' => 'Status', 'work_program_id' => 'Program kerja', 'participant_ids' => 'Peserta', 'cover_image' => 'Foto sampul'];
    @endphp
    <div class="admin-content-heading"><div><span class="eyebrow-label">TINJAUAN ADMIN</span><h1>Persetujuan Program &amp; Kegiatan</h1><p>Tinjau usulan Pengurus sebelum perubahan diterapkan pada data resmi.</p></div><a class="admin-secondary-button" href="{{ route('admin.activities.index') }}">← Kembali ke Kegiatan</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif

    @forelse ($requests as $changeRequest)
        <article class="dashboard-card admin-card approval-card">
            <div class="approval-card-heading">
                <div><span class="approval-type">{{ $changeRequest->subject_type === 'program' ? 'PROGRAM KERJA' : 'KEGIATAN' }}</span><h2>{{ $actionLabels[$changeRequest->action] ?? ucfirst($changeRequest->action) }} · {{ $changeRequest->payload['title'] ?? ($changeRequest->subject_type === 'program' ? 'Program kerja' : 'Kegiatan') }}</h2><p>Diajukan oleh {{ $changeRequest->proposedBy?->name ?? 'Pengurus' }} · {{ $changeRequest->created_at->translatedFormat('d F Y, H:i') }}</p></div>
                <span class="admin-badge {{ $changeRequest->status }}">{{ $statusLabels[$changeRequest->status] ?? $changeRequest->status }}</span>
            </div>
            @if ($changeRequest->note)<p class="approval-note"><strong>Catatan Pengurus:</strong> {{ $changeRequest->note }}</p>@endif
            @if (in_array($changeRequest->action, ['create', 'update'], true) && $changeRequest->payload)
                <dl class="approval-data-grid">
                    @foreach ($fieldLabels as $field => $label)
                        @if (array_key_exists($field, $changeRequest->payload))
                            @php($value = $changeRequest->payload[$field])
                            @if ($field === 'participant_ids')
                                @php($value = count($value).' anggota dipilih')
                            @elseif ($field === 'status')
                                @php($value = ['planned' => 'Belum terlaksana', 'ongoing' => 'Sedang terlaksana', 'completed' => 'Sudah terlaksana'][$value] ?? $value)
                            @elseif ($field === 'work_program_id')
                                @php($value = $value ? \App\Models\WorkProgram::query()->find($value)?->title : 'Tidak dikaitkan')
                            @endif
                            <div><dt>{{ $label }}</dt><dd>{{ is_scalar($value) ? ($value ?: '—') : '—' }}</dd></div>
                        @endif
                    @endforeach
                </dl>
            @elseif ($changeRequest->action === 'delete')
                <p class="approval-note">Pengurus mengusulkan penghapusan data ini. Perubahan belum diterapkan.</p>
            @endif

            @if ($changeRequest->status === 'pending')
                <div class="approval-actions">
                    <form method="POST" action="{{ route('admin.activity-requests.approve', $changeRequest) }}" onsubmit="return confirm('Setujui usulan ini dan terapkan perubahannya?')">@csrf<button class="admin-action-button" type="submit">Setujui Usulan</button></form>
                    <form class="approval-reject-form" method="POST" action="{{ route('admin.activity-requests.reject', $changeRequest) }}">@csrf<label for="rejection-{{ $changeRequest->id }}">Alasan penolakan</label><textarea class="admin-textarea" id="rejection-{{ $changeRequest->id }}" name="rejection_reason" maxlength="2000" required placeholder="Tuliskan alasan agar Pengurus memahami keputusan Admin."></textarea><button class="admin-delete-button" type="submit">Tolak Usulan</button></form>
                </div>
            @elseif ($changeRequest->status === 'rejected' && $changeRequest->rejection_reason)
                <div class="admin-alert error"><strong>Alasan penolakan:</strong> {{ $changeRequest->rejection_reason }}</div>
                <p class="approval-reviewer">Ditinjau {{ $changeRequest->reviewed_at?->translatedFormat('d F Y, H:i') }} oleh {{ $changeRequest->reviewedBy?->name ?? 'Admin' }}</p>
            @else
                <p class="approval-reviewer">Disetujui {{ $changeRequest->reviewed_at?->translatedFormat('d F Y, H:i') }} oleh {{ $changeRequest->reviewedBy?->name ?? 'Admin' }}</p>
            @endif
        </article>
    @empty
        <div class="dashboard-card activity-empty"><span class="activity-empty-mark">✓</span><strong>Belum ada usulan perubahan</strong><p>Usulan Pengurus akan tampil di sini untuk ditinjau Admin.</p></div>
    @endforelse
    <div class="admin-pagination">{{ $requests->links() }}</div>
@endsection
