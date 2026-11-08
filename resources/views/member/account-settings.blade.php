@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">KEAMANAN DAN AKSES</span><h1>Pengaturan Akun</h1><p>Kelola email untuk masuk dan kata sandi akun MAHAPATI Anda.</p></div>
        <a class="admin-secondary-button" href="{{ route('member.account.show') }}">Lihat Akun</a>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">Periksa kembali isian. {{ $errors->first() }}</div>@endif

    <section class="dashboard-card admin-tool-card admin-tool-form-card member-account-settings-card">
        <div class="member-account-summary"><span class="user-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><div><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></div><a href="{{ route('member.account.show') }}">Detail akun →</a></div>
        <form method="POST" action="{{ route('member.account-settings.update') }}" class="admin-tool-form">
            @csrf
            @method('PUT')
            <div class="tool-form-intro"><h2>Email login</h2><p>Email ini digunakan saat Anda masuk ke dashboard. Saat mengubah email, masukkan kata sandi saat ini untuk memastikan perubahan dilakukan oleh Anda.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Email login *</span><input class="admin-input" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="email">@error('email')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Kata sandi saat ini</span><span class="member-password-input"><input class="admin-input" type="password" name="current_password" autocomplete="current-password"><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></span>@error('current_password')<span class="admin-error">{{ $message }}</span>@enderror</label>
            </div>
            <div class="tool-form-intro"><h2>Ganti kata sandi</h2><p>Kosongkan tiga kolom kata sandi bila tidak ingin menggantinya. Kata sandi baru minimal 12 karakter. Kata sandi saat ini wajib diisi untuk mengganti kata sandi.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Kata sandi baru</span><span class="member-password-input"><input class="admin-input" type="password" name="password" autocomplete="new-password"><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></span>@error('password')<span class="admin-error">{{ $message }}</span>@enderror</label>
                <label class="admin-field"><span>Ulangi kata sandi baru</span><span class="member-password-input"><input class="admin-input" type="password" name="password_confirmation" autocomplete="new-password"><button class="auth-password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi" aria-pressed="false"><svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg><svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.1 0 2.1-.2 3-.5"/></svg></button></span></label>
            </div>
            <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Pengaturan Akun</button></div>
        </form>
    </section>
    <script src="{{ asset('js/auth-password-toggle.js') }}?v=1" defer></script>
@endsection
