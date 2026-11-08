@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Data Anggota' : 'Tambah Anggota')

@section('content')
    <div class="admin-content-heading">
        <div><h1>{{ $isEditing ? 'Edit Data Anggota' : 'Tambah Anggota' }}</h1><p>Admin mengelola status, jenis keanggotaan, dan jabatan anggota.</p></div>
        <a class="admin-secondary-button" href="{{ route('admin.members.index') }}">← Kembali ke Daftar</a>
    </div>

    @if ($errors->any())<div class="admin-alert error" role="alert">Periksa kembali isian yang ditandai.</div>@endif

    <form class="dashboard-card admin-card" method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.members.update', $member) : route('admin.members.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="admin-form-grid">
            <div class="admin-field admin-field-wide"><label for="full_name">Nama lengkap *</label><input class="admin-input" id="full_name" name="full_name" value="{{ old('full_name', $member->full_name) }}" maxlength="255" required>@error('full_name')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="alam_name">Nama alam</label><input class="admin-input" id="alam_name" name="alam_name" value="{{ old('alam_name', $member->alam_name) }}" maxlength="255"><small>Opsional, dapat dilengkapi Admin setelah nama alam diberikan.</small>@error('alam_name')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="nra">Nomor Registrasi Anggota (NRA) *</label><input class="admin-input" id="nra" name="nra" value="{{ old('nra', $member->nra) }}" maxlength="50" required>@error('nra')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="nim">NIM</label><input class="admin-input" id="nim" name="nim" value="{{ old('nim', $member->nim) }}" maxlength="30"><small>Opsional.</small>@error('nim')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="membership_type">Jenis keanggotaan *</label><select class="admin-select" id="membership_type" name="membership_type" required><option value="anggota_tetap" @selected(old('membership_type', $member->membership_type) === 'anggota_tetap')>Anggota tetap (belum lulus)</option><option value="anggota_istimewa" @selected(old('membership_type', $member->membership_type) === 'anggota_istimewa')>Anggota istimewa (sudah lulus)</option></select>@error('membership_type')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="status">Status keanggotaan *</label><select class="admin-select" id="status" name="status" required><option value="aktif" @selected(old('status', $member->status) === 'aktif')>Aktif</option><option value="nonaktif" @selected(old('status', $member->status) === 'nonaktif')>Nonaktif</option></select>@error('status')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="position">Jabatan</label><input class="admin-input" id="position" name="position" value="{{ old('position', $member->position) }}" maxlength="255"><small>Opsional. Divisi tidak dikelola pada formulir ini.</small>@error('position')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="phone">Nomor telepon *</label><input class="admin-input" id="phone" name="phone" type="tel" value="{{ old('phone', $member->phone) }}" maxlength="30" required>@error('phone')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field admin-field-wide"><label for="address">Alamat lengkap {{ $isEditing ? '(opsional)' : '*' }}</label><textarea class="admin-textarea" id="address" name="address" maxlength="2000" @required(! $isEditing)>{{ old('address', $member->address) }}</textarea>@error('address')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field admin-field-wide"><label for="profile_photo">Foto profil {{ $isEditing ? '' : '*' }}</label><input class="admin-input" id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp" {{ $isEditing ? '' : 'required' }}>@if ($member->profile_photo)<small>Foto saat ini tersimpan. Pilih file baru jika ingin menggantinya.</small>@endif<small>JPG, PNG, atau WebP. Maksimal 2 MB. Foto profil wajib tersedia.</small>@error('profile_photo')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div>
        <div class="admin-button-row"><button class="admin-action-button" type="submit">{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Anggota' }}</button><a class="admin-secondary-button" href="{{ route('admin.members.index') }}">Batal</a></div>
    </form>
@endsection
