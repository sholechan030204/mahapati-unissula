@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Kegiatan' : 'Tambah Kegiatan')

@section('content')
    @php($routePrefix = $isProposal ? 'pengurus' : 'admin')
    <div class="admin-content-heading"><div><span class="eyebrow-label">KEGIATAN MAHAPATI</span><h1>{{ $isEditing ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h1><p>Isi jadwal, lokasi, program terkait, dan anggota yang mengikuti kegiatan.</p></div><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'activities']) }}">← Kembali</a></div>
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if ($isProposal)<div class="activity-permission-note"><span>ⓘ</span> Kegiatan yang kamu ajukan akan terlihat setelah Admin meninjau dan menyetujuinya.</div>@endif

    <form class="dashboard-card admin-card activity-form-card" method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route($routePrefix.'.activities.update', $activity) : route($routePrefix.'.activities.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="activity-form-columns">
            <div>
                <div class="form-section-title"><span class="form-section-number">01</span><div><h2>Informasi Kegiatan</h2><p>Informasi dasar yang akan digunakan pada daftar dan detail kegiatan.</p></div></div>
                <div class="admin-form-grid">
                    <div class="admin-field admin-field-wide"><label for="title">Nama kegiatan *</label><input class="admin-input" id="title" name="title" value="{{ old('title', $activity->title) }}" maxlength="180" required placeholder="Contoh: Pendidikan Navigasi Darat">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="work_program_id">Program kerja</label><select class="admin-select" id="work_program_id" name="work_program_id"><option value="">Tidak dikaitkan</option>@foreach ($workPrograms as $workProgram)<option value="{{ $workProgram->id }}" @selected((string) old('work_program_id', $activity->work_program_id) === (string) $workProgram->id)>{{ $workProgram->title }}</option>@endforeach</select>@error('work_program_id')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="location">Lokasi *</label><input class="admin-input" id="location" name="location" value="{{ old('location', $activity->location) }}" maxlength="255" required placeholder="Lokasi kegiatan">@error('location')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="planned_at">Tanggal dan waktu rencana *</label><input class="admin-input" type="datetime-local" id="planned_at" name="planned_at" value="{{ old('planned_at', $activity->planned_at?->format('Y-m-d\\TH:i')) }}" required>@error('planned_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="realized_at">Tanggal dan waktu realisasi</label><input class="admin-input" type="datetime-local" id="realized_at" name="realized_at" value="{{ old('realized_at', $activity->realized_at?->format('Y-m-d\\TH:i')) }}"><small>Isi ketika kegiatan benar-benar dilaksanakan.</small>@error('realized_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="status">Status kegiatan *</label><select class="admin-select" id="status" name="status" required><option value="planned" @selected(old('status', $activity->status) === 'planned')>Belum terlaksana</option><option value="ongoing" @selected(old('status', $activity->status) === 'ongoing')>Sedang terlaksana</option><option value="completed" @selected(old('status', $activity->status) === 'completed')>Sudah terlaksana</option></select>@error('status')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field admin-field-wide"><label for="description">Deskripsi kegiatan *</label><textarea class="admin-textarea activity-description" id="description" name="description" maxlength="5000" required placeholder="Jelaskan tujuan dan rangkaian kegiatan.">{{ old('description', $activity->description) }}</textarea>@error('description')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field admin-field-wide"><label for="cover_image">Foto sampul kegiatan (opsional)</label><input class="admin-input" type="file" id="cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.webp"><small>JPG, PNG, atau WebP, maksimal 2 MB. File yang lebih besar akan ditolak sebelum formulir dikirim.</small><span class="admin-error" id="cover-image-size-error" role="alert" hidden></span>@if ($activity->cover_image)<img class="activity-cover-preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activity->cover_image) }}" alt="Sampul {{ $activity->title }}">@endif @error('cover_image')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <label class="featured-activity-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $activity->is_featured))><span><strong>Tampilkan sebagai Sorotan Utama</strong><small>Kegiatan ini akan tampil di bagian sorotan halaman publik. Hanya satu kegiatan menjadi sorotan pada satu waktu.</small></span></label>
                </div>
            </div>
            <aside class="participant-picker">
                <div class="form-section-title"><span class="form-section-number">02</span><div><h2>Anggota Peserta</h2><p>Pilih anggota yang mengikuti kegiatan ini.</p></div></div>
                <div class="participant-picker-list">
                    @forelse ($members as $member)
                        <label class="participant-choice"><input type="checkbox" name="participant_ids[]" value="{{ $member->id }}" @checked(in_array($member->id, old('participant_ids', $activity->relationLoaded('participants') ? $activity->participants->modelKeys() : [])))><span class="participant-avatar">{{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}</span><span class="participant-name"><strong>{{ $member->full_name }}</strong><small>{{ $member->nra }} · {{ $member->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</small></span><span class="participant-check">✓</span></label>
                    @empty
                        <div class="admin-empty">Data anggota belum tersedia.</div>
                    @endforelse
                </div>
                <small class="participant-help">Daftar peserta dapat diperbarui dengan mengedit kegiatan.</small>
                @error('participant_ids')<span class="admin-error">{{ $message }}</span>@enderror
                @error('participant_ids.*')<span class="admin-error">{{ $message }}</span>@enderror
            </aside>
        </div>
        <div class="admin-button-row activity-form-actions"><button class="admin-action-button" type="submit">{{ $isProposal ? 'Kirim Usulan ke Admin' : ($isEditing ? 'Simpan Perubahan' : 'Simpan Kegiatan') }}</button><a class="admin-secondary-button" href="{{ route($routePrefix.'.activities.index', ['tab' => 'activities']) }}">Batal</a></div>
    </form>
    <script>
        (() => {
            const form = document.querySelector('form.activity-form-card');
            const fileInput = document.querySelector('#cover_image');
            const sizeError = document.querySelector('#cover-image-size-error');
            const maxBytes = 2 * 1024 * 1024;

            if (!form || !fileInput || !sizeError) return;

            fileInput.addEventListener('change', () => {
                const file = fileInput.files?.[0];
                sizeError.hidden = true;
                sizeError.textContent = '';

                if (file && file.size > maxBytes) {
                    const sizeInMb = (file.size / (1024 * 1024)).toFixed(1);
                    fileInput.value = '';
                    sizeError.textContent = `Ukuran foto ${sizeInMb} MB. Batas maksimal 2 MB. Pilih foto yang lebih kecil atau lanjutkan tanpa foto.`;
                    sizeError.hidden = false;
                }
            });

            form.addEventListener('submit', (event) => {
                const file = fileInput.files?.[0];

                if (file && file.size > maxBytes) {
                    event.preventDefault();
                    sizeError.textContent = 'Foto sampul melebihi batas 2 MB. Pilih foto yang lebih kecil atau hapus foto sebelum menyimpan.';
                    sizeError.hidden = false;
                    fileInput.focus();
                }
            });
        })();
    </script>
@endsection
