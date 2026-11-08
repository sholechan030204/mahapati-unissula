@extends('layouts.admin')

@section('title', 'Profil Anggota')

@section('content')
    <div class="admin-content-heading">
        <div><h1>Profil Anggota</h1><p>Informasi keanggotaan dan riwayat perubahan data.</p></div>
        <a class="admin-secondary-button" href="{{ $isReadOnly ? route('pengurus.members.index') : route('admin.members.index') }}">← Kembali ke Daftar</a>
    </div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif

    <section class="dashboard-card admin-card">
        <div class="profile-summary">
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo) }}" alt="Foto {{ $member->full_name }}">
            <div>
                <div class="panel-heading"><h2>{{ $member->full_name }}</h2><span class="admin-badge {{ $member->status === 'nonaktif' ? 'inactive' : '' }}">{{ ucfirst($member->status) }}</span></div>
                <p class="panel-subtitle">{{ $member->alam_name ?: 'Nama alam belum diisi' }}</p>
                <div class="profile-fields">
                    <div class="profile-field"><small>NRA</small>{{ $member->nra }}</div>
                    <div class="profile-field"><small>NIM</small>{{ $member->nim ?: '—' }}</div>
                    <div class="profile-field"><small>Jenis keanggotaan</small>{{ $member->membership_type === 'anggota_tetap' ? 'Anggota tetap' : 'Anggota istimewa' }}</div>
                    <div class="profile-field"><small>Jabatan</small>{{ $member->position ?: '—' }}</div>
                    <div class="profile-field"><small>Nomor telepon</small>{{ $member->phone }}</div>
                    <div class="profile-field"><small>Email akun</small>{{ $member->user?->email ?: 'Belum terhubung ke akun' }}</div>
                    <div class="profile-field" style="grid-column:1/-1"><small>Alamat lengkap</small>{{ $member->address ?: 'Belum ada domisili yang tercatat' }}</div>
                </div>
            </div>
        </div>
        <div class="admin-button-row">
            @unless ($isReadOnly)<a class="admin-action-button" href="{{ route('admin.members.edit', $member) }}">Edit Data Anggota</a>@endunless
            <button class="admin-secondary-button" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
        <p class="panel-subtitle" style="margin-top:14px">Alamat dan nomor telepon hanya dapat dilihat Admin dan Pengurus.</p>
    </section>

    @unless ($isReadOnly)
        <section class="dashboard-card admin-card history-card">
            <div class="panel-heading"><h2>Role Akun</h2></div>
            @if ($member->user && in_array($member->user->role, ['anggota', 'pengurus'], true))
                <p class="panel-subtitle">Role menentukan dashboard dan hak akses akun. Mengubah role tidak mengubah jenis atau status keanggotaan.</p>
                <form method="POST" action="{{ route('admin.members.role.update', $member) }}" class="admin-tool-form" onsubmit="return confirm('Ubah role akun {{ addslashes($member->full_name) }}?')">
                    @csrf
                    @method('PUT')
                    <div class="admin-field">
                        <label for="account-role">Role saat ini: {{ $member->user->role === 'pengurus' ? 'Pengurus' : 'Anggota' }}</label>
                        <select class="admin-select" id="account-role" name="role" required>
                            <option value="anggota" @selected(old('role', $member->user->role) === 'anggota')>Anggota</option>
                            <option value="pengurus" @selected(old('role', $member->user->role) === 'pengurus')>Pengurus</option>
                        </select>
                        @error('role')<span class="admin-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Role</button></div>
                </form>
                <small>Setelah role berubah, pemilik akun perlu keluar lalu masuk kembali agar dashboard dan hak aksesnya diperbarui.</small>
            @elseif ($member->user)
                <p class="panel-subtitle">Akun ini memiliki role Admin. Role Admin tidak dapat diubah dari profil anggota.</p>
            @else
                <p class="panel-subtitle">Anggota ini belum terhubung ke akun. Setujui pengajuan akun anggota terlebih dahulu sebelum mengatur role.</p>
            @endif
        </section>

        <section class="dashboard-card admin-card history-card">
            <div class="panel-heading"><h2>Riwayat Perubahan Data</h2></div>
            @forelse ($member->changes as $change)
                @php
                    $oldValue = $change->old_value ?: '—';
                    $newValue = $change->new_value ?: '—';
                    if ($change->field_name === 'account_role') {
                        $roleLabels = ['anggota' => 'Anggota', 'pengurus' => 'Pengurus'];
                        $oldValue = $roleLabels[$change->old_value] ?? $oldValue;
                        $newValue = $roleLabels[$change->new_value] ?? $newValue;
                    }
                @endphp
                <div class="history-row"><strong>{{ $fieldLabels[$change->field_name] ?? $change->field_name }}</strong><br>{{ $oldValue }} → {{ $newValue }}<br><small>{{ $change->changedBy?->name ?? 'Admin' }} · {{ $change->created_at->format('d M Y, H:i') }}</small></div>
            @empty
                <div class="admin-empty">Belum ada riwayat perubahan.</div>
            @endforelse
        </section>
    @endunless
@endsection
