@extends('layouts.admin')

@section('title', 'Akun')

@section('content')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">INFORMASI AKUN</span><h1>Akun</h1><p>Ringkasan akses akun dan data keanggotaan yang terhubung.</p></div>
        <a class="admin-action-button" href="{{ route('member.account-settings.edit') }}">Pengaturan Akun</a>
    </div>

    <section class="dashboard-card member-account-card">
        <div class="member-account-card-heading"><span class="user-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><div><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></div><span class="admin-badge approved">Role Anggota</span></div>
        <dl class="member-self-facts member-account-facts">
            <div><dt>Role akun</dt><dd>Anggota</dd></div>
            <div><dt>Tanggal akun dibuat</dt><dd>{{ $user->created_at?->translatedFormat('d F Y') ?? '—' }}</dd></div>
            @if ($member)
                <div><dt>Nama anggota</dt><dd>{{ $member->full_name }}</dd></div>
                <div><dt>NRA</dt><dd>{{ $member->nra }}</dd></div>
                <div><dt>Jenis keanggotaan</dt><dd>{{ $member->membership_type === 'anggota_istimewa' ? 'Anggota istimewa' : 'Anggota tetap' }}</dd></div>
                <div><dt>Status keanggotaan</dt><dd>{{ $member->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</dd></div>
            @else
                <div class="member-account-facts-wide"><dt>Data anggota</dt><dd>Belum terhubung. Hubungi Admin MAHAPATI.</dd></div>
            @endif
        </dl>
        <div class="admin-alert" role="note">Role, jenis keanggotaan, dan status keanggotaan ditetapkan Admin. Untuk mengubah email login atau kata sandi, gunakan Pengaturan Akun.</div>
        <div class="admin-button-row"><a class="admin-secondary-button" href="{{ route('member.profile.edit') }}">Buka Profil Saya</a><a class="admin-action-button" href="{{ route('member.account-settings.edit') }}">Kelola Email dan Kata Sandi</a></div>
    </section>
@endsection
