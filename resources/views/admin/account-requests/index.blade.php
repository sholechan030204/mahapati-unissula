@extends('layouts.admin')

@section('title', 'Notifikasi Pengajuan Akun')

@section('content')
    <div class="admin-content-heading">
        <div><h1>Pengajuan Akun Anggota</h1><p>Periksa data anggota sebelum menyetujui akun. Role ditentukan Admin.</p></div>
        <a class="admin-secondary-button" href="{{ route('admin.members.index') }}">Manajemen Anggota</a>
    </div>

    @if (session('status'))
        <div class="admin-alert" role="status">{{ session('status') }}
            @if (session('passwordSetupUrl'))
                <div class="admin-field" style="margin-top:10px"><label for="setup-url">Tautan sekali pakai untuk membuat password</label><input class="admin-input" id="setup-url" readonly value="{{ session('passwordSetupUrl') }}" onclick="this.select()"><small>Salin dan sampaikan secara pribadi. Jangan bagikan di tempat umum.</small></div>
            @endif
        </div>
    @endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif

    @forelse ($accountRequests as $accountRequest)
        <article class="dashboard-card admin-card">
            <div class="admin-grid">
                <div>
                    <div class="panel-heading"><h2>{{ $accountRequest->name }}</h2><span class="admin-badge {{ $accountRequest->status }}">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$accountRequest->status] ?? $accountRequest->status }}</span></div>
                    <div class="profile-fields">
                        <div class="profile-field"><small>Email</small>{{ $accountRequest->email }}</div>
                        <div class="profile-field"><small>WhatsApp</small>{{ $accountRequest->whatsapp }}</div>
                        <div class="profile-field"><small>NRA</small>{{ $accountRequest->nra ?: '—' }}</div>
                        <div class="profile-field"><small>NIM</small>{{ $accountRequest->nim ?: '—' }}</div>
                    </div>
                    <p class="panel-subtitle" style="margin-top:12px">Diajukan {{ $accountRequest->created_at->format('d M Y, H:i') }}@if ($accountRequest->reviewed_at) · Ditinjau {{ $accountRequest->reviewed_at->format('d M Y, H:i') }} oleh {{ $accountRequest->reviewer?->name ?? 'Admin' }}@endif</p>
                    @if ($accountRequest->rejection_reason)<div class="admin-alert error"><strong>Alasan penolakan:</strong> {{ $accountRequest->rejection_reason }}</div>@endif
                </div>
                @if ($accountRequest->status === 'pending')
                    <div>
                        <form method="POST" action="{{ route('admin.account-requests.approve', $accountRequest) }}">
                            @csrf
                            <div class="admin-field"><label for="role-{{ $accountRequest->id }}">Role akun setelah disetujui</label><select class="admin-select" id="role-{{ $accountRequest->id }}" name="role" required><option value="anggota">Anggota</option><option value="pengurus">Pengurus</option></select></div>
                            <div class="admin-button-row"><button class="admin-action-button" type="submit">Setujui dan Buat Akun</button></div>
                        </form>
                        <form method="POST" action="{{ route('admin.account-requests.reject', $accountRequest) }}" style="margin-top:15px">
                            @csrf
                            <div class="admin-field"><label for="reason-{{ $accountRequest->id }}">Alasan penolakan</label><textarea class="admin-textarea" id="reason-{{ $accountRequest->id }}" name="rejection_reason" maxlength="2000" required placeholder="Jelaskan alasan penolakan"></textarea></div>
                            <div class="admin-button-row"><button class="admin-secondary-button" type="submit">Tolak Pengajuan</button></div>
                        </form>
                    </div>
                @else
                    <div>
                        <div class="admin-empty">@if ($accountRequest->approvedUser) Akun {{ $accountRequest->approvedUser->email }} ({{ $accountRequest->approvedUser->role }}) @else Pengajuan sudah diproses. @endif</div>
                        @if ($accountRequest->status === 'approved' && $accountRequest->approvedUser)
                            <form method="POST" action="{{ route('admin.account-requests.password-link', $accountRequest) }}" style="margin-top:12px">
                                @csrf
                                <button class="admin-secondary-button" type="submit">Buat Ulang Tautan Password</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </article>
    @empty
        <section class="dashboard-card admin-empty">Belum ada pengajuan akun.</section>
    @endforelse

    <div class="admin-pagination">{{ $accountRequests->links() }}</div>
@endsection
