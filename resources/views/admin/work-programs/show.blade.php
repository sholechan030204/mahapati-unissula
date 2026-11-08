@extends('layouts.admin')

@section('title', 'Detail Program Kerja')

@section('content')
    @php($statusLabels = ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'])
    <div class="admin-content-heading"><div><span class="eyebrow-label">DETAIL PROGRAM KERJA</span><h1>{{ $workProgram->title }}</h1><p>Perencanaan, realisasi, dan kegiatan yang termasuk dalam program ini.</p></div><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'programs']) }}">← Kembali ke Program</a></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card admin-card work-program-detail-card">
        <div class="panel-heading"><h2>Ringkasan Program</h2><span class="activity-status-pill {{ $workProgram->status }}">{{ $statusLabels[$workProgram->status] ?? $workProgram->status }}</span></div>
        <p class="work-program-description">{{ $workProgram->description }}</p>
        <div class="schedule-comparison">
            <article class="schedule-period planned-period">
                <span class="schedule-period-label"><i></i>Jadwal Rencana</span>
                <strong>{{ $workProgram->planned_start_date->translatedFormat('d M Y') }}</strong>
                <small>
                    @if ($workProgram->planned_end_date)
                        Sampai {{ $workProgram->planned_end_date->translatedFormat('d M Y') }}
                    @else
                        Tanggal target program
                    @endif
                </small>
            </article>
            <article class="schedule-period actual-period">
                <span class="schedule-period-label"><i></i>Realisasi</span>
                <strong>{{ $workProgram->actual_start_date?->translatedFormat('d M Y') ?? 'Belum dimulai' }}</strong>
                <small>
                    @if ($workProgram->actual_end_date)
                        Sampai {{ $workProgram->actual_end_date->translatedFormat('d M Y') }}
                    @elseif ($workProgram->actual_start_date)
                        Tanggal selesai belum dicatat
                    @else
                        Tanggal aktual diisi saat program berjalan
                    @endif
                </small>
            </article>
            <article class="schedule-period"><span class="schedule-period-label">Kegiatan terkait</span><strong>{{ $workProgram->activities->count() }}</strong><small>kegiatan tercatat</small></article>
        </div>
        @unless ($isReadOnly)
            <div class="admin-button-row">
                <a class="admin-action-button" href="{{ route($routePrefix.'.work-programs.edit', $workProgram) }}">{{ $isProposal ? 'Usulkan Edit' : 'Edit Program' }}</a>
                <form method="POST" action="{{ route($routePrefix.'.work-programs.destroy', $workProgram) }}" onsubmit="return confirm('{{ $isProposal ? 'Kirim usulan penghapusan program ini kepada Admin?' : 'Hapus program kerja ini?' }}')">
                    @csrf @method('DELETE')
                    <button class="admin-delete-button" type="submit">{{ $isProposal ? 'Usulkan Hapus' : 'Hapus Program' }}</button>
                </form>
            </div>
        @endunless
    </section>

    <section class="dashboard-card admin-card related-activities-card">
        <div class="panel-heading"><div><h2>Kegiatan dalam Program</h2><p class="panel-subtitle">Daftar kegiatan yang dikaitkan dengan program kerja ini.</p></div>@unless ($isReadOnly)<a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.create') }}">＋ {{ $isProposal ? 'Usulkan Kegiatan' : 'Tambah Kegiatan' }}</a>@endunless</div>
        @if ($workProgram->activities->isEmpty())
            <div class="admin-empty">Belum ada kegiatan yang terhubung ke program ini.</div>
        @else
            <div class="table-scroll"><table class="admin-table activity-table"><thead><tr><th>Tanggal Rencana</th><th>Nama Kegiatan</th><th>Peserta</th><th>Status</th><th></th></tr></thead><tbody>
                @foreach ($workProgram->activities as $activity)
                    <tr><td>{{ $activity->planned_at->translatedFormat('d M Y') }}</td><td><strong>{{ $activity->title }}</strong><small class="table-secondary">{{ $activity->location }}</small></td><td>{{ $activity->participants_count }} anggota</td><td><span class="activity-status-pill {{ $activity->status }}">{{ $statusLabels[$activity->status] ?? $activity->status }}</span></td><td><a class="table-action-link" href="{{ route($routePrefix.'.activities.show', $activity) }}">Detail →</a></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>
@endsection
