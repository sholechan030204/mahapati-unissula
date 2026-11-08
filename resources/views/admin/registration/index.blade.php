@extends('layouts.admin')

@section('title', 'Pengelolaan Pendaftaran')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">PENERIMAAN CALON ANGGOTA</span><h1>Pendaftaran</h1><p>Atur status buka/tutup secara manual, perbarui jadwal, dan tinjau data formulir awal.</p></div><span class="registration-admin-status {{ $registration['is_open'] ? 'is-open' : 'is-closed' }}">{{ $registration['is_open'] ? '● Dibuka' : '● Ditutup' }}</span></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif

    <section class="dashboard-card registration-admin-card">
        <div class="activity-list-toolbar"><div><h2>Pengaturan Open Recruitment</h2><p>Mengubah tanggal tidak otomatis membuka atau menutup formulir; gunakan sakelar status di bawah.</p></div></div>
        <form method="POST" action="{{ route($routePrefix.'.registration.update') }}" enctype="multipart/form-data" class="registration-admin-form">
            @csrf @method('PUT')
            <div class="registration-toggle-row"><div><strong>Status pendaftaran</strong><span data-registration-hint>{{ old('is_open', $registration['is_open']) ? 'Formulir calon anggota dapat dikirim.' : 'Formulir calon anggota ditutup.' }}</span></div><label class="registration-toggle"><input type="hidden" name="is_open" value="0"><input type="checkbox" name="is_open" value="1" data-registration-toggle @checked((bool) old('is_open', $registration['is_open']))><span aria-hidden="true"></span><b data-registration-label>{{ (bool) old('is_open', $registration['is_open']) ? 'DIBUKA' : 'DITUTUP' }}</b></label></div>
            <div class="registration-admin-grid">
                <div class="admin-field"><label for="opens_at">Tanggal mulai pendaftaran</label><input class="admin-input" type="date" id="opens_at" name="opens_at" value="{{ old('opens_at', $registration['opens_at']) }}">@error('opens_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="closes_at">Tanggal akhir pendaftaran</label><input class="admin-input" type="date" id="closes_at" name="closes_at" value="{{ old('closes_at', $registration['closes_at']) }}">@error('closes_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="interview_starts_at">Wawancara mulai</label><input class="admin-input" type="date" id="interview_starts_at" name="interview_starts_at" value="{{ old('interview_starts_at', $registration['interview_starts_at']) }}">@error('interview_starts_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="interview_ends_at">Wawancara berakhir</label><input class="admin-input" type="date" id="interview_ends_at" name="interview_ends_at" value="{{ old('interview_ends_at', $registration['interview_ends_at']) }}">@error('interview_ends_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="announcement_at">Tanggal pengumuman</label><input class="admin-input" type="date" id="announcement_at" name="announcement_at" value="{{ old('announcement_at', $registration['announcement_at']) }}">@error('announcement_at')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="quota">Kuota <small>(opsional)</small></label><input class="admin-input" type="number" id="quota" name="quota" min="1" value="{{ old('quota', $registration['quota']) }}" placeholder="Tidak ditentukan">@error('quota')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="fee">Biaya pendaftaran <small>(opsional)</small></label><input class="admin-input" type="number" id="fee" name="fee" min="0" value="{{ old('fee', $registration['fee']) }}" placeholder="Tidak dipungut biaya">@error('fee')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="whatsapp_group_link">Tautan grup WhatsApp calon</label><input class="admin-input" type="url" id="whatsapp_group_link" name="whatsapp_group_link" value="{{ old('whatsapp_group_link', $registration['whatsapp_group_link']) }}" placeholder="https://chat.whatsapp.com/...">@error('whatsapp_group_link')<span class="admin-error">{{ $message }}</span>@enderror<small>Tautan ini diberikan setelah formulir awal berhasil dikirim.</small></div>
            </div>
            <div class="registration-pamphlet-field admin-field"><label for="pamphlet">Pamflet Open Recruitment</label><input class="admin-input" type="file" id="pamphlet" name="pamphlet" accept=".jpg,.jpeg,.png,.webp"><small>JPG, PNG, atau WebP; maksimal 2 MB. Pamflet tampil di Beranda dan halaman Pendaftaran.</small>@error('pamphlet')<span class="admin-error">{{ $message }}</span>@enderror
                @if ($registration['pamphlet_path'])<img class="registration-pamphlet-preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($registration['pamphlet_path']) }}" alt="Pamflet Open Recruitment yang sedang ditampilkan"><label class="registration-remove-pamphlet"><input type="checkbox" name="remove_pamphlet" value="1"> Hapus pamflet dari website</label>@endif
            </div>
            <div class="admin-button-row registration-admin-actions"><button class="admin-action-button" type="submit">Simpan Pengaturan</button></div>
        </form>
    </section>

    <section class="dashboard-card registration-applicants-card">
        <div class="activity-list-toolbar"><div><h2>Formulir Awal Masuk</h2><p>Data ini hanya dapat dilihat Admin dan Pengurus untuk menindaklanjuti pendaftar.</p></div><span class="registration-applicant-count">{{ number_format($applicants->total()) }} pendaftar</span></div>
        @if ($applicants->isEmpty())
            <div class="activity-empty"><span class="activity-empty-mark">♙</span><strong>Belum ada formulir masuk</strong><p>Formulir yang dikirim calon anggota akan muncul di sini.</p></div>
        @else
            <div class="table-scroll"><table class="admin-table registration-applicants-table"><thead><tr><th>Nama</th><th>NIM</th><th>Program Studi</th><th>Angkatan</th><th>Semester</th><th>Email</th><th>WhatsApp</th><th>Dikirim</th>@if ($isAdmin)<th>Aksi</th>@endif</tr></thead><tbody>
                @foreach ($applicants as $applicant)
                    <tr><td><strong>{{ $applicant->full_name }}</strong></td><td>{{ $applicant->student_number }}</td><td>{{ $applicant->study_program }}</td><td>{{ $applicant->cohort }}</td><td>{{ $applicant->semester }}</td><td><a href="mailto:{{ $applicant->email }}">{{ $applicant->email }}</a></td><td><a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D+/', '', $applicant->whatsapp)) }}" target="_blank" rel="noopener">{{ $applicant->whatsapp }}</a></td><td>{{ $applicant->created_at->translatedFormat('d M Y, H:i') }}</td>@if ($isAdmin)<td><form method="POST" action="{{ route('admin.registration.applicants.destroy', $applicant) }}" onsubmit="return confirm('Hapus data pendaftar ini?')">@csrf @method('DELETE')<button class="cash-delete-button" type="submit">Hapus</button></form></td>@endif</tr>
                @endforeach
            </tbody></table></div>
            <div class="admin-pagination">{{ $applicants->links() }}</div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        const registrationToggle = document.querySelector('[data-registration-toggle]');
        registrationToggle?.addEventListener('change', () => {
            const isOpen = registrationToggle.checked;
            document.querySelector('[data-registration-label]').textContent = isOpen ? 'DIBUKA' : 'DITUTUP';
            document.querySelector('[data-registration-hint]').textContent = isOpen ? 'Formulir calon anggota dapat dikirim.' : 'Formulir calon anggota ditutup.';
        });
    </script>
@endpush
