@extends('layouts.admin')

@section('title', 'Domisili Saya')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">DATA PRIBADI</span><h1>Domisili Saya</h1><p>Perbarui alamat tempat tinggal Anda. Perubahan langsung tersimpan dan tercatat di riwayat data anggota.</p></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if (! $member)
        <div class="admin-alert" role="status">Akun ini belum terhubung dengan data anggota. Hubungi Admin agar domisili dapat dikelola.</div>
    @else
        <form class="dashboard-card domicile-self-card" method="POST" action="{{ route('member.domicile.update') }}">
            @csrf @method('PUT')
            <div class="domicile-self-identity"><span class="user-avatar">{{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}</span><div><strong>{{ $member->full_name }}</strong><small>NRA {{ $member->nra }}</small></div></div>
            <div class="admin-field"><label for="address">Alamat domisili lengkap *</label><textarea class="admin-textarea domicile-address-input" id="address" name="address" rows="6" maxlength="2000" required placeholder="Tuliskan alamat tempat tinggal saat ini selengkap mungkin.">{{ old('address', $member->address) }}</textarea><small>Maksimal 2.000 karakter. Alamat ini hanya dapat dilihat Admin dan Pengurus untuk koordinasi.</small>@error('address')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-button-row domicile-self-actions"><button class="admin-action-button" type="submit">Simpan Domisili</button></div>
        </form>
    @endif
@endsection
