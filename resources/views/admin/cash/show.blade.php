@extends('layouts.admin')

@section('title', 'Detail Transaksi Kas')

@section('content')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">KEUANGAN ORGANISASI</span><h1>Detail Transaksi</h1><p>Rincian transaksi kas yang tercatat di sistem.</p></div>
        <a class="admin-secondary-button" href="{{ route('admin.cash-transactions.index') }}">← Kembali ke Kas</a>
    </div>

    <section class="dashboard-card admin-card cash-detail-card">
        <div class="cash-detail-heading">
            <div><span class="cash-type-pill {{ $transaction->type }}">{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span><h2>{{ $transaction->description }}</h2></div>
            <strong class="cash-detail-amount {{ $transaction->type }}">{{ $transaction->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong>
        </div>
        <dl class="cash-detail-facts">
            <div><dt>Tanggal transaksi</dt><dd>{{ $transaction->transaction_date->translatedFormat('l, d F Y') }}</dd></div>
            <div><dt>Dicatat oleh</dt><dd>{{ $transaction->createdBy?->name ?? 'Admin' }}</dd></div>
            <div><dt>Terakhir diperbarui</dt><dd>{{ $transaction->updatedBy?->name ?? 'Belum pernah diperbarui' }}</dd></div>
            <div><dt>Waktu pencatatan</dt><dd>{{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</dd></div>
        </dl>
        <div class="admin-button-row">
            <a class="admin-action-button" href="{{ route('admin.cash-transactions.edit', $transaction) }}">Edit Transaksi</a>
            <form method="POST" action="{{ route('admin.cash-transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini dari catatan kas?')">
                @csrf
                @method('DELETE')
                <button class="cash-delete-button cash-delete-detail" type="submit">Hapus Transaksi</button>
            </form>
        </div>
    </section>
@endsection
