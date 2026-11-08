@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Transaksi Kas' : 'Catat Transaksi Kas')

@section('content')
    <div class="admin-content-heading">
        <div><span class="eyebrow-label">KEUANGAN ORGANISASI</span><h1>{{ $isEditing ? 'Edit Transaksi Kas' : 'Catat Transaksi Kas' }}</h1><p>Catat pemasukan atau pengeluaran yang benar-benar terjadi.</p></div>
        <a class="admin-secondary-button" href="{{ route('admin.cash-transactions.index') }}">← Kembali ke Kas</a>
    </div>

    @if ($errors->any())
        <div class="admin-alert error" role="alert">Periksa kembali isian transaksi yang ditandai.</div>
    @endif

    <form class="dashboard-card admin-card cash-entry-form" method="POST" action="{{ $isEditing ? route('admin.cash-transactions.update', $transaction) : route('admin.cash-transactions.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="form-section-title"><span class="form-section-number">01</span><div><h2>Informasi Transaksi</h2><p>Jenis, tanggal, jumlah, dan keterangan transaksi kas.</p></div></div>
        <div class="admin-form-grid cash-entry-grid">
            <div class="admin-field"><label for="type">Jenis transaksi *</label><select class="admin-select" id="type" name="type" required><option value="income" @selected(old('type', $transaction->type) === 'income')>Pemasukan</option><option value="expense" @selected(old('type', $transaction->type) === 'expense')>Pengeluaran</option></select>@error('type')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="transaction_date">Tanggal transaksi *</label><input class="admin-input" id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date?->format('Y-m-d')) }}" required>@error('transaction_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="amount">Jumlah (rupiah) *</label><input class="admin-input" id="amount" type="number" name="amount" min="1" max="999999999999999999" step="1" value="{{ old('amount', $transaction->amount) }}" placeholder="Contoh: 250000" required><small>Masukkan angka tanpa titik atau simbol Rp.</small>@error('amount')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field admin-field-wide"><label for="description">Uraian transaksi *</label><textarea class="admin-textarea cash-description-input" id="description" name="description" maxlength="2000" required placeholder="Contoh: Pembelian perlengkapan kegiatan pendakian">{{ old('description', $transaction->description) }}</textarea>@error('description')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div>
        <div class="cash-entry-guidance"><strong>Periksa sebelum menyimpan</strong><span>Pemasukan menambah saldo. Pengeluaran mengurangi saldo. Perubahan saldo dihitung otomatis dari seluruh transaksi.</span></div>
        <div class="admin-button-row"><button class="admin-action-button" type="submit">{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Transaksi' }}</button><a class="admin-secondary-button" href="{{ route('admin.cash-transactions.index') }}">Batal</a></div>
    </form>
@endsection
