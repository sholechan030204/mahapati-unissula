@extends('layouts.admin')

@section('title', 'Domisili Anggota')

@section('content')
    <div class="admin-content-heading domicile-heading"><div><span class="eyebrow-label">DATA INTERNAL ANGGOTA</span><h1>Domisili Anggota</h1><p>Lihat alamat anggota untuk kebutuhan koordinasi. Informasi ini hanya tersedia bagi Admin.</p></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    <section class="activity-summary-grid domicile-summary-grid" aria-label="Ringkasan domisili anggota">
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon blue">♙</span><div><strong>{{ number_format($totalMembers) }}</strong><span>Anggota</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon green">⌖</span><div><strong>{{ number_format($withDomicile) }}</strong><span>Domisili Tercatat</span></div></article>
        <article class="dashboard-card activity-summary-card"><span class="activity-summary-icon amber">!</span><div><strong>{{ number_format($withoutDomicile) }}</strong><span>Alamat Belum Tercatat</span><small class="domicile-summary-note">Alamat belum diisi atau dikosongkan.</small></div></article>
    </section>
    <section class="dashboard-card domicile-management-card">
        <div class="activity-list-toolbar domicile-list-toolbar"><div><h2>Daftar Domisili</h2><p>Alamat pribadi tidak ditampilkan pada website publik. Tidak ada peta lokasi.</p></div>
            <form class="activity-filters domicile-filters" method="GET" action="{{ route('admin.domiciles.index') }}">
                <input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, NRA, NIM, atau alamat" aria-label="Cari domisili">
                <select class="admin-select" name="status" aria-label="Status anggota"><option value="">Semua status</option><option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option><option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option></select>
                <select class="admin-select" name="domicile" aria-label="Kelengkapan domisili"><option value="">Semua domisili</option><option value="available" @selected(($filters['domicile'] ?? '') === 'available')>Ada alamat</option><option value="missing" @selected(($filters['domicile'] ?? '') === 'missing')>Belum ada alamat</option></select>
                <button class="admin-action-button" type="submit">Filter</button>
                @if (($filters['q'] ?? '') !== '' || ($filters['status'] ?? '') !== '' || ($filters['domicile'] ?? '') !== '')<a class="admin-secondary-button" href="{{ route('admin.domiciles.index') }}">Reset</a>@endif
            </form>
        </div>
        @if ($members->isEmpty())
            <div class="activity-empty"><span class="activity-empty-mark">⌖</span><strong>Tidak ada data domisili</strong><p>Ubah kata kunci atau filter yang dipilih.</p></div>
        @else
            <div class="table-scroll"><table class="admin-table domicile-table"><thead><tr><th>Anggota</th><th>Status</th><th>Alamat Lengkap</th><th>Nomor Telepon</th><th>Aksi</th></tr></thead><tbody>
                @foreach ($members as $member)
                    <tr><td><strong>{{ $member->full_name }}</strong><small class="table-secondary">NRA {{ $member->nra }}@if ($member->nim) · NIM {{ $member->nim }}@endif</small></td><td><span class="admin-badge {{ $member->status === 'nonaktif' ? 'inactive' : '' }}">{{ ucfirst($member->status) }}</span></td><td class="domicile-address">{{ $member->address ?: 'Belum ada alamat domisili' }}</td><td>{{ $member->phone }}</td><td><div class="domicile-row-actions"><a class="table-action-link" href="{{ route('admin.members.show', $member) }}">Profil →</a>@if ($member->address)<form method="POST" action="{{ route('admin.domiciles.destroy', $member) }}" onsubmit="return confirm('Hapus alamat domisili {{ addslashes($member->full_name) }}? Penghapusan akan tercatat di riwayat data anggota.')">@csrf @method('DELETE')<button class="cash-delete-button" type="submit">Hapus</button></form>@endif</div></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="admin-pagination">{{ $members->links() }}</div>
        @endif
    </section>
@endsection
