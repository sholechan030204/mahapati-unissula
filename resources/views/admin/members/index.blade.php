@extends('layouts.admin')

@section('title', 'Manajemen Anggota')

@section('content')
    <div class="admin-content-heading">
        <div><h1>Manajemen Anggota</h1><p>Kelola seluruh data anggota MAHAPATI.</p></div>
        @unless ($isReadOnly)<a class="admin-action-button" href="{{ route('admin.members.create') }}">＋ Tambah Anggota</a>@endunless
    </div>

    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif

    <section class="dashboard-card admin-filter-card">
        <form class="admin-filters" method="GET" action="{{ $isReadOnly ? route('pengurus.members.index') : route('admin.members.index') }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NRA, atau NIM" aria-label="Cari anggota">
            <select name="membership_type" aria-label="Filter jenis keanggotaan"><option value="">Semua jenis keanggotaan</option><option value="anggota_tetap" @selected(request('membership_type') === 'anggota_tetap')>Anggota tetap</option><option value="anggota_istimewa" @selected(request('membership_type') === 'anggota_istimewa')>Anggota istimewa</option></select>
            <select name="status" aria-label="Filter status"><option value="">Semua status</option><option value="aktif" @selected(request('status') === 'aktif')>Aktif</option><option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option></select>
            <button type="submit">Terapkan</button>
        </form>
    </section>

    <section class="dashboard-card admin-table-card">
        @if ($members->isEmpty())
            <div class="admin-empty">Belum ada data anggota yang cocok dengan pencarian ini.</div>
        @else
            <div class="table-scroll">
                <table class="admin-table">
                    <thead><tr><th>Nama</th><th>NRA</th><th>Jenis Keanggotaan</th><th>Status</th><th>Jabatan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td><span class="member-cell"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo) }}" alt="Foto {{ $member->full_name }}"><span>{{ $member->full_name }}@if ($member->alam_name)<small>{{ $member->alam_name }}</small>@endif</span></span></td>
                                <td>{{ $member->nra }}</td>
                                <td>{{ $member->membership_type === 'anggota_tetap' ? 'Anggota Tetap' : 'Anggota Istimewa' }}</td>
                                <td><span class="admin-badge {{ $member->status === 'nonaktif' ? 'inactive' : '' }}">{{ ucfirst($member->status) }}</span></td>
                                <td>{{ $member->position ?: '—' }}</td>
                                <td><a href="{{ $isReadOnly ? route('pengurus.members.show', $member) : route('admin.members.show', $member) }}">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $members->links() }}</div>
        @endif
    </section>
@endsection
