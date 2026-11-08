@extends('layouts.admin')

@section('title', 'Profil Pengurus')

@section('content')
    <div class="admin-content-heading">
        <div>
            <span class="eyebrow-label">AKUN PENGURUS</span>
            <h1>Profil Saya</h1>
            <p>Perbarui identitas profil dan akun Anda. Perubahan profil langsung tersimpan tanpa menunggu persetujuan Admin.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="admin-alert" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="admin-alert error" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="dashboard-card admin-tool-card admin-tool-form-card admin-profile-card">
        <div class="admin-profile-avatar" aria-hidden="true">
            @if ($member?->profile_photo)
                <img class="pengurus-profile-photo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo) }}" alt="">
            @else
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            @endif
        </div>

        @if ($member)
            <div class="profile-fields">
                <div class="profile-field"><small>NRA</small>{{ $member->nra }}</div>
                <div class="profile-field"><small>NIM</small>{{ $member->nim ?: '—' }}</div>
                <div class="profile-field"><small>Jenis keanggotaan</small>{{ $member->membership_type === 'anggota_tetap' ? 'Anggota tetap' : 'Anggota istimewa' }}</div>
                <div class="profile-field"><small>Status</small>{{ ucfirst($member->status) }}</div>
                <div class="profile-field"><small>Jabatan</small>{{ $member->position ?: 'Belum dicatat' }}</div>
            </div>
            <p class="panel-subtitle">NRA, NIM, jenis dan status keanggotaan, serta jabatan dikelola Admin.</p>
        @else
            <div class="admin-alert error" role="status">Akun ini belum terhubung ke data anggota. Hubungi Admin agar profil keanggotaan dapat ditampilkan.</div>
        @endif

        <form method="POST" action="{{ route('pengurus.profile.update') }}" class="admin-tool-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="tool-form-intro"><h2>Identitas profil</h2><p>Nama, nama alam, dan foto profil Anda langsung diperbarui. Riwayat perubahan dicatat untuk Admin.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Nama lengkap *</span><input class="admin-input" type="text" name="name" value="{{ old('name', $member?->full_name ?? $user->name) }}" maxlength="255" required autocomplete="name">@error('name')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Nama alam</span><input class="admin-input" type="text" name="alam_name" value="{{ old('alam_name', $member?->alam_name) }}" maxlength="255" @disabled(!$member)>@error('alam_name')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field admin-field-wide"><span>Foto profil</span><input class="admin-input" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" @disabled(!$member)><small>JPG, PNG, atau WebP; maksimal 2 MB. Foto berubah tanpa persetujuan Admin.</small>@error('profile_photo')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field admin-field-wide"><span>Email login *</span><input class="admin-input" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="email">@error('email')<span class="admin-error">{{ $message }}</span>@enderror</label>
            </div>

            <div class="tool-form-intro"><h2>Ganti kata sandi</h2><p>Kosongkan jika tidak ingin mengganti kata sandi. Kata sandi saat ini wajib diisi jika membuat kata sandi baru. Minimal 12 karakter.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Kata sandi saat ini</span><input class="admin-input" type="password" name="current_password" autocomplete="current-password">@error('current_password')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Kata sandi baru</span><input class="admin-input" type="password" name="password" autocomplete="new-password">@error('password')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Ulangi kata sandi baru</span><input class="admin-input" type="password" name="password_confirmation" autocomplete="new-password"></label>
            </div>
            <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Profil</button></div>
        </form>
    </section>
@endsection
