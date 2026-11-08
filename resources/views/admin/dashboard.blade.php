@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="dashboard-page-heading">
        <div>
            <h1>Selamat datang, {{ auth()->user()->name }} 👋</h1>
            <p>Berikut ringkasan data MAHAPATI yang sudah tercatat di sistem.</p>
        </div>
        <span class="dashboard-date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    </div>

    @if ($pendingAccountRequests > 0 || $pendingActivityRequests > 0)
        <div class="dashboard-alert">
            @if ($pendingAccountRequests > 0)Ada <strong>{{ $pendingAccountRequests }} pengajuan akun</strong> menunggu pemeriksaan. <a href="{{ route('admin.account-requests.index') }}">Lihat pengajuan akun →</a>@endif
            @if ($pendingAccountRequests > 0 && $pendingActivityRequests > 0)<span class="dashboard-alert-divider">·</span>@endif
            @if ($pendingActivityRequests > 0)Ada <strong>{{ $pendingActivityRequests }} usulan program/kegiatan</strong> menunggu persetujuan. <a href="{{ route('admin.activity-requests.index') }}">Tinjau usulan →</a>@endif
        </div>
    @endif

    <section class="metric-grid" aria-label="Ringkasan anggota">
        <article class="dashboard-card metric-card"><span class="metric-icon">♙</span><div><strong class="metric-value">{{ number_format($totalMembers) }}</strong><span class="metric-label">Total Anggota</span></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon green">✓</span><div><strong class="metric-value">{{ number_format($activeMembers) }}</strong><span class="metric-label">Anggota Aktif</span></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon gold">◆</span><div><strong class="metric-value">{{ number_format($permanentMembers) }}</strong><span class="metric-label">Anggota Tetap</span></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon orange">★</span><div><strong class="metric-value">{{ number_format($specialMembers) }}</strong><span class="metric-label">Anggota Istimewa</span></div></article>
    </section>

    <section class="dashboard-grid">
        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Garis Waktu Program Kerja</h2><span class="admin-badge pending">Rencana &amp; realisasi</span></div>
            <p class="panel-subtitle">Jadwal rencana dan waktu pelaksanaan aktual ditampilkan terpisah.</p>
            @if ($timelinePrograms->isEmpty())
                <div class="empty-panel">
                    <span class="empty-icon">⌁</span>
                    <strong>Belum ada program kerja untuk {{ $timelineYear }}</strong>
                    <p>Setelah program kerja dicatat, diagram ini membandingkan jadwal rencana dengan tanggal realisasinya.</p>
                    <a class="admin-inline-link" href="{{ route('admin.activities.index', ['tab' => 'programs']) }}">Buka Program Kerja →</a>
                </div>
            @else
                @php($timelineMonths = collect(range(1, 12))->map(fn (int $month) => \Illuminate\Support\Carbon::create($timelineYear, $month, 1)->locale('id')->translatedFormat('M')))
                <div class="timeline-chart" aria-label="Garis waktu program kerja tahun {{ $timelineYear }}">
                    <div class="timeline-month-axis"><span></span><div class="timeline-month-grid">@foreach ($timelineMonths as $monthLabel)<span>{{ $monthLabel }}</span>@endforeach</div></div>
                    @foreach ($timelinePrograms as $timelineItem)
                        @php($workProgram = $timelineItem['workProgram'])
                        <div class="timeline-program-row">
                            <div class="timeline-program-title"><strong>{{ $workProgram->title }}</strong><span>{{ ['planned' => 'Belum terlaksana', 'ongoing' => 'Sedang terlaksana', 'completed' => 'Sudah terlaksana'][$workProgram->status] ?? $workProgram->status }}</span></div>
                            <div class="timeline-program-bars">
                                <div class="timeline-bar-row"><span>Rencana</span><div class="timeline-track">@if ($timelineItem['plannedBar'])<i class="timeline-bar planned-bar" style="left: {{ $timelineItem['plannedBar']['left'] }}%; width: {{ $timelineItem['plannedBar']['width'] }}%"></i>@endif</div><small>{{ $workProgram->planned_start_date->translatedFormat('d M') }}@if ($workProgram->planned_end_date)–{{ $workProgram->planned_end_date->translatedFormat('d M') }}@endif</small></div>
                                <div class="timeline-bar-row"><span>Realisasi</span><div class="timeline-track">@if ($timelineItem['actualBar'])<i class="timeline-bar actual-bar" style="left: {{ $timelineItem['actualBar']['left'] }}%; width: {{ $timelineItem['actualBar']['width'] }}%"></i>@endif</div><small>{{ $workProgram->actual_start_date?->translatedFormat('d M') ?? 'Belum tercatat' }}@if ($workProgram->actual_end_date)–{{ $workProgram->actual_end_date->translatedFormat('d M') }}@endif</small></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="legend"><span><i></i>Rencana</span><span><i class="actual"></i>Realisasi</span></div>
        </article>

        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Komposisi Anggota</h2><a href="{{ route('admin.members.index') }}">Lihat anggota →</a></div>
            <p class="panel-subtitle">Jumlah dihitung dari data keanggotaan yang tersimpan.</p>
            <div class="type-list">
                @php($memberDenominator = max($totalMembers, 1))
                <div class="type-row"><span>Anggota tetap</span><div class="type-track"><div class="type-fill" style="width: {{ min(100, ($permanentMembers / $memberDenominator) * 100) }}%"></div></div><strong class="type-count">{{ $permanentMembers }}</strong></div>
                <div class="type-row"><span>Anggota istimewa</span><div class="type-track"><div class="type-fill special" style="width: {{ min(100, ($specialMembers / $memberDenominator) * 100) }}%"></div></div><strong class="type-count">{{ $specialMembers }}</strong></div>
                <div class="type-row"><span>Anggota aktif</span><div class="type-track"><div class="type-fill" style="width: {{ min(100, ($activeMembers / $memberDenominator) * 100) }}%"></div></div><strong class="type-count">{{ $activeMembers }}</strong></div>
            </div>
            <div class="admin-button-row"><a class="admin-action-button" href="{{ route('admin.members.create') }}">+ Tambah Anggota</a></div>
        </article>
    </section>

    <section class="two-col-panels">
        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Aktivitas Terbaru</h2></div>
            <div class="activity-list">
                @forelse ($recentMemberChanges as $change)
                    <div class="activity-item"><span class="attention-dot">✎</span><div><strong>{{ $change->member?->full_name ?? 'Data anggota' }} · {{ str_replace('_', ' ', $change->field_name) }}</strong><small>Diubah oleh {{ $change->changedBy?->name ?? 'Admin' }} · {{ $change->created_at->diffForHumans() }}</small></div></div>
                @empty
                    <div class="admin-empty">Aktivitas perubahan data anggota akan muncul di sini.</div>
                @endforelse
                @foreach ($recentAccountRequests as $accountRequest)
                    <div class="activity-item"><span class="attention-dot">♙</span><div><strong>Pengajuan akun: {{ $accountRequest->name }}</strong><small>{{ ucfirst($accountRequest->status) }} · {{ $accountRequest->created_at->diffForHumans() }}</small></div></div>
                @endforeach
            </div>
        </article>

        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Ringkasan Kas</h2><a href="{{ route('admin.cash-transactions.index') }}">Kelola kas →</a></div>
            <div class="dashboard-cash-balance"><span>Saldo saat ini</span><strong>Rp {{ number_format($cashBalance, 0, ',', '.') }}</strong></div>
            <div class="dashboard-cash-month">
                <div><span>Pemasukan bulan ini</span><strong class="cash-income">+ Rp {{ number_format($cashMonthIncome, 0, ',', '.') }}</strong></div>
                <div><span>Pengeluaran bulan ini</span><strong class="cash-expense">− Rp {{ number_format($cashMonthExpenses, 0, ',', '.') }}</strong></div>
            </div>
        </article>
    </section>
@endsection
