@auth
    @php
        $dashboardUrl = match (auth()->user()->role) {
            'admin' => route('admin.dashboard'),
            'pengurus' => route('pengurus.dashboard'),
            default => route('member.dashboard'),
        };
        $notificationUrl = match (auth()->user()->role) {
            'admin' => route('admin.notifications.index'),
            'pengurus' => route('pengurus.notifications.index'),
            default => route('member.notifications.index'),
        };
        $publicMember = auth()->user()->member;
        $publicAvatarName = $publicMember?->full_name ?? auth()->user()->name;
    @endphp
    <div class="public-auth-actions">
        <details class="public-profile-menu" data-profile-menu>
            <summary class="public-user-avatar" aria-label="Buka menu profil">
                @if ($publicMember?->profile_photo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($publicMember->profile_photo) }}" alt="">
                @else
                    <span aria-hidden="true">{{ mb_strtoupper(mb_substr($publicAvatarName, 0, 1)) }}</span>
                @endif
            </summary>
            <div class="public-profile-dropdown">
                <div class="public-profile-identity">
                    <span class="public-profile-large-avatar">
                        @if ($publicMember?->profile_photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($publicMember->profile_photo) }}" alt="">
                        @else
                            {{ mb_strtoupper(mb_substr($publicAvatarName, 0, 1)) }}
                        @endif
                    </span>
                    <span class="public-profile-name">{{ $publicAvatarName }}</span>
                    <span class="public-profile-email">{{ auth()->user()->email }}</span>
                    <span class="public-profile-role">{{ ucfirst(auth()->user()->role) }} MAHAPATI</span>
                </div>
                <nav class="public-profile-links" aria-label="Menu profil">
                    <a href="{{ $dashboardUrl }}"><span aria-hidden="true">⌂</span>Dashboard</a>
                    @if (auth()->user()->role === 'anggota')
                        <a href="{{ route('member.domicile.edit') }}"><span aria-hidden="true">⌖</span>Domisili Saya</a>
                    @endif
                    <a href="{{ $notificationUrl }}"><span aria-hidden="true">♧</span>Notifikasi</a>
                    <a href="{{ route('contact') }}"><span aria-hidden="true">?</span>Bantuan &amp; Kontak</a>
                </nav>
                <form class="public-profile-logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><span aria-hidden="true">⇥</span>Keluar</button>
                </form>
            </div>
        </details>
    </div>
@else
    <a class="login-button" href="{{ route('login') }}">Masuk</a>
@endauth
