@extends('layouts.admin')

@section('title', 'Dashboard Anggota')

@section('content')
    <div class="dashboard-page-heading member-dashboard-heading">
        <div>
            <span class="eyebrow-label">RUANG ANGGOTA MAHAPATI</span>
            <h1>Selamat datang, {{ $member?->full_name ?? auth()->user()->name }} 👋</h1>
            <p>Ringkasan kegiatan dan kabar terbaru untuk akun Anda.</p>
        </div>
        <span class="dashboard-date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    </div>

    @unless ($hasMemberProfile)
        <div class="dashboard-alert" role="status">Akun Anda belum terhubung ke data anggota. Hubungi Admin agar profil dan aktivitas anggota dapat ditampilkan.</div>
    @endunless

    <section class="metric-grid member-metric-grid" aria-label="Ringkasan aktivitas anggota">
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('member.activities.index') }}"><span class="metric-icon">▣</span><span><strong class="metric-value">{{ number_format($activityCount) }}</strong><span class="metric-label">Kegiatan Diikuti</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('member.achievement-submissions.index') }}"><span class="metric-icon gold">★</span><span><strong class="metric-value">{{ number_format($achievementCount) }}</strong><span class="metric-label">Prestasi Dikirim</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('member.certificates.mine') }}"><span class="metric-icon green">▧</span><span><strong class="metric-value">{{ number_format($certificateCount) }}</strong><span class="metric-label">Sertifikat</span></span></a>
        <a class="dashboard-card metric-card member-metric-card" href="{{ route('member.gallery-submissions.index') }}"><span class="metric-icon orange">✎</span><span><strong class="metric-value">{{ number_format($contentCount) }}</strong><span class="metric-label">Konten Dikirim</span></span></a>
    </section>

    <section class="dashboard-grid member-dashboard-grid">
        <article class="dashboard-card dashboard-panel member-program-panel">
            <div class="panel-heading"><h2>Garis Waktu Program Kerja</h2><span class="admin-badge pending">Hanya melihat</span></div>
            <p class="panel-subtitle">Rencana dan realisasi program tahun {{ $timelineYear }}.</p>
            @if ($timelinePrograms->isEmpty())
                <div class="empty-panel"><span class="empty-icon">⌁</span><strong>Belum ada program kerja untuk {{ $timelineYear }}</strong><p>Garis waktu akan menampilkan jadwal rencana dan realisasi program yang dikelola Pengurus dan disetujui Admin.</p></div>
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
            <a class="member-panel-link" href="{{ route('member.activities.index', ['tab' => 'programs']) }}">Lihat program kerja →</a>
        </article>

        <article class="dashboard-card dashboard-panel member-profile-panel">
            <div class="panel-heading"><h2>Profil Saya</h2><a href="{{ route('member.domicile.edit') }}">Domisili saya →</a></div>
            @if ($member)
                <div class="member-profile-summary">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo) }}" alt="Foto {{ $member->full_name }}">
                    <div><strong>{{ $member->full_name }}</strong>@if ($member->alam_name)<span>{{ $member->alam_name }}</span>@endif<span class="member-type-badge">{{ $member->membership_type === 'anggota_istimewa' ? 'Anggota Istimewa' : 'Anggota Tetap' }}</span></div>
                </div>
                <dl class="member-profile-facts">
                    <div><dt>NRA</dt><dd>{{ $member->nra }}</dd></div>
                    <div><dt>NIM</dt><dd>{{ $member->nim ?: 'Belum dicatat' }}</dd></div>
                    <div><dt>Status</dt><dd>{{ $member->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</dd></div>
                    <div><dt>Jabatan</dt><dd>{{ $member->position ?: 'Belum dicatat' }}</dd></div>
                </dl>
            @else
                <div class="empty-panel member-profile-empty"><span class="empty-icon">♙</span><strong>Profil anggota belum terhubung</strong><p>Hubungi Admin untuk menghubungkan akun dengan data anggota.</p></div>
            @endif
        </article>
    </section>

    <section class="two-col-panels member-dashboard-lower">
        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Kegiatan Saya</h2><a href="{{ route('member.activities.index') }}">Lihat semua →</a></div>
            <div class="activity-list">
                @forelse ($activities as $activity)
                    <a class="activity-item member-activity-link" href="{{ route('member.activities.show', $activity) }}"><span class="attention-dot">⌖</span><span><strong>{{ $activity->title }}</strong><small>{{ $activity->location }} · {{ $activity->planned_at?->translatedFormat('d M Y') }} · {{ ['planned' => 'Direncanakan', 'ongoing' => 'Sedang berlangsung', 'completed' => 'Selesai'][$activity->status] ?? $activity->status }}</small></span></a>
                @empty
                    <div class="member-dashboard-empty">@if ($hasMemberProfile)Anda belum tercatat sebagai peserta kegiatan.@else Kegiatan anggota akan tampil setelah profil Anda terhubung.@endif</div>
                @endforelse
            </div>
        </article>

        <article class="dashboard-card dashboard-panel">
            <div class="panel-heading"><h2>Notifikasi Terbaru</h2><a href="{{ route('member.notifications.index') }}">Lihat semua →</a></div>
            <div class="activity-list">
                @forelse ($recentNotifications as $notification)
                    <a class="activity-item member-activity-link {{ $notification->read_at ? '' : 'member-notification-unread' }}" href="{{ route('member.notifications.open', $notification->id) }}"><span class="attention-dot">{{ $notification->read_at ? '✓' : '!' }}</span><span><strong>{{ $notification->data['title'] ?? 'Pembaruan MAHAPATI' }}</strong><small>{{ $notification->data['message'] ?? 'Ada informasi baru untuk akun Anda.' }} · {{ $notification->created_at->diffForHumans() }}</small></span></a>
                @empty
                    <div class="member-dashboard-empty">Belum ada notifikasi baru.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="dashboard-card dashboard-panel member-cash-panel">
        <div class="panel-heading"><h2>Ringkasan Kas</h2><span class="admin-badge">Hanya melihat</span></div>
        <p class="panel-subtitle">Ringkasan kas organisasi. Anggota tidak dapat melihat atau mengelola transaksi.</p>
        <div class="dashboard-cash-balance"><span>Saldo saat ini</span><strong>Rp {{ number_format($cashBalance, 0, ',', '.') }}</strong></div>
        <div class="dashboard-cash-month">
            <div><span>Pemasukan bulan ini</span><strong class="cash-income">+ Rp {{ number_format($cashMonthIncome, 0, ',', '.') }}</strong></div>
            <div><span>Pengeluaran bulan ini</span><strong class="cash-expense">− Rp {{ number_format($cashMonthExpenses, 0, ',', '.') }}</strong></div>
        </div>
    </section>
@endsection
