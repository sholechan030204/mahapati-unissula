@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">PROFIL ANGGOTA</span><h1>Profil Saya</h1><p>Perbarui nama, nama alam, dan foto profil Anda. Perubahan langsung tersimpan dan tercatat untuk Admin.</p></div>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">Periksa kembali isian. {{ $errors->first() }}</div>@endif

    <section class="dashboard-card member-self-profile-card">
        @if ($member)
            <div class="member-self-profile-heading">
                <div class="member-self-photo">
                    @if ($member->profile_photo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo) }}" alt="Foto {{ $member->full_name }}">
                    @else
                        <span>{{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}</span>
                    @endif
                </div>
                <div><span class="eyebrow-label">DATA KEANGGOTAAN</span><h2>{{ $member->full_name }}</h2><p>{{ $member->alam_name ?: 'Nama alam belum dicatat' }}</p></div>
                <span class="member-type-badge">{{ $member->membership_type === 'anggota_istimewa' ? 'Anggota Istimewa' : 'Anggota Tetap' }}</span>
            </div>
            <dl class="member-self-facts">
                <div><dt>NRA</dt><dd>{{ $member->nra }}</dd></div>
                <div><dt>NIM</dt><dd>{{ $member->nim ?: 'Belum dicatat' }}</dd></div>
                <div><dt>Status keanggotaan</dt><dd>{{ $member->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</dd></div>
                <div><dt>Jabatan</dt><dd>{{ $member->position ?: 'Belum dicatat' }}</dd></div>
            </dl>
            <div class="admin-alert" role="note">Jenis dan status keanggotaan, NRA, NIM, serta jabatan dikelola Admin. Data kontak pribadi tidak ditampilkan di website publik.</div>
        @else
            <div class="admin-alert error" role="status">Akun Anda belum terhubung dengan data anggota. Hubungi Admin agar profil anggota dapat ditampilkan.</div>
        @endif

        <form method="POST" action="{{ route('member.profile.update') }}" class="admin-tool-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="tool-form-intro"><h2>Ubah profil</h2><p>Nama dan nama alam yang Anda ubah akan masuk ke riwayat perubahan anggota.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Nama lengkap *</span><input class="admin-input" type="text" name="name" value="{{ old('name', $member?->full_name ?? $user->name) }}" maxlength="255" required autocomplete="name">@error('name')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Nama alam</span><input class="admin-input" type="text" name="alam_name" value="{{ old('alam_name', $member?->alam_name) }}" maxlength="255" @disabled(!$member)>@error('alam_name')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field admin-field-wide"><span>Foto profil</span><input class="admin-input" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" @disabled(!$member)><small>JPG, PNG, atau WebP; maksimal 2 MB. Foto profil langsung diperbarui.</small>@error('profile_photo')<span class="admin-error">{{ $message }}</span>@enderror</label>
            </div>
            <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Profil</button></div>
        </form>
    </section>
@endsection
