@extends('layouts.admin')

@section('title', 'Kas MAHAPATI')

@section('content')
    <div class="admin-content-heading cash-page-heading">
        <div>
            <span class="eyebrow-label">KEUANGAN ORGANISASI</span>
            <h1>Kas MAHAPATI</h1>
            <p>Catat transaksi kas dan pantau saldo serta pergerakan dana.</p>
        </div>
        <a class="admin-action-button" href="{{ route('admin.cash-transactions.create') }}">＋ Catat Transaksi</a>
    </div>

    @if (session('status'))
        <div class="admin-alert" role="status">{{ session('status') }}</div>
    @endif

    <section class="cash-summary-grid" aria-label="Ringkasan kas">
        <article class="dashboard-card cash-summary-card cash-balance-card">
            <span class="cash-summary-icon navy">▤</span>
            <div><span>Saldo kas saat ini</span><strong>Rp {{ number_format($currentBalance, 0, ',', '.') }}</strong><small>Saldo bersih seluruh transaksi yang tercatat</small></div>
        </article>
        <article class="dashboard-card cash-summary-card">
            <span class="cash-summary-icon green">↙</span>
            <div><span>Total pemasukan sesuai filter</span><strong class="cash-income">Rp {{ number_format($periodIncome, 0, ',', '.') }}</strong><small>Periode yang dipilih di bawah</small></div>
        </article>
        <article class="dashboard-card cash-summary-card">
            <span class="cash-summary-icon red">↗</span>
            <div><span>Total pengeluaran sesuai filter</span><strong class="cash-expense">Rp {{ number_format($periodExpenses, 0, ',', '.') }}</strong><small>Periode yang dipilih di bawah</small></div>
        </article>
    </section>

    <section class="dashboard-card cash-filter-card">
        <div class="cash-section-heading"><div><h2>Filter transaksi</h2><p>Filter tanggal, bulan, tahun, atau cari uraian transaksi.</p></div></div>
        <form class="cash-filters" method="GET" action="{{ route('admin.cash-transactions.index') }}">
            <input class="admin-input" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari uraian transaksi" aria-label="Cari uraian transaksi">
            <label class="cash-filter-field"><span>Dari tanggal</span><input class="admin-input" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
            <label class="cash-filter-field"><span>Sampai tanggal</span><input class="admin-input" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
            <label class="cash-filter-field"><span>Bulan</span><select class="admin-select" name="month"><option value="">Semua bulan</option>@foreach (range(1, 12) as $month)<option value="{{ $month }}" @selected((string) ($filters['month'] ?? '') === (string) $month)>{{ \Illuminate\Support\Carbon::createFromDate(2000, $month, 1)->locale('id')->translatedFormat('F') }}</option>@endforeach</select></label>
            <label class="cash-filter-field"><span>Tahun</span><input class="admin-input" type="number" name="year" min="2000" max="2100" value="{{ $filters['year'] ?? '' }}" placeholder="Contoh: {{ now()->year }}"></label>
            <div class="cash-filter-actions"><button class="admin-action-button" type="submit">Terapkan Filter</button><a class="admin-secondary-button" href="{{ route('admin.cash-transactions.index') }}">Atur Ulang</a></div>
        </form>
    </section>

    <section class="dashboard-card cash-ledger-card">
        <div class="cash-section-heading cash-ledger-heading">
            <div><h2>Daftar transaksi</h2><p>{{ $transactions->total() }} transaksi tercatat</p></div>
            @if (filled($monthlyDuesSpreadsheetUrl))
                <a class="admin-secondary-button" href="{{ $monthlyDuesSpreadsheetUrl }}" target="_blank" rel="noopener noreferrer">Buka Excel Iuran Bulanan ↗</a>
            @else
                <span class="cash-spreadsheet-pending">Tautan Excel iuran belum dikonfigurasi</span>
            @endif
        </div>

        @if ($transactions->isEmpty())
            <div class="activity-empty cash-empty-state">
                <span class="activity-empty-mark">▤</span>
                <strong>Belum ada transaksi pada periode ini</strong>
                <p>Tambahkan pemasukan atau pengeluaran untuk mulai mencatat kas MAHAPATI.</p>
                <a class="admin-action-button" href="{{ route('admin.cash-transactions.create') }}">＋ Catat Transaksi</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="admin-table cash-table">
                    <thead><tr><th>Tanggal</th><th>Jenis</th><th>Uraian</th><th>Jumlah</th><th>Dicatat oleh</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                                <td><span class="cash-type-pill {{ $transaction->type }}">{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span></td>
                                <td class="cash-description-cell">{{ $transaction->description }}</td>
                                <td class="cash-amount-cell {{ $transaction->type }}">{{ $transaction->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td>{{ $transaction->createdBy?->name ?? 'Admin' }}</td>
                                <td>
                                    <div class="cash-row-actions">
                                        <a class="table-action-link" href="{{ route('admin.cash-transactions.show', $transaction) }}">Detail</a>
                                        <a class="table-action-link" href="{{ route('admin.cash-transactions.edit', $transaction) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.cash-transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini dari catatan kas?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="cash-delete-button" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $transactions->links() }}</div>
        @endif
    </section>

    <p class="cash-permission-note">Pencatatan kas hanya dapat dikelola Admin. Pengurus dan Anggota hanya melihat ringkasan kas pada dashboard masing-masing.</p>
@endsection
