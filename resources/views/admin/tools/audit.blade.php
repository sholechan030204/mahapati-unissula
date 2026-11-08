@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">RIWAYAT AKTIVITAS ADMIN</span><h1>Audit Log</h1><p>Catatan otomatis untuk perubahan Admin yang berhasil. Isi formulir dan kata sandi tidak disimpan dalam log.</p></div></div>
    <section class="dashboard-card admin-tool-card">
        <form class="admin-tool-filters" method="GET" action="{{ route('admin.audit.index') }}">
            <input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari aksi, Admin, atau alamat halaman">
            <label class="admin-field"><span>Dari tanggal</span><input class="admin-input" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
            <label class="admin-field"><span>Sampai tanggal</span><input class="admin-input" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
            <button class="admin-action-button" type="submit">Filter</button><a class="admin-secondary-button" href="{{ route('admin.audit.index') }}">Atur Ulang</a>
        </form>
        @if ($logs->isEmpty())
            <div class="activity-empty"><span class="activity-empty-mark">▤</span><strong>Belum ada aktivitas tercatat</strong><p>Aksi Admin yang berhasil akan muncul di sini.</p></div>
        @else
            <div class="table-scroll"><table class="admin-table"><thead><tr><th>Waktu</th><th>Admin</th><th>Aktivitas</th><th>Metode</th><th>Alamat</th><th>IP</th><th>Hasil</th></tr></thead><tbody>
                @foreach ($logs as $log)
                    <tr><td>{{ $log->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i:s') }}</td><td>{{ $log->user?->name ?? 'Akun tidak tersedia' }}<small class="audit-subtext">{{ $log->user?->email }}</small></td><td>{{ $log->action }}</td><td><span class="audit-method">{{ $log->http_method }}</span></td><td>{{ $log->url }}</td><td>{{ $log->ip_address ?? '—' }}</td><td><span class="status-pill">{{ $log->status_code }}</span></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="admin-pagination">{{ $logs->links() }}</div>
        @endif
    </section>
@endsection
