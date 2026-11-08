@extends('layouts.admin')

@section('title', 'Kegiatan dan Program Kerja')

@section('content')
    @php
        $routePrefix = $isAdmin ? 'admin' : ($isPengurus ? 'pengurus' : 'member');
        $statusLabels = ['planned' => 'Belum Terlaksana', 'ongoing' => 'Sedang Terlaksana', 'completed' => 'Sudah Terlaksana'];
    @endphp

    <div class="admin-content-heading activity-page-heading">
        <div>
            <span class="eyebrow-label">PERENCANAAN DAN PELAKSANAAN</span>
            <h1>Kegiatan &amp; Program Kerja</h1>
            <p>Kelola rencana, pantau realisasi, dan lihat anggota yang terlibat dalam setiap kegiatan.</p>
        </div>
        @if ($isAdmin)
            <div class="admin-button-row activity-heading-actions">
                <a class="admin-secondary-button" href="{{ route('admin.activity-requests.index') }}">Persetujuan Pengurus @if ($pendingRequestCount > 0)<span class="button-count">{{ $pendingRequestCount }}</span>@endif</a>
                <a class="admin-secondary-button" href="{{ route('admin.work-programs.create') }}">＋ Program Kerja</a>
                <a class="admin-action-button" href="{{ route('admin.activities.create') }}">＋ Tambah Kegiatan</a>
            </div>
        @elseif ($isPengurus)
            <div class="admin-button-row activity-heading-actions">
                <a class="admin-secondary-button" href="{{ route('pengurus.work-programs.create') }}">＋ Usulkan Program Kerja</a>
                <a class="admin-action-button" href="{{ route('pengurus.activities.create') }}">＋ Usulkan Kegiatan</a>
            </div>
        @endif
    </div>

    @if (session('status'))
        <div class="admin-alert" role="status">{{ session('status') }}</div>
    @endif

    <section class="activity-summary-grid" aria-label="Ringkasan kegiatan">
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon blue">▦</span><div><strong>{{ number_format($programCount) }}</strong><span>Program Kerja</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon gold">⌁</span><div><strong>{{ number_format($activityCount) }}</strong><span>Total Kegiatan</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon green">✓</span><div><strong>{{ number_format($completedProgramCount) }}</strong><span>Program Sudah Terlaksana</span></div></article>
        @if ($isAdmin)
            <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon amber">♧</span><div><strong>{{ number_format($pendingRequestCount) }}</strong><span>Menunggu Persetujuan</span></div></article>
        @else
            <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon slate">◷</span><div><strong>{{ now()->translatedFormat('Y') }}</strong><span>Periode Tahun Berjalan</span></div></article>
        @endif
    </section>

    <nav class="activity-tabs" aria-label="Jenis data kegiatan">
        <a class="activity-tab {{ $tab === 'programs' ? 'is-active' : '' }}" href="{{ route($routePrefix.'.activities.index', ['tab' => 'programs']) }}">Program Kerja</a>
        <a class="activity-tab {{ $tab === 'activities' ? 'is-active' : '' }}" href="{{ route($routePrefix.'.activities.index', ['tab' => 'activities']) }}">Daftar Kegiatan</a>
        @if ($isAdmin)
            <a class="activity-tab" href="{{ route('admin.activity-requests.index') }}">Persetujuan Pengurus @if ($pendingRequestCount > 0)<span class="nav-count">{{ $pendingRequestCount }}</span>@endif</a>
        @endif
    </nav>

    <section class="dashboard-card activity-list-card">
        <div class="activity-list-toolbar">
            <div><h2>{{ $tab === 'programs' ? 'Daftar Program Kerja' : 'Daftar Kegiatan' }}</h2><p>{{ $tab === 'programs' ? 'Bandingkan waktu yang direncanakan dengan realisasi setiap program.' : 'Jadwal, lokasi, status, dan peserta kegiatan.' }}</p></div>
            <form class="activity-filters" method="GET" action="{{ route($routePrefix.'.activities.index') }}">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input class="admin-input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau lokasi" aria-label="Cari program kerja atau kegiatan">
                <select class="admin-select" name="status" aria-label="Filter status">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="admin-action-button" type="submit">Filter</button>
            </form>
        </div>

        @if ($tab === 'programs')
            @if ($workPrograms->isEmpty())
                <div class="activity-empty"><span class="activity-empty-mark">⌁</span><strong>Belum ada program kerja</strong><p>Program kerja yang sudah dicatat akan muncul di sini bersama perbandingan jadwal rencana dan realisasinya.</p>@if ($isAdmin)<a class="admin-action-button" href="{{ route('admin.work-programs.create') }}">Tambah Program Kerja</a>@elseif ($isPengurus)<a class="admin-action-button" href="{{ route('pengurus.work-programs.create') }}">Usulkan Program Kerja</a>@endif</div>
            @else
                <div class="table-scroll"><table class="admin-table activity-table">
                    <thead><tr><th>Program Kerja</th><th>Rencana</th><th>Realisasi</th><th>Status</th><th>Kegiatan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($workPrograms as $workProgram)
                            <tr>
                                <td><strong>{{ $workProgram->title }}</strong><small class="table-secondary">{{ \Illuminate\Support\Str::limit($workProgram->description, 90) }}</small></td>
                                <td>{{ $workProgram->planned_start_date->translatedFormat('d M Y') }}@if ($workProgram->planned_end_date) <span class="date-separator">–</span> {{ $workProgram->planned_end_date->translatedFormat('d M Y') }}@endif</td>
                                <td>@if ($workProgram->actual_start_date){{ $workProgram->actual_start_date->translatedFormat('d M Y') }}@if ($workProgram->actual_end_date)<span class="date-separator">–</span>{{ $workProgram->actual_end_date->translatedFormat('d M Y') }}@endif @else<span class="table-muted">Belum tercatat</span>@endif</td>
                                <td><span class="activity-status-pill {{ $workProgram->status }}">{{ $statusLabels[$workProgram->status] ?? $workProgram->status }}</span></td>
                                <td>{{ $workProgram->activities_count }}</td>
                                <td><a class="table-action-link" href="{{ route($routePrefix.'.work-programs.show', $workProgram) }}">Detail →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
                <div class="admin-pagination">{{ $workPrograms->appends(['tab' => 'programs'])->links() }}</div>
            @endif
        @else
            @if ($activities->isEmpty())
                <div class="activity-empty"><span class="activity-empty-mark">⌁</span><strong>Belum ada kegiatan</strong><p>Kegiatan beserta tanggal, lokasi, dan daftar peserta akan muncul di sini.</p>@if ($isAdmin)<a class="admin-action-button" href="{{ route('admin.activities.create') }}">Tambah Kegiatan</a>@elseif ($isPengurus)<a class="admin-action-button" href="{{ route('pengurus.activities.create') }}">Usulkan Kegiatan</a>@endif</div>
            @else
                <div class="table-scroll"><table class="admin-table activity-table">
                    <thead><tr><th>Tanggal Rencana</th><th>Kegiatan</th><th>Program Kerja</th><th>Lokasi</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td><strong>{{ $activity->planned_at->translatedFormat('d M Y') }}</strong><small class="table-secondary">{{ $activity->planned_at->format('H:i') }} WIB</small></td>
                                <td><strong>{{ $activity->title }}</strong>@if ($activity->realized_at)<small class="table-secondary">Realisasi: {{ $activity->realized_at->translatedFormat('d M Y H:i') }}</small>@endif</td>
                                <td>{{ $activity->workProgram?->title ?? '—' }}</td>
                                <td>{{ $activity->location }}</td>
                                <td>{{ number_format($activity->participants_count) }} anggota</td>
                                <td><span class="activity-status-pill {{ $activity->status }}">{{ $statusLabels[$activity->status] ?? $activity->status }}</span></td>
                                <td><a class="table-action-link" href="{{ route($routePrefix.'.activities.show', $activity) }}">Detail →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
                <div class="admin-pagination">{{ $activities->appends(['tab' => 'activities'])->links() }}</div>
            @endif
        @endif
    </section>

    @if ($isPengurus)
        <p class="activity-permission-note"><span>ⓘ</span> Usulan tambah, edit, atau hapus dari Pengurus baru diterapkan setelah disetujui Admin. Anggota dapat melihat informasi kegiatan tanpa mengubahnya.</p>
    @elseif ($isReadOnly)
        <p class="activity-permission-note"><span>ⓘ</span> Halaman ini hanya untuk melihat program kerja dan kegiatan MAHAPATI.</p>
    @endif
@endsection
