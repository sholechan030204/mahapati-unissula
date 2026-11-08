@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">AKUN YANG SEDANG MASUK</span><h1>Profil Admin</h1><p>Perbarui identitas akun dan kata sandi Anda.</p></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    <section class="dashboard-card admin-tool-card admin-tool-form-card admin-profile-card">
        <div class="admin-profile-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-tool-form">
            @csrf @method('PUT')
            <div class="admin-form-grid">
                <label class="admin-field"><span>Nama</span><input class="admin-input" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"></label>
                <label class="admin-field"><span>Email login</span><input class="admin-input" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"></label>
            </div>
            <div class="tool-form-intro"><h2>Ganti kata sandi</h2><p>Kosongkan bagian ini jika tidak ingin mengganti kata sandi. Kata sandi baru minimal 12 karakter.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Kata sandi saat ini</span><input class="admin-input" type="password" name="current_password" autocomplete="current-password"></label>
                <label class="admin-field"><span>Kata sandi baru</span><input class="admin-input" type="password" name="password" autocomplete="new-password"></label>
                <label class="admin-field"><span>Ulangi kata sandi baru</span><input class="admin-input" type="password" name="password_confirmation" autocomplete="new-password"></label>
            </div>
            <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Profil</button></div>
        </form>
    </section>
@endsection
