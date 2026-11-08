@extends('layouts.admin')

@section('title', 'Dashboard Pengurus')

@section('content')
    <div class="dashboard-page-heading member-dashboard-heading">
        <div>
            <span class="eyebrow-label">RUANG KERJA PENGURUS</span>
            <h1>Selamat datang, {{ auth()->user()->name }} 👋</h1>
            <p>Pantau program kerja, kegiatan, dan kiriman konten MAHAPATI.</p>
        </div>
        <span class="dashboard-date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    </div>

    <section class="metric-grid member-metric-grid" aria-label="Ringkasan kerja pengurus">
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('pengurus.activities.index') }}"><span class="metric-icon">▣</span><span><strong class="metric-value">{{ number_format($activityCount) }}</strong><span class="metric-label">Total Kegiatan</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('pengurus.activities.index', ['tab' => 'programs']) }}"><span class="metric-icon gold">⌁</span><span><strong class="metric-value">{{ number_format($programCount) }}</strong><span class="metric-label">Program Kerja</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('pengurus.activities.index') }}"><span class="metric-icon orange">◷</span><span><strong class="metric-value">{{ number_format($pendingActivityChanges) }}</strong><span class="metric-label">Perubahan Menunggu Admin</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('pengurus.news.index') }}"><span class="metric-icon green">✎</span><span><strong class="metric-value">{{ number_format($pendingContent) }}</strong><span class="metric-label">Konten Menunggu Admin</span></span></a>
    </section>

    <section class="dashboard-grid member-dashboard-grid">
        <article class="dashboard-card dashboard-panel member-program-panel">
            <div class="panel-heading"><h2>Garis Waktu Program Kerja</h2><span class="admin-badge pending">Dapat dikelola Pengurus</span></div>
            <p class="panel-subtitle">Rencana dan realisasi program tahun {{ $timelineYear }}. Perubahan yang diajukan menunggu persetujuan Admin.</p>
            @if ($timelinePrograms->isEmpty())
                <div class="empty-panel"><span class="empty-icon">⌁</span><strong>Belum ada program kerja untuk {{ $timelineYear }}</strong><p>Tambahkan program kerja melalui menu Kegiatan.</p></div>
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
            <a class="member-panel-link" href="{{ route('pengurus.activities.index', ['tab' => 'programs']) }}">Kelola program kerja →</a>
        </article>

        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Ringkasan Kas</h2><span class="admin-badge">Hanya melihat</span></div>
            <p class="panel-subtitle">Transaksi kas dikelola Admin. Ringkasan ini hanya dapat dilihat.</p>
            <div class="dashboard-cash-balance"><span>Saldo saat ini</span><strong>Rp {{ number_format($cashBalance, 0, ',', '.') }}</strong></div>
            <div class="dashboard-cash-month">
                <div><span>Pemasukan bulan ini</span><strong class="cash-income">+ Rp {{ number_format($cashMonthIncome, 0, ',', '.') }}</strong></div>
                <div><span>Pengeluaran bulan ini</span><strong class="cash-expense">− Rp {{ number_format($cashMonthExpenses, 0, ',', '.') }}</strong></div>
            </div>
        </article>
    </section>

    <section class="two-col-panels member-dashboard-lower">
        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Kegiatan Terbaru</h2><a href="{{ route('pengurus.activities.index') }}">Kelola kegiatan →</a></div>
            <div class="activity-list">
                @forelse ($recentActivities as $activity)
                    <a class="activity-item member-activity-link" href="{{ route('pengurus.activities.show', $activity) }}"><span class="attention-dot">▣</span><span><strong>{{ $activity->title }}</strong><small>{{ $activity->location ?: 'Lokasi belum dicatat' }} · {{ $activity->planned_at?->translatedFormat('d M Y') }} · {{ ['planned' => 'Direncanakan', 'ongoing' => 'Sedang berlangsung', 'completed' => 'Selesai'][$activity->status] ?? $activity->status }}</small></span></a>
                @empty
                    <div class="member-dashboard-empty">Belum ada kegiatan yang tercatat.</div>
                @endforelse
            </div>
        </article>
        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Notifikasi Terbaru</h2><a href="{{ route('pengurus.notifications.index') }}">Lihat semua →</a></div>
            <div class="activity-list">
                @forelse ($recentNotifications as $notification)
                    <a class="activity-item member-activity-link {{ $notification->read_at ? '' : 'member-notification-unread' }}" href="{{ route('pengurus.notifications.open', $notification->id) }}"><span class="attention-dot">{{ $notification->read_at ? '✓' : '!' }}</span><span><strong>{{ $notification->data['title'] ?? 'Pembaruan MAHAPATI' }}</strong><small>{{ $notification->data['message'] ?? 'Ada informasi baru untuk akun Anda.' }} · {{ $notification->created_at->diffForHumans() }}</small></span></a>
                @empty
                    <div class="member-dashboard-empty">Belum ada notifikasi baru.</div>
                @endforelse
            </div>
        </article>
    </section>
@endsection
