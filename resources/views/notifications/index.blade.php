@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
    <div class="admin-content-heading notification-heading"><div><span class="eyebrow-label">PUSAT INFORMASI</span><h1>Notifikasi</h1><p>Pengajuan yang perlu ditinjau dan kabar terbaru tentang kiriman Anda.</p></div>@if ($unreadCount > 0)<form method="POST" action="{{ route(auth()->user()->role === 'admin' ? 'admin.notifications.read-all' : (auth()->user()->role === 'pengurus' ? 'pengurus.notifications.read-all' : 'member.notifications.read-all')) }}">@csrf<button class="admin-secondary-button" type="submit">Tandai semua dibaca</button></form>@endif</div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="dashboard-card notification-list-card" aria-label="Daftar notifikasi">
        @forelse ($notifications as $notification)
            @php
                $routePrefix = auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'pengurus' ? 'pengurus' : 'member');
                $notificationUrl = route($routePrefix.'.notifications.open', $notification->id);
            @endphp
            <article class="notification-row {{ $notification->read_at ? '' : 'is-unread' }}">
                <span class="notification-row-icon" aria-hidden="true">{{ $notification->read_at ? '✓' : '•' }}</span>
                <div class="notification-row-content"><div class="notification-row-heading"><strong>{{ $notification->data['title'] ?? 'Pembaruan MAHAPATI' }}</strong>@unless ($notification->read_at)<span class="notification-unread-label">Baru</span>@endunless</div><p>{{ $notification->data['message'] ?? '' }}</p><time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->translatedFormat('d M Y, H:i') }}</time></div>
                <a class="table-action-link" href="{{ $notificationUrl }}">Lihat →</a>
            </article>
        @empty
            <div class="notification-empty"><span aria-hidden="true">♧</span><strong>Belum ada notifikasi</strong><p>Notifikasi persetujuan dan pembaruan akan muncul di halaman ini.</p></div>
        @endforelse
        @if ($notifications->hasPages())<div class="admin-pagination">{{ $notifications->links() }}</div>@endif
    </section>
@endsection
