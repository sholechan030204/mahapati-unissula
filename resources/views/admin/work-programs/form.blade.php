@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Program Kerja' : 'Tambah Program Kerja')

@section('content')
    @php($routePrefix = $isProposal ? 'pengurus' : 'admin')
    <div class="admin-content-heading"><div><span class="eyebrow-label">PERENCANAAN MAHAPATI</span><h1>{{ $isEditing ? 'Edit Program Kerja' : 'Program Kerja Baru' }}</h1><p>Catat jadwal yang direncanakan. Tanggal realisasi dapat dilengkapi ketika program berjalan.</p></div><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'programs']) }}">← Kembali</a></div>
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if ($isProposal)<div class="activity-permission-note"><span>ⓘ</span> Usulan ini tidak langsung mengubah data. Admin akan meninjau dan menyetujui atau menolaknya beserta alasan.</div>@endif

    <form class="dashboard-card admin-card activity-form-card" method="POST" action="{{ $isEditing ? route($routePrefix.'.work-programs.update', $workProgram) : route($routePrefix.'.work-programs.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="form-section-title"><span class="form-section-number">01</span><div><h2>Informasi Program</h2><p>Nama dan tujuan program kerja.</p></div></div>
        <div class="admin-form-grid">
            <div class="admin-field admin-field-wide"><label for="title">Nama program kerja *</label><input class="admin-input" id="title" name="title" value="{{ old('title', $workProgram->title) }}" maxlength="180" required placeholder="Contoh: Pendidikan dan Pelatihan Anggota">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field admin-field-wide"><label for="description">Deskripsi dan sasaran *</label><textarea class="admin-textarea activity-description" id="description" name="description" maxlength="5000" required placeholder="Jelaskan tujuan dan sasaran program kerja ini.">{{ old('description', $workProgram->description) }}</textarea>@error('description')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div>

        <div class="form-section-title"><span class="form-section-number">02</span><div><h2>Jadwal dan Status</h2><p>Rencana dan tanggal aktual dicatat terpisah agar keterlambatan terlihat jelas di garis waktu dashboard.</p></div></div>
        <div class="admin-form-grid">
            <div class="admin-field"><label for="planned_start_date">Tanggal mulai rencana *</label><input class="admin-input" type="date" id="planned_start_date" name="planned_start_date" value="{{ old('planned_start_date', $workProgram->planned_start_date?->format('Y-m-d')) }}" required>@error('planned_start_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="planned_end_date">Tanggal selesai rencana</label><input class="admin-input" type="date" id="planned_end_date" name="planned_end_date" value="{{ old('planned_end_date', $workProgram->planned_end_date?->format('Y-m-d')) }}"><small>Opsional jika program hanya memiliki satu tanggal target.</small>@error('planned_end_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="actual_start_date">Tanggal mulai realisasi</label><input class="admin-input" type="date" id="actual_start_date" name="actual_start_date" value="{{ old('actual_start_date', $workProgram->actual_start_date?->format('Y-m-d')) }}"><small>Isi saat program benar-benar mulai dilaksanakan.</small>@error('actual_start_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="actual_end_date">Tanggal selesai realisasi</label><input class="admin-input" type="date" id="actual_end_date" name="actual_end_date" value="{{ old('actual_end_date', $workProgram->actual_end_date?->format('Y-m-d')) }}"><small>Opsional jika realisasi hanya satu tanggal.</small>@error('actual_end_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="status">Status program *</label><select class="admin-select" id="status" name="status" required><option value="planned" @selected(old('status', $workProgram->status) === 'planned')>Belum terlaksana</option><option value="ongoing" @selected(old('status', $workProgram->status) === 'ongoing')>Sedang terlaksana</option><option value="completed" @selected(old('status', $workProgram->status) === 'completed')>Sudah terlaksana</option></select><small>Status selesai membutuhkan tanggal mulai realisasi.</small>@error('status')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div>
        <div class="admin-button-row activity-form-actions"><button class="admin-action-button" type="submit">{{ $isProposal ? 'Kirim Usulan ke Admin' : ($isEditing ? 'Simpan Perubahan' : 'Simpan Program Kerja') }}</button><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'programs']) }}">Batal</a></div>
    </form>
@endsection
