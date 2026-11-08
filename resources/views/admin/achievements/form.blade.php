@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Prestasi' : ($isSubmission ? 'Kirim Usulan Prestasi' : 'Tambah Prestasi'))

@section('content')
    @php
        $saveRoute = $isSubmission
            ? route($routePrefix === 'pengurus' ? 'pengurus.achievements.store' : 'member.achievement-submissions.store')
            : ($isEditing ? route('admin.achievements.update', $achievement) : route('admin.achievements.store'));
        $backRoute = $isSubmission
            ? route($routePrefix === 'pengurus' ? 'pengurus.achievements.index' : 'member.achievement-submissions.index')
            : route('admin.achievements.index');
    @endphp
    <div class="admin-content-heading"><div><span class="eyebrow-label">CATATAN PENCAPAIAN</span><h1>{{ $isEditing ? 'Edit Prestasi' : ($isSubmission ? 'Kirim Usulan Prestasi' : 'Tambah Prestasi') }}</h1><p>Catat pencapaian dengan informasi yang jelas dan bukti foto bila tersedia.</p></div><a class="admin-secondary-button" href="{{ $backRoute }}">← Kembali</a></div>
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if ($isSubmission)<div class="activity-permission-note"><span>ⓘ</span> Usulan akan ditinjau Admin sebelum diterbitkan. Admin dapat menyetujui atau menolak dengan alasan.</div>@endif
    <form class="dashboard-card admin-card achievement-form-card" method="POST" enctype="multipart/form-data" action="{{ $saveRoute }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="achievement-form-layout"><div class="achievement-form-fields">
            <div class="admin-field"><label for="title">Nama prestasi atau pencapaian *</label><input class="admin-input" id="title" name="title" value="{{ old('title', $achievement->title) }}" maxlength="180" required placeholder="Contoh: Juara Lomba Susur Gua Regional">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-form-grid achievement-field-grid">
                <div class="admin-field"><label for="level">Tingkat pencapaian</label><input class="admin-input" id="level" name="level" value="{{ old('level', $achievement->level) }}" maxlength="100" placeholder="Contoh: Regional, Nasional">@error('level')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="achievement_date">Tanggal pencapaian</label><input class="admin-input" type="date" id="achievement_date" name="achievement_date" value="{{ old('achievement_date', $achievement->achievement_date?->format('Y-m-d')) }}">@error('achievement_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
            </div>
            <div class="admin-field"><label for="organizer">Penyelenggara</label><input class="admin-input" id="organizer" name="organizer" value="{{ old('organizer', $achievement->organizer) }}" maxlength="180" placeholder="Nama lembaga atau penyelenggara">@error('organizer')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="summary">Ringkasan *</label><textarea class="admin-textarea achievement-summary-input" id="summary" name="summary" maxlength="300" required placeholder="Ringkasan singkat yang tampil pada kartu prestasi.">{{ old('summary', $achievement->summary) }}</textarea><small>Maksimal 300 karakter.</small>@error('summary')<span class="admin-error">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="description">Deskripsi lengkap *</label><textarea class="admin-textarea achievement-description-input" id="description" name="description" maxlength="30000" required placeholder="Ceritakan pencapaian dan kontribusi MAHAPATI.">{{ old('description', $achievement->description) }}</textarea>@error('description')<span class="admin-error">{{ $message }}</span>@enderror</div>
        </div><aside class="achievement-form-aside">
            <div class="admin-field"><label for="image">Foto atau bukti visual (opsional)</label><input class="admin-input" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp"><small>JPG, PNG, atau WebP; maksimal 2 MB.</small>@error('image')<span class="admin-error">{{ $message }}</span>@enderror</div>
            @if ($achievement->image_path)<img class="achievement-form-preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($achievement->image_path) }}" alt="Dokumentasi prestasi saat ini">@if ($isEditing)<label class="achievement-remove-image"><input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))> Hapus foto saat disimpan</label>@endif @endif
            @unless ($isSubmission)<div class="admin-field"><label for="status">Status publikasi *</label><select class="admin-select" id="status" name="status" required><option value="draft" @selected(old('status', $achievement->status) === 'draft')>Simpan sebagai draft</option><option value="published" @selected(old('status', $achievement->status) === 'published')>Terbitkan sekarang</option></select>@error('status')<span class="admin-error">{{ $message }}</span>@enderror</div>@endunless
            <div class="achievement-form-guidance"><strong>Sebelum menyimpan</strong><span>Pastikan informasi pencapaian akurat. Foto bersifat opsional dan hanya diterbitkan setelah Admin menyetujui kiriman.</span></div>
        </aside></div>
        <div class="admin-button-row achievement-form-actions"><button class="admin-action-button" type="submit">{{ $isSubmission ? 'Kirim untuk Persetujuan' : ($isEditing ? 'Simpan Perubahan' : 'Simpan Prestasi') }}</button><a class="admin-secondary-button" href="{{ $backRoute }}">Batal</a></div>
    </form>
@endsection
